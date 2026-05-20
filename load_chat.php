<?php
session_start();
include 'db_server.php';

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// สมมุติว่าดึงข้อความทั้งหมดที่เกี่ยวข้องกับ HR หรือ user นี้
$sql = "SELECT * FROM chat_messages 
        WHERE sender_id = $user_id OR receiver_id = $user_id
        ORDER BY created_at ASC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $align = ($row['sender_id'] == $user_id) ? 'text-end' : 'text-start';
    echo "<div class='$align'><div class='p-2 m-1 border rounded bg-light'>{$row['message']}<br><small class='text-muted'>{$row['created_at']}</small></div></div>";
  }
} else {
  echo "<div class='text-center text-muted'>ยังไม่มีข้อความ</div>";
}
?>
