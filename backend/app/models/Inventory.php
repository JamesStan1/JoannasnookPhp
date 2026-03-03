<?php

namespace App\Models;

class Inventory extends BaseModel {
    protected $table = 'inventory';

    public function getFiltered($category = '', $search = '') {
        $where = ['deleted_at IS NULL'];
        $params = [];

        if ($category) {
            $where[] = 'category = ?';
            $params[] = $category;
        }
        if ($search) {
            $where[] = '(name LIKE ? OR category LIKE ? OR supplier LIKE ?)';
            $s = '%' . $search . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }

        $whereStr = implode(' AND ', $where);
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE $whereStr ORDER BY name ASC");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getArchived() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
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
    }

    public function getLowStockItems($threshold = 10) {
        $query = "SELECT * FROM {$this->table} WHERE current_stock <= ? ORDER BY current_stock ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }

    public function updateStock($itemId, $quantity, $action = 'reduce') {
        $item = $this->findById($itemId);
        if (!$item) return false;

        $newStock = $action === 'reduce' ? $item['current_stock'] - $quantity : $item['current_stock'] + $quantity;
        if ($newStock < 0) return false;

        return $this->update($itemId, [
            'current_stock' => $newStock,
            'last_updated'  => date('Y-m-d H:i:s'),
        ]);
    }
}
