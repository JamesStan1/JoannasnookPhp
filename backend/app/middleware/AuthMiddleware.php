<?php

namespace App\Middleware;

class AuthMiddleware {
    public static function handle() {
        $headers = getallheaders();
        $token = null;

        if (isset($headers['Authorization'])) {
            $matches = [];
            if (preg_match('/Bearer\s+([^\s]+)/', $headers['Authorization'], $matches)) {
                $token = $matches[1];
            }
        }

        if (!$token) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Authorization token missing',
            ]);
            exit;
        }

        return \App\Services\JwtService::verify($token);
    }
}
