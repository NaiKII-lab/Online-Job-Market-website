<?php
include('db_server.php');
header('Content-Type: application/json');

$username = trim($_POST['username']);
$password = trim($_POST['password']);
$email = trim($_POST['email']);
$phones = trim($_POST['phones']);
$role = $_POST['role'] ?? 'user';
$company_name = $_POST['company_name'] ?? null;
$business_type = $_POST['business_type'] ?? null;

if (empty($username) || empty($password) || empty($email)) {
  echo json_encode(["status" => "error", "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
  exit;
}

// 🔍 ตรวจว่ามีผู้ใช้นี้แล้วหรือยัง
$check = mysqli_query($conn, "SELECT user_id FROM user WHERE username='$username' OR email='$email'");
if (mysqli_num_rows($check) > 0) {
  echo json_encode(["status" => "error", "message" => "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว"]);
  exit;
}

// ✅ เข้ารหัสรหัสผ่าน
$hashed = password_hash($password, PASSWORD_DEFAULT);

// 🏢 เพิ่มข้อมูลลงฐานข้อมูล
$sql = "INSERT INTO user (username, password, email, phones, role, company_name, business_type)
        VALUES ('$username', '$hashed', '$email', '$phones', '$role', '$company_name', '$business_type')";
        
if (mysqli_query($conn, $sql)) {
  echo json_encode(["status" => "success"]);
} else {
  echo json_encode(["status" => "error", "message" => "ไม่สามารถสมัครสมาชิกได้"]);
}
?>
