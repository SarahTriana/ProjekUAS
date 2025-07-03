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

// Ambil data kursus
$query = "SELECT * FROM courses";
$result = mysqli_query($conn, $query);


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

        <div class="content">
            
           <div class="container-fluid py-4">
            <?php if ($_SESSION['role'] === 'admin') : ?>

                <div class="card shadow-lg mb-5 border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Tambah Kursus Baru</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="../../../php/kursus/store.php" class="row g-3 needs-validation" novalidate>
                            <div class="col-md-6 mb-3">
                                <label for="nama_kursus" class="form-label">Nama Kursus</label>
                                <input type="text" class="form-control" name="nama_kursus" id="nama_kursus" required>
                                <div class="invalid-feedback">Harap isi nama kursus</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="durasi_jam" class="form-label">Durasi (jam)</label>
                                <input type="number" class="form-control" name="durasi_jam" id="durasi_jam" required>
                                <div class="invalid-feedback">Harap isi durasi</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="harga" class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" name="harga" id="harga" step="0.01" required>
                                <div class="invalid-feedback">Harap isi harga</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="level" class="form-label">Level</label>
                                <select class="form-select" name="level" id="level" required>
                                    <option value="">-- Pilih Level --</option>
                                    <option value="Dasar">Dasar</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Lanjut">Lanjut</option>
                                </select>
                                <div class="invalid-feedback">Harap pilih level</div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Kursus</label>
                                <textarea class="form-control" name="deskripsi" id="deskripsi" rows="4" required></textarea>
                                <div class="invalid-feedback">Harap isi deskripsi</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Aktif</label><br>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="status_aktif" name="status_aktif" value="1" checked>
                                    <label class="form-check-label" for="status_aktif">Aktif</label>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="fas fa-save me-2"></i>Simpan Kursus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

                 <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h4 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Kursus</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kursus</th>
                                        <th>Durasi (jam)</th>
                                        <th>Harga</th>
                                        <th>Level</th>
                                        <th>Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
                                            <td><?= $row['durasi_jam'] ?> jam</td>
                                            <td>Rp <?= number_format($row['harga'], 2, ',', '.') ?></td>
                                            <td><?= $row['level'] ?></td>
                                            <td>
                                                <span class="badge <?= $row['status_aktif'] ? 'bg-success' : 'bg-secondary' ?>">
                                                    <?= $row['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                <a href="detail_kursus.php?course_id=<?= $row['course_id'] ?>" class="btn btn-info text-white" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    <?php if ($_SESSION['role'] === 'admin') : ?>
                                                        <a href="edit_kursus.php?id=<?= $row['course_id'] ?>" class="btn btn-warning btn-sm text-white" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>

                                                        <a href="../../../php/kursus/delete.php?id=<?= $row['course_id'] ?>" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Yakin ingin menghapus kursus ini?')" title="Hapus">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php if (mysqli_num_rows($result) == 0): ?>
                                        <tr><td colspan="6" class="text-center text-muted">Belum ada kursus</td></tr>
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