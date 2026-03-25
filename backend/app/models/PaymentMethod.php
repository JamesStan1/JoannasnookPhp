<?php

namespace App\Models;

use PDO;

class PaymentMethod extends BaseModel {
    protected $table = 'payment_methods';

    private function tableExists(): bool {
        try {
            $this->db->query('SELECT 1 FROM payment_methods LIMIT 1');
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getAll() {
        if (!$this->tableExists()) return [];
        $stmt = $this->db->query(
            'SELECT * FROM payment_methods ORDER BY sort_order ASC, id ASC'
        );
        return $stmt->fetchAll();
    }

    public function getActive() {
        if (!$this->tableExists()) return [];
        $stmt = $this->db->prepare(
            'SELECT * FROM payment_methods WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createMethod(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO payment_methods (name, account_name, account_number, instructions, icon, is_active, sort_order)
             VALUES (:name, :account_name, :account_number, :instructions, :icon, :is_active, :sort_order)'
        );
        $stmt->execute([
            ':name'           => trim($data['name'] ?? ''),
            ':account_name'   => trim($data['account_name'] ?? ''),
            ':account_number' => trim($data['account_number'] ?? ''),
            ':instructions'   => trim($data['instructions'] ?? ''),
            ':icon'           => trim($data['icon'] ?? '💳'),
            ':is_active'      => isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1,
            ':sort_order'     => (int)($data['sort_order'] ?? 0),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateMethod(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            'UPDATE payment_methods
             SET name = :name, account_name = :account_name, account_number = :account_number,
                 instructions = :instructions, icon = :icon,
                 is_active = :is_active, sort_order = :sort_order
             WHERE id = :id'
        );
        return $stmt->execute([
            ':id'             => $id,
            ':name'           => trim($data['name'] ?? ''),
            ':account_name'   => trim($data['account_name'] ?? ''),
            ':account_number' => trim($data['account_number'] ?? ''),
            ':instructions'   => trim($data['instructions'] ?? ''),
            ':icon'           => trim($data['icon'] ?? '💳'),
            ':is_active'      => isset($data['is_active']) ? (int)(bool)$data['is_active'] : 1,
            ':sort_order'     => (int)($data['sort_order'] ?? 0),
        ]);
    }

    public function deleteMethod(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM payment_methods WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
