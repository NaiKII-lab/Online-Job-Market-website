<?php
session_start();
include('db_server.php');

$company_id = $_SESSION['user_id']; // ✅ บริษัทที่กำลังค้นหา

$skill = mysqli_real_escape_string($conn, $_GET['skill'] ?? '');
$keyword = mysqli_real_escape_string($conn, $_GET['keyword'] ?? '');
$edu = mysqli_real_escape_string($conn, $_GET['edu'] ?? '');

$where = "WHERE u.role = 'user'"; // ✅ จำกัดเฉพาะผู้สมัคร (ไม่รวมบริษัท)

if ($skill) {
  $where .= " AND JSON_SEARCH(d.skills, 'one', '$skill') IS NOT NULL";
}
if ($keyword) {
  $where .= " AND (d.fullname LIKE '%$keyword%' OR u.username LIKE '%$keyword%' OR d.skills LIKE '%$keyword%')";
}
if ($edu) {
  $where .= " AND d.education LIKE '%$edu%'";
}

$sql = "
SELECT 
  u.user_id, u.username, u.avatar,
  d.fullname, d.skills,
  (SELECT 1 FROM interested_users iu 
   WHERE iu.company_id = $company_id AND iu.user_id = u.user_id LIMIT 1) AS interested

FROM user u
JOIN user_profile_details d ON u.user_id = d.user_id
$where
ORDER BY u.user_id DESC
";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
  exit("<p class='text-muted text-center my-4'>📭 ไม่พบผู้สมัครที่ตรงกับเงื่อนไข</p>");
}

echo "<div class='row justify-content-center'>";

while($row = mysqli_fetch_assoc($result)) {
  $avatar = $row['avatar'] ?: "default_avatar.png";
  $skills = json_decode($row['skills'], true) ?: [];

  echo "
  <div class='col-md-5 mb-3'>
    <div class='card shadow-sm applicant-card'>
      <div class='card-body'>
        <div class='d-flex align-items-center mb-2'>
          <img src='{$avatar}' width='50' height='50' class='rounded-circle border me-2' style='object-fit:cover;'>
          <div>
            <h6 class='text-primary fw-bold applicant-name' 
                data-userid='{$row['user_id']}' 
                style='cursor:pointer'>
                {$row['fullname']}
            </h6>
          </div>
        </div>

        <p class='small mb-2'><b>ทักษะ:</b> ".implode(" • ", $skills)."</p>

        <button class='btn btn-outline-info view-jobseeker btn-sm w-100'
        data-userid='{$row['user_id']}'>
        🔍 ดูรายละเอียด
        </button>";

        // ✅ แสดงปุ่ม/Badge แทนที่ปุ่มสนใจ
        if ($row['interested']) {
          echo "<span class='badge bg-warning text-dark d-block mt-2'>⭐ ส่งความใจแล้ว</span>";
        } else {
          echo "<button class='btn btn-primary btn-sm w-100 mt-2 interest-btn'
                  data-userid='{$row['user_id']}'>
                  ⭐ สนใจในตัวเขา
                </button>";
        }

  echo "</div></div></div>";
}

echo "</div>";
?>
