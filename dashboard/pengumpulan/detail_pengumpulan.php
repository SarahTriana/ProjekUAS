<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pengajar','admin'])) {
    header("Location: ../../../views/login.php");
    exit;
}
 $isLogin = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);

 if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
include '../../../database/koneksi.php';

$user_id       = (int)$_SESSION['user_id'];
$role          = $_SESSION['role'];
$assignment_id = intval($_GET['assignment_id']);

// Ambil detail tugas dan course_id terkait
$sqlA = "
  SELECT a.deskripsi_tugas, a.tanggal_batas_akhir, a.poin_maksimal,
         l.nama_pelajaran, m.course_id
  FROM assignments a
  JOIN lessons l USING(lesson_id)
  JOIN modules m USING(module_id)
  WHERE a.assignment_id = $assignment_id
";
$resA = mysqli_query($conn, $sqlA);
$tugas = mysqli_fetch_assoc($resA);
if (!$tugas) exit('Tugas tidak ditemukan.');

// Validasi pengajar: harus mengajar course ini
if ($role === 'pengajar') {
    $check = mysqli_query($conn, "
      SELECT 1 FROM schedules sch
      JOIN enrollments e USING(schedule_id)
      WHERE sch.instructor_id = $user_id
        AND sch.course_id = {$tugas['course_id']}
      LIMIT 1
    ");
    if (mysqli_num_rows($check) === 0) exit('Tidak berwenang.');
}

// Hitung total siswa dari enrollments
if ($role === 'pengajar') {
    $resCnt = mysqli_query($conn, "
      SELECT COUNT(DISTINCT e.student_id) AS cnt
      FROM enrollments e
      JOIN schedules sch USING(schedule_id)
      WHERE sch.instructor_id = $user_id
        AND sch.course_id = {$tugas['course_id']}
    ");
    $totalS = mysqli_fetch_assoc($resCnt)['cnt'];
} else {
    $resAll = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE role='siswa'");
    $totalS = mysqli_fetch_assoc($resAll)['cnt'];
}

// Ambil submission siswa yang terdaftar ke schedule pengajar
$sqlS = "
  SELECT DISTINCT s.submission_id, s.student_id, s.tanggal_submit,
         s.file_submission_url, s.nilai, s.feedback_instructor,
         u.nama_lengkap
  FROM submissions s
  JOIN users u ON s.student_id = u.user_id
  JOIN enrollments e ON s.student_id = e.student_id
  JOIN schedules sch ON e.schedule_id = sch.schedule_id
  WHERE s.assignment_id = $assignment_id
";
// (tambahan kondisi)

if ($role === 'pengajar') {
    $sqlS .= " AND sch.instructor_id = $user_id AND sch.course_id = {$tugas['course_id']}";
}
$sqlS .= " ORDER BY s.tanggal_submit DESC";
$resS = mysqli_query($conn, $sqlS);
$submissions = mysqli_fetch_all($resS, MYSQLI_ASSOC);

$submitted = count($submissions);
$gradedRes = mysqli_query($conn, "
  SELECT COUNT(*) AS cnt
  FROM submissions
  WHERE assignment_id = $assignment_id
    AND nilai IS NOT NULL
");
$graded = mysqli_fetch_assoc($gradedRes)['cnt'];
$notSubmitted = $totalS - $submitted;
?>
<!-- HTML & UI sama seperti sebelumnya -->



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Sistem Pendaftaran Kursus Komputer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/dasboard.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            color: #2d3748;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #718096;
            font-size: 1.1rem;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            display: block;
        }

        .stat-label {
            color: #718096;
            font-size: 0.9rem;
        }

        .submission-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .submission-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .submission-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .student-info {
            display: flex;
            align-items: center;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .student-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .submission-date {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .submission-body {
            padding: 1.5rem;
        }

        .file-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }

        .file-icon {
            color: #667eea;
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }

        .file-name {
            font-weight: 600;
            color: #2d3748;
        }

        .file-size {
            color: #718096;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-submitted {
            background: #d4edda;
            color: #155724;
        }

        .status-graded {
            background: #cce5ff;
            color: #004085;
        }

        .status-late {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-download {
            background: var(--success-gradient);
            color: white;
        }

        .btn-feedback {
            background: var(--warning-gradient);
            color: white;
        }

        .btn-grade {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .feedback-form {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
            border: 2px solid #e9ecef;
        }

        .grade-input {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.8rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-align: center;
            width: 100px;
        }

        .grade-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .feedback-textarea {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            resize: vertical;
            min-height: 100px;
        }

        .feedback-textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .existing-feedback {
            background: rgba(102, 126, 234, 0.1);
            border-left: 4px solid #667eea;
            padding: 1rem;
            border-radius: 0 10px 10px 0;
            margin-top: 1rem;
        }

        .current-grade {
            background: var(--primary-gradient);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.2rem;
            display: inline-block;
            margin-left: 1rem;
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-btn {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 2px solid rgba(102, 126, 234, 0.2);
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            margin: 0.25rem;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: white;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .submission-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
      <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-laptop-code"></i>
            <h2>IT Learning</h2>
        </div>

        <div class="sidebar-menu">
            <!-- Menu Utama -->
            <div class="menu-category">Menu Utama</div>
            <div class="menu-item" onclick="window.location.href='../dashboard.php'">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </div>

            <!-- FITUR ADMINISTRATOR -->
            <?php if ($_SESSION['role'] == 'admin') : ?>
                <div class="menu-category">Administrator</div>

                <!-- 1) Manajemen Pengguna -->
                <div class="menu-item" onclick="toggleDropdown('user-dropdown')">
                    <i class="fas fa-users"></i>
                    <span>Manajemen User</span>
                    <i class="fas fa-chevron-down ml-auto" id="user-chevron"></i>
                </div>
                <div class="menu-dropdown" id="user-dropdown">
                    <div class="dropdown-item" onclick="window.location.href='../manajemen_user/data_siswa.php'">Data Siswa</div>
                    <div class="dropdown-item" onclick="window.location.href='../manajemen_user/data_pengajar.php'">Data Pengajar</div>
                </div>
            <?php endif; ?>

            <!-- 2) Manajemen Kursus -->
            <div class="menu-item " onclick="toggleDropdown('course-dropdown')">
                <i class="fas fa-book"></i>
                <span>Manajemen Kursus</span>
                <i class="fas fa-chevron-down ml-auto" id="course-chevron"></i>
            </div>
            <div class="menu-dropdown" id="course-dropdown">
                <div class="dropdown-item" onclick="window.location.href='../manajemen_kursus/daftar_kursus.php'">Daftar Kursus</div>
                <div class="dropdown-item" onclick="window.location.href='../manajemen_kursus/modul.php'">Modul & Materi</div>
                <div class="dropdown-item" onclick="window.location.href='../manajemen_kursus/pelajaran.php'">Pelajaran</div>
            </div>
            <!-- 3) Jadwal & Kelas -->
            <div class="menu-item" onclick="window.location.href='../jadwal/jadwal.php'">
                <i class="fas fa-calendar-alt"></i>
                <span>Jadwal Kelas</span>
            </div>

            <!-- 4) Pendaftaran -->
            <?php if ($_SESSION['role'] == 'admin') : ?>
                <div class="menu-item" onclick="window.location.href='../pendaftaran_kursus/pendaftaran.php'">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Pendaftaran Kursus</span>
                </div>
            <?php endif; ?>

            <!-- 5) Forum & Tugas -->
            <div class="menu-item active" onclick="toggleDropdown('activity-dropdown')">
                <i class="fas fa-tasks"></i>
                <span>Aktivitas Belajar</span>
                <i class="fas fa-chevron-down ml-auto" id="activity-chevron"></i>
            </div>
            <div class="menu-dropdown" id="activity-dropdown">
                <div class="dropdown-item" onclick="window.location.href='../forum/forum.php'">Forum Diskusi</div>
                <div class="dropdown-item" onclick="window.location.href='../tugas/tugas.php'">Tugas & Penilaian</div>
                <div class="dropdown-item" onclick="window.location.href='submission.php'">Submissions</div>
            </div>

            <!-- FITUR REPORT -->
            <div class="menu-category">Laporan</div>

            <?php if ($_SESSION['role'] == 'admin') : ?>
                
                <div class="menu-item" onclick="window.location.href='../pembayaran/laporan_pembayaran.php'">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Laporan Pembayaran</span>
                </div>
               
            <?php endif; ?>

            <!-- Laporan Sertifikat bisa dilihat semua role -->
            <div class="menu-item" onclick="window.location.href='../sertifikat/sertifikat.php'">
                <i class="fas fa-certificate"></i>
                <span>Laporan Sertifikat</span>
            </div>

            <div class="menu-item" onclick="window.location.href='../../../php/logout.php'">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log Out</span>
            </div>
        </div>

         
    </div>


    <div class="main-content" id="mainContent">
        <div class="header">
            <div class="header-title">
                <h1>Daftar Pengumpulan tugas</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-clipboard-list me-2"></i>
                Daftar Pengumpulan Tugas
            </h1>
            <p class="page-subtitle">Analisis Algoritma Sorting - Struktur Data</p>
        </div>

        <!-- Statistics Cards -->
       <div class="stats-cards">

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--primary-gradient);">
                <i class="fas fa-users"></i>
                </div>
                <span class="stat-number"><?= $totalS ?></span>
                <span class="stat-label">Total Siswa</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--success-gradient);">
                <i class="fas fa-check-circle"></i>
                </div>
                <span class="stat-number"><?= $submitted ?></span>
                <span class="stat-label">Sudah Submit</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--warning-gradient);">
                <i class="fas fa-star"></i>
                </div>
                <span class="stat-number"><?= $graded ?></span>
                <span class="stat-label">Sudah Dinilai</span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--danger-gradient);">
                <i class="fas fa-clock"></i>
                </div>
                <span class="stat-number"><?= $notSubmitted ?></span>
                <span class="stat-label">Belum Submit</span>
            </div>

        </div>


        <!-- Filter Section -->
      <div class="filter-section">
    <h5 class="mb-3">Filter Pengumpulan</h5>
    <div class="d-flex flex-wrap">
      <button class="filter-btn active" data-filter="all"><i class="fas fa-list me-1"></i>Semua</button>
      <button class="filter-btn" data-filter="submitted"><i class="fas fa-check me-1"></i>Sudah Submit</button>
      <button class="filter-btn" data-filter="graded"><i class="fas fa-star me-1"></i>Sudah Dinilai</button>
      <button class="filter-btn" data-filter="ungraded"><i class="fas fa-hourglass-half me-1"></i>Belum Dinilai</button>
      <button class="filter-btn" data-filter="late"><i class="fas fa-exclamation-triangle me-1"></i>Terlambat</button>
    </div>
  </div>

    <div id="submissionsList">
                <?php foreach ($submissions as $row):
                    $isLate = strtotime($row['tanggal_submit']) > strtotime($tugas['tanggal_batas_akhir']);
                    $status = is_null($row['nilai']) ? 'submitted' : 'graded';
                    if ($isLate) $status = 'late';
                ?>
        <div class="submission-card fade-in" data-status="<?= $status ?>">
        <div class="submission-header">
            <div class="student-info">
            <div class="student-avatar"><i class="fas fa-user"></i></div>
            <div>
                <div class="student-name"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                <div class="submission-date">Dikumpulkan: <?= date('d M Y, H:i', strtotime($row['tanggal_submit'])) ?>
                <?= $isLate ? '(Terlambat)' : '' ?></div>
            </div>
            </div>
            <div class="d-flex align-items-center">
            <span class="status-badge status-<?= $status ?>"> <?= ucfirst($status) ?> </span>
            <?php if ($status==='graded'): ?><span class="current-grade ms-2"><?= number_format($row['nilai'],2) ?></span><?php endif; ?>
            </div>
        </div>
        <div class="submission-body">
            <div class="file-info"><i class="fas fa-file-icon <?= pathinfo($row['file_submission_url'], PATHINFO_EXTENSION) ?>"></i>
            <div class="file-name"><?= htmlspecialchars($row['file_submission_url']) ?></div>
            </div>
            <div class="action-buttons mt-3">
            <a href="../../../uploads/tugas/<?= htmlspecialchars($row['file_submission_url']) ?>" class="btn btn-action btn-download"><i class="fas fa-download me-2"></i>Download</a>
            <?php
                $hasGrade = !is_null($row['nilai']);
                $btnText = $hasGrade ? 'Edit Feedback' : 'Nilai';
                $btnIcon = $hasGrade ? 'edit' : 'star';
                ?>
            
            <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#feedback<?= $row['submission_id'] ?>">
            <i class="fas fa-<?= $btnIcon ?> me-1"></i><?= $btnText ?>
            </button>
            </div>
            <div class="collapse mt-2" id="feedback<?= $row['submission_id'] ?>">
            <form action="../../../php/pengumpulan/proses.php" method="POST">
                <input type="hidden" name="submission_id" value="<?= $row['submission_id'] ?>">
                <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
                <div class="row">
                <div class="col-md-3"><label>Nilai (0–100)</label><input type="number" name="nilai" class="form-control grade-input" value="<?= $row['nilai'] ?>" min="0" max="100" required></div>
                <div class="col-md-9"><textarea name="feedback" class="form-control"><?= htmlspecialchars($row['feedback_instructor'] ?? '') ?></textarea></div>
                </div>
                <?php if ($isLate): ?><div class="alert alert-warning mt-2">Pengumpulan terlambat</div><?php endif; ?>
                <div class="mt-2">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    

                </div>
            </form>
            </div>
        </div>
        </div>
            <?php endforeach; ?>
            


            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>Tidak ada pengumpulan ditemukan</h3>
                <p>Belum ada siswa yang mengumpulkan tugas dengan filter yang dipilih.</p>
            </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
 <script>
  // Filter
  document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const f = btn.dataset.filter;
      let cnt = 0;
      document.querySelectorAll('.submission-card').forEach(card => {
        const st = card.dataset.status;
        const show = f==='all' || (f==='submitted' && st==='submitted') || (f==='graded' && st==='graded') || (f==='ungraded' && st==='submitted') || (f==='late' && st==='late');
        card.style.display = show ? 'block' : 'none';
        if (show) cnt++;
      });
    });
  });

  // Simulasi efek submit tanpa blokir form
  document.querySelectorAll('.btn-action-simulate').forEach(btn => {
    btn.addEventListener('click', function() {
      const form = this.closest('form');
      this.disabled = true;
      this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
      form.submit();
    });
  });

  // Input validation & color
  document.querySelectorAll('.grade-input').forEach(inp => {
    inp.addEventListener('input', () => {
      const v = +inp.value;
      inp.value = v<0?0:(v>100?100:v);
      inp.style.borderColor = v>=80?'#198754':(v>=60?'#ffc107':'#dc3545');
      inp.style.backgroundColor = v>=80?'#d1e7dd':(v>=60?'#fff3cd':'#f8d7da');
    });
  });
</script>
 
        
        <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

   <script src="../../../js/dashboard.js"></script>
</body>
</html>