<?php
session_start();
include('db_server.php');

$user_id = $_SESSION['user_id'] ?? 0;
$receiver_id = intval($_GET['receiver_id'] ?? 0);
$job_id = intval($_GET['job_id'] ?? 0);

if (!$user_id || !$receiver_id || !$job_id) exit('ข้อมูลไม่ครบ');

// ✅ mark ข้อความของอีกฝั่งว่าอ่านแล้ว
$update = "
  UPDATE chat 
  SET is_read = 1 
  WHERE receiver_id = $user_id AND sender_id = $receiver_id AND job_id = $job_id
";
mysqli_query($conn, $update);

// ✅ ดึงข้อความทั้งหมด
$sql = "
  SELECT c.*, u.username 
  FROM chat c
  JOIN user u ON c.sender_id = u.user_id
  WHERE c.job_id = $job_id 
    AND ((c.sender_id = $user_id AND c.receiver_id = $receiver_id)
      OR (c.sender_id = $receiver_id AND c.receiver_id = $user_id))
  ORDER BY c.created_at ASC
";
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {
  $isMine = ($row['sender_id'] == $user_id);
  $align = $isMine ? 'text-end' : 'text-start';
  $bubble = $isMine ? 'bg-primary text-white' : 'bg-light border';

  echo "<div class='my-2 $align'>";
  echo "  <div class='d-inline-block px-3 py-2 rounded-3 $bubble'>";
  echo htmlspecialchars($row['message']);
  echo "  </div>";

  // ✅ แสดงเวลาเฉพาะข้อความของอีกฝั่ง หรือถ้าเป็นของเรายังไม่อ่าน
  if (!$isMine || !$row['is_read']) {
    echo "<div class='small text-muted'>" . date('H:i', strtotime($row['created_at'])) . "</div>";
  }

  // ✅ ถ้าอีกฝั่งอ่านแล้ว (แสดงแค่บรรทัดเดียว)
  if ($isMine && $row['is_read']) {
    echo "<div class='small text-muted mt-1'>อ่านแล้ว " . date('H:i', strtotime($row['created_at'])) . "</div>";
  }

  echo "</div>";
}
?>
