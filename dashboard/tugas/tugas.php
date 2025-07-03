<?php
session_start();

// Izinkan hanya admin atau pengajar
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pengajar', 'admin'])) {
    header("Location: ../../../views/login.php");
    exit;
}

include '../../../database/koneksi.php';

$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'];

// ============================
// Ambil Pelajaran Sesuai Pengajar
// (untuk dropdown input tugas)
// ============================
if ($role === 'pengajar') {
 $lesson_query = mysqli_query($conn, "
    SELECT 
        l.lesson_id, 
        l.nama_pelajaran, 
        m.nama_modul, 
        m.urutan,
        c.nama_kursus, 
        c.course_id
    FROM lessons l
    JOIN modules m ON l.module_id = m.module_id
    JOIN courses c ON m.course_id = c.course_id
    JOIN schedules s ON c.course_id = s.course_id
    WHERE s.instructor_id = $user_id
    ORDER BY c.course_id, m.urutan, l.lesson_id ASC
");

} else {
    // Admin bisa melihat semua pelajaran
    $lesson_query = mysqli_query($conn, "
        SELECT l.lesson_id, l.nama_pelajaran, m.nama_modul, c.nama_kursus
        FROM lessons l
        JOIN modules m ON l.module_id = m.module_id
        JOIN courses c ON m.course_id = c.course_id
        ORDER BY c.course_id, m.urutan, l.lesson_id ASC
    ");
}

// ============================
// Ambil Assignment (Tugas)
// ============================
if ($role === 'pengajar') {
   $assignment_query = mysqli_query($conn, "
    SELECT 
        a.assignment_id,
        a.judul_tugas,
        a.deskripsi_tugas,
        a.tanggal_batas_akhir,
        a.poin_maksimal,
        l.nama_pelajaran,
        m.nama_modul,
        m.urutan,
        c.nama_kursus,
        c.course_id
    FROM assignments a
    JOIN lessons l ON a.lesson_id = l.lesson_id
    JOIN modules m ON l.module_id = m.module_id
    JOIN courses c ON m.course_id = c.course_id
    JOIN schedules s ON c.course_id = s.course_id
    WHERE s.instructor_id = $user_id
    ORDER BY c.course_id, m.urutan ASC
");

} else {
    // Admin bisa melihat semua tugas
    $assignment_query = mysqli_query($conn, "
        SELECT a.*, l.nama_pelajaran, m.nama_modul, c.nama_kursus
        FROM assignments a
        JOIN lessons l ON a.lesson_id = l.lesson_id
        JOIN modules m ON l.module_id = m.module_id
        JOIN courses c ON m.course_id = c.course_id
        ORDER BY a.tanggal_batas_akhir DESC
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
            <div class="menu-item active" onclick="toggleDropdown('activity-dropdown')">
                <i class="fas fa-tasks"></i>
                <span>Aktivitas Belajar</span>
                <i class="fas fa-chevron-down ml-auto" id="activity-chevron"></i>
            </div>
            <div class="menu-dropdown" id="activity-dropdown">
                <div class="dropdown-item" onclick="window.location.href='../forum/forum.php'">Forum Diskusi</div>
                <div class="dropdown-item" onclick="window.location.href='tugas.php'">Tugas & Penilaian</div>
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
                <h1>Daftar Pelajaran</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content">
            
<div class="container-fluid py-4">
<!-- Form Tambah Pelajaran -->
<div class="card shadow-lg mb-5 border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>Tambah Tugas</h4>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="../../../php/tugas/store.php" class="row g-3 needs-validation" novalidate>
            
            <!-- Pilih Pelajaran -->
            <div class="col-md-6 mb-3">
                <label for="lesson_id" class="form-label">Pilih Pelajaran</label>
                <select class="form-select" name="lesson_id" id="lesson_id" required>
                    <option value="">-- Pilih Pelajaran --</option>
                    <?php while ($l = mysqli_fetch_assoc($lesson_query)) : ?>
                        <option value="<?= $l['lesson_id'] ?>">
                            <?= htmlspecialchars($l['nama_kursus']) ?> - <?= htmlspecialchars($l['nama_modul']) ?> - <?= htmlspecialchars($l['nama_pelajaran']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="invalid-feedback">Harap pilih pelajaran</div>
            </div>

            <!-- Judul Tugas -->
            <div class="col-md-6 mb-3">
                <label for="judul_tugas" class="form-label">Judul Tugas</label>
                <input type="text" class="form-control" name="judul_tugas" id="judul_tugas" required>
                <div class="invalid-feedback">Harap isi judul tugas</div>
            </div>

            <!-- Deskripsi -->
            <div class="col-12 mb-3">
                <label for="deskripsi_tugas" class="form-label">Deskripsi Tugas</label>
                <textarea class="form-control" name="deskripsi_tugas" id="deskripsi_tugas" rows="4" required></textarea>
                <div class="invalid-feedback">Harap isi deskripsi tugas</div>
            </div>

            <!-- Tanggal Batas Akhir -->
            <div class="col-md-6 mb-3">
                <label for="tanggal_batas_akhir" class="form-label">Batas Akhir Pengumpulan</label>
                <input type="datetime-local" class="form-control" name="tanggal_batas_akhir" id="tanggal_batas_akhir" required>
                <div class="invalid-feedback">Harap isi tanggal batas akhir</div>
            </div>

            <!-- Poin Maksimal -->
            <div class="col-md-6 mb-3">
                <label for="poin_maksimal" class="form-label">Poin Maksimal</label>
                <input type="number" class="form-control" name="poin_maksimal" id="poin_maksimal" required>
                <div class="invalid-feedback">Harap isi poin maksimal</div>
            </div>

            <!-- Tombol Simpan -->
            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-save me-2"></i>Simpan Tugas
                </button>
            </div>
        </form>
    </div>
</div>


<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>Daftar Tugas</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kursus</th>
                        <th>Modul</th>
                        <th>Pelajaran</th>
                        <th>Judul Tugas</th>
                        <th>Batas Akhir</th>
                        <th>Poin Maksimal</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($assignment_query) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($assignment_query)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
                                <td><?= htmlspecialchars($row['nama_modul']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pelajaran']) ?></td>
                                <td><?= htmlspecialchars($row['judul_tugas']) ?></td>
                                <td>
                                    <?= date('d M Y H:i', strtotime($row['tanggal_batas_akhir'])) ?>
                                </td>
                                <td><?= (int)$row['poin_maksimal'] ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="detail.php?assignment_id=<?= $row['assignment_id'] ?>" class="btn btn-info text-white" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit.php?assignment_id=<?= $row['assignment_id'] ?>" class="btn btn-warning text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../../../php/tugas/hapus.php?assignment_id=<?= $row['assignment_id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus tugas ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada tugas ditambahkan.</td></tr>
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
 
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

   <script src="../../../js/dashboard.js"></script>
</body>
</html>