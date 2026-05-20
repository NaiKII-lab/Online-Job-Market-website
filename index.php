<?php  
    session_start(); 
 
    if (isset($_GET['logout'])) { 
        session_destroy(); 
        unset($_SESSION['username']); 
        header("location: index.php"); 
    } 
?>


<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>หางานออนไลน์</title>
  <link rel="icon" type="image/png" sizes="32x32" href="job.png">
<link rel="apple-touch-icon" href="job.png">

  <link rel="stylesheet" href="style1.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>let sessionRole = '<?= $_SESSION['role'] ?? '' ?>';</script>

  
</head>
<body>


<header class="site-header shadow-sm">
  <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap py-3 px-4">
    <!-- 🔹 โลโก้ -->
    <h3 class="text-white fw-bold m-0">
      <i class="bi bi-briefcase-fill me-2"></i> Job Online Market
      <small class="fw-light" style="font-size: 0.9rem;">– หางานง่ายในไม่กี่คลิก</small>
    </h3>

    <!-- 🔹 เมนู -->
    <nav class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
      <a href="index.php" class="nav-link text-white fw-semibold me-3">หน้าแรก</a>
      <?php if (!isset($_SESSION['role']) || $_SESSION['role'] == 'user') : ?>
  <a class="nav-link text-white fw-semibold me-3" href="#" 
     data-bs-toggle="modal" data-bs-target="#allJobsModal">
     งานทั้งหมด
  </a>
<?php endif; ?>
      
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'company') : ?>
        <a href="#" 
     class="nav-link text-white fw-semibold me-3" 
     data-bs-toggle="modal" 
     data-bs-target="#allApplicantsModal">
     ผู้สมัครทั้งหมด
  </a>
        <a class="nav-link text-white fw-semibold me-3" href="#" data-bs-toggle="modal" data-bs-target="#allJobsModal">งานของฉัน</a>
        <a href="#" 
   class="nav-link text-white fw-semibold me-3" 
   data-bs-toggle="modal" 
   data-bs-target="#addJobModal">
   เพิ่มงานใหม่
</a>
      <?php endif; ?>

      <?php if (!isset($_SESSION['username'])) : ?>
        <a href="#" class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#loginModal">
          <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
        </a>
      <?php endif; ?>
    </nav>

    <!-- 🔹 Dropdown ผู้ใช้ -->
    <?php if (isset($_SESSION['username'])): ?>
      <?php
        include('db_server.php');
        $user_id = $_SESSION['user_id'];
        $userInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email, avatar FROM user WHERE user_id = $user_id"));
        $userAvatar = !empty($userInfo['avatar']) ? $userInfo['avatar'] : 'default_avatar.png';
        $userEmail  = $userInfo['email'] ?? '-';
      ?>
      <div class="dropdown user-dropdown mt-2 mt-md-0">
        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar" width="36" height="36"
               class="rounded-circle me-2 border border-white shadow-sm">
          <span class="username-text"><?= htmlspecialchars($_SESSION['username']) ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
          <li class="px-3 py-2 text-center border-bottom">
            <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Avatar" width="70" height="70" class="rounded-circle mb-2 border">
            <div><strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
            <div class="text-muted small"><?= htmlspecialchars($userEmail) ?></div>
          </li>
          <?php if ($_SESSION['role'] == 'user'): ?>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notifModal">📩 แจ้งเตือน <span id="notif-badge" class="badge bg-danger">0</span></a></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">📄 โปรไฟล์</a></li>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#personalProfileModal">🧾 ประวัติของฉัน</a></li>
          <?php endif; ?>
          <?php if ($_SESSION['role'] == 'company'): ?>
            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">📄 โปรไฟล์</a></li>
            <?php endif; ?>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="index.php?logout='1'">🔓 ออกจากระบบ</a></li>
        </ul>
      </div>
    <?php endif; ?>
  </div>
</header>


<!-- 🔔 Modal: กล่องข้อความแจ้งเตือน -->
<div class="modal fade" id="notifModal" tabindex="-1" aria-labelledby="notifModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title" id="notifModalLabel">📩 กล่องข้อความแจ้งเตือน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="notif-content">
        <div class="text-center text-muted py-3">กำลังโหลด...</div>
      </div>
    </div>
  </div>
</div>


<!-- Modal Login -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

      <!-- Tabs -->
      <div class="d-flex text-center">
        <div class="flex-fill py-3 tab-login active" id="tab-user">หางาน</div>
        <div class="flex-fill py-3 tab-login" id="tab-company">หาคน</div>
      </div>

      <div class="modal-body p-4">
        <!-- 🔹 ผู้สมัคร -->
        <form id="loginFormUser" class="login-section">
          <h5 class="text-center mb-3 fw-bold">เข้าสู่ระบบสำหรับผู้สมัครงาน</h5>
          <div class="mb-3">
            <label>ชื่อหรืออีเมล</label>
            <input type="text" name="username" class="form-control" placeholder="USERNAME/EMAIL" required>
          </div>
          <div class="mb-3 position-relative">
  <label class="form-label">รหัสผ่าน</label>
  <div class="position-relative">
    <input type="password" name="password" class="form-control pe-5" placeholder="PASSWORD" required>
    <button type="button" class="btn toggle-password border-0 position-absolute end-0 top-50 translate-middle-y me-2">
      <i class="bi bi-eye"></i>
    </button>
  </div>
</div>
          <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">เข้าสู่ระบบ</button>
          <p class="mt-3 text-center small">
            ยังไม่มีบัญชี? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">สมัครสมาชิก</a>
          </p>
        </form>

        <!-- 🔹 นายจ้าง -->
        <form id="loginFormCompany" class="login-section d-none">
          <h5 class="text-center mb-3 fw-bold">เข้าสู่ระบบสำหรับนายจ้าง / บริษัท</h5>
          <div class="mb-3">
            <label>ชื่อผู้ใช้หรืออีเมลบริษัท</label>
            <input type="text" name="username" class="form-control" placeholder="USERNAME/EMAIL" required>
          </div>
          <div class="mb-3 position-relative">
  <label class="form-label">รหัสผ่าน</label>
  <div class="position-relative">
    <input type="password" name="password" class="form-control pe-5" placeholder="PASSWORD" required>
    <button type="button" class="btn toggle-password border-0 position-absolute end-0 top-50 translate-middle-y me-2">
      <i class="bi bi-eye"></i>
    </button>
  </div>
</div>
          <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">เข้าสู่ระบบ</button>
          <p class="mt-3 text-center small">
            ยังไม่มีบัญชีนายจ้าง? <a href="#" data-bs-toggle="modal" data-bs-target="#registerCompanyModal" data-bs-dismiss="modal">สมัครนายจ้าง</a>
          </p>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- Modal Register ผู้หางาน -->
<div class="modal fade" id="registerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #6ee7e7, #46c2cb);">
        <h5 class="modal-title fw-bold">🧑‍💼 สมัครสมาชิกผู้หางาน</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body px-4 py-3" style="background-color: #f4ffff;">
        <form id="registerUserForm">
          <div class="mb-3">
            <label class="form-label fw-semibold">ชื่อผู้ใช้ (Username)</label>
            <input type="text" name="username" class="form-control form-control-lg rounded-3 shadow-sm input-mint" placeholder="เช่น jobseeker01" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">อีเมล (Email)</label>
            <input type="email" name="email" class="form-control form-control-lg rounded-3 shadow-sm input-mint" placeholder="example@email.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">รหัสผ่าน (Password)</label>
            <div class="input-group">
              <input type="password" name="password" id="password" class="form-control form-control-lg rounded-start-3 shadow-sm input-mint" placeholder="••••••••" required>
              <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3"><i class="bi bi-eye"></i></button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">ยืนยันรหัสผ่าน (Confirm Password)</label>
            <div class="input-group">
              <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-lg rounded-start-3 shadow-sm input-mint" placeholder="กรอกรหัสผ่านอีกครั้ง" required>
              <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3"><i class="bi bi-eye"></i></button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">เบอร์โทรศัพท์</label>
            <input type="text" name="phones" class="form-control form-control-lg rounded-3 shadow-sm input-mint" placeholder="เช่น 0912345678">
          </div>

          <button type="submit" class="btn w-100 py-2 fw-bold rounded-3 shadow-sm register-btn">
            ✅ สมัครสมาชิก
          </button>
        </form>

        <p class="mt-4 text-center text-muted">
          มีบัญชีแล้ว?
          <a href="#" class="text-info fw-semibold" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
            เข้าสู่ระบบ
          </a>
        </p>
      </div>
    </div>
  </div>
</div>




<!-- 🏢 Modal สมัครสมาชิกบริษัท -->
<div class="modal fade" id="registerCompanyModal" tabindex="-1" aria-labelledby="registerCompanyLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-4">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #2d6cdf, #f2b705);">
        <h5 class="modal-title fw-bold" id="registerCompanyLabel">🏢 สมัครสมาชิกสำหรับนายจ้าง</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4" style="background-color: #f8faff;">
        <form id="registerCompanyForm">
          <div class="mb-3">
            <label class="form-label fw-semibold">ชื่อผู้ใช้ (Username)</label>
            <input type="text" name="username" class="form-control form-control-lg input-company" placeholder="ตั้งชื่อผู้ใช้ของคุณ" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">อีเมล</label>
            <input type="email" name="email" class="form-control form-control-lg input-company" placeholder="example@company.com" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">เบอร์โทรศัพท์</label>
            <input type="text" name="phones" class="form-control form-control-lg input-company" placeholder="เช่น 0812345678" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">รหัสผ่าน</label>
            <div class="input-group">
              <input type="password" name="password" id="companyPassword" class="form-control form-control-lg input-company rounded-start-3" placeholder="อย่างน้อย 6 ตัวอักษร" required>
              <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">ยืนยันรหัสผ่าน</label>
            <div class="input-group">
              <input type="password" id="companyConfirmPassword" class="form-control form-control-lg input-company rounded-start-3" placeholder="พิมพ์รหัสผ่านอีกครั้ง" required>
              <button type="button" class="btn btn-outline-secondary toggle-password rounded-end-3">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">ชื่อบริษัท</label>
            <input type="text" name="company_name" class="form-control form-control-lg input-company" placeholder="ชื่อบริษัทของคุณ" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">ประเภทธุรกิจ</label>
           <select name="business_type" class="form-select input-company" required>
              <option value="">-- เลือกประเภทธุรกิจ --</option>
              <option value="เทคโนโลยี / IT">เทคโนโลยี / IT</option>
              <option value="การตลาด / สื่อสาร">การตลาด / สื่อสาร</option>
              <option value="การผลิต / โรงงาน">การผลิต / โรงงาน</option>
              <option value="ก่อสร้าง / วิศวกรรม">ก่อสร้าง / วิศวกรรม</option>
              <option value="ขนส่ง / โลจิสติกส์">ขนส่ง / โลจิสติกส์</option>
              <option value="อื่น ๆ">อื่น ๆ</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm company-register-btn">
            ✅ สมัครสมาชิกบริษัท
          </button>
        </form>

        <p class="text-center mt-3 mb-0">
          มีบัญชีแล้ว?
          <a href="#" class="text-decoration-none fw-semibold text-primary"
             data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
             เข้าสู่ระบบ
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<!--Modal: เชิญงาน-->
<div class="modal fade" id="selectJobModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">📌 เลือกงานที่ต้องการชวนผู้สมัคร</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="inviteJobUserId">
        <div id="jobSelectList" class="p-2 text-center">⏳ กำลังโหลด...</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button class="btn btn-success" id="confirmSendInvite">✅ ส่งคำเชิญ</button>
      </div>
    </div>
  </div>
</div>




<!-- Modal: ประวัติส่วนตัว -->
<div class="modal fade" id="personalProfileModal" tabindex="-1" aria-labelledby="personalProfileLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="personalProfileLabel">🧾 ประวัติส่วนตัว</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="personalProfileForm">
          <div class="alert alert-info">
  💡 คุณสามารถบันทึกข้อมูลบางส่วนได้ และกลับมาแก้ไขเพิ่มเติมภายหลังได้
</div>
          <!-- 🔹 ข้อมูลส่วนบุคคล -->
            <h5 class="mb-3 mt-2 text-primary">ข้อมูลส่วนบุคคล *</h5>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">ชื่อ-นามสกุล</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="fullname" required readonly>
                  <button type="button" class="btn btn-outline-primary editable-btn" data-field="fullname">
                    ✏️
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">อีเมล(สำหรับติดต่อ)</label>
                <div class="input-group">
                  <input type="email" class="form-control" name="email" required readonly>
                  <button type="button" class="btn btn-outline-primary editable-btn" data-field="email">
                    ✏️
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">เบอร์โทรศัพท์</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="phone" readonly>
                  <button type="button" class="btn btn-outline-primary editable-btn" data-field="phone">
                    ✏️
                  </button>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">ที่อยู่</label>
                <div class="input-group">
                  <input type="text" class="form-control" name="address" readonly>
                  <button type="button" class="btn btn-outline-primary editable-btn" data-field="address">
                    ✏️
                  </button>
                </div>
              </div>
            </div>

            <!-- 🔹 เพิ่มช่องอายุ -->
          <div class="col-md-6">
            <label class="form-label">อายุ</label>
            <div class="input-group">
              <input type="number" class="form-control" name="age" min="10" max="100" placeholder="กรอกอายุ" readonly>
              <button type="button" class="btn btn-outline-primary editable-btn" data-field="age">✏️</button>
            </div>
          </div>
            
          <div class="mb-3">
  <label class="form-label fw-semibold">เพศ</label>
  <select name="gender" class="form-select">
    <option value="">-- เลือกเพศ --</option>
    <option value="ชาย">ชาย</option>
    <option value="หญิง">หญิง</option>
    <option value="อื่นๆ">อื่นๆ</option>
  </select>
