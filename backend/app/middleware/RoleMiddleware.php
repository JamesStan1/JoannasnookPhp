<?php

namespace App\Middleware;

class RoleMiddleware {
    public static function handle($user, $requiredRoles) {
        if (!is_array($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }

        // Manager has the same access as admin across all modules
        $effectiveRole = $user['role'];
        if ($effectiveRole === 'manager' && in_array('admin', $requiredRoles)) {
            return true;
        }

        if (!in_array($effectiveRole, $requiredRoles)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Insufficient permissions',
            ]);
            exit;
        }

        return true;
    }
}
