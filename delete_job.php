<?php
session_start();
include('db_server.php');

$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$job_id = intval($_POST['id'] ?? 0);

if ($role !== 'company' || !$user_id || !$job_id) {
  echo "unauthorized";
  exit;
}

$sql = "DELETE FROM jobs WHERE id_jobs = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $job_id, $user_id);

if ($stmt->execute()) echo "success";
else echo "error";
?>
