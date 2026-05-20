<?php
session_start();
include('db_server.php');

// ต้องเป็นบริษัทเท่านั้น
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

$company_id = $_SESSION['user_id'];
$id = intval($_GET['id']);

$sql = "SELECT * FROM jobs WHERE id_jobs = $id AND user_id = $company_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row); // ✅ ส่งกลับแบบ JSON
} else {
    echo json_encode(['error' => 'not_found']);
}
?>
