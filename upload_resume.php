<?php
session_start();
include('db_server.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') exit;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $targetDir = "uploads/resumes/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = "resume_" . $user_id . ".pdf";
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['resume']['tmp_name'], $targetFile)) {
            $sql = "UPDATE user SET resume_file='$targetFile' WHERE user_id=$user_id";
            mysqli_query($conn, $sql);
            $_SESSION['success'] = "อัปโหลดเรซูเม่เรียบร้อยแล้ว!";
            header("Location: jobs_user.php");
            exit;
        } else {
            echo "❌ อัปโหลดไฟล์ไม่สำเร็จ";
        }
    }
}
?>
