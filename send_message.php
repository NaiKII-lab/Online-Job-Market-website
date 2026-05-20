<?php
session_start();
include('db_server.php');

$sender_id = $_SESSION['user_id'] ?? 0;
$receiver_id = intval($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message'] ?? '');

if ($sender_id <= 0 || $receiver_id <= 0 || $message == '') {
    exit('❌ ข้อมูลไม่ครบ');
}

// บันทึกลงตาราง chat
$sql = "INSERT INTO chat (sender_id, receiver_id, message) VALUES ($sender_id, $receiver_id, '$message')";
$insertChat = mysqli_query($conn, $sql);

// ถ้าสำเร็จ ให้เพิ่มเข้า message ด้วย
if ($insertChat) {
    $msg = mysqli_real_escape_string($conn, $message);
    $sqlNotif = "INSERT INTO message (user_id, message) VALUES ($receiver_id, '📩 ข้อความใหม่จากผู้ใช้ #' . $sender_id . ': $msg')";
    mysqli_query($conn, $sqlNotif);

    echo '✅ success';
} else {
    echo '❌ error';
}
?>
