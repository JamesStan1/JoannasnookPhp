<?php

namespace App\Models;

class Bill extends BaseModel {
    protected $table = 'bills';

    public function createBill($billData, $items) {
        $billId = $this->create($billData);

        $billItem = new BillItem();
        foreach ($items as $item) {
            $item['bill_id'] = $billId;
            $billItem->create($item);
        }

        return $billId;
    }

    public function getBillDetails($billId) {
        $query = "
            SELECT b.*, u.name as guest_name, u.email as guest_email
            FROM {$this->table} b
            LEFT JOIN users u ON b.guest_id = u.id
            WHERE b.id = ?
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$billId]);
        return $stmt->fetch();
    }

    public function getBillItems($billId) {
        $billItem = new BillItem();
        return $billItem->find(['bill_id' => $billId]);
    }

    public function getGuestBills($guestId) {
        return $this->find(['guest_id' => $guestId]);
    }

    public function getAllBills() {
        $query = "
            SELECT
                b.id, b.bill_number, b.bill_type, b.total_amount,
                b.payment_status, b.payment_method, b.created_at,
                b.reservation_id, b.event_id,
                -- guest
                u.name  AS guest_name,
                -- reservation / room
                r.check_in_date, r.check_out_date,
                rm.room_number, rm.type AS room_type,
                -- event
                e.event_type, e.venue, e.event_date, e.client_name AS event_client
            FROM {$this->table} b
            LEFT JOIN users        u  ON u.id  = b.guest_id
            LEFT JOIN reservations r  ON r.id  = b.reservation_id
            LEFT JOIN rooms        rm ON rm.id = r.room_id
            LEFT JOIN events       e  ON e.id  = b.event_id
            ORDER BY b.created_at DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
