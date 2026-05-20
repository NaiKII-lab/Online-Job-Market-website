<?php
session_start();
include('db_server.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'company') exit;
$company_id = $_SESSION['user_id'];

$query = "
SELECT 
  a.id AS app_id,
  a.status, 
  a.applied_at, 
  u.user_id,
  u.username, 
  u.email, 
  u.phones, 
  u.avatar,
  j.job_title,
  j.id_jobs
FROM job_applications a
JOIN user u ON a.user_id = u.user_id
JOIN jobs j ON a.job_id = j.id_jobs
WHERE j.user_id = $company_id
ORDER BY a.applied_at DESC
LIMIT 5
";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0): ?>
  <div class="container my-4">
    <h2 class="mb-3 text-primary fw-bold">👥 ผู้สมัครล่าสุด</h2>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <?php
        $avatar = !empty($row['avatar']) ? htmlspecialchars($row['avatar']) : 'default_avatar.png';
        $status = $row['status'];
        $statusBadge = match ($status) {
          'รอการตอบกลับ' => '<span class="badge bg-secondary status-badge">⏳ รอการตอบกลับ</span>',
          'ไม่รับ'        => '<span class="badge bg-danger status-badge">❌ ไม่รับ</span>',
          'สนใจในตัวเขา'  => '<span class="badge bg-warning text-dark status-badge">⭐ สนใจในตัวเขา</span>',
          'รับเข้าทำงาน'  => '<span class="badge bg-success status-badge">✅ รับเข้าทำงาน</span>',
          default          => '<span class="badge bg-info text-dark status-badge">📌 ' . htmlspecialchars($status) . '</span>'
        };
      ?>

      <div class="applicant-card card shadow-sm border-0 mb-3">
        <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between">

          <div class="d-flex align-items-center">
            <img src="<?= $avatar ?>" alt="avatar"
                 class="rounded-circle border border-light me-3"
                 width="60" height="60" style="object-fit: cover;">
            <div>
              <h5 class="mb-1">
                <strong class="applicant-name text-primary"
                        data-appid="<?= htmlspecialchars($row['app_id']) ?>"
                        data-username="<?= htmlspecialchars($row['username']) ?>"
                        style="cursor:pointer;">
                  <?= htmlspecialchars($row['username']) ?>
                </strong>
                <small class="text-muted">สมัครงาน</small>
                <b><?= htmlspecialchars($row['job_title']) ?></b>
              </h5>
              <small class="text-muted">📅 <?= htmlspecialchars($row['applied_at']) ?></small><br>
              <span class="status-badge"><?= $statusBadge ?></span>
            </div>
          </div>

          <div class="mt-3 mt-md-0">
            <?php if ($status == 'รอการตอบกลับ'): ?>
              <!-- ปุ่มเมื่อยังไม่ตอบกลับ -->
              <button class="btn btn-outline-danger btn-sm status-btn me-2" 
                      data-appid="<?= $row['app_id'] ?>" data-status="ไม่รับ">❌ ไม่รับ</button>
              <button class="btn btn-outline-warning btn-sm status-btn me-2" 
                      data-appid="<?= $row['app_id'] ?>" data-status="สนใจในตัวเขา">⭐ สนใจในตัวเขา</button>
              <button class="btn btn-outline-success btn-sm status-btn" 
                      data-appid="<?= $row['app_id'] ?>" data-status="รับเข้าทำงาน">✅ รับเข้าทำงาน</button>

            <?php elseif ($status == 'สนใจในตัวเขา'): ?>
              <!-- ✅ เพิ่มปุ่มแชท -->
              <button class="btn btn-outline-info btn-sm send-message-btn rounded-pill"
                      data-jobid="<?= $row['id_jobs'] ?>"
                      data-receiverid="<?= $row['user_id'] ?>"
                      data-hrname="<?= htmlspecialchars($row['username']) ?>">
                💬 ส่งข้อความ
              </button>

            <?php elseif ($status == 'รับเข้าทำงาน'): ?>
              <span class="text-success fw-bold">🎉 รับเข้าทำงานแล้ว</span>

            <?php else: ?>
              <span class="text-muted"><?= htmlspecialchars($status) ?></span>
            <?php endif; ?>
          </div>

        </div>
      </div>
    <?php endwhile; ?>
  </div>

<?php else: ?>
  <div class="alert alert-secondary text-center w-75 mx-auto mt-4 shadow-sm">
    ❌ ยังไม่มีผู้สมัครงาน
  </div>
<?php endif; ?>
