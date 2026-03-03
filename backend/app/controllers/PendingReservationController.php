<?php

namespace App\Controllers;

use App\Models\PendingReservation;

class PendingReservationController {

    // ── Public: Guest submits a reservation request ────────────────────────
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return error('Method not allowed', 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['guest_name'])) {
            return error('Guest name is required', 400);
        }

        $type = $data['reservation_type'] ?? 'room';
        if ($type === 'room' && empty($data['check_in_date'])) {
            return error('Check-in date is required for room reservations', 400);
        }

        $ref = 'PR-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));

        $model = new PendingReservation();
        $id = $model->create([
            'reference_number'  => $ref,
            'reservation_type'  => $type,
            'guest_name'        => $data['guest_name'],
            'guest_email'       => $data['guest_email']       ?? null,
            'guest_contact'     => $data['guest_contact']     ?? null,
            'guest_address'     => $data['guest_address']     ?? null,
            'guest_country'     => $data['guest_country']     ?? null,
            'room_id'           => $data['room_id']           ?? null,
            'check_in_date'     => $data['check_in_date']     ?? null,
            'check_out_date'    => $data['check_out_date']    ?? null,
            'number_of_guests'  => $data['number_of_guests']  ?? 1,
            'event_name'        => $data['event_name']        ?? null,
            'event_type'        => $data['event_type']        ?? null,
            'event_date'        => $data['event_date']        ?? null,
            'event_time'        => $data['event_time']        ?? null,
            'event_package_id'  => $data['event_package_id']  ?? null,
            'buffet_set'        => $data['buffet_set']        ?? null,
            'supervisor'        => $data['supervisor']        ?? null,
            'selected_foods'    => isset($data['selected_foods']) ? implode(', ', (array)$data['selected_foods']) : null,
            'special_requests'  => $data['special_requests']  ?? null,
            'remarks'           => $data['remarks']           ?? null,
            'estimated_total'   => $data['estimated_total']   ?? 0,
            'status'            => 'pending',
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        return response(['id' => $id, 'reference_number' => $ref], 201, 'Reservation request submitted successfully');
    }

    // ── Admin: Get all pending reservation requests ────────────────────────
    public function getAll() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $status = $_GET['status'] ?? null;
        $type   = $_GET['type']   ?? null;

        $model = new PendingReservation();
        $list  = $model->getFiltered($status, $type);

        return response($list, 200);
    }

    // ── Admin: Get single pending reservation ─────────────────────────────
    public function getById($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $model   = new PendingReservation();
        $pending = $model->findById($id);

        if (!$pending) return error('Request not found', 404);
        return response($pending, 200);
    }

    // ── Admin: Approve ─────────────────────────────────────────────────────
    public function approve($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data  = json_decode(file_get_contents('php://input'), true) ?? [];
        $model = new PendingReservation();
        $pending = $model->findById($id);

        if (!$pending) return error('Request not found', 404);
        if ($pending['status'] !== 'pending') return error('This request has already been processed', 409);

        if ($pending['reservation_type'] === 'room') {
            // ── Create / find a guest user account ──────────────────────
            $userModel  = new \App\Models\User();
            $guestEmail = !empty($pending['guest_email'])
                ? $pending['guest_email']
                : 'online_' . $id . '_' . time() . '@srcbhotel.com';

            $existing = $userModel->findOne(['email' => $guestEmail]);
            if ($existing) {
                $guestId = $existing['id'];
            } else {
                $guestId = $userModel->create([
                    'name'       => $pending['guest_name'],
                    'email'      => $guestEmail,
                    'password'   => password_hash('guest' . $id . time(), PASSWORD_DEFAULT),
                    'role'       => 'guest',
                    'active'     => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // ── Create the reservation ───────────────────────────────────
            $paymentOption = $data['payment_option'] ?? 'full_payment';
            $downPayment   = $paymentOption === 'downpayment' ? (float)($data['down_payment'] ?? 0) : 0;

            $reservationModel = new \App\Models\Reservation();
            $resId = $reservationModel->create([
                'guest_id'         => $guestId,
                'room_id'          => $pending['room_id'],
                'check_in_date'    => $pending['check_in_date'],
                'check_out_date'   => $pending['check_out_date'],
                'number_of_guests' => $pending['number_of_guests'],
                'special_requests' => $pending['special_requests'],
                'down_payment'     => $downPayment,
                'payment_option'   => $paymentOption,
                'status'           => 'approved',
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            // ── Mark room as reserved ────────────────────────────────────
            $roomModel = new \App\Models\Room();
            $roomModel->updateRoomStatus($pending['room_id'], 'reserved');

            // ── Update pending record ────────────────────────────────────
            $model->update($id, [
                'status'         => 'approved',
                'payment_option' => $paymentOption,
                'down_payment'   => $downPayment,
                'approved_by'    => $user['user_id'],
                'approved_at'    => date('Y-m-d H:i:s'),
                'reservation_id' => $resId,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $auditLog = new \App\Models\AuditLog();
            $auditLog->log($user['user_id'], 'approve', 'pending_reservations',
                "Online room reservation approved: pending #$id → reservation #$resId");

            $reservation = $reservationModel->getReservationDetails($resId);
            return response($reservation, 200, 'Room reservation approved successfully');
        }

        // ── Event type: create an actual event record ────────────────────
        $paymentOption = $data['payment_option'] ?? 'full_payment';
        $downPayment   = $paymentOption === 'downpayment' ? (float)($data['down_payment'] ?? 0) : 0;
        $paymentMethod = $data['payment_method']      ?? null;
        $onlineType    = $data['online_payment_type'] ?? null;
        $paymentRef    = $data['payment_ref']         ?? null;

        $eventModel = new \App\Models\Event();
        $eventId = $eventModel->create([
            'event_name'       => $pending['event_name']       ?? '',
            'event_type'       => $pending['event_type']       ?? 'Event',
            'client_name'      => $pending['guest_name'],
            'client_email'     => $pending['guest_email']      ?? '',
            'client_phone'     => $pending['guest_contact']    ?? '',
            'client_address'   => $pending['guest_address']    ?? '',
            'event_date'       => $pending['event_date'],
            'event_time'       => $pending['event_time']       ?? '08:00:00',
            'number_of_guests' => (int)($pending['number_of_guests'] ?? 1),
            'additional_guests'=> 0,
            'venue'            => 'SRCB Hotel & Café',
            'package_id'       => $pending['event_package_id'] ?? null,
            'package_set'      => $pending['buffet_set']       ?? '',
            'price_per_head'   => 0,
            'total_amount'     => (float)($pending['estimated_total'] ?? 0),
            'down_payment'     => $downPayment,
            'payment_option'   => $paymentOption,
            'payment_method'   => ($paymentMethod === 'online' && $onlineType) ? $onlineType : ($paymentMethod ?? null),
            'payment_ref'      => $paymentRef,
            'source'           => 'online',
            'status'           => 'confirmed',
            'notes'            => trim(implode(' | ', array_filter([
                $pending['special_requests'] ?? null,
                $pending['selected_foods']   ? 'Foods: ' . $pending['selected_foods'] : null,
            ]))),
            'remarks'          => $pending['remarks'] ?? '',
            'booked_by'        => $pending['guest_name'],
            'created_by'       => $user['user_id'],
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        // ── Update pending record ────────────────────────────────────────
        $model->update($id, [
            'status'         => 'approved',
            'payment_option' => $paymentOption,
            'down_payment'   => $downPayment,
            'approved_by'    => $user['user_id'],
            'approved_at'    => date('Y-m-d H:i:s'),
            'reservation_id' => $eventId,   // reuse column to store event ID
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'approve', 'pending_reservations',
            "Online event reservation approved: pending #$id → event #$eventId");

        $event = $eventModel->findById($eventId);
        $event['remaining_balance'] = $event['total_amount'] - $event['down_payment'];
        $event['reference_number']  = 'EVT-' . str_pad($eventId, 5, '0', STR_PAD_LEFT);
        return response($event, 200, 'Event reservation approved and created successfully');
    }

    // ── Admin: Reject ──────────────────────────────────────────────────────
    public function reject($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data    = json_decode(file_get_contents('php://input'), true) ?? [];
        $model   = new PendingReservation();
        $pending = $model->findById($id);

        if (!$pending) return error('Request not found', 404);
        if ($pending['status'] !== 'pending') return error('This request has already been processed', 409);

        $model->update($id, [
            'status'           => 'rejected',
            'rejection_reason' => $data['reason'] ?? null,
            'rejected_at'      => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'reject', 'pending_reservations',
            "Reservation request rejected: pending #$id" . (isset($data['reason']) ? " — {$data['reason']}" : ''));

        return response([], 200, 'Reservation request rejected');
    }

    // ── Admin: Duplicate-check helper ─────────────────────────────────────
    public function checkDuplicate() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $email   = $_GET['email']         ?? '';
        $name    = $_GET['name']          ?? '';
        $checkIn = $_GET['check_in_date'] ?? '';
        $roomId  = $_GET['room_id']       ?? '';

        $db = \Database::connect();
        $matches = [];

        // Check existing reservations with same room and overlapping dates
        if ($roomId && $checkIn) {
            $stmt = $db->prepare("
                SELECT r.id, r.check_in_date, r.check_out_date, r.status, u.name AS guest_name, u.email AS guest_email
                FROM reservations r
                LEFT JOIN users u ON r.guest_id = u.id
                WHERE r.room_id = ?
                  AND r.status NOT IN ('cancelled','checked_out')
                  AND r.check_in_date <= ?
                  AND r.check_out_date >= ?
                LIMIT 5
            ");
            $stmt->execute([$roomId, $checkIn, $checkIn]);
            $matches = $stmt->fetchAll();
        }

        // Check if same guest has a pending reservation
        $pendingByGuest = [];
        if ($email || $name) {
            $conditions = $email
                ? "pr.guest_email = ?"
                : "pr.guest_name LIKE ?";
            $param = $email ?: "%$name%";
            $stmt = $db->prepare("
                SELECT pr.*, r.room_number FROM pending_reservations pr
                LEFT JOIN rooms r ON pr.room_id = r.id
                WHERE $conditions AND pr.status = 'pending'
                LIMIT 5
            ");
            $stmt->execute([$param]);
            $pendingByGuest = $stmt->fetchAll();
        }

        return response([
            'room_conflicts'    => $matches,
            'pending_by_guest'  => $pendingByGuest,
            'has_conflicts'     => count($matches) > 0 || count($pendingByGuest) > 0,
        ], 200);
    }
}
