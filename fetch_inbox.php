<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('db_server.php');

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
  echo '<div class="text-danger text-center p-3">❌ ไม่พบรหัสผู้ใช้ (Session หาย)</div>';
  exit;
}

$sql = "SELECT * FROM message WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 20";

$result = mysqli_query($conn, $sql);

if (!$result) {
  echo '<div class="text-danger text-center p-3">❌ Query ล้มเหลว: ' . mysqli_error($conn) . '</div>';
  exit;
}

if (mysqli_num_rows($result) == 0) {
  echo '<div class="text-center text-muted py-4">📭 ไม่มีข้อความเข้า</div>';
  exit;
}

while ($row = mysqli_fetch_assoc($result)) {
  $statusIcon = $row['is_read'] ? '✅' : '🟡';
  echo '<div class="border-bottom py-2">';
  echo "<div class='d-flex justify-content-between align-items-center'>";
  echo "<div><strong>{$statusIcon}</strong> " . htmlspecialchars($row['message']) . "</div>";
  echo "<div class='text-muted small'>" . htmlspecialchars($row['created_at']) . "</div>";
  echo "</div>";
  echo "</div>";
}

// ✅ mark as read
mysqli_query($conn, "UPDATE message SET is_read = 1 WHERE user_id = $user_id");
?>
