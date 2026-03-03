<?php

namespace App\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;

class RestaurantController {
    public function getMenuItems() {
        $menuModel = new MenuItem();
        $items = $menuModel->getAllActive();
        return response($items, 200);
    }

    public function getAllMenuItems() {
        $user = \App\Middleware\AuthMiddleware::handle();
        $menuModel = new MenuItem();
        $items = $menuModel->getAllIncludingInactive();
        return response($items, 200);
    }

    public function getByCategory($category) {
        $menuModel = new MenuItem();
        $items = $menuModel->getByCategory($category);
        return response($items, 200);
    }

    private function handleImageUpload($fileKey = 'image') {
        if (empty($_FILES[$fileKey]['tmp_name'])) return null;
        $file = $_FILES[$fileKey];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed)) return null;
        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('dish_', true) . '.' . strtolower($ext);
        $dir  = __DIR__ . '/../../public/uploads/menu/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        move_uploaded_file($file['tmp_name'], $dir . $name);
        return 'uploads/menu/' . $name;
    }

    public function createMenuItem() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        // Support both multipart/form-data (with image) and JSON
        $isMultipart = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart') !== false;
        if ($isMultipart) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        if (empty($data['name']) || !isset($data['price'])) {
            return error('name and price are required', 400);
        }

        $imageUrl = $this->handleImageUpload('image');

        $menuModel = new MenuItem();
        $itemId = $menuModel->create([
            'name'        => $data['name'],
            'category'    => $data['category'] ?? null,
            'description' => $data['description'] ?? null,
            'image_url'   => $imageUrl,
            'price'       => $data['price'],
            'active'      => isset($data['active']) ? (int)$data['active'] : 1,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'restaurant', "Menu item created: $itemId");

        $item = $menuModel->findById($itemId);
        return response($item, 201, 'Menu item created');
    }

    public function updateMenuItem($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        // Support both multipart/form-data (with image) and JSON
        $isMultipart = isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart') !== false;
        if ($isMultipart) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        $menuModel = new MenuItem();
        $item = $menuModel->findById($id);
        if (!$item) return error('Menu item not found', 404);

        $imageUrl = $this->handleImageUpload('image');
        // If user explicitly passes remove_image=1 (no new file), clear stored image
        if (!$imageUrl && !empty($data['remove_image'])) {
            $imageUrl = null;
        } elseif (!$imageUrl) {
            $imageUrl = $item['image_url']; // keep existing
        }

        $menuModel->update($id, [
            'name'        => $data['name'] ?? $item['name'],
            'category'    => $data['category'] ?? $item['category'],
            'description' => $data['description'] ?? $item['description'],
            'image_url'   => $imageUrl,
            'price'       => $data['price'] ?? $item['price'],
            'active'      => isset($data['active']) ? (int)$data['active'] : $item['active'],
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'restaurant', "Menu item updated: $id");

        $updated = $menuModel->findById($id);
        return response($updated, 200, 'Menu item updated');
    }

    public function deleteMenuItem($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $menuModel = new MenuItem();
        $item = $menuModel->findById($id);
        if (!$item) return error('Menu item not found', 404);

        // Soft delete by setting active = 0
        $menuModel->update($id, ['active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'restaurant', "Menu item removed: $id");

        return response([], 200, 'Menu item removed');
    }

    public function getArchivedMenuItems() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $db = (new MenuItem())->getDb();
        $query = "SELECT * FROM menu_items WHERE active = 0 ORDER BY updated_at DESC, name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return response($items, 200);
    }

    public function restoreMenuItem($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $menuModel = new MenuItem();
        $item = $menuModel->findById($id);
        if (!$item) return error('Menu item not found', 404);

        $menuModel->update($id, ['active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'restaurant', "Menu item restored: $id");

        return response([], 200, 'Menu item restored');
    }

    public function forceDeleteMenuItem($id) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin']);

        $menuModel = new MenuItem();
        $item = $menuModel->findById($id);
        if (!$item) return error('Menu item not found', 404);

        $menuModel->delete($id);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'delete', 'restaurant', "Menu item permanently deleted: $id");

        return response([], 200, 'Menu item permanently deleted');
    }

    public function createOrder() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['items'])) {
            return error('Order items required', 400);
        }

        $orderModel = new Order();
        $orderId = $orderModel->createWithItems([
            'customer_id'     => $data['customer_id'] ?? null,
            'customer_name'   => $data['customer_name'] ?? 'Guest',
            'cashier_id'      => $user['user_id'],
            'order_type'      => $data['order_type'] ?? 'restaurant',
            'payment_method'  => $data['payment_method'] ?? 'cash',
            'received_amount' => isset($data['received_amount']) ? $data['received_amount'] : null,
            'discount_type'   => $data['discount_type'] ?? 'none',
            'total_amount'    => $data['total_amount'],
            'status'          => 'completed',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ], $data['items']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'restaurant', "Order created: $orderId");

        return response(['order_id' => $orderId], 201, 'Order created');
    }

    public function getOrders() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $db = (new Order())->getDb();
        $query = "
            SELECT o.id, o.customer_name, o.payment_method, o.total_amount,
                   o.received_amount, o.discount_type, o.status, o.created_at,
                   u.name AS cashier_name,
                   COUNT(oi.id) AS item_count
            FROM orders o
            LEFT JOIN users u ON o.cashier_id = u.id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.order_type = 'pos'
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return response($orders, 200);
    }

    public function getOrderById($id) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $db = (new Order())->getDb();
        $query = "
            SELECT o.*, u.name AS cashier_name
            FROM orders o
            LEFT JOIN users u ON o.cashier_id = u.id
            WHERE o.id = ?
        ";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) return error('Order not found', 404);

        $itemsQuery = "
            SELECT oi.quantity, oi.unit_price, mi.name AS item_name
            FROM order_items oi
            LEFT JOIN menu_items mi ON mi.id = oi.menu_item_id
            WHERE oi.order_id = ?
        ";
        $stmt2 = $db->prepare($itemsQuery);
        $stmt2->execute([$id]);
        $order['items'] = $stmt2->fetchAll(\PDO::FETCH_ASSOC);

        return response($order, 200);
    }

    public function getKitchenOrders() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['chef']);

        $orderModel = new Order();
        $orders = $orderModel->getKitchenOrders();

        foreach ($orders as &$order) {
            $order['items'] = $orderModel->getOrderItems($order['id']);
        }

        return response($orders, 200);
    }

    public function updateOrderStatus($orderId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['chef', 'manager']);

        $data = json_decode(file_get_contents('php://input'), true);

        $orderModel = new Order();
        $orderModel->update($orderId, [
            'status' => $data['status'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'restaurant', "Order status updated: $orderId to {$data['status']}");

        return response([], 200, 'Order status updated');
    }
}
