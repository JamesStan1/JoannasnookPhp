<?php

namespace App\Models;

class AuditLog extends BaseModel {
    protected $table = 'audit_logs';

    public function log($userId, $action, $module, $details = null) {
        return $this->create([
            'user_id' => $userId,
            'action' => $action,
            'module' => $module,
            'details' => $details,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getActivityLog($filters = []) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";

        if (!empty($filters['user_id'])) {
            $query .= " AND user_id = {$filters['user_id']}";
        }

        if (!empty($filters['module'])) {
            $query .= " AND module = '{$filters['module']}'";
        }

        if (!empty($filters['start_date'])) {
            $query .= " AND DATE(created_at) >= '{$filters['start_date']}'";
        }

        if (!empty($filters['end_date'])) {
            $query .= " AND DATE(created_at) <= '{$filters['end_date']}'";
        }

        $query .= " ORDER BY created_at DESC LIMIT 1000";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
