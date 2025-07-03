 <?php
session_start();
include '../database/koneksi.php'; // file koneksi database kamu
$currentPage = basename($_SERVER['PHP_SELF']);

// Simpan URL sekarang, asalkan bukan login dan logout
if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Ambil data dari tabel Users
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $userId");
$userData = mysqli_fetch_assoc($queryUser);

// Kalau role siswa → ambil detail dari tabel Students
$studentData = null;
if ($userData['role'] === 'siswa') {
    $queryStudent = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $userId");
    $studentData = mysqli_fetch_assoc($queryStudent);
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
        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .card-profile {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .card-profile:hover {
            transform: translateY(-5px);
        }
        .info-label {
            font-weight: 500;
            color: #6c757d;
        }
        .info-value {
            font-weight: 500;
            color: #495057;
        }
        .btn-edit {
            border-radius: 50px;
            padding: 8px 20px;
        }
        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border-radius: 3px;
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
              <a class="nav-link " href="jadwal.php">jadwal saya</a>
                <a class="nav-link" href="status_pendaftaran.php">Status Pendaftaran</a>
                <a class="nav-link active" href="profil.php">Profil saya</a>
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
 
    



<div class="container py-5" style="margin-top: 100px;">
    <!-- Header Profil -->
    <div class="profile-header p-4 mb-4 bg-light rounded">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($userData['nama_lengkap']) ?>&background=random" alt="Foto Profil" class="profile-pic rounded-circle mb-3" width="100">
            </div>
            <div class="col-md-8">
                <h2><?= htmlspecialchars($userData['nama_lengkap']) ?></h2>
                <p><i class="bi bi-mortarboard me-2"></i><?= ucfirst($userData['role']) ?></p>
                <p><i class="bi bi-calendar me-2"></i>Bergabung sejak: <?= date('d F Y', strtotime($userData['tanggal_registrasi'])) ?></p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kolom Kiri - Data Diri -->
        <div class="col-lg-8">
            <div class="card card-profile mb-4 p-4">
                <h4>Informasi Pribadi</h4>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <p><i class="bi bi-envelope me-2"></i>Email:</p>
                        <p><?= htmlspecialchars($userData['email']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p><i class="bi bi-telephone me-2"></i>Telepon:</p>
                        <p><?= htmlspecialchars($userData['telepon']) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p><i class="bi bi-geo-alt me-2"></i>Alamat:</p>
                        <p><?= htmlspecialchars($userData['alamat']) ?></p>
                    </div>
                    <?php if ($studentData): ?>
                    <div class="col-md-6 mb-3">
                        <p><i class="bi bi-calendar-event me-2"></i>Tanggal Lahir:</p>
                        <p><?= date('d M Y', strtotime($studentData['tanggal_lahir'])) ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p><i class="bi bi-book me-2"></i>Pendidikan Terakhir:</p>
                        <p><?= htmlspecialchars($studentData['pendidikan_terakhir']) ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6 mb-3">
                        <p><i class="bi bi-clock-history me-2"></i>Terakhir Login:</p>
                        <p><?= date('d M Y, H:i', strtotime($userData['last_login'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan - Aksi -->
        <div class="col-lg-4">
            <div class="card p-4 mb-4">
                <h4>Aksi Cepat</h4>
                <button class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#editProfilModal">
                    <i class="bi bi-pencil-square me-2"></i>Edit Profil
                </button>
                <button class="btn btn-outline-danger w-100 mb-3" data-bs-toggle="modal" data-bs-target="#ubahPasswordModal">
                    <i class="bi bi-shield-lock me-2"></i>Ubah Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfilModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="../php/profil_siswa/proses.php" method="post">
                <input type="hidden" name="user_id" value="<?= $userId ?>">
                <input type="hidden" name="update_profil" value="1">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5>Edit Profil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $userId ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($userData['nama_lengkap']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email']) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Telepon</label>
                            <input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($userData['telepon']) ?>">
                        </div>
                        <?php if ($studentData): ?>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="<?= $studentData['tanggal_lahir'] ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Pendidikan Terakhir</label>
                            <select name="pendidikan_terakhir" class="form-select">
                                <?php
                                $options = ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2'];
                                foreach ($options as $opt) {
                                    $selected = ($studentData['pendidikan_terakhir'] == $opt) ? 'selected' : '';
                                    echo "<option value='$opt' $selected>$opt</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div class="col-12 mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control"><?= htmlspecialchars($userData['alamat']) ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ubah Password -->
<div class="modal fade" id="ubahPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="../php/profil_siswa/proses.php" method="post">
             <input type="hidden" name="user_id" value="<?= $userId ?>">
            <input type="hidden" name="ubah_password" value="1">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5>Ubah Password</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $userId ?>">
                    <div class="mb-3">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Ubah Password</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
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
 
     <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
      integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

     <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

     <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>

     <script src="vendor/js/main.js"></script>
  </body>
</html>