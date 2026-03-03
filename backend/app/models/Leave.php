<?php

namespace App\Models;

class Leave extends BaseModel {
    protected $table = 'leaves';

    public function getStaffLeaves($staffId) {
        return $this->find(['staff_id' => $staffId]);
    }

    public function getPendingLeaves() {
        return $this->find(['status' => 'pending']);
    }

    public function approveLeave($leaveId) {
        return $this->update($leaveId, [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function rejectLeave($leaveId, $reason = null) {
        return $this->update($leaveId, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);
    }

    public function getAllLeavesWithStaff($status = null) {
        $query = "
            SELECT l.*, u.name AS staff_name, u.email AS staff_email, u.role AS staff_role
            FROM {$this->table} l
            JOIN users u ON l.staff_id = u.id
        ";
        $params = [];
        if ($status) {
            $query .= " WHERE l.status = ?";
            $params[] = $status;
        }
        $query .= " ORDER BY l.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getLeaveBalance($staffId, $leaveType) {
        $query = "
            SELECT SUM(number_of_days) as used_days FROM {$this->table}
            WHERE staff_id = ? AND leave_type = ? AND status = 'approved'
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$staffId, $leaveType]);
        $result = $stmt->fetch();
        return $result['used_days'] ?? 0;
    }
}
