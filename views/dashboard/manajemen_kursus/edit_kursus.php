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

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='../../../auth/login.php';</script>";
    exit;
}

// Cek apakah ID kursus tersedia
if (!isset($_GET['id'])) {
    header("Location: daftar_kursus.php");
    exit;
}

$course_id = $_GET['id'];

// Ambil data kursus berdasarkan ID
$query = "SELECT * FROM courses WHERE course_id = $course_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data kursus tidak ditemukan.";
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
                <h1>Data Pengajar</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content">
<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-book me-2"></i>Edit Data Kursus
                </h4>
                <span class="badge bg-light text-dark">
                    ID: <?= htmlspecialchars($data['course_id']) ?>
                </span>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="../../../php/kursus/update.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="course_id" value="<?= $data['course_id'] ?>">

                <div class="row g-3">
                    <!-- Nama Kursus -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Kursus</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-book text-primary"></i></span>
                            <input type="text" name="nama_kursus" class="form-control" value="<?= htmlspecialchars($data['nama_kursus']) ?>" required>
                            <div class="invalid-feedback">Harap isi nama kursus</div>
                        </div>
                    </div>

                    <!-- Durasi Jam -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Durasi (jam)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-clock text-primary"></i></span>
                            <input type="number" name="durasi_jam" class="form-control" value="<?= htmlspecialchars($data['durasi_jam']) ?>" required>
                            <div class="invalid-feedback">Harap isi durasi kursus</div>
                        </div>
                    </div>

                    <!-- Harga -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-money-bill-wave text-primary"></i></span>
                            <input type="number" step="0.01" name="harga" class="form-control" value="<?= htmlspecialchars($data['harga']) ?>" required>
                            <div class="invalid-feedback">Harap isi harga kursus</div>
                        </div>
                    </div>

                    <!-- Level -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Level</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-signal text-primary"></i></span>
                            <select name="level" class="form-select" required>
                                <option value="">-- Pilih Level --</option>
                                <option value="Dasar" <?= $data['level'] == 'Dasar' ? 'selected' : '' ?>>Dasar</option>
                                <option value="Menengah" <?= $data['level'] == 'Menengah' ? 'selected' : '' ?>>Menengah</option>
                                <option value="Lanjut" <?= $data['level'] == 'Lanjut' ? 'selected' : '' ?>>Lanjut</option>
                            </select>
                            <div class="invalid-feedback">Harap pilih level</div>
                        </div>
                    </div>

                    <!-- Status Aktif -->
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Status Aktif</label><br>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" <?= $data['status_aktif'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_aktif">Aktif</label>
                    </div>
                  </div>


                    <!-- Deskripsi -->
                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi Kursus</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light align-items-start"><i class="fas fa-align-left text-primary mt-2"></i></span>
                            <textarea name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                            <div class="invalid-feedback">Harap isi deskripsi kursus</div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="col-12 mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="daftar_kursus.php" class="btn btn-outline-secondary me-md-2 px-4">
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
