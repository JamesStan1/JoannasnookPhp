<?php

namespace App\Models;

class Reservation extends BaseModel {
    protected $table = 'reservations';

    public function getReservationDetails($reservationId) {
        $query = "
            SELECT r.*, u.name as guest_name, u.email as guest_email,
                   rm.room_number, rm.type as room_type
            FROM {$this->table} r
            JOIN users u ON r.guest_id = u.id
            JOIN rooms rm ON r.room_id = rm.id
            WHERE r.id = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$reservationId]);
        return $stmt->fetch();
    }

    public function getGuestReservations($guestId) {
        $query = "
            SELECT r.*, rm.room_number, rm.type as room_type
            FROM {$this->table} r
            JOIN rooms rm ON r.room_id = rm.id
            WHERE r.guest_id = ?
            ORDER BY r.check_in_date DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$guestId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($reservationId, $status) {
        return $this->update($reservationId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getUpcomingReservations($days = 7) {
        $query = "
            SELECT r.*, u.name as guest_name, rm.room_number
            FROM {$this->table} r
            JOIN users u ON r.guest_id = u.id
            JOIN rooms rm ON r.room_id = rm.id
            WHERE r.check_in_date <= DATE_ADD(NOW(), INTERVAL ? DAY)
            AND r.status = 'approved'
            ORDER BY r.check_in_date ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }

    public function getAllReservations($filters = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $s = '%' . $filters['search'] . '%';
            $where[] = "(u.name LIKE ? OR rm.room_number LIKE ? OR CONCAT('RES-', LPAD(r.id,5,'0')) LIKE ?)";
            $params[] = $s; $params[] = $s; $params[] = $s;
        }
        if (!empty($filters['status'])) {
            $where[] = "r.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['date'])) {
            $where[] = "(r.check_in_date = ? OR r.check_out_date = ?)";
            $params[] = $filters['date']; $params[] = $filters['date'];
        }

        $whereStr = implode(' AND ', $where);
        $query = "
            SELECT r.*,
                   u.name as guest_name, u.email as guest_email,
                   rm.room_number, rm.type as room_type, rm.price as room_price,
                   CONCAT('RES-', LPAD(r.id,5,'0')) as reference_number,
                   GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 1) as nights,
                   (rm.price * GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 1)) as total_amount,
                   ((rm.price * GREATEST(DATEDIFF(r.check_out_date, r.check_in_date), 1)) - COALESCE(r.down_payment, 0)) as remaining_balance,
                   0 as cafe_payment
            FROM {$this->table} r
            JOIN users u ON r.guest_id = u.id
            JOIN rooms rm ON r.room_id = rm.id
            WHERE $whereStr
            ORDER BY r.created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStats() {
        $today = date('Y-m-d');
        $stats = [];

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE check_in_date = ? AND status != 'cancelled'");
        $stmt->execute([$today]);
        $stats['today_reservations'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status != 'cancelled'");
        $stmt->execute();
        $stats['total_reservations'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE check_out_date = ? AND status IN ('checked_in', 'approved')");
        $stmt->execute([$today]);
        $stats['today_checkouts'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM((rm.price * GREATEST(DATEDIFF(r.check_out_date, r.check_in_date),1)) - COALESCE(r.down_payment, 0)), 0)
            FROM {$this->table} r
            JOIN rooms rm ON r.room_id = rm.id
            WHERE r.status IN ('approved', 'checked_in')
        ");
        $stmt->execute();
        $stats['balance_to_collect'] = (float)$stmt->fetchColumn();

        return $stats;
    }

    public function getBookedDates() {
        $query = "
            SELECT r.check_in_date, r.check_out_date, r.status,
                   rm.room_number, u.name as guest_name
            FROM {$this->table} r
            JOIN rooms rm ON r.room_id = rm.id
            JOIN users u ON r.guest_id = u.id
            WHERE r.status IN ('approved', 'checked_in', 'pending')
            AND r.check_out_date >= CURDATE()
            ORDER BY r.check_in_date ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
