<?php

namespace App\Models;

use PDO;

class BaseModel {
    protected $db;
    protected $table;

    public function __construct() {
        $this->db = \Database::connect();
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array_values($data));

        return $this->db->lastInsertId();
    }

    public function findById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function find($conditions = []) {
        $query = "SELECT * FROM {$this->table}";

        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = ?";
            }
            $query .= " WHERE " . implode(' AND ', $where);

            $stmt = $this->db->prepare($query);
            $stmt->execute(array_values($conditions));
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    public function findOne($conditions = []) {
        $results = $this->find($conditions);
        return !empty($results) ? $results[0] : null;
    }

    public function update($id, $data) {
        $columns = [];
        foreach ($data as $key => $value) {
            $columns[] = "$key = ?";
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $columns) . " WHERE id = ?";
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($values);
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    public function all() {
        $query = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function paginate($page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM {$this->table} LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function count() {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    public function getDb() {
        return $this->db;
    }
}
