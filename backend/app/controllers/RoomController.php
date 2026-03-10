<?php

namespace App\Controllers;

use App\Models\Room;

class RoomController {
    public function getAll() {
        $roomModel = new Room();
        $rooms = $roomModel->all();
        return response($rooms, 200);
    }

    public function getById($id) {
        $roomModel = new Room();
        $room = $roomModel->findById($id);

        if (!$room) {
            return error('Room not found', 404);
        }

        return response($room, 200);
    }

    private function handleImageUpload($fileKey = 'image') {
        if (empty($_FILES[$fileKey]['tmp_name'])) return null;
        $file = $_FILES[$fileKey];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) return null;
        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('room_', true) . '.' . strtolower($ext);
        $dir  = rtrim(UPLOADS_BASE_PATH, '/') . '/rooms/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        move_uploaded_file($file['tmp_name'], $dir . $name);
        return 'uploads/rooms/' . $name;
    }

    public function create() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return error('Method not allowed', 405);
        }

        $isMultipart = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart') !== false;
        if ($isMultipart) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $required = ['room_number', 'type', 'price'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $imageUrl = $this->handleImageUpload('image');

        $roomModel = new Room();
        $roomId = $roomModel->create([
            'room_number' => $data['room_number'],
            'type' => $data['type'],
            'price' => $data['price'],
            'capacity' => $data['capacity'] ?? 2,
            'description' => $data['description'] ?? null,
            'image_url' => $imageUrl,
            'status' => 'available',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'rooms', "Room created: $roomId");

        return response(['room_id' => $roomId], 201, 'Room created successfully');
    }

    public function update($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager']);

        $isMultipart = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart') !== false;
        if ($isMultipart) {
            // PHP doesn't parse PUT multipart automatically; parse raw input for PUT workaround
            // Frontend should POST with _method=PUT override, or we accept POST for updates with image
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true);
        }

        $roomModel = new Room();
        $existingRoom = $roomModel->findById($id);
        if (!$existingRoom) return error('Room not found', 404);

        $imageUrl = $this->handleImageUpload('image');
        if (!$imageUrl && !empty($data['remove_image'])) {
            $imageUrl = null;
        } elseif (!$imageUrl) {
            $imageUrl = $existingRoom['image_url']; // keep existing
        }

        $updateData = [
            'room_number' => $data['room_number'] ?? $existingRoom['room_number'],
            'type' => $data['type'] ?? $existingRoom['type'],
            'price' => $data['price'] ?? $existingRoom['price'],
            'capacity' => $data['capacity'] ?? $existingRoom['capacity'],
            'description' => $data['description'] ?? $existingRoom['description'],
            'image_url' => $imageUrl,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $roomModel->update($id, $updateData);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'rooms', "Room updated: $id");

        return response([], 200, 'Room updated successfully');
    }

    public function updateStatus($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'housekeeping']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['status'])) {
            return error('Status is required', 400);
        }

        $roomModel = new Room();
        $roomModel->updateRoomStatus($id, $data['status']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'rooms', "Room status updated: $id to {$data['status']}");

        return response([], 200, 'Room status updated');
    }

    public function getArchived() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $roomModel = new Room();
        return response($roomModel->getArchived(), 200);
    }

    public function archive($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $roomModel = new Room();
        $room = $roomModel->findById($id);
        if (!$room) return error('Room not found', 404);

        $roomModel->softDelete($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'rooms', "Room archived: $id");

        return response([], 200, 'Room archived');
    }

    public function restore($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $roomModel = new Room();
        $roomModel->restore($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'rooms', "Room restored: $id");

        return response([], 200, 'Room restored');
    }

    public function forceDelete($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $roomModel = new Room();
        $roomModel->delete($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'rooms', "Room permanently deleted: $id");

        return response([], 200, 'Room permanently deleted');
    }
}
