<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\Payroll;

class StaffController {

    /**
     * QR Scan Clock-In / Clock-Out (Admin / Manager)
     * Accepts { "token": "<qr_token>" } and automatically clocks the matched
     * user in (if not yet recorded today) or out (if already clocked in).
     */
    public function qrScanClock() {
        $actor = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($actor, ['admin']);

        $data  = json_decode(file_get_contents('php://input'), true);
        $token = trim($data['token'] ?? '');

        if (!$token) return error('QR token is required', 400);

        $userModel = new \App\Models\User();
        $target    = $userModel->findByQrToken($token);

        if (!$target) return error('Invalid QR code', 404);

        $targetId        = $target['id'];
        $attendanceModel = new Attendance();
        $existingRecord  = $attendanceModel->getTodayRecord($targetId);

        if (!$existingRecord) {
            $recordId = $attendanceModel->clockIn($targetId);

            $auditLog = new \App\Models\AuditLog();
            $auditLog->log($actor['user_id'], 'create', 'attendance',
                "QR clock-in: user {$targetId} ({$target['name']})");

            return response([
                'action'    => 'clock_in',
                'record_id' => $recordId,
                'user_name' => $target['name'],
                'clock_in'  => date('H:i:s'),
            ], 200, "{$target['name']} clocked in");
        }

        if ($existingRecord['clock_out_time']) {
            return error("{$target['name']} has already clocked out today", 400);
        }

        $attendanceModel->clockOut($targetId);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($actor['user_id'], 'update', 'attendance',
            "QR clock-out: user {$targetId} ({$target['name']})");

        return response([
            'action'    => 'clock_out',
            'user_name' => $target['name'],
            'clock_out' => date('H:i:s'),
        ], 200, "{$target['name']} clocked out");
    }

    /**
     * Staff Self QR Clock-In / Clock-Out
     * The authenticated staff scans their OWN QR code to clock in or out.
     * Verifies the token belongs to the authenticated user before recording.
     */
    public function staffQrClock() {
        $user  = \App\Middleware\AuthMiddleware::handle();
        $data  = json_decode(file_get_contents('php://input'), true);
        $token = trim($data['token'] ?? '');
        $action = trim($data['action'] ?? ''); // 'clock_in' or 'clock_out'

        if (!$token)  return error('QR token is required', 400);
        if (!in_array($action, ['clock_in', 'clock_out'])) return error('Invalid action', 400);

        // Verify the scanned token belongs to the authenticated user
        $userModel = new \App\Models\User();
        $target    = $userModel->findByQrToken($token);

        if (!$target) return error('Invalid QR code', 404);

        if ((int)$target['id'] !== (int)$user['user_id']) {
            return error('This QR code does not belong to your account', 403);
        }

        $attendanceModel = new Attendance();

        if ($action === 'clock_in') {
            $existing = $attendanceModel->getTodayRecord($user['user_id']);
            if ($existing) return error('You have already clocked in today', 400);

            $recordId = $attendanceModel->clockIn($user['user_id']);

            $auditLog = new \App\Models\AuditLog();
            $auditLog->log($user['user_id'], 'create', 'attendance', 'QR self clock-in');

            return response([
                'action'    => 'clock_in',
                'record_id' => $recordId,
                'clock_in'  => date('H:i:s'),
            ], 200, 'Clocked in successfully');
        }

        // clock_out
        $existing = $attendanceModel->getTodayRecord($user['user_id']);
        if (!$existing) return error('No clock-in record found for today', 400);
        if ($existing['clock_out_time']) return error('You have already clocked out today', 400);

        $attendanceModel->clockOut($user['user_id']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'attendance', 'QR self clock-out');

        return response([
            'action'    => 'clock_out',
            'clock_out' => date('H:i:s'),
        ], 200, 'Clocked out successfully');
    }

    public function clockIn() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $attendanceModel = new Attendance();
        $existingRecord = $attendanceModel->getTodayRecord($user['user_id']);

        if ($existingRecord) {
            return error('Already clocked in today', 400);
        }

        $recordId = $attendanceModel->clockIn($user['user_id']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'attendance', 'Clock in');

