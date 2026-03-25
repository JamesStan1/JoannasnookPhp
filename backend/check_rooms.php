<?php
require_once 'app/helpers/Bootstrap.php';
$db = Database::connect();
$stmt = $db->query('SELECT id, room_number, type, status FROM rooms WHERE deleted_at IS NULL ORDER BY type');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT);
