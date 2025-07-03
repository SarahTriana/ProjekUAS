<?php
session_start();
 

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pengajar', 'admin'])) {
    header("Location: ../../../views/login.php");
    exit;
}
 $isLogin = isset($_SESSION['user_id']);
$currentPage = basename($_SERVER['PHP_SELF']);

 if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}

include '../../../database/koneksi.php';

$user_id = (int) $_SESSION['user_id'];

 $kursus_query = mysqli_query($conn, "SELECT course_id, nama_kursus FROM courses");

if ($_SESSION['role'] === 'pengajar') {
    $modul_query = mysqli_query($conn, "
        SELECT DISTINCT m.*, c.nama_kursus, c.course_id
        FROM modules m
        JOIN courses c ON m.course_id = c.course_id
        JOIN schedules s ON c.course_id = s.course_id
        WHERE s.instructor_id = $user_id
        ORDER BY c.course_id, m.urutan ASC
    ");
} else {
    // admin lihat semua modul
    $modul_query = mysqli_query($conn, "
        SELECT m.*, c.nama_kursus, c.course_id
        FROM modules m
        JOIN courses c ON m.course_id = c.course_id
        ORDER BY c.course_id, m.urutan ASC
    ");
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
                <h1>Daftar Modul</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content">
            
<div class="container-fluid py-4">
<!-- Form Tambah Modul -->
<div class="card shadow-lg mb-5 border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-layer-group me-2"></i>Tambah Modul</h4>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="../../../php/modul/store.php" class="row g-3 needs-validation" novalidate>
    <div class="col-md-6 mb-3">
        <label for="course_id" class="form-label">Pilih Kursus</label>
        <select class="form-select" name="course_id" id="course_id" required>
            <option value="">-- Pilih Kursus --</option>
            <?php while ($k = mysqli_fetch_assoc($kursus_query)): ?>
                <option value="<?= $k['course_id'] ?>"><?= htmlspecialchars($k['nama_kursus']) ?></option>
            <?php endwhile; ?>
        </select>
        <div class="invalid-feedback">Harap pilih kursus</div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="nama_modul" class="form-label">Nama Modul</label>
        <input type="text" class="form-control" name="nama_modul" id="nama_modul" required>
        <div class="invalid-feedback">Harap isi nama modul</div>
    </div>

    <div class="col-12 mb-3">
        <label for="deskripsi_modul" class="form-label">Deskripsi Modul</label>
        <textarea class="form-control" name="deskripsi_modul" id="deskripsi_modul" rows="3" required></textarea>
        <div class="invalid-feedback">Harap isi deskripsi</div>
    </div>

    <div class="col-md-3 mb-3">
        <label for="urutan" class="form-label">Urutan Modul</label>
        <input type="number" class="form-control" name="urutan" id="urutan" required>
        <div class="invalid-feedback">Harap isi urutan modul</div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Modul</button>
    </div>
</form>

    </div>
</div>

<!-- Tabel Daftar Modul -->
<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Modul Kursus</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kursus</th>
                        <th>Urutan</th>
                        <th>Nama Modul</th>
                        <th>Deskripsi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($modul_query) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($modul_query)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
                                <td><?= $row['urutan'] ?></td>
                                <td><?= htmlspecialchars($row['nama_modul']) ?></td>
                                <td><?= nl2br(htmlspecialchars($row['deskripsi_modul'])) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="detail_modul.php?module_id=<?= $row['module_id'] ?>" class="btn btn-info text-white" title="Detail">
                                           <i class="fas fa-eye"></i>
                                       </a>
                                       <a href="edit_modul.php?module_id=<?= $row['module_id'] ?>" class="btn btn-warning text-white" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                       <a href="../../../php/modul/delete.php?module_id=<?= $row['module_id'] ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus modul ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>


                                    </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada modul ditambahkan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
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