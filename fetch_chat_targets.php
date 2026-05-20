<?php
session_start();
include 'db_server.php';

$job_id = intval($_GET['job_id']);
$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

if(!$job_id || !$user_id) exit('ไม่พบข้อมูล');

$html = "<div class='p-2'><button id='backToJobList' class='btn btn-outline-secondary btn-sm mb-2'>⬅️ ย้อนกลับ</button></div>";

if($role == 'user'){
    // ผู้ใช้เลือก HR ของงานนี้
    $sql = "SELECT u.user_id, u.username, u.avatar
            FROM jobs j 
            JOIN user u ON j.user_id = u.user_id
            WHERE j.id_jobs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while($row = $res->fetch_assoc()){
        $avatar = $row['avatar'] ?: 'default_avatar.png';
        $html .= "
          <button class='btn btn-outline-info w-100 mb-1 text-start select-person' 
                  data-receiverid='{$row['user_id']}'>
            <img src='{$avatar}' width='30' height='30' class='rounded-circle me-2'>
            แชทกับ HR: {$row['username']}
          </button>
        ";
    }
}

elseif($role == 'company'){
    // HR ดูรายชื่อผู้สมัครของงานนี้
    $sql = "SELECT u.user_id, u.username, u.avatar
            FROM job_applications a
            JOIN user u ON u.user_id = a.user_id
            WHERE a.job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while($row = $res->fetch_assoc()){
        $avatar = $row['avatar'] ?: 'default_avatar.png';
        $html .= "
          <button class='btn btn-outline-success w-100 mb-1 text-start select-person' 
                  data-receiverid='{$row['user_id']}'>
            <img src='{$avatar}' width='30' height='30' class='rounded-circle me-2'>
            แชทกับผู้สมัคร: {$row['username']}
          </button>
        ";
    }
}

echo $html ?: "<div class='text-center text-muted p-3'>ไม่มีข้อมูลผู้ติดต่อ</div>";
?>