        return response(['record_id' => $recordId], 200, 'Clocked in successfully');
    }

    public function clockOut() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $attendanceModel = new Attendance();
        $attendanceModel->clockOut($user['user_id']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'attendance', 'Clock out');

        return response([], 200, 'Clocked out successfully');
    }

    public function getAttendanceRecord() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $attendanceModel = new Attendance();
        $record = $attendanceModel->getTodayRecord($user['user_id']);

        return response($record, 200);
    }

    public function getAttendanceReport() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        if (empty($_GET['staff_id']) || empty($_GET['start_date']) || empty($_GET['end_date'])) {
            return error('staff_id, start_date, and end_date required', 400);
        }

        $attendanceModel = new Attendance();
        $report = $attendanceModel->getAttendanceReport(
            $_GET['staff_id'],
            $_GET['start_date'],
            $_GET['end_date']
        );

        return response($report, 200);
    }

    public function requestLeave() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['start_date', 'end_date', 'leave_type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $leaveModel = new Leave();
        $start = new \DateTime($data['start_date']);
        $end = new \DateTime($data['end_date']);
        $days = $end->diff($start)->days + 1;

        $leaveId = $leaveModel->create([
            'staff_id' => $user['user_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'number_of_days' => $days,
            'leave_type' => $data['leave_type'],
            'reason' => $data['reason'] ?? null,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'leaves', "Leave request submitted: $leaveId");

        return response(['leave_id' => $leaveId], 201, 'Leave request submitted');
    }

    public function getMyLeaves() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $leaveModel = new Leave();
        $leaves = $leaveModel->getStaffLeaves($user['user_id']);

        usort($leaves, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

        return response($leaves, 200);
    }

    public function getAllLeavesAdmin() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $status = $_GET['status'] ?? null;

        $leaveModel = new Leave();
        $leaves = $leaveModel->getAllLeavesWithStaff($status);

        return response($leaves, 200);
    }

    public function getPendingLeaves() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $leaveModel = new Leave();
        $leaves = $leaveModel->getPendingLeaves();

        return response($leaves, 200);
    }

    public function approveLeave($leaveId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $leaveModel = new Leave();
        $leaveModel->approveLeave($leaveId);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'leaves', "Leave approved: $leaveId");

        return response([], 200, 'Leave approved');
    }

    public function rejectLeave($leaveId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $leaveModel = new Leave();
        $leaveModel->rejectLeave($leaveId, $data['reason'] ?? null);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'leaves', "Leave rejected: $leaveId");

        return response([], 200, 'Leave rejected');
    }

    public function getWeeklyPayroll() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        // Default to current Monday
        $weekStart = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));
        // Guard: ensure it is actually a Monday-based date (just use as-is)
        $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));

        $payrollModel = new Payroll();
        $staff = $payrollModel->getWeeklyPayroll($weekStart, $weekEnd);

        $totalHours = round(array_sum(array_column($staff, 'hours_worked')), 2);
        $totalPay   = round(array_sum(array_column($staff, 'total_pay')), 2);

        return response([
            'week_start'  => $weekStart,
            'week_end'    => $weekEnd,
            'staff'       => $staff,
            'total_hours' => $totalHours,
            'total_pay'   => $totalPay,
        ], 200);
    }

    public function setHourlyRate() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['user_id']) || !isset($data['rate'])) {
            return error('user_id and rate are required', 400);
        }

        $payrollModel = new Payroll();
        $payrollModel->setHourlyRate($data['user_id'], (float)$data['rate']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'payroll', "Hourly rate set for user: {$data['user_id']}");

        return response([], 200, 'Hourly rate updated');
    }

    public function getPayroll($staffId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $payrollModel = new Payroll();
        $payrolls = $payrollModel->getStaffPayroll($staffId);

        return response($payrolls, 200);
    }

    public function calculatePayroll($staffId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['month'])) {
            return error('Month is required', 400);
        }

        $payrollModel = new Payroll();
        $calculation = $payrollModel->calculatePayroll($staffId, $data['month']);

        return response($calculation, 200);
    }

    public function recordPayment($staffId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['month', 'amount', 'payment_date'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $payrollModel = new Payroll();
        $payrollId = $payrollModel->create([
            'staff_id' => $staffId,
            'month' => $data['month'],
            'basic_salary' => $data['basic_salary'] ?? 0,
            'allowances' => $data['allowances'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
            'net_salary' => $data['amount'],
            'payment_date' => $data['payment_date'],
            'payment_method' => $data['payment_method'] ?? 'bank',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'payroll', "Payroll recorded for staff: $staffId");

        return response(['payroll_id' => $payrollId], 201, 'Payroll recorded');
    }
}
