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

    $nama_siswa = '';
    if ($student_id && $role === 'siswa') {
        $qNama = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE user_id = '$student_id'");
        $dNama = mysqli_fetch_assoc($qNama);
        $nama_siswa = $dNama['nama_lengkap'] ?? '';
    }

    $query = "
        SELECT 
            s.*, 
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
            <a class="nav-link active" href="pendaftaran_kursus.php">Kursus</a>
            <a class="nav-link" href="index.php">Contact</a>

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
    <br>
    <br>

          <section class="popular-courses m-5 py-5  ">
            <div class="container">
                 <!-- Bagian Intro -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">🌟 Temukan Kursus Terbaik Untuk Masa Depanmu!</h2>
            <p class="text-muted">
                Dari pemula hingga profesional, semua bisa belajar di sini.
            </p>
        </div>
              <div class="row justify-content-between align-items-center mb-4">
                <div class="col-md-6">
                  <div class="d-flex align-items-center">
                    </div>
                </div>
                
              </div>
              <div class="row g-4">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6">
                  <div class="card h-100 border-0 shadow-sm hover-shadow">
                    <div class="card-body p-4">
                      <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                          <i class="bi bi-laptop text-success fs-4"></i>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success small">Tersedia</span>
                      </div>

                      <h3 class="h5 fw-bold mb-3"><?= $row['nama_kursus'] ?></h3>
                      <p class="text-muted mb-4">
                        Jadwal: <?= $row['hari_pelaksanaan'] ?> <br>
                        Waktu: <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?> <br>
                        Lokasi: <?= $row['lokasi_kelas'] ?>
                      </p>

                      <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-success bg-opacity-10 text-success"><?= $row['level'] ?></span>
                        <div class="text-warning">
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star-fill"></i>
                          <i class="bi bi-star"></i>
                          <span class="text-muted small ms-1">(20)</span>
                        </div>
                      </div>

                      <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div>
                          <span class="d-block text-muted small">
                            <i class="bi bi-people me-1"></i> <?= $row['jumlah_pendaftar'] ?>/<?= $row['kapasitas_maksimal'] ?> Siswa
                          </span>
                                          <h4 class="h5 text-primary mb-0 mt-1">Rp <?= number_format($row['harga'], 0, ',', '.') ?></h4>
                                        </div>
                                          <?php if ($row['jumlah_pendaftar'] >= $row['kapasitas_maksimal']): ?>
                                  <span class="badge bg-danger rounded-pill px-3 py-2">Penuh</span>
                                <?php elseif ($student_id && $role === 'siswa'): ?>
                                      <a href="review.php?id=<?= $row['course_id'] ?>" 
                                        class="btn btn-outline-primary rounded-pill px-3">
                                        Ulasan <i class="bi bi-chat-dots ms-1"></i>
                                      </a>
                                  <a href="#" 
                                    class="btn btn-primary rounded-pill px-3" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#daftarModal" 
                                    data-id="<?= $row['schedule_id'] ?>" 
                                    data-kursus="<?= $row['nama_kursus'] ?>"
                                    data-hari="<?= $row['hari_pelaksanaan'] ?>"
                                    data-waktu="<?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?>">
                                    Daftar <i class="bi bi-arrow-right ms-1"></i>
                                  </a>
                                <?php else: ?>
                                  <a href="#" 
                                    class="btn btn-outline-secondary rounded-pill px-3" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#loginDuluModal">
                                    Daftar <i class="bi bi-arrow-right ms-1"></i>
                                  </a>
                                <?php endif; ?>


                      </div>
                    </div>
                  </div>
                </div>
              <?php endwhile; ?>
              </div>
            </div>
          </section>


<!-- Form Pendaftaran Kursus Modal -->
<div class="modal fade" id="daftarModal" tabindex="-1" aria-labelledby="daftarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form action="../php/pendaftaran_kursus/pendaftaran_kursus.php" method="POST">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-gradient-primary text-white rounded-top">
          <h5 class="modal-title fw-bold fs-4" id="daftarModalLabel">
            <i class="fas fa-edit me-2"></i>FORM PENDAFTARAN KURSUS
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <input type="hidden" name="schedule_id" id="schedule_id">
          
          <div class="row g-4">
            <!-- Student Info Section -->
            <div class="col-md-6">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light-primary text-primary fw-bold">
                  <i class="fas fa-user-graduate me-2"></i>INFORMASI SISWA
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="nama_lengkap" class="form-label small fw-bold text-muted">
                      NAMA LENGKAP
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-user"></i>
                      </span>
                      <input type="text" class="form-control py-2 bg-light" value="<?= $nama_siswa ?>" readonly>
                    </div>
                  </div>
                  
                  <!-- You can add more student info fields here if needed -->
                  <div class="alert alert-info mt-3 small">
                    <i class="fas fa-info-circle me-2"></i>Pastikan data diri sudah benar sebelum mendaftar.
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Course Info Section -->
            <div class="col-md-6">
              <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-light-primary text-primary fw-bold">
                  <i class="fas fa-book-open me-2"></i>INFORMASI KURSUS
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">
                      NAMA KURSUS
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-book"></i>
                      </span>
                      <input type="text" class="form-control py-2 bg-light" id="nama_kursus" readonly>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">
                      JADWAL KURSUS
                    </label>
                    <div class="input-group">
                      <span class="input-group-text bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-calendar-alt"></i>
                      </span>
                      <input type="text" class="form-control py-2 bg-light" id="jadwal_kursus" readonly>
                    </div>
                  </div>
                  
                  <div class="alert alert-warning mt-3 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>Harap periksa jadwal sebelum mendaftar.
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Terms and Conditions -->
          <div class="mt-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="agreeTerms" required>
              <label class="form-check-label small" for="agreeTerms">
                Saya menyetujui <a href="#" class="text-primary">syarat dan ketentuan</a> yang berlaku
              </label>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light rounded-bottom">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm">
            <i class="fas fa-paper-plane me-2"></i>Kirim Pendaftaran
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Login Warning Modal -->
<div class="modal fade" id="loginDuluModal" tabindex="-1" aria-labelledby="loginDuluModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold" id="loginAlertLabel">
          <i class="fas fa-exclamation-triangle me-2"></i>Login Dulu Kak 😄
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div class="mb-3">
          <i class="fas fa-user-lock text-danger" style="font-size: 3rem;"></i>
        </div>
        <h5 class="fw-bold mb-3">Akses Terbatas!</h5>
        <p class="mb-0">Untuk mendaftar kursus ini, kamu harus login terlebih dahulu. Yuk login dulu ya kakak! 😊</p>
      </div>
      <div class="modal-footer justify-content-center border-0">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
          <i class="fas fa-clock me-2"></i>Nanti Saja
        </button>
        <a href="login.html" class="btn btn-danger px-4">
          <i class="fas fa-sign-in-alt me-2"></i>Login Sekarang
        </a>
      </div>
    </div>
  </div>
</div>


<script>
  var daftarModal = document.getElementById('daftarModal');
  daftarModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var scheduleId = button.getAttribute('data-id');
    var namaKursus = button.getAttribute('data-kursus');
    var hariWaktu = button.getAttribute('data-hari') + ', ' + button.getAttribute('data-waktu');

    daftarModal.querySelector('#schedule_id').value = scheduleId;
    daftarModal.querySelector('#nama_kursus').value = namaKursus;
    daftarModal.querySelector('#jadwal_kursus').value = hariWaktu;
  });
</script>

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