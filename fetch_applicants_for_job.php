<?php
session_start();
include 'db_server.php';

$job_id = intval($_GET['job_id']);
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

if(!$job_id || $role != 'company') exit('ข้อมูลไม่ถูกต้อง');

$html = "<div class='p-2'><button id='backToJobList' class='btn btn-outline-secondary btn-sm mb-2'>⬅️ ย้อนกลับ</button></div>";

$sql = "SELECT a.user_id, u.username, u.avatar
        FROM job_applications a
        JOIN user u ON u.user_id = a.user_id
        WHERE a.job_id = ?
        ORDER BY a.applied_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    echo $html."<div class='p-3 text-center text-muted'>ยังไม่มีผู้สมัครงานนี้</div>";
    exit;
}

while($row = $res->fetch_assoc()){
    $avatar = $row['avatar'] ?: 'default_avatar.png';
    $html .= "
      <button class='btn btn-outline-success w-100 mb-1 text-start select-chat-target' 
              data-jobid='{$job_id}' data-receiverid='{$row['user_id']}'>
        <img src='{$avatar}' width='30' height='30' class='rounded-circle me-2'>
        แชทกับผู้สมัคร: {$row['username']}
      </button>
    ";
}

echo $html;
?>
