<?php

namespace App\Models;

class EventPackage extends BaseModel {
    protected $table = 'event_packages';

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY price ASC");
        return $stmt->fetchAll();
    }

    public function getActive() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY price ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
