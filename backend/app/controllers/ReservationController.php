<?php

namespace App\Controllers;

use App\Models\Room;
use App\Models\Reservation;

class ReservationController {
    public function getAvailableRooms() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return error('Method not allowed', 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['check_in_date']) || !isset($data['check_out_date'])) {
            return error('Check-in and check-out dates required', 400);
        }

        $roomModel = new Room();
        $rooms = $roomModel->getAvailableRooms($data['check_in_date'], $data['check_out_date']);

        return response($rooms, 200);
    }

    public function create() {
        $user = \App\Middleware\AuthMiddleware::handle();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return error('Method not allowed', 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['room_id', 'check_in_date', 'check_out_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $reservationModel = new Reservation();
        $reservationId = $reservationModel->create([
            'guest_id' => $user['user_id'],
            'room_id' => $data['room_id'],
            'check_in_date' => $data['check_in_date'],
            'check_out_date' => $data['check_out_date'],
            'number_of_guests' => $data['number_of_guests'] ?? 1,
            'special_requests' => $data['special_requests'] ?? null,
            'down_payment' => $data['down_payment'] ?? 0,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Log activity
        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'reservations', "Reservation created: $reservationId");

        return response(['reservation_id' => $reservationId], 201, 'Reservation created successfully');
    }

    public function getById($id) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $reservationModel = new Reservation();
        $reservation = $reservationModel->getReservationDetails($id);

        if (!$reservation) {
            return error('Reservation not found', 404);
        }

        return response($reservation, 200);
    }

    public function getGuestReservations() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $reservationModel = new Reservation();
        $reservations = $reservationModel->getGuestReservations($user['user_id']);

        return response($reservations, 200);
    }

    public function approve($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $reservationModel = new Reservation();
        $reservationModel->updateStatus($id, 'approved');

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'reservations', "Reservation approved: $id");

        return response([], 200, 'Reservation approved');
    }

    public function checkIn($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $reservationModel = new Reservation();
        $reservationModel->updateStatus($id, 'checked_in');

        $roomModel = new Room();
        $reservation = $reservationModel->findById($id);
        $roomModel->updateRoomStatus($reservation['room_id'], 'occupied');

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'reservations', "Guest checked in: $id");

        return response([], 200, 'Guest checked in successfully');
    }

    public function checkOut($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $reservationModel = new Reservation();
        $reservation = $reservationModel->getReservationDetails($id);

        if (!$reservation) {
            return error('Reservation not found', 404);
        }

        // ── Balance check: prevent checkout if there is an unpaid balance ──────
        $db = $reservationModel->getDb();

        // Check any existing bills for this reservation first
        $billStmt = $db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) AS total_billed,
                   COALESCE(SUM(paid_amount),  0) AS total_paid
            FROM bills WHERE reservation_id = ?
        ");
        $billStmt->execute([$id]);
        $billRow = $billStmt->fetch(\PDO::FETCH_ASSOC);

        if ($billRow && (float)$billRow['total_billed'] > 0) {
            // Bills exist — use them to determine outstanding balance
            $balance = (float)$billRow['total_billed'] - (float)$billRow['total_paid'];
        } else {
            // No bills yet — calculate from room price × nights minus down payment
            $rStmt = $db->prepare("SELECT price FROM rooms WHERE id = ? LIMIT 1");
            $rStmt->execute([$reservation['room_id']]);
            $roomRow = $rStmt->fetch(\PDO::FETCH_ASSOC);
            $pricePerNight = (float)($roomRow['price'] ?? 0);

            $checkIn  = new \DateTime($reservation['check_in_date']);
            $checkOut = new \DateTime($reservation['check_out_date']);
            $nights   = max(1, (int)$checkIn->diff($checkOut)->days);

            $totalAmount = $pricePerNight * $nights;
            $balance     = $totalAmount - (float)($reservation['down_payment'] ?? 0);
        }

        if ($balance > 0.01) {
            return error(
                'Cannot check out: there is an unpaid balance of ₱' . number_format($balance, 2) . '. Please settle the full amount before checking out.',
                422
            );
        }
        // ─────────────────────────────────────────────────────────────────────

        $reservationModel->updateStatus($id, 'checked_out');

        $roomModel = new Room();
        $roomModel->updateRoomStatus($reservation['room_id'], 'dirty');

        // ── Auto-generate room bill on checkout (if none exists yet) ──────────
        $chk = $db->prepare("SELECT id FROM bills WHERE reservation_id = ? AND bill_type = 'room' LIMIT 1");
        $chk->execute([$id]);
        if (!$chk->fetch()) {
            $checkIn  = new \DateTime($reservation['check_in_date']);
            $checkOut = new \DateTime($reservation['check_out_date']);
            $nights   = max(1, (int)$checkIn->diff($checkOut)->days);

            // Get room price
            $rStmt = $db->prepare("SELECT price FROM rooms WHERE id = ? LIMIT 1");
            $rStmt->execute([$reservation['room_id']]);
            $roomRow     = $rStmt->fetch(\PDO::FETCH_ASSOC);
            $pricePerNight = (float)($roomRow['price'] ?? 0);
            $totalAmount   = $pricePerNight * $nights;
            $downPayment   = (float)($reservation['down_payment'] ?? 0);
            $now           = date('Y-m-d H:i:s');
            $billNumber    = 'ROOM-' . $id . '-' . date('Ymd');

            $db->prepare("
                INSERT INTO bills
                  (guest_id, reservation_id, bill_type, bill_number,
                   subtotal, discount, tax, total_amount,
                   payment_status, paid_amount, created_at, updated_at)
                VALUES (?,?,'room',?,?,0,0,?,'pending',?,?,?)
            ")->execute([
                $reservation['guest_id'], $id, $billNumber,
                $totalAmount, $totalAmount, $downPayment, $now, $now,
            ]);

            $billId = $db->lastInsertId();
            $db->prepare("
                INSERT INTO bill_items (bill_id, description, quantity, unit_price, amount, created_at)
                VALUES (?,?,?,?,?,?)
            ")->execute([
                $billId,
                "Room {$reservation['room_number']} ({$reservation['room_type']}) — {$nights} night(s) @ ₱" . number_format($pricePerNight, 2),
                $nights,
                $pricePerNight,
                $totalAmount,
                $now,
            ]);
        }
        // ─────────────────────────────────────────────────────────────────────

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'reservations', "Guest checked out: $id");

        return response([], 200, 'Guest checked out successfully');
    }

    public function cancel($id) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $reservationModel = new Reservation();
        $reservation = $reservationModel->findById($id);

        if (!$reservation) {
            return error('Reservation not found', 404);
        }

        if ($reservation['guest_id'] != $user['user_id'] && $user['role'] === 'guest') {
            return error('Unauthorized', 403);
        }

        $reservationModel->updateStatus($id, 'cancelled');

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'reservations', "Reservation cancelled: $id");

        return response([], 200, 'Reservation cancelled');
    }

    public function adminGetAll() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $filters = [
            'search' => $_GET['search'] ?? null,
            'status' => $_GET['status'] ?? null,
            'date'   => $_GET['date'] ?? null,
        ];

        $reservationModel = new Reservation();
        $reservations = $reservationModel->getAllReservations($filters);

        return response($reservations, 200);
    }

    public function adminGetStats() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $reservationModel = new Reservation();
        $stats = $reservationModel->getStats();

        return response($stats, 200);
    }

    public function adminGetBookedDates() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $reservationModel = new Reservation();
        $dates = $reservationModel->getBookedDates();

        return response($dates, 200);
    }

    public function walkIn() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['room_id', 'guest_name', 'check_in_date', 'check_out_date', 'number_of_guests'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        // For walk-in, we may need to find or create a guest user
        $userModel = new \App\Models\User();
        $guestId = null;

        if (!empty($data['guest_id'])) {
            $guestId = $data['guest_id'];
        } else {
            // Try to find an existing user by email first to avoid duplicate-key errors
            $email = !empty($data['guest_email']) ? $data['guest_email'] : null;
            $existingGuest = $email ? $userModel->findByEmail($email) : null;

            if ($existingGuest) {
                $guestId = $existingGuest['id'];
            } else {
                // Create a new guest user
                $guestData = [
                    'name'       => $data['guest_name'],
                    'email'      => $email ?? ('walkin_' . time() . '@srcbhotel.com'),
                    'password'   => password_hash('walkin' . time(), PASSWORD_DEFAULT),
                    'role'       => 'guest',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $guestId = $userModel->create($guestData);
            }
        }

        // Update guest profile with extra fields that exist on the users table
        $profileUpdate = array_filter(['phone' => $data['contact_number'] ?? null]);
        if (!empty($profileUpdate)) {
            $userModel->update($guestId, $profileUpdate);
        }

        $reservationData = [
            'guest_id'         => $guestId,
            'room_id'          => $data['room_id'],
            'check_in_date'    => $data['check_in_date'],
            'check_out_date'   => $data['check_out_date'],
            'number_of_guests' => $data['number_of_guests'],
            'special_requests' => $data['special_requests'] ?? null,
            'remarks'          => $data['remarks'] ?? null,
            'payment_option'   => $data['payment_option'] ?? 'full_payment',
            'down_payment'     => $data['down_payment'] ?? 0,
            'status'           => 'approved',
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $reservationModel = new Reservation();
        $id = $reservationModel->create($reservationData);

        // Set room to occupied
        $roomModel = new \App\Models\Room();
        $roomModel->updateRoomStatus($data['room_id'], 'occupied');

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'reservations', "Walk-in reservation created: $id");

        $newReservation = $reservationModel->getReservationDetails($id);
        return response($newReservation, 201, 'Walk-in reservation created');
    }

    public function searchGuests() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $q = $_GET['q'] ?? '';
        if (strlen($q) < 2) return response([], 200);

        $db = \Database::connect();
        $like = '%' . $q . '%';
        $stmt = $db->prepare("SELECT id, name, email, phone AS contact_number FROM users WHERE role = 'guest' AND active = 1 AND (name LIKE :n OR email LIKE :e OR phone LIKE :p) LIMIT 10");
        $stmt->execute([':n' => $like, ':e' => $like, ':p' => $like]);
        return response($stmt->fetchAll(), 200);
    }

    public function update($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $data = json_decode(file_get_contents('php://input'), true);

        $reservationModel = new Reservation();
        $reservation = $reservationModel->findById($id);

        if (!$reservation) {
            return error('Reservation not found', 404);
        }

        $updateData = [];
        $allowed = ['check_in_date', 'check_out_date', 'number_of_guests', 'special_requests', 'down_payment', 'room_id'];
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        $reservationModel->update($id, $updateData);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'reservations', "Reservation updated: $id");

        return response($reservationModel->getReservationDetails($id), 200, 'Reservation updated');
    }
}
