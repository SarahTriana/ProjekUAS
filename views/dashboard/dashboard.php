<?php
include '../../database/koneksi.php';

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

$role = $_SESSION['role'];
 $siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'siswa'"));
$jumlah_siswaa = $siswa['total'];

 $pengajar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'pengajar'"));
$jumlah_pengajar = $pengajar['total'];

 $kursus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM courses"));
$jumlah_kursusa = $kursus['total'];

 $sertifikat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM certificates"));
$jumlah_sertifikata = $sertifikat['total'];


 $user_id = $_SESSION['user_id'];
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $user_id");
$user = mysqli_fetch_assoc($queryUser);

 $role = $user['role'];
$student = null;
$instructor = null;

if ($role == 'siswa') {
    $queryStudent = mysqli_query($conn, "SELECT * FROM students WHERE student_id = $user_id");
    $student = mysqli_fetch_assoc($queryStudent);
}

if ($role == 'pengajar') {
    $queryInstructor = mysqli_query($conn, "SELECT * FROM instructors WHERE instructor_id = $user_id");
    $instructor = mysqli_fetch_assoc($queryInstructor);
}
$instructor_id = $user['user_id'];  
$sql = "SELECT COUNT(DISTINCT enrollments.student_id) AS total_siswa
        FROM enrollments
        JOIN schedules ON enrollments.schedule_id = schedules.schedule_id
        WHERE schedules.instructor_id = $instructor_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$jumlah_siswa = $row['total_siswa'];
$sql = "SELECT COUNT(DISTINCT schedules.course_id) AS total_kursus
        FROM schedules
        WHERE schedules.instructor_id = $instructor_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$jumlah_kursus = $row['total_kursus'];

$sql = "SELECT COUNT(certificates.certificate_id) AS total_sertifikat
        FROM certificates
        JOIN enrollments ON certificates.enrollment_id = enrollments.enrollment_id
        JOIN schedules ON enrollments.schedule_id = schedules.schedule_id
        WHERE schedules.instructor_id = $instructor_id";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$jumlah_sertifikat = $row['total_sertifikat'];
