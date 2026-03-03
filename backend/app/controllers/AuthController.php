<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\JwtService;

class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return error('Method not allowed', 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return error('Email and password required', 400);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($data['email']);

        if (!$user || !$userModel->verifyPassword($data['password'], $user['password'])) {
            return error('Invalid credentials', 401);
        }

        // Update last login
        $userModel->updateLastLogin($user['id']);

        // Log login activity
        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['id'], 'login', 'auth', 'User login successful');

        $token = JwtService::generate($user);

        $avatarUrl = null;
        if (!empty($user['avatar'])) {
            $avatarUrl = '/uploads/avatars/' . basename($user['avatar']);
        }

        return response([
            'token' => $token,
            'user' => [
                'id'     => $user['id'],
                'name'   => $user['name'],
                'email'  => $user['email'],
                'role'   => $user['role'],
                'avatar' => $avatarUrl,
            ],
        ], 200, 'Login successful');
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return error('Method not allowed', 405);
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['name', 'email', 'password', 'phone'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $userModel = new User();

        // Check if user exists
        if ($userModel->findByEmail($data['email'])) {
            return error('Email already registered', 409);
        }

        $userId = $userModel->createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'],
            'role' => $data['role'] ?? 'guest',
            'active' => 1,
        ]);

        return response(['user_id' => $userId], 201, 'Registration successful');
    }

    public function verifyToken() {
        try {
            $jwtData   = \App\Middleware\AuthMiddleware::handle();
            $userModel = new User();
            $profile   = $userModel->findById($jwtData['user_id']);
            if (!$profile) return error('User not found', 404);
            unset($profile['password']);
            if (!empty($profile['avatar'])) {
                $profile['avatar'] = '/uploads/avatars/' . basename($profile['avatar']);
            }
            // Merge JWT payload keys that the frontend expects
            $profile['user_id'] = $jwtData['user_id'];
            return response($profile, 200, 'Token valid');
        } catch (\Exception $e) {
            return error('Invalid token', 401);
        }
    }

    public function logout() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'logout', 'auth', 'User logout');

        return response([], 200, 'Logout successful');
    }

    public function getProfile() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $userModel = new User();
        $profile = $userModel->findById($user['user_id']);
        if (!$profile) return error('User not found', 404);
        unset($profile['password']);
        // Normalize avatar to a usable URL path
        if (!empty($profile['avatar'])) {
            $profile['avatar'] = '/uploads/avatars/' . basename($profile['avatar']);
        }
        return response($profile, 200);
    }

    public function updateProfile() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $data = json_decode(file_get_contents('php://input'), true);
        $userModel = new User();

        $update = [];
        foreach (['name', 'phone'] as $field) {
            if (isset($data[$field])) $update[$field] = $data[$field];
        }

        // Allow admins to update their own email
        if (!empty($data['email']) && $user['role'] === 'admin') {
            $newEmail = trim($data['email']);
            $existing = $userModel->findByEmail($newEmail);
            if ($existing && $existing['id'] != $user['user_id']) {
                return error('Email already in use', 409);
            }
            $update['email'] = $newEmail;
        }

        if (!empty($data['new_password'])) {
            if (empty($data['current_password'])) return error('Current password required', 400);
            $profile = $userModel->findById($user['user_id']);
            if (!$userModel->verifyPassword($data['current_password'], $profile['password'])) {
                return error('Current password is incorrect', 401);
            }
            $update['password'] = password_hash($data['new_password'], PASSWORD_BCRYPT);
        }

        if (empty($update)) return error('No fields to update', 400);

        $update['updated_at'] = date('Y-m-d H:i:s');
        $userModel->update($user['user_id'], $update);

        $profile = $userModel->findById($user['user_id']);
        unset($profile['password']);
        if (!empty($profile['avatar'])) {
            $profile['avatar'] = '/uploads/avatars/' . basename($profile['avatar']);
        }

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'auth', 'Profile updated');

        return response($profile, 200, 'Profile updated');
    }

    public function uploadAvatar() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $userId = $user['user_id'];

        if (empty($_FILES['avatar'])) {
            return error('No file uploaded', 400);
        }

        $file = $_FILES['avatar'];

        // Validate file type
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed)) {
            return error('Invalid file type. Only JPEG, PNG, GIF and WebP are allowed.', 400);
        }

        // Max 5 MB
        if ($file['size'] > 5 * 1024 * 1024) {
            return error('File too large. Maximum size is 5 MB.', 400);
        }

        $ext = match($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Remove old avatar if it exists
        $userModel = new User();
        $existing = $userModel->findById($userId);
        if (!empty($existing['avatar'])) {
            $oldPath = $uploadDir . basename($existing['avatar']);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $filename  = 'avatar_' . $userId . '_' . time() . '.' . $ext;
        $destPath  = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return error('Failed to save file', 500);
        }

        // Store only the filename in DB
        $userModel->update($userId, [
            'avatar'     => $filename,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $avatarUrl = '/uploads/avatars/' . $filename;

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($userId, 'update', 'auth', 'Avatar updated');

        return response(['avatar' => $avatarUrl], 200, 'Avatar uploaded successfully');
    }

    public function removeAvatar() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $userId = $user['user_id'];

        $userModel = new User();
        $existing = $userModel->findById($userId);

        if (!empty($existing['avatar'])) {
            $uploadDir = __DIR__ . '/../../public/uploads/avatars/';
            $oldPath   = $uploadDir . basename($existing['avatar']);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
            $userModel->update($userId, ['avatar' => null, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($userId, 'update', 'auth', 'Avatar removed');

        return response([], 200, 'Avatar removed');
    }
}
