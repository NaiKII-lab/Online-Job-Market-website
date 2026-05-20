<?php
session_start();
include('db_server.php');
header('Content-Type: application/json; charset=utf-8');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
  echo json_encode([]);
  exit;
}

// ✅ ดึงเฉพาะแจ้งเตือนที่ยังไม่อ่าน
$sql = "SELECT id, message, is_read, created_at 
        FROM notifications 
        WHERE user_id = ? AND is_read = 0 
        ORDER BY created_at DESC 
        LIMIT 20";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

$notifications = [];
while ($row = $res->fetch_assoc()) {
  $notifications[] = $row;
}

echo json_encode($notifications);
exit;
