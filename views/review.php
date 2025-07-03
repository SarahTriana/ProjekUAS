<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// Simpan URL sekarang, asalkan bukan login dan logout
if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
include '../database/koneksi.php';

$student_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $student_id && $role === 'siswa') {
    if (!isset($_POST['schedule_id']) || empty($_POST['schedule_id'])) {
        echo "<script>alert('Jadwal tidak valid.');window.location='pendaftaran_kursus.php';</script>";
        exit;
    }

    date_default_timezone_set('Asia/Jakarta');
    $schedule_id = $_POST['schedule_id'];
    $tanggal_daftar = date('Y-m-d H:i:s');

    mysqli_query($conn, "
        INSERT INTO enrollments (student_id, schedule_id, tanggal_daftar, status_pendaftaran)
        VALUES ('$student_id', '$schedule_id', '$tanggal_daftar', 'pending')
    ");

    echo "<script>alert('Pendaftaran berhasil! Menunggu konfirmasi.');window.location='pendaftaran_kursus.php';</script>";
    exit;
}

// ============================
// Ambil Nama Siswa Jika Login
// ============================
$nama_siswa = '';
if ($student_id && $role === 'siswa') {
    $qNama = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE user_id = '$student_id'");
    $dNama = mysqli_fetch_assoc($qNama);
    $nama_siswa = $dNama['nama_lengkap'] ?? '';
}
 
$course_id = $_GET['id'] ?? null;
$kursus = null;
$jadwal = null;
$avgRating = 0;
$totalReview = 0;
$reviews = [];

if ($course_id) {
     $qKursus = mysqli_query($conn, "SELECT * FROM courses WHERE course_id = '$course_id'");
    $kursus = mysqli_fetch_assoc($qKursus);

     $qJadwal = mysqli_query($conn, "
        SELECT * FROM schedules 
        WHERE course_id = '$course_id' 
        ORDER BY tanggal_mulai ASC 
        LIMIT 1
    ");
    $jadwal = mysqli_fetch_assoc($qJadwal);

     $qRating = mysqli_query($conn, "
        SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_review 
        FROM reviews 
        WHERE course_id = '$course_id' AND tipe_review = 'kursus'
    ");
    $rRating = mysqli_fetch_assoc($qRating);
    $avgRating = round($rRating['avg_rating'] ?? 0, 1);
    $totalReview = $rRating['total_review'] ?? 0;

     $qReview = mysqli_query($conn, "
        SELECT r.*, u.nama_lengkap 
        FROM reviews r 
        JOIN users u ON r.student_id = u.user_id
        WHERE r.course_id = '$course_id'
        ORDER BY r.tanggal_review DESC
    ");
    while ($r = mysqli_fetch_assoc($qReview)) {
        $reviews[] = $r;
    }
}
 
$query = "
    SELECT 
        s.*, 
        c.course_id,
        c.nama_kursus, 
        c.level, 
        c.harga,
        (SELECT COUNT(*) 
         FROM enrollments e 
         WHERE e.schedule_id = s.schedule_id 
           AND e.status_pendaftaran NOT IN ('dibatalkan', 'ditolak')
        ) AS jumlah_pendaftar
    FROM schedules s
    JOIN courses c ON s.course_id = c.course_id
";
$result = mysqli_query($conn, $query);
?>





<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="img/png" href="vendor/images/icon.png" />
    <link rel="stylesheet" href="vendor/style/main.css" />
    <title>BeCreative - Homepage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .course-header {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .schedule-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
        }
        .review-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .rating {
            color: #ffc107;
        }
        .btn-review {
            background-color: #0d6efd;
            color: white;
            font-weight: 500;
        }
        .modal-review {
            max-width: 600px;
        }
    </style>
    <style>
    .rating {
        color: #ddd;
        font-size: 1rem;
    }
    
    .rating i {
        margin-right: 2px;
    }
    
    .review-card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .review-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
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
        <div class="navbar-nav ms-auto me-3">
            <a class="nav-link " href="index.php">Home</a>
            <a class="nav-link active" href="pendaftaran_kursus.php">Kursus</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
              <a class="nav-link" href="jadwal.php">jadwal saya</a>
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
 <br>
 <br>
 <br>
 <br>
 
  
     <div class="container py-5">
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
          <div class="card-body p-4 p-md-5">
            <div class="row g-4 g-lg-5">

              <!-- Informasi Kursus -->
              <div class="col-lg-8">
                <div class="d-flex flex-column h-100">
                  <div class="mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary fs-6 mb-3"><?= $kursus['level'] ?></span>
                    <h1 class="fw-bold mb-2 display-6"><?= $kursus['nama_kursus'] ?></h1>
                    <p class="lead text-muted"><?= $kursus['deskripsi'] ?></p>
                  </div>

                  <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                    <span class="badge bg-primary bg-opacity-10 text-primary py-2 px-3">
                      <i class="bi bi-bar-chart-fill me-1"></i> <?= $kursus['level'] ?>
                    </span>
                    <span class="badge bg-light text-dark py-2 px-3">
                      <i class="bi bi-clock-fill me-1 text-primary"></i> <?= $kursus['durasi_jam'] ?> Jam
                    </span>
                    <span class="badge <?= $kursus['status_aktif'] ? 'bg-success' : 'bg-secondary' ?> bg-opacity-10 text-<?= $kursus['status_aktif'] ? 'success' : 'secondary' ?> py-2 px-3">
                      <i class="bi bi-check-circle-fill me-1"></i> <?= $kursus['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?>
                    </span>
                  </div>

                  <div class="mb-4">
                    <p class="mb-3 fs-5">Kursus ini membimbing Anda membangun aplikasi web modern dari awal hingga deployment menggunakan teknologi terbaru seperti React, Node.js, dan MongoDB.</p>
                    <h3 class="text-primary fw-bold mb-0">Rp <?= number_format($kursus['harga'], 0, ',', '.') ?></h3>
                  </div>
                </div>
              </div>

              <!-- Informasi Jadwal -->
              <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body p-4">
                    <h3 class="fw-bold mb-4 text-center"><i class="bi bi-calendar-week text-primary me-2"></i>Jadwal Kursus</h3>
                    <?php if ($jadwal): ?>
                    <ul class="list-unstyled mb-4">
                      <li class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-calendar-event text-primary fs-5"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Mulai</small>
                            <strong><?= date('d M Y', strtotime($jadwal['tanggal_mulai'])) ?></strong>
                          </div>
                        </div>
                      </li>
                      <li class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-calendar-x text-primary fs-5"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Selesai</small>
                            <strong><?= date('d M Y', strtotime($jadwal['tanggal_selesai'])) ?></strong>
                          </div>
                        </div>
                      </li>
                      <li class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-clock text-primary fs-5"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Waktu</small>
                            <strong><?= date('H:i', strtotime($jadwal['waktu_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['waktu_selesai'])) ?> WIB</strong>
                          </div>
                        </div>
                      </li>
                      <li class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-calendar3-week text-primary fs-5"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Hari</small>
                            <strong><?= $jadwal['hari_pelaksanaan'] ?></strong>
                          </div>
                        </div>
                      </li>
                      <li class="mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-geo-alt text-primary fs-5"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Lokasi</small>
                            <strong><?= $jadwal['lokasi_kelas'] ?></strong>
                          </div>
                        </div>
                      </li>
                      <li class="mb-4">
                        <div class="d-flex align-items-center">
                          <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-people-fill text-primary fs-5"></i>
                          </div>
                          <div>
                            <small class="text-muted d-block">Kapasitas</small>
                            <strong><?= $jadwal['kapasitas_maksimal'] ?> Peserta</strong>
                          </div>
                        </div>
                      </li>
                    </ul>
                    <?php else: ?>
                      <p class="text-muted text-center">Belum ada jadwal tersedia untuk kursus ini.</p>
                    <?php endif; ?>

                    <div class="d-grid gap-3">
                      <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="bi bi-star-fill me-1"></i> Beri Ulasan
                      </button>
                      <a href="pendaftaran_kursus.php" class="btn btn-link text-decoration-none text-center">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Kursus
                      </a>
                    </div>

                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
         <div class="row py-5">
           <div class="col-12">
  <!-- Header Rata-Rata Rating -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-3 mb-md-0">
      <i class="fas fa-comment-alt text-primary me-2"></i> Ulasan Peserta
    </h2>

    <div class="d-flex align-items-center bg-light bg-opacity-10 p-3 rounded-3">
      <div class="text-center me-3">
        <h1 class="fw-bold mb-0 display-6 text-primary"><?= $avgRating ?></h1>
        <small class="text-muted">dari 5.0</small>
      </div>
      <div>
        <div class="rating mb-1">
          <?php
            $full = floor($avgRating);
            $half = ($avgRating - $full >= 0.5) ? 1 : 0;
            for ($i = 0; $i < $full; $i++) echo '<i class="fas fa-star text-warning"></i>';
            if ($half) echo '<i class="fas fa-star-half-alt text-warning"></i>';
            for ($i = $full + $half; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
          ?>
        </div>
        <span class="text-muted small">Berdasarkan <?= $totalReview ?> ulasan</span>
      </div>
    </div>
  </div>

  <!-- Daftar Review -->
  <div class="row g-4">
    <?php foreach ($reviews as $review): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 border-0 shadow-sm review-card-hover">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between mb-3">
              <div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($review['nama_lengkap']) ?></h5>
                <p class="text-muted small mb-0"><?= date('d M Y', strtotime($review['tanggal_review'])) ?></p>
              </div>
              <div class="rating">
                <?php
                  for ($i = 0; $i < $review['rating']; $i++) echo '<i class="fas fa-star text-warning"></i>';
                  for ($i = $review['rating']; $i < 5; $i++) echo '<i class="far fa-star text-warning"></i>';
                ?>
              </div>
            </div>
            <p class="mb-0"><?= htmlspecialchars($review['komentar']) ?></p>
          </div>

          <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4 d-flex justify-content-between">
            <div>
              <span class="badge <?= $review['tipe_review'] === 'kursus' ? 'bg-success bg-opacity-10 text-success' : 'bg-primary bg-opacity-10 text-primary' ?>">
                <?= ucfirst($review['tipe_review']) ?>
              </span>
            </div>

            <?php if ($review['student_id'] == $student_id): ?>
              <form action="../php/riview/hapus.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus ulasan ini?')">
                <input type="hidden" name="review_id" value="<?= $review['review_id'] ?>">
                 <input type="hidden" name="tipe_review" value="<?= $review['tipe_review'] ?>">
                <input type="hidden" name="instructor_id" value="<?= $review['instructor_id'] ?? '' ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-trash"></i> Hapus
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


 
    </div>

  <!-- Modal Ulasan -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-review">
    <div class="modal-content">
      <form action="../php/riview/proses_riview.php" method="POST" id="formReview">
        <div class="modal-header">
          <h5 class="modal-title" id="reviewModalLabel">Beri Ulasan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <!-- Hidden Input -->
          <input type="hidden" name="course_id" value="<?= $kursus['course_id'] ?>">
          <input type="hidden" name="instructor_id" value="<?= $jadwal['instructor_id'] ?>">
          <input type="hidden" name="rating" id="ratingValue" value="0">

          <!-- Rating -->
          <div class="mb-4">
            <label class="form-label">Rating</label>
            <div class="rating fs-3 text-warning" id="ratingStars">
              <i class="fa-regular fa-star" data-rating="1"></i>
              <i class="fa-regular fa-star" data-rating="2"></i>
              <i class="fa-regular fa-star" data-rating="3"></i>
              <i class="fa-regular fa-star" data-rating="4"></i>
              <i class="fa-regular fa-star" data-rating="5"></i>
            </div>
          </div>

          <!-- Tipe Review -->
          <div class="mb-3">
            <label for="reviewType" class="form-label">Jenis Ulasan</label>
            <select class="form-select" id="reviewType" name="tipe_review" required>
              <option value="kursus">Ulasan untuk Kursus</option>
              <option value="pengajar">Ulasan untuk Pengajar</option>
            </select>
          </div>

          <!-- Komentar -->
          <div class="mb-3">
            <label for="reviewComment" class="form-label">Komentar</label>
            <textarea class="form-control" id="reviewComment" name="komentar" rows="4" placeholder="Bagikan pengalaman Anda mengikuti kursus ini" required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
        </div>
      </form>
    </div>
  </div>
</div>

 <script>
  const stars = document.querySelectorAll('#ratingStars i');
  const ratingValue = document.getElementById('ratingValue');

  stars.forEach((star, index) => {
    star.addEventListener('click', () => {
      const rating = index + 1;
      ratingValue.value = rating;

      // Reset semua ke kosong
      stars.forEach(s => {
        s.classList.remove('fa-solid');
        s.classList.add('fa-regular');
      });

      // Isi bintang sesuai rating
      for (let i = 0; i < rating; i++) {
        stars[i].classList.remove('fa-regular');
        stars[i].classList.add('fa-solid');
      }
    });
  });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
      integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>
    <script src="vendor/js/main.js"></script>
  </body>
</html>