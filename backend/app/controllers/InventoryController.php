<?php

namespace App\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLog;

class InventoryController {

    private function computeStatus(array &$items): void {
        foreach ($items as &$item) {
            if ($item['current_stock'] <= 0) {
                $item['status'] = 'Out of Stock';
            } elseif ($item['current_stock'] <= $item['minimum_stock']) {
                $item['status'] = 'Low Stock';
            } else {
                $item['status'] = 'In Stock';
            }
        }
    }

    public function getAll() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $category = $_GET['category'] ?? '';
        $search   = $_GET['search'] ?? '';

        $items = (new Inventory())->getFiltered($category, $search);
        $this->computeStatus($items);

        return response($items, 200);
    }

    public function getLowStock() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $items = (new Inventory())->getLowStockItems();
        $this->computeStatus($items);

        return response($items, 200);
    }

    public function addItem() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        foreach (['name', 'category', 'quantity'] as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return error("$field is required", 400);
            }
        }

        $inventoryModel = new Inventory();
        $itemId = $inventoryModel->create([
            'name'          => trim($data['name']),
            'category'      => trim($data['category']),
            'current_stock' => (int)$data['quantity'],
            'unit'          => trim($data['unit'] ?? 'piece'),
            'unit_price'    => (float)($data['unit_price'] ?? 0),
            'minimum_stock' => (int)($data['minimum_stock'] ?? 5),
            'on_delivery'   => (int)($data['on_delivery'] ?? 0),
            'supplier'      => trim($data['supplier'] ?? ''),
            'created_at'    => date('Y-m-d H:i:s'),
            'last_updated'  => date('Y-m-d H:i:s'),
        ]);

        (new InventoryLog())->logAction($itemId, 'add', (int)$data['quantity'], $user['user_id'], 'Initial stock');

        $item = $inventoryModel->findById($itemId);
        return response($item, 201, 'Item added');
    }

    public function updateItem($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $inventoryModel = new Inventory();
        $item = $inventoryModel->findById($itemId);
        if (!$item) return error('Item not found', 404);

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return error('Invalid data', 400);

        $inventoryModel->update($itemId, [
            'name'          => trim($data['name']           ?? $item['name']),
            'category'      => trim($data['category']       ?? $item['category']),
            'current_stock' => (int)($data['quantity']      ?? $item['current_stock']),
            'unit'          => trim($data['unit']           ?? $item['unit']),
            'unit_price'    => (float)($data['unit_price']  ?? $item['unit_price']),
            'minimum_stock' => (int)($data['minimum_stock'] ?? $item['minimum_stock']),
            'on_delivery'   => (int)($data['on_delivery']   ?? $item['on_delivery']),
            'supplier'      => trim($data['supplier']       ?? $item['supplier']),
            'last_updated'  => date('Y-m-d H:i:s'),
        ]);

        $updated = $inventoryModel->findById($itemId);
        return response($updated, 200, 'Item updated');
    }

    public function updateStock($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['quantity']) || empty($data['action'])) {
            return error('Quantity and action required', 400);
        }

        $inventoryModel = new Inventory();
        $result = $inventoryModel->updateStock($itemId, (int)$data['quantity'], $data['action']);
        if (!$result) return error('Failed to update stock', 400);

        (new InventoryLog())->logAction($itemId, $data['action'], (int)$data['quantity'], $user['user_id'], $data['notes'] ?? null);

        return response([], 200, 'Stock updated');
    }

    public function withdrawItem($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['quantity']) || (int)$data['quantity'] <= 0) {
            return error('Quantity must be greater than 0', 400);
        }

        $inventoryModel = new Inventory();
        $item = $inventoryModel->findById($itemId);
        if (!$item) return error('Item not found', 404);

        if ($item['current_stock'] < (int)$data['quantity']) {
            return error('Insufficient stock. Available: ' . $item['current_stock'], 400);
        }

        $result = $inventoryModel->updateStock($itemId, (int)$data['quantity'], 'reduce');
        if (!$result) return error('Failed to withdraw stock', 400);

        (new InventoryLog())->logAction($itemId, 'reduce', (int)$data['quantity'], $user['user_id'], $data['notes'] ?? 'Withdrawal');

        $updated = $inventoryModel->findById($itemId);
        return response($updated, 200, 'Stock withdrawn successfully');
    }

    public function deleteItem($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $item = (new Inventory())->findById($itemId);
        if (!$item) return error('Item not found', 404);

        (new Inventory())->softDelete($itemId);
        return response([], 200, 'Item archived');
    }

    public function getArchived() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $items = (new Inventory())->getArchived();
        $this->computeStatus($items);

        return response($items, 200);
    }

    public function restoreItem($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $model = new Inventory();
        $item = $model->findById($itemId);
        if (!$item || empty($item['deleted_at'])) return error('Archived item not found', 404);

        $model->restore($itemId);
        return response([], 200, 'Item restored');
    }

    public function forceDeleteItem($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $model = new Inventory();
        $item = $model->findById($itemId);
        if (!$item || empty($item['deleted_at'])) return error('Archived item not found', 404);

        $model->forceDelete($itemId);
        return response([], 200, 'Item permanently deleted');
    }

    public function getHistory($itemId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $history = (new InventoryLog())->getInventoryHistory($itemId);
        return response($history, 200);
    }

    public function getActivityLog() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $limit = max(1, min(500, (int)($_GET['limit'] ?? 100)));
        $logs = (new InventoryLog())->getAllWithDetails($limit);
        return response($logs, 200);
    }
}
