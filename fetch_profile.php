<?php
session_start();
include('db_server.php');
header('Content-Type: application/json; charset=utf-8');



// ✅ ตรวจ session
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'msg' => 'unauthorized']);
  exit;
}

$user_id = $_SESSION['user_id'];

// ✅ ดึงข้อมูลโปรไฟล์จากตาราง user
$sql = "SELECT username, avatar FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  $avatar = !empty($row['avatar']) ? $row['avatar'] : 'uploads/avatars/default.png';
  echo json_encode([
    'status' => 'success',
    'username' => $row['username'],
    'avatar' => $avatar
  ]);
} else {
  echo json_encode(['status' => 'error', 'msg' => 'ไม่พบข้อมูลผู้ใช้']);
}
?>
