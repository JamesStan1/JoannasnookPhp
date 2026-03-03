<?php

namespace App\Services;

class JwtService {
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }

    public static function generate($user) {
        $config = require __DIR__ . '/../../config/auth.php';

        $header = self::base64UrlEncode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = self::base64UrlEncode(json_encode([
            'iat'     => time(),
            'exp'     => time() + $config['jwt']['expiration'],
            'user_id' => $user['id'],
            'email'   => $user['email'],
            'name'    => $user['name'],
            'role'    => $user['role'],
        ]));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $config['jwt']['secret'], true)
        );

        return "$header.$payload.$signature";
    }

    public static function verify($token) {
        try {
            $config = require __DIR__ . '/../../config/auth.php';
            $parts = explode('.', $token);

            if (count($parts) !== 3) {
                throw new \Exception('Invalid token format');
            }

            [$header, $payload, $signature] = $parts;

            $expectedSig = self::base64UrlEncode(
                hash_hmac('sha256', "$header.$payload", $config['jwt']['secret'], true)
            );

            if (!hash_equals($expectedSig, $signature)) {
                throw new \Exception('Invalid signature');
            }

            $data = json_decode(self::base64UrlDecode($payload), true);

            if ($data['exp'] < time()) {
                throw new \Exception('Token expired');
            }

            return $data;
        } catch (\Exception $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Invalid token: ' . $e->getMessage(),
            ]);
            exit;
        }
    }
}
