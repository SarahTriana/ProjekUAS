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
 
 $query = "SELECT u.user_id, u.nama_lengkap, u.email, u.telepon, u.alamat, i.spesialisasi, i.pengalaman_mengajar_tahun, i.rating_rata_rata 
          FROM users u 
          JOIN instructors i ON u.user_id = i.instructor_id 
          WHERE u.role = 'pengajar'";
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
                <h1>Data Pengajar</h1>
                <p>Selamat datang kembali, Admin</p>
            </div>
        
        </div>

        <div class="content">
            
           <div class="container-fluid py-4">
    <!-- Add New Instructor Card -->
    <div class="card shadow-lg mb-5 border-0">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Tambah Pengajar Baru</h4>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="../../../php/pengajar/pengajar_store.php" class="row g-3 needs-validation" novalidate>
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
                    <label for="spesialisasi" class="form-label">Spesialisasi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-tools"></i></span>
                        <input type="text" class="form-control" id="spesialisasi" name="spesialisasi" required>
                        <div class="invalid-feedback">Harap isi bidang spesialisasi</div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="pengalaman_mengajar_tahun" class="form-label">Pengalaman Mengajar (Tahun)</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-hourglass-half"></i></span>
                        <input type="number" class="form-control" id="pengalaman_mengajar_tahun" name="pengalaman_mengajar_tahun" required>
                        <div class="invalid-feedback">Harap isi pengalaman</div>
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

    <!-- Instructor List Card -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0"><i class="fas fa-users me-2"></i>Daftar Pengajar</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="instructorTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Spesialisasi</th>
                            <th>Pengalaman</th>
                            <th>Rating</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['telepon']) ?></td>
                            <td><?= htmlspecialchars($row['spesialisasi']) ?></td>
                            <td><?= htmlspecialchars($row['pengalaman_mengajar_tahun']) ?> tahun</td>
                            <td><?= number_format($row['rating_rata_rata'] ?? 0, 1) ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="detail_pengajar.php?id=<?= $row['user_id'] ?>" class="btn btn-info text-white" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit_pengajar.php?id=<?= $row['user_id'] ?>" class="btn btn-warning text-white" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="../../../php/pengajar/delete.php?id=<?= $row['user_id'] ?>" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus pengajar ini?')"
                                       title="Hapus">
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
                    Menampilkan <span><?= mysqli_num_rows($result) ?></span> pengajar
                </div>
                <!-- Placeholder pagination -->
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
         
        </div>

        <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>

   <script src="../../../js/dashboard.js"></script>
</body>
</html>