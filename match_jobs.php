<?php
session_start();
require_once 'db_server.php';

mysqli_set_charset($conn, "utf8mb4");
header('Content-Type: text/html; charset=utf-8');

// ตรวจสอบการเข้าสู่ระบบ
$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) exit("<div class='alert alert-warning text-center'>🔒 กรุณาเข้าสู่ระบบก่อนใช้งาน</div>");

// ดึงข้อมูลโปรไฟล์ผู้ใช้
$prof = mysqli_fetch_assoc(mysqli_query(
  $conn,
  "SELECT skills, gender, age, education, desired_jobs 
   FROM user_profile_details 
   WHERE user_id=".(int)$user_id." LIMIT 1"
));

if (!$prof) exit("<div class='alert alert-info text-center'>ℹ️ กรุณากรอกโปรไฟล์ก่อนใช้งานระบบแนะนำงาน</div>");

function safe_decode($v){
  $j = json_decode($v, true);
  return is_array($j) ? $j : [];
}

// ผู้ใช้
$skills = array_map(fn($s)=>mb_strtolower(trim($s),'UTF-8'), safe_decode($prof['skills']));
$userGender = mb_strtolower(trim($prof['gender'] ?? ''), 'UTF-8');
$userAge = intval($prof['age'] ?? 0);
$edu = safe_decode($prof['education']);
$userDegree = mb_strtolower($edu['degree'][0] ?? '', 'UTF-8');

// ตำแหน่งงานที่สนใจ
$desiredList = safe_decode($prof['desired_jobs']);
if (!is_array($desiredList)) $desiredList = $desiredList ? [$desiredList] : [];
$desiredList = array_map(fn($v)=>mb_strtolower(trim($v),'UTF-8'), $desiredList);

// ดึงงานทั้งหมด
$res = mysqli_query($conn, "SELECT * FROM jobs ORDER BY created_at DESC");
$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;

$scored = [];

foreach ($rows as $row) {

  $jobTitle = mb_strtolower(trim($row['job_title']), 'UTF-8');

  // กรองงานให้ตรงกับสายที่สนใจ
  $matchDesired = false;
  foreach ($desiredList as $djob) {
    if ($djob && mb_stripos($jobTitle, $djob) !== false) {
        $matchDesired = true;
        break;
    }
  }
  if (!$matchDesired) continue;

  $jobQual = safe_decode($row['qualifications']);

  $scoreMax = 0;
  $scoreGet = 0;

  foreach ($jobQual as $q) {
    $qText = mb_strtolower(trim($q['text'] ?? ''), 'UTF-8');
    $weight = intval($q['weight'] ?? 1);

    $scoreMax += $weight; // คะแนนเต็มสะสม

    // ===== 1. เพศ =====
    $genderWords = ['ชาย','หญิง','เพศชาย','เพศหญิง','male','female'];
    if (in_array($qText, $genderWords)) {
      if (
        ($qText === "ชาย" && $userGender === "ชาย") ||
        ($qText === "หญิง" && $userGender === "หญิง") ||
        ($qText === "เพศชาย" && $userGender === "ชาย") ||
        ($qText === "เพศหญิง" && $userGender === "หญิง") ||
        ($qText === "male" && $userGender === "ชาย") ||
        ($qText === "female" && $userGender === "หญิง")
      ) $scoreGet += $weight;

      continue;
    }

    // ===== 2. อายุช่วง =====
    if (preg_match('/อายุ\s*([0-9]+)\s*-\s*([0-9]+)/u', $qText, $m)) {
      if ($userAge >= intval($m[1]) && $userAge <= intval($m[2])) {
         $scoreGet += $weight;
      }
      continue;
    }

    // ===== 3. วุฒิ =====
    $degreeKeywords = ['ปริญญาตรี','ปริญญาโท','ปริญญาเอก','ปวส','ปวช','ม.6','มัธยม'];
    foreach ($degreeKeywords as $deg) {
      if (str_contains($qText, $deg) && str_contains($userDegree, $deg)) {
         $scoreGet += $weight;
      }
    }

    // ===== 4. ทักษะ (สะสมได้หลายรายการ) =====
    foreach ($skills as $skill) {
      if (mb_stripos($qText, $skill) !== false) {
         $scoreGet += $weight;
      }
    }
  }

  // คิด % การตรง
  $percent = ($scoreMax > 0) ? round(($scoreGet * 100) / $scoreMax) : 0;

  // ถ้า 0% → ไม่ต้องแสดงงานนี้
  if ($percent <= 0) continue;

  $row['_score']   = $scoreGet;
  $row['_max']     = $scoreMax;
  $row['_percent'] = $percent;

  $scored[] = $row;
}

// เรียงงานจากเปอร์เซ็นต์มาก → น้อย
usort($scored, fn($a,$b) => $b['_percent'] <=> $a['_percent']);

if (!$scored) {
  echo '<div class="text-center py-4">🤔 ไม่พบงานที่ตรงกับข้อมูลของคุณ</div>';
  exit;
}

/* แสดงผล */
echo '<div class="row g-3">';

foreach ($scored as $job) {

  $logo = !empty($job['logo']) ? htmlspecialchars($job['logo']) : 'uploads/default-logo.png';

  $score = $job['_score'];
  $max   = $job['_max'];
  $percent = $job['_percent'];

  // เรทความตรง
  if ($percent >= 80) {
    $matchText = '🟢 ตรงมาก';
    $badgeClass = 'bg-success';
  } elseif ($percent >= 50) {
    $matchText = '🟡 ปานกลาง';
    $badgeClass = 'bg-warning text-dark';
  } else {
    $matchText = '🔴 ตรงน้อย';
    $badgeClass = 'bg-danger';
  }

  echo "
  <div class='col-md-6'>
    <div class='card shadow-sm border-0 h-100'>
      <div class='card-body d-flex'>
        <div class='me-3' style='width:60px;height:60px;border:1px solid #ddd;border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#fafafa;'>
          <img src='$logo' style='max-width:100%;max-height:100%;object-fit:contain;'>
        </div>

        <div class='flex-grow-1'>
          <h6 class='text-primary mb-1'>".htmlspecialchars($job['job_title'])."</h6>
          <div class='text-muted small'>".htmlspecialchars($job['company_name'])."</div>

          <span class='badge $badgeClass mt-2'>$matchText ($percent%)</span>

          <div class='mt-1 small text-dark'>
            คะแนน: <b>$score / $max</b>
          </div>

          <button class='btn btn-outline-success btn-sm rounded-pill mt-2 view-job-detail' 
                  data-id='{$job['id_jobs']}'>🔍 ดูรายละเอียด</button>
        </div>
      </div>
    </div>
  </div>";
}

echo '</div>';
?>
