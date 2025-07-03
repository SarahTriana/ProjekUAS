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

// Validasi parameter ID kursus
if (!isset($_GET['course_id']) || !is_numeric($_GET['course_id'])) {
    header("Location: daftar_kursus.php");  
    exit;
}


$kursus_id = (int) $_GET['course_id'];

// Ambil data kursus
$query = "SELECT * FROM courses WHERE course_id = $kursus_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data kursus tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($result);

// Format tanggal registrasi
$tanggal_registrasi = !empty($data['tanggal_registrasi']) 
    ? date("d M Y H:i", strtotime($data['tanggal_registrasi'])) 
    : '-';
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
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h3 class="mb-0"><i class="fas fa-book-open me-2"></i>Detail Kursus</h3>
            <div>
                <span class="badge <?= $data['status_aktif'] ? 'bg-success' : 'bg-danger' ?> rounded-pill px-3 py-2">
                    <i class="fas fa-circle me-1"></i> <?= $data['status_aktif'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
            </div>
        </div>

      <div class="card-body p-4">
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 80px; height: 80px;">
                    <i class="fas fa-laptop-code text-primary fs-3"></i>
                </div>
                <div class="ms-4">
                    <h4 class="mb-1"><?= htmlspecialchars($data['nama_kursus']) ?></h4>
                    <span class="badge bg-primary text-white text-capitalize px-3 py-2"><?= htmlspecialchars($data['level']) ?></span>
                </div>
            </div>

            <ul class="list-group list-group-flush shadow-sm rounded">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-clock me-2 text-muted"></i>Durasi</div>
                    <span class="fw-semibold"><?= htmlspecialchars($data['durasi_jam']) ?> Jam</span>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-money-bill-wave me-2 text-muted"></i>Harga</div>
                    <span class="fw-semibold text-success">Rp <?= number_format($data['harga'], 0, ',', '.') ?></span>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><i class="fas fa-check-circle me-2 text-muted"></i>Status</div>
                    <span class="fw-semibold">
                        <?= $data['status_aktif'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>' ?>
                    </span>
                </li>
            </ul>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title border-bottom pb-2 mb-3">
                        <i class="fas fa-info-circle me-2"></i>Deskripsi Kursus
                    </h5>

                    <p class="ps-2"><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>

                   
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="d-flex justify-content-between mt-4">
        <a href="daftar_kursus.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
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