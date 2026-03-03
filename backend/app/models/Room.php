<?php

namespace App\Models;

class Room extends BaseModel {
    protected $table = 'rooms';

    public function all() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY room_number ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getArchived() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function softDelete($id) {
        return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function restore($id) {
        return $this->update($id, ['deleted_at' => null, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function getAvailableRooms($checkInDate, $checkOutDate) {
        $query = "
            SELECT r.* FROM {$this->table} r
            WHERE r.status = 'available'
            AND r.deleted_at IS NULL
            AND r.id NOT IN (
                SELECT DISTINCT room_id FROM reservations
                WHERE status IN ('approved', 'checked_in')
                AND check_in_date <= ? AND check_out_date >= ?
            )
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$checkOutDate, $checkInDate]);
        return $stmt->fetchAll();
    }

    public function getRoomStatus($roomId) {
        return $this->findById($roomId);
    }

    public function updateRoomStatus($roomId, $status) {
        return $this->update($roomId, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRoomsNeedingCleaning() {
        // Only rooms explicitly marked as dirty need cleaning.
        // ReservationController sets status='dirty' on checkout, and
        // markRoomClean() sets it back to 'available', so this query
        // will naturally stop returning a room once it is cleaned.
        $query = "
            SELECT r.id, r.room_number, r.type, r.status,
                   'dirty' AS clean_reason,
                   r.updated_at AS triggered_at
            FROM rooms r
            WHERE r.deleted_at IS NULL
              AND r.status = 'dirty'
            ORDER BY r.updated_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
