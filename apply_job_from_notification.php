<?php
session_start();
include "db_server.php";

header("Content-Type: application/json");

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    echo json_encode(["status" => "error", "msg" => "กรุณาเข้าสู่ระบบ"]);
    exit;
}

$user_id = $_SESSION["user_id"];
$job_id = intval($_POST["job_id"] ?? 0);

if ($job_id <= 0) {
    echo json_encode(["status" => "error", "msg" => "ไม่พบงานที่ต้องการสมัคร"]);
    exit;
}

// ✅ เช็คว่าผู้ใช้สมัครงานนี้ไปแล้วหรือยัง
$check = $conn->query("SELECT id FROM job_applications WHERE user_id = $user_id AND job_id = $job_id");

if ($check->num_rows > 0) {
    echo json_encode(["status" => "exists", "msg" => "คุณสมัครงานนี้แล้ว"]);
    exit;
}

// ✅ สมัครงานใหม่ (สถานะเริ่มต้น "รอพิจารณา")
$sql = "INSERT INTO job_applications (user_id, job_id, status, applied_at) 
        VALUES ($user_id, $job_id, 'รอพิจารณา', NOW())";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "msg" => $conn->error]);
}
?>
