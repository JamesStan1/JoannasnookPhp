<?php

namespace App\Models;

class User extends BaseModel {
    protected $table = 'users';

    public function findByEmail($email) {
        return $this->findOne(['email' => $email]);
    }

    public function createUser($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $data['qr_token']  = bin2hex(random_bytes(16)); // 32-char unique token
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->create($data);
    }

    public function findByQrToken($token) {
        return $this->findOne(['qr_token' => $token]);
    }

    public function verifyPassword($plainPassword, $hashedPassword) {
        return password_verify($plainPassword, $hashedPassword);
    }

    public function findByRole($role) {
        return $this->find(['role' => $role]);
    }

    public function updateLastLogin($userId) {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }

    public function getArchivedUsers() {
        $query = "
            SELECT *, updated_at AS archived_at
            FROM {$this->table}
            WHERE active = 0
            ORDER BY updated_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
