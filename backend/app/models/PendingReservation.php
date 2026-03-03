<?php

namespace App\Models;

class PendingReservation extends BaseModel {
    protected $table = 'pending_reservations';

    public function getAllWithDetails() {
        $stmt = $this->db->query("
            SELECT pr.*,
                   r.room_number, r.type AS room_type, r.price AS room_price,
                   ep.package_name AS event_package_name
            FROM pending_reservations pr
            LEFT JOIN rooms r ON pr.room_id = r.id
            LEFT JOIN event_packages ep ON pr.event_package_id = ep.id
            ORDER BY pr.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function getFiltered($status = null, $type = null) {
        $where = [];
        $params = [];
        if ($status) { $where[] = 'pr.status = ?'; $params[] = $status; }
        if ($type)   { $where[] = 'pr.reservation_type = ?'; $params[] = $type; }

        $whereClause = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT pr.*,
                   r.room_number, r.type AS room_type, r.price AS room_price,
                   ep.package_name AS event_package_name
            FROM pending_reservations pr
            LEFT JOIN rooms r ON pr.room_id = r.id
            LEFT JOIN event_packages ep ON pr.event_package_id = ep.id
            $whereClause
            ORDER BY pr.created_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
