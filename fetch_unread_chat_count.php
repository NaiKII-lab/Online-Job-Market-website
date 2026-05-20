<?php
session_start();
include 'db_server.php';

$user_id = $_SESSION['user_id'] ?? 0;
if(!$user_id) exit(json_encode(['count'=>0]));

$sql = "SELECT COUNT(*) AS cnt FROM chat WHERE receiver_id=? AND is_read=0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
echo json_encode(['count'=>intval($row['cnt'])]); 
?>
