<?php

namespace App\Models;

class Staff extends BaseModel {
    protected $table = 'staff';

    public function getStaffByRole($role) {
        $query = "
            SELECT s.*, u.name, u.email, u.role
            FROM {$this->table} s
            JOIN users u ON s.user_id = u.id
            WHERE u.role = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
}
