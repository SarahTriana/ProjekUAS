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

 
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$query = "
  SELECT 
    e.enrollment_id,
    u.nama_lengkap,
    c.nama_kursus,
    s.hari_pelaksanaan,
    s.tanggal_mulai,
    s.tanggal_selesai,
    e.tanggal_daftar,
    e.status_pendaftaran,

    p.payment_id,
    p.jumlah_pembayaran,
    p.tanggal_pembayaran,
    p.metode_pembayaran,
    p.status_pembayaran,
    p.kode_referensi_bank

  FROM enrollments e
  JOIN users u ON e.student_id = u.user_id
  JOIN schedules s ON e.schedule_id = s.schedule_id
  JOIN courses c ON s.course_id = c.course_id
  INNER JOIN payments p ON p.enrollment_id = e.enrollment_id
  WHERE u.nama_lengkap LIKE '%$search%' 
     OR c.nama_kursus LIKE '%$search%'
  ORDER BY e.tanggal_daftar DESC
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
             <div class="menu-item " onclick="window.location.href='../pendaftaran_kursus/pendaftaran.php'">
                <i class="fas fa-clipboard-list"></i>
                <span>Manajemen Pendaftaran</span>
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
             
            <div class="menu-item active" onclick="window.location.href='../pembayaran/laporan_pembayaran.php'">
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
                <h1>Laporan Pembayaran</h1>
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

      <!-- 🔍 Form Pencarian -->
      <div class="p-3 border-bottom bg-light">
        <form class="row g-2 align-items-center">
          <div class="col-md-4">
            <input type="text" class="form-control" placeholder="Cari nama siswa atau kursus..." name="search" value="<?= htmlspecialchars($search) ?>">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Cari</button>
          </div>
          <div class="col-auto ms-auto">
            <a href="../../../php/pendaftaran_kursus/export_exce.php" class="btn btn-outline-secondary">
              <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="../../../php/pendaftaran_kursus/cetak.php" target="_blank" class="btn btn-outline-secondary">
              <i class="fas fa-print me-1"></i> Cetak
            </a>
          </div>
        </form>
      </div>

      <!-- 📋 Tabel -->
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
           <thead class="table-light">
  <tr>
    <th>Nama</th>
    <th>Kursus</th>
    <th>Jumlah Bayar</th>
    <th>Metode</th>
    <th>Tanggal</th>
    <th>Status Pendaftaran</th>
    <th>Status Pembayaran</th>
    <th class="text-center">Aksi</th>
  </tr>
</thead>
<tbody>
  <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
      <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
      <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
      <td>Rp<?= number_format($row['jumlah_pembayaran'], 0, ',', '.') ?></td>
      <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
      <td><?= date('d M Y', strtotime($row['tanggal_pembayaran'])) ?></td>
      <td>
  <span class="badge bg-<?= 
    $row['status_pendaftaran'] == 'dibatalkan' ? 'danger' :
    ($row['status_pendaftaran'] == 'selesai' ? 'success' : 'success') ?>">
    <?= ucfirst($row['status_pendaftaran']) ?>
  </span>
</td>
      <td>
        <?php if (!empty($row['status_pembayaran'])): ?>
          <span class="badge bg-<?= 
            $row['status_pembayaran'] == 'pending' ? 'warning' : 
            ($row['status_pembayaran'] == 'sukses' ? 'success' : 
            ($row['status_pembayaran'] == 'gagal' ? 'danger' : 'secondary')) ?>">
            <?= ucfirst($row['status_pembayaran']) ?>
          </span>
        <?php else: ?>
          <span class="text-muted">Belum bayar</span>
        <?php endif; ?>
      </td>
      <td class="text-center">
        <div class="d-flex align-items-center justify-content-center gap-1">
          <button class="btn btn-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $row['payment_id'] ?>">
            <i class="fas fa-eye"></i>
          </button>
        <div class="dropdown">
  <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
    <i class="fas fa-cog"></i> Aksi
  </button>
  <ul class="dropdown-menu">
    <li>
      <a class="dropdown-item text-warning" href="../../../php/pembayaran/status_pembayaran.php?payment_id=<?= $row['payment_id'] ?>&status=pending">
        <i class="fas fa-clock me-2"></i> Pending
      </a>
    </li>
    <li>
      <a class="dropdown-item text-success" href="../../../php/pembayaran/status_pembayaran.php?payment_id=<?= $row['payment_id'] ?>&status=sukses">
        <i class="fas fa-check-circle me-2"></i> Sukses
      </a>
    </li>
    <li>
      <a class="dropdown-item text-danger" href="../../../php/pembayaran/status_pembayaran.php?payment_id=<?= $row['payment_id'] ?>&status=gagal">
        <i class="fas fa-times-circle me-2"></i> Gagal
      </a>
    </li>
    <!-- <li>
      <a class="dropdown-item text-secondary" href="../../../php/pembayaran/status_pembayaran.php?payment_id=<?= $row['payment_id'] ?>&status=dikembalikan">
        <i class="fas fa-undo me-2"></i> Dikembalikan
      </a>
    </li> -->
  </ul>
</div>

        </div>
      </td>
    </tr>

    <!-- Modal Detail -->
    <div class="modal fade" id="modalDetail<?= $row['payment_id'] ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Detail Pembayaran</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4">
            <div class="row">
              <div class="col-md-6 mb-3">
                <strong>Nama Siswa:</strong><br>
                <?= $row['nama_lengkap'] ?>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Nama Kursus:</strong><br>
                <?= $row['nama_kursus'] ?>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Jumlah Pembayaran:</strong><br>
                Rp<?= number_format($row['jumlah_pembayaran'], 0, ',', '.') ?>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Tanggal Pembayaran:</strong><br>
                <?= date('d M Y H:i', strtotime($row['tanggal_pembayaran'])) ?>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Metode Pembayaran:</strong><br>
                <?= $row['metode_pembayaran'] ?>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Kode Referensi Bank:</strong><br>
                <?= $row['kode_referensi_bank'] ?: '-' ?>
              </div>
              <div class="col-md-6 mb-3">
                <strong>Status Pembayaran:</strong><br>
                <span class="badge bg-<?= 
                  $row['status_pembayaran'] == 'pending' ? 'warning' : 
                  ($row['status_pembayaran'] == 'sukses' ? 'success' : 'danger') ?>">
                  <?= ucfirst($row['status_pembayaran']) ?>
                </span>
              </div>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  <?php endwhile; ?>

  <?php if (mysqli_num_rows($result) == 0): ?>
    <tr>
      <td colspan="7" class="text-center text-muted py-4">
        <i class="fas fa-folder-open fa-2x mb-2"></i><br>
        Tidak ada data pembayaran ditemukan.
      </td>
    </tr>
  <?php endif; ?>
</tbody>

          </table>
        </div>
      </div>

      <!-- ℹ️ Footer -->
      <div class="card-footer text-muted d-flex justify-content-between align-items-center px-3">
        <small><i class="fas fa-info-circle me-1"></i>Data ditampilkan real-time berdasarkan status pendaftaran dan pembayaran.</small>
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