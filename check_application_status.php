<?php
session_start();
include 'db_server.php';

$job_id = intval($_GET['job_id']);
$user_id = $_SESSION['user_id'] ?? 0;

$response = ['applied' => false, 'status' => ''];

if ($user_id > 0) {
    $sql = "SELECT status FROM job_applications WHERE job_id=$job_id AND user_id=$user_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $response['applied'] = true;
        $response['status'] = $row['status'];
    }
}

echo json_encode($response);
?>
