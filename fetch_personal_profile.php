<?php
session_start();
include('db_server.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$sql = "SELECT * FROM user_profile_details WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 'empty'], JSON_UNESCAPED_UNICODE);
    exit;
}

$row = mysqli_fetch_assoc($result);

echo json_encode([
    'fullname' => $row['fullname'] ?? '',
    'email' => $row['email'] ?? '',
    'phone' => $row['phone'] ?? '',
    'address' => $row['address'] ?? '',
    'gender' => $row['gender'] ?? '',
    'age' => $row['age'] ?? '',
    'desired_jobs' => json_decode($row['desired_jobs'] ?? '[]', true),
    'education' => json_decode($row['education'] ?? '{"school":[],"degree":[],"year":[]}', true),
    'work_experience' => json_decode($row['work_experience'] ?? '{"company":[],"position":[],"year":[]}', true),
    'certificates' => json_decode($row['certificates'] ?? '[]', true),
    'languages' => json_decode($row['languages'] ?? '[]', true),
    'skills' => json_decode($row['skills'] ?? '[]', true),
    'ref_contacts' => json_decode($row['ref_contacts'] ?? '{"name":[],"contact":[]}', true),
    'resume_file' => $row['resume_file'] ?? ''
], JSON_UNESCAPED_UNICODE);
?>
