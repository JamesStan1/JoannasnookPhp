<?php

namespace App\Middleware;

class RoleMiddleware {
    public static function handle($user, $requiredRoles) {
        if (!is_array($requiredRoles)) {
            $requiredRoles = [$requiredRoles];
        }

        if (!in_array($user['role'], $requiredRoles)) {
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
