<?php
session_start();
include('db_server.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company') {
  echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
  exit;
}

$app_id = intval($_POST['app_id'] ?? 0);
$new_status = trim($_POST['status'] ?? '');

if (!$app_id || !$new_status) {
  echo json_encode(['status' => 'error', 'msg' => 'ข้อมูลไม่ครบ']);
  exit;
}

// ✅ อัปเดตสถานะใน job_applications
$sql = "UPDATE job_applications SET status=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $app_id);
$stmt->execute();

// ✅ ดึงข้อมูลผู้สมัครและงาน
$q_user = "
  SELECT a.user_id, a.job_id, j.job_title 
  FROM job_applications a 
  JOIN jobs j ON a.job_id = j.id_jobs 
  WHERE a.id = ?
";
$stmt2 = $conn->prepare($q_user);
$stmt2->bind_param("i", $app_id);
$stmt2->execute();
$res = $stmt2->get_result();
$user = $res->fetch_assoc();

if ($user) {
  $user_id = $user['user_id'];
  $job_id = $user['job_id'];   // ✅ เพิ่มตรงนี้
  $job_title = $user['job_title'];

  // ✅ ข้อความแจ้งเตือน
  switch ($new_status) {
    case 'สนใจในตัวเขา':
      $message = "⭐ HR สนใจใบสมัครของคุณในตำแหน่ง \"$job_title\"";
      break;
    case 'ไม่รับ':
      $message = "❌ ใบสมัครของคุณในตำแหน่ง \"$job_title\" ไม่ได้รับการพิจารณา";
      break;
    case 'รับเข้าทำงาน':
      $message = "🎉 ขอแสดงความยินดี! คุณได้รับการรับเข้าทำงานในตำแหน่ง \"$job_title\"";
      break;
    default:
      $message = "📢 HR ได้เปลี่ยนสถานะใบสมัครของคุณในตำแหน่ง \"$job_title\" เป็น: $new_status";
  }

  // ✅ เพิ่มข้อความลงตาราง notifications พร้อม job_id
  $notif_sql = "INSERT INTO notifications (user_id, job_id, message, is_read, created_at)
                VALUES (?, ?, ?, 0, NOW())";
  $stmt3 = $conn->prepare($notif_sql);
  $stmt3->bind_param("iis", $user_id, $job_id, $message);
  $stmt3->execute();
}

echo json_encode(['status' => 'success', 'msg' => 'อัปเดตเรียบร้อย']);
