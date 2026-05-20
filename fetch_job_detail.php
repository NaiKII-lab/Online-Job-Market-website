<?php
session_start();
include('db_server.php');

$id = intval($_GET['id']);
$role = $_SESSION['role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;

$sql = "SELECT * FROM jobs WHERE id_jobs = $id";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {

    // 🔹 ตรวจสอบว่าผู้ใช้สมัครงานนี้ไปแล้วหรือยัง
    $applied = false;
    $status_text = '';
    if ($role === 'user' && $user_id > 0) {
        $check = mysqli_query($conn, "SELECT status FROM job_applications WHERE user_id = $user_id AND job_id = $id");
        if (mysqli_num_rows($check) > 0) {
            $applied = true;
            $rowApp = mysqli_fetch_assoc($check);
            $status_text = $rowApp['status'];
        }
    }

    $logo = !empty($row['logo']) ? $row['logo'] : 'default-logo.png';

    echo "
    <div class='container'>
      <div class='text-center mb-3'>
        <img src='{$logo}' class='img-fluid rounded mb-3' style='max-height:100px;'>
        <h4 class='text-primary fw-bold'>" . htmlspecialchars($row['job_title']) . "</h4>
        <p class='text-muted'>" . htmlspecialchars($row['company_name']) . " (" . htmlspecialchars($row['business_type']) . ")</p>
      </div>

      <div class='row'>
        <div class='col-md-6 mb-3'>
          <strong>📍 สถานที่ทำงาน:</strong><br>" . htmlspecialchars($row['location']) . "
        </div>
        <div class='col-md-6 mb-3'>
          <strong>💰 เงินเดือน:</strong><br>" . number_format($row['salary']) . " บาท
        </div>
      </div>

      <div class='mb-3'>
  <strong>🎯 คุณสมบัติ:</strong>
  <div class='border rounded p-2 bg-light mt-1'>
";

$quals = json_decode($row['qualifications'], true);
if (is_array($quals) && count($quals) > 0) {
    echo "<ol class='mb-0'>";
    foreach ($quals as $q) {
        echo "<li>" . htmlspecialchars($q['text']) . "</li>";
    }
    echo "</ol>";
} else {
    echo "<span class='text-muted'>- ไม่ระบุ -</span>";
}

echo "
  </div>
</div>


      <div class='mb-3'>
        <strong>📝 รายละเอียดงาน:</strong>
        <div class='border rounded p-2 bg-light mt-1' style='white-space: pre-line;'>
          " . nl2br(htmlspecialchars($row['job_description'])) . "
        </div>
      </div>

      <div class='mb-3'>
  <strong>🎁 สวัสดิการ:</strong>
  <div class='border rounded p-2 bg-light mt-1'>
";

$benef = $row['benefits'];
$benefJson = json_decode($benef, true);

if (is_array($benefJson) && count($benefJson) > 0) {
    echo "<ul class='mb-0'>";
    foreach ($benefJson as $b) {
        echo "<li>" . htmlspecialchars($b) . "</li>";
    }
    echo "</ul>";
} elseif (trim($benef) !== "") {
    echo nl2br(htmlspecialchars($benef));
} else {
    echo "<span class='text-muted'>- ไม่ระบุ -</span>";
}

echo "
  </div>
</div>

      <div class='text-end text-muted small mb-2'>
        📅 ประกาศเมื่อ: {$row['created_at']}
      </div>
    </div>

    <script>
      window.currentUserRole = '{$role}';
      window.userApplied = " . ($applied ? 'true' : 'false') . ";
      window.applyStatus = '{$status_text}';
    </script>
    ";
} else {
    echo "<div class='alert alert-warning text-center'>ไม่พบบันทึกงานนี้</div>";
}
?>
