<?php

namespace App\Models;

class InventoryLog extends BaseModel {
    protected $table = 'inventory_logs';

    public function logAction($inventoryId, $action, $quantity, $userId, $notes = null) {
        return $this->create([
            'inventory_id' => $inventoryId,
            'action' => $action,
            'quantity' => $quantity,
            'user_id' => $userId,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getInventoryHistory($inventoryId) {
        $stmt = $this->db->prepare(
            "SELECT il.*, COALESCE(u.name, CONCAT('User #', il.user_id)) AS performed_by
             FROM {$this->table} il
             LEFT JOIN users u ON il.user_id = u.id
             WHERE il.inventory_id = ?
             ORDER BY il.created_at DESC"
        );
        $stmt->execute([$inventoryId]);
        return $stmt->fetchAll();
    }

    public function getAllWithDetails($limit = 100) {
        $stmt = $this->db->prepare(
            "SELECT il.*,
                    i.name        AS item_name,
                    COALESCE(u.name, CONCAT('User #', il.user_id)) AS performed_by
             FROM {$this->table} il
             LEFT JOIN inventory i ON il.inventory_id = i.id
             LEFT JOIN users u     ON il.user_id = u.id
             ORDER BY il.created_at DESC
             LIMIT ?"
        );
        $stmt->bindValue(1, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
