<?php

namespace App\Models;

class HousekeepingTask extends BaseModel {
    protected $table = 'housekeeping_tasks';

    public function getStaffTasks($staffId) {
        $query = "
            SELECT ht.*, r.room_number, r.type as room_type
            FROM {$this->table} ht
            JOIN rooms r ON ht.room_id = r.id
            WHERE ht.staff_id = ?
            AND ht.status != 'completed'
            ORDER BY ht.priority DESC, ht.created_at ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$staffId]);
        return $stmt->fetchAll();
    }

    public function getPendingTasks() {
        $query = "
            SELECT ht.*, r.room_number, u.name as staff_name
            FROM {$this->table} ht
            JOIN rooms r ON ht.room_id = r.id
            LEFT JOIN users u ON ht.staff_id = u.id
            WHERE ht.status = 'pending'
            ORDER BY ht.priority DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function completeTask($taskId) {
        return $this->update($taskId, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function completeTasksByRoom($roomId) {
        $query = "
            UPDATE {$this->table}
            SET status = 'completed', completed_at = ?, updated_at = ?
            WHERE room_id = ? AND status IN ('pending', 'assigned', 'in_progress')
        ";
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$now, $now, $roomId]);
    }
}
