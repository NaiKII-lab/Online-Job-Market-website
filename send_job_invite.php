<?php
session_start();
include "db_server.php";

$hr_id = $_SESSION['user_id'] ?? 0;
$user_id = intval($_POST['user_id'] ?? 0);
$job_id = intval($_POST['job_id'] ?? 0);

if (!$hr_id || !$user_id || !$job_id) {
    exit(json_encode(["status" => "error", "msg" => "ข้อมูลไม่ครบ"]));
}

// ✅ ดึงชื่องาน + บริษัท
$job = mysqli_fetch_assoc(mysqli_query($conn, "SELECT job_title, company_name FROM jobs WHERE id_jobs=$job_id"));
$job_title = $job['job_title'];
$company = $job['company_name'];

// ✅ ข้อความแจ้งเตือน
$message = "บริษัท $company เชิญคุณสมัครงาน: $job_title";

// ✅ บันทึกไปยัง notifications
$sql = "INSERT INTO notifications (user_id, job_id, message, is_read) 
        VALUES ($user_id, $job_id, '$message', 0)";
mysqli_query($conn, $sql);

echo json_encode(["status" => "success"]);
