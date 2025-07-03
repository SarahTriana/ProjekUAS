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

$dataJadwal = [];

if ($student_id) {
  $jadwalQuery = "
  SELECT 
    c.course_id,
    c.nama_kursus,
    c.level,
    s.tanggal_mulai,
    s.tanggal_selesai,
    s.hari_pelaksanaan,
    s.waktu_mulai,
    s.waktu_selesai,
    s.lokasi_kelas
  FROM enrollments e
  JOIN schedules s ON e.schedule_id = s.schedule_id
  JOIN courses c ON s.course_id = c.course_id
  WHERE e.student_id = $student_id AND e.status_pendaftaran = 'selesai'
";


  $result = mysqli_query($conn, $jadwalQuery);
  if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $dataJadwal[] = $row;
    }
  }
}
?>

 

<?php
function getLevelColor($level) {
  $level = strtolower($level);
  switch ($level) {
    case 'pemula': return 'info';
    case 'menengah': return 'primary';
    case 'lanjutan': return 'warning';
    case 'expert': return 'danger';
    default: return 'secondary';
  }
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
    />
    <link rel="stylesheet" href="vendor/style/main.css" />
    <title>BeCreative - Homepage</title>
<style>
  .card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
  }
  
  .empty-state {
    max-width: 500px;
    margin: 0 auto;
  }
  
  .badge {
    font-size: 0.75rem;
    margin-top: 0.5rem;
  }
   .text-gradient-primary {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .hover-effect {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
  }
  
  .hover-effect:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    border-color: rgba(78, 115, 223, 0.3);
  }
  
  .icon-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    flex-shrink: 0;
  }
  
  .timeline-item {
    position: relative;
    padding-left: 15px;
  }
  
  .timeline-item:before {
    content: "";
    position: absolute;
    left: 20px;
    top: 40px;
    height: calc(100% - 40px);
    width: 2px;
    background: rgba(0,0,0,0.1);
  }
  
  .timeline-item:last-child:before {
    display: none;
  }
  
  .rounded-bl-3 {
    border-bottom-left-radius: 1rem !important;
  }
</style>
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
              <a class="nav-link active" href="jadwal.php">jadwal saya</a>
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
<div class="container py-5" style="margin-top: 200px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0 text-gradient-primary">
      <i class="bi bi-calendar2-week me-2"></i>Jadwal Kursus Saya
    </h3>
    <span class="badge bg-primary bg-opacity-10 text-primary">
      <i class="bi bi-bookmark-check me-1"></i><?= count($dataJadwal) ?> Kursus
    </span>
  </div>

  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (!empty($dataJadwal)): ?>
      <?php foreach ($dataJadwal as $row): ?>
        <div class="col">
          <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-effect">
            <div class="card-header bg-light bg-opacity-50 border-bottom-0 position-relative">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold text-truncate"><?= htmlspecialchars($row['nama_kursus']) ?></h5>
                <span class="badge bg-<?= getLevelColor($row['level']) ?> bg-opacity-10 text-<?= getLevelColor($row['level']) ?>">
                  <?= htmlspecialchars($row['level']) ?>
                </span>
              </div>
              <div class="position-absolute top-0 end-0 bg-primary bg-opacity-10 px-2 py-1 rounded-bl-3">
                <small class="text-muted"><i class="bi bi-people-fill me-1"></i>20 Peserta</small>
              </div>
            </div>
            
            <div class="card-body pt-3">
              <div class="timeline-item mb-3">
                <div class="d-flex">
                  <span class="icon-circle bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-calendar2-range"></i>
                  </span>
                  <div class="ms-3">
                    <h6 class="mb-0">Periode Kursus</h6>
                    <p class="text-muted small mb-0">
                      <?= date('d M Y', strtotime($row['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="timeline-item mb-3">
                <div class="d-flex">
                  <span class="icon-circle bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                  </span>
                  <div class="ms-3">
                    <h6 class="mb-0">Jadwal Mingguan</h6>
                    <p class="text-muted small mb-0">
                      <?= htmlspecialchars($row['hari_pelaksanaan']) ?>, <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?>
                    </p>
                  </div>
                </div>
              </div>
              
              <div class="timeline-item mb-3">
                <div class="d-flex">
                  <span class="icon-circle bg-info bg-opacity-10 text-info">
                    <i class="bi bi-geo-alt"></i>
                  </span>
                  <div class="ms-3">
                    <h6 class="mb-0">Lokasi Kelas</h6>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($row['lokasi_kelas']) ?></p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="card-footer bg-transparent border-top-0 pt-0">
              <a href="detail_kursus.php?course_id=<?= $row['course_id'] ?>" class="btn btn-outline-primary w-100 rounded-pill">
    <i class="bi bi-eye-fill me-2"></i>Lihat Detail Kursus
</a>

            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="card border-0 shadow-none rounded-4 bg-light bg-opacity-25">
          <div class="card-body text-center py-5">
            <div class="empty-state">
              <div class="icon-circle bg-warning bg-opacity-10 text-warning mx-auto mb-4" style="width: 80px; height: 80px;">
                <i class="bi bi-calendar-x fs-2"></i>
              </div>
              <h4 class="text-dark mb-3">Belum Ada Jadwal Kursus</h4>
              <p class="text-muted mb-4">Saat ini Anda belum memiliki kursus yang sedang berjalan atau telah disetujui.</p>
              <a href="daftar_kursus.php" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-search me-2"></i>Temukan Kursus
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
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