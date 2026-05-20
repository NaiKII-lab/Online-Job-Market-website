<?php
session_start();
include('db_server.php');

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

// ✅ รับค่าจาก AJAX (ถ้ามี)
$keyword  = mysqli_real_escape_string($conn, $_GET['keyword'] ?? '');
$location = mysqli_real_escape_string($conn, $_GET['location'] ?? '');
$category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');

// ✅ เงื่อนไขค้นหา
$where = "WHERE 1=1";

if ($keyword !== '') {
    $where .= " AND (j.job_title LIKE '%$keyword%' OR j.company_name LIKE '%$keyword%' OR j.business_type LIKE '%$keyword%')";
}

if ($location !== '') {
    $where .= " AND j.location LIKE '%$location%'";
}

if ($category !== '') {
    $where .= " AND j.business_type LIKE '%$category%'";
}

// ✅ ดึงเฉพาะงานที่ตรงเงื่อนไข
$query = "SELECT j.*, u.username AS hr_name, u.user_id AS hr_id
          FROM jobs j
          JOIN user u ON j.user_id = u.user_id
          $where
          ORDER BY j.created_at DESC
          LIMIT 12";  // จากเดิม 5 → ปรับให้โหลดมากขึ้น


$query = "SELECT j.*, u.username AS hr_name, u.user_id AS hr_id
          FROM jobs j
          JOIN user u ON j.user_id = u.user_id
          ORDER BY j.created_at DESC LIMIT 5";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $logo = !empty($row['logo']) ? $row['logo'] : 'default-logo.png';

        echo '
        <div class="job d-flex align-items-start p-3 mb-3 shadow-sm rounded" style="background:#fff; border:1px solid #eee;">

          <!-- โลโก้บริษัท -->
          <div class="job-logo me-3" style="
            width:65px; height:65px; border:1px solid #ddd; border-radius:10px;
            display:flex; align-items:center; justify-content:center; overflow:hidden;
            background:#fafafa;">
            <img src="' . htmlspecialchars($logo) . '" alt="Logo" style="max-width:100%; max-height:100%; object-fit:contain;">
          </div>

          <!-- เนื้อหางาน -->
          <div class="job-listings flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
              <div>
                <h5 class="mb-1 text-primary fw-semibold">' . htmlspecialchars($row['job_title']) . '</h5>
                <div class="text-muted mb-1">
                  <i class="bi bi-building"></i> ' . htmlspecialchars($row['company_name']) . '
                </div>
                <div class="text-secondary small">
                  📍 ' . htmlspecialchars($row['location']) . ' | 💰 ' . number_format($row['salary']) . ' บาท
                </div>
              </div>
            </div>

            <div class="mt-2">
              <button class="btn btn-outline-success btn-sm rounded-pill me-2 view-job-detail"
                  data-id="' . $row['id_jobs'] . '">
            🔍 ดูรายละเอียด
            </button>';

        // ถ้ายังไม่ล็อกอิน
        if (empty($role)) {
            echo '<span class="text-muted small">🔒 กรุณาเข้าสู่ระบบเพื่อสมัครหรือติดต่อบริษัท</span>';
        }

        // ถ้าเป็นผู้สมัคร
        elseif ($role == 'user') {
            $check_sql = "SELECT status FROM job_applications 
                          WHERE job_id=" . intval($row['id_jobs']) . " AND user_id=$user_id";
            $check_result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                $app = mysqli_fetch_assoc($check_result);
                echo '
                <span id="job-status-' . $row['id_jobs'] . '" 
                      class="badge bg-info text-dark rounded-pill">
                      📌 สถานะ: ' . htmlspecialchars($app['status']) . '
                </span>
                <button class="btn btn-outline-info btn-sm ms-2 send-message-btn rounded-pill"
                        data-jobid="' . $row['id_jobs'] . '" 
                        data-receiverid="' . $row['hr_id'] . '"
                        data-hrname="' . htmlspecialchars($row['hr_name']) . '">
                        💬 ส่งข้อความ
                </button>';
            } else {
                echo '
                <button id="apply-btn-' . $row['id_jobs'] . '" 
                        class="btn btn-primary btn-sm rounded-pill shadow-sm apply-btn" 
                        data-id="' . $row['id_jobs'] . '">
                        ✉️ สมัครงาน
                </button>';
            }
        }

        echo '
            </div>
          </div>
        </div>';
    }
} else {
    echo "<p class='text-muted text-center py-3'>❌ ยังไม่มีงานในระบบ</p>";
}
?>
