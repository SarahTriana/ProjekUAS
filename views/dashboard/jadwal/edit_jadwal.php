<?php
include '../../../database/koneksi.php';
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
$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan";
    exit;
}

$schedule_id = $_GET['id'];

// Ambil data jadwal
$query = "
    SELECT s.*, c.nama_kursus, u.nama_lengkap 
    FROM schedules s
    JOIN courses c ON s.course_id = c.course_id
    JOIN users u ON s.instructor_id = u.user_id
    WHERE s.schedule_id = '$schedule_id'
";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data jadwal tidak ditemukan.";
    exit;
}

// Ambil daftar kursus dan pengajar
$course_query = mysqli_query($conn, "SELECT * FROM courses");
$instructor_query = mysqli_query($conn, "
    SELECT u.user_id, u.nama_lengkap 
    FROM users u 
    JOIN instructors i ON u.user_id = i.instructor_id 
    WHERE u.role = 'pengajar'
");
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
    <style>
.card {
    border-radius: 0.75rem;
}
.card-header {
    border-radius: 0.75rem 0.75rem 0 0 !important;
}
.input-group-text {
    min-width: 45px;
    justify-content: center;
}
.form-control, .input-group-text {
    border: 1px solid #dee2e6;
}
.was-validated .form-control:invalid, .form-control.is-invalid {
    border-color: #dc3545;
}
.was-validated .form-control:valid, .form-control.is-valid {
    border-color: #198754;
}
</style>
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
            <div class="menu-item " onclick="toggleDropdown('course-dropdown')">
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
                <h1>Data Siswa</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content">
<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0"><i class="fas fa-calendar-edit me-2"></i>Edit Jadwal Kursus</h4>
        </div>

        <div class="card-body p-4">
            <form action="../../../php/jadwal/update.php" method="POST" class="row g-3">
                <input type="hidden" name="schedule_id" value="<?= $data['schedule_id'] ?>">

                <!-- Nama Kursus -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Nama Kursus</label>
                    <select name="course_id" class="form-select" required>
                        <?php while ($course = mysqli_fetch_assoc($course_query)) { ?>
                            <option value="<?= $course['course_id'] ?>"
                                <?= ($course['course_id'] == $data['course_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($course['nama_kursus']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <!-- Pengajar -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pengajar</label>
                    <?php if ($role === 'pengajar'): ?>
                        <input type="hidden" name="instructor_id" value="<?= $user_id ?>">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" readonly>
                    <?php else: ?>
                        <select name="instructor_id" class="form-select" required>
                            <?php while ($instructor = mysqli_fetch_assoc($instructor_query)) { ?>
                                <option value="<?= $instructor['user_id'] ?>"
                                    <?= ($instructor['user_id'] == $data['instructor_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($instructor['nama_lengkap']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    <?php endif; ?>
                </div>

                <!-- Tanggal -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $data['tanggal_mulai'] ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="<?= $data['tanggal_selesai'] ?>" required>
                </div>

                <!-- Waktu -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai" class="form-control" value="<?= $data['waktu_mulai'] ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control" value="<?= $data['waktu_selesai'] ?>" required>
                </div>

                <!-- Hari, Kapasitas, Lokasi -->
                <div class="col-md-6">
                    <label class="form-label fw-bold">Hari Pelaksanaan</label>
                    <input type="text" name="hari_pelaksanaan" class="form-control" value="<?= htmlspecialchars($data['hari_pelaksanaan']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Kapasitas Maksimal</label>
                    <input type="number" name="kapasitas_maksimal" class="form-control" value="<?= $data['kapasitas_maksimal'] ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-bold">Lokasi Kelas</label>
                    <input type="text" name="lokasi_kelas" class="form-control" value="<?= htmlspecialchars($data['lokasi_kelas']) ?>" required>
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="jadwal.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    </div>
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

   <script src="../../../js/dashboard.js"></script>
</body>
</html>
