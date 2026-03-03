<?php

namespace App\Models;

class Order extends BaseModel {
    protected $table = 'orders';

    public function createWithItems($orderData, $items) {
        $orderId = $this->create($orderData);

        $orderItem = new OrderItem();
        foreach ($items as $item) {
            $item['order_id'] = $orderId;
            $orderItem->create($item);
        }

        return $orderId;
    }

    public function getOrderDetails($orderId) {
        $query = "
            SELECT o.*, u.name as customer_name
            FROM {$this->table} o
            LEFT JOIN users u ON o.customer_id = u.id
            WHERE o.id = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }

    public function getOrderItems($orderId) {
        $orderItem = new OrderItem();
        return $orderItem->find(['order_id' => $orderId]);
    }

    public function getKitchenOrders() {
        $query = "
            SELECT o.* FROM {$this->table} o
            WHERE o.status = 'pending' OR o.status = 'in_progress'
            ORDER BY o.created_at ASC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