if ($_SESSION['role'] == 'pengajar') {
    $pengajar_id = $_SESSION['user_id'];

    $query = "
        SELECT r.*, u.nama_lengkap 
        FROM reviews r
        JOIN users u ON r.student_id = u.user_id
        WHERE r.instructor_id = $pengajar_id
        ORDER BY r.tanggal_review DESC
        LIMIT 5
    ";

    $result = mysqli_query($conn, $query);
    $reviews = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Sistem Pendaftaran Kursus Komputer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dasboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background: linear-gradient(135deg, #6e8efb,rgb(4, 43, 132));
            color: white;
            padding: 20px;
            text-align: center;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid white;
            margin: 0 auto 15px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: #6e8efb;
        }
        .profile-details {
            padding: 20px;
        }
        .detail-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .edit-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-left: 1px solid #eee;
        }
        @media (max-width: 768px) {
            .edit-section {
                border-left: none;
                border-top: 1px solid #eee;
            }
        }
    </style>
     <style>
        .avatar {
            font-weight: 600;
            color: #6c757d;
        }
        .list-group-item {
            transition: background-color 0.2s;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
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
                <div class="menu-item active" onclick="window.location.href='dashboard.php'">
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
                        <div class="dropdown-item" onclick="window.location.href='./manajemen_user/data_siswa.php'">Data Siswa</div>
                        <div class="dropdown-item" onclick="window.location.href='./manajemen_user/data_pengajar.php'">Data Pengajar</div>
                    </div>
                <?php endif; ?>

                <!-- 2) Manajemen Kursus -->
                <div class="menu-item" onclick="toggleDropdown('course-dropdown')">
                    <i class="fas fa-book"></i>
                    <span>Manajemen Kursus</span>
                    <i class="fas fa-chevron-down ml-auto" id="course-chevron"></i>
                </div>
                <div class="menu-dropdown" id="course-dropdown">
                    <div class="dropdown-item" onclick="window.location.href='./manajemen_kursus/daftar_kursus.php'">Daftar Kursus</div>
                    <div class="dropdown-item" onclick="window.location.href='./manajemen_kursus/modul.php'">Modul & Materi</div>
                    <div class="dropdown-item" onclick="window.location.href='./manajemen_kursus/pelajaran.php'">Pelajaran</div>
                </div>

                <!-- 3) Jadwal & Kelas -->
                <div class="menu-item" onclick="window.location.href='./jadwal/jadwal.php'">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Jadwal Kelas</span>
                </div>

                <!-- 4) Pendaftaran -->
                <?php if ($_SESSION['role'] == 'admin') : ?>
                    <div class="menu-item" onclick="window.location.href='./pendaftaran_kursus/pendaftaran.php'">
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
                    <div class="dropdown-item" onclick="window.location.href='./forum/forum.php'">Forum Diskusi</div>
                    <div class="dropdown-item" onclick="window.location.href='./tugas/tugas.php'">Tugas & Penilaian</div>
                    <div class="dropdown-item" onclick="window.location.href='./pengumpulan/pengumpulan.php'">Submissions</div>
                </div>

                <!-- FITUR REPORT -->
                <div class="menu-category">Laporan</div>

                <?php if ($_SESSION['role'] == 'admin') : ?>
                    <div class="menu-item" onclick="window.location.href='./pembayaran/laporan_pembayaran.php'">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Laporan Pembayaran</span>
                    </div>
                <?php endif; ?>

                <div class="menu-item" onclick="window.location.href='./sertifikat/sertifikat.php'">
                    <i class="fas fa-certificate"></i>
                    <span>Laporan Sertifikat</span>
                </div>

                <div class="menu-item" onclick="window.location.href='../../php/logout.php'">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </div>
    </div>

 
    </div>

    <div class="main-content" id="mainContent">
        <div class="header">
            <div class="header-title">
                <h1>--Dashboard--</h1>
                <p>Akses cepat ke informasi penting Anda!</p>
            </div>
             
        </div>

        <div class="content">
            
            <div class="welcome-banner fade-in">
                <h2>Selamat Datang di Sistem IT Learning</h2>
                <p>Kelola pendaftaran kursus komputer dengan mudah dan efisien. Pantau statistik terbaru dan aktivitas peserta.</p>
            </div>
                <?php if ($user['role'] == 'admin') : ?>

            <div class="stats-container">
                <div class="stat-card fade-in delay-1">
                    <div class="stat-card-header">
                        <i class="fas fa-users"></i>
                        <h3>Jumlah Siswa</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_siswaa ?></div>
                    <div class="stat-card-diff positive">
                        <i class="fas fa-arrow-up"></i>
                        +12% dari kemarin
                    </div>
                </div>

                <div class="stat-card fade-in delay-2">
                    <div class="stat-card-header">
                        <i class="fas fa-book-open"></i>
                        <h3>Jumlah Pengajar</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_pengajar ?></div>
                    <div class="stat-card-diff positive">
                        <i class="fas fa-arrow-up"></i>
                        +2 program baru
                    </div>
                </div>

                <div class="stat-card fade-in delay-3">
                    <div class="stat-card-header">
                        <i class="fas fa-chart-line"></i>
                        <h3>Jumlah Kursus</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_kursusa ?></div>
                    <div class="stat-card-diff positive">
                        <i class="fas fa-arrow-up"></i>
                        +24% dari bulan lalu
                    </div>
                </div>

                <div class="stat-card fade-in delay-4">
                    <div class="stat-card-header">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Jumlah Sertifikat</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_sertifikata ?></div>
                    <div class="stat-card-diff negative">
                        <i class="fas fa-arrow-down"></i>
                        -5% dari bulan lalu
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($user['role'] == 'pengajar') : ?>
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <i class="fas fa-users"></i>
                        <h3>Jumlah Siswa</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_siswa ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <i class="fas fa-book-open"></i>
                        <h3>Kursus Yang Diajar</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_kursus ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Jumlah Sertifikat</h3>
                    </div>
                    <div class="stat-card-value"><?= $jumlah_sertifikat ?></div>
                </div>
            </div>
            <?php endif; ?>


                <div class="container my-5">
                    <div class="row">
                        <div class="col-md-12 mx-auto">
                            <div class="profile-card">
                                <div class="row g-0">

                                    <!-- Kiri -->
                                    <div class="col-md-7">
                                        <div class="profile-header">
                                            <div class="profile-avatar">
                                                <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                                            </div>
                                            <h3><?= $user['nama_lengkap'] ?></h3>
                                            <p class="badge bg-light text-dark"><?= ucfirst($user['role']) ?></p>
                                        </div>
                                    <div class="profile-details row">
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Email</div>
                                                <div><?= $user['email'] ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Telepon</div>
                                                <div><?= $user['telepon'] ?? '-' ?></div>
                                            </div>
                                            <div class="detail-item">
                                                <div class="detail-label">Alamat</div>
                                                <div><?= $user['alamat'] ?? '-' ?></div>
                                            </div>

                                            <?php if ($role == 'siswa') : ?>
                                            <div class="detail-item">
                                                <div class="detail-label">Pendidikan Terakhir</div>
                                                <div><?= $student['pendidikan_terakhir'] ?? '-' ?></div>
                                            </div>
                                            <?php endif; ?>

                                            <?php if ($role == 'pengajar') : ?>
                                            <div class="detail-item">
                                                <div class="detail-label">Spesialisasi</div>
                                                <div><?= $instructor['spesialisasi'] ?? '-' ?></div>
                                            </div>
                                            <?php endif; ?>
                                        </div>

                
                                            <div class="col-md-6">
                                                <?php if ($role == 'siswa') : ?>
                                                <div class="detail-item">
                                                    <div class="detail-label">Tanggal Lahir</div>
                                                    <div><?= isset($student['tanggal_lahir']) ? date('d F Y', strtotime($student['tanggal_lahir'])) : '-' ?></div>
                                                </div>
                                                <?php endif; ?>

                                                <?php if ($role == 'pengajar') : ?>
                                                <div class="detail-item">
                                                    <div class="detail-label">Pengalaman Mengajar</div>
                                                    <div><?= $instructor['pengalaman_mengajar_tahun'] ?? '0' ?> tahun</div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Rating</div>
                                                    <div>
                                                        <?php
                                                        $rating = $instructor['rating_rata_rata'] ?? 0;
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            echo $i <= $rating
                                                                ? '<i class="fas fa-star text-warning"></i>'
                                                                : '<i class="far fa-star text-warning"></i>';
                                                        }
                                                        echo " (" . number_format($rating, 1) . ")";
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            <?php date_default_timezone_set('Asia/Jakarta'); ?>

                                                <div class="detail-item">
                                                    <div class="detail-label">Tanggal Registrasi</div>
                                                    <div><?= date('d F Y H:i', strtotime($user['tanggal_registrasi'])) ?></div>
                                                </div>
                                                <div class="detail-item">
                                                    <div class="detail-label">Terakhir Login</div>
                                                    <div><?= $user['last_login'] ? date('d F Y H:i', strtotime($user['last_login'])) : 'Belum pernah login' ?></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Kanan -->
                                    <div class="col-md-5">
                                        <div class="edit-section h-100 d-flex flex-column">
                                            <h4 class="mb-4">Pengaturan Profil</h4>

                                            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                                <i class="fas fa-edit me-2"></i>Edit Profil
                                            </button>

                                            <button class="btn btn-outline-secondary mb-3" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                                <i class="fas fa-lock me-2"></i>Ganti Password
                                            </button>

                                            <div class="mt-auto">
                                                <div class="alert alert-info">
                                                    <small>
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        Terakhir diperbarui: <?= date('d F Y H:i') ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div> <!-- row -->
                            </div> <!-- profile card -->
                        </div>
                    </div>
                </div>

               <?php if ($_SESSION['role'] == 'pengajar') : ?>
                    <div class="card mt-4 border-0 shadow">
                        <div class="card-header bg-primary text-white py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-star me-2"></i>Ulasan dari Siswa
                                </h5>
                                <?php if (!empty($reviews)) : ?>
                                    <span class="badge bg-light text-primary">
                                        <?= count($reviews) ?> Ulasan
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-body p-0">
                            <?php if (empty($reviews)) : ?>
                                <div class="text-center py-4">
                                    <div class="py-3">
                                        <i class="far fa-comment-alt text-muted fa-3x"></i>
                                    </div>
                                    <h6 class="text-muted">Belum ada ulasan</h6>
                                    <p class="text-muted small mb-0">Siswa belum memberikan penilaian</p>
                                </div>
                            <?php else : ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($reviews as $review) : ?>
                                        <div class="list-group-item border-0 py-3 px-4">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <?= strtoupper(substr($review['nama_lengkap'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <strong><?= htmlspecialchars($review['nama_lengkap']) ?></strong>
                                                        <small class="text-muted"><?= date('d M Y', strtotime($review['tanggal_review'])) ?></small>
                                                    </div>
                                                    <div class="mb-2">
                                                        <span class="text-warning">
                                                            <?php for ($i = 0; $i < $review['rating']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                                                            <?php for ($i = $review['rating']; $i < 5; $i++) echo '<i class="far fa-star"></i>'; ?>
                                                        </span>
                                                        <span class="ms-2 small text-muted"><?= $review['rating'] ?>.0</span>
                                                    </div>
                                                    <p class="mb-0 text-break"><?= nl2br(htmlspecialchars($review['komentar'])) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                
                <?php endif; ?>

   


                <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Profil</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form action="../../php/profil/update.php" method="POST">
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" name="nama_lengkap" value="<?= $user['nama_lengkap'] ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="telepon" class="form-label">Telepon</label>
                                            <input type="text" class="form-control" name="telepon" value="<?= $user['telepon'] ?? '' ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="alamat" class="form-label">Alamat</label>
                                            <textarea class="form-control" name="alamat"><?= $user['alamat'] ?? '' ?></textarea>
                                        </div>
                                    </div>

                                    <?php if ($user['role'] == 'siswa') : ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Pendidikan Terakhir</label>
                                            <select class="form-select" name="pendidikan_terakhir">
                                                <?php
                                                $options = ['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'];
                                                foreach ($options as $opt) {
                                                    $selected = ($student['pendidikan_terakhir'] ?? '') == $opt ? 'selected' : '';
                                                    echo "<option value='$opt' $selected>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" name="tanggal_lahir" value="<?= $student['tanggal_lahir'] ?? '' ?>">
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <?php if ($user['role'] == 'pengajar') : ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Spesialisasi</label>
                                            <input type="text" class="form-control" name="spesialisasi" value="<?= $instructor['spesialisasi'] ?? '' ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Pengalaman Mengajar (tahun)</label>
                                            <input type="number" class="form-control" name="pengalaman_mengajar_tahun" value="<?= $instructor['pengalaman_mengajar_tahun'] ?? 0 ?>" min="0">
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary" name="update_profile">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                 <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Ganti Password</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="../../php/profil/password.php" method="POST">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Password Saat Ini</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" class="btn btn-primary">Ganti Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                
                
                
            </div>
            
            <div class="footer">
                <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
   <script src="../../js/dashboard.js"></script>
</body>
</html>