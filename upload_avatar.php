<?php
session_start();
include('db_server.php');

// ป้องกันอะไรหลุดปนออกมา
ob_start();
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(0);

// ต้องล็อกอิน
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status' => 'error', 'msg' => 'unauthorized']); exit;
}

$user_id = (int)$_SESSION['user_id'];

if (empty($_FILES['avatar_file']['name'])) {
  echo json_encode(['status' => 'error', 'msg' => 'ไม่พบไฟล์รูปภาพ']); exit;
}

$targetDir = "uploads/avatars/";
if (!is_dir($targetDir)) { @mkdir($targetDir, 0777, true); }

$ext = pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION);
$ext = strtolower($ext ?: 'jpg');

// คุณอาจจะ whitelist นามสกุล
$allowed = ['jpg','jpeg','png','gif','webp'];
if (!in_array($ext, $allowed)) {
  echo json_encode(['status' => 'error', 'msg' => 'ชนิดไฟล์ไม่ถูกต้อง']); exit;
}

$fileName = "avatar_" . $user_id . "_" . time() . "." . $ext;
$targetFile = $targetDir . $fileName;

if (!move_uploaded_file($_FILES['avatar_file']['tmp_name'], $targetFile)) {
  echo json_encode(['status' => 'error', 'msg' => 'ไม่สามารถอัปโหลดรูปได้']); exit;
}

// บันทึกลง DB
$stmt = $conn->prepare("UPDATE user SET avatar=? WHERE user_id=?");
$stmt->bind_param("si", $targetFile, $user_id);
$stmt->execute();

// ล้างบัฟเฟอร์กันเศษ output
ob_clean();
echo json_encode([
  'status' => 'success',
  'avatar' => $targetFile
]);
exit;
