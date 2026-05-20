<?php
session_start();
include('db_server.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = mysqli_real_escape_string($conn, $_POST['username']); // อาจเป็น username หรือ email
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role'] ?? 'user'); // role เริ่มต้นเป็น user

    // 🔍 ค้นหาจาก username หรือ email
    $query = "
        SELECT * FROM user 
        WHERE (username = '$username' OR email = '$username') 
        AND role = '$role' 
        LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // ✅ ตรวจสอบรหัสผ่าน
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id']  = $row['user_id'];
            $_SESSION['role']     = $row['role'];

            echo json_encode(["status" => "success", "role" => $row['role']]);
        } else {
            echo json_encode(["status" => "error", "message" => "❌ รหัสผ่านไม่ถูกต้อง"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "❌ ไม่พบบัญชีนี้"]);
    }
}
?>
