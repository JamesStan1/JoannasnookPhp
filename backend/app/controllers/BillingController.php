<?php

namespace App\Controllers;

use App\Models\Bill;
use App\Models\BillItem;

class BillingController {
    public function createBill() {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'accountant']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['items'])) {
            return error('Bill items required', 400);
        }

        $billModel = new Bill();
        $billId = $billModel->createBill([
            'guest_id'       => $data['guest_id'] ?? null,
            'reservation_id' => $data['reservation_id'] ?? null,
            'event_id'       => $data['event_id'] ?? null,
            'bill_type'      => $data['bill_type'] ?? 'room',
            'subtotal'       => $data['subtotal'],
            'discount'       => $data['discount'] ?? 0,
            'tax'            => $data['tax'] ?? 0,
            'total_amount'   => $data['total_amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'payment_status' => 'pending',
            'processed_by'   => $user['user_id'],
            'bill_number'    => 'BILL-' . date('Ymd') . '-' . rand(1000, 9999),
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], $data['items']);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'create', 'billing', "Bill created: $billId");

        return response(['bill_id' => $billId], 201, 'Bill created');
    }

    public function getAllBills() {
        $user = \App\Middleware\AuthMiddleware::handle();

        $billModel = new Bill();
        $bills = $billModel->getAllBills();

        return response($bills, 200);
    }

    public function getBillDetails($billId) {
        $billModel = new Bill();
        $bill = $billModel->getBillDetails($billId);

        if (!$bill) {
            return error('Bill not found', 404);
        }

        $bill['items'] = $billModel->getBillItems($billId);
        return response($bill, 200);
    }

    public function getGuestBills($guestId) {
        $user = \App\Middleware\AuthMiddleware::handle();

        $billModel = new Bill();
        $bills = $billModel->getGuestBills($guestId);

        return response($bills, 200);
    }

    public function recordPayment($billId) {
        $user = \App\Middleware\AuthMiddleware::handle();
        \App\Middleware\RoleMiddleware::handle($user, ['admin', 'manager', 'accountant']);

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['amount']) || empty($data['method'])) {
            return error('Amount and payment method required', 400);
        }

        $billModel = new Bill();
        $billModel->update($billId, [
            'payment_status' => 'paid',
            'payment_method' => $data['method'],
            'paid_amount'    => $data['amount'],
            'payment_date'   => date('Y-m-d H:i:s'),
            'processed_by'   => $user['user_id'],
        ]);

        $auditLog = new \App\Models\AuditLog();
        $auditLog->log($user['user_id'], 'update', 'billing', "Payment recorded for bill: $billId");

        return response([], 200, 'Payment recorded');
    }

    public function generateReceipt($billId) {
        $billModel = new Bill();
        $bill = $billModel->getBillDetails($billId);

        if (!$bill) {
            return error('Bill not found', 404);
        }

        $bill['items'] = $billModel->getBillItems($billId);

        // Generate receipt content
        $receipt = $this->formatReceipt($bill);

        return response(['receipt' => $receipt], 200);
    }

    private function formatReceipt($bill) {
        $receipt = "\n";
        $receipt .= "=" . str_repeat("=", 50) . "\n";
        $receipt .= str_pad("RECEIPT", 52, " ", STR_PAD_BOTH) . "\n";
        $receipt .= "=" . str_repeat("=", 50) . "\n";
        $receipt .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $receipt .= "Bill #: " . $bill['bill_number'] . "\n\n";

        if ($bill['guest_name']) {
            $receipt .= "Guest: " . $bill['guest_name'] . "\n";
            $receipt .= "Email: " . $bill['guest_email'] . "\n\n";
        }

        $receipt .= str_repeat("-", 52) . "\n";
        $receipt .= str_pad("Item", 30) . str_pad("Qty", 8, " ", STR_PAD_LEFT) . str_pad("Amount", 12, " ", STR_PAD_LEFT) . "\n";
        $receipt .= str_repeat("-", 52) . "\n";

        foreach ($bill['items'] as $item) {
            $receipt .= str_pad($item['description'], 30);
            $receipt .= str_pad($item['quantity'], 8, " ", STR_PAD_LEFT);
            $receipt .= str_pad($item['amount'], 12, " ", STR_PAD_LEFT) . "\n";
        }

        $receipt .= str_repeat("-", 52) . "\n";
        $receipt .= str_pad("Subtotal:", 42) . str_pad($bill['subtotal'], 10, " ", STR_PAD_LEFT) . "\n";

        if ($bill['discount'] > 0) {
            $receipt .= str_pad("Discount:", 42) . str_pad(-$bill['discount'], 10, " ", STR_PAD_LEFT) . "\n";
        }

        if ($bill['tax'] > 0) {
            $receipt .= str_pad("Tax:", 42) . str_pad($bill['tax'], 10, " ", STR_PAD_LEFT) . "\n";
        }

        $receipt .= str_repeat("=", 52) . "\n";
        $receipt .= str_pad("TOTAL:", 42) . str_pad($bill['total_amount'], 10, " ", STR_PAD_LEFT) . "\n";
        $receipt .= str_repeat("=", 52) . "\n";

        if ($bill['payment_status'] === 'paid') {
            $receipt .= "Payment Method: " . $bill['payment_method'] . "\n";
            $receipt .= "Status: PAID\n";
        } else {
            $receipt .= "Status: PENDING\n";
        }

        $receipt .= "\nThank you for your business!\n";
        $receipt .= "=" . str_repeat("=", 50) . "\n";

        return $receipt;
    }
}
