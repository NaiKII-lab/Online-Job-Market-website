<?php
session_start();
include 'db_server.php';

$user_id = $_SESSION['user_id'] ?? 0;
$job_id = intval($_POST['job_id'] ?? 0);
$receiver_id = intval($_POST['receiver_id'] ?? 0); // ✅ เพิ่ม
$message = trim($_POST['message'] ?? '');

header('Content-Type: application/json');

if(!$user_id || !$job_id || !$receiver_id || !$message){
    echo json_encode(['status'=>'error','msg'=>'ข้อมูลไม่ครบ']);
    exit;
}

// ✅ บันทึกข้อความ (แบบ 1–1)
$stmt = $conn->prepare("INSERT INTO chat (sender_id, receiver_id, job_id, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiis", $user_id, $receiver_id, $job_id, $message);

if($stmt->execute()){
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'error','msg'=>$stmt->error]);
}
?>
