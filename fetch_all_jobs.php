<?php
session_start();
include('db_server.php');

$user_id = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';
$mode = $_GET['mode'] ?? 'home';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

// ค่าค้นหา
$keyword = mysqli_real_escape_string($conn, $_GET['keyword'] ?? '');
$location = mysqli_real_escape_string($conn, $_GET['location'] ?? '');
$category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');

$where = "WHERE 1=1";
if ($keyword) {
    $where .= " AND (j.job_title LIKE '%$keyword%' OR j.company_name LIKE '%$keyword%' OR j.job_description LIKE '%$keyword%')";
}
if ($location) {
    $where .= " AND j.location LIKE '%$location%'";
}
if ($category) {
    $where .= " AND j.business_type LIKE '%$category%'";
}

/* ✅ ฝั่ง HR → แสดงเฉพาะงานของตัวเอง */
if ($role === 'company') {

    $count_sql = "SELECT COUNT(*) AS total FROM jobs WHERE user_id = $user_id";
    $count_result = mysqli_query($conn, $count_sql);
    $total_jobs = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total_jobs / $limit);

    $sql = "SELECT * FROM jobs WHERE user_id = $user_id ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo '<div class="text-center text-muted py-4">📭 ยังไม่มีงานที่คุณประกาศ</div>';
        exit;
    }

    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-hover align-middle">';
    echo '<thead class="table-light">
            <tr>
              <th width="5%">#</th>
              <th>ชื่องาน</th>
              <th>เงินเดือน</th>
              <th>สถานที่</th>
              <th>วันที่ประกาศ</th>
              <th width="22%" class="text-center">การจัดการ</th>
            </tr>
          </thead>
          <tbody>';

    $i = 1 + $offset;
    while ($row = mysqli_fetch_assoc($result)) {
        $logo = !empty($row['logo']) ? $row['logo'] : 'default-logo.png';
        echo "
        <tr>
          <td class='text-center'>{$i}</td>
          <td>
            <div class='d-flex align-items-center'>
              <img src='{$logo}' width='45' height='45' class='me-2 rounded border'>
              <div>
                <strong class='text-primary'>{$row['job_title']}</strong><br>
                <small class='text-muted'>{$row['business_type']}</small>
              </div>
            </div>
          </td>
          <td>" . number_format($row['salary']) . " บาท</td>
          <td>{$row['location']}</td>
          <td><small>{$row['created_at']}</small></td>
          <td class='text-center'>
            <button class='btn btn-outline-info btn-sm view-job-detail' data-id='{$row['id_jobs']}'>🔍 ดูรายละเอียด</button>
            <button class='btn btn-outline-warning btn-sm edit-job-btn' data-id='{$row['id_jobs']}'>✏️ แก้ไข</button>
            <button class='btn btn-outline-danger btn-sm delete-job-btn' data-id='{$row['id_jobs']}'>🗑️ ลบ</button>
          </td>
        </tr>";
        $i++;
    }
    echo '</tbody></table></div>';

    // ✅ pagination
    if ($total_pages > 1) {
        echo '<nav><ul class="pagination justify-content-center mt-3">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            echo "<li class='page-item $active'><a class='page-link load-page' href='#' data-page='$i'>$i</a></li>";
        }
        echo '</ul></nav>';
    }
    exit;
}

/* ✅ ฝั่ง USER (หรือยังไม่ล็อกอิน) → เห็นงานทั้งหมด */
$count_sql = "SELECT COUNT(*) AS total FROM jobs j $where";
$count_result = mysqli_query($conn, $count_sql);
$total_jobs = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_jobs / $limit);

$sql = "SELECT j.*, u.username AS hr_name, u.user_id AS hr_id
        FROM jobs j
        JOIN user u ON j.user_id = u.user_id
        $where
        ORDER BY j.created_at DESC
        LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo '<div class="text-center text-muted py-4">📭 ไม่พบงานที่ตรงกับการค้นหา</div>';
    exit;
}

echo '<div class="row">';
while ($row = mysqli_fetch_assoc($result)) {
    $logo = !empty($row['logo']) ? $row['logo'] : 'default-logo.png';
    $job_id = intval($row['id_jobs']);

    echo "
    <div class='col-md-6 col-lg-4 mb-3'>
      <div class='card h-100 shadow-sm'>
        <div class='card-body'>
          <div class='d-flex align-items-center mb-2'>
            <img src='{$logo}' width='50' height='50' class='rounded border me-2'>
            <div>
              <h6 class='text-primary fw-bold mb-0'>{$row['job_title']}</h6>
              <small class='text-muted'>{$row['company_name']}</small>
            </div>
          </div>

          <p class='small text-muted mb-1'>📍 {$row['location']}</p>
          <p class='small text-muted mb-2'>💰 " . number_format($row['salary']) . " บาท</p>

          <button class='btn btn-outline-success btn-sm w-100 mb-2 view-job-detail' data-id='{$job_id}'>🔍 ดูรายละเอียด</button>
    ";

    /* ✅ ถ้าไม่ล็อกอิน → ให้แสดงปุ่ม "เข้าสู่ระบบเพื่อสมัคร" */
    if (!$user_id) {
        echo "<button class='btn btn-secondary btn-sm w-100 open-login-modal'>🔒 กรุณาเข้าสู่ระบบ</button>";
    }
    /* ✅ ถ้าล็อกอินเป็น USER → สมัครงานได้ */
    else if ($role == 'user') {
        $check_sql = "SELECT status FROM job_applications WHERE job_id=$job_id AND user_id=$user_id";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
    $app = mysqli_fetch_assoc($check_result);
    echo "
    <div class='text-center'>
      <span class='badge bg-info text-dark rounded-pill d-block mb-2'>📌 สถานะ: {$app['status']}</span>
      <button class='btn btn-outline-info btn-sm w-100 send-message-btn'
        data-jobid='{$job_id}' data-receiverid='{$row['hr_id']}' data-hrname='{$row['hr_name']}'>💬 ส่งข้อความ</button>
    </div>";
}
 else {
            echo "<button id='apply-btn-{$job_id}' class='btn btn-primary btn-sm w-100 apply-btn' data-id='{$job_id}'>✉️ สมัครงาน</button>";
        }
    }

    echo "</div></div></div>";
}
echo "</div>";

if ($total_pages > 1) {
    $pageClass = ($mode === "modal") ? "modal-page-btn" : "home-page-btn";
    echo '<nav><ul class="pagination justify-content-center mt-3">';
    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        echo "<li class='page-item $active'>
                <a class='page-link $pageClass' href='#' data-page='$i'>$i</a>
              </li>";
    }
    echo '</ul></nav>';
}


?>
