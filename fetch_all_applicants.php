<?php
session_start();
include('db_server.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
  exit('<div class="alert alert-warning text-center">🚫 เข้าถึงได้เฉพาะบริษัทเท่านั้น</div>');
}

$company_id = $_SESSION['user_id'];
$keyword = mysqli_real_escape_string($conn, $_GET['keyword'] ?? '');
$job = mysqli_real_escape_string($conn, $_GET['job'] ?? '');

$where = "WHERE j.user_id = $company_id";
if ($keyword) $where .= " AND (u.username LIKE '%$keyword%' OR u.email LIKE '%$keyword%')";
if ($job) $where .= " AND (j.job_title LIKE '%$job%')";

$query = "
SELECT 
  a.id AS app_id,
  a.status,
  a.applied_at,
  u.username, u.email, u.avatar,
  j.job_title
FROM job_applications a
JOIN user u ON a.user_id = u.user_id
JOIN jobs j ON a.job_id = j.id_jobs
$where
ORDER BY a.applied_at DESC
";

$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
  exit('<div class="text-center text-muted py-3">ไม่มีผู้สมัครในขณะนี้</div>');
}

while ($row = mysqli_fetch_assoc($result)) {
  $avatar = $row['avatar'] ?: 'default_avatar.png';
  $statusColor = match($row['status']) {
    'ไม่รับ' => 'danger',
    'สนใจในตัวเขา' => 'warning text-dark',
    'รับเข้าทำงาน' => 'success',
    default => 'info text-dark'
  };

  echo '
  <div class="d-flex align-items-center border rounded p-3 mb-2 bg-white shadow-sm">
    <img src="'.htmlspecialchars($avatar).'" class="rounded-circle me-3" width="60" height="60" style="object-fit:cover;">
    <div class="flex-grow-1">
      <h6 class="text-primary mb-1">'.htmlspecialchars($row['username']).'</h6>
      <div class="text-muted small">📧 '.htmlspecialchars($row['email']).' | 💼 '.htmlspecialchars($row['job_title']).'</div>
      <div class="mt-1"><span class="badge bg-'.$statusColor.'">📌 '.htmlspecialchars($row['status']).'</span></div>
    </div>
    <button class="btn btn-outline-primary btn-sm view-applicant-detail" data-appid="'.$row['app_id'].'">
      🔍 ดูรายละเอียด
    </button>
  </div>';
}
?>
