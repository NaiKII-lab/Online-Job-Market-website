<?php
session_start();
include('db_server.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$app_id = intval($_GET['app_id'] ?? 0);
if ($app_id <= 0) {
  exit('<div class="alert alert-danger">❌ ไม่มีรหัสผู้สมัคร</div>');
}

$sql = "
SELECT 
  a.id AS app_id,
  a.status,
  u.username, u.email AS user_email, u.phones AS user_phone, u.avatar,
  d.fullname, d.email, d.phone, d.address, d.desired_jobs,
  d.age, d.gender, d.education, d.work_experience,
  d.certificates, d.languages, d.skills, d.ref_contacts,
  d.resume_file AS profile_resume
FROM job_applications a
JOIN user u ON a.user_id = u.user_id
LEFT JOIN user_profile_details d ON u.user_id = d.user_id
WHERE a.id = $app_id
LIMIT 1
";

$res = mysqli_query($conn, $sql);
if (!$res || mysqli_num_rows($res) == 0) {
  exit('<div class="alert alert-danger">❌ ไม่พบข้อมูลผู้สมัครในระบบ</div>');
}

$row = mysqli_fetch_assoc($res);

// ✅ เลือกไฟล์เรซูเม่ที่มีอยู่จริง
$resume = $row['profile_resume'];

$avatar = $row['avatar'] ?: 'uploads/default_avatar.png';
?>

<div class="text-center mb-3">
  <img src="<?= htmlspecialchars($avatar) ?>" class="rounded-circle border shadow" width="100" height="100" style="object-fit:cover;">
  <h5 class="mt-2"><?= htmlspecialchars($row['fullname'] ?: $row['username']) ?></h5>
  <p class="text-muted mb-1">📧 <?= htmlspecialchars($row['email'] ?: $row['user_email']) ?> | ☎️ <?= htmlspecialchars($row['phone'] ?: $row['user_phone']) ?></p>
  <p class="text-muted mb-0">🎯 ตำแหน่งที่สนใจ: <?= htmlspecialchars($row['desired_jobs'] ?: '-') ?></p>
</div>

<hr>
<h6>📍 ข้อมูลส่วนตัว</h6>
<p><strong>อายุ:</strong> <?= htmlspecialchars($row['age'] ?: '-') ?> ปี</p>
<p><strong>เพศ:</strong> <?= htmlspecialchars($row['gender'] ?: '-') ?></p>
<p><strong>ที่อยู่:</strong> <?= htmlspecialchars($row['address'] ?: '-') ?></p>

<hr>
<h6>🎓 การศึกษา</h6>
<?php
$edu = json_decode($row['education'], true);
if ($edu && isset($edu['school'])) {
  echo "<ul>";
  foreach ($edu['school'] as $i => $school) {
    echo "<li>🎓 {$school} - {$edu['degree'][$i]} ({$edu['year'][$i]})</li>";
  }
  echo "</ul>";
} else {
  echo '<p class="text-muted">- ไม่มีข้อมูล -</p>';
}
?>

<hr>
<h6>💡 ทักษะ</h6>
<?php
$skills = json_decode($row['skills'], true);
if ($skills) {
  echo "<ul>";
  foreach ($skills as $s) echo "<li>• {$s}</li>";
  echo "</ul>";
} else echo '<p class="text-muted">- ไม่มีข้อมูล -</p>';
?>

<h6 class="mt-3">🏢 ประสบการณ์ทำงาน / ฝึกงาน</h6>
<?php
$work = json_decode($row['work_experience'], true);
if (isset($work['company']) && count($work['company'])) {
  echo "<ul>";
  foreach ($work['company'] as $i => $company) {
    $pos = htmlspecialchars($work['position'][$i] ?? '');
    $year = htmlspecialchars($work['year'][$i] ?? '');
    echo "<li>🏢 " . htmlspecialchars($company) . " - $pos ($year)</li>";
  }
  echo "</ul>";
} else {
  echo "<p class='text-muted'>- ไม่มีข้อมูล -</p>";
}
?>

<h6 class="mt-3">📜 ใบรับรอง / Certificates</h6>
<?php
$cert = json_decode($row['certificates'], true);
if (is_array($cert) && count($cert)) {
  echo "<ul>";
  foreach ($cert as $c) echo "<li>• " . htmlspecialchars($c) . "</li>";
  echo "</ul>";
} else {
  echo "<p class='text-muted'>- ไม่มีข้อมูล -</p>";
}
?>

<h6 class="mt-3">🗣 ภาษา</h6>
<?php
$lang = json_decode($row['languages'], true);
if (is_array($lang) && count($lang)) {
  echo "<ul>";
  foreach ($lang as $l) echo "<li>• " . htmlspecialchars($l) . "</li>";
  echo "</ul>";
} else {
  echo "<p class='text-muted'>- ไม่มีข้อมูล -</p>";
}
?>

<h6 class="mt-3">📇 ผู้ติดต่ออ้างอิง (Reference)</h6>
<?php
$ref = json_decode($row['ref_contacts'], true);
if (is_array($ref) && count($ref)) {

  echo "<ul>";
  foreach ($ref as $r) {
    if (is_array($r)) {
      // ✅ ถ้าเก็บแบบ array เช่น {name:..., phone:...}
      $txt = implode(" - ", array_map('htmlspecialchars', $r));
      echo "<li>• $txt</li>";
    } else {
      // ✅ ถ้าเก็บแบบข้อความปกติ
      echo "<li>• " . htmlspecialchars($r) . "</li>";
    }
  }
  echo "</ul>";

} else {
  echo "<p class='text-muted'>- ไม่มีข้อมูล -</p>";
}
?>


<hr>
<h6>📄 เรซูเม่</h6>
<?php if ($resume): ?>
  <a href="<?= htmlspecialchars($resume) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
    📎 เปิดเรซูเม่
  </a>
<?php else: ?>
  <p class="text-muted">- ไม่ได้อัพโหลดเรซูเม่ -</p>
<?php endif; ?>

<hr>
<h6>⚙️ จัดการสถานะผู้สมัคร</h6>
<div class="d-flex justify-content-center gap-2 mt-3">
  <button class="btn btn-outline-danger status-btn" data-appid="<?= $row['app_id'] ?>" data-status="ไม่รับ">❌ ไม่รับ</button>
  <button class="btn btn-outline-warning status-btn" data-appid="<?= $row['app_id'] ?>" data-status="สนใจในตัวเขา">⭐ สนใจในตัวเขา</button>
  <button class="btn btn-outline-success status-btn" data-appid="<?= $row['app_id'] ?>" data-status="รับเข้าทำงาน">✅ รับเข้าทำงาน</button>
</div>
