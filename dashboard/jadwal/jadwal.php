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

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ambil daftar kursus
$course_query = mysqli_query($conn, "SELECT course_id, nama_kursus FROM courses");

// Ambil daftar pengajar
$instructor_query = mysqli_query($conn, "
    SELECT u.user_id, u.nama_lengkap 
    FROM users u 
    JOIN instructors i ON u.user_id = i.instructor_id 
    WHERE u.role = 'pengajar'
");

// Ambil data jadwal
if ($role == 'pengajar') {
    $query = "
        SELECT s.*, c.nama_kursus, u.nama_lengkap AS nama_pengajar
        FROM schedules s
        JOIN courses c ON s.course_id = c.course_id
        JOIN users u ON s.instructor_id = u.user_id
        WHERE s.instructor_id = '$user_id'
    ";
} else {
    $query = "
        SELECT s.*, c.nama_kursus, u.nama_lengkap AS nama_pengajar
        FROM schedules s
        JOIN courses c ON s.course_id = c.course_id
        JOIN users u ON s.instructor_id = u.user_id
    ";
}
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
                <h1>Data Siswa</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>
 
    <div class="content">
 <div class="container-fluid py-4">
    <div class="card shadow-lg mb-5 border-0">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Tambah Jadwal Kursus</h4>
        </div>
        <div class="card-body p-4">
            <div class="container">
        <h3 class="mb-4">Tambah Jadwal Kursus</h3>
        <form action="../../../php/jadwal/store.php" method="POST" class="row g-3">

            <!-- Pilih Kursus -->
            <div class="col-md-6">
                <label for="course_id" class="form-label">Nama Kursus</label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">-- Pilih Kursus --</option>
                    <?php while ($course = mysqli_fetch_assoc($course_query)) { ?>
                        <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['nama_kursus']) ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Pengajar -->
            <div class="col-md-6">
                <label for="instructor_id" class="form-label">Pengajar</label>
                <?php if ($role == 'pengajar'): ?>
                    <?php
                        $getNama = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE user_id = '$user_id'");
                        $nama_pengajar = mysqli_fetch_assoc($getNama)['nama_lengkap'];
                    ?>
                    <input type="hidden" name="instructor_id" value="<?= $user_id ?>">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($nama_pengajar) ?>" readonly>
                <?php else: ?>
                    <select name="instructor_id" id="instructor_id" class="form-select" required>
                        <option value="">-- Pilih Pengajar --</option>
                        <?php while ($instructor = mysqli_fetch_assoc($instructor_query)) { ?>
                            <option value="<?= $instructor['user_id'] ?>"><?= htmlspecialchars($instructor['nama_lengkap']) ?></option>
                        <?php } ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Tanggal -->
            <div class="col-md-6">
                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
            </div>

            <!-- Waktu -->
            <div class="col-md-6">
                <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" id="waktu_mulai" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" id="waktu_selesai" class="form-control" required>
            </div>

            <!-- Hari dan Lokasi -->
            <div class="col-md-6">
                <label for="hari_pelaksanaan" class="form-label">Hari Pelaksanaan</label>
                <input type="text" name="hari_pelaksanaan" id="hari_pelaksanaan" class="form-control" placeholder="Contoh: Senin, Rabu, Jumat" required>
            </div>

            <div class="col-md-6">
                <label for="kapasitas_maksimal" class="form-label">Kapasitas Maksimal</label>
                <input type="number" name="kapasitas_maksimal" id="kapasitas_maksimal" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label for="lokasi_kelas" class="form-label">Lokasi Kelas</label>
                <input type="text" name="lokasi_kelas" id="lokasi_kelas" class="form-control" placeholder="Contoh: Online atau Gedung A, Lantai 2" required>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
        </div>
    </div>
</div>
<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Daftar Jadwal Kursus</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="jadwalTable">
                <thead class="table-light">
                    <tr>
                        <th>Nama Kursus</th>
                        <th>Pengajar</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Hari</th>
                        <th>Lokasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
                            <td><?= htmlspecialchars($row['nama_pengajar']) ?></td>
                            <td><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?> -
                                <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?></td>
                            <td><?= date('H:i', strtotime($row['waktu_mulai'])) ?> -
                                <?= date('H:i', strtotime($row['waktu_selesai'])) ?></td>
                            <td><?= htmlspecialchars($row['hari_pelaksanaan']) ?></td>
                            <td><?= htmlspecialchars($row['lokasi_kelas']) ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="detail_jadwal.php?id=<?= $row['schedule_id'] ?>" class="btn btn-info text-white" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_jadwal.php?id=<?= $row['schedule_id'] ?>" class="btn btn-warning text-white" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                   <a href="../../../php/jadwal/delete.php?id=<?= $row['schedule_id'] ?>" 
                                    class="btn btn-danger" 
                                    onclick="return confirm('Yakin ingin menghapus jadwal ini?')"
                                    title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada jadwal yang tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Menampilkan <span><?= mysqli_num_rows($result) ?></span> jadwal
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#">Sebelumnya</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">Selanjutnya</a></li>
                </ul>
            </nav>
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