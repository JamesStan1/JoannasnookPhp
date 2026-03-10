<?php

namespace App\Controllers;

use App\Models\Room;

class FrontDeskController {

    private function db() {
        return (new Room())->getDb();
    }

    // GET /api/front-desk/dashboard
    public function getDashboard() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'front_desk']);

        $db = $this->db();

        // 1. Upcoming events (today and future, not cancelled/completed)
        $eventsStmt = $db->prepare("
            SELECT id,
                   COALESCE(event_name, '') AS event_name,
                   event_type, client_name, client_phone, client_email,
                   event_date, event_time, number_of_guests, venue, status,
                   CONCAT('EVT-', LPAD(id, 5, '0')) AS reference_number
            FROM events
            WHERE event_date >= CURDATE()
              AND status IN ('pending', 'confirmed')
              AND deleted_at IS NULL
            ORDER BY event_date ASC, event_time ASC
            LIMIT 30
        ");
        $eventsStmt->execute();
        $upcomingEvents = $eventsStmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Ready food orders — all chef_order items with status = 'ready'
        $readyStmt = $db->prepare("
            SELECT co.id, co.invoice_id, co.item_name, co.quantity,
                   co.notes, co.status, co.created_at,
                   o.customer_name, o.room_number
            FROM chef_orders co
            LEFT JOIN orders o ON o.id = co.pos_order_id
            WHERE co.status = 'ready'
            ORDER BY co.invoice_id ASC, co.created_at ASC
        ");
        $readyStmt->execute();
        $readyOrders = $readyStmt->fetchAll(\PDO::FETCH_ASSOC);

        // 3. All rooms with the most relevant active reservation joined in
        $roomsStmt = $db->prepare("
            SELECT r.*,
                   res.id            AS res_id,
                   res.check_in_date,
                   res.check_out_date,
                   res.status        AS res_status,
                   res.number_of_guests AS res_guests,
                   res.down_payment  AS res_down_payment,
                   res.special_requests,
                   CONCAT('RES-', LPAD(res.id, 5, '0')) AS res_reference,
                   u.name            AS guest_name,
                   u.email           AS guest_email
            FROM rooms r
            LEFT JOIN reservations res ON res.id = (
                SELECT r2.id
                FROM reservations r2
                WHERE r2.room_id = r.id
                  AND r2.status IN ('checked_in', 'approved')
                  AND r2.check_out_date >= CURDATE()
                ORDER BY
                    CASE r2.status WHEN 'checked_in' THEN 0 ELSE 1 END,
                    r2.check_in_date ASC
                LIMIT 1
            )
            LEFT JOIN users u ON u.id = res.guest_id
            WHERE r.deleted_at IS NULL
            ORDER BY
                CAST(r.room_number AS UNSIGNED) ASC,
                r.room_number ASC
        ");
        $roomsStmt->execute();
        $rooms = $roomsStmt->fetchAll(\PDO::FETCH_ASSOC);

        return response([
            'upcoming_events' => $upcomingEvents,
            'ready_orders'    => $readyOrders,
            'rooms'           => $rooms,
        ], 200);
    }
}
