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

$student_id = $_GET['id'];

$query = "SELECT students.*, users.user_id, users.nama_lengkap, users.email, users.telepon, users.alamat, users.last_login 
FROM students 
INNER JOIN users ON students.student_id = users.user_id 
WHERE students.student_id = $student_id";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data siswa tidak ditemukan.";
    exit;
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
                <h1>Data Siswa</h1>
                <p>Selamat datang kembali, Admin</p>
            </div>
        
        </div>

        <div class="content">
<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>Edit Data Siswa
                </h4>
                <span class="badge bg-light text-dark">
                    ID: <?= htmlspecialchars($data['user_id']) ?>
                </span>
            </div>
        </div>
        
        <div class="card-body p-4">
            <form action="../../../php/siswa/update.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="user_id" value="<?= $data['user_id'] ?>">
                
                <div class="row g-3">
                    <!-- Nama Lengkap -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-user text-primary"></i>
                            </span>
                            <input type="text" name="nama_lengkap" class="form-control" 
                                   value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
                            <div class="invalid-feedback">
                                Harap isi nama lengkap
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-envelope text-primary"></i>
                            </span>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($data['email']) ?>" required>
                            <div class="invalid-feedback">
                                Harap isi email yang valid
                            </div>
                        </div>
                    </div>
                    
                    <!-- Telepon -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-phone text-primary"></i>
                            </span>
                            <input type="text" name="telepon" class="form-control" 
                                   value="<?= htmlspecialchars($data['telepon']) ?>">
                        </div>
                    </div>
                    
                    <!-- Tanggal Lahir -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanggal Lahir</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-calendar-day text-primary"></i>
                            </span>
                            <input type="date" name="tanggal_lahir" class="form-control" 
                                   value="<?= htmlspecialchars($data['tanggal_lahir']) ?>">
                        </div>
                    </div>
                    
                    <!-- Pendidikan Terakhir -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Pendidikan Terakhir</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-graduation-cap text-primary"></i>
                            </span>
                            <input type="text" name="pendidikan_terakhir" class="form-control" 
                                   value="<?= htmlspecialchars($data['pendidikan_terakhir']) ?>">
                        </div>
                    </div>
                    
                    <!-- Alamat -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light align-items-start">
                                <i class="fas fa-map-marker-alt text-primary mt-2"></i>
                            </span>
                            <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($data['alamat']) ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="col-12 mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="data_siswa.php" class="btn btn-outline-secondary me-md-2 px-4">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
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
