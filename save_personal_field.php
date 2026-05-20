<?php
session_start();
include('db_server.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$field = $_POST['field'] ?? '';
$value = $_POST['value'] ?? '';

if (empty($field)) {
    echo json_encode(['status' => 'error', 'msg' => 'no field']);
    exit;
}

// ✅ decode ถ้า value ถูกส่งมาเป็น JSON string
$decoded_value = json_decode($value, true);
if (json_last_error() === JSON_ERROR_NONE) {
    $value = $decoded_value;
}

// 🧠 กำหนดค่าวิธีอัปเดต
switch ($field) {
    case 'fullname':
    case 'email':
    case 'phone':
    case 'address':
    case 'age': // 🆕 เพิ่มฟิลด์อายุ
    case 'gender':
        $stmt = $conn->prepare("UPDATE user_profile_details SET {$field}=? WHERE user_id=?");
        $stmt->bind_param("si", $value, $user_id);
        break;

    case 'desired_jobs':
    case 'certificates':
    case 'languages':
    case 'skills':
        $jsonValue = json_encode($value, JSON_UNESCAPED_UNICODE);
        $stmt = $conn->prepare("UPDATE user_profile_details SET {$field}=? WHERE user_id=?");
        $stmt->bind_param("si", $jsonValue, $user_id);
        break;

    case 'education':
    case 'work_experience':
    case 'ref_contacts':
        $jsonValue = json_encode($value, JSON_UNESCAPED_UNICODE);
        $stmt = $conn->prepare("UPDATE user_profile_details SET {$field}=? WHERE user_id=?");
        $stmt->bind_param("si", $jsonValue, $user_id);
        break;

    default:
        echo json_encode(['status' => 'error', 'msg' => 'unknown field']);
        exit;
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'msg' => 'บันทึกข้อมูลสำเร็จ']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'update failed']);
}

$stmt->close();
$conn->close();
?>
