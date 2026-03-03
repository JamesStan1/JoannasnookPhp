<?php

namespace App\Models;

class MenuItem extends BaseModel {
    protected $table = 'menu_items';

    public function getByCategory($category) {
        $query = "SELECT * FROM {$this->table} WHERE category = ? AND active = 1 ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }

    public function getAllActive() {
        $query = "SELECT * FROM {$this->table} WHERE active = 1 ORDER BY category, name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllIncludingInactive() {
        $query = "SELECT * FROM {$this->table} ORDER BY category, name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
