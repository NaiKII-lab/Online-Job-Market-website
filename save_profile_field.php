<?php
session_start();
include('db_server.php');

$user_id = intval($_POST['user_id'] ?? 0);
$response = ['status'=>'error'];

if($user_id > 0){
    $folder_avatar = 'uploads/avatar/';
    $folder_resume = 'uploads/resume/';
    if(!is_dir($folder_avatar)) mkdir($folder_avatar, 0777, true);
    if(!is_dir($folder_resume)) mkdir($folder_resume, 0777, true);

    // ตัวแปรเก็บ path รูป/เรซูเม่ล่าสุด
    $newAvatarPath = '';
    $newResumePath = '';

    // ✅ อัปโหลด avatar
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error']==0){
        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $fileName = $folder_avatar . 'avatar_'.$user_id.'_'.time().'.'.$ext;
        if(move_uploaded_file($_FILES['avatar']['tmp_name'], $fileName)){
            $sql = "UPDATE user SET avatar='$fileName' WHERE user_id=$user_id";
            if(mysqli_query($conn, $sql)){
                $newAvatarPath = $fileName;
            }
        }
    }

    // ✅ อัปโหลด resume
    if(isset($_FILES['resume_file']) && $_FILES['resume_file']['error']==0){
        $ext = pathinfo($_FILES['resume_file']['name'], PATHINFO_EXTENSION);
        $fileName = $folder_resume . 'resume_'.$user_id.'_'.time().'.'.$ext;
        if(move_uploaded_file($_FILES['resume_file']['tmp_name'], $fileName)){
            $sql = "UPDATE user SET resume_file='$fileName' WHERE user_id=$user_id";
            if(mysqli_query($conn, $sql)){
                $newResumePath = $fileName;
            }
        }
    }

    // ✅ อัปเดตฟิลด์อื่น ๆ
    if(isset($_POST['field']) && isset($_POST['value'])){
        $field = $_POST['field'];
        $value = mysqli_real_escape_string($conn, $_POST['value']);
        $allowed = ['username','email','phones','contact_email'];
        if(in_array($field, $allowed)){
            $sql = "UPDATE user SET $field='$value' WHERE user_id=$user_id";
            mysqli_query($conn, $sql);
        }
    }

    $response = [
        'status' => 'success',
        'avatar' => $newAvatarPath,    // ✅ ส่ง path รูปกลับไป
        'resume' => $newResumePath     // ✅ ส่ง path เรซูเม่กลับไป
    ];
}

echo json_encode($response);
?>
