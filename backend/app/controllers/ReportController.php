<?php

namespace App\Controllers;

use App\Models\Report;
use App\Models\AuditLog;

class ReportController {

    public function submitReport() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['category', 'subject', 'description'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $reportModel = new Report();
        $id = $reportModel->submitReport([
            'reporter_id'  => $user['user_id'],
            'category'     => $data['category'],
            'subject'      => $data['subject'],
            'description'  => $data['description'],
            'is_anonymous' => isset($data['is_anonymous']) ? (int) $data['is_anonymous'] : 0,
            'status'       => 'pending',
        ]);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'create', 'reports', "Report submitted: $id");

        return response(['report_id' => $id], 201, 'Report submitted');
    }

    public function getMyReports() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $reportModel = new Report();
        $reports = $reportModel->getMyReports($user['user_id']);

        return response($reports, 200);
    }

    public function getAllReports() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $status   = $_GET['status']   ?? null;
        $category = $_GET['category'] ?? null;

        $reportModel = new Report();
        $reports = $reportModel->getAllReports($status, $category);

        $total     = count($reports);
        $pending   = count(array_filter($reports, fn($r) => $r['status'] === 'pending'));
        $in_review = count(array_filter($reports, fn($r) => $r['status'] === 'in_review'));
        $resolved  = count(array_filter($reports, fn($r) => $r['status'] === 'resolved'));
        $dismissed = count(array_filter($reports, fn($r) => $r['status'] === 'dismissed'));

        return response([
            'summary' => compact('total', 'pending', 'in_review', 'resolved', 'dismissed'),
            'reports' => $reports,
        ], 200);
    }

    public function respondToReport($reportId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $status   = $data['status']   ?? 'resolved';
        $response = $data['response'] ?? null;

        $allowed = ['in_review', 'resolved', 'dismissed'];
        if (!in_array($status, $allowed)) {
            return error('Invalid status', 400);
        }

        $reportModel = new Report();
        $reportModel->respond($reportId, $user['user_id'], $response, $status);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'update', 'reports', "Report $reportId marked as $status");

        return response([], 200, "Report marked as $status");
    }

    public function updateReportStatus($reportId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data   = json_decode(file_get_contents('php://input'), true);
        $status = $data['status'] ?? null;

        $allowed = ['pending', 'in_review', 'resolved', 'dismissed'];
        if (!$status || !in_array($status, $allowed)) {
            return error('Valid status required', 400);
        }

        $reportModel = new Report();
        $reportModel->updateStatus($reportId, $status);

        $auditLog = new AuditLog();
        $auditLog->log($user['user_id'], 'update', 'reports', "Report $reportId status → $status");

        return response([], 200, 'Status updated');
    }
}
