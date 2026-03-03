<?php

namespace App\Models;

class Attendance extends BaseModel {
    protected $table = 'attendance';

    public function clockIn($staffId) {
        return $this->create([
            'staff_id' => $staffId,
            'clock_in_time' => date('Y-m-d H:i:s'),
            'date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getDailyAttendance($date) {
        $query = "
            SELECT
                u.id AS user_id, u.name, u.email, u.role, u.active,
                a.id AS attendance_id, a.clock_in_time, a.clock_out_time, a.date,
                CASE WHEN a.id IS NOT NULL THEN 'present' ELSE 'absent' END AS status,
                CASE
                    WHEN a.clock_in_time IS NOT NULL AND a.clock_out_time IS NOT NULL
                    THEN ROUND(TIMESTAMPDIFF(MINUTE, a.clock_in_time, a.clock_out_time) / 60, 2)
                    ELSE NULL
                END AS hours_worked
            FROM users u
            LEFT JOIN attendance a ON u.id = a.staff_id AND a.date = ?
            WHERE u.active = 1
            ORDER BY
                CASE WHEN a.id IS NOT NULL THEN 0 ELSE 1 END,
                u.name ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    public function adminClockIn($staffId, $date, $time = null) {
        $existing = $this->findOne(['staff_id' => $staffId, 'date' => $date]);
        if ($existing) return false;

        return $this->create([
            'staff_id' => $staffId,
            'clock_in_time' => $time ?? date('Y-m-d H:i:s'),
            'date' => $date,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function adminClockOut($attendanceId, $time = null) {
        $query = "UPDATE {$this->table} SET clock_out_time = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$time ?? date('Y-m-d H:i:s'), $attendanceId]);
    }

    public function clockOut($staffId) {
        $query = "UPDATE {$this->table} SET clock_out_time = ? WHERE staff_id = ? AND date = ? AND clock_out_time IS NULL";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([date('Y-m-d H:i:s'), $staffId, date('Y-m-d')]);
    }

    public function getTodayRecord($staffId) {
        return $this->findOne([
            'staff_id' => $staffId,
            'date' => date('Y-m-d'),
        ]);
    }

    public function getAttendanceReport($staffId, $startDate, $endDate) {
        $query = "
            SELECT * FROM {$this->table}
            WHERE staff_id = ? AND date BETWEEN ? AND ?
            ORDER BY date DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$staffId, $startDate, $endDate]);
        return $stmt->fetchAll();
    }
}