</div>

          <!-- 🔹 ลักษณะงานที่ต้องการ -->
          <h5 class="mt-4 text-primary">ลักษณะงานที่ต้องการ *</h5>
          <div id="desired-jobs-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addDesiredJobBtn">
            ➕ เพิ่มลักษณะงาน
          </button>
          

          <!-- 🔹 ประวัติการศึกษา -->
          <h5 class="mt-4 text-primary">ประวัติการศึกษา *</h5>
          <div id="education-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addEducationBtn">
            ➕ เพิ่มประวัติการศึกษา
          </button>

          <!-- 🔹 ประวัติการทำงาน / ฝึกงาน -->
          <h5 class="mt-4 text-primary">ประวัติการทำงาน / ฝึกงาน</h5>
          <div id="work-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addWorkBtn">
            ➕ เพิ่มประสบการณ์
          </button>

          <!-- 🔹 อบรม / ประกาศนียบัตร -->
          <h5 class="mt-4 text-primary">อบรม / ประกาศนียบัตร</h5>
          <div id="certificate-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addCertificateBtn">
            ➕ เพิ่มหลักสูตร / ใบประกาศ
          </button>

          <!-- 🔹 ความสามารถทางภาษา -->
          <h5 class="mt-4 text-primary">ความสามารถทางภาษา</h5>
          <div id="language-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addLanguageBtn">
            ➕ เพิ่มภาษา
          </button>

          <!-- 🔹 ความสามารถอื่น ๆ -->
          <h5 class="mt-4 text-primary">ความสามารถอื่น ๆ</h5>
          <div id="skill-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addSkillBtn">
            ➕ เพิ่มความสามารถ
          </button>

          <!-- 🔹 บุคคลอ้างอิง -->
          <h5 class="mt-4 text-primary">บุคคลอ้างอิง</h5>
          <div id="reference-list" class="mb-3"></div>
          <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addReferenceBtn">
            ➕ เพิ่มบุคคลอ้างอิง
          </button>
          <h5 class="mt-4 text-primary">แนบไฟล์เรซูเม่ (PDF / DOCX)</h5>
          <div class="mb-3">
            <input type="file" name="resume_file" accept=".pdf,.doc,.docx" class="form-control">
            <div class="form-text text-muted">รองรับไฟล์ .pdf และ .docx ขนาดไม่เกิน 5 MB</div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button type="submit" form="personalProfileForm" class="btn btn-primary">💾 บันทึกข้อมูล</button>
      </div>
    </div>
  </div>
</div>


<!-- 🟨 Modal แก้ไขงาน -->
<div class="modal fade" id="editJobModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-dark fw-bold">✏️ แก้ไขประกาศงาน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="editJobForm">
        <div class="modal-body">
          
          <!-- 🔹 โลโก้บริษัท -->
          <div class="text-center mb-4">
            <div id="logo-preview" class="p-3 border rounded bg-light d-inline-block">
              <small class="text-muted">ไม่มีโลโก้</small>
            </div>
            <p class="mt-2 mb-1 text-secondary">โลโก้บริษัท (อัปโหลดใหม่)</p>
            <input type="file" name="logo" id="edit-logo" accept="image/*" class="form-control w-50 mx-auto">
          </div>

          <hr>

          <input type="hidden" name="id_jobs" id="edit-id">

          <div class="mb-3">
            <label class="form-label">ชื่อตำแหน่งงาน *</label>
            <input type="text" name="job_title" id="edit-title" class="form-control" required>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ชื่อบริษัท *</label>
              <input type="text" name="company_name" id="edit-company" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">ประเภทธุรกิจ *</label>
              <select name="business_type" id="edit-business" class="form-select" required>
                <option value="">-- เลือกประเภทธุรกิจ --</option>
                <option value="เทคโนโลยี / IT">เทคโนโลยี / IT</option>
                <option value="การศึกษา">การศึกษา</option>
                <option value="อาหารและเครื่องดื่ม">อาหารและเครื่องดื่ม</option>
                <option value="การแพทย์ / สุขภาพ">การแพทย์ / สุขภาพ</option>
                <option value="ค้าปลีก / ขายส่ง">ค้าปลีก / ขายส่ง</option>
                <option value="ก่อสร้าง / วิศวกรรม">ก่อสร้าง / วิศวกรรม</option>
                <option value="การเงิน / ธนาคาร">การเงิน / ธนาคาร</option>
                <option value="การท่องเที่ยว / โรงแรม">การท่องเที่ยว / โรงแรม</option>
                <option value="ขนส่ง / โลจิสติกส์">ขนส่ง / โลจิสติกส์</option>
                <option value="บัญชี">บัญชี</option>
                <option value="อื่น ๆ">อื่น ๆ</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">สถานที่ทำงาน *</label>
            <input type="text" name="location" id="edit-location" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">เงินเดือน *</label>
            <input type="number" name="salary" id="edit-salary" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">รายละเอียดงาน *</label>
            <textarea name="job_description" id="edit-description" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mb-3">
  <label class="form-label fw-bold">คุณสมบัติผู้สมัคร *</label>
  <div id="edit-qualifications-list"></div>
  <button type="button" class="btn btn-outline-success btn-sm mt-2" id="editAddQualificationBtn">➕ เพิ่มคุณสมบัติ</button>
</div>

<div class="mb-3">
  <label class="form-label fw-bold">🎁 สวัสดิการ</label>
  <div id="edit-benefits-list"></div>
  <button type="button" class="btn btn-outline-success btn-sm mt-2" id="editAddBenefitBtn">➕ เพิ่มสวัสดิการ</button>
</div>


        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">❌ ยกเลิก</button>
          <button type="submit" class="btn btn-success">💾 บันทึกการแก้ไข</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- 🧑‍💼 Modal ผู้สมัครทั้งหมด -->
<div class="modal fade" id="allApplicantsModal" tabindex="-1" aria-labelledby="allApplicantsLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="allApplicantsLabel">🧑‍💼 ผู้สมัครทั้งหมด</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- 🔍 ส่วนค้นหาผู้สมัคร -->
        <div class="p-3 mb-3 rounded" style="background:#f4f8ff;">
          <h5 class="text-center fw-bold text-primary mb-3">ค้นหาผู้สมัคร</h5>

          <div class="row g-2 justify-content-center">
            <div class="col-md-5">
              <input type="text" id="searchApplicantKeyword" class="form-control" placeholder="ชื่อผู้สมัคร หรือ อีเมล">
            </div>
            <div class="col-md-4">
              <input type="text" id="searchJobFilter" class="form-control" placeholder="ชื่องาน">
            </div>
            <div class="col-md-2">
              <button id="btnSearchApplicant" class="btn btn-primary w-100">ค้นหา</button>
            </div>
          </div>
        </div>

        <!-- 📋 รายการผู้สมัคร -->
        <div id="applicantsListContainer">
          <div class="text-center text-muted py-3">⏳ กำลังโหลดข้อมูลผู้สมัคร...</div>
        </div>

      </div>
    </div>
  </div>
</div>




<!-- 🟦 Modal งานทั้งหมด -->
<div class="modal fade" id="allJobsModal" tabindex="-1" aria-labelledby="allJobsLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="allJobsLabel">📋 งานทั้งหมด</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- 🔍 ส่วนค้นหา -->
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] === 'user'): ?>
  <div class="p-3 mb-3 rounded" style="background:#f4f8ff;"> 
    <h5 class="text-center fw-bold text-primary mb-3">ค้นหางานที่คุณต้องการ</h5> 

    <div class="row g-2 justify-content-center"> 
      <div class="col-md-5"> 
        <input type="text" id="searchKeyword" class="form-control" placeholder="ชื่องาน หรือ คำค้นหา"> 
      </div> 
      <div class="col-md-4"> 
        <input type="text" id="searchLocation" class="form-control" placeholder="จังหวัด หรือ สถานที่"> 
      </div> 
      <div class="col-md-2"> 
        <button id="btnSearchJob" class="btn btn-primary w-100">ค้นหา</button> 
      </div> 
    </div> 

    <!-- 🧭 หมวดหมู่งาน --> 
    <div class="text-center mt-3">
      <h6 class="fw-bold text-primary mb-2">หมวดหมู่งานยอดนิยม</h6>
      <div id="jobCategories" class="d-flex flex-wrap justify-content-center gap-2">
        ⏳ กำลังโหลดหมวดหมู่...
      </div>
    </div>
  </div>
<?php endif; ?>


        <!-- 💡 ปุ่มหางานที่เหมาะกับคุณ -->
<div class="text-end mb-3">
  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
    <!-- ✅ แสดงเฉพาะเมื่อเป็นผู้ใช้ทั่วไป -->
    <button id="matchJobsBtn" 
            class="btn-ai-match px-4 py-2 rounded-pill shadow-sm fw-bold">
      💡 หางานที่เหมาะกับคุณ
    </button>

  <?php elseif (!isset($_SESSION['role'])): ?>
    <!-- 🚫 ยังไม่ล็อกอิน -->
    <button id="matchJobsBtn" 
            class="btn-ai-match px-4 py-2 rounded-pill shadow-sm fw-bold" 
            disabled 
            title="กรุณาเข้าสู่ระบบก่อน">
      🔒 กรุณาเข้าสู่ระบบเพื่อดูงานที่เหมาะกับคุณ
    </button>

  <?php endif; ?>
</div>



        <!-- 🔙 ปุ่มย้อนกลับ (อยู่ตรงนี้!) -->
        <div class="text-start mb-2">
          <button id="backToAllJobsBtn" class="btn btn-outline-secondary btn-sm rounded-pill" style="display:none;">
            🔙 ย้อนกลับ
          </button>
        </div>

        <!-- 📋 รายการงาน -->
        <div id="jobListContainer">
          <div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>
        </div>

      </div>
    </div>
  </div>
</div>





<!-- 🔹 Modal: รายละเอียดผู้สมัคร -->
<div class="modal fade" id="applicantDetailModal" tabindex="-1" aria-labelledby="applicantDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="applicantDetailLabel">รายละเอียดผู้สมัคร</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="applicantDetailContent">
        ⏳ กำลังโหลด...
      </div>
    </div>
  </div>
</div>

<!-- ✅ Modal: ข้อมูลผู้หางาน -->
<div class="modal fade" id="jobSeekerModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content shadow-lg border-0">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-person"></i> ข้อมูลผู้หางาน</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="jobSeekerContent">
        <div class="text-center py-4">⏳ กำลังโหลดข้อมูล...</div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
      </div>

    </div>
  </div>
</div>




<!-- Chat Box -->
<div id="chatBox" class="card shadow" 
     style="position: fixed; bottom: 20px; right: 20px; width: 300px; max-height: 400px; display:none; z-index:1050; border-radius:10px; overflow:hidden;">
  <div class="card-header bg-info text-white d-flex justify-content-between align-items-center" style="cursor:pointer;">
    <span>💬 แชท</span>
    <button id="closeChat" class="btn btn-sm btn-light">&times;</button>
  </div>
  <!-- ส่วนหัวห้องแชท (ปุ่มย้อนกลับ) -->
<div id="chatHeader" class="d-none p-2 border-bottom bg-light d-flex justify-content-between align-items-center">
  <button class="btn btn-outline-secondary btn-sm" id="backToApplicantList">⬅️ ย้อนกลับ</button>
  <strong>💬 ห้องแชท</strong>
</div>

<!-- ส่วนข้อความแชท -->
<div class="card-body p-0" style="height:300px; overflow-y:auto;" id="chatContent">
  <div class="text-center text-muted py-2">⏳ โหลดแชท...</div>
</div>

  <div class="card-footer p-2 d-flex">
    <input type="text" id="chatInput" class="form-control me-2" placeholder="พิมพ์ข้อความ...">
    <button class="btn btn-primary" id="sendChatBtn" disabled>ส่ง</button>
  </div>
</div>



<!-- 🧩 Modal โปรไฟล์ -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content profile-box">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="profileModalLabel">โปรไฟล์ส่วนตัว</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body text-center">
        <!-- รูปโปรไฟล์ -->
        <div class="avatar-section mb-3">
          <img id="profileAvatar" src="uploads/avatars/default.png" class="rounded-circle border shadow-sm" width="120" height="120" alt="avatar">
          <div class="mt-2">
            <label for="avatarUpload" class="btn btn-outline-primary btn-sm">เปลี่ยนรูป</label>
            <input type="file" id="avatarUpload" name="avatar_file" class="d-none" accept="image/*">
          </div>
        </div>

        <!-- ข้อมูลบัญชี -->
        <div class="mb-3 text-start position-relative">
          <label class="form-label fw-bold">ชื่อผู้ใช้</label>
          <div class="input-group">
            <input type="text" id="profileUsername" class="form-control text-center bg-light" readonly>
            <button type="button" id="editUsernameBtn" class="btn btn-outline-primary">✏️</button>
          </div>
        </div>

        <!-- เปลี่ยนรหัสผ่าน -->
<div class="password-section text-start">
  <div class="mb-2 d-flex align-items-center justify-content-center">
    <div class="d-inline-flex align-items-center">
      <h6 class="fw-bold text-primary mb-0 me-2">เปลี่ยนรหัสผ่าน 🔒</h6>
      <button type="button" id="editPasswordBtn" class="btn btn-outline-primary btn-sm p-1" style="line-height:1;">✏️</button>
    </div>
  </div>

          <!-- 🔐 ฟอร์มเปลี่ยนรหัสผ่าน -->
  <div id="passwordEditFields" class="d-none">

    <!-- รหัสผ่านเดิม -->
    <div class="mb-2 position-relative">
      <input type="password" id="oldPassword" class="form-control pe-5" placeholder="รหัสผ่านเดิม">
      <button type="button" class="btn btn-sm btn-light position-absolute top-50 end-0 translate-middle-y me-2 border-0 toggle-password">
        <i class="bi bi-eye"></i>
      </button>
    </div>

    <!-- รหัสผ่านใหม่ -->
    <div class="mb-3 position-relative">
      <input type="password" id="newPassword" class="form-control pe-5" placeholder="รหัสผ่านใหม่">
      <button type="button" class="btn btn-sm btn-light position-absolute top-50 end-0 translate-middle-y me-2 border-0 toggle-password">
        <i class="bi bi-eye"></i>
      </button>
    </div>

    <button id="changePasswordBtn" class="btn btn-success w-100">
      💾 บันทึกรหัสผ่านใหม่
    </button>
  </div>

        </div>
      </div>
    </div>
  </div>
