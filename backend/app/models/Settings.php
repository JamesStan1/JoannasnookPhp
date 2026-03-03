<?php

namespace App\Models;

class Settings extends BaseModel {
    protected $table = 'settings';

    /**
     * Find a single setting by its key.
     * Uses a raw query because 'key' is a reserved word in MariaDB/MySQL
     * and the generic BaseModel::find() does not backtick column names.
     */
    private function findByKey($key) {
        $stmt = $this->db->prepare("SELECT * FROM `settings` WHERE `key` = ? LIMIT 1");
        $stmt->execute([$key]);
        return $stmt->fetch() ?: null;
    }

    public function get($key, $default = null) {
        $setting = $this->findByKey($key);
        return $setting ? $setting['value'] : $default;
    }

    public function set($key, $value) {
        $existing = $this->findByKey($key);

        if ($existing) {
            $stmt = $this->db->prepare(
                "UPDATE `settings` SET `value` = ?, `updated_at` = ? WHERE `id` = ?"
            );
            return $stmt->execute([$value, date('Y-m-d H:i:s'), $existing['id']]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?)"
            );
            return $stmt->execute([$key, $value, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
        }
    }
}
