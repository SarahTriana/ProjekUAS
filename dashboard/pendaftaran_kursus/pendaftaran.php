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

// Ambil daftar pendaftaran
$query = "
    SELECT e.enrollment_id, u.nama_lengkap, c.nama_kursus, s.hari_pelaksanaan, s.tanggal_mulai, s.tanggal_selesai, e.tanggal_daftar, e.status_pendaftaran
    FROM enrollments e
    JOIN users u ON e.student_id = u.user_id
    JOIN schedules s ON e.schedule_id = s.schedule_id
    JOIN courses c ON s.course_id = c.course_id
    ORDER BY e.tanggal_daftar DESC
";
$result = mysqli_query($conn, $query);
// Cari data jika ada parameter ?search=...
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "
  SELECT enrollments.*, users.nama_lengkap, courses.nama_kursus, schedules.hari_pelaksanaan, 
         schedules.tanggal_mulai, schedules.tanggal_selesai
  FROM enrollments
  JOIN users ON enrollments.student_id = users.user_id
  JOIN schedules ON enrollments.schedule_id = schedules.schedule_id
  JOIN courses ON schedules.course_id = courses.course_id
  WHERE users.nama_lengkap LIKE '%$search%' 
     OR courses.nama_kursus LIKE '%$search%'
  ORDER BY enrollments.tanggal_daftar DESC
";
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
     <style>
        .dropdown-menu {
    position: fixed !important; /* Muncul di atas semua elemen */
    max-height: 200px; /* Batas tinggi dropdown (opsional) */
    overflow-y: auto;  /* Scroll hanya di dropdown jika tinggi melebihi max-height */
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
            <div class="menu-item" onclick="toggleDropdown('user-dropdown')">
                <i class="fas fa-users"></i>
                <span>Manajemen User</span>
                <i class="fas fa-chevron-down ml-auto" id="user-chevron"></i>
            </div>
             <div class="menu-dropdown" id="user-dropdown">
                <div class="dropdown-item" onclick="window.location.href='../manajemen_user/data_siswa.php'">Data Siswa</div>
                <div class="dropdown-item" onclick="window.location.href='../manajemen_user/data_pengajar.php'">Data Pengajar</div>
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
            <div class="menu-item active" onclick="window.location.href='pendaftaran.php'">
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
                <h1>Daftar Kursus</h1>
                <p>Selamat datang kembali, Admin</p>
            </div>
        
        </div>

        <div class="content">
            
            <div class="container-fluid py-4">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-gradient bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Daftar Pendaftaran Kursus</h4>
                    <span class="badge bg-light text-dark px-3 py-2">Total: <?= mysqli_num_rows($result) ?> Pendaftar</span>
                    </div>

                    <!-- 🔍 Search Filter -->
                    <div class="p-3 border-bottom bg-light">
                    <form class="row g-2 align-items-center">
                        <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Cari nama siswa atau kursus..." name="search">
                        </div>
                        <div class="col-auto">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Cari</button>
                        </div>
                        <div class="col-auto ms-auto">
                        <a href="../../../php/pendaftaran_kursus/export_exce.php" class="btn btn-outline-secondary"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                        <a href="../../../php/pendaftaran_kursus/cetak.php" target="_blank" class="btn btn-outline-secondary"><i class="fas fa-print me-1"></i> Cetak</a>
                         </div>
                    </form>
                    </div>

                    <!-- 📋 Tabel -->
                    <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                            <th><i class="fas fa-user me-1"></i>Nama Siswa</th>
                            <th><i class="fas fa-book me-1"></i>Kursus</th>
                            <th><i class="fas fa-calendar-alt me-1"></i>Hari</th>
                            <th><i class="fas fa-calendar me-1"></i>Tanggal</th>
                            <th><i class="fas fa-clock me-1"></i>Daftar</th>
                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                            <th class="text-center"><i class="fas fa-cogs me-1"></i>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
                                <td><?= htmlspecialchars($row['hari_pelaksanaan']) ?></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($row['tanggal_selesai'])) ?></td>
                                <td><span class="badge bg-light text-dark"><?= date('d M Y H:i', strtotime($row['tanggal_daftar'])) ?></span></td>
                                <td>
                              <span class="badge 
                                <?= $row['status_pendaftaran'] == 'pending' ? 'bg-warning' : 
                                ($row['status_pendaftaran'] == 'diterima' ? 'bg-success' : 
                                ($row['status_pendaftaran'] == 'selesai' ? 'bg-primary' : 'bg-danger')) ?>">
                                <?= ucfirst($row['status_pendaftaran']) ?>
                            </span>

                                </td>
                                <td class="text-center">
                                <!-- <a href="detail.php?id=<?= $row['enrollment_id'] ?>" style="color: white;" class="btn btn-info btn-sm me-1">
                                    <i class="fas fa-eye"></i> Detail
                                </a> -->
                                    <?php if (!in_array($row['status_pendaftaran'], ['dibatalkan', 'selesai'])): ?>
                                        <div class="dropdown">
                                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="fas fa-cog me-1"></i> Aksi
                                        </button>
                                        <ul class="dropdown-menu">

                                            <li>
                                            <form action="../../../php/pendaftaran_kursus/proses_penerimaan.php" method="POST" class="dropdown-item m-0 p-0">
                                                <input type="hidden" name="enrollment_id" value="<?= $row['enrollment_id'] ?>">
                                                <input type="hidden" name="aksi" value="pending">
                                                <button type="submit" class="btn w-100 text-start text-warning">
                                                <i class="fas fa-clock me-2"></i> Pending
                                                </button>
                                            </form>
                                            </li>

                                            <li>
                                            <form action="../../../php/pendaftaran_kursus/proses_penerimaan.php" method="POST" class="dropdown-item m-0 p-0">
                                                <input type="hidden" name="enrollment_id" value="<?= $row['enrollment_id'] ?>">
                                                <input type="hidden" name="aksi" value="diterima">
                                                <button type="submit" class="btn w-100 text-start text-success">
                                                <i class="fas fa-check-circle me-2"></i> Diterima
                                                </button>
                                            </form>
                                            </li>

                                            <li>
                                            <form action="../../../php/pendaftaran_kursus/proses_penerimaan.php" method="POST" class="dropdown-item m-0 p-0">
                                                <input type="hidden" name="enrollment_id" value="<?= $row['enrollment_id'] ?>">
                                                <input type="hidden" name="aksi" value="ditolak">
                                                <button type="submit" class="btn w-100 text-start text-danger">
                                                <i class="fas fa-times-circle me-2"></i> Ditolak
                                                </button>
                                            </form>
                                            </li>

                                        </ul>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Terkunci</span>
                                    <?php endif; ?>
 

                                </td>
                            </tr>
                            <?php endwhile; ?>

                            <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                Belum ada pendaftaran yang masuk.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>
                    </div>

                    <!-- 📌 Footer Table Info -->
                    <div class="card-footer text-muted d-flex justify-content-between align-items-center px-3">
                    <small><i class="fas fa-info-circle me-1"></i>Data pendaftar ditampilkan realtime berdasarkan status.</small>
                    <a href="#top" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-up"></i> Kembali ke Atas</a>
                    </div>
                </div>
            </div>



         
        </div>

        <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

   <script src="../../../js/dashboard.js"></script>
</body>
</html>