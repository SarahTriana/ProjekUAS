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

// Izinkan hanya admin atau pengajar

include '../../../database/koneksi.php';

$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'];

// ============================
// Ambil Modul Sesuai Pengajar
// ============================
if ($role === 'pengajar') {
    $modul_query = mysqli_query($conn, "
        SELECT DISTINCT m.module_id, m.nama_modul, m.urutan, c.nama_kursus, c.course_id
        FROM modules m
        JOIN courses c ON m.course_id = c.course_id
        JOIN schedules s ON c.course_id = s.course_id
        WHERE s.instructor_id = $user_id
        ORDER BY c.course_id, m.urutan ASC
    ");
} else {
    // Admin melihat semua modul
    $modul_query = mysqli_query($conn, "
        SELECT m.module_id, m.nama_modul, m.urutan, c.nama_kursus, c.course_id
        FROM modules m
        JOIN courses c ON m.course_id = c.course_id
        ORDER BY c.course_id, m.urutan ASC
    ");
}


// ============================
// Ambil Pelajaran Sesuai Pengajar
// ============================
if ($role === 'pengajar') {
    $lesson_query = mysqli_query($conn, "
        SELECT DISTINCT 
            l.*, 
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
    // Admin melihat semua pelajaran
    $lesson_query = mysqli_query($conn, "
        SELECT 
            l.*, 
            m.nama_modul, 
            m.urutan, 
            c.nama_kursus, 
            c.course_id
        FROM lessons l
        JOIN modules m ON l.module_id = m.module_id
        JOIN courses c ON m.course_id = c.course_id
        ORDER BY c.course_id, m.urutan, l.lesson_id ASC
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
                <h1>Daftar Pelajaran</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content">
            
<div class="container-fluid py-4">
<!-- Form Tambah Pelajaran -->
<div class="card shadow-lg mb-5 border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-book me-2"></i>Tambah Pelajaran</h4>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="../../../php/pelajaran/store.php" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
            <div class="col-md-6 mb-3">
                <label for="module_id" class="form-label">Pilih Modul</label>
                <select class="form-select" name="module_id" id="module_id" required>
                    <option value="">-- Pilih Modul --</option>
                    <?php while ($m = mysqli_fetch_assoc($modul_query)) : ?>
                        <option value="<?= $m['module_id'] ?>">
                            <?= htmlspecialchars($m['nama_kursus']) ?> - <?= htmlspecialchars($m['nama_modul']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <div class="invalid-feedback">Harap pilih modul</div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="nama_pelajaran" class="form-label">Nama Pelajaran</label>
                <input type="text" class="form-control" name="nama_pelajaran" id="nama_pelajaran" required>
                <div class="invalid-feedback">Harap isi nama pelajaran</div>
            </div>

     <label for="tipe_konten">Tipe Konten</label>
    <select name="tipe_konten" id="tipe_konten" class="form-select" required onchange="toggleDurasiInput()">
        <option value="">-- Pilih Tipe --</option>
        <option value="teks">Teks</option>
        <option value="video">Video</option>
        <option value="pdf">PDF</option>
        <option value="link">Link</option>
    </select>

    <div class="mt-3">
        <label for="konten_file">Upload File</label>
        <input type="file" name="konten_file" id="konten_file" class="form-control" required>
    </div>

    <!-- Kolom Durasi (hanya muncul jika tipe = video) -->
    <div class="mt-3 d-none" id="durasi_group">
        <label for="durasi_menit">Durasi (menit)</label>
        <input type="number" name="durasi_menit" id="durasi_menit" class="form-control" placeholder="Contoh: 10">
    </div>

  


            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Pelajaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Daftar Pelajaran -->
<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white py-3">
        <h4 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Pelajaran</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kursus</th>
                        <th>Modul</th>
                        <th>Pelajaran</th>
                        <th>Konten</th>
                        <th>Tipe</th>
                        <th>Durasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($lesson_query) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($lesson_query)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_kursus']) ?></td>
                                <td><?= htmlspecialchars($row['nama_modul']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pelajaran']) ?></td>
                              <td>
                                    <?php
                                        $path = htmlspecialchars($row['konten_pelajaran']);
                                        $basename = basename($path); 
                                        $icon = '';

                                        switch ($row['tipe_konten']) {
                                            case 'video':
                                                $icon = '<i class="fas fa-video text-danger me-1"></i>';
                                                break;
                                            case 'pdf':
                                                $icon = '<i class="fas fa-file-pdf text-danger me-1"></i>';
                                                break;
                                            case 'teks':
                                                $icon = '<i class="fas fa-file-alt text-primary me-1"></i>';
                                                break;
                                            case 'link':
                                                $icon = '<i class="fas fa-link text-success me-1"></i>';
                                                break;
                                            default:
                                                $icon = '<i class="fas fa-file text-secondary me-1"></i>';
                                        }
                                    ?>

                                    <?php if (in_array($row['tipe_konten'], ['video', 'pdf', 'teks', 'link'])): ?>
                                        <a href="../../../uploads/pelajaran/<?= $basename ?>" target="_blank"    class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                            <?= $icon ?>Show
                                        </a>
                                    <?php else: ?>
                                        <?= $icon ?><?= $basename ?>
                                    <?php endif; ?>
                                </td>

                                <td class="text-capitalize"><?= htmlspecialchars($row['tipe_konten']) ?></td>
                                <td><?= ((int)$row['durasi_menit'] > 0) ? (int)$row['durasi_menit'] . ' menit' : '-' ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="detail_pelajaran.php?lesson_id=<?= $row['lesson_id'] ?>" class="btn btn-info text-white" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                     <a href="edit_pelajaran.php?lesson_id=<?= $row['lesson_id'] ?>" class="btn btn-warning text-white" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                        <a href="../../../php/pelajaran/delete.php?lesson_id=<?= $row['lesson_id'] ?>" class="btn btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus pelajaran ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada pelajaran ditambahkan.</td></tr>
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
<script>
function toggleDurasiInput() {
    const tipe = document.getElementById('tipe_konten').value;
    const durasiGroup = document.getElementById('durasi_group');
    const fileInput = document.getElementById('konten_file');

    // Set file type
    if (tipe === "teks") {
        fileInput.accept = ".txt";
        durasiGroup.classList.add('d-none');
    } else if (tipe === "video") {
        fileInput.accept = "video/*";
        durasiGroup.classList.remove('d-none');
    } else if (tipe === "pdf") {
        fileInput.accept = ".pdf";
        durasiGroup.classList.add('d-none');
    } else if (tipe === "link") {
        fileInput.accept = ".txt";
        durasiGroup.classList.add('d-none');
    } else {
        fileInput.removeAttribute("accept");
        durasiGroup.classList.add('d-none');
    }
}
</script>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

   <script src="../../../js/dashboard.js"></script>
</body>
</html>