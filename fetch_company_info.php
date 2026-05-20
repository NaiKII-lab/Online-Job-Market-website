<?php
session_start();
include('db_server.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT company_name, business_type FROM user WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);
echo json_encode($data);
?>
