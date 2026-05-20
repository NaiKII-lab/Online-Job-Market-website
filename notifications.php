<?php
session_start();
include 'db_server.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  exit('กรุณาเข้าสู่ระบบก่อน');
}

$user_id = $_SESSION['user_id'];

// ✅ ดึงแจ้งเตือนพร้อมข้อมูลงาน
$sql = "
  SELECT n.id, n.message, n.is_read, n.created_at, n.job_id,
         j.job_title, j.company_name, j.id_jobs
  FROM notifications n
  LEFT JOIN jobs j ON n.job_id = j.id_jobs
  WHERE n.user_id = ?
  ORDER BY n.created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// ✅ จัดกลุ่มตามงาน
$grouped = [];
while ($row = $result->fetch_assoc()) {
  $job = $row['job_title'] ?: 'ไม่ทราบตำแหน่ง';
  $grouped[$job][] = $row;
}
?>

<div class="accordion" id="jobAccordion">
  <?php if (!empty($grouped)): ?>
    <?php $index = 0; foreach ($grouped as $job_title => $messages): $index++; ?>
      <?php $job_id = $messages[0]['job_id'] ?? 0; ?>
      <div class="accordion-item mb-2 shadow-sm border border-primary rounded-3">
        <h2 class="accordion-header" id="heading<?= $index ?>">
          <div class="d-flex justify-content-between align-items-center">
            <button class="accordion-button collapsed fw-bold bg-primary text-white flex-grow-1"
                    type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>"
                    aria-expanded="false" aria-controls="collapse<?= $index ?>">
              📂 <?= htmlspecialchars($job_title) ?>
              <span class="ms-2 small">(<?= htmlspecialchars($messages[0]['company_name'] ?? '-') ?>)</span>
            </button>

            <button class="btn btn-light btn-sm text-primary fw-bold mark-all-read ms-2"
                    data-jobid="<?= $job_id ?>">อ่านทั้งหมด</button>
          </div>
        </h2>

        <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
             aria-labelledby="heading<?= $index ?>" data-bs-parent="#jobAccordion">
          <div class="accordion-body p-0">
            <ul class="list-group list-group-flush">
              <?php foreach ($messages as $row): ?>
                <?php $isInvite = ($row['job_id'] > 0); // ✅ ถ้ามี job_id แสดงปุ่มสมัคร ?>
                <li class="list-group-item d-flex justify-content-between align-items-start
                    <?= $row['is_read'] ? '' : 'list-group-item-warning'; ?>">

                  <div>
                    <div class="fw-bold"><?= htmlspecialchars($row['message']) ?></div>
                    <small class="text-muted"><?= $row['created_at'] ?></small>
                  </div>

                  <div class="d-flex flex-column align-items-end">

                    <?php if ($isInvite): ?>
                      <?php
// ตรวจสอบว่าสมัครงานนี้แล้วหรือยัง
$job_id = $row['job_id'];
$check_apply = $conn->query("SELECT status FROM job_applications WHERE user_id=$user_id AND job_id=$job_id");

if ($check_apply && $check_apply->num_rows > 0) {
    $app = $check_apply->fetch_assoc();
    echo "<span class='badge bg-info text-dark'>📌 สถานะ: {$app['status']}</span>";
} else {
    echo "
<button class='btn btn-success btn-sm w-100 apply-job-btn'
        data-jobid='{$job_id}'>
    ✅ สมัครงานนี้
</button>";

}
?>

                    <?php endif; ?>

                    <?php if (!$row['is_read']): ?>
                      <button class="btn btn-sm btn-outline-primary mark-read-btn"
                              data-id="<?= $row['id'] ?>">อ่านแล้ว</button>
                    <?php endif; ?>

                  </div>

                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="alert alert-info text-center mt-3">
      ยังไม่มีการแจ้งเตือนจากงานใด ๆ
    </div>
  <?php endif; ?>
</div>

<!-- ✅ JS Event -->
<script>
$(document).on('click', '.mark-read-btn', function() {
  let id = $(this).data('id');
  $.post('mark_read.php', { id: id }, function(res) {
    if (res.success) {
      $('button[data-id="' + id + '"]').closest('li').removeClass('list-group-item-warning');
      $('button[data-id="' + id + '"]').remove();
    }
  }, 'json');
});

$(document).on('click', '.mark-all-read', function() {
  let jobId = $(this).data('jobid');
  $.post('mark_read.php', { job_id: jobId }, function(res) {
    if (res.success) {
      $('button[data-jobid="' + jobId + '"]').closest('.accordion-item')
        .find('.list-group-item-warning')
        .removeClass('list-group-item-warning')
        .find('.mark-read-btn').remove();
    }
  }, 'json');
});

$(document).on("click", ".apply-job-btn", function(){
    let btn = $(this);
    let jobId = btn.data("jobid");

    btn.prop("disabled", true).text("⏳ กำลังสมัคร...");

    $.post("apply_job.php", { job_id: jobId }, function(res){
        let data = (typeof res === "string") ? JSON.parse(res) : res;

        if(data.status === "success"){
            Swal.fire("✅ สมัครสำเร็จ!", data.msg, "success");
            btn.fadeOut(300, function(){
                $(this).replaceWith(`<span class="badge bg-info text-dark d-block mt-2">📌 สมัครแล้ว</span>`);
            });
        } else {
            Swal.fire("❌ ไม่สำเร็จ", data.msg, "error");
            btn.prop("disabled", false).text("✅ สมัครงานนี้");
        }
    });
});

</script>
