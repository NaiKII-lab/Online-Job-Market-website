<?php
session_start();
include "db_server.php";

$company_id = $_SESSION['user_id'] ?? 0;
$sql = "SELECT id_jobs, job_title FROM jobs WHERE user_id = $company_id ORDER BY created_at DESC";
$res = mysqli_query($conn, $sql);

if (!$res || mysqli_num_rows($res) == 0) {
    exit("<p class='text-muted text-center'>❌ ยังไม่มีงานที่เปิดรับ</p>");
}

while($row = mysqli_fetch_assoc($res)){
    echo "
    <div class='form-check text-start'>
      <input class='form-check-input invite-job-radio' type='radio' name='jobSelect' value='{$row['id_jobs']}'>
      <label class='form-check-label fw-bold'>{$row['job_title']}</label>
    </div>";
}
