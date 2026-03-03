<?php

namespace App\Controllers;

use App\Models\MenuItem;
use App\Models\AuditLog;

class POSController {

    // ── GET /api/pos/menu ─────────────────────────────────────────────────────
    public function getMenu() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $db   = (new MenuItem())->getDb();
        $stmt = $db->query("SELECT * FROM menu_items WHERE active = 1 ORDER BY category, name");
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return response($items, 200);
    }

    // ── GET /api/pos/rooms ────────────────────────────────────────────────────
    // Returns rooms currently checked-in (occupied) so cashier can charge to room
    public function getOccupiedRooms() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $db   = (new MenuItem())->getDb();
        $stmt = $db->query("
            SELECT r.id, r.room_number, r.type,
                   u.name AS guest_name
            FROM rooms r
            LEFT JOIN reservations res ON res.room_id = r.id AND res.status = 'checked_in'
            LEFT JOIN users u          ON u.id = res.guest_id
            WHERE r.status = 'occupied'
            ORDER BY r.room_number
        ");
        $rooms = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return response($rooms, 200);
    }

    // ── GET /api/pos/discounts ────────────────────────────────────────────────
    public function getDiscounts() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $db   = (new MenuItem())->getDb();
        $stmt = $db->query("SELECT id, name, type, value, is_default FROM discounts ORDER BY name");
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return response($rows, 200);
    }

    // ── POST /api/pos/checkout ────────────────────────────────────────────────
    public function checkout() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['items'])) {
            return error('Cart is empty', 400);
        }
        if (empty($data['payment_method']) && empty($data['charge_to_room'])) {
            return error('Payment method required', 400);
        }

        $db = (new MenuItem())->getDb();

        // Generate invoice ID
        $invoiceId = 'INV-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Totals
        $subtotal      = 0;
        foreach ($data['items'] as $item) {
            $subtotal += (float)$item['unit_price'] * (int)$item['quantity'];
        }
        $discountRate   = 0;
        $discountAmount = 0;
        $discountType   = $data['discount_type'] ?? 'none';

        if ($discountType !== 'none' && is_numeric($discountType)) {
            // Look up from DB — handles both percentage and fixed types
            $dStmt = $db->prepare("SELECT type, value FROM discounts WHERE id = ?");
            $dStmt->execute([(int)$discountType]);
            $disc = $dStmt->fetch(\PDO::FETCH_ASSOC);
            if ($disc) {
                if ($disc['type'] === 'percentage') {
                    $discountAmount = $subtotal * ((float)$disc['value'] / 100);
                } else {
                    $discountAmount = min((float)$disc['value'], $subtotal);
                }
            }
        }
        $total = $subtotal - $discountAmount;

        $chargeToRoom = !empty($data['charge_to_room']);
        $roomId       = $chargeToRoom && !empty($data['room_id']) ? (int)$data['room_id'] : null;
        $roomNumber   = $data['room_number'] ?? null;
        $paymentMethod = $chargeToRoom ? 'charge_to_room' : ($data['payment_method'] ?? 'cash');

        try {
            $db->beginTransaction();

            // 1. Insert order
            $referenceNumber = !empty($data['reference_number']) ? trim($data['reference_number']) : null;
            $stmt = $db->prepare("
                INSERT INTO orders
                  (cashier_id, customer_name, order_type, payment_method,
                   received_amount, reference_number, discount_type, charge_to_room, room_id,
                   room_number, invoice_id, total_amount, status,
                   created_at, updated_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,'completed',NOW(),NOW())
            ");
            $stmt->execute([
                $user['user_id'],
                $data['customer_name'] ?? 'Guest',
                'pos',
                $paymentMethod,
                isset($data['received_amount']) ? (float)$data['received_amount'] : null,
                $referenceNumber,
                $discountType,
                $chargeToRoom ? 1 : 0,
                $roomId,
                $roomNumber,
                $invoiceId,
                $total,
            ]);
            $orderId = $db->lastInsertId();

            // 2. Insert order items + chef_orders
            $itemStmt  = $db->prepare("
                INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, created_at)
                VALUES (?,?,?,?,NOW())
            ");
            $chefStmt  = $db->prepare("
                INSERT INTO chef_orders (pos_order_id, invoice_id, item_name, quantity, notes, status, created_at, updated_at)
                VALUES (?,?,?,?,?,'pending',NOW(),NOW())
            ");
            foreach ($data['items'] as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['menu_item_id'],
                    (int)$item['quantity'],
                    (float)$item['unit_price'],
                ]);
                $chefStmt->execute([
                    $orderId,
                    $invoiceId,
                    $item['item_name'],
                    (int)$item['quantity'],
                    $item['notes'] ?? null,
                ]);
            }

            // 3. If charge to room — add to room's bill
            if ($chargeToRoom && $roomId) {
                $this->chargeToRoomBill($db, $roomId, $invoiceId, $total, $orderId);
            }

            $db->commit();

            // Audit log
            $al = new AuditLog();
            $al->log($user['user_id'], 'create', 'pos', "POS checkout invoice $invoiceId — ₱$total");

            return response([
                'order_id'   => $orderId,
                'invoice_id' => $invoiceId,
                'subtotal'   => $subtotal,
                'discount'   => $discountAmount,
                'total'      => $total,
            ], 201, 'Checkout successful');

        } catch (\Exception $e) {
            $db->rollBack();
            return error('Checkout failed: ' . $e->getMessage(), 500);
        }
    }

    // ── POST /api/pos/receipt-preview ─────────────────────────────────────────
    // Returns receipt data for print popup (no finalization)
    public function receiptPreview() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        if (empty($data['items'])) return error('Cart is empty', 400);

        $subtotal = 0;
        foreach ($data['items'] as $item) {
            $subtotal += (float)$item['unit_price'] * (int)$item['quantity'];
        }
        $discountType   = $data['discount_type'] ?? 'none';
        $discountAmount = 0;

        if ($discountType !== 'none' && is_numeric($discountType)) {
            $db    = (new MenuItem())->getDb();
            $dStmt = $db->prepare("SELECT type, value FROM discounts WHERE id = ?");
            $dStmt->execute([(int)$discountType]);
            $disc  = $dStmt->fetch(\PDO::FETCH_ASSOC);
            if ($disc) {
                if ($disc['type'] === 'percentage') {
                    $discountAmount = $subtotal * ((float)$disc['value'] / 100);
                } else {
                    $discountAmount = min((float)$disc['value'], $subtotal);
                }
            }
        }
        $total          = $subtotal - $discountAmount;

        return response([
            'cashier'       => $data['cashier'] ?? 'Cashier',
            'customer_name' => $data['customer_name'] ?? 'Guest',
            'items'         => $data['items'],
            'subtotal'      => $subtotal,
            'discount_type' => $discountType,
            'discount'      => $discountAmount,
            'total'         => $total,
            'payment_method'=> $data['payment_method'] ?? 'cash',
            'received'      => $data['received_amount'] ?? 0,
            'change'        => max(0, ($data['received_amount'] ?? 0) - $total),
            'generated_at'  => date('Y-m-d H:i:s'),
        ], 200);
    }

    // ── GET /api/pos/orders ───────────────────────────────────────────────────
    public function getOrders() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $db   = (new MenuItem())->getDb();

        $stmt = $db->prepare("
            SELECT o.*,
                   u.name AS cashier_name
            FROM orders o
            LEFT JOIN users u ON u.id = o.cashier_id
            WHERE o.order_type = 'pos'
            ORDER BY o.created_at DESC
            LIMIT 200
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Attach items
        $itemStmt = $db->prepare("
            SELECT oi.quantity, oi.unit_price,
                   mi.name AS item_name, mi.category
            FROM order_items oi
            LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id
            WHERE oi.order_id = ?
        ");
        foreach ($orders as &$order) {
            $itemStmt->execute([$order['id']]);
            $order['items'] = $itemStmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        return response($orders, 200);
    }

    // ── Private: charge total to room bill ──────────────────────────────────
    private function chargeToRoomBill(\PDO $db, int $roomId, string $invoiceId, float $total, int $orderId) {
        // Find active checked-in reservation for this room
        $stmt = $db->prepare("
            SELECT id, guest_id FROM reservations
            WHERE room_id = ? AND status = 'checked_in'
            ORDER BY check_in_date DESC LIMIT 1
        ");
        $stmt->execute([$roomId]);
        $reservation = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$reservation) return; // room isn't checked in, skip

        // Check if a bill already exists for this reservation
        $stmt2 = $db->prepare("SELECT id FROM bills WHERE reservation_id = ? AND bill_type = 'restaurant' LIMIT 1");
        $stmt2->execute([$reservation['id']]);
        $bill = $stmt2->fetch(\PDO::FETCH_ASSOC);

        $now = date('Y-m-d H:i:s');

        if ($bill) {
            // Update existing bill total
            $db->prepare("UPDATE bills SET total_amount = total_amount + ?, subtotal = subtotal + ?, updated_at = ? WHERE id = ?")
               ->execute([$total, $total, $now, $bill['id']]);
            $billId = $bill['id'];
        } else {
            // Create new restaurant bill for this reservation
            $billNumber = 'RST-' . $reservation['id'] . '-' . time();
            $stmt3 = $db->prepare("INSERT INTO bills (guest_id, reservation_id, bill_type, bill_number, subtotal, discount, tax, total_amount, payment_status, paid_amount, created_at, updated_at) VALUES (?,?,'restaurant',?,?,0,0,?,'pending',0,?,?)");
            $stmt3->execute([$reservation['guest_id'], $reservation['id'], $billNumber, $total, $total, $now, $now]);
            $billId = $db->lastInsertId();
        }

        // Add bill item
        $db->prepare("INSERT INTO bill_items (bill_id, description, quantity, unit_price, amount, created_at) VALUES (?,?,1,?,?,?)")
           ->execute([$billId, "POS Order $invoiceId", $total, $total, $now]);
    }
}
