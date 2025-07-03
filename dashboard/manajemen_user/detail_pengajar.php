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

if (!isset($_GET['id'])) {
    header("Location: data_pengajar.php");
    exit;
}

$user_id = $_GET['id'];

$query = "SELECT users.*, instructors.spesialisasi, instructors.pengalaman_mengajar_tahun, instructors.rating_rata_rata 
          FROM users 
          INNER JOIN instructors ON users.user_id = instructors.instructor_id 
          WHERE users.user_id = $user_id";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

$last_login_time = !empty($data['last_login']) ? strtotime($data['last_login']) : null;
$is_online = $last_login_time && (time() - $last_login_time <= 600);
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
            <div class="menu-item " onclick="window.location.href='../dashboard.php'">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
             </div>

            <!-- FITUR ADMINISTRATOR -->
            <div class="menu-category">Administrator</div>
            
            <!-- 1) Manajemen Pengguna -->
            <div class="menu-item active" onclick="toggleDropdown('user-dropdown')">
                <i class="fas fa-users"></i>
                <span>Manajemen User</span>
                <i class="fas fa-chevron-down ml-auto" id="user-chevron"></i>
            </div>
            <div class="menu-dropdown" id="user-dropdown">
                <div class="dropdown-item" onclick="window.location.href='data_siswa.php'">Data Siswa</div>
                <div class="dropdown-item" onclick="window.location.href='data_pengajar.php'">Data Pengajar</div>
            </div>


            <!-- 2) Manajemen Kursus -->
            <div class="menu-item" onclick="toggleDropdown('course-dropdown')">
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
             <div class="menu-item" onclick="window.location.href='../pendaftaran_kursus/pendaftaran.php'">
                <i class="fas fa-clipboard-list"></i>
                <span>Pendaftaran Kursus</span>
            </div>

            <!-- 5) Forum & Tugas -->
            <div class="menu-item" onclick="toggleDropdown('activity-dropdown')">
                <i class="fas fa-tasks"></i>
                <span>Aktivitas Belajar</span>
                <i class="fas fa-chevron-down ml-auto" id="activity-chevron"></i>
            </div>
            <div class="menu-dropdown" id="activity-dropdown">
                <div class="dropdown-item" onclick="window.location.href='../forum/forum.php'">Forum Diskusi</div>
                <div class="dropdown-item"onclick="window.location.href='../tugas/tugas.php'">Tugas & Penilaian</div>
                <div class="dropdown-item" onclick="window.location.href='../pengumpulan/pengumpulan.php'">Submissions</div>
            </div>

            <!-- FITUR REPORT -->
            <div class="menu-category">Laporan</div>
            
              <div class="menu-item" onclick="window.location.href='../pembayaran/laporan_pembayaran.php'">
                <i class="fas fa-money-bill-wave"></i>
                <span>Laporan Pembayaran</span>
            </div>
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
            <h1>Data Pengajar</h1>
            <p>Selamat datang kembali, Admin</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
                <h3 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Detail Profil Pengajar</h3>
                <div>
                    <?php if ($is_online): ?>
                        <span class="badge bg-success rounded-pill px-3 py-2">
                            <i class="fas fa-circle me-1"></i> Online
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                            <i class="fas fa-clock me-1"></i>
                            <?= $data['last_login'] ? 'Terakhir login: ' . date("d M Y H:i", $last_login_time) : 'Belum login' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-user-tie text-primary fs-3"></i>
                            </div>
                            <div class="ms-4">
                                <h4 class="mb-1"><?= htmlspecialchars($data['nama_lengkap']) ?></h4>
                                <span class="badge bg-info text-dark text-capitalize"><?= $data['role'] ?></span>
                            </div>
                        </div>

                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div><i class="fas fa-envelope text-muted me-2"></i> Email</div>
                                <span class="fw-bold"><?= htmlspecialchars($data['email']) ?></span>
                            </div>

                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div><i class="fas fa-phone text-muted me-2"></i> Telepon</div>
                                <span class="fw-bold"><?= htmlspecialchars($data['telepon']) ?></span>
                            </div>

                            
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title border-bottom pb-2 mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>

                                <!-- Ganti bagian ini di Kolom Kanan -->
                                    <div class="mb-3">
                                        <h6 class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Alamat</h6>
                                        <p class="ps-4"><?= nl2br(htmlspecialchars($data['alamat'])) ?></p>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="text-muted"><i class="fas fa-code me-2"></i>Spesialisasi</h6>
                                        <p class="ps-4"><?= htmlspecialchars($data['spesialisasi']) ?></p>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="text-muted"><i class="fas fa-briefcase me-2"></i>Pengalaman Mengajar</h6>
                                        <p class="ps-4"><?= htmlspecialchars($data['pengalaman_mengajar_tahun']) ?> tahun</p>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="text-muted"><i class="fas fa-star me-2"></i>Rating Rata-rata</h6>
                                        <p class="ps-4"><?= htmlspecialchars($data['rating_rata_rata']) ?>/5</p>
                                    </div>

                                    <div class="mb-3">
                                        <h6 class="text-muted"><i class="fas fa-calendar-check me-2"></i>Tanggal Registrasi</h6>
                                        <p class="ps-4"><?= date("d M Y H:i", strtotime($data['tanggal_registrasi'])) ?></p>
                                    </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="data_pengajar.php" class="btn btn-outline-secondary">
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