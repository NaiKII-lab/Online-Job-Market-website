<?php
session_start();
include('db_server.php');

$user_id = $_SESSION['user_id'] ?? 0;
$job_id = intval($_POST['job_id'] ?? $_GET['job_id'] ?? 0); // ✅ รับได้ทั้ง GET และ POST

if (!$user_id || !$job_id) {
    echo json_encode(['status' => 'error', 'msg' => 'ข้อมูลไม่ครบ']);
    exit;
}

// ✅ ตรวจสอบว่ามีข้อมูลประวัติส่วนตัวหรือยัง
$check_profile = mysqli_query($conn, "SELECT 1 FROM user_profile_details WHERE user_id = $user_id LIMIT 1");
if (mysqli_num_rows($check_profile) === 0) {
    echo json_encode([
        'status' => 'no_profile',
        'msg' => 'กรุณากรอกประวัติส่วนตัวก่อนสมัครงาน'
    ]);
    exit;
}

// ✅ ตรวจสอบว่าสมัครไปแล้วหรือยัง
$check_applied = mysqli_query($conn, "SELECT id FROM job_applications WHERE user_id=$user_id AND job_id=$job_id");
if (mysqli_num_rows($check_applied) > 0) {
    echo json_encode(['status' => 'error', 'msg' => 'คุณได้สมัครงานนี้แล้ว']);
    exit;
}

// ✅ สมัครงาน
$sql = "INSERT INTO job_applications (job_id, user_id, status, applied_at)
        VALUES ($job_id, $user_id, 'รอการตอบกลับ', NOW())";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'msg' => 'สมัครงานสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'ไม่สามารถสมัครงานได้: ' . mysqli_error($conn)]);
}
?>
