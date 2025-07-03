<?php
session_start();
include '../database/koneksi.php';
$currentPage = basename($_SERVER['PHP_SELF']);

// Simpan URL sekarang, asalkan bukan login dan logout
if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
$student_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$assignment_id = $_GET['assignment_id'] ?? null;

if (!$student_id || !$assignment_id) {
    die("Data tidak valid.");
}

// Ambil info tugas + pelajaran + modul + kursus
$tugasQuery = mysqli_query($conn, "
       SELECT a.assignment_id, a.judul_tugas, a.deskripsi_tugas, a.tanggal_batas_akhir,
           l.nama_pelajaran, m.nama_modul, c.nama_kursus, u.nama_lengkap AS nama_pengajar
    FROM assignments a
    JOIN lessons l ON a.lesson_id = l.lesson_id
    JOIN modules m ON l.module_id = m.module_id
    JOIN courses c ON m.course_id = c.course_id
    JOIN schedules s ON c.course_id = s.course_id
    JOIN users u ON s.instructor_id = u.user_id
    WHERE a.assignment_id = $assignment_id
    LIMIT 1
");

$tugas = mysqli_fetch_assoc($tugasQuery);
if (!$tugas) {
    die("Tugas tidak ditemukan.");
}

// Ambil submission siswa
$submissionQuery = mysqli_query($conn, "
    SELECT * FROM submissions 
    WHERE assignment_id = $assignment_id AND student_id = $student_id
    LIMIT 1
");
$submission = mysqli_fetch_assoc($submissionQuery);

// Format waktu
function formatWaktu($datetime) {
    date_default_timezone_set('Asia/Jakarta');  // Set timezone Indonesia
    return date('d M Y, H:i', strtotime($datetime)) . ' WIB';
}


 
?>


<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="img/png" href="vendor/images/icon.png" />

     <link
      href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
      rel="stylesheet"
    />
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link
      rel="stylesheet"
      href="https://unpkg.com/swiper/swiper-bundle.min.css"
    /> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="vendor/style/main.css" />
    <title>BeCreative - Homepage</title>
<style>
        .assignment-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
        }
        .file-card {
            transition: all 0.3s ease;
            border-left: 4px solid #2575fc;
        }
        .file-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .grade-badge {
            font-size: 1.2rem;
            padding: 0.5em 0.8em;
        }
        .feedback-card {
            border-left: 4px solid #28a745;
        }
        .submission-timeline {
            position: relative;
            padding-left: 2rem;
        }
        .submission-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.1rem;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #2575fc;
            border: 3px solid white;
        }
</style>
  </head>
  <body>
     <nav class="navbar navbar-expand-lg navbar-light fixed-top">
      <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#" style="font-size: 1.8rem; letter-spacing: 1px;">
    <i class="bi bi-lightbulb-fill me-2"></i> IT Learning
</a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNavAltMarkup"
          aria-controls="navbarNavAltMarkup"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div
          class="collapse navbar-collapse align-items-center"
          id="navbarNavAltMarkup"
        >
     <div class="navbar-nav ms-auto me-3">
            <a class="nav-link " href="index.php">Home</a>
            <a class="nav-link " href="pendaftaran_kursus.php">Kursus</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
              <a class="nav-link active" href="jadwal">jadwal saya</a>
                <a class="nav-link" href="status_pendaftaran.php">Status Pendaftaran</a>
                <a class="nav-link" href="profil.php">Profil saya</a>
            <?php endif; ?>
            
        </div>
           
             <div class="navbar-auth d-lg-flex align-items-center mt-4 mt-md-0 text-center">
              <?php if (isset($_SESSION['user_id'])): ?>
                <form action="../php/logout.php" method="POST" class="d-inline">
                  <button type="submit" class="btn btn-danger ms-sm-4 mt-3 mt-sm-0 d-block d-sm-inline">Logout</button>
                </form>
                           <?php else: ?>
                <a class="nav-link me-0 me-lg-3 p-0" href="login.php">Masuk</a>
                <a href="registar.php" class="btn-second ms-sm-4 mt-3 mt-sm-0 d-block d-sm-inline">Daftar Sekarang</a>
              <?php endif; ?>
            </div>
        </div>
      </div>
    </nav>
 

 
<div class="container py-5" style="margin-top: 160px;">
    <!-- Header -->
    <div class="assignment-header rounded-3 p-4 mb-4 shadow">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="display-6 fw-bold mb-1"><?= htmlspecialchars($tugas['judul_tugas']) ?></h1>
                <p class="mb-0">Mata Pelajaran: <?= htmlspecialchars($tugas['nama_pelajaran']) ?> | Modul: <?= htmlspecialchars($tugas['nama_modul']) ?></p>
                <p class="mb-0">Pengajar: <?= htmlspecialchars($tugas['nama_pengajar']) ?></p>
            </div>
            <div class="text-end">
