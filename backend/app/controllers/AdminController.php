<?php

namespace App\Controllers;

use App\Models\AuditLog;
use App\Models\Settings;

class AdminController {
    public function getDashboard() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        // Get statistics
        $roomModel = new \App\Models\Room();
        $reservationModel = new \App\Models\Reservation();
        $userModel = new \App\Models\User();

        $stats = [
            'total_rooms' => $roomModel->count(),
            'total_reservations' => $reservationModel->count(),
            'total_staff' => $userModel->count(),
            'upcoming_check_ins' => count($reservationModel->getUpcomingReservations(7)),
        ];

        return response($stats, 200);
    }

    public function getActivityLogs() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'module' => $_GET['module'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null,
        ];

        $auditLog = new AuditLog();
        $logs = $auditLog->getActivityLog($filters);

        return response($logs, 200);
    }

    public function getPublicSettings() {
        $settingsModel = new Settings();
        $keys = ['hotel_name', 'hotel_email', 'hotel_phone', 'hotel_address'];
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settingsModel->get($key, '');
        }
        return response($result, 200);
    }

    public function getSettings() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $settingsModel = new Settings();
        $settings = $settingsModel->all();

        return response($settings, 200);
    }

    public function updateSettings() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $settingsModel = new Settings();
        foreach ($data as $key => $value) {
            $settingsModel->set($key, $value);
        }

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'update', 'settings', 'System settings updated');

        return response([], 200, 'Settings updated');
    }

    public function getAllUsers() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager']);

        $db = (new \App\Models\User())->getDb();
        $stmt = $db->prepare("
            SELECT u.*, COALESCE(s.salary, NULL) AS hourly_rate
            FROM users u
            LEFT JOIN staff s ON s.user_id = u.id
            WHERE u.active = 1
            ORDER BY u.name ASC
        ");
        $stmt->execute();
        $users = $stmt->fetchAll();

        return response($users, 200);
    }

    public function getArchivedUsers() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $userModel = new \App\Models\User();
        $users = $userModel->getArchivedUsers();

        return response($users, 200);
    }

    public function restoreUser($userId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $userModel = new \App\Models\User();
        $userModel->update($userId, ['active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'users', "User restored: $userId");

        return response([], 200, 'User restored');
    }

    public function getDailyAttendance() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $date = $_GET['date'] ?? date('Y-m-d');

        $attendanceModel = new \App\Models\Attendance();
        $records = $attendanceModel->getDailyAttendance($date);

        $total   = count($records);
        $present = count(array_filter($records, fn($r) => $r['status'] === 'present'));
        $absent  = $total - $present;

        return response([
            'date'    => $date,
            'summary' => compact('total', 'present', 'absent'),
            'records' => $records,
        ], 200);
    }

    public function adminClockIn() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data    = json_decode(file_get_contents('php://input'), true);
        $staffId = $data['staff_id'] ?? null;
        $date    = $data['date']     ?? date('Y-m-d');
        $time    = $data['time']     ?? null;

        if (!$staffId) return error('staff_id is required', 400);

        $attendanceModel = new \App\Models\Attendance();
        $id = $attendanceModel->adminClockIn($staffId, $date, $time ? "$date $time" : null);

        if (!$id) return error('Already has a clock-in record for this date', 409);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'attendance', "Admin clocked in staff $staffId for $date");

        return response(['attendance_id' => $id], 201, 'Clocked in');
    }

    public function adminClockOut($attendanceId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);
        $date = $data['date'] ?? date('Y-m-d');
        $time = $data['time'] ?? null;

        $attendanceModel = new \App\Models\Attendance();
        $attendanceModel->adminClockOut($attendanceId, $time ? "$date $time" : null);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'attendance', "Admin clocked out attendance record $attendanceId");

        return response([], 200, 'Clocked out');
    }

    public function createUser() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['name', 'email', 'password', 'role'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $userModel = new \App\Models\User();

        if ($userModel->findByEmail($data['email'])) {
            return error('Email already exists', 409);
        }

        $userId = $userModel->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'active' => 1,
        ]);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'create', 'users', "User created: $userId");

        return response(['user_id' => $userId], 201, 'User created');
    }

    public function updateUser($userId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        // Allow IT role to update email and password
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        } else {
            unset($data['password']);
        }

        $userModel = new \App\Models\User();
        $userModel->update($userId, $data);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'update', 'users', "User updated: $userId");

        return response([], 200, 'User updated');
    }

    public function deleteUser($userId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $userModel = new \App\Models\User();

        $target = $userModel->findById($userId);
        if (!$target) return error('User not found', 404);

        // Soft-delete: set active = 0 so related records remain intact
        $userModel->update($userId, [
            'active'     => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'users', "User archived: $userId");

        return response([], 200, 'User deleted');
    }

    public function getUserQrCode($userId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $userModel = new \App\Models\User();
        $target = $userModel->findById($userId);

        if (!$target) return error('User not found', 404);

        // Ensure the user has a qr_token (backfill for legacy accounts)
        if (empty($target['qr_token'])) {
            $token = bin2hex(random_bytes(16));
            $userModel->update($userId, ['qr_token' => $token]);
            $target['qr_token'] = $token;
        }

        return response([
            'user_id'  => $target['id'],
            'name'     => $target['name'],
            'qr_token' => $target['qr_token'],
        ], 200);
    }

    public function exportReport() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $type   = $_GET['type']   ?? 'reservations';
        $format = $_GET['format'] ?? 'json';
        $month  = $_GET['month']  ?? null; // e.g. "2026-02"

        try {
            $data = [];
            if ($type === 'reservations') {
                $db = \Database::connect();
                // guest_id is the correct FK column in the reservations table
                $sql = "
                    SELECT
                        CONCAT('RES-', LPAD(r.id, 5, '0')) AS reference_number,
                        r.id,
                        COALESCE(u.name,  'N/A')  AS guest_name,
                        COALESCE(u.email, 'N/A')  AS guest_email,
                        COALESCE(ro.room_number, r.room_id) AS room_number,
                        COALESCE(ro.type,  'N/A') AS room_type,
                        COALESCE(ro.price, 0)     AS room_price,
                        r.check_in_date,
                        r.check_out_date,
                        GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 1) AS nights,
                        ROUND(COALESCE(ro.price, 0) * GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 1), 2) AS total_amount,
                        COALESCE(r.down_payment, 0) AS down_payment,
                        ROUND(COALESCE(ro.price, 0) * GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 1) - COALESCE(r.down_payment, 0), 2) AS remaining_balance,
                        r.status,
                        COALESCE(r.special_requests, '') AS special_requests,
                        r.created_at
                    FROM reservations r
                    LEFT JOIN users u  ON u.id  = r.guest_id
                    LEFT JOIN rooms ro ON ro.id = r.room_id
                    WHERE 1=1";
                if ($month) {
                    $sql .= " AND DATE_FORMAT(r.check_in_date, '%Y-%m') = " . $db->quote($month);
                }
                $sql .= " ORDER BY r.check_in_date DESC";
                $stmt = $db->query($sql);
                $data = $stmt->fetchAll();

            } elseif ($type === 'events') {
                $db = \Database::connect();
                // Query the events table directly — it has client_name stored inline
                $sql = "
                    SELECT
                        CONCAT('EVT-', LPAD(e.id, 5, '0')) AS reference_number,
                        e.id,
                        e.event_type,
                        e.client_name,
                        COALESCE(e.client_phone, '') AS client_phone,
                        COALESCE(e.venue, '')        AS venue,
                        e.event_date,
                        e.event_time,
                        COALESCE(e.number_of_guests, 0) AS number_of_guests,
                        COALESCE(e.price_per_head, 0)   AS price_per_head,
                        COALESCE(e.total_amount, 0)     AS total_amount,
                        COALESCE(e.down_payment, 0)     AS down_payment,
                        ROUND(COALESCE(e.total_amount, 0) - COALESCE(e.down_payment, 0), 2) AS remaining_balance,
                        e.status,
                        COALESCE(e.notes, '') AS notes,
                        e.created_at
                    FROM events e
                    WHERE e.deleted_at IS NULL";
                if ($month) {
                    $sql .= " AND DATE_FORMAT(e.event_date, '%Y-%m') = " . $db->quote($month);
                }
                $sql .= " ORDER BY e.event_date DESC, e.event_time DESC";
                $stmt = $db->query($sql);
                $data = $stmt->fetchAll();

            } elseif ($type === 'revenue') {
                $billModel = new \App\Models\Bill();
                $data = $billModel->all();
            } elseif ($type === 'inventory') {
                $inventoryModel = new \App\Models\Inventory();
                $data = $inventoryModel->all();
            }

            if ($format === 'csv') {
                return $this->exportCSV($data, $type);
            }

            return response($data, 200);

        } catch (\PDOException $e) {
            return error('Database error during export: ' . $e->getMessage(), 500);
        } catch (\Exception $e) {
            return error('Export failed: ' . $e->getMessage(), 500);
        }
    }

    private function exportCSV($data, $type) {
        if (empty($data)) {
            return error('No data available to export for the selected period', 404);
        }

        // Friendly column headers for each export type
        $headerMaps = [
            'reservations' => [
                'reference_number' => 'Reference #',
                'id'               => 'ID',
                'guest_name'       => 'Guest Name',
                'guest_email'      => 'Email',
                'room_number'      => 'Room Number',
                'room_type'        => 'Room Type',
                'room_price'       => 'Rate/Night (PHP)',
                'check_in_date'    => 'Check-In Date',
                'check_out_date'   => 'Check-Out Date',
                'nights'           => 'Nights',
                'total_amount'     => 'Total Amount (PHP)',
                'down_payment'     => 'Down Payment (PHP)',
                'remaining_balance'=> 'Remaining Balance (PHP)',
                'status'           => 'Status',
                'special_requests' => 'Special Requests',
                'created_at'       => 'Created At',
            ],
            'events' => [
                'reference_number'  => 'Reference #',
                'id'                => 'ID',
                'event_type'        => 'Event Type',
                'client_name'       => 'Client Name',
                'client_phone'      => 'Contact Number',
                'venue'             => 'Venue',
                'event_date'        => 'Event Date',
                'event_time'        => 'Event Time',
                'number_of_guests'  => 'No. of Guests',
                'price_per_head'    => 'Price/Head (PHP)',
                'total_amount'      => 'Total Amount (PHP)',
                'down_payment'      => 'Down Payment (PHP)',
                'remaining_balance' => 'Remaining Balance (PHP)',
                'status'            => 'Status',
                'notes'             => 'Notes',
                'created_at'        => 'Created At',
            ],
        ];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $type . '_export_' . date('Y-m-d') . '.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8 compatibility
        fwrite($output, "\xEF\xBB\xBF");

        $map = $headerMaps[$type] ?? [];
        $keys = array_keys($data[0]);
        // Write header row
        $headers = array_map(fn($k) => $map[$k] ?? ucwords(str_replace('_', ' ', $k)), $keys);
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, array_values($row));
        }

        fclose($output);
        exit;
    }

    public function getAnalytics() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db = \Database::connect();

        // --- Summary Cards ---

        // Upcoming Events (pending/confirmed from events table)
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM events WHERE status IN ('pending','confirmed') AND deleted_at IS NULL");
        $upcomingEvents = (int)$stmt->fetch()['cnt'];

        // Total Staff
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM users WHERE role IN ('manager','front_desk','housekeeping','chef','accountant') AND active = 1");
        $totalStaff = (int)$stmt->fetch()['cnt'];

        // Available Rooms
        $stmt = $db->query("SELECT COUNT(*) as cnt FROM rooms WHERE status = 'available'");
        $availableRooms = (int)$stmt->fetch()['cnt'];

        // ── Room Revenue ──────────────────────────────────────────────────────
        // Completed room reservations = reservations with status 'checked_out'
        // Bills are auto-created as 'pending' on checkout, so we use reservation
        // status (not payment status) as the signal that the service was delivered.
        $stmt = $db->query("
            SELECT COALESCE(SUM(b.total_amount), 0) as room_revenue
            FROM bills b
            INNER JOIN reservations r ON r.id = b.reservation_id
            WHERE b.bill_type = 'room'
              AND r.status = 'checked_out'
        ");
        $roomRevenue = (float)$stmt->fetchColumn();

        // ── Event Revenue ─────────────────────────────────────────────────────
        // Primary: events confirmed/completed (revenue recognised when approved)
        $stmt = $db->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM events
            WHERE status IN ('confirmed','completed') AND deleted_at IS NULL
        ");
        $eventRevFromEvents = (float)$stmt->fetchColumn();

        // Secondary: event bills paid/partial that are NOT already covered above
        // (handles legacy bills or manual billing entries with no events row)
        $stmt = $db->query("
            SELECT COALESCE(SUM(b.total_amount), 0)
            FROM bills b
            WHERE b.bill_type = 'event'
              AND b.payment_status IN ('paid','partial')
              AND (
                b.event_id IS NULL OR b.event_id = 0
                OR NOT EXISTS (
                  SELECT 1 FROM events e
                  WHERE e.id = b.event_id
                    AND e.status IN ('confirmed','completed')
                    AND e.deleted_at IS NULL
                )
              )
        ");
        $eventRevFromBills = (float)$stmt->fetchColumn();
        $eventRevenue = $eventRevFromEvents + $eventRevFromBills;

        // ── Café Revenue (restaurant + room-service bills) ────────────────────
        // Include paid, partial, and pending bills that belong to a checked_out
        // reservation (room-service charged to room during stay).
        $stmt = $db->query("
            SELECT COALESCE(SUM(b.total_amount), 0) as cafe_revenue
            FROM bills b
            WHERE b.bill_type IN ('restaurant','room_service')
              AND b.payment_status IN ('paid','partial')
        ");
        $cafeRevenue = (float)$stmt->fetchColumn();

        // ── POS Revenue (direct cash/card transactions, not charged-to-room) ─
        $stmt = $db->query("
            SELECT COALESCE(SUM(total_amount), 0) as pos_revenue
            FROM orders
            WHERE order_type = 'pos' AND status = 'completed'
              AND (charge_to_room = 0 OR charge_to_room IS NULL)
        ");
        $posRevenue = (float)$stmt->fetchColumn();

        $grandTotal = $roomRevenue + $eventRevenue + $cafeRevenue + $posRevenue;

        // --- Monthly Revenue Trends (last 7 months) — all 4 sources ----------
        $stmt = $db->query("
            SELECT
                ym AS month,
                SUM(CASE WHEN src = 'room'  THEN amt ELSE 0 END) AS room,
                SUM(CASE WHEN src = 'cafe'  THEN amt ELSE 0 END) AS cafe,
                SUM(CASE WHEN src = 'event' THEN amt ELSE 0 END) AS event,
                SUM(CASE WHEN src = 'pos'   THEN amt ELSE 0 END) AS pos
            FROM (
                SELECT DATE_FORMAT(b.created_at,'%Y-%m') AS ym, b.total_amount AS amt, 'room' AS src
                FROM bills b
                INNER JOIN reservations r ON r.id = b.reservation_id
                WHERE b.bill_type = 'room' AND r.status = 'checked_out'
                  AND b.created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
                UNION ALL
                SELECT DATE_FORMAT(created_at,'%Y-%m'), total_amount, 'cafe'
                FROM bills
                WHERE bill_type IN ('restaurant','room_service')
                  AND payment_status IN ('paid','partial')
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
                UNION ALL
                SELECT DATE_FORMAT(event_date,'%Y-%m'), total_amount, 'event'
                FROM events
                WHERE status IN ('confirmed','completed') AND deleted_at IS NULL
                  AND event_date >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
                UNION ALL
                SELECT DATE_FORMAT(created_at,'%Y-%m'), total_amount, 'pos'
                FROM orders
                WHERE order_type = 'pos' AND status = 'completed'
                  AND (charge_to_room = 0 OR charge_to_room IS NULL)
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
            ) combined
            GROUP BY ym
            ORDER BY ym ASC
        ");
        $revenueTrends = $stmt->fetchAll();

        // --- Daily Revenue Breakdown (last 7 days) ---------------------------
        $stmt = $db->query("
            SELECT
                day,
                SUM(CASE WHEN src = 'room'  THEN amt ELSE 0 END) AS room,
                SUM(CASE WHEN src = 'cafe'  THEN amt ELSE 0 END) AS cafe,
                SUM(CASE WHEN src = 'event' THEN amt ELSE 0 END) AS event,
                SUM(CASE WHEN src = 'pos'   THEN amt ELSE 0 END) AS pos,
                SUM(amt) AS total
            FROM (
                SELECT DATE(b.created_at) AS day, b.total_amount AS amt, 'room' AS src
                FROM bills b
                INNER JOIN reservations r ON r.id = b.reservation_id
                WHERE b.bill_type = 'room' AND r.status = 'checked_out'
                  AND b.created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                UNION ALL
                SELECT DATE(created_at), total_amount, 'cafe'
                FROM bills
                WHERE bill_type IN ('restaurant','room_service')
                  AND payment_status IN ('paid','partial')
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                UNION ALL
                SELECT DATE(event_date), total_amount, 'event'
                FROM events
                WHERE status IN ('confirmed','completed') AND deleted_at IS NULL
                  AND event_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                UNION ALL
                SELECT DATE(created_at), total_amount, 'pos'
                FROM orders
                WHERE order_type = 'pos' AND status = 'completed'
                  AND (charge_to_room = 0 OR charge_to_room IS NULL)
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            ) d
            GROUP BY day
            ORDER BY day ASC
        ");
        $dailyTrends = $stmt->fetchAll();

        // --- Weekly Revenue Breakdown (last 8 weeks) -------------------------
        $stmt = $db->query("
            SELECT
                YEARWEEK(day, 1) AS yw,
                MIN(day)         AS week_start,
                SUM(CASE WHEN src = 'room'  THEN amt ELSE 0 END) AS room,
                SUM(CASE WHEN src = 'cafe'  THEN amt ELSE 0 END) AS cafe,
                SUM(CASE WHEN src = 'event' THEN amt ELSE 0 END) AS event,
                SUM(CASE WHEN src = 'pos'   THEN amt ELSE 0 END) AS pos,
                SUM(amt) AS total
            FROM (
                SELECT DATE(b.created_at) AS day, b.total_amount AS amt, 'room' AS src
                FROM bills b
                INNER JOIN reservations r ON r.id = b.reservation_id
                WHERE b.bill_type = 'room' AND r.status = 'checked_out'
                  AND b.created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                UNION ALL
                SELECT DATE(created_at), total_amount, 'cafe'
                FROM bills
                WHERE bill_type IN ('restaurant','room_service')
                  AND payment_status IN ('paid','partial')
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                UNION ALL
                SELECT DATE(event_date), total_amount, 'event'
                FROM events
                WHERE status IN ('confirmed','completed') AND deleted_at IS NULL
                  AND event_date >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                UNION ALL
                SELECT DATE(created_at), total_amount, 'pos'
                FROM orders
                WHERE order_type = 'pos' AND status = 'completed'
                  AND (charge_to_room = 0 OR charge_to_room IS NULL)
                  AND created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
            ) d
            GROUP BY yw
            ORDER BY yw ASC
        ");
        $weeklyTrends = $stmt->fetchAll();

        // --- Popularity Analysis ---

        // Room type popularity (by reservations)
        $stmt = $db->query("
            SELECT r.type, COUNT(res.id) as reservations
            FROM rooms r
            LEFT JOIN reservations res ON r.id = res.room_id
              AND res.status NOT IN ('cancelled')
            GROUP BY r.type
            ORDER BY reservations DESC
        ");
        $roomPopularity = $stmt->fetchAll();

        // Cafe item popularity — combine order_items (POS) + bill_items (restaurant billing)
        $stmt = $db->query("
            SELECT name, SUM(total_ordered) AS total_ordered
            FROM (
                SELECT mi.name, SUM(oi.quantity) AS total_ordered
                FROM order_items oi
                JOIN menu_items mi ON oi.menu_item_id = mi.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status = 'completed'
                GROUP BY mi.id, mi.name
                UNION ALL
                SELECT bi.description AS name, SUM(bi.quantity) AS total_ordered
                FROM bill_items bi
                JOIN bills b ON bi.bill_id = b.id
                WHERE b.bill_type IN ('restaurant', 'room_service')
                GROUP BY bi.description
            ) combined
            GROUP BY name
            ORDER BY total_ordered DESC
            LIMIT 6
        ");
        $cafePopularity = $stmt->fetchAll();

        // Event monthly frequency (last 6 months) — from events table
        $stmt = $db->query("
            SELECT DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count
            FROM events
            WHERE status IN ('pending','confirmed','completed')
              AND deleted_at IS NULL
              AND event_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY month
            ORDER BY month ASC
        ");
        $eventFrequency = $stmt->fetchAll();

        // Event type popularity — breakdown by event_type
        $stmt = $db->query("
            SELECT event_type, COUNT(*) as count
            FROM events
            WHERE status IN ('pending','confirmed','completed')
              AND deleted_at IS NULL
            GROUP BY event_type
            ORDER BY count DESC
            LIMIT 8
        ");
        $eventTypePopularity = $stmt->fetchAll();

        // --- Predictive Analysis (all 4 revenue sources, last 6 months) ------
        $stmt = $db->query("
            SELECT ym AS month, SUM(amt) AS total FROM (
                SELECT DATE_FORMAT(b.created_at,'%Y-%m') AS ym, b.total_amount AS amt
                FROM bills b
                INNER JOIN reservations r ON r.id = b.reservation_id
                WHERE b.bill_type = 'room' AND r.status = 'checked_out'
                  AND b.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                UNION ALL
                SELECT DATE_FORMAT(created_at,'%Y-%m'), total_amount
                FROM bills WHERE bill_type IN ('restaurant','room_service')
                  AND payment_status IN ('paid','partial')
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                UNION ALL
                SELECT DATE_FORMAT(event_date,'%Y-%m'), total_amount
                FROM events WHERE status IN ('confirmed','completed') AND deleted_at IS NULL
                  AND event_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                UNION ALL
                SELECT DATE_FORMAT(created_at,'%Y-%m'), total_amount
                FROM orders WHERE order_type='pos' AND status='completed'
                  AND (charge_to_room=0 OR charge_to_room IS NULL)
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ) combined
            GROUP BY ym ORDER BY ym ASC
        ");
        $monthlyTotals = $stmt->fetchAll();

        // Linear regression for prediction
        $n = count($monthlyTotals);
        $prediction = ['next_month' => 0, 'trend' => 'stable', 'projected_room' => 0, 'projected_cafe' => 0, 'projected_event' => 0, 'projected_pos' => 0];

        if ($n >= 2) {
            $xSum = 0; $ySum = 0; $xySum = 0; $x2Sum = 0;
            foreach ($monthlyTotals as $i => $row) {
                $x = $i + 1;
                $y = (float)$row['total'];
                $xSum += $x; $ySum += $y;
                $xySum += $x * $y; $x2Sum += $x * $x;
            }
            $slope = ($n * $xySum - $xSum * $ySum) / ($n * $x2Sum - $xSum * $xSum);
            $intercept = ($ySum - $slope * $xSum) / $n;
            $nextX = $n + 1;
            $predicted = max(0, round($slope * $nextX + $intercept, 2));
            $trend = $slope > 50 ? 'up' : ($slope < -50 ? 'down' : 'stable');

            // Ratio from all 4 sources in last 3 months
            $stmt2 = $db->query("
                SELECT src, SUM(amt) as total FROM (
                    SELECT 'room' AS src, b.total_amount AS amt
                    FROM bills b
                    INNER JOIN reservations r ON r.id = b.reservation_id
                    WHERE b.bill_type='room' AND r.status='checked_out'
                      AND b.created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    UNION ALL
                    SELECT 'cafe', total_amount FROM bills
                    WHERE bill_type IN ('restaurant','room_service')
                      AND payment_status IN ('paid','partial')
                      AND created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    UNION ALL
                    SELECT 'event', total_amount FROM events
                    WHERE status IN ('confirmed','completed') AND deleted_at IS NULL
                      AND event_date >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                    UNION ALL
                    SELECT 'pos', total_amount FROM orders
                    WHERE order_type='pos' AND status='completed'
                      AND (charge_to_room=0 OR charge_to_room IS NULL)
                      AND created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
                ) r GROUP BY src
            ");
            $ratioRows = $stmt2->fetchAll(\PDO::FETCH_KEY_PAIR);
            $total3 = array_sum($ratioRows) ?: 1;

            $prediction = [
                'next_month'      => $predicted,
                'trend'           => $trend,
                'slope'           => round($slope, 2),
                'projected_room'  => round($predicted * (($ratioRows['room']  ?? 0) / $total3), 2),
                'projected_cafe'  => round($predicted * (($ratioRows['cafe']  ?? 0) / $total3), 2),
                'projected_event' => round($predicted * (($ratioRows['event'] ?? 0) / $total3), 2),
                'projected_pos'   => round($predicted * (($ratioRows['pos']   ?? 0) / $total3), 2),
            ];
        }

        return response([
            'summary' => [
                'upcoming_events'     => $upcomingEvents,
                'total_staff'         => $totalStaff,
                'available_rooms'     => $availableRooms,
                'total_room_revenue'  => $roomRevenue,
                'total_cafe_revenue'  => $cafeRevenue,
                'total_event_revenue' => $eventRevenue,
                'total_pos_revenue'   => $posRevenue,
                'grand_total_revenue' => $grandTotal,
            ],
            'revenue_trends'  => $revenueTrends,
            'daily_trends'    => $dailyTrends,
            'weekly_trends'   => $weeklyTrends,
            'popularity'      => [
                'rooms'       => $roomPopularity,
                'cafe'        => $cafePopularity,
                'events'      => $eventFrequency,
                'event_types' => $eventTypePopularity,
            ],
            'prediction' => $prediction,
        ], 200);
    }

    // ── Audit Logs (sales view: POS orders + reservations) ──────────────────

    public function getSalesAuditLogs() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db       = \Database::connect();
        $search   = trim($_GET['search']   ?? '');
        $dateFrom = trim($_GET['date_from'] ?? '');
        $dateTo   = trim($_GET['date_to']   ?? '');
        $employee = trim($_GET['employee']  ?? '');

        $like    = '%' . $search . '%';
        $empLike = '%' . $employee . '%';

        // ── POS / Café orders (all completed POS, restaurant, room_service) ────
        $params1 = [':s1' => $like, ':s2' => $like, ':s3' => $like];
        $empCond1 = '';
        $dfCond1  = '';
        $dtCond1  = '';
        if ($employee !== '') { $empCond1 = ' AND u.name LIKE :emp'; $params1[':emp'] = $empLike; }
        if ($dateFrom  !== '') { $dfCond1  = ' AND DATE(o.created_at) >= :df'; $params1[':df'] = $dateFrom; }
        if ($dateTo    !== '') { $dtCond1  = ' AND DATE(o.created_at) <= :dt'; $params1[':dt'] = $dateTo; }

        $stmt1 = $db->prepare("
            SELECT
                o.id,
                COALESCE(o.invoice_id, CONCAT('ORD-', o.id)) AS transaction_id,
                o.created_at,
                COALESCE(o.customer_name, 'Guest')            AS name,
                (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS items,
                o.total_amount                                AS sales,
                COALESCE(o.payment_method, 'cash')           AS payment_method,
                COALESCE(u.name, 'Unknown')                  AS employee,
                'POS' AS source
            FROM orders o
            LEFT JOIN users u ON u.id = o.cashier_id
            WHERE o.status = 'completed'
              AND (o.customer_name LIKE :s1 OR u.name LIKE :s2 OR o.invoice_id LIKE :s3)
              $empCond1 $dfCond1 $dtCond1
            ORDER BY o.created_at DESC
            LIMIT 500
        ");
        $stmt1->execute($params1);
        $posRows = $stmt1->fetchAll();

        // ── Room bills (paid) ────────────────────────────────────────────────
        $params2 = [':s1' => $like, ':s2' => $like];
        $empCond2 = '';
        $dfCond2  = '';
        $dtCond2  = '';
        if ($employee !== '') { $empCond2 = ' AND up.name LIKE :emp'; $params2[':emp'] = $empLike; }
        if ($dateFrom  !== '') { $dfCond2  = ' AND DATE(COALESCE(b.payment_date, b.created_at)) >= :df'; $params2[':df'] = $dateFrom; }
        if ($dateTo    !== '') { $dtCond2  = ' AND DATE(COALESCE(b.payment_date, b.created_at)) <= :dt'; $params2[':dt'] = $dateTo; }

        $stmt2 = $db->prepare("
            SELECT
                b.id,
                b.bill_number                                        AS transaction_id,
                COALESCE(b.payment_date, b.created_at)              AS created_at,
                COALESCE(ug.name, 'Guest')                          AS name,
                1                                                    AS items,
                b.total_amount                                       AS sales,
                COALESCE(b.payment_method, 'N/A')                   AS payment_method,
                COALESCE(up.name, 'N/A')                            AS employee,
                'Room' AS source
            FROM bills b
            LEFT JOIN users ug ON ug.id = b.guest_id
            LEFT JOIN users up ON up.id = b.processed_by
            WHERE b.bill_type = 'room'
              AND b.payment_status = 'paid'
              AND (ug.name LIKE :s1 OR b.bill_number LIKE :s2)
              $empCond2 $dfCond2 $dtCond2
            ORDER BY b.created_at DESC
            LIMIT 500
        ");
        $stmt2->execute($params2);
        $roomRows = $stmt2->fetchAll();

        // ── Event bills (paid) ───────────────────────────────────────────────
        $params3 = [':s1' => $like, ':s2' => $like];
        $empCond3 = '';
        $dfCond3  = '';
        $dtCond3  = '';
        if ($employee !== '') { $empCond3 = ' AND up.name LIKE :emp'; $params3[':emp'] = $empLike; }
        if ($dateFrom  !== '') { $dfCond3  = ' AND DATE(COALESCE(b.payment_date, b.created_at)) >= :df'; $params3[':df'] = $dateFrom; }
        if ($dateTo    !== '') { $dtCond3  = ' AND DATE(COALESCE(b.payment_date, b.created_at)) <= :dt'; $params3[':dt'] = $dateTo; }

        $stmt3 = $db->prepare("
            SELECT
                b.id,
                b.bill_number                                        AS transaction_id,
                COALESCE(b.payment_date, b.created_at)              AS created_at,
                COALESCE(ug.name, 'Guest')                          AS name,
                1                                                    AS items,
                b.total_amount                                       AS sales,
                COALESCE(b.payment_method, 'N/A')                   AS payment_method,
                COALESCE(up.name, 'N/A')                            AS employee,
                'Event' AS source
            FROM bills b
            LEFT JOIN users ug ON ug.id = b.guest_id
            LEFT JOIN users up ON up.id = b.processed_by
            WHERE b.bill_type = 'event'
              AND b.payment_status = 'paid'
              AND (ug.name LIKE :s1 OR b.bill_number LIKE :s2)
              $empCond3 $dfCond3 $dtCond3
            ORDER BY b.created_at DESC
            LIMIT 500
        ");
        $stmt3->execute($params3);
        $eventRows = $stmt3->fetchAll();

        $all = array_merge($posRows, $roomRows, $eventRows);
        usort($all, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return response($all, 200);
    }

    // ── System Logs (audit_logs with user name) ──────────────────────────────

    public function getSystemLogs() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['it']);

        $db = \Database::connect();

        $stmt = $db->query("
            SELECT al.created_at AS time, COALESCE(u.name, 'System') AS user, al.action, al.module, al.details
            FROM audit_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.created_at DESC
            LIMIT 50
        ");
        $logs = $stmt->fetchAll();

        // Active users last 24h
        $stmt2 = $db->query("
            SELECT COUNT(DISTINCT user_id) as cnt FROM audit_logs
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ");
        $activeUsers = (int)$stmt2->fetch()['cnt'];

        return response(['logs' => $logs, 'active_users' => $activeUsers], 200);
    }

    // ── IT: Staff Hourly Rates ───────────────────────────────────────────────

    public function getStaffHourlyRates() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['it', 'admin']);

        $db = \Database::connect();
        $stmt = $db->query("
            SELECT
                u.id,
                u.name,
                u.email,
                u.role,
                COALESCE(s.salary, 0) AS hourly_rate,
                u.active
            FROM users u
            LEFT JOIN staff s ON s.user_id = u.id
            WHERE u.active = 1
              AND u.role NOT IN ('guest')
            ORDER BY u.name ASC
        ");
        $staff = $stmt->fetchAll();
        return response($staff, 200);
    }

    public function updateStaffHourlyRate($userId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['it', 'admin']);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['hourly_rate'])) {
            return error('hourly_rate is required', 400);
        }

        $rate = (float) $data['hourly_rate'];
        if ($rate < 0) {
            return error('hourly_rate must be non-negative', 400);
        }

        $db = \Database::connect();

        // Upsert into staff table (salary column stores hourly rate)
        $stmt = $db->prepare("SELECT id FROM staff WHERE user_id = :uid");
        $stmt->execute([':uid' => $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            $db->prepare("UPDATE staff SET salary = :rate WHERE user_id = :uid")
               ->execute([':rate' => $rate, ':uid' => $userId]);
        } else {
            $db->prepare("INSERT INTO staff (user_id, salary) VALUES (:uid, :rate)")
               ->execute([':uid' => $userId, ':rate' => $rate]);
        }

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'update', 'staff', "Hourly rate updated for user: $userId to $rate");

        return response([], 200, 'Hourly rate updated');
    }

    // ── IT: Forgot Password Requests ────────────────────────────────────────

    public function getForgotPasswordRequests() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['it', 'admin']);

        $db = \Database::connect();
        $stmt = $db->query("
            SELECT
                fpr.id,
                fpr.name,
                fpr.email,
                fpr.message,
                fpr.status,
                fpr.created_at,
                fpr.resolved_at,
                COALESCE(u.name, NULL) AS resolved_by_name
            FROM forgot_password_requests fpr
            LEFT JOIN users u ON u.id = fpr.resolved_by
            ORDER BY fpr.created_at DESC
        ");
        $requests = $stmt->fetchAll();
        return response($requests, 200);
    }

    public function submitForgotPasswordRequest() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return error('name, email, and message are required', 400);
        }

        // Sanitize inputs
        $name    = htmlspecialchars(strip_tags(trim($data['name'])),    ENT_QUOTES, 'UTF-8');
        $email   = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
        $message = htmlspecialchars(strip_tags(trim($data['message'])), ENT_QUOTES, 'UTF-8');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return error('Invalid email address', 400);
        }

        $db = \Database::connect();
        $stmt = $db->prepare("
            INSERT INTO forgot_password_requests (name, email, message, status, created_at)
            VALUES (:name, :email, :message, 'pending', NOW())
        ");
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':message' => $message,
        ]);

        return response(['id' => $db->lastInsertId()], 201, 'Request submitted');
    }

    public function resolveForgotPasswordRequest($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['it', 'admin']);

        $data = json_decode(file_get_contents('php://input'), true);
        $status = $data['status'] ?? 'resolved';

        if (!in_array($status, ['resolved', 'dismissed'], true)) {
            return error('status must be resolved or dismissed', 400);
        }

        $db = \Database::connect();
        $stmt = $db->prepare("
            UPDATE forgot_password_requests
               SET status = :status, resolved_by = :by, resolved_at = NOW()
             WHERE id = :id
        ");
        $stmt->execute([':status' => $status, ':by' => $user['user_id'], ':id' => $id]);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'update', 'forgot_password_requests', "Request $id marked $status");

        return response([], 200, 'Request updated');
    }

    // ── Holidays ─────────────────────────────────────────────────────────────

    public function getHolidays() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db = \Database::connect();
        $rows = $db->query("SELECT * FROM holidays ORDER BY date ASC")->fetchAll();
        return response($rows, 200);
    }

    public function addHoliday() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['date'])) return error('date is required', 400);

        $db = \Database::connect();
        $stmt = $db->prepare("INSERT INTO holidays (date, description) VALUES (:date, :desc)");
        $stmt->execute([':date' => $data['date'], ':desc' => $data['description'] ?? null]);

        return response(['id' => $db->lastInsertId()], 201, 'Holiday added');
    }

    public function deleteHoliday($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db = \Database::connect();
        $db->prepare("DELETE FROM holidays WHERE id = :id")->execute([':id' => $id]);
        return response([], 200, 'Holiday deleted');
    }

    // ── Discounts ─────────────────────────────────────────────────────────────

    public function getDiscounts() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db = \Database::connect();
        $rows = $db->query("SELECT * FROM discounts ORDER BY created_at ASC")->fetchAll();
        return response($rows, 200);
    }

    public function addDiscount() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['name'])) return error('name is required', 400);

        $db = \Database::connect();
        $stmt = $db->prepare("INSERT INTO discounts (name, type, value) VALUES (:name, :type, :value)");
        $stmt->execute([':name' => $data['name'], ':type' => $data['type'] ?? 'percentage', ':value' => $data['value'] ?? 0]);

        return response(['id' => $db->lastInsertId()], 201, 'Discount added');
    }

    public function deleteDiscount($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db = \Database::connect();
        $db->prepare("DELETE FROM discounts WHERE id = :id")->execute([':id' => $id]);
        return response([], 200, 'Discount deleted');
    }

    public function setDefaultDiscount($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $db = \Database::connect();
        $db->exec("UPDATE discounts SET is_default = 0");
        $db->prepare("UPDATE discounts SET is_default = 1 WHERE id = :id")->execute([':id' => $id]);
        return response([], 200, 'Default discount set');
    }
}
