<?php
session_start();
include('db_server.php');
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
  echo json_encode(['success' => false]);
  exit;
}

// ✅ ถ้ามี id หมายถึงอ่านแค่รายการเดียว
if (isset($_POST['id'])) {
  $id = intval($_POST['id']);
  $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $id, $user_id);
  $stmt->execute();
  echo json_encode(['success' => true]);
  exit;
}

// ✅ ถ้ามี job_id หมายถึงอ่านทั้งหมดในงานนั้น
if (isset($_POST['job_id'])) {
  $job_id = intval($_POST['job_id']);
  $sql = "UPDATE notifications SET is_read = 1 WHERE job_id = ? AND user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $job_id, $user_id);
  $stmt->execute();
  echo json_encode(['success' => true]);
  exit;
}

echo json_encode(['success' => false]);