<p class="mb-1">Deadline: <?= date('d F Y', strtotime($tugas['tanggal_batas_akhir'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Jika belum mengumpulkan -->
    <?php if (!$submission): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-circle me-2"></i>
            Kamu belum mengumpulkan tugas ini.
        </div>
    <?php else: ?>
        <!-- Submission Status Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="card-title mb-3">Detail Pengumpulan</h5>
                        <div class="submission-timeline">
                            <div class="timeline-item">
                                <h6 class="mb-1">Tugas dikumpulkan</h6>
                                <p class="text-muted small mb-2"><i class="far fa-clock me-1"></i><?= formatWaktu($submission['tanggal_submit']) ?></p>
                            </div>
                            <?php if (!is_null($submission['nilai'])): ?>
                                <div class="timeline-item">
                                    <h6 class="mb-1">Tugas dinilai</h6>
                                    <p class="text-muted small mb-2"><i class="far fa-clock me-1"></i>Sudah dinilai</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 text-center py-3">
                        <div class="d-inline-block position-relative">
                            <div class="position-relative">
                                <svg width="120" height="120" viewBox="0 0 36 36" class="circular-chart">
                                    <path class="circle-bg"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="#eee" stroke-width="3"/>
                                    <path class="circle"
                                        stroke-dasharray="<?= $submission['nilai'] ?>, 100"
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="#2575fc" stroke-width="3" stroke-linecap="round"/>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                  <span class="display-5 fw-bold">
                                      <?= is_null($submission['nilai']) ? '0' : rtrim(rtrim(number_format($submission['nilai'], 2, '.', ''), '0'), '.') ?>
                                  </span>
                                </div>
                            </div>
                        </div>
                        <h5 class="mt-3">Nilai Anda</h5>
                        <p class="text-muted">* Nilai maksimal: 100</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Submission Card -->
        <div class="card file-card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title mb-3"><i class="fas fa-file-alt me-2"></i>File Tugas Anda</h5>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-file-pdf text-danger fs-3 me-3"></i>
                            <div>
                                <p class="mb-0 fw-bold"><?= basename($submission['file_submission_url']) ?></p>
                                <p class="text-muted small mb-0">Diunggah pada <?= formatWaktu($submission['tanggal_submit']) ?></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="../uploads/tugas/<?= htmlspecialchars($submission['file_submission_url']) ?>" class="btn btn-outline-primary btn-sm" download>
                            <i class="fas fa-download me-1"></i>Unduh
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Section -->
        <?php if (!empty($submission['feedback_instructor'])): ?>
        <div class="card feedback-card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="fas fa-comment-dots me-2"></i>Feedback Pengajar</h5>
                
                <div class="bg-light p-3 rounded">
                    <p class="mb-2 fw-bold">Komentar:</p>
                    <p><?= nl2br(htmlspecialchars($submission['feedback_instructor'])) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

   
</div>

 

 
    






 

    <footer class="footer section-margin">
      <div class="container">
        <div
          class="row row-content justify-content-between justify-content-md-start"
        >
          <div class="col-lg-2 col-md-6">
            <img src="vendor/images/logo.png" alt="" />
            <a href="#" class="email mt-4 d-inline-block text-white"
              >help@becreative.com</a
            >
            <p class="phone text-white">(0321) 887372</p>
            <div class="icons mt-4">
              <a href="#"><i class="bx bxl-whatsapp"></i></a>
              <a href="#"><i class="bx bxl-instagram-alt mx-2"></i></a>
              <a href="#"><i class="bx bxl-facebook-circle"></i></a>
            </div>
          </div>
          <div class="col-lg-2 offset-lg-2 col-md-3 mt-4 mt-sm-0">
            <h3>Payment</h3>
            <ul>
              <li><img src="vendor/images/bca.png" alt="" /></li>
              <li><img src="vendor/images/bri.png" alt="" /></li>
              <li><img src="vendor/images/bni.png" alt="" /></li>
              <li><img src="vendor/images/mandiri.png" alt="" /></li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-3 mt-4 mt-sm-0">
            <h3>Information</h3>
            <ul>
              <li>Office Hours</li>
              <li>Requirements</li>
              <li>About us</li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-3 mt-4 mt-sm-0">
            <h3>Helpfull Link</h3>
            <ul>
              <li>Service</li>
              <li>Support</li>
              <li>Terms & Condition</li>
              <li>Privacy Policy</li>
            </ul>
          </div>
          <div class="col-lg-2 col-md-3 mt-4 mt-sm-0">
            <h3>Address</h3>
            <p class="text-white">
              Jl Gatot Subroto No. 123 Blok. A23 Malang, Jawa Timur
            </p>
            <a href="#" class="maps text-white">Google Map</a>
          </div>
        </div>
        <div class="row text-center">
          <div class="col-12">
            <p class="text-white">
              &copy;Copyright 2022 all right reserved | Built by Mardha Mardiya
            </p>
          </div>
        </div>
      </div>
    </footer>
    <!-- FOOTER END -->
 
    <!-- Jquery -->
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
      integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!-- Boostrap Script -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>

    <!-- Main Script -->
    <script src="vendor/js/main.js"></script>
  </body>
</html>