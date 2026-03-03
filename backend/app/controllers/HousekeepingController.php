<?php

namespace App\Controllers;

use App\Models\HousekeepingTask;
use App\Models\Room;

class HousekeepingController {
    public function getStaffTasks() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['housekeeping']);

        $taskModel = new HousekeepingTask();
        $tasks = $taskModel->getStaffTasks($user['user_id']);

        return response($tasks, 200);
    }

    public function getPendingTasks() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $taskModel = new HousekeepingTask();
        $tasks = $taskModel->getPendingTasks();

        return response($tasks, 200);
    }

    public function createTask() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $required = ['room_id', 'task_type'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return error("$field is required", 400);
            }
        }

        $taskModel = new HousekeepingTask();
        $taskId = $taskModel->create([
            'room_id' => $data['room_id'],
            'staff_id' => $data['staff_id'] ?? null,
            'task_type' => $data['task_type'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'housekeeping', "Task created: $taskId");

        return response(['task_id' => $taskId], 201, 'Task created');
    }

    public function assignTask() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $data = json_decode(file_get_contents('php://input'), true);

        $taskModel = new HousekeepingTask();
        $taskModel->update($data['task_id'], [
            'staff_id' => $data['staff_id'],
            'status' => 'assigned',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'housekeeping', "Task assigned to staff: {$data['staff_id']}");

        return response([], 200, 'Task assigned');
    }

    public function completeTask($taskId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['housekeeping']);

        $taskModel = new HousekeepingTask();
        $task = $taskModel->findById($taskId);

        if (!$task) {
            return error('Task not found', 404);
        }

        $taskModel->completeTask($taskId);

        // Update room status to clean
        $roomModel = new Room();
        $roomModel->updateRoomStatus($task['room_id'], 'clean');

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'housekeeping', "Task completed: $taskId");

        return response([], 200, 'Task completed');
    }

    public function getRoomStatus($roomId) {
        $roomModel = new Room();
        $room = $roomModel->findById($roomId);

        if (!$room) {
            return error('Room not found', 404);
        }

        return response($room, 200);
    }

    public function getCleaningNotifications() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'housekeeping']);

        $roomModel = new Room();
        $rooms = $roomModel->getRoomsNeedingCleaning();

        return response($rooms, 200);
    }

    public function markRoomClean($roomId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'housekeeping']);

        $roomModel = new Room();
        $room = $roomModel->findById($roomId);

        if (!$room) {
            return error('Room not found', 404);
        }

        $roomModel->updateRoomStatus($roomId, 'available');

        // Auto-complete any pending/assigned housekeeping tasks for this room
        $taskModel = new HousekeepingTask();
        $taskModel->completeTasksByRoom($roomId);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'housekeeping', "Room $roomId marked as clean by staff {$user['user_id']}");

        return response([], 200, 'Room marked as clean');
    }
}
