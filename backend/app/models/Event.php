<?php

namespace App\Models;

class Event extends BaseModel {
    protected $table = 'events';

    public function getAll($filters = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $s = '%' . $filters['search'] . '%';
            $where[] = "(e.event_type LIKE ? OR e.client_name LIKE ? OR e.venue LIKE ?)";
            $params[] = $s; $params[] = $s; $params[] = $s;
        }
        if (!empty($filters['status'])) {
            $where[] = "e.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['date'])) {
            $where[] = "e.event_date = ?";
            $params[] = $filters['date'];
        }

        $where[] = 'e.deleted_at IS NULL';
        $whereStr = implode(' AND ', $where);
        $query = "
            SELECT e.*,
                   (e.total_amount - COALESCE(e.down_payment, 0)) AS remaining_balance,
                   CONCAT('EVT-', LPAD(e.id, 5, '0')) AS reference_number
            FROM {$this->table} e
            WHERE $whereStr
            ORDER BY e.event_date ASC, e.event_time ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getStats() {
        $stats = [];

        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE status != 'cancelled' AND deleted_at IS NULL");
        $stats['total_bookings'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'pending' AND deleted_at IS NULL");
        $stats['pending_reservations'] = (int)$stmt->fetchColumn();

        $stmt = $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM {$this->table} WHERE status IN ('confirmed', 'completed') AND deleted_at IS NULL");
        $stats['total_revenue'] = (float)$stmt->fetchColumn();

        // Upcoming event
        $stmt = $this->db->prepare("
            SELECT event_type, event_date, event_time
            FROM {$this->table}
            WHERE event_date >= CURDATE() AND status IN ('pending', 'confirmed')
            ORDER BY event_date ASC, event_time ASC
            LIMIT 1
        ");
        $stmt->execute();
        $stats['upcoming_event'] = $stmt->fetch() ?: null;

        return $stats;
    }

    public function getByDate($date) {
        $stmt = $this->db->prepare("
            SELECT *, (total_amount - COALESCE(down_payment, 0)) AS remaining_balance,
                   CONCAT('EVT-', LPAD(id, 5, '0')) AS reference_number
            FROM {$this->table}
            WHERE event_date = ? AND status != 'cancelled' AND deleted_at IS NULL
            ORDER BY event_time ASC
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    public function getBookedDates() {
        $stmt = $this->db->query("
            SELECT DISTINCT event_date FROM {$this->table}
            WHERE status != 'cancelled' AND event_date >= CURDATE() AND deleted_at IS NULL
        ");
        return array_column($stmt->fetchAll(), 'event_date');
    }
    public function getArchived($search = '') {
        $params = [];
        $where = ['deleted_at IS NOT NULL'];

        if ($search) {
            $s = '%' . $search . '%';
            $where[] = '(event_type LIKE ? OR client_name LIKE ? OR venue LIKE ?)';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }

        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("
            SELECT *,
                   (total_amount - COALESCE(down_payment, 0)) AS remaining_balance,
                   CONCAT('EVT-', LPAD(id, 5, '0')) AS reference_number
            FROM {$this->table}
            WHERE $whereStr
            ORDER BY deleted_at DESC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Check whether a non-cancelled, non-deleted event already occupies the
     * given date + time slot.  Pass $excludeId when editing an existing event
     * so the event being edited does not conflict with itself.
     */
    public function hasConflict($date, $time, $excludeId = null) {
        $sql = "SELECT id FROM {$this->table}
                WHERE event_date = ?
                  AND event_time = ?
                  AND status != 'cancelled'
                  AND deleted_at IS NULL";
        $params = [$date, $time];

        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function softDelete($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = ? WHERE id = ?");
        return $stmt->execute([date('Y-m-d H:i:s'), $id]);
    }

    public function restore($id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }}
