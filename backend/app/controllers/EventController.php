<?php

namespace App\Controllers;

use App\Models\Event;

class EventController {
    private $eventModel;

    public function __construct() {
        $this->eventModel = new Event();
    }

    public function getAll() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk', 'staff']);

        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'date'   => $_GET['date']   ?? '',
        ];

        $events = $this->eventModel->getAll($filters);
        return response($events, 200);
    }

    public function getStats() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk', 'staff']);

        $stats = $this->eventModel->getStats();
        return response($stats, 200);
    }

    public function getBookedDates() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk', 'staff']);

        $dates = $this->eventModel->getBookedDates();
        return response($dates, 200);
    }

    public function getByDate() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk', 'staff']);

        $date   = $_GET['date'] ?? date('Y-m-d');
        $events = $this->eventModel->getByDate($date);
        return response($events, 200);
    }

    public function getById($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk', 'staff']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $event['remaining_balance'] = $event['total_amount'] - ($event['down_payment'] ?? 0);
        $event['reference_number']  = 'EVT-' . str_pad($event['id'], 5, '0', STR_PAD_LEFT);
        return response($event, 200);
    }

    public function checkConflict() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $date      = $_GET['date']       ?? '';
        $time      = $_GET['time']       ?? '';
        $excludeId = isset($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : null;

        if (!$date || !$time) return error('date and time are required', 400);

        $conflict = $this->eventModel->hasConflict($date, $time, $excludeId);
        return response(['conflict' => $conflict], 200);
    }

    public function create() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return error('Invalid data', 400);

        $required = ['event_type', 'client_name', 'event_date', 'event_time', 'number_of_guests'];
        foreach ($required as $field) {
            if (empty($data[$field])) return error("Field '$field' is required", 400);
        }

        // Duplicate date+time check
        if ($this->eventModel->hasConflict($data['event_date'], $data['event_time'])) {
            return error('This date and time slot is already booked by another customer. Please choose a different schedule.', 409);
        }

        $pricePerHead = (float)($data['price_per_head'] ?? 0);
        $guests       = (int)($data['number_of_guests'] ?? 0);
        $totalAmount  = isset($data['total_amount']) ? (float)$data['total_amount'] : $pricePerHead * $guests;
        $downPayment  = isset($data['down_payment'])  ? (float)$data['down_payment'] : $totalAmount * 0.30;

        $insertData = [
            'event_name'       => trim($data['event_name'] ?? ''),
            'event_type'       => trim($data['event_type']),
            'client_name'      => trim($data['client_name']),
            'client_email'     => trim($data['client_email'] ?? ''),
            'client_phone'     => trim($data['client_phone'] ?? ''),
            'client_address'   => trim($data['client_address'] ?? ''),
            'event_date'       => $data['event_date'],
            'event_time'       => $data['event_time'],
            'number_of_guests' => $guests,
            'additional_guests'=> (int)($data['additional_guests'] ?? 0),
            'venue'            => trim($data['venue'] ?? ''),
            'package_id'       => $data['package_id'] ?? null,
            'package_set'      => trim($data['package_set'] ?? ''),
            'price_per_head'   => $pricePerHead,
            'total_amount'     => $totalAmount,
            'down_payment'     => $downPayment,
            'payment_option'   => $data['payment_option'] ?? null,
            'payment_method'   => $data['payment_method'] ?? null,
            'payment_ref'      => $data['payment_ref']    ?? null,
            'supervisor_id'    => $data['supervisor_id']  ?? null,
            'booked_by'        => trim($data['booked_by'] ?? ''),
            'status'           => $data['status'] ?? 'pending',
            'notes'            => trim($data['notes']   ?? ''),
            'remarks'          => trim($data['remarks'] ?? ''),
            'created_by'       => $user['user_id'],
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $id    = $this->eventModel->create($insertData);
        $event = $this->eventModel->findById($id);
        $event['remaining_balance'] = $event['total_amount'] - $event['down_payment'];
        $event['reference_number']  = 'EVT-' . str_pad($id, 5, '0', STR_PAD_LEFT);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'events', "Event created: $id");

        return response($event, 201, 'Event created');
    }

    public function update($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return error('Invalid data', 400);

        $pricePerHead = isset($data['price_per_head'])   ? (float)$data['price_per_head']   : (float)$event['price_per_head'];
        $guests       = isset($data['number_of_guests']) ? (int)$data['number_of_guests']   : (int)$event['number_of_guests'];
        $totalAmount  = isset($data['total_amount'])     ? (float)$data['total_amount']      : $pricePerHead * $guests;
        $downPayment  = isset($data['down_payment'])     ? (float)$data['down_payment']      : (float)$event['down_payment'];

        // Duplicate date+time check (exclude the event being updated)
        $newDate = $data['event_date'] ?? $event['event_date'];
        $newTime = $data['event_time'] ?? $event['event_time'];
        if ($this->eventModel->hasConflict($newDate, $newTime, (int)$id)) {
            return error('This date and time slot is already booked by another customer. Please choose a different schedule.', 409);
        }

        $updateData = [
            'event_name'       => trim($data['event_name']     ?? $event['event_name']    ?? ''),
            'event_type'       => trim($data['event_type']     ?? $event['event_type']),
            'client_name'      => trim($data['client_name']    ?? $event['client_name']),
            'client_email'     => trim($data['client_email']   ?? $event['client_email']  ?? ''),
            'client_phone'     => trim($data['client_phone']   ?? $event['client_phone']  ?? ''),
            'client_address'   => trim($data['client_address'] ?? $event['client_address']?? ''),
            'event_date'       => $data['event_date']       ?? $event['event_date'],
            'event_time'       => $data['event_time']       ?? $event['event_time'],
            'number_of_guests' => $guests,
            'additional_guests'=> (int)($data['additional_guests'] ?? $event['additional_guests'] ?? 0),
            'venue'            => trim($data['venue']           ?? $event['venue']         ?? ''),
            'package_id'       => $data['package_id']       ?? $event['package_id']       ?? null,
            'package_set'      => trim($data['package_set']    ?? $event['package_set']   ?? ''),
            'price_per_head'   => $pricePerHead,
            'total_amount'     => $totalAmount,
            'down_payment'     => $downPayment,
            'payment_option'   => $data['payment_option']   ?? $event['payment_option']   ?? null,
            'payment_method'   => $data['payment_method']   ?? $event['payment_method']   ?? null,
            'payment_ref'      => $data['payment_ref']      ?? $event['payment_ref']      ?? null,
            'supervisor_id'    => $data['supervisor_id']    ?? $event['supervisor_id']    ?? null,
            'booked_by'        => trim($data['booked_by']      ?? $event['booked_by']     ?? ''),
            'status'           => trim($data['status']         ?? $event['status']),
            'notes'            => trim($data['notes']          ?? $event['notes']         ?? ''),
            'remarks'          => trim($data['remarks']        ?? $event['remarks']       ?? ''),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->eventModel->update($id, $updateData);
        $updated = $this->eventModel->findById($id);
        $updated['remaining_balance'] = $updated['total_amount'] - $updated['down_payment'];
        $updated['reference_number']  = 'EVT-' . str_pad($id, 5, '0', STR_PAD_LEFT);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'events', "Event updated: $id");

        return response($updated, 200, 'Event updated');
    }

    public function updateStatus($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $data      = json_decode(file_get_contents('php://input'), true);
        $newStatus = $data['status'] ?? '';

        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowed)) return error('Invalid status', 400);

        // ── Balance check: prevent completion if there is an unpaid balance ────
        if ($newStatus === 'completed') {
            $totalAmount = (float)($event['total_amount'] ?? 0);
            $downPayment = (float)($event['down_payment'] ?? 0);
            $balance     = $totalAmount - $downPayment;
            if ($balance > 0.01) {
                return error(
                    'Cannot mark as completed: there is an unpaid balance of ₱' . number_format($balance, 2) . '. Please record the full payment before completing this event.',
                    422
                );
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        if ($newStatus === 'confirmed') {
            $updateFields = [
                'status'              => 'confirmed',
                'payment_option'      => $data['payment_option']      ?? $event['payment_option']      ?? null,
                'down_payment'        => isset($data['down_payment'])  ? (float)$data['down_payment']   : (float)$event['down_payment'],
                'payment_method'      => $data['payment_method']      ?? $event['payment_method']      ?? null,
                'online_payment_type' => $data['online_payment_type'] ?? $event['online_payment_type'] ?? null,
                'payment_ref'         => $data['payment_ref']         ?? $event['payment_ref']         ?? null,
                'updated_at'          => date('Y-m-d H:i:s'),
            ];
            $this->eventModel->update($id, $updateFields);
        } else {
            $this->eventModel->update($id, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        // ── Sync event bill when marked completed ──────────────────────────────
        if ($newStatus === 'completed') {
            $db          = $this->eventModel->getDb();
            $totalAmount = (float)($event['total_amount'] ?? 0);
            $downPayment = (float)($event['down_payment'] ?? 0);
            $payStatus   = ($downPayment >= $totalAmount && $totalAmount > 0) ? 'paid'
                         : ($downPayment > 0 ? 'partial' : 'pending');
            $now         = date('Y-m-d H:i:s');

            $chk = $db->prepare("SELECT id FROM bills WHERE event_id = ? AND bill_type = 'event' LIMIT 1");
            $chk->execute([$id]);
            $existingBill = $chk->fetch(\PDO::FETCH_ASSOC);

            if ($existingBill) {
                // Update existing bill with final payment info
                $db->prepare("
                    UPDATE bills
                       SET paid_amount    = ?,
                           payment_status = ?,
                           payment_date   = ?,
                           updated_at     = ?
                     WHERE id = ?
                ")->execute([
                    $downPayment,
                    $payStatus,
                    $payStatus !== 'pending' ? $now : null,
                    $now,
                    $existingBill['id'],
                ]);
            } else {
                $billNumber = 'EVT-' . $id . '-' . date('Ymd');
                $guestId    = null;
                if (!empty($event['client_email'])) {
                    $uStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                    $uStmt->execute([$event['client_email']]);
                    $uRow = $uStmt->fetch(\PDO::FETCH_ASSOC);
                    if ($uRow) $guestId = $uRow['id'];
                }

                $db->prepare("
                    INSERT INTO bills
                      (guest_id, event_id, bill_type, bill_number,
                       subtotal, discount, tax, total_amount,
                       payment_status, paid_amount, payment_date, created_at, updated_at)
                    VALUES (?,?,'event',?,?,0,0,?,?,?,?,?,?)
                ")->execute([
                    $guestId, $id, $billNumber,
                    $totalAmount, $totalAmount,
                    $payStatus, $downPayment,
                    $payStatus !== 'pending' ? $now : null,
                    $now, $now,
                ]);

                $billId = $db->lastInsertId();
                $desc   = ($event['event_type'] ?? 'Event')
                        . ' at ' . ($event['venue'] ?? '—')
                        . ' on ' . ($event['event_date'] ?? '—')
                        . ' (Client: ' . ($event['client_name'] ?? '—') . ')';
                $db->prepare("
                    INSERT INTO bill_items (bill_id, description, quantity, unit_price, amount, created_at)
                    VALUES (?,?,1,?,?,?)
                ")->execute([$billId, $desc, $totalAmount, $totalAmount, $now]);
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'events', "Event $id status → $newStatus");

        return response(['status' => $newStatus], 200, 'Status updated');
    }

    public function recordPayment($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return error('Invalid data', 400);

        $totalAmount    = (float)$event['total_amount'];
        $paymentOption  = $data['payment_option'] ?? 'downpayment';
        $newDownPayment = $paymentOption === 'full_payment'
            ? $totalAmount
            : (float)($data['down_payment'] ?? $event['down_payment'] ?? 0);

        // Clamp to total amount (can never overpay)
        if ($newDownPayment > $totalAmount) $newDownPayment = $totalAmount;

        $paymentMethod = $data['payment_method'] ?? $event['payment_method'] ?? null;
        $paymentRef    = $data['payment_ref']    ?? $event['payment_ref']    ?? null;
        $now           = date('Y-m-d H:i:s');

        // Update event payment fields
        $this->eventModel->update($id, [
            'down_payment'   => $newDownPayment,
            'payment_option' => $paymentOption,
            'payment_method' => $paymentMethod,
            'payment_ref'    => $paymentRef,
            'updated_at'     => $now,
        ]);

        // Determine payment_status based on how much has been paid
        $paymentStatus = ($newDownPayment >= $totalAmount && $totalAmount > 0) ? 'paid'
                       : ($newDownPayment > 0 ? 'partial' : 'pending');

        $db = $this->eventModel->getDb();

        // Check for an existing bill for this event
        $chk = $db->prepare("SELECT id FROM bills WHERE event_id = ? AND bill_type = 'event' LIMIT 1");
        $chk->execute([$id]);
        $existingBill = $chk->fetch(\PDO::FETCH_ASSOC);

        if ($existingBill) {
            // Update existing bill with latest payment info
            $db->prepare("
                UPDATE bills
                   SET paid_amount    = ?,
                       payment_status = ?,
                       payment_method = ?,
                       payment_date   = ?,
                       updated_at     = ?
                 WHERE id = ?
            ")->execute([
                $newDownPayment,
                $paymentStatus,
                $paymentMethod,
                $paymentStatus !== 'pending' ? $now : null,
                $now,
                $existingBill['id'],
            ]);
        } else {
            // Create a new bill record
            $billNumber = 'EVT-' . $id . '-' . date('Ymd');
            $guestId    = null;
            if (!empty($event['client_email'])) {
                $uStmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $uStmt->execute([$event['client_email']]);
                $uRow = $uStmt->fetch(\PDO::FETCH_ASSOC);
                if ($uRow) $guestId = $uRow['id'];
            }

            $db->prepare("
                INSERT INTO bills
                  (guest_id, event_id, bill_type, bill_number,
                   subtotal, discount, tax, total_amount,
                   payment_method, payment_status, paid_amount, payment_date,
                   created_at, updated_at)
                VALUES (?,?,'event',?,?,0,0,?,?,?,?,?,?,?)
            ")->execute([
                $guestId, $id, $billNumber,
                $totalAmount, $totalAmount,
                $paymentMethod,
                $paymentStatus,
                $newDownPayment,
                $paymentStatus !== 'pending' ? $now : null,
                $now, $now,
            ]);

            $billId = $db->lastInsertId();
            $desc   = ($event['event_type'] ?? 'Event')
                    . ' at ' . ($event['venue'] ?? '—')
                    . ' on '   . ($event['event_date'] ?? '—')
                    . ' (Client: ' . ($event['client_name'] ?? '—') . ')';
            $db->prepare("
                INSERT INTO bill_items (bill_id, description, quantity, unit_price, amount, created_at)
                VALUES (?,?,1,?,?,?)
            ")->execute([$billId, $desc, $totalAmount, $totalAmount, $now]);
        }

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'events', "Payment recorded for event $id: ₱$newDownPayment ($paymentStatus)");

        $updated = $this->eventModel->findById($id);
        $updated['remaining_balance'] = (float)$updated['total_amount'] - (float)$updated['down_payment'];
        $updated['reference_number']  = 'EVT-' . str_pad($id, 5, '0', STR_PAD_LEFT);
        return response($updated, 200, 'Payment recorded successfully');
    }

    public function delete($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $this->eventModel->softDelete($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'events', "Event archived: $id");

        return response([], 200, 'Event archived');
    }

    public function getArchived() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $search = $_GET['search'] ?? '';
        $data   = $this->eventModel->getArchived($search);
        return response($data, 200);
    }

    public function restore($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $this->eventModel->restore($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'events', "Event restored: $id");

        return response([], 200, 'Event restored');
    }

    public function forceDelete($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $event = $this->eventModel->findById($id);
        if (!$event) return error('Event not found', 404);

        $this->eventModel->forceDelete($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'events', "Event permanently deleted: $id");

        return response([], 200, 'Event permanently deleted');
    }
}
