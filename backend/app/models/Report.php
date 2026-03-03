<?php

namespace App\Models;

class Report extends BaseModel {
    protected $table = 'staff_reports';

    public function getMyReports($reporterId) {
        $query = "
            SELECT r.*,
                   u.name AS responder_name
            FROM {$this->table} r
            LEFT JOIN users u ON r.responded_by = u.id
            WHERE r.reporter_id = ?
            ORDER BY r.created_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$reporterId]);
        return $stmt->fetchAll();
    }

    public function getAllReports($status = null, $category = null) {
        $query = "
            SELECT r.*,
                   CASE WHEN r.is_anonymous = 1 THEN 'Anonymous' ELSE u.name END AS reporter_name,
                   CASE WHEN r.is_anonymous = 1 THEN NULL ELSE u.email END AS reporter_email,
                   CASE WHEN r.is_anonymous = 1 THEN NULL ELSE u.role END AS reporter_role,
                   resp.name AS responder_name
            FROM {$this->table} r
            LEFT JOIN users u    ON r.reporter_id    = u.id
            LEFT JOIN users resp ON r.responded_by   = resp.id
            WHERE 1=1
        ";
        $params = [];

        if ($status) {
            $query   .= " AND r.status = ?";
            $params[] = $status;
        }
        if ($category) {
            $query   .= " AND r.category = ?";
            $params[] = $category;
        }

        $query .= " ORDER BY
            CASE r.status WHEN 'pending' THEN 0 WHEN 'in_review' THEN 1 ELSE 2 END,
            r.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function submitReport($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    public function respond($reportId, $responderId, $response, $status) {
        return $this->update($reportId, [
            'admin_response' => $response,
            'responded_by'   => $responderId,
            'responded_at'   => date('Y-m-d H:i:s'),
            'status'         => $status,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function updateStatus($reportId, $status) {
        return $this->update($reportId, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