</div>



<!-- 🔍 Modal: รายละเอียดงาน -->
<div class="modal fade" id="jobDetailModal" tabindex="-1" aria-labelledby="jobDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="jobDetailLabel">รายละเอียดงาน</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="jobDetailContent">
        <div class="text-center text-muted py-4">⏳ กำลังโหลด...</div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-success d-none" id="applyJobBtn">สมัครงานนี้</button>
      </div>
    </div>
  </div>
</div>



<!-- 🟦 Modal เพิ่มงาน -->
<div class="modal fade" id="addJobModal" tabindex="-1" aria-labelledby="addJobLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="addJobLabel">📝 เพิ่มประกาศงานใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="addJobForm" enctype="multipart/form-data">
        <div class="modal-body">

          <!-- โลโก้บริษัท -->
          <div class="mb-3 text-center">
            <label class="form-label fw-bold">โลโก้บริษัท</label><br>
            <div id="logoPreview"
                 style="width:120px; height:120px; border:2px dashed #0d6efd;
                        display:flex; align-items:center; justify-content:center;
                        margin:auto; border-radius:10px; overflow:hidden;">
              <span class="text-muted">เลือกรูป</span>
            </div>
            <input type="file" name="logo" id="logoInput" class="form-control mt-2" accept="image/*">
          </div>

          <!-- 🏢 ชื่อบริษัท -->
          <div class="mb-3">
            <label class="form-label">ชื่อบริษัท</label>
            <div class="input-group">
              <input type="text" name="company_name" class="form-control"
                     value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" readonly>
              <button type="button" class="btn btn-outline-primary edit-company-btn" data-field="company_name">✏️</button>
            </div>
          </div>

          <!-- 🧾 ประเภทธุรกิจ -->
          <div class="mb-3">
            <label class="form-label">ประเภทธุรกิจ</label>
            <div class="input-group">
              <input type="text" name="business_type" class="form-control"
                     value="<?= htmlspecialchars($company['business_type'] ?? '') ?>" readonly>
              <button type="button" class="btn btn-outline-primary edit-company-btn" data-field="business_type">✏️</button>
            </div>
          </div>

          <!-- ชื่อตำแหน่งงาน -->
          <div class="mb-3">
            <label class="form-label fw-bold">ชื่อตำแหน่งงาน</label>
            <input type="text" name="job_title" class="form-control" placeholder="เช่น เจ้าหน้าที่บัญชี, โปรแกรมเมอร์" required>
          </div>

          <!-- ✅ คุณสมบัติ (มีน้ำหนักความสำคัญ) -->
          <div class="mb-3">
            <label class="form-label fw-bold">คุณสมบัติ</label>
            <div id="qualifications-list"></div>
            <button type="button" class="btn btn-outline-success btn-sm mt-2" id="addQualificationBtn">
              ➕ เพิ่มคุณสมบัติ
            </button>
          </div>


          <!-- ✅ สวัสดิการ (Dynamic Input) -->
          <div class="mb-3">
            <label class="form-label fw-bold">สวัสดิการ</label>
            <div id="benefits-list"></div>
            <button type="button" class="btn btn-outline-success btn-sm mt-2" id="addBenefitBtn">➕ เพิ่มสวัสดิการ</button>
          </div>

          <!-- รายละเอียดงาน -->
          <div class="mb-3">
            <label class="form-label fw-bold">รายละเอียดงาน</label>
            <textarea name="job_description" class="form-control" rows="4" placeholder="อธิบายรายละเอียดงาน"></textarea>
          </div>

          <!-- เงินเดือน / สถานที่ทำงาน -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">เงินเดือน (บาท)</label>
              <input type="number" name="salary" class="form-control" placeholder="เช่น 25000" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">สถานที่ทำงาน</label>
              <input type="text" name="location" class="form-control" placeholder="เช่น กรุงเทพฯ, เชียงใหม่" required>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">💾 บันทึกงาน</button>
        </div>
      </form>

    </div>
  </div>
</div>






<!-- Floating Button -->
<?php if(isset($_SESSION['role'])): ?>
<button id="chatFloatBtn" class="btn btn-primary rounded-circle shadow"
        style="position: fixed; bottom: 80px; right: 20px; width:60px; height:60px; z-index:1050; font-size:24px;">
  💬
</button>
<?php endif; ?>



<?php
$role = $_SESSION['role'] ?? ''; // ดึง role จาก session
?>

<?php if ($role == '' || $role == 'user'): ?>
  <!-- 🧍‍♀️ สำหรับผู้ใช้ทั่วไปและผู้สมัคร -->
  <div class="search-bar">
  <h2>ค้นหางานที่คุณต้องการ</h2>
  <input type="text" id="searchKeywordHome" placeholder="ชื่องาน หรือ คำค้นหา">
  <input type="text" id="searchLocationHome" placeholder="จังหวัด หรือ สถานที่">
  <button id="btnSearchJobHome">ค้นหา</button>
</div>

<div class="job-categories mt-3 text-center">
  <h2 class="fw-bold text-primary">หมวดหมู่งานยอดนิยม</h2>
  <div id="homeJobCategories" class="d-flex justify-content-center flex-wrap gap-2 mt-2">
      ⏳ กำลังโหลด...
  </div>
</div>

<div class="container mt-4">
  <div id="homeJobList" class="row justify-content-center g-3"></div>
</div>






<?php elseif ($role == 'company'): ?> 
<!-- 🏢 หน้าบริษัท: ค้นหาผู้สมัคร -->
<div class="search-bar">
  <h2>ผู้สมัครที่คุณต้องการ</h2>
  <input type="text" id="searchApplicantSkill" placeholder="ทักษะ หรือ คำค้นหา">
  <input type="text" id="searchApplicantEdu" placeholder="วุฒิการศึกษา หรือ ประสบการณ์ทำงาน">
  <button id="btnSearchApplicant" type="button">ค้นหา</button>
</div>
  </div>
</div>


<!-- 🏷 หมวดหมู่ทักษะ -->
<div class="text-center mt-4">
  <h3 class="fw-bold text-primary">หมวดหมู่ทักษะผู้สมัคร</h3>
  <div id="skillCategories" class="d-flex flex-wrap justify-content-center gap-2 mt-3">
      ⏳ กำลังโหลด...
  </div>
</div>

<!-- 📌 พื้นที่แสดงการ์ดผู้สมัคร -->
<div id="applicantList" class="container mt-4 text-center">
    ⏳ กำลังโหลดข้อมูลผู้สมัคร...
</div>

<?php endif; ?>



<!-- 🔔 Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;" id="toast-container"></div>

<!-- Toast แจ้งเตือน -->
<div id="toast" class="toast">เข้าสู่ระบบสำเร็จ!</div>

<!-- 🔊 เสียงแจ้งเตือน -->
<audio id="notif-sound" preload="auto">
  <source src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" type="audio/ogg">
</audio>

<div id="alert-container" class="container mt-3"></div>
<div id="alert-container"></div>


<div class="job-listings">

 <?php 
 include('db_server.php');
  if (!isset($_SESSION['role'])) {
    // ผู้ใช้งานทั่วไป
    echo '<h2>งานมาใหม่</h2>'; 
$query = "SELECT * FROM jobs ORDER BY created_at DESC LIMIT 5";  
$result = mysqli_query($conn, $query); 

while ($row = mysqli_fetch_assoc($result)) {   
    // 🖼 โลโก้ 
    $logo = !empty($row['logo']) ? $row['logo'] : 'default-logo.png';  

    // ✅ เริ่มกล่องงานแบบ Flex แถวเดียว (กรอบสวย)
    echo '<div class="job shadow-sm rounded d-flex align-items-start p-3 mb-3" 
              style="background:#fff; border:1px solid #ddd;">';  

    // ✅ โลโก้อยู่ซ้าย 
    echo '<div class="job-logo" style=" 
            width:70px; 
            height:70px; 
            border:1px solid #ddd; 
            border-radius:10px; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
            overflow:hidden; 
            background:#fafafa;
            margin-right:20px;
        "> 
            <img src="' . htmlspecialchars($logo) . '" style="max-width:100%; max-height:100%; object-fit:contain;"> 
          </div>'; 

    // ✅ ข้อมูลงานอยู่ขวา (ใช้ class เดียวกับ user)
    echo '<div class="job-content flex-grow-1">';
    echo '<h5 class="text-primary mb-1">' . htmlspecialchars($row['job_title']) . '</h5>';   
    echo '<div class="text-muted mb-1">' . htmlspecialchars($row['company_name']) . '</div>';  
    echo '<small class="text-secondary">📍 ' . htmlspecialchars($row['location']) . 
         ' | 💰 ' . number_format($row['salary']) . ' บาท</small><br>'; 
    echo '<button class="btn btn-outline-success btn-sm rounded-pill me-2 view-job-detail"
        data-id="' . $row['id_jobs'] . '">
    🔍 ดูรายละเอียด
</button>';
    echo '</div>'; 

    echo '</div>'; // ปิดการ์ดงาน
}


}
  if (isset($_SESSION['role']) && $_SESSION['role'] == 'user') {
      echo '<h2>งานมาใหม่</h2>';
      echo '<div id="latest-jobs">';
      // PHP จะเติมงานล่าสุด
      echo '</div>';
  }

  if (isset($_SESSION['role']) && $_SESSION['role'] == 'company') {
      echo '<h2>ผู้สมัครล่าสุด</h2>';
      echo '<div id="latest-applicants">';
      // PHP จะเติมผู้สมัครล่าสุด
      echo '</div>';
  }
?>

  
  
</div>




  <footer>
    &copy; 2025 Job Online Market - เว็บไซต์หางานสำหรับทุกคน
  </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
<script src="script.js"></script>
<script>

$(document).ready(function(){

  // ✅ สลับแท็บผู้สมัคร / นายจ้าง
  $('#tab-user').on('click', function(){
    $('.tab-login').removeClass('active');
    $(this).addClass('active');
    $('#loginFormUser').removeClass('d-none');
    $('#loginFormCompany').addClass('d-none');
  });

  $('#tab-company').on('click', function(){
    $('.tab-login').removeClass('active');
    $(this).addClass('active');
    $('#loginFormCompany').removeClass('d-none');
    $('#loginFormUser').addClass('d-none');
  });

  // 🔹 Login สำหรับ "ผู้สมัครงาน"
  $("#loginFormUser").submit(function(e){
    e.preventDefault();
    $.post("process_login.php", $(this).serialize() + "&role=user", function(response){
      if(response.status === "success"){
        showToast("เข้าสู่ระบบผู้สมัครสำเร็จ ✅", "success");
        setTimeout(() => location.reload(), 2000);
      } else {
        showToast("❌ " + response.message, "error");
      }
    }, "json");
  });

  // 🔹 Login สำหรับ "นายจ้าง / บริษัท"
  $("#loginFormCompany").submit(function(e){
    e.preventDefault();
    $.post("process_login.php", $(this).serialize() + "&role=company", function(response){
      if(response.status === "success"){
        showToast("เข้าสู่ระบบนายจ้างสำเร็จ ✅", "success");
        setTimeout(() => location.reload(), 2000);
      } else {
        showToast("❌ " + response.message, "error");
      }
    }, "json");
  });
// 🧑‍💼 สมัครสมาชิกผู้ใช้ทั่วไป
$(document).on('submit', '#registerUserForm', function(e) {
  e.preventDefault();

  const password = $('#password').val().trim();
  const confirm = $('#confirm_password').val().trim();

  // ✅ ตรวจรหัสผ่านก่อนส่ง
  if (password === '' || confirm === '') {
    showToast("⚠️ กรุณากรอกรหัสผ่านให้ครบ", "warning");
    return false;
  }

  if (password !== confirm) {
    showToast("❌ รหัสผ่านไม่ตรงกัน", "error");
    return false;
  }

  // ✅ ส่งข้อมูลไปยัง process_register.php
  const formData = $(this).serialize() + '&role=user'; // เพิ่ม role=user

  $.post('process_register.php', formData, function(res) {
    if (res.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'สมัครสมาชิกสำเร็จ 🎉',
        text: 'คุณสามารถเข้าสู่ระบบได้ทันที',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#28a745'
      }).then(() => {
        $('#registerModal').modal('hide');
        $('#loginModal').modal('show');
      });
    } else {
      Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถสมัครสมาชิกได้', 'error');
    }
  }, 'json').fail(() => {
    Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
  });
});

  // 🔹 สมัครสมาชิก (ตรวจรหัสผ่านตรงกัน)
  const registerForm = document.getElementById('registerUserForm');

  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      const password = document.getElementById('password').value.trim();
      const confirm = document.getElementById('confirm_password').value.trim();

      if (password === '' || confirm === '') {
        e.preventDefault();
        showToast("⚠️ กรุณากรอกรหัสผ่านให้ครบ", "warning");
        return false;
      }

      if (password !== confirm) {
        e.preventDefault();
        showToast("❌ รหัสผ่านไม่ตรงกัน", "error");
        return false;
      }
    });
  }

  // 👁 ฟังก์ชันโชว์/ซ่อนรหัสผ่าน
  $('.toggle-password').on('click', function () {
    const input = $(this).siblings('input');
    const icon = $(this).find('i');

    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('bi-eye').addClass('bi-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('bi-eye-slash').addClass('bi-eye');
    }
  });
  });


