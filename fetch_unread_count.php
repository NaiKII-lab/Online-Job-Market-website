<?php
session_start();
include 'db_server.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['count' => 0]);
  exit;
}

$user_id = intval($_SESSION['user_id']);

$sql = "SELECT COUNT(*) AS unread_count 
        FROM notifications 
        WHERE user_id=? AND is_read=0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

echo json_encode(['count' => intval($row['unread_count'])]);
