 <?php
session_start();
include '../database/koneksi.php';
 $isLogin = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);

 if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
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
    <link
      rel="stylesheet"
      href="https://unpkg.com/swiper/swiper-bundle.min.css"
    />
    <link rel="stylesheet" href="vendor/style/main.css" />
    <title>BeCreative - Homepage</title>
  <style>
          .hover-shadow {
            transition: all 0.3s ease;
          }
          .hover-shadow:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-5px);
          }
          .rounded-circle {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
          }
        .contact-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .contact-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            height: 100%;
        }
        .contact-card:hover {
            transform: translateY(-5px);
        }
        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #3498db;
        }
        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(to right, #2c3e50, #3498db);
            border-radius: 3px;
        }
        .map-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .social-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 10px;
            transition: all 0.3s;
        }
        .social-icon:hover {
            transform: scale(1.1);
        }
 
    </style>
  </head>
  <body>
    <!-- NAVBAR START -->
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
            <a class="nav-link active" href="index.php">Home</a>
            <a class="nav-link" href="pendaftaran_kursus.php">Kursus</a>
            <a class="nav-link" href="#kontak">Contact</a>
            
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
    <!-- NAVBAR END -->

    <!-- HOME START -->
    <section class="home">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="heading-home">
              Asah Kemampuan Bidangmu Menjadi Lebih
              <span class="prop">Profesional</span> dan
              <span class="kre">Kreatif</span>
            </h1>
            <p class="subheading-home">
              Ambil dan temukan passion-mu disini, menjadi satu langkah awal
              kesuksesanmu di masa depan
            </p>

          </div>
          <div class="col-lg-6 mt-5 mt-lg-0">
          <div class="home-thumbnail mx-auto adjusted-thumbnail">
            <img src="vendor/images/20250619_150043.png" class="img-fluid responsive-thumbnail" alt="" />
          </div>
          </div>
        </div>
      </div>
    </section>
    <!-- HOME END -->

    <!-- PARTNER START -->
    <section class="partner section-margin">
      <div class="container">
        <div class="row text-center">
          <div class="col-lg-12">
            <h3 class="label-section">Partner Kami</h3>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-lg-2 col-md-3 col-6 d-flex align-items-center">
            <img src="vendor/images/partner1.png" class="img-fluid" alt="" />
          </div>
          <div class="col-lg-2 col-md-3 mt-4 mt-lg-0 col-6">
            <img src="vendor/images/partner2.png" class="img-fluid" alt="" />
          </div>
          <div class="col-lg-2 col-md-3 mt-4 mt-lg-0 col-6">
            <img src="vendor/images/partner3.png" class="img-fluid" alt="" />
          </div>
          <div class="col-lg-2 col-md-3 mt-4 mt-lg-0 col-6">
            <img src="vendor/images/partner4.png" class="img-fluid" alt="" />
          </div>
        </div>
      </div>
    </section>
    <!-- PARTNER END -->
 
    <!-- PRODUCT START -->
      <section class="popular-courses m-5 py-5 bg-light">
        <div class="container">
          <div class="row justify-content-between align-items-center mb-4">
            <div class="col-md-6">
              <div class="d-flex align-items-center">
                <i class="bi bi-stars text-primary fs-4 me-2"></i>
                <h2 class="h4 mb-0 fw-bold">Pelatihan Populer Kami</h2>
              </div>
            </div>
            <div class="col-md-6 text-md-end">
              <a href="pendaftaran_kursus.php" class="btn btn-outline-primary rounded-pill">Lihat Semua Kelas <i class="bi bi-chevron-right ms-1"></i></a>
            </div>
          </div>

          <div class="row g-4">
            <!-- Course 1 -->
            <div class="col-lg-4 col-md-6">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body p-4">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                      <i class="bi bi-code-slash text-primary fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success small">Aktif</span>
                  </div>
                  
                  <h3 class="h5 fw-bold mb-3">Fullstack Web Development</h3>
                  <p class="text-muted mb-4">Pelajari pembuatan aplikasi web modern dari frontend hingga backend dengan teknologi terkini.</p>
                  
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary">Lanjut</span>
                    <div class="text-warning">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <span class="text-muted small ms-1">(1,232)</span>
                    </div>
                  </div>
                  
                  <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div>
                      <span class="d-block text-muted small"><i class="bi bi-clock me-1"></i> 80 Jam</span>
                      <h4 class="h5 text-primary mb-0 mt-1">Rp 400.000</h4>
                    </div>
                   </div>
                </div>
              </div>
            </div>

            <!-- Course 2 -->
            <div class="col-lg-4 col-md-6">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body p-4">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                      <i class="bi bi-palette text-warning fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success small">Aktif</span>
                  </div>
                  
                  <h3 class="h5 fw-bold mb-3">UI/UX Design Mastery</h3>
                  <p class="text-muted mb-4">Kuasi prinsip desain modern dan tools terbaik untuk menciptakan pengalaman pengguna yang memukau.</p>
                  
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-warning bg-opacity-10 text-warning">Menengah</span>
                    <div class="text-warning">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-half"></i>
                      <span class="text-muted small ms-1">(956)</span>
                    </div>
                  </div>
                  
                  <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div>
                      <span class="d-block text-muted small"><i class="bi bi-clock me-1"></i> 60 Jam</span>
                      <h4 class="h5 text-primary mb-0 mt-1">Rp 600.000</h4>
                    </div>
                   </div>
                </div>
              </div>
            </div>

            <!-- Course 3 -->
            <div class="col-lg-4 col-md-6">
              <div class="card h-100 border-0 shadow-sm hover-shadow">
                <div class="card-body p-4">
                  <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                      <i class="bi bi-laptop text-success fs-4"></i>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success small">Aktif</span>
                  </div>
                  
                  <h3 class="h5 fw-bold mb-3">Frontend Development</h3>
                  <p class="text-muted mb-4">Pelajari teknik modern pengembangan frontend dengan ReactJS dan framework populer lainnya.</p>
                  
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success">Dasar</span>
                    <div class="text-warning">
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star-fill"></i>
                      <i class="bi bi-star"></i>
                      <span class="text-muted small ms-1">(1,542)</span>
                    </div>
                  </div>
                  
                  <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <div>
                      <span class="d-block text-muted small"><i class="bi bi-clock me-1"></i> 40 Jam</span>
                      <h4 class="h5 text-primary mb-0 mt-1">Rp 200.000</h4>
                    </div>
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
 

    <!-- PRODUCT END -->

    <!-- ABOUT START -->
    <section class="about section-margin">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <img src="vendor/images/20250619_145600.png" class="img-fluid" alt="" />
          </div>
          <div class="col-md-5 offset-md-1 mt-4 mt-sm-0">
            <h3 class="heading-section">
              Platform Pelatihan Online Terbaik Kini Telah Hadir
            </h3>
            <p class="subheading-section mt-3">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id morbi
              purus lacus diam at mi in facilisis commodo.
            </p>
            <p class="subheading-section">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id morbi
              purus lacus diam at mi in facilisis commodo.
            </p>

            <a href="#" class="btn-first mt-5 d-inline-block"
              >Baca Selengkapnya</a
            >
          </div>
        </div>
      </div>
    </section>
    
    <section class="cta section-margin">
      <div class="container">
        <div class="row align-items-center justify-content-between">
          <div class="col-lg-4">
            <h3 class="heading-section">
              Get Ready to Learn and Grow Your Skill
            </h3>
            <p class="subheading-section mt-3">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id morbi
              purus lacus diam at mi in facilisis commodo.
            </p>
            <p class="subheading-section">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Id morbi
              purus lacus diam at mi in facilisis commodo.
            </p>

            <a href="#" class="btn-first mt-5 d-inline-block"
              >Mulai Belajar Sekarang</a
            >
          </div>
          <div class="col-lg-6 mt-5">
            <div class="thumbnail">
              <img
                src="vendor/images/20250619_150354.png"
                class="img-fluid"
                alt=""
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  
    <div class="container py-5" id="kontak">
        <!-- Header Kontak -->
        <div class="contact-header p-5 mb-5 text-center">
            <h1 class="display-5 fw-bold mb-3">Hubungi Kami</h1>
            <p class="lead mb-0">Kami siap membantu Anda dengan segala pertanyaan dan kebutuhan informasi</p>
        </div>

        <!-- Informasi Kontak -->
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="contact-card p-4 text-center">
                    <i class="bi bi-geo-alt-fill contact-icon"></i>
                    <h3 class="h4 fw-bold mb-3">Lokasi Kami</h3>
                    <p class="mb-0">Jl. KS Tubun No. 78<br>Bontang, Kalimantan Timur<br>Indonesia 75313</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="contact-card p-4 text-center">
                    <i class="bi bi-telephone-fill contact-icon"></i>
                    <h3 class="h4 fw-bold mb-3">Telepon</h3>
                    <p class="mb-2"><strong>Customer Service:</strong></p>
                    <p class="mb-0">+62 85247302382</p>
                    <p class="mb-0">+62 896 54664 2859 (WhatsApp)</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="contact-card p-4 text-center">
                    <i class="bi bi-envelope-fill contact-icon"></i>
                    <h3 class="h4 fw-bold mb-3">Email</h3>
                    <p class="mb-2"><strong>Informasi Umum:</strong></p>
                    <p class="mb-0">IT@learning.id</p>
                    <p class="mb-2 mt-3"><strong>Dukungan:</strong></p>
                    <p class="mb-0">support@learning.id</p>
                </div>
            </div>
        </div>

        <!-- Jam Operasional -->
      

        <!-- Media Sosial -->
        <div class="row">
            <div class="col-12">
                 <div class="d-flex justify-content-center">
                    <a href="#" class="social-icon bg-primary text-white me-3">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="social-icon bg-info text-white me-3">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="social-icon bg-danger text-white me-3">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="social-icon bg-success text-white me-3">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="#" class="social-icon bg-dark text-white">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    <!-- FOOTER START -->
    <footer class="footer section-margin">
      <div class="container">
        <div
          class="row row-content justify-content-between justify-content-md-start"
        >
          <div class="col-lg-2 col-md-6">
            <img src="vendor/images/logo.png" alt="" />
            <a href="#" class="email mt-4 d-inline-block text-white"
              >help@bsncreative.com</a
            >
            <p class="phone text-white">(0548) 220314</p>
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
              Jl KS Tubun No.78 Blok. A23 Bontang, Kalimantan Timur
            </p>
            <a href="#" class="maps text-white">Google Map</a>
          </div>
        </div>
        <div class="row text-center">
          <div class="col-12">
            <p class="text-white">
              &copy;Copyright 2025 all right reserved | Built by Berliana Sandra Nugraha
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
