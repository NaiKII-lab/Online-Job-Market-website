<?php
session_start();
include 'db_server.php';

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

if(!$user_id) exit('<div class="text-danger">❌ ไม่พบผู้ใช้</div>');

$html = '<div class="p-2 border-bottom d-flex justify-content-between align-items-center">
  <strong>💬 เลือกเพื่อเริ่มแชท</strong>
</div>';

if ($role == 'user') {
    // ✅ ฝั่งผู้สมัคร: แสดงงานที่เคยสมัคร
    $sql = "SELECT j.id_jobs, j.job_title, j.user_id AS hr_id, u.username AS hr_name
            FROM job_applications a
            JOIN jobs j ON j.id_jobs = a.job_id
            JOIN user u ON u.user_id = j.user_id
            WHERE a.user_id = ?
            ORDER BY a.applied_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        $html .= "<div class='p-3 text-muted'>ยังไม่มีการสมัครงาน</div>";
    } else {
        while($row = $res->fetch_assoc()) {
            $html .= '<button class="select-chat-target btn btn-outline-info w-100 text-start mb-2" 
                         data-jobid="'.$row['id_jobs'].'" 
                         data-receiverid="'.$row['hr_id'].'">
                        💼 '.$row['job_title'].'<br>
                        <small>HR: '.$row['hr_name'].'</small>
                      </button>';
        }
    }

} elseif ($role == 'company') {
    // ✅ ฝั่งบริษัท: แสดงงานที่ตัวเองลง
    $sql = "SELECT id_jobs, job_title FROM jobs WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows == 0) {
        $html .= "<div class='p-3 text-muted'>ยังไม่มีประกาศงาน</div>";
    } else {
        while($job = $res->fetch_assoc()) {
            $html .= '
              <div class="border rounded p-2 mb-2">
                <div class="d-flex justify-content-between align-items-center">
                  <strong>'.$job['job_title'].'</strong>
                  <button class="btn btn-sm btn-outline-primary view-applicants" data-jobid="'.$job['id_jobs'].'">
                    👥 ดูผู้สมัคร
                  </button>
                </div>
              </div>';
        }
    }
}

// ✅ เพิ่มส่วนสคริปต์ (ให้เรียก fetch_applicants_in_job.php ตอนกด “ดูผู้สมัคร”)

echo $html;

// ✅ ถ้ามีการส่ง job_id มา = แสดงผู้สมัครของงานนั้น
if (isset($_GET["job"])) {
    $job_id = intval($_GET["job"]);
    $sql = "SELECT a.user_id, u.username
            FROM job_applications a
            JOIN user u ON u.user_id = a.user_id
            WHERE a.job_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $res = $stmt->get_result();

    echo '<div class="p-2 border-bottom d-flex justify-content-between align-items-center">
            <button id="backToJobList" class="btn btn-sm btn-outline-secondary">⬅️ ย้อนกลับ</button>
            <strong>ผู้สมัครในงานนี้</strong>
          </div>';

    if ($res->num_rows == 0) {
        echo "<div class='p-3 text-muted'>ยังไม่มีผู้สมัครในงานนี้</div>";
    } else {
        while($row = $res->fetch_assoc()){
            echo '<button class="select-chat-target btn btn-outline-success w-100 text-start mb-2"
                    data-jobid="'.$job_id.'" 
                    data-receiverid="'.$row['user_id'].'">
                    👤 '.$row['username'].'
                  </button>';
        }
    }
    exit;
}
?>
