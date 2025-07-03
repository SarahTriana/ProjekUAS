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
    header("Location: jadwal.php");
    exit;
}

$schedule_id = $_GET['id'];

// Ambil detail jadwal
$query = "SELECT s.*, c.nama_kursus, c.status_aktif, u.nama_lengkap AS nama_pengajar
          FROM schedules s
          JOIN courses c ON s.course_id = c.course_id
          JOIN users u ON s.instructor_id = u.user_id
          WHERE s.schedule_id = $schedule_id";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data jadwal tidak ditemukan.";
    exit;
}

if ($data['status_aktif'] == 1) {
    $status_text = 'Aktif';
    $status_badge = 'bg-success';
} else {
    $status_text = 'Nonaktif';
    $status_badge = 'bg-secondary';
}

$schedule_id = $data['schedule_id'];
$queryPendaftar = mysqli_query($conn, "SELECT COUNT(*) as total FROM enrollments WHERE schedule_id = $schedule_id AND status_pendaftaran IN ('pending', 'diterima', 'selesai')");
$jumlahPendaftar = 0;
if ($rowPendaftar = mysqli_fetch_assoc($queryPendaftar)) {
    $jumlahPendaftar = $rowPendaftar['total'];
}

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
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .schedule-card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border-left: 4px solid #4e73df;
        }
        .schedule-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
        }
        .badge-online {
            background-color: #1cc88a;
        }
        .badge-offline {
            background-color: #36b9cc;
        }
        .info-icon {
            color: #4e73df;
            margin-right: 6px;
            font-size: 0.9rem;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .session-badge {
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
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
            <div class="menu-item active" onclick="window.location.href='jadwal.php'">
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
                <h1>Jadwal Kelas</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

 <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0 text-gray-800"><i class="bi bi-calendar2-week"></i> Detail Jadwal Kelas</h2>
     </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card schedule-card">
               <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary"><?= htmlspecialchars($data['nama_kursus']) ?></h4>
                    <span class="badge <?= $status_badge ?>">
                        <?= $status_text ?>
                    </span>
                </div>
            </div>



                <div class="card-body">
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="detail-item">
                                <p class="mb-1 small text-muted"><i class="bi bi-calendar-check info-icon"></i> Periode Kelas</p>
                                <p class="ps-3"><?= date('d M Y', strtotime($data['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($data['tanggal_selesai'])) ?></p>
                            </div>

                            <div class="detail-item">
                                <p class="mb-1 small text-muted"><i class="bi bi-clock info-icon"></i> Jam Mengajar</p>
                                <p class="ps-3"><?= date('H:i', strtotime($data['waktu_mulai'])) ?> - <?= date('H:i', strtotime($data['waktu_selesai'])) ?> WIB</p>
                            </div>

                            <div class="detail-item">
                                <p class="mb-1 small text-muted"><i class="bi bi-calendar-week info-icon"></i> Hari</p>
                                <p class="ps-3"><?= htmlspecialchars($data['hari_pelaksanaan']) ?></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                          <div class="detail-item">
                            <p class="mb-1 small text-muted"><i class="bi bi-people info-icon"></i> Kapasitas Kelas</p>
                            <p class="ps-3"><?= $jumlahPendaftar ?>/<?= $data['kapasitas_maksimal'] ?> siswa</p>
                            </div>


                            <div class="detail-item">
                                <p class="mb-1 small text-muted"><i class="bi bi-geo-alt info-icon"></i> Lokasi</p>
                                <p class="ps-3"><?= htmlspecialchars($data['lokasi_kelas']) ?></p>
                                <span class="badge badge-online text-white session-badge">
                                    <?= stripos($data['lokasi_kelas'], 'online') !== false ? 'Online Class' : 'Offline Class' ?>
                                </span>
                            </div>

                            <div class="detail-item">
                                <p class="mb-1 small text-muted"><i class="bi bi-journal-text info-icon"></i> Pengajar</p>
                                <p class="ps-3"><?= htmlspecialchars($data['nama_pengajar']) ?></p>
                            </div>
                        </div>
                    </div>

 
                  
                </div>

                <div class="card-footer bg-white py-2">
                    <div class="d-flex justify-content-between">
                        <a href="jadwal.php" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-left"></i> Kembali
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

   <script src="../../../js/dashboard.js"></script>
</body>
</html>






  
    

  