// 🏢 สมัครสมาชิกบริษัท
$(document).on('submit', '#registerCompanyForm', function(e) {
  e.preventDefault();

  const formData = $(this).serialize() + '&role=company'; // ✅ กำหนด role เป็น company

  $.post('process_register.php', formData, function(res) {
    if (res.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'สมัครสมาชิกสำเร็จ 🎉',
        text: 'คุณสามารถเข้าสู่ระบบได้ทันที',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#28a745'
      }).then(() => {
        $('#registerCompanyModal').modal('hide');
        $('#loginModal').modal('show');
      });
    } else {
      Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถสมัครสมาชิกได้', 'error');
    }
  }, 'json').fail(() => {
    Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
  });
});



  // 🔐 ตรวจสอบรหัสผ่านตรงกันก่อนสมัคร
  $('#registerCompanyForm').on('submit', function (e) {
    const pass = $('#companyPassword').val().trim();
    const confirm = $('#companyConfirmPassword').val().trim();

    if (pass === '' || confirm === '') {
      e.preventDefault();
      showToast("⚠️ กรุณากรอกรหัสผ่านให้ครบ", "warning");
      return false;
    }

    if (pass !== confirm) {
      e.preventDefault();
      showToast("❌ รหัสผ่านไม่ตรงกัน", "error");
      return false;
    }
  });




/* ✅ ฟังก์ชันแสดง toast */
function showToast(message, type){
  let toast = $("#toast");
  toast.text(message);
  toast.css({
    backgroundColor: type === "error" ? "#dc3545" : "#28a745",
    color: "#fff",
    padding: "12px 20px",
    borderRadius: "8px",
    position: "fixed",
    top: "20px",
    right: "20px",
    zIndex: "9999",
    display: "none"
  }).fadeIn(200);

  setTimeout(() => toast.fadeOut(400), 2500);
}








//เอาไว้ดึงAPI เสียงและสีดึงแจ้งเตือนอัตโนมัติ
function showToast(message, type="info") {
  let toastId = "toast-" + Date.now();
  let bgClass = "text-bg-info"; // ค่าเริ่มต้น (ฟ้า)

  if (type === "success") bgClass = "text-bg-success";
  if (type === "warning") bgClass = "text-bg-warning";
  if (type === "danger")  bgClass = "text-bg-danger";
  

  let toastHtml = `
    <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 mb-2 shadow" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          🔔 ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;
  $("#toast-container").append(toastHtml);

  let toastEl = document.getElementById(toastId);
  let bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
  bsToast.show();

  // 🔊 เล่นเสียง
  document.getElementById("notif-sound").play();

  // ลบออกหลัง Toast หาย
  toastEl.addEventListener("hidden.bs.toast", () => {
    $(toastEl).remove();
  });
}



function fetchNotifications() {
  $.get("fetch_notifications.php", function(res){
    try {
      let notifs = JSON.parse(res);
      if (notifs.length > 0 && document.visibilityState === "visible") {
        // อ่าน displayedNotifs จาก localStorage
        let displayedNotifs = new Set(JSON.parse(localStorage.getItem("displayedNotifs") || "[]"));

        notifs.forEach(n => {
          if (!displayedNotifs.has(n.id)) {
            displayedNotifs.add(n.id);

            let type = "info";
            if (n.message.includes("❌")) type = "danger";
            else if (n.message.includes("⭐")) type = "warning";
            else if (n.message.includes("🎉")) type = "success";

            showToast(n.message, type);
          }
        });

        // เก็บกลับไป
        localStorage.setItem("displayedNotifs", JSON.stringify([...displayedNotifs]));
      }
    } catch(e) {
      console.error("JSON parse error:", e, res);
    }
  });
}


// ดึงทุก 5 วิ
setInterval(fetchNotifications, 5000);



// โหลดการแจ้งเตือนเมื่อเปิด Modal
$('#notifModal').on('show.bs.modal', function () {
  $('#notif-content').html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');
  $.get('notifications.php', function(res){
    $('#notif-content').html(res);
  }).fail(function(){
    $('#notif-content').html('<div class="alert alert-danger">❌ โหลดข้อมูลไม่สำเร็จ</div>');
  });
});

// ปุ่ม "อ่านแล้ว"
$(document).on('click', '.mark-read-btn', function(){
  const btn = $(this);
  const notifId = btn.data('id');

  $.get('read_notification.php', { id: notifId }, function(res){
    if (res.status === 'success') {
      btn.closest('.list-group-item').removeClass('list-group-item-warning');
      btn.remove();
       // อัปเดต badge
    }
  }, 'json');
});

// อัปเดต badge จำนวน unread
function updateBadge() {
  $.get('fetch_unread_count.php', function(res){
    const data = typeof res === 'string' ? JSON.parse(res) : res;
    if (data.count > 0) {
      $('#notif-badge').text(data.count).show();
    } else {
      $('#notif-badge').hide();
    }
  });
}

updateBadge();
setInterval(updateBadge, 5000);




//ดึงสถานะแบบ real-time
function updateJobStatus(jobId) {
  $.get('check_application_status.php', { job_id: jobId }, function(res) {
    try {
      let data = JSON.parse(res);
      let badge = $('#job-status-' + jobId);
      let btn = $('#apply-btn-' + jobId);

      if (data.applied) {
        // ถ้าเคยสมัคร ให้แสดง badge
        if (badge.length === 0) {
          // แทนที่ปุ่มด้วย badge
          btn.replaceWith('<span id="job-status-' + jobId + '" class="badge bg-info text-dark">📌 สถานะ: ' + data.status + '</span>');
        } else {
          // อัปเดตข้อความ badge
          badge.text('📌 สถานะ: ' + data.status);
        }
      } else {
        // ถ้ายังไม่สมัคร แสดงปุ่ม
        if (btn.length === 0) {
          if (badge.length > 0) {
            badge.replaceWith('<button id="apply-btn-' + jobId + '" class="btn btn-primary btn-sm shadow-sm apply-btn" data-id="' + jobId + '">สมัครงาน</button>');
          }
        }
      }
    } catch(e) {
      console.error('JSON parse error:', e, res);
    }
  });
}

// อัปเดตทุก 5 วินาที
setInterval(function() {
  $('.apply-btn, [id^=job-status-]').each(function() {
    let jobId = $(this).attr('data-id') || $(this).attr('id').split('-')[2];
    updateJobStatus(jobId);
  });
}, 5000);

// ---------------- User: อัปเดตงานล่าสุด ----------------
function fetchUserJobs() {
    $.get('fetch_latest_jobs.php', function(res) {
        $('#latest-jobs').html(res);
    });
}

// ---------------- Company: อัปเดตผู้สมัครล่าสุด ----------------
function fetchLatestApplicants() {
    $.get('fetch_latest_applicants.php', function(res) {
        $('#latest-applicants').html(res);
    });
}

// ---------------- User: สมัครงาน ----------------
// 📨 สมัครงานจากปุ่มในหน้าแรก (หรือหน้าอื่นที่ไม่ใช่ modal รายละเอียด)
$(document).on('click', '.apply-btn', function () {
  const jobId = $(this).data('id');
  const $btn = $(this);

  $.post('apply_job.php', { job_id: jobId }, function (res) {
    try {
      const data = JSON.parse(res);

      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'สมัครงานเรียบร้อย!',
          text: 'ระบบได้บันทึกการสมัครของคุณแล้ว 🎉',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });

        // อัปเดตปุ่มหลังสมัครสำเร็จ
        $btn.removeClass('btn-primary')
            .addClass('btn-secondary')
            .prop('disabled', true)
            .text('สมัครแล้ว (รอการตอบกลับ)');
      } 
      else if (data.msg === 'กรุณากรอกประวัติส่วนตัวก่อนสมัครงาน') {
        Swal.fire({
          icon: 'warning',
          title: 'กรอกประวัติก่อนสมัคร',
          text: 'คุณต้องกรอกข้อมูลส่วนตัวก่อนจึงจะสมัครงานได้',
          confirmButtonText: 'ไปที่โปรไฟล์',
          confirmButtonColor: '#f39c12'
        }).then(() => {
          window.location.href = 'profilejob.html';
        });
      } 
      else {
        Swal.fire('ผิดพลาด', data.msg, 'error');
      }
    } catch (e) {
      Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถส่งข้อมูลได้', 'error');
    }
  });
});










// ---------------- Company: เปลี่ยนสถานะผู้สมัคร ----------------



// เรียกครั้งแรก + interval ทุก 5 วินาที
if (!sessionRole || sessionRole == 'user') fetchUserJobs();
if (sessionRole == 'company') fetchLatestApplicants();

setInterval(() => {
    if (!sessionRole || sessionRole == 'user') fetchUserJobs();
    if (sessionRole == 'company') fetchLatestApplicants();
}, 5000);



// 🔁 ฟังก์ชันอัปเดตสถานะ (ของคุณ)
function updateJobStatus(jobId) {
  $.get('check_application_status.php', { job_id: jobId }, function(res) {
    console.log('status response:', res); // 🧩 ดูข้อมูลจริงจากเซิร์ฟเวอร์
    try {
      let data = JSON.parse(res);
      let badge = $('#job-status-' + jobId);
      let btn = $('#apply-btn-' + jobId);

      if (data.applied) {
        if (badge.length === 0) {
          btn.replaceWith('<span id="job-status-' + jobId + '" class="badge bg-info text-dark">📌 สถานะ: ' + data.status + '</span>');
        } else {
          badge.text('📌 สถานะ: ' + data.status);
        }
      }
    } catch(e) {
      console.error('JSON parse error:', e, res);
    }
  });
}





//chat

let selectedJobId = null;
let selectedReceiverId = null;
let chatInterval = null;
let autoScroll = true;
// 👇 เพิ่มตัวบอกหน้าปัจจุบัน
let currentView = 'list'; // 'list' | 'applicants' | 'chat'


// เปิดกล่องแชท
$('#chatFloatBtn').click(function(){
  $('#chatBox').show();
  $(this).hide();
  resetChat();
  loadChatTargets();
});

// ปิดกล่องแชท
$('#closeChat').click(function(){
  $('#chatBox').hide();
  $('#chatFloatBtn').show();
  resetChat();
});

// รีเซ็ตค่า (ใช้ทุกครั้งที่ออกจากแชท)
function resetChat(){
  selectedJobId = null;
  selectedReceiverId = null;
  currentView = 'list';
  $('#sendChatBtn').prop('disabled', true);
  $('#chatContent').html('<div class="text-center text-muted py-2">⏳ โหลดแชท...</div>');
  if (chatInterval) { clearInterval(chatInterval); chatInterval = null; }
}

// ----- รายการหลัก -----
function loadChatTargets(){
  $.get('fetch_jobs_for_chat.php', function(res){
    // ✅ ถ้าเป็น user ให้มีหัวข้อ, ถ้าเป็น company ให้ไม่แสดงหัว
    let header = '';
    if (sessionRole === 'user') {
      header = `
        <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
          <strong>📋 เลือกงาน/คู่สนทนา</strong>
        </div>`;
    }
    $('#chatContent').html(`
      ${header}
      <div class="p-2">${res}</div>
    `);
  });
}

// บริษัทกดเลือก "งานของฉัน" → ไปหน้าผู้สมัครของงานนั้น
$(document).on('click', '.select-job-for-applicants', function(){
  const jobId = $(this).data('jobid');
  currentView = 'applicants';
  if (chatInterval) { clearInterval(chatInterval); chatInterval = null; }
  $('#sendChatBtn').prop('disabled', true);
  $.get('fetch_applicants_for_job.php', { job_id: jobId }, function(res){
    $('#chatContent').html(res);
    // เก็บ job ไว้สำหรับกลับจากห้องแชท
    selectedJobId = jobId;
    selectedReceiverId = null;
  });
});

// 🔙 ย้อนกลับจาก "รายชื่อผู้สมัคร" → กลับไปเลือกงาน/คู่สนทนา
$(document).on('click', '#backToJobList', function(){
  if (chatInterval) { clearInterval(chatInterval); chatInterval = null; }
  // รีเซ็ต state เพื่อกัน fetchChat วิ่งซ้ำ
  selectedJobId = null;
  selectedReceiverId = null;
  loadChatTargets();
});

// เข้าแชท
$(document).on('click', '.select-chat-target', function(){
  selectedJobId = $(this).data('jobid');
  selectedReceiverId = $(this).data('receiverid');
  currentView = 'chat';
  $('#sendChatBtn').prop('disabled', false);

  $('#chatHeader').removeClass('d-none');

  if (chatInterval) { clearInterval(chatInterval); }
  fetchChat(); // โหลดครั้งแรก
  chatInterval = setInterval(fetchChat, 2000);
});

// 🔙 ย้อนกลับจากห้องแชท → กลับไปหน้าผู้สมัครของงานเดิม (ถ้าบริษัท) หรือกลับหน้า list (ถ้า user)
$(document).on('click', '#backToApplicantList', function(){
  if (chatInterval) { clearInterval(chatInterval); chatInterval = null; }
  $('#sendChatBtn').prop('disabled', true);
  $('#chatHeader').addClass('d-none');
  // ถ้ามี jobId (ฝั่งบริษัท) กลับไปหน้าผู้สมัครของงานนั้น
  if (selectedJobId && sessionRole === 'company') {
    currentView = 'applicants';
    $.get('fetch_applicants_for_job.php', { job_id: selectedJobId }, function(res){
      $('#chatContent').html(res);
      selectedReceiverId = null;
      // คง selectedJobId ไว้เพื่อให้ยังอยู่ในงานเดิม
    });
  } else {
    // ฝั่ง user กลับหน้า list
    selectedJobId = null;
    selectedReceiverId = null;
    loadChatTargets();
  }
});

// ✅ ฟังก์ชัน fetchChat()
function fetchChat(){
  if (!selectedJobId || !selectedReceiverId) return;

  $.get('fetch_chat.php', { job_id: selectedJobId, receiver_id: selectedReceiverId }, function(res){
    $('#chatContent').html(res);
    if (autoScroll) $('#chatContent').scrollTop($('#chatContent')[0].scrollHeight);
  });
}

// ✅ ส่งข้อความเมื่อกดปุ่ม
$('#sendChatBtn').click(function(){
  if (!selectedJobId || !selectedReceiverId) return;
  const msg = $('#chatInput').val().trim();
  if (msg === '') return;

  $.post('send_chat.php', {
      message: msg,
      job_id: selectedJobId,
      receiver_id: selectedReceiverId
  }, function(res){
      const data = (typeof res === 'string') ? JSON.parse(res) : res;
      if (data.status === 'success') {
          $('#chatInput').val('');
          fetchChat();
      }
  });
});

// ✅ ส่งข้อความเมื่อกด Enter
$('#chatInput').on('keypress', function(e) {
    if (e.which === 13 && !e.shiftKey) {
        e.preventDefault();
        $('#sendChatBtn').click();
    }
});


// autoScroll flag
$('#chatContent').on('scroll', function(){
  const el = $(this)[0];
  autoScroll = el.scrollTop + el.clientHeight >= el.scrollHeight - 10;
});





let prevUnread = 0;

function fetchUnreadChat() {
    $.get('fetch_unread_chat_count.php', function(res){
        let data = (typeof res === 'string') ? JSON.parse(res) : res;
        let count = data.count || 0;

        if(count > prevUnread){
            let newMsg = count - prevUnread;
            showToast(`💬 มีข้อความใหม่ ${newMsg} ข้อความ`, 'info');
            document.getElementById("notif-sound").play();
        }
        prevUnread = count;
    });
}


// เมื่อคลิกปุ่มส่งข้อความ
$(document).on('click', '.send-message-btn', function() {
  const jobId = $(this).data('jobid');
  const receiverId = $(this).data('receiverid');
  const hrName = $(this).data('hrname');

  if (!jobId || !receiverId) {
    alert("❌ ข้อมูลไม่ครบ (jobId หรือ receiverId ว่าง)");
    return;
  }

  // ✅ เปิดกล่องแชท
  $('#chatBox').show();
  $('#chatFloatBtn').hide();
  $('#allJobsModal').modal('hide');

  // ตั้งค่าตัวแปร
  selectedJobId = jobId;
  selectedReceiverId = receiverId;
  currentView = 'chat';

  // ✅ layout ของห้องแชท (ไม่มีปุ่มย้อนกลับ)
  $('#chatContent').html(`
    <div id="chatHeader" class="p-2 border-bottom bg-light text-center">
      <strong>💬 แชทกับ ${hrName}</strong>
    </div>
    <div id="chatMessages" style="height:230px; overflow-y:auto;" class="p-2 text-muted text-center">
      ⏳ กำลังโหลดแชท...
    </div>
  `);

  // ✅ โหลดข้อความครั้งแรก
  loadChatMessages();

  // ✅ อัปเดตทุก 2 วินาที
  if (chatInterval) clearInterval(chatInterval);
  chatInterval = setInterval(loadChatMessages, 2000);

  // เปิดปุ่มส่ง
  $('#sendChatBtn').prop('disabled', false);
});

// ---------------- ฟังก์ชันโหลดข้อความ ----------------
function loadChatMessages() {
  if (!selectedJobId || !selectedReceiverId) return;

  $.get('fetch_chat.php', { job_id: selectedJobId, receiver_id: selectedReceiverId }, function(res) {
    const box = $('#chatMessages');
    const shouldScroll = box.scrollTop() + box.innerHeight() >= box[0].scrollHeight - 10;

    // ✅ อัปเดตเฉพาะข้อความ
    box.html(res);

    // ✅ scroll เฉพาะตอนอยู่ล่างสุด
    if (shouldScroll) {
      box.scrollTop(box[0].scrollHeight);
    }
  });
}




// เรียกทุก 5 วินาที
setInterval(fetchUnreadChat, 5000);
fetchUnreadChat(); // เรียกครั้งแรกตอนโหลด

//หน้า ดูประวัติ
$(document).ready(function () {

  // 🟢 โหลดโปรไฟล์เมื่อเปิด modal
  $('#profileModal').on('show.bs.modal', function () {
    $.get('fetch_profile.php', function (data) {
      if (data.status === 'success') {
        $('#profileUsername').val(data.username);
        $('#profileAvatar').attr('src', data.avatar);
      }
    }, 'json');
  });

  // 🔊 ฟังก์ชัน toast + เสียง
  function showToast(message, type = 'success') {
  let container = $('#toast-container');
  let sound = $('#notif-sound')[0];

  // ล้าง toast เดิมก่อน
  container.find('.toast').remove();

  // 🔧 สร้าง toast element ใหม่
  let toast = $(`
    <div class="toast align-items-center text-white border-0 show" 
         role="alert" aria-live="assertive" aria-atomic="true"
         style="min-width: 260px; animation: fadeIn 0.3s;">
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `);

  // สีพื้นตามประเภท
  if (type === 'success') {
    toast.addClass('bg-success');
    sound.play();
  } else {
    toast.addClass('bg-danger');
  }

  // เพิ่มเข้า container
  container.append(toast);

  // ⏳ ให้ toast อยู่ได้ 2 วิ แล้วค่อยจางหาย
  setTimeout(() => {
    toast.fadeOut(400, function () {
      $(this).remove();
    });
  }, 2000);
}


  // 🖼️ เปลี่ยนรูปโปรไฟล์
  // เปลี่ยนเฉพาะบล็อกอัปโหลดรูป
$('#avatarUpload').on('change', function (e) {
  const file = e.target.files[0];
  if (!file) return;

  // preview ทันที
  $('#profileAvatar').attr('src', URL.createObjectURL(file));

  const formData = new FormData();
  formData.append('avatar_file', file);

  $.ajax({
    url: 'upload_avatar.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    dataType: 'json',              // <<< สำคัญ
    success: function (data) {     // <<< ได้เป็น object เลย
      if (data.status === 'success') {
        showToast('✅ เปลี่ยนรูปโปรไฟล์สำเร็จ', 'success');
        refreshUserDropdown();
        // reload modal ให้ดึง path ล่าสุด
        $('#profileModal').modal('hide');
        setTimeout(() => $('#profileModal').modal('show'), 300);
      } else {
        showToast('❌ ' + (data.msg || 'อัปโหลดไม่สำเร็จ'), 'error');
      }
    },
    error: function (xhr) {
      console.error('upload error:', xhr.responseText);
      showToast('🚨 อัปโหลดรูปไม่สำเร็จ (เครือข่าย/รูปแบบ)', 'error');
    }
  });
});

  // ✏️ แก้ไขชื่อผู้ใช้
  $('#editUsernameBtn').click(function () {
    const input = $('#profileUsername');
    const btn = $(this);

    if (input.prop('readonly')) {
      input.prop('readonly', false).focus().removeClass('bg-light');
      btn.text('💾');
    } else {
      const newUsername = input.val().trim();
      if (!newUsername) {
        showToast('⚠️ กรุณากรอกชื่อผู้ใช้', 'error');
        return;
      }

      $.post('update_username.php', { username: newUsername }, function (data) {
        if (data.status === 'success') {
          showToast('✅ เปลี่ยนชื่อผู้ใช้สำเร็จ', 'success');
          refreshUserDropdown();
          input.prop('readonly', true).addClass('bg-light');
          btn.text('✏️');

          $('#profileModal').modal('hide');
          setTimeout(() => $('#profileModal').modal('show'), 300);
        } else {
          showToast('❌ ' + data.msg, 'error');
        }
      }, 'json').fail(() => showToast('🚨 ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์', 'error'));
    }
  });

  // ✏️ เปิด/ปิดฟอร์มเปลี่ยนรหัสผ่าน
  $('#editPasswordBtn').click(function () {
    $('#passwordEditFields').toggleClass('d-none');
    $(this).text($(this).text() === '✏️' ? '❌' : '✏️');
  });

  // 💾 บันทึกรหัสผ่านใหม่
  $('#changePasswordBtn').click(function () {
    const oldPass = $('#oldPassword').val().trim();
    const newPass = $('#newPassword').val().trim();

    if (!oldPass || !newPass) {
      showToast('⚠️ กรุณากรอกรหัสผ่านให้ครบ', 'error');
      return;
    }

    $.post('update_password.php', { old_password: oldPass, new_password: newPass }, function (data) {
      if (data.status === 'success') {
        showToast('✅ เปลี่ยนรหัสผ่านสำเร็จ', 'success');
        $('#passwordEditFields').addClass('d-none');
        $('#oldPassword,#newPassword').val('');
        $('#editPasswordBtn').text('✏️');

        $('#profileModal').modal('hide');
        setTimeout(() => $('#profileModal').modal('show'), 300);
      } else {
        showToast('❌ ' + data.msg, 'error');
      }
    }, 'json').fail(() => showToast('🚨 ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์', 'error'));
  });


  // 🎯 ดึงข้อมูล user เรียลไทม์
function refreshUserDropdown() {
  $.get('fetch_profile.php', function (data) {
    if (data.status === 'success') {
      // ✅ อัปเดตรูปโปรไฟล์
      $('.user-dropdown-btn img').attr('src', data.avatar + '?t=' + Date.now());
      
      // ✅ อัปเดตเฉพาะชื่อในปุ่ม dropdown
      $('.user-dropdown-btn .username-text').text(data.username);
    }
  }, 'json');
}

setInterval(refreshUserDropdown, 10000);



  // แก้ไข field ทีละตัว
  $(document).on('click', '.edit-field', function(){
    let field = $(this).data('field');
    let currentValue = $(`#view-${field}`).text();

    if(field === 'avatar'){
      $(`#view-avatar`).html(`<input type="file" id="input-avatar" accept="image/*">
        <button class="btn btn-sm btn-success save-field" data-field="assvatar">บันทึก</button>
        <button class="btn btn-sm btn-secondary cancel-field" data-field="avatar">ยกเลิก</button>`);
    } else if(field === 'resume_file'){
      $(`#view-resume`).html(`<input type="file" id="input-resume_file" accept=".pdf,.doc,.docx">
        <button class="btn btn-sm btn-success save-field" data-field="resume_file">บันทึก</button>
        <button class="btn btn-sm btn-secondary cancel-field" data-field="resume_file">ยกเลิก</button>`);
    } else {
      $(`#view-${field}`).html(`<input type="text" id="input-${field}" value="${currentValue}">
        <button class="btn btn-sm btn-success save-field" data-field="${field}">บันทึก</button>
        <button class="btn btn-sm btn-secondary cancel-field" data-field="${field}">ยกเลิก</button>`);
    }
  });

  // บันทึก
  $(document).on('click', '.save-field', function(){
    let field = $(this).data('field');
    let formData = new FormData();
    formData.append('user_id', <?= $_SESSION['user_id'] ?? 0 ?>);

    if(field === 'avatar'){
      let file = $('#input-avatar')[0].files[0];
      if(file) formData.append('avatar', file);
    } else if(field === 'resume_file'){
      let file = $('#input-resume_file')[0].files[0];
      if(file) formData.append('resume_file', file);
    } else {
      formData.append('field', field);
      formData.append('value', $(`#input-${field}`).val());
    }

    $.ajax({
      url: 'save_profile_field.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res){
        let data = JSON.parse(res);
        if(data.status === 'success'){
        loadProfile();
        showToast('บันทึกเรียบร้อย', 'success');

        // ✅ ถ้ามี path ของ avatar ใหม่ → อัปเดตภาพบน Navbar ทันที
        if (data.avatar) {
          // ✅ เปลี่ยนทุก avatar พร้อมกัน (เล็ก+ใหญ่)
          $('.avatar-img').attr('src', data.avatar + '?t=' + new Date().getTime());
        }
        } else {
          showToast('❌ เกิดข้อผิดพลาด', 'error');
        }
      }
    });
  });

  // ยกเลิก
  $(document).on('click', '.cancel-field', function(){
    loadProfile();
  });
});


