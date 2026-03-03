<?php

namespace App\Models;

class Payroll extends BaseModel {
    protected $table = 'payroll';

    public function getStaffPayroll($staffId) {
        return $this->find(['staff_id' => $staffId]);
    }

    public function calculatePayroll($staffId, $month) {
        $query = "
            SELECT COUNT(*) as working_days FROM attendance
            WHERE staff_id = ? AND MONTH(date) = ? AND YEAR(date) = YEAR(CURDATE())
            AND clock_out_time IS NOT NULL
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$staffId, $month]);
        $attendance = $stmt->fetch();

        $query = "
            SELECT SUM(number_of_days) as leave_days FROM leaves
            WHERE staff_id = ? AND MONTH(start_date) = ? AND status = 'approved'
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$staffId, $month]);
        $leaves = $stmt->fetch();

        return [
            'working_days' => $attendance['working_days'] ?? 0,
            'leave_days'   => $leaves['leave_days'] ?? 0,
        ];
    }

    /**
     * Get all active staff with hours worked + computed pay for a week.
     */
    public function getWeeklyPayroll($weekStart, $weekEnd) {
        $query = "
            SELECT
                u.id          AS user_id,
                u.name,
                u.email,
                u.role,
                COALESCE(s.department, '') AS department,
                COALESCE(s.salary, 0)      AS hourly_rate,
                COALESCE(
                    SUM(
                        CASE WHEN a.clock_out_time IS NOT NULL
                             THEN TIMESTAMPDIFF(MINUTE, a.clock_in_time, a.clock_out_time) / 60.0
                             ELSE 0 END
                    ), 0
                ) AS hours_worked,
                COUNT(CASE WHEN a.id IS NOT NULL AND a.clock_in_time IS NOT NULL THEN 1 END) AS days_present,
                COALESCE(
                    SUM(
                        CASE WHEN a.clock_in_time IS NOT NULL
                             THEN GREATEST(0, TIMESTAMPDIFF(MINUTE,
                                      CONCAT(DATE(a.date), ' 08:00:00'),
                                      a.clock_in_time))
                             ELSE 0 END
                    ), 0
                ) AS total_late_minutes,
                COALESCE((
                    SELECT SUM(GREATEST(0, DATEDIFF(LEAST(lp.end_date, ?), GREATEST(lp.start_date, ?)) + 1))
                    FROM leaves lp
                    WHERE lp.staff_id = u.id
                      AND lp.status   = 'approved'
                      AND lp.leave_type != 'unpaid'
                      AND lp.start_date <= ? AND lp.end_date >= ?
                ), 0) AS paid_leave_days,
                COALESCE((
                    SELECT SUM(GREATEST(0, DATEDIFF(LEAST(lu.end_date, ?), GREATEST(lu.start_date, ?)) + 1))
                    FROM leaves lu
                    WHERE lu.staff_id = u.id
                      AND lu.status   = 'approved'
                      AND lu.leave_type = 'unpaid'
                      AND lu.start_date <= ? AND lu.end_date >= ?
                ), 0) AS unpaid_leave_days
            FROM users u
            LEFT JOIN staff       s ON s.user_id  = u.id
            LEFT JOIN attendance  a ON a.staff_id = u.id
                                   AND a.date BETWEEN ? AND ?
            WHERE u.active = 1 AND u.role != 'guest'
            GROUP BY u.id, u.name, u.email, u.role, s.salary, s.department
            ORDER BY u.name ASC
        ";
        $stmt = $this->db->prepare($query);
        // Params: paid_leave subquery (4), unpaid_leave subquery (4), attendance JOIN (2)
        $stmt->execute([
            $weekEnd, $weekStart, $weekEnd, $weekStart,
            $weekEnd, $weekStart, $weekEnd, $weekStart,
            $weekStart, $weekEnd,
        ]);
        $rows = $stmt->fetchAll();

        return array_map(function ($row) {
            $row['hours_worked']       = round((float)$row['hours_worked'], 2);
            $row['hourly_rate']        = (float)$row['hourly_rate'];
            $row['days_present']       = (int)$row['days_present'];
            $row['total_late_minutes'] = (int)$row['total_late_minutes'];
            $row['late_deduction']     = round(($row['total_late_minutes'] / 60.0) * $row['hourly_rate'], 2);
            $row['paid_leave_days']    = (int)$row['paid_leave_days'];
            $row['unpaid_leave_days']  = (int)$row['unpaid_leave_days'];
            // Paid leave: compensate 8 hrs per approved leave day
            $row['leave_pay']          = round($row['paid_leave_days'] * 8 * $row['hourly_rate'], 2);
            $row['total_pay']          = round($row['hours_worked'] * $row['hourly_rate'] + $row['leave_pay'], 2);
            return $row;
        }, $rows);
    }

    /**
     * Upsert hourly rate (stored in staff.salary) for a user.
     */
    public function setHourlyRate($userId, $rate) {
        $query = "
            INSERT INTO staff (user_id, salary, created_at)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE salary = ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId, $rate, $rate]);
    }

    /**
     * Get a single staff member's hourly rate.
     */
    public function getHourlyRate($userId) {
        $query = "SELECT COALESCE(salary, 0) AS hourly_rate FROM staff WHERE user_id = ?";
        $stmt  = $this->db->prepare($query);
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ? (float)$row['hourly_rate'] : 0;
    }

    /**
     * Get full payroll history, optionally filtered by month (YYYY-MM).
     */
    public function getAllPayrollHistory($month = null) {
        $where  = $month ? "WHERE p.month = ?" : "";
        $params = $month ? [$month] : [];
        $query  = "
            SELECT p.*, u.name AS staff_name, u.role
            FROM payroll p
            JOIN users u ON u.id = p.staff_id
            $where
            ORDER BY p.created_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
