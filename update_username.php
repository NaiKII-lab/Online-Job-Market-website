<?php
session_start();
include('db_server.php');

// ✅ ล้าง output buffer เพื่อกันข้อความแทรก


// ✅ ตั้ง header เป็น JSON

while (ob_get_level()) ob_end_clean(); 
header('Content-Type: application/json; charset=utf-8');

// ✅ ปิด error display (ไม่ให้ warning ปน)
ini_set('display_errors', 0);
error_reporting(0);

// ✅ ตรวจ session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'unauthorized']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$new_username = trim($_POST['username'] ?? '');

// ✅ ตรวจค่าว่าง
if ($new_username === '') {
    echo json_encode(['status' => 'error', 'msg' => 'กรุณากรอกชื่อผู้ใช้']);
    exit;
}

// ✅ ตรวจการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo json_encode(['status' => 'error', 'msg' => 'เชื่อมต่อฐานข้อมูลไม่ได้']);
    exit;
}

// ✅ อัปเดตชื่อผู้ใช้
$stmt = $conn->prepare("UPDATE user SET username = ? WHERE user_id = ?");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'msg' => 'เตรียมคำสั่ง SQL ไม่สำเร็จ']);
    exit;
}

$stmt->bind_param("si", $new_username, $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'username' => $new_username]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'อัปเดตไม่สำเร็จ']);
}

$stmt->close();
$conn->close();
exit;
?>
