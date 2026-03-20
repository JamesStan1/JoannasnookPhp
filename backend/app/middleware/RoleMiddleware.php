<?php

namespace App\Middleware;

class RoleMiddleware {
    public static function handle($user, $requiredRoles) {
        if (!is_array($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }

        // IT is a superuser — bypasses all role restrictions.
        // Manager also gets admin-level access wherever admin is required.
        $effectiveRole = $user['role'];
        if ($effectiveRole === 'it') {
            return true;
        }
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
