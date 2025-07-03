<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../views/login.php");
    exit;
}
 $isLogin = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);

 if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
include '../../../database/koneksi.php';

// Validasi parameter ID modul
if (!isset($_GET['module_id']) || !is_numeric($_GET['module_id'])) {
    header("Location: modul.php");
    exit;
}

$modul_id = (int) $_GET['module_id'];

// Ambil data modul
$query = "SELECT m.*, c.nama_kursus, c.level FROM modules m 
          JOIN courses c ON m.course_id = c.course_id
          WHERE m.module_id = $modul_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data modul tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Sistem Pendaftaran Kursus Komputer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/dasboard.css">
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
            <div class="menu-item active" onclick="toggleDropdown('course-dropdown')">
                <i class="fas fa-book"></i>
                <span>Manajemen Kursus</span>
                <i class="fas fa-chevron-down ml-auto" id="course-chevron"></i>
            </div>
            <div class="menu-dropdown" id="course-dropdown">
                <div class="dropdown-item" onclick="window.location.href='daftar_kursus.php'">Daftar Kursus</div>
                <div class="dropdown-item" onclick="window.location.href='modul.php'">Modul & Materi</div>
                <div class="dropdown-item" onclick="window.location.href='pelajaran.php'">Pelajaran</div>
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
            <div class="menu-item" onclick="toggleDropdown('activity-dropdown')">
                <i class="fas fa-tasks"></i>
                <span>Aktivitas Belajar</span>
                <i class="fas fa-chevron-down ml-auto" id="activity-chevron"></i>
            </div>
            <div class="menu-dropdown" id="activity-dropdown">
                <div class="dropdown-item" onclick="window.location.href='../forum/forum.php'">Forum Diskusi</div>
                <div class="dropdown-item" onclick="window.location.href='../tugas/tugas.php'">Tugas & Penilaian</div>
                <div class="dropdown-item" onclick="window.location.href='../pengumpulan/pengumpulan.php'">Submissions</div>
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
            <h1>Daftar Kursus</h1>
            <p>Selamat datang kembali</p>
        </div>
    </div>

  <div class="container mt-5">
    <div class="card border-0 shadow-lg">
        <div class="card-header  text-white d-flex justify-content-between align-items-center py-3">
           
            
    </div>

       <div class="container mt-5">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0"><i class="fas fa-layer-group me-2"></i>Detail Modul</h3>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <h4 class="mb-3"><?= htmlspecialchars($data['nama_modul']) ?></h4>
                        <p><i class="fas fa-book me-2 text-muted"></i><strong>Kursus:</strong> <?= htmlspecialchars($data['nama_kursus']) ?></p>
                        <p><i class="fas fa-sort-numeric-up me-2 text-muted"></i><strong>Urutan Modul:</strong> <?= htmlspecialchars($data['urutan']) ?></p>
                        <p><i class="fas fa-layer-group me-2 text-muted"></i><strong>Level:</strong> <?= htmlspecialchars($data['level']) ?></p>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title border-bottom pb-2 mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Deskripsi Modul
                                </h5>
                                <p class="ps-2"><?= nl2br(htmlspecialchars($data['deskripsi_modul'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Kembali -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="modul.php?course_id=<?= $data['course_id'] ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

 <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>

   <script src="../../../js/dashboard.js"></script>
</body>
</html>