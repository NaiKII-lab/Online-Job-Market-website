<?php
session_start();
include("db_server.php");

$user_id = intval($_GET["user_id"] ?? 0);
if ($user_id <= 0) exit("❌ ไม่มีข้อมูลผู้ใช้");

$sql = "SELECT u.user_id, u.username, u.email, u.phones, u.avatar,
               d.fullname, d.address, d.education, d.work_experience, d.skills, d.resume_file
        FROM user u
        LEFT JOIN user_profile_details d ON u.user_id = d.user_id
        WHERE u.user_id = $user_id LIMIT 1";

$result = mysqli_query($conn, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
  exit("<p class='text-center text-danger'>❌ ไม่พบข้อมูลผู้ใช้</p>");
}

$data = mysqli_fetch_assoc($result);

$avatar = $data["avatar"] ?: "default_avatar.png";
$skills = json_decode($data["skills"], true) ?: [];

/* ✅ ฟังก์ชันแปลง JSON education */
function renderEducation($json){
  $j = json_decode($json, true);
  if(!$j || !isset($j['school'])) return "<p class='text-muted'>- ไม่ระบุ -</p>";

  $out = "<ul class='ps-3'>";
  for($i=0; $i<count($j['school']); $i++){
    $out .= "<li>📘 {$j['school'][$i]} — {$j['degree'][$i]} ({$j['year'][$i]})</li>";
  }
  return $out . "</ul>";
}

/* ✅ ฟังก์ชันแปลง JSON work_experience */
function renderWork($json){
  $j = json_decode($json, true);
  if(!$j || !isset($j['company'])) return "<p class='text-muted'>- ไม่ระบุ -</p>";

  $out = "<ul class='ps-3'>";
  for($i=0; $i<count($j['company']); $i++){
    $out .= "<li>💼 {$j['company'][$i]} — {$j['position'][$i]} ({$j['year'][$i]})</li>";
  }
  return $out . "</ul>";
}

?>
<div class="text-center">
  <img src="<?= $avatar ?>" class="rounded-circle border shadow" width="110" height="110" style="object-fit:cover;">
  <h4 class="mt-2 fw-bold"><?= htmlspecialchars($data["fullname"] ?? $data["username"]) ?></h4>
  <p class="text-muted">📧 <?= $data["email"] ?> | ☎️ <?= $data["phones"] ?></p>
</div>

<hr>

<h6>💡 ทักษะ</h6>
<?php if ($skills): ?>
  <?php foreach ($skills as $s): ?>
    <span class="badge bg-info text-dark me-1"><?= htmlspecialchars($s) ?></span>
  <?php endforeach ?>
<?php else: ?>
  <p class="text-muted">- ไม่ระบุ -</p>
<?php endif ?>

<h6 class="mt-3">🎓 การศึกษา</h6>
<?= renderEducation($data["education"]) ?>

<h6 class="mt-3">💼 ประสบการณ์ทำงาน</h6>
<?= renderWork($data["work_experience"]) ?>

<h6 class="mt-3">📄 เรซูเม่</h6>
<?php if (!empty($data["resume_file"])): ?>
  <a href="<?= $data["resume_file"] ?>" target="_blank" class="btn btn-outline-dark btn-sm">
      📥 ดาวน์โหลดเรซูเม่
  </a>
<?php else: ?>
  <p class="text-muted">- ยังไม่อัปโหลดเรซูเม่ -</p>
<?php endif ?>

<div class="text-center mt-4">
  <button class="btn btn-primary w-100 mt-3 interest-btn"
        data-userid="<?= $user_id ?>"
        data-appid="0">
    ⭐ สนใจในตัวเขา
  </button>
</div>
