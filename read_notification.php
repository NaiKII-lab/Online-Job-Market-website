<?php
session_start();
include 'db_server.php';
header('Content-Type: application/json; charset=utf-8');

// ถ้าไม่มี session ให้จบ
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'msg' => 'unauthorized']);
  exit;
}

$user_id = intval($_SESSION['user_id']);
$id = intval($_GET['id'] ?? 0);

// ✅ ถ้าไม่มี id — ห้ามอ่าน
if ($id <= 0) {
  echo json_encode(['status' => 'ignored', 'msg' => 'no id provided']);
  exit;
}

// ✅ อ่านเฉพาะ id นั้น
$sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

echo json_encode(['status' => 'success']);
