<?php
session_start();
include('db_server.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') {
    exit(json_encode(['status' => 'error', 'msg' => 'Unauthorized']));
}

$company_id = $_SESSION['user_id'];

$company_name   = mysqli_real_escape_string($conn, $_POST['company_name']);
$business_type  = mysqli_real_escape_string($conn, $_POST['business_type']);
$job_title      = mysqli_real_escape_string($conn, $_POST['job_title']);
$description    = mysqli_real_escape_string($conn, $_POST['job_description']);
$salary         = intval($_POST['salary']);
$location       = mysqli_real_escape_string($conn, $_POST['location']);

/* ✅ บันทึกคุณสมบัติเป็น JSON */
$qual_json = [];
if (!empty($_POST['qual_text']) && is_array($_POST['qual_text'])) {
    foreach ($_POST['qual_text'] as $i => $text) {
        $text = trim($text);
        if ($text !== '') {
            $weight = $_POST['qual_weight'][$i] ?? 1;
            $qual_json[] = [
                'text' => $text,
                'weight' => intval($weight)
            ];
        }
    }
}
$qualifications = mysqli_real_escape_string($conn, json_encode($qual_json, JSON_UNESCAPED_UNICODE));


/* ✅ บันทึกสวัสดิการเป็น JSON */
$benef_json = [];
if (!empty($_POST['benefits']) && is_array($_POST['benefits'])) {
    foreach ($_POST['benefits'] as $b) {
        $b = trim($b);
        if ($b !== '') $benef_json[] = $b;
    }
}
$benefits = mysqli_real_escape_string($conn, json_encode($benef_json, JSON_UNESCAPED_UNICODE));

/* ✅ INSERT */
$sql = "
INSERT INTO jobs (
    user_id, company_name, business_type, job_title,
    qualifications, benefits, job_description, salary, location, created_at
) VALUES (
    $company_id, '$company_name', '$business_type', '$job_title',
    '$qualifications', '$benefits', '$description', $salary, '$location', NOW()
)";

if (mysqli_query($conn, $sql)) {
    echo json_encode(['status' => 'success', 'msg' => 'เพิ่มงานสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'msg' => mysqli_error($conn)]);
}
?>
