<?php

namespace App\Controllers;

use App\Models\AuditLog;
use App\Models\MenuItem;

class ChefController {

    private function db() {
        return (new MenuItem())->getDb();
    }

    // ── GET /api/chef/orders ──────────────────────────────────────────────────
    // Returns all non-served chef orders grouped by invoice
    public function getOrders() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'chef']);

        $db = $this->db();
        $stmt = $db->prepare("
            SELECT co.*,
                   o.customer_name, o.cashier_id, o.room_number,
                   u.name AS cashier_name
            FROM chef_orders co
            LEFT JOIN orders o ON o.id = co.pos_order_id
            LEFT JOIN users  u ON u.id = o.cashier_id
            WHERE co.status != 'served'
            ORDER BY co.created_at ASC
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Group by invoice_id
        $grouped = [];
        foreach ($rows as $row) {
            $inv = $row['invoice_id'];
            if (!isset($grouped[$inv])) {
                $grouped[$inv] = [
                    'invoice_id'    => $inv,
                    'pos_order_id'  => $row['pos_order_id'],
                    'customer_name' => $row['customer_name'],
                    'cashier_name'  => $row['cashier_name'],
                    'created_at'    => $row['created_at'],
                    'items'         => [],
                    'overall_status'=> $row['status'], // will be updated below
                ];
            }
            $grouped[$inv]['items'][] = [
                'id'        => $row['id'],
                'invoice_id'=> $row['invoice_id'],
                'item_name' => $row['item_name'],
                'quantity'  => $row['quantity'],
                'notes'     => $row['notes'],
                'status'    => $row['status'],
                'customer_name' => $row['customer_name'],
                'room_number'   => $row['room_number'] ?? null,
                'created_at'    => $row['created_at'],
            ];
            // Overall status = lowest progress status
            $priority = ['pending' => 1, 'preparing' => 2, 'ready' => 3, 'served' => 4];
            $cur = $priority[$row['status']] ?? 1;
            $grp = $priority[$grouped[$inv]['overall_status']] ?? 1;
            if ($cur < $grp) $grouped[$inv]['overall_status'] = $row['status'];
        }

        return response(array_values($grouped), 200);
    }

    // ── GET /api/chef/orders/all ──────────────────────────────────────────────
    // Returns all orders including served (for order history)
    public function getAllOrders() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'chef']);

        $db = $this->db();
        $stmt = $db->prepare("
            SELECT co.*,
                   o.customer_name,
                   u.name AS cashier_name
            FROM chef_orders co
            LEFT JOIN orders o ON o.id = co.pos_order_id
            LEFT JOIN users  u ON u.id = o.cashier_id
            ORDER BY co.created_at DESC
            LIMIT 300
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return response($rows, 200);
    }

    // ── PUT /api/chef/orders/{id}/status ──────────────────────────────────────
    public function updateStatus($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'chef']);

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = $data['status'] ?? null;
        $allowed = ['pending', 'preparing', 'ready', 'served'];
        if (!in_array($status, $allowed)) {
            return error('Invalid status', 400);
        }

        $db = $this->db();
        $stmt = $db->prepare("UPDATE chef_orders SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$status, $id]);

        // If served — delete the row and check if entire invoice is done
        if ($status === 'served') {
            // Get invoice before deleting
            $inv = $db->prepare("SELECT invoice_id, pos_order_id FROM chef_orders WHERE id = ?");
            $inv->execute([$id]);
            $row = $inv->fetch(\PDO::FETCH_ASSOC);

            $db->prepare("DELETE FROM chef_orders WHERE id = ?")->execute([$id]);

            if ($row) {
                // Check if all items in this invoice are now served (deleted)
                $remaining = $db->prepare("SELECT COUNT(*) FROM chef_orders WHERE invoice_id = ? AND status != 'served'");
                $remaining->execute([$row['invoice_id']]);
                $count = (int)$remaining->fetchColumn();

                if ($count === 0) {
                    // All done — delete remaining served rows for this invoice
                    $db->prepare("DELETE FROM chef_orders WHERE invoice_id = ?")->execute([$row['invoice_id']]);

                    // Check if room balance hit 0 — send housekeeping notification
                    $this->checkRoomBalance($db, $row['pos_order_id']);
                }
            }
        }

        $al = new AuditLog();
        $al->log($user['user_id'], 'update', 'chef', "Chef order $id status → $status");

        return response([], 200, 'Status updated');
    }

    // ── PUT /api/chef/orders/invoice/{invoiceId}/status ───────────────────────
    // Update ALL items in an invoice at once
    public function updateInvoiceStatus($invoiceId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'chef']);

        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = $data['status'] ?? null;
        $allowed = ['pending', 'preparing', 'ready', 'served'];
        if (!in_array($status, $allowed)) return error('Invalid status', 400);

        $db = $this->db();

        if ($status === 'served') {
            // Get pos_order_id before deleting
            $row = $db->prepare("SELECT pos_order_id FROM chef_orders WHERE invoice_id = ? LIMIT 1");
            $row->execute([$invoiceId]);
            $r = $row->fetch(\PDO::FETCH_ASSOC);

            $db->prepare("DELETE FROM chef_orders WHERE invoice_id = ?")->execute([$invoiceId]);

            if ($r) $this->checkRoomBalance($db, $r['pos_order_id']);
        } else {
            $db->prepare("UPDATE chef_orders SET status = ?, updated_at = NOW() WHERE invoice_id = ?")
               ->execute([$status, $invoiceId]);
        }

        $al = new AuditLog();
        $al->log($user['user_id'], 'update', 'chef', "Invoice $invoiceId all items → $status");

        return response([], 200, 'Invoice status updated');
    }

    // ── DELETE /api/chef/orders/{id} ──────────────────────────────────────────
    public function deleteOrder($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db   = $this->db();
        $db->prepare("DELETE FROM chef_orders WHERE id = ?")->execute([$id]);

        return response([], 200, 'Order deleted');
    }

    // ── Private: check room balance and notify housekeeping ──────────────────
    private function checkRoomBalance(\PDO $db, int $posOrderId) {
        // Get the order's room
        $stmt = $db->prepare("SELECT room_id, charge_to_room FROM orders WHERE id = ?");
        $stmt->execute([$posOrderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order || !$order['charge_to_room'] || !$order['room_id']) return;

        $roomId = $order['room_id'];

        // Find active reservation for room
        $stmt2 = $db->prepare("SELECT id, guest_id FROM reservations WHERE room_id = ? AND status = 'checked_in' LIMIT 1");
        $stmt2->execute([$roomId]);
        $res = $stmt2->fetch(\PDO::FETCH_ASSOC);
        if (!$res) return;

        // Sum all unpaid restaurant bills for this reservation
        $stmt3 = $db->prepare("SELECT COALESCE(SUM(total_amount - paid_amount), 0) AS balance FROM bills WHERE reservation_id = ? AND bill_type = 'restaurant' AND payment_status != 'paid'");
        $stmt3->execute([$res['id']]);
        $balance = (float)$stmt3->fetchColumn();

        if ($balance <= 0) {
            // Notify housekeeping — create a housekeeping task
            $stmt4 = $db->prepare("INSERT INTO housekeeping_tasks (room_id, task_type, description, priority, status, created_at, updated_at) VALUES (?,'Room Ready Check','All room charges cleared — guest account at ₱0. Room may be ready for checkout.','medium','pending',NOW(),NOW())");
            $stmt4->execute([$roomId]);
        }
    }
}
