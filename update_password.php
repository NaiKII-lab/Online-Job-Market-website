<?php
session_start();
include('db_server.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'msg' => 'unauthorized']);
  exit;
}

$user_id = $_SESSION['user_id'];
$old = $_POST['old_password'] ?? '';
$new = $_POST['new_password'] ?? '';

if ($old === '' || $new === '') {
  echo json_encode(['status' => 'error', 'msg' => 'ข้อมูลไม่ครบ']);
  exit;
}

// ✅ ตรวจสอบรหัสผ่านเดิม
$sql = "SELECT password FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($old, $user['password'])) {
  echo json_encode(['status' => 'error', 'msg' => 'รหัสผ่านเดิมไม่ถูกต้อง']);
  exit;
}

$stmt->close();

// ✅ เข้ารหัสและบันทึกรหัสผ่านใหม่
$new_hashed = password_hash($new, PASSWORD_DEFAULT);
$sql = "UPDATE user SET password = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_hashed, $user_id);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'msg' => 'เปลี่ยนรหัสผ่านสำเร็จ']);
} else {
  echo json_encode(['status' => 'error', 'msg' => 'อัปเดตไม่สำเร็จ']);
}

$stmt->close();
$conn->close();
?>
