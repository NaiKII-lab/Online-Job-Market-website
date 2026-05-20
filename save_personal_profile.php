<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('db_server.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'unauthorized'], JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$address = $_POST['address'] ?? '';
$age = ($_POST['age'] !== '') ? intval($_POST['age']) : null;
$gender = $_POST['gender'] ?? '';

$desired_jobs = json_encode($_POST['desired_jobs'] ?? [], JSON_UNESCAPED_UNICODE);
$education = json_encode([
    'school' => $_POST['education_school'] ?? [],
    'degree' => $_POST['education_degree'] ?? [],
    'year' => $_POST['education_year'] ?? []
], JSON_UNESCAPED_UNICODE);

$work = json_encode([
    'company' => $_POST['work_company'] ?? [],
    'position' => $_POST['work_position'] ?? [],
    'year' => $_POST['work_year'] ?? []
], JSON_UNESCAPED_UNICODE);

$certificates = json_encode($_POST['certificates'] ?? [], JSON_UNESCAPED_UNICODE);
$languages = json_encode($_POST['languages'] ?? [], JSON_UNESCAPED_UNICODE);
$skills = json_encode($_POST['skills'] ?? [], JSON_UNESCAPED_UNICODE);
$references = json_encode([
    'name' => $_POST['reference_name'] ?? [],
    'contact' => $_POST['reference_contact'] ?? []
], JSON_UNESCAPED_UNICODE);

// ✅ resume file
$resume_path = null;
if (!empty($_FILES['resume_file']['name'])) {
    $dir = "uploads/resumes/";
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $filename = uniqid("resume_") . "_" . basename($_FILES["resume_file"]["name"]);
    $target = $dir . $filename;
    if (move_uploaded_file($_FILES["resume_file"]["tmp_name"], $target)) {
        $resume_path = $target;
    }
} else {
    $old = mysqli_query($conn, "SELECT resume_file FROM user_profile_details WHERE user_id = $user_id LIMIT 1");
    $resume_path = ($old && mysqli_num_rows($old) > 0) ? mysqli_fetch_assoc($old)['resume_file'] : null;
}

$exists = mysqli_query($conn, "SELECT id FROM user_profile_details WHERE user_id = $user_id LIMIT 1");
$hasRow = ($exists && mysqli_num_rows($exists) > 0);

if ($hasRow) {
    // ✅ UPDATE (15 fields)
    $sql = "UPDATE user_profile_details 
        SET fullname=?, email=?, phone=?, address=?, desired_jobs=?, age=?, gender=?, education=?, 
            work_experience=?, certificates=?, languages=?, skills=?, ref_contacts=?, resume_file=?, updated_at=NOW()
        WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssissssssssi",
        $fullname, $email, $phone, $address, $desired_jobs, $age, $gender,
        $education, $work, $certificates, $languages, $skills, $references, $resume_path, $user_id
    );
} else {
    // ✅ INSERT (15 fields)
    $sql = "INSERT INTO user_profile_details 
      (user_id, fullname, email, phone, address, desired_jobs, age, gender, education, work_experience,
       certificates, languages, skills, ref_contacts, resume_file, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssissssssss",
        $user_id, $fullname, $email, $phone, $address, $desired_jobs, $age, $gender,
        $education, $work, $certificates, $languages, $skills, $references, $resume_path
    );
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'msg' => '✅ บันทึกข้อมูลสำเร็จ'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status' => 'error', 'msg' => '❌ '.$stmt->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>
