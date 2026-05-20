<?php
session_start();
include 'db_server.php';

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

if(!$user_id) exit('ไม่พบผู้ใช้');

$html = '';

if($role == 'user'){
    // 🔹 ผู้ใช้ทั่วไป: แสดงงานที่สมัครแล้ว
    $sql = "SELECT j.id_jobs, j.job_title, j.user_id as hr_id, u.username as hr_name
            FROM job_applications a
            JOIN jobs j ON j.id_jobs = a.job_id
            JOIN user u ON u.user_id = j.user_id
            WHERE a.user_id=? ORDER BY a.applied_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while($row = $res->fetch_assoc()){
        $html .= '<button class="select-chat-target btn btn-outline-info w-100 mb-1" 
                     data-jobid="'.$row['id_jobs'].'" data-receiverid="'.$row['hr_id'].'">
                    💼 '.$row['job_title'].' <br><small>HR: '.$row['hr_name'].'</small>
                  </button>';
    }
}

elseif($role == 'company'){
    // 🔹 ฝั่งบริษัท: แสดงงานที่บริษัทลงไว้ + จำนวนผู้สมัคร
    $sql = "SELECT j.id_jobs, j.job_title, 
                   COUNT(a.id) AS applicant_count
            FROM jobs j
            LEFT JOIN job_applications a ON j.id_jobs = a.job_id
            WHERE j.user_id=?
            GROUP BY j.id_jobs
            ORDER BY j.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){
        $html .= "<div class='p-2 border-bottom'><strong>📋 เลือกงานของคุณ</strong></div>";
        while($row = $res->fetch_assoc()){
            $count = $row['applicant_count'];
            $badge = $count > 0 
                ? "<span class='badge bg-success ms-2'>$count ผู้สมัคร</span>" 
                : "<span class='badge bg-secondary ms-2'>0</span>";

            $html .= '<button class="btn btn-outline-primary w-100 mb-1 text-start select-job-for-applicants"
                         data-jobid="'.$row['id_jobs'].'">
                         💼 '.$row['job_title'].' '.$badge.'
                       </button>';
        }
    } else {
        $html = "<div class='text-center text-muted p-3'>❌ ยังไม่มีงานที่คุณลงไว้</div>";
    }
}

echo $html;
?>
