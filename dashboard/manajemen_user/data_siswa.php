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

 $query = "SELECT u.user_id, u.nama_lengkap, u.email, u.telepon, s.pendidikan_terakhir, s.tanggal_lahir 
          FROM users u 
          JOIN students s ON u.user_id = s.student_id 
          WHERE u.role = 'siswa'";
$result = mysqli_query($conn, $query);
$role = $_SESSION['role'];
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
    <div class="container-fluid py-4">
        <!-- Add New Student Card -->
        <div class="card shadow-lg mb-5 border-0">
            <div class="card-header bg-primary text-white py-3">
                <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Tambah Siswa Baru</h4>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="../../../php/siswa/siswa_store.php" class="row g-3 needs-validation" novalidate>
                    <div class="col-md-6 mb-3">
                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            <div class="invalid-feedback">Harap isi nama lengkap</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Harap isi email yang valid</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="telepon" class="form-label">Telepon</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" id="telepon" name="telepon" required>
                            <div class="invalid-feedback">Harap isi nomor telepon</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control" id="alamat" name="alamat" required>
                            <div class="invalid-feedback">Harap isi alamat</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">Harap isi password</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                            <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
                            <div class="invalid-feedback">Harap isi pendidikan terakhir</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                            <div class="invalid-feedback">Harap pilih tanggal lahir</div>
                        </div>
                    </div>
                    
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-save me-2"></i>Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student List Card -->
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Siswa</h4>
                    <!-- <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control form-control-sm me-2" placeholder="Cari siswa...">
                        <button class="btn btn-light btn-sm" id="refreshBtn">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div> -->
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="studentTable">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Nama Lengkap</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Telepon</th>
                                <th class="text-nowrap">Pendidikan</th>
                                <th class="text-nowrap">Tanggal Lahir</th>
                                <th class="text-nowrap text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                <td class="align-middle text-truncate" style="max-width: 200px;"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($row['telepon']) ?></td>
                                <td class="align-middle"><?= htmlspecialchars($row['pendidikan_terakhir']) ?></td>
                                <td class="align-middle"><?= date('d M Y', strtotime($row['tanggal_lahir'])) ?></td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="detail_siswa.php?id=<?= $row['user_id'] ?>" 
                                           class="btn btn-info text-white" 
                                           data-bs-toggle="tooltip" 
                                           title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_siswa.php?id=<?= $row['user_id'] ?>" 
                                           class="btn btn-warning text-white" 
                                           data-bs-toggle="tooltip" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../../../php/siswa/delete.php?id=<?= $row['user_id'] ?>" 
                                           class="btn btn-danger" 
                                           data-bs-toggle="tooltip" 
                                           title="Hapus"
                                           onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan <span id="rowCount"><?= mysqli_num_rows($result) ?></span> siswa
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Selanjutnya</a>
                            </li>
                        </ul>
                    </nav>
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