//เพิ่ม แก้ไข ประวัติส่วนตัว
$(document).ready(function() {
  // เพิ่มช่องลักษณะงาน
  // 🟢 ปุ่มเพิ่มช่อง (ป้องกันซ้ำด้วย .off().on())
$(document).on('click', '#addDesiredJobBtn', function() {
  $('#desired-jobs-list').append(`
    <div class="input-group mb-2">
      <input type="text" name="desired_jobs[]" class="form-control" placeholder="ตำแหน่งที่ต้องการ" required>
      <button type="button" class="btn btn-outline-danger remove-field">✖</button>
    </div>
  `);
});

$(document).on('click', '#addEducationBtn', function() {
  $('#education-list').append(`
    <div class="border rounded p-2 mb-2 bg-light">
      <input type="text" class="form-control mb-1" name="education_school[]" placeholder="ชื่อสถาบัน" required>
      <input type="text" class="form-control mb-1" name="education_degree[]" placeholder="วุฒิการศึกษา / สาขา" required>
      <input type="text" class="form-control mb-1" name="education_year[]" placeholder="ปีที่จบ">
      <button type="button" class="btn btn-sm btn-outline-danger remove-field">ลบ</button>
    </div>
  `);
});

$(document).on('click', '#addWorkBtn', function() {
  $('#work-list').append(`
    <div class="border rounded p-2 mb-2 bg-light">
      <input type="text" class="form-control mb-1" name="work_company[]" placeholder="ชื่อบริษัท / สถานที่ฝึกงาน" required>
      <input type="text" class="form-control mb-1" name="work_position[]" placeholder="ตำแหน่ง" required>
      <input type="text" class="form-control mb-1" name="work_year[]" placeholder="ระยะเวลา (เช่น 2022–2024)">
      <button type="button" class="btn btn-sm btn-outline-danger remove-field">ลบ</button>
    </div>
  `);
});

$(document).on('click', '#addCertificateBtn', function() {
  $('#certificate-list').append(`
    <div class="input-group mb-2">
      <input type="text" name="certificates[]" class="form-control" placeholder="ชื่อหลักสูตร / ใบประกาศ">
      <button type="button" class="btn btn-outline-danger remove-field">✖</button>
    </div>
  `);
});

$(document).on('click', '#addLanguageBtn', function() {
  $('#language-list').append(`
    <div class="input-group mb-2">
      <input type="text" name="languages[]" class="form-control" placeholder="ชื่อภาษา เช่น อังกฤษ จีน ญี่ปุ่น">
      <button type="button" class="btn btn-outline-danger remove-field">✖</button>
    </div>
  `);
});

$(document).on('click', '#addSkillBtn', function() {
  $('#skill-list').append(`
    <div class="input-group mb-2">
      <input type="text" name="skills[]" class="form-control" placeholder="เช่น Microsoft Office, Photoshop, ขับรถยนต์ได้">
      <button type="button" class="btn btn-outline-danger remove-field">✖</button>
    </div>
  `);
});

$(document).on('click', '#addReferenceBtn', function() {
  $('#reference-list').append(`
    <div class="border rounded p-2 mb-2 bg-light">
      <input type="text" class="form-control mb-1" name="reference_name[]" placeholder="ชื่อ-นามสกุล" required>
      <input type="text" class="form-control mb-1" name="reference_contact[]" placeholder="ข้อมูลติดต่อ (เบอร์โทร / อีเมล)">
      <button type="button" class="btn btn-sm btn-outline-danger remove-field">ลบ</button>
    </div>
  `);
});

// ลบช่อง
$(document).on('click', '.remove-field', function() {
  $(this).closest('div').remove();
});



// ส่วนแก้ไข
  
$(document).ready(function(){

  // 🧾 โหลดข้อมูลเมื่อเปิด Modal
$('#personalProfileModal').on('show.bs.modal', function(){
  $.get('fetch_personal_profile.php', function(res){
    try {
      let data = JSON.parse(res);

      // 🟦 1. ข้อมูลส่วนบุคคล
// 🟦 1. ข้อมูลส่วนบุคคล
const personal = ['fullname', 'email', 'phone', 'address', 'age', 'gender'];
personal.forEach(f => {
  const input = $('[name="'+f+'"]');
  const value = data[f] ?? '';

  if (input.is('select')) {
    // 🟣 ช่องเพศ (select)
    input.val(value);

    if (value.trim() !== '') {
      // ✅ ถ้ามีข้อมูล → ล็อกไว้
      input.prop('disabled', true).addClass('bg-light');
    } else {
      // ❌ ถ้ายังไม่มีข้อมูล → ปลดล็อก
      input.prop('disabled', false).removeClass('bg-light');
    }

  } else {
    // 🟤 ช่อง input ปกติ
    input.val(value);

    if (value.trim() !== '') {
      // ✅ มีข้อมูล → ล็อกไว้
      input.prop('readonly', true).addClass('bg-light');
    } else {
      // ❌ ยังไม่มีข้อมูล → พิมพ์ได้
      input.prop('readonly', false).removeClass('bg-light');
    }
  }
});

      // 🟢 แสดงค่าเพศที่เคยเลือกไว้ แต่ไม่ล็อกไว้
$('select[name="gender"]').val(data.gender || '').prop('disabled', false).removeClass('bg-light');


      // ✅ อายุ
      $('input[name="age"]').val(data.age || '').prop('readonly', !!data.age);

      // ✅ ลักษณะงานที่ต้องการ
      $('#desired-jobs-list').empty();
      if (Array.isArray(data.desired_jobs) && data.desired_jobs.length > 0) {
        data.desired_jobs.forEach(job => {
          $('#desired-jobs-list').append(`
            <div class="input-group mb-2">
              <input type="text" name="desired_jobs[]" value="${job}" class="form-control bg-light" readonly>
            </div>
          `);
        });
        if (!$('#desired-jobs-list').next('.editable-btn').length) {
          $('#desired-jobs-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="desired_jobs">✏️</button>`);
        }
      } else {
        $('#desired-jobs-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // ✅ การศึกษา
      $('#education-list').empty();
      if (data.education && Array.isArray(data.education.school) && data.education.school.length > 0) {
        for (let i=0; i<data.education.school.length; i++){
          $('#education-list').append(`
            <div class="border rounded p-2 mb-2 bg-light">
              <input type="text" name="education_school[]" value="${data.education.school[i] ?? ''}" class="form-control mb-1" readonly>
              <input type="text" name="education_degree[]" value="${data.education.degree[i] ?? ''}" class="form-control mb-1" readonly>
              <input type="text" name="education_year[]" value="${data.education.year[i] ?? ''}" class="form-control mb-1" readonly>
            </div>
          `);
        }
        if (!$('#education-list').next('.editable-btn').length) {
          $('#education-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="education">✏️</button>`);
        }
      } else {
        $('#education-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // ✅ ประสบการณ์ทำงาน / ฝึกงาน
      $('#work-list').empty();
      if (data.work_experience && Array.isArray(data.work_experience.company) && data.work_experience.company.length > 0) {
        for (let i=0; i<data.work_experience.company.length; i++){
          $('#work-list').append(`
            <div class="border rounded p-2 mb-2 bg-light">
              <input type="text" name="work_company[]" value="${data.work_experience.company[i] ?? ''}" class="form-control mb-1" readonly>
              <input type="text" name="work_position[]" value="${data.work_experience.position[i] ?? ''}" class="form-control mb-1" readonly>
              <input type="text" name="work_year[]" value="${data.work_experience.year[i] ?? ''}" class="form-control mb-1" readonly>
            </div>
          `);
        }
        if (!$('#work-list').next('.editable-btn').length) {
          $('#work-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="work_experience">✏️</button>`);
        }
      } else {
        $('#work-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // ✅ ใบประกาศ
      $('#certificate-list').empty();
      if (Array.isArray(data.certificates) && data.certificates.length > 0) {
        data.certificates.forEach(c => {
          $('#certificate-list').append(`
            <div class="input-group mb-2">
              <input type="text" name="certificates[]" value="${c}" class="form-control bg-light" readonly>
            </div>
          `);
        });
        if (!$('#certificate-list').next('.editable-btn').length) {
          $('#certificate-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="certificates">✏️</button>`);
        }
      } else {
        $('#certificate-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // ✅ ภาษา
      $('#language-list').empty();
      if (Array.isArray(data.languages) && data.languages.length > 0) {
        data.languages.forEach(lang => {
          $('#language-list').append(`
            <div class="input-group mb-2">
              <input type="text" name="languages[]" value="${lang}" class="form-control bg-light" readonly>
            </div>
          `);
        });
        if (!$('#language-list').next('.editable-btn').length) {
          $('#language-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="languages">✏️</button>`);
        }
      } else {
        $('#language-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // ✅ ความสามารถอื่น ๆ
      $('#skill-list').empty();
      if (Array.isArray(data.skills) && data.skills.length > 0) {
        data.skills.forEach(s => {
          $('#skill-list').append(`
            <div class="input-group mb-2">
              <input type="text" name="skills[]" value="${s}" class="form-control bg-light" readonly>
            </div>
          `);
        });
        if (!$('#skill-list').next('.editable-btn').length) {
          $('#skill-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="skills">✏️</button>`);
        }
      } else {
        $('#skill-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // ✅ บุคคลอ้างอิง
      $('#reference-list').empty();
      if (data.ref_contacts && Array.isArray(data.ref_contacts.name) && data.ref_contacts.name.length > 0) {
        for (let i=0; i<data.ref_contacts.name.length; i++){
          $('#reference-list').append(`
            <div class="border rounded p-2 mb-2 bg-light">
              <input type="text" name="reference_name[]" value="${data.ref_contacts.name[i] ?? ''}" class="form-control mb-1" readonly>
              <input type="text" name="reference_contact[]" value="${data.ref_contacts.contact[i] ?? ''}" class="form-control mb-1" readonly>
            </div>
          `);
        }
        if (!$('#reference-list').next('.editable-btn').length) {
          $('#reference-list').after(`<button type="button" class="btn btn-outline-primary editable-btn" data-field="ref_contacts">✏️</button>`);
        }
      } else {
        $('#reference-list').html(`<div class="text-muted">ยังไม่มีข้อมูล</div>`);
      }

      // 🧾 ✅ แสดงไฟล์เรซูเม่เดิม
      const resumeInput = $('input[name="resume_file"]');
      if (data.resume_file && data.resume_file !== '') {
        resumeInput.hide();
        if ($('#resume-view-link').length === 0) {
          resumeInput.after(`
            <div id="resume-view-link" class="mt-2">
              📄 <a href="${data.resume_file}" target="_blank" class="text-primary text-decoration-none">
                ดูเรซูเม่ของคุณ
              </a>
              <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="changeResumeBtn">เปลี่ยนไฟล์</button>
            </div>
          `);
        }
      } else {
        $('#resume-view-link').remove();
        resumeInput.show();
      }

    } catch (err) {
      console.error('JSON parse error:', err, res);
    }
  });
});

// 🔹 ถ้ากด “เปลี่ยนไฟล์”
$(document).on('click', '#changeResumeBtn', function(){
  $('#resume-view-link').remove();
  $('input[name="resume_file"]').show().val('');
});

  

  
  // ❌ ลบ input ที่เพิ่ม
  $(document).on('click', '.remove-field', function(){
    $(this).closest('div').remove();
  });



// ✅ ปุ่ม ✏️ แก้ไข input
$(document).on('click', '.editable-btn', function() {
  let btn = $(this);
  let field = btn.data('field');

  // 🟢 ปลดล็อก input ตามประเภท field
  switch(field) {
    case 'desired_jobs':
      $('#desired-jobs-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'education':
      $('#education-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'work_experience':
      $('#work-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'certificates':
      $('#certificate-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'languages':
      $('#language-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'skills':
      $('#skill-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'ref_contacts':
      $('#reference-list input').prop('readonly', false).removeClass('bg-light');
      break;
    case 'gender':
      $('select[name="gender"]').prop('disabled', false).removeClass('bg-light').focus();
      break;

    case 'age': // 🆕 เพิ่มกรณีอายุ
      $('input[name="age"]').prop('readonly', false).removeClass('bg-light').focus();
      break;
    default:
      $('input[name="'+field+'"]').prop('readonly', false).removeClass('bg-light').focus();
  }

  // 🔄 เปลี่ยนปุ่มเป็น “บันทึก”
  btn.text('💾 บันทึก')
     .removeClass('btn-outline-primary editable-btn')
     .addClass('btn-success save-edit');
});


// ✅ ปุ่ม 💾 บันทึกข้อมูล
$(document).on('click', '.save-edit', function() {
  let btn = $(this); 
  let field = btn.data('field'); 
  let value;

  // 🧩 เก็บค่าข้อมูลจากแต่ละกลุ่ม
  switch(field) {
    case 'desired_jobs':
      value = $('#desired-jobs-list input').map(function(){ return $(this).val(); }).get();
      break;
    case 'education':
      value = {
        school: $('input[name="education_school[]"]').map(function(){ return $(this).val(); }).get(),
        degree: $('input[name="education_degree[]"]').map(function(){ return $(this).val(); }).get(),
        year: $('input[name="education_year[]"]').map(function(){ return $(this).val(); }).get()
      };
      break;
    case 'work_experience':
      value = {
        company: $('input[name="work_company[]"]').map(function(){ return $(this).val(); }).get(),
        position: $('input[name="work_position[]"]').map(function(){ return $(this).val(); }).get(),
        year: $('input[name="work_year[]"]').map(function(){ return $(this).val(); }).get()
      };
      break;
    case 'certificates':
      value = $('input[name="certificates[]"]').map(function(){ return $(this).val(); }).get();
      break;
    case 'languages':
      value = $('input[name="languages[]"]').map(function(){ return $(this).val(); }).get();
      break;
    case 'skills':
      value = $('input[name="skills[]"]').map(function(){ return $(this).val(); }).get();
      break;
    case 'ref_contacts':
      value = {
        name: $('input[name="reference_name[]"]').map(function(){ return $(this).val(); }).get(),
        contact: $('input[name="reference_contact[]"]').map(function(){ return $(this).val(); }).get()
      };
      break;
    case 'gender':
      value = $('select[name="gender"]').val();
      break;

    case 'age': // 🆕 กรณีอายุ
      value = $('input[name="age"]').val();
      break;
    default:
      value = $('input[name="'+field+'"]').val();
  }

  // 📤 ส่งข้อมูลไป save_personal_field.php
  $.post('save_personal_field.php', { field, value: JSON.stringify(value) }, function(res){
    try {
      let data = JSON.parse(res);
      if (data.status === 'success') {
        showToast('✅ บันทึกสำเร็จ', 'success');

        // 🔒 ล็อก input กลับ
        switch(field) {
          case 'desired_jobs':
            $('#desired-jobs-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'education':
            $('#education-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'work_experience':
            $('#work-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'certificates':
            $('#certificate-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'languages':
            $('#language-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'skills':
            $('#skill-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'ref_contacts':
            $('#reference-list input').prop('readonly', true).addClass('bg-light');
            break;
          case 'gender':
            $('select[name="gender"]').prop('disabled', true).addClass('bg-light');
            break;

          case 'age': // 🆕 ล็อกอายุคืน
            $('input[name="age"]').prop('readonly', true).addClass('bg-light');
            break;
          default:
            $('input[name="'+field+'"]').prop('readonly', true).addClass('bg-light');
        }

        // 🔄 เปลี่ยนปุ่มกลับเป็น ✏️
        btn.text('✏️')
           .removeClass('btn-success save-edit')
           .addClass('btn-outline-primary editable-btn');
      } else {
        showToast('❌ บันทึกไม่สำเร็จ', 'danger');
      } 
    } catch(e) { 
      console.error('JSON error:', e, res); 
    }
  });
});




  
  // 💾 บันทึกข้อมูลทั้งหมด
  $(document).on('submit', '#personalProfileForm', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: 'save_personal_profile.php',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res){
        try {
          const data = JSON.parse(res);
          if (data.status === 'success') {
            showToast('✅ บันทึกข้อมูลสำเร็จ', 'success');
            $('#personalProfileModal').modal('hide');
            setTimeout(()=>$('#personalProfileModal').modal('show'), 400);
          } else {
            showToast('❌ '+data.msg, 'danger');
          }
        } catch(e){
          showToast('⚠️ ข้อผิดพลาดในการบันทึก', 'danger');
        }
      },
      error(){ showToast('🚨 ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์', 'danger'); }
    });
  });

});





  // ปุ่มลบช่อง
  $(document).on('click', '.remove-field', function() {
    $(this).closest('div').remove();
  });
});



// ✅ เมื่อคลิกชื่อผู้สมัคร
$(document).on('click', '.applicant-name', function() {
  const appId = $(this).data('appid');
  console.log('กำลังโหลดข้อมูลผู้สมัคร', appId);
  
  $('#applicantDetailModal').modal('show');
  $('#applicantDetailContent').html('<div class="text-center py-4">⏳ กำลังโหลด...</div>');

  $.get('fetch_applicant_detail.php', { app_id: appId }, function(res) {
    $('#applicantDetailContent').html(res);
  }).fail(function() {
    $('#applicantDetailContent').html('<div class="alert alert-danger">❌ โหลดข้อมูลไม่สำเร็จ</div>');
  });
});

// คลิกเปลี่ยนสถานะ (ใน/นอกโมดัล ใช้ร่วมกัน)
$(document).on('click', '.status-btn', function () {
  const appId = $(this).data('appid');
  const status = $(this).data('status');

  console.log('คลิกเปลี่ยนสถานะ -> appId:', appId, 'status:', status);

  if (!appId || !status) {
    Swal.fire('ข้อมูลไม่ครบ', 'ไม่พบรหัสใบสมัครหรือสถานะ', 'error');
    return;
  }

  Swal.fire({
    title: 'ยืนยันการเปลี่ยนสถานะ',
    text: `คุณต้องการเปลี่ยนสถานะเป็น "${status}" หรือไม่?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'ยืนยัน',
    cancelButtonText: 'ยกเลิก',
    reverseButtons: true
  }).then((result) => {
    if (!result.isConfirmed) return;

    $.post('update_status.php', { app_id: appId, status: status }, function (res) {
  console.log('update_status response:', res);

  if (res.status === 'success') {
    Swal.fire({
      icon: 'success',
      title: 'อัปเดตสำเร็จ',
      text: `สถานะถูกเปลี่ยนเป็น "${status}" แล้ว`,
      timer: 1500,
      showConfirmButton: false
    });

    // ✅ อัปเดต badge และ UI เหมือนเดิม
    let modalBadge = '';
    if (status === 'ไม่รับ') {
      modalBadge = '<span class="badge bg-danger">❌ ไม่รับ</span>';
    } else if (status === 'สนใจในตัวเขา') {
      modalBadge = '<span class="badge bg-warning text-dark">⭐ สนใจในตัวเขา</span>';
    } else if (status === 'รับเข้าทำงาน') {
      modalBadge = '<span class="badge bg-success">✅ รับเข้าทำงาน</span>';
    } else {
      modalBadge = `<span class="badge bg-info text-dark">📌 ${status}</span>`;
    }

    const modalStatus = $('#applicantDetailModal p span.badge');
    if (modalStatus.length) {
      modalStatus.replaceWith(modalBadge);
    } else {
      $('#applicantDetailModal p:contains("สถานะปัจจุบัน")').append(modalBadge);
    }

    const card = $(`.status-btn[data-appid='${appId}']`).closest('.applicant-card');
    const cardBadge = card.find('.badge');

    if (cardBadge.length) {
      cardBadge.replaceWith(modalBadge);
    } else {
      card.find('small.text-muted').after(modalBadge);
    }

    if (status !== 'รอการตอบกลับ') {
      card.find('.status-btn').remove();
    }

    if (typeof fetchLatestApplicants === 'function') fetchLatestApplicants();

  } else {
    Swal.fire('เกิดข้อผิดพลาด', res.msg || 'ไม่สามารถอัปเดตสถานะได้', 'error');
  }
}, 'json').fail(() => {
  Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์', 'error');
});

  });
});




/* ===========================
   ✅ Modal : งานทั้งหมด
=========================== */
$(document).ready(function () {

    $('#allJobsModal').on('show.bs.modal', function () {
        $.get('fetch_categories.php?mode=modal', function (res) {
            $("#jobCategories").html(res);
        });
        loadModalJobs();
    });

    function loadModalJobs(page = 1, keyword = '', location = '', category = '') {
        $('#jobListContainer').html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');
        $.get('fetch_all_jobs.php', { page, keyword, location, category, mode: "modal" }, function (res) {
            $('#jobListContainer').html(res);
        });
    }

    $(document).on('click', '.modal-page-btn', function (e) {
        e.preventDefault();
        loadModalJobs($(this).data('page'));
    });

    $(document).on('click', '.modal-category-btn', function () {
        $('.modal-category-btn').removeClass('btn-primary text-white').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary text-white');
        loadModalJobs(1, '', '', $(this).data('cat'));
    });

    $('#btnSearchJob').on('click', function () {
        loadModalJobs(1, $('#searchKeyword').val(), $('#searchLocation').val());
    });

});

/* ===========================
   ✅ หน้า Index
=========================== */
$(document).ready(function () {

    $.get('fetch_categories.php?mode=home', function (res) {
        $('#homeJobCategories').html(res);
    });

    loadHomeJobs();

    function loadHomeJobs(page = 1, keyword = '', location = '', category = '') {
        $("#homeJobList").html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');
        $.get('fetch_all_jobs.php', { page, keyword, location, category, mode: "home" }, function (res) {
            $("#homeJobList").html(res);
        });
    }

    $(document).on('click', '.home-page-btn', function (e) {
        e.preventDefault();
        loadHomeJobs($(this).data('page'));
    });

    $(document).on('click', '.home-category-btn', function () {
        $('.home-category-btn').removeClass('btn-primary text-white').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary text-white');
        loadHomeJobs(1, '', '', $(this).data('cat'));
    });

    $('#btnSearchJobHome').on('click', function () {
        loadHomeJobs(1, $('#searchKeywordHome').val(), $('#searchLocationHome').val());
    });

});


// โหลดงานแบบ AJAX
function loadHomeJobs(page = 1, keyword = '', location = '', category = '') {
    $('#homeJobList').html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');
    $.get('fetch_all_jobs.php', { page, keyword, location, category, home: 1 }, function (res) {
        $('#homeJobList').html(res);
    });
}



// ลบงาน
$(document).on('click', '.delete-job-btn', function () {
  const jobId = $(this).data('id');
  Swal.fire({
    title: 'ยืนยันการลบงาน?',
    text: 'เมื่อลบแล้วจะไม่สามารถกู้คืนได้!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33'
  }).then(result => {
    if (!result.isConfirmed) return;
    $.post('delete_job.php', { id: jobId }, res => {
      if (res.trim() === 'success') {
        Swal.fire('สำเร็จ', 'ลบงานเรียบร้อยแล้ว', 'success');
        $('#allJobsModal').modal('hide');
      } else {
        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถลบงานได้', 'error');
      }
    }).fail(() => Swal.fire('เกิดข้อผิดพลาด', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้', 'error'));
  });
});




// โหลดหน้าใหม่ใน modal งานทั้งหมด
$(document).on('click', '.load-page', function(e) {
  e.preventDefault();
  const page = $(this).data('page');
  $('#allJobsContent').html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');
  $.get('fetch_all_jobs.php', { page: page }, function(res) {
    $('#allJobsContent').html(res);
  });
});


// 🟢 เมื่อคลิกปุ่มดูรายละเอียด
// 🟢 รวมทั้ง .view-job-detail และ .view-job-btn ให้คลิกได้ทั้งคู่
// 🟢 เมื่อคลิกดูรายละเอียดงาน
$(document).on('click', '.view-job-detail', function() {
  const jobId = $(this).data('id');
  $('#jobDetailModal').modal('show');
  $('#jobDetailContent').html('<div class="text-center text-muted py-4">⏳ กำลังโหลด...</div>');

  $.get('fetch_job_detail.php', { id: jobId }, function(res) {
    $('#jobDetailContent').html(res);

    // ✅ ตรวจ role และสถานะ
    if (window.currentUserRole === 'user') {
      if (window.userApplied) {
        $('#applyJobBtn')
          .removeClass('d-none btn-success')
          .addClass('btn-secondary')
          .text('สมัครแล้ว (' + window.applyStatus + ')')
          .prop('disabled', true);
      } else {
        $('#applyJobBtn')
          .removeClass('d-none btn-secondary')
          .addClass('btn-success')
          .attr('data-id', jobId)
          .text('สมัครงานนี้')
          .prop('disabled', false);
      }
    } else {
      $('#applyJobBtn').addClass('d-none');
    }
  }).fail(() => {
    $('#jobDetailContent').html('<div class="alert alert-danger">❌ โหลดข้อมูลไม่สำเร็จ</div>');
  });
});

// 📨 สมัครงาน
$('#applyJobBtn').on('click', function() {
  const jobId = $(this).attr('data-id');

  $.post('apply_job.php', { job_id: jobId }, function(res) {
    try {
      const data = JSON.parse(res);

      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'สมัครงานเรียบร้อย!',
          text: 'ระบบได้บันทึกการสมัครของคุณแล้ว 🎉',
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        });

        $('#applyJobBtn')
          .removeClass('btn-success')
          .addClass('btn-secondary')
          .prop('disabled', true)
          .text('สมัครแล้ว (รอการตอบกลับ)');
      } 
      else if (data.msg === 'กรุณากรอกประวัติส่วนตัวก่อนสมัครงาน') {
        Swal.fire({
          icon: 'warning',
          title: 'กรอกประวัติก่อนสมัคร',
          text: 'คุณต้องกรอกข้อมูลส่วนตัวก่อนจึงจะสมัครงานได้',
          confirmButtonText: 'ไปที่โปรไฟล์',
          confirmButtonColor: '#f39c12'
        }).then(() => {
          window.location.href = 'profilejob.html'; // ← ลิงก์ไปหน้าแก้ไขประวัติ
        });
      } 
      else {
        Swal.fire('ผิดพลาด', data.msg, 'error');
      }

    } catch (e) {
      Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถส่งข้อมูลได้', 'error');
    }
  });
});




// เมื่อคลิก "หางานที่เหมาะกับคุณ"
$('#matchJobsBtn').on('click', function () {
  $('#backToAllJobsBtn').show(); // แสดงปุ่มย้อนกลับ
  $('#jobListContainer').html('<div class="text-center text-muted py-4">🤖 กำลังวิเคราะห์ความเหมาะสมของคุณ...</div>');
  
  $.get('match_jobs.php', function (res) {
    $('#jobListContainer').html(res);
  }).fail(() => {
    $('#jobListContainer').html('<div class="alert alert-danger">❌ เกิดข้อผิดพลาด</div>');
  });
});

// เมื่อคลิก "ย้อนกลับ"
$('#backToAllJobsBtn').on('click', function () {
  $('#backToAllJobsBtn').hide(); // ซ่อนปุ่มย้อนกลับ
  $('#jobListContainer').html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');
  
  $.get('fetch_all_jobs.php', function (res) {
    $('#jobListContainer').html(res);
  });
});


function checkJobStatus(jobId) {
    $.get('check_application_status.php', { job_id: jobId }, function(res) {
        let data = JSON.parse(res);

        if (data.applied) {
            $("#apply-btn-" + jobId).replaceWith(`
                <div class='text-center'>
                  <span class='badge bg-info text-dark rounded-pill d-block mb-2'>📌 สถานะ: `+data.status+`</span>
                  <button class='btn btn-outline-info btn-sm w-100 send-message-btn'
                      data-jobid='${jobId}' data-receiverid='${data.hr_id}' data-hrname='${data.hr_name}'>
                      💬 ส่งข้อความ
                  </button>
                </div>
            `);
        }
    });
}

// ✅ เช็คทุก 4 วินาที
setInterval(function() {
    $(".apply-btn").each(function(){
        checkJobStatus($(this).data("id"));
    });
}, 4000);




// 📋 เปิด modal แก้ไขงาน
$(document).on('click', '.edit-job-btn', function () {
  const jobId = $(this).data('id');

  $('#editJobForm')[0].reset();
  $('#edit-qualifications-list').empty();
  $('#edit-benefits-list').empty();
  $('#logo-preview').html('<small class="text-muted">กำลังโหลด...</small>');

  $.get('fetch_job_edit.php', { id: jobId }, function (res) {
    let data = JSON.parse(res);

    if (data.error) {
      alert('❌ ไม่พบข้อมูลงาน');
      return;
    }

    $('#edit-id').val(data.id_jobs);
    $('#edit-title').val(data.job_title);
    $('#edit-company').val(data.company_name);
    $('#edit-business').val(data.business_type);
    $('#edit-location').val(data.location);
    $('#edit-salary').val(data.salary);
    $('#edit-description').val(data.job_description);

    if (data.logo) {
      $('#logo-preview').html(`<img src="${data.logo}" width="100" class="border rounded">`);
    }

    // ✅ โหลดคุณสมบัติแบบ JSON ถ้าไม่ใช่ JSON จะไม่ Error
// ✅ โหลดคุณสมบัติแบบ Dynamic จากฐานข้อมูล
let quals = safeJSON(data.qualifications, []);
quals.forEach((q, i) => {
  $('#edit-qualifications-list').append(`
    <div class="input-group mb-2 qualification-item">
      <input type="text" name="qual_text[]" class="form-control" value="${q.text ?? ''}">
      <div class="ms-2">
        <label><input type="radio" name="qual_weight[${i}]" value="1" ${q.weight==1?"checked":""}> น้อย</label>
        <label><input type="radio" name="qual_weight[${i}]" value="3" ${q.weight==3?"checked":""}> ปานกลาง</label>
        <label><input type="radio" name="qual_weight[${i}]" value="5" ${q.weight==5?"checked":""}> มาก</label>
      </div>
      <button class="btn btn-outline-danger remove-item">🗑️</button>
    </div>
  `);
});


// ✅ โหลดสวัสดิการ
let bens = safeJSON(data.benefits, []);
bens.forEach(b => {
  $('#edit-benefits-list').append(`
    <div class="input-group mb-2">
      <input type="text" name="benefits[]" class="form-control" value="${b}">
      <button class="btn btn-outline-danger remove-item">🗑️</button>
    </div>
  `);
});


    $('#allJobsModal').modal('hide');
    $('#editJobModal').modal('show');
  });
});


// ป้องกัน JSON.parse error เมื่อ data เป็น null / string ปกติ
function safeJSON(str, fallback = []) {
  try {
    const j = JSON.parse(str);
    return Array.isArray(j) ? j : fallback;
  } catch (e) {
    return fallback;
  }
}


// ✅ เพิ่มคุณสมบัติ (add + edit)
$(document).on('click', '#addQualificationBtn, #editAddQualificationBtn', function () {
  let index = $(this).closest('.modal-body').find('.qualification-item').length;
  const targetList = $(this).attr('id') === 'editAddQualificationBtn' ? '#edit-qualifications-list' : '#qualifications-list';

  $(targetList).append(`
    <div class="input-group mb-2 align-items-center qualification-item">
      <input type="text" name="qual_text[]" class="form-control" placeholder="ระบุคุณสมบัติ เช่น ใช้ Excel ได้ดี">
      <div class="ms-2">
        <label><input type="radio" name="qual_weight[${index}]" value="1" checked> น้อย</label>
        <label><input type="radio" name="qual_weight[${index}]" value="3"> ปานกลาง</label>
        <label><input type="radio" name="qual_weight[${index}]" value="5"> มาก</label>
      </div>
      <button class="btn btn-outline-danger ms-2 remove-item">🗑️</button>
    </div>
  `);
});


// ✅ เพิ่มสวัสดิการ
$(document).on('click', '#editAddBenefitBtn', function () {
  $('#edit-benefits-list').append(`
    <div class="input-group mb-2 align-items-center">
      <input type="text" name="benefits[]" class="form-control" placeholder="เช่น โบนัส, ประกันสังคม">
      <button class="btn btn-outline-danger ms-2 remove-item">🗑️</button>
    </div>
  `);
});

// ✅ ลบช่อง input
$(document).on('click', '.remove-item', function () {
  $(this).closest('.input-group').remove();
});

// ✅ Preview โลโก้ทันทีเมื่อเลือกไฟล์ใหม่
$(document).on('change', '#edit-logo', function () {
  const file = this.files[0];
  if (file) {
    let reader = new FileReader();
    reader.onload = function (e) {
      $('#logo-preview').html(`<img src="${e.target.result}" width="100" class="border rounded">`);
    };
    reader.readAsDataURL(file);
  }
});

$(document).on('change', '#logoInput', function () {
  const file = this.files[0];
  if (file) {
    let reader = new FileReader();
    reader.onload = function (e) {
      $('#logoPreview').html(`<img src="${e.target.result}" width="120" class="border rounded">`);
    };
    reader.readAsDataURL(file);
  }
});



// 💾 บันทึกการแก้ไข
$('#editJobForm').on('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append('ajax', true);

  $.ajax({
    url: 'update_job_ajax.php',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      if (res.trim() === 'success') {
        Swal.fire('✅ สำเร็จ', 'แก้ไขงานเรียบร้อยแล้ว', 'success');
        $('#editJobModal').modal('hide');
        // โหลดรายการงานใหม่
        $.get('fetch_all_jobs.php', function (html) {
          $('#allJobsContent').html(html);
        });
      } else {
        Swal.fire('❌ ผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
      }
    }
  });
});


// โหลดผู้สมัครทั้งหมด
// ✅ โหลดรายการผู้สมัคร
function fetchAllApplicants(keyword = '', job = '') {
  $('#applicantsListContainer').html('<div class="text-center text-muted py-3">⏳ กำลังโหลด...</div>');

  $.get('fetch_all_applicants.php', { keyword: keyword, job: job }, function(res) {
    $('#applicantsListContainer').html(res);
  }).fail(() => {
    $('#applicantsListContainer').html('<div class="alert alert-danger">❌ โหลดข้อมูลไม่สำเร็จ</div>');
  });
}

// โหลดเมื่อเปิด Modal
$('#allApplicantsModal').on('show.bs.modal', function() {
  fetchAllApplicants();
});

// ปุ่มค้นหา
$('#btnSearchApplicant').on('click', function() {
  const keyword = $('#searchApplicantKeyword').val().trim();
  const job = $('#searchJobFilter').val().trim();
  fetchAllApplicants(keyword, job);
});

// อัปเดตเรียลไทม์ทุก 5 วินาที
setInterval(() => {
  if ($('#allApplicantsModal').hasClass('show')) {
    fetchAllApplicants($('#searchApplicantKeyword').val().trim(), $('#searchApplicantJob').val().trim());
  }
}, 5000);

// ✅ เปิดดูข้อมูลผู้หางาน
$(document).on("click", ".view-jobseeker", function(){
    const userId = $(this).data("userid");

    $("#jobSeekerModal").modal("show");
    $("#jobSeekerContent").html("<div class='text-center py-4'>⏳ กำลังโหลด...</div>");

    $.get("fetch_jobseeker_detail.php", { user_id: userId }, function(html){
        $("#jobSeekerContent").html(html);
    }).fail(function(){
        $("#jobSeekerContent").html("<div class='text-center text-danger py-4'>❌ โหลดข้อมูลไม่สำเร็จ</div>");
    });
});


// ✅ แสดงตัวอย่างโลโก้
$('#logoInput').on('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(event) {
      $('#logoPreview').html(`<img src="${event.target.result}" width="100%" height="100%" style="object-fit:cover;">`);
    };
    reader.readAsDataURL(file);
  } else {
    $('#logoPreview').html('<span class="text-muted">เลือกรูป</span>');
  }
});


$('#addJobModal').on('show.bs.modal', function() {
  $.get('fetch_company_info.php', function(res) {
    let data = JSON.parse(res);
    $('input[name=company_name]').val(data.company_name);
    $('input[name=business_type]').val(data.business_type);
  });
});

// ✅ บันทึกงานใหม่ผ่าน AJAX
$('#addJobForm').on('submit', function(e) {
  e.preventDefault();

  const formData = new FormData(this);

  $.ajax({
    url: 'add_job.php',
    type: 'POST',
    data: formData,
    contentType: false,
    processData: false,
    success: function(res) {
      try {
        const data = JSON.parse(res);
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'เพิ่มงานสำเร็จ!',
            text: 'ระบบได้บันทึกข้อมูลของคุณแล้ว',
            confirmButtonColor: '#0d6efd'
          });
          $('#addJobModal').modal('hide');
          $('#addJobForm')[0].reset();
          $('#logoPreview').html('<span class="text-muted">เลือกรูป</span>');
        } else {
          Swal.fire('ผิดพลาด', data.msg || 'ไม่สามารถเพิ่มงานได้', 'error');
        }
      } catch (e) {
        Swal.fire('เกิดข้อผิดพลาด', 'รูปแบบข้อมูลไม่ถูกต้อง', 'error');
      }
    },
    error: function() {
      Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้', 'error');
    }
  });
});


//ข้อความแจ้งเตือนสถาณะ
$(document).on('click', '#applicantDetailModal .status-btn', function(){
  let status = $(this).data('status');
  showToast(`อัปเดตสถานะเป็น "${status}" แล้ว ✅`, 'success');
  $('#applicantDetailModal').modal('hide');
});

$(document).on('click', '.home-category-btn', function(){

    $('.home-category-btn').removeClass('active');
    $(this).addClass('active');

    loadHomeJobs(1, $('#searchKeyword').val(), $('#searchLocation').val(), $(this).data('cat'));
});

// ✅ โหลดหมวดหมู่ทักษะ & ผู้สมัครตอนเข้าเว็บ
$(document).ready(function(){
  $.get('fetch_skills.php', function(res){
    $("#skillCategories").html(res);
  });

  $("#applicantList").html(
  "<p class='text-muted py-4'>🔍 กรุณาเลือกหมวดหมู่ทักษะด้านบนก่อน</p>"
);

});

// ✅ คลิกปุ่มหมวดทักษะ
$(document).on('click', '.skill-btn', function(){
    $(".skill-btn").removeClass("btn-primary text-white").addClass("btn-outline-secondary");
    $(this).removeClass("btn-outline-secondary").addClass("btn-primary text-white");

    const skill = $(this).data("skill");
    loadApplicants(1, skill, '', '');
});

// ฟังก์ชันโหลดผู้สมัคร (เดิมของคุณ)
function loadApplicants(page = 1, skill = '', keyword = '', edu = '') {
  $("#applicantList").html("<p class='text-muted py-4'>⏳ กำลังโหลด...</p>");
  $.get("fetch_applicants_by_skill.php", { page, skill, keyword, edu }, function(res){
    $("#applicantList").html(res);
  });
}

// ฟังก์ชันรวม logic ค้นหา
function doApplicantSearch() {
  const skill = $(".skill-btn.btn-primary").data("skill") || '';
  const keyword = $("#searchApplicantSkill").val();
  const edu = $("#searchApplicantEdu").val();
  loadApplicants(1, skill, keyword, edu);
}

// ✅ ปุ่มค้นหา (ใช้ delegated + กัน default)
$(document).on('click', '#btnSearchApplicant', function(e){
  e.preventDefault();
  doApplicantSearch();
});

// ✅ กด Enter ในช่อง input ให้ทำงานเท่าปุ่ม
$(document).on('keydown', '#searchApplicantSkill, #searchApplicantEdu', function(e){
  if (e.key === 'Enter') {
    e.preventDefault();
    doApplicantSearch();
  }
});

// ✅ ถ้ามี form ครอบ search-bar ไว้ ดัก submit ไว้ด้วย
$(document).on('submit', '.search-bar form', function(e){
  e.preventDefault();
  doApplicantSearch();
});






// ✅ เมื่อคลิกชื่อ / ปุ่มดูรายละเอียด → เปิด modal ผู้สมัคร
$(document).on("click", ".interest-btn", function(){

    const userId = $(this).data("userid");
    if (!userId) return Swal.fire("ผิดพลาด", "ไม่พบรหัสผู้หางาน", "error");

    // ✅ ปิด modal ผู้หางานก่อน
    $("#jobSeekerModal").modal("hide");

    // ✅ รอให้ modal ปิดสนิทก่อนค่อยเปิด modal เลือกงาน
    setTimeout(() => {
        $("#inviteJobUserId").val(userId);
        $("#selectJobModal").modal("show");

        // ✅ โหลดรายการงานของบริษัท
        $("#jobSelectList").html("<p class='text-center py-3'>⏳ กำลังโหลด...</p>");
        $.get("fetch_company_jobs.php", function(res){
            $("#jobSelectList").html(res);
        });

    }, 300); // delay 0.3s กัน modal ซ้อน

});


//ปุ่มส่งคำเชิญงาน
$("#confirmSendInvite").click(function(){

    const userId = $("#inviteJobUserId").val();
    const jobId = $(".invite-job-radio:checked").val();

    if (!jobId) return Swal.fire("กรุณาเลือกงานด้วย", "", "warning");

    $.post("send_job_invite.php", { user_id: userId, job_id: jobId }, function(res){
        let data = JSON.parse(res);
        if (data.status === "success") {
            $("#selectJobModal").modal("hide");
            Swal.fire("✅ ส่งคำเชิญสำเร็จ!", "ผู้สมัครจะเห็นคำเชิญในกล่องแจ้งเตือน", "success");
        } else {
            Swal.fire("❌ ผิดพลาด", data.msg || "ระบบขัดข้อง", "error");
        }
    });

});





//รายละเอียดผู้สมัคร
$(document).on('click', '.view-applicant-detail', function(){
    let appId = $(this).data('appid');
    
    // โหลดข้อมูลจากไฟล์ PHP ที่จะดึงข้อมูลผู้สมัครแบบละเอียด
    $.get('fetch_applicant_detail.php', { app_id: appId }, function(res){
        $('#applicantDetailModal .modal-body').html(res);
        $('#applicantDetailModal').modal('show');
    });
});



// === ปลดล็อกปุ่มแก้ไขบริษัท/ประเภทธุรกิจ ===
$(document).on('click', '.edit-company-btn', function(){
  let field = $(this).data('field');
  let input = $('input[name="'+field+'"]');

  if (input.prop('readonly')) {
    input.prop('readonly', false).focus().addClass('editing');
    $(this).removeClass('btn-outline-primary').addClass('btn-success').text('✔️');
  } else {
    input.prop('readonly', true).removeClass('editing');
    $(this).removeClass('btn-success').addClass('btn-outline-primary').text('✏️');
  }
});




// === Dynamic Input: สวัสดิการ ===
$('#addBenefitBtn').on('click', function(){
  $('#benefits-list').append(`
    <div class="input-group mb-2 benefit-item">
      <input type="text" name="benefits[]" class="form-control" placeholder="เช่น ประกันสังคม, โบนัส, OT">
      <button class="btn btn-outline-danger remove-item">🗑️</button>
    </div>
  `);
});

// === ลบแถว input ===
$(document).on('click', '.remove-item', function(){
  $(this).closest('.input-group').remove();
});

// === Preview โลโก้บริษัท ===
$('#logoInput').on('change', function(e){
  let file = e.target.files[0];
  if (!file) return;
  $('#logoPreview').html(`<img src="${URL.createObjectURL(file)}" style="width:100%;height:100%;object-fit:cover;">`);
});






</script>


</body>
</html>
