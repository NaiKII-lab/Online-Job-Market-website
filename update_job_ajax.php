<?php
session_start();
include('db_server.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') exit('unauthorized');

$company_id = $_SESSION['user_id'];
$id = intval($_POST['id_jobs']);
$title = mysqli_real_escape_string($conn, $_POST['job_title']);
$company = mysqli_real_escape_string($conn, $_POST['company_name']);
$business = mysqli_real_escape_string($conn, $_POST['business_type']);
$location = mysqli_real_escape_string($conn, $_POST['location']);
$salary = floatval($_POST['salary']);
$desc = mysqli_real_escape_string($conn, $_POST['job_description']);
// ✅ รวม Array เป็น JSON เก็บลงฐานข้อมูล
$qualData = [];
if (!empty($_POST['qual_text'])) {
    foreach ($_POST['qual_text'] as $i => $text) {
        $text = trim($text);
        if ($text !== '') {
            $weight = isset($_POST['qual_weight'][$i]) ? intval($_POST['qual_weight'][$i]) : 1;

            $qualData[] = [
                "text" => $text,
                "weight" => $weight
            ];
        }
    }
}
$qual = mysqli_real_escape_string($conn, json_encode($qualData, JSON_UNESCAPED_UNICODE));


// ✅ สวัสดิการ
$benefitData = [];
if (!empty($_POST['benefits'])) {
    foreach ($_POST['benefits'] as $b) {
        $b = trim($b);
        if ($b !== '') $benefitData[] = $b;
    }
}
$benefits = mysqli_real_escape_string($conn, json_encode($benefitData, JSON_UNESCAPED_UNICODE));


$updateLogo = '';
if (!empty($_FILES['logo']['name'])) {
    $fileTmp = $_FILES['logo']['tmp_name'];
    $fileName = time() . "_" . basename($_FILES['logo']['name']);
    $uploadDir = "uploads/logos/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    move_uploaded_file($fileTmp, $uploadDir . $fileName);
    $logoPath = $uploadDir . $fileName;
    $updateLogo = ", logo = '$logoPath'";
}

$sql = "UPDATE jobs SET 
          job_title='$title',
          company_name='$company',
          business_type='$business',
          location='$location',
          salary='$salary',
          job_description='$desc',
          qualifications='$qual',
          benefits='$benefits'
          $updateLogo
        WHERE id_jobs=$id AND user_id=$company_id";


echo mysqli_query($conn, $sql) ? 'success' : 'error';
?>
