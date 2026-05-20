<?php
session_start();
include "db_server.php";

$company_id = $_SESSION['user_id'] ?? 0;
$user_id = intval($_POST['user_id'] ?? 0);

if (!$company_id || !$user_id) {
    exit(json_encode(["status" => "error", "msg" => "ข้อมูลไม่ครบ"]));
}

// ✅ กันกดซ้ำ (เช็คว่าบันทึกไปแล้วหรือยัง)
$check = mysqli_query($conn, "SELECT * FROM interested_users WHERE hr_id=$company_id AND user_id=$user_id LIMIT 1");
if (mysqli_num_rows($check) > 0) {
    exit(json_encode(["status" => "exists", "msg" => "คุณเคยบันทึกผู้หางานนี้แล้ว"]));
}

// ✅ บันทึกลงตาราง interested_users
$sql = "INSERT INTO interested_users (hr_id, user_id) VALUES ($company_id, $user_id)";
mysqli_query($conn, $sql);

// ✅ ดึงชื่อบริษัท/HR
$hr = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM user WHERE user_id=$company_id"));
$hr_name = $hr['username'] ?? "บริษัทไม่ระบุ";

// ✅ สร้างข้อความแจ้งเตือน
$message = "บริษัท $hr_name สนใจในตัวคุณ ✅ โปรดตรวจสอบข้อมูลและตอบกลับได้";

// ✅ เพิ่มเข้า notifications (job_id = 0 เพราะไม่ใช่การสมัครงาน)
$notif_sql = "INSERT INTO notifications (user_id, message, job_id, is_read) 
              VALUES ($user_id, '$message', 0, 0)";
mysqli_query($conn, $notif_sql);

echo json_encode(["status" => "success"]);
