<?php
session_start();
include('db_server.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']); // 'user' หรือ 'company'
    $phones   = mysqli_real_escape_string($conn, $_POST['phones']);
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
    $role = 'user';

    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password !== $confirm_password){
        die("❌ รหัสผ่านไม่ตรงกัน กรุณากรอกใหม่");
    }
    // ✅ เข้ารหัส password ก่อนเก็บ
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ตรวจสอบว่ามี username หรือ email ซ้ำไหม
    $check = "SELECT * FROM user WHERE username='$username' OR email='$email'";
    $check_result = mysqli_query($conn, $check);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('❌ มีชื่อผู้ใช้หรืออีเมลนี้แล้ว'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO user (username, email, password, role, phones, contact_email) 
            VALUES ('$username', '$email', '$hashed_password', '$role', '$phones', '$contact_email')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ สมัครสมาชิกสำเร็จ!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('❌ เกิดข้อผิดพลาด: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
?>
