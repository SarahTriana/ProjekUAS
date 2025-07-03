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

$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'];

// =====================
// Ambil Sertifikat
// =====================
$query = "
SELECT c.*, u.nama_lengkap, co.nama_kursus 
FROM certificates c
JOIN enrollments e ON c.enrollment_id = e.enrollment_id
JOIN users u ON e.student_id = u.user_id
JOIN schedules s ON e.schedule_id = s.schedule_id
JOIN courses co ON s.course_id = co.course_id
";

if ($role === 'siswa') {
    $query .= " WHERE e.student_id = $user_id";
} elseif ($role === 'pengajar') {
    $query .= " WHERE s.instructor_id = $user_id";
}

$result = mysqli_query($conn, $query);
$certificates = [];
while ($row = mysqli_fetch_assoc($result)) {
    $certificates[] = $row;
}

// =====================
// Ambil Data Pendaftaran untuk Dropdown
// =====================
$enrollments = [];

$enrollQuery = "
SELECT e.enrollment_id, u.nama_lengkap, co.nama_kursus 
FROM enrollments e
JOIN users u ON e.student_id = u.user_id
JOIN schedules s ON e.schedule_id = s.schedule_id
JOIN courses co ON s.course_id = co.course_id
WHERE e.status_pendaftaran = 'selesai'
";

if ($role === 'pengajar') {
    $enrollQuery .= " AND s.instructor_id = $user_id";
} elseif ($role === 'siswa') {
    $enrollQuery .= " AND e.student_id = $user_id";
}

$enrollResult = mysqli_query($conn, $enrollQuery);
while ($row = mysqli_fetch_assoc($enrollResult)) {
    $enrollments[] = $row;
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
        .card-certificate {
            transition: transform 0.3s;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .card-certificate:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .certificate-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #28a745;
        }
        .certificate-img {
            height: 120px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
        .search-container {
            margin-bottom: 30px;
        }
        .add-certificate-btn {
            margin-bottom: 20px;
        }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-laptop-code"></i>
            <h2>EduTech</h2>
        </div>

       <div class="sidebar-menu">
            <!-- Menu Utama -->
            <div class="menu-category">Menu Utama</div>
            <div class="menu-item " onclick="window.location.href='../dashboard.php'">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </div>

            <!-- FITUR ADMINISTRATOR -->
            <?php if ($_SESSION['role'] === 'admin') : ?>
                <div class="menu-category">Administrator</div>

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
 
             <div class="menu-item" onclick="window.location.href='../jadwal/jadwal.php'">
                <i class="fas fa-calendar-alt"></i>
                <span>Jadwal Kelas</span>
            </div>

             <?php if ($_SESSION['role'] === 'admin') : ?>
                <div class="menu-item " onclick="window.location.href='../pendaftaran_kursus/pendaftaran.php'">
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
                <div class="dropdown-item" onclick="window.location.href='../tugastugas.php'">Tugas & Penilaian</div>
                <div class="dropdown-item" onclick="window.location.href='../pengumpulan/pengumpulan.php'">Submissions</div>
            </div>

            <!-- FITUR REPORT -->
            <div class="menu-category">Laporan</div>

            <?php if ($_SESSION['role'] === 'admin') : ?>
                
                <div class="menu-item" onclick="window.location.href='../pembayaran/laporan_pembayaran.php'">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Laporan Pembayaran</span>
                </div>
                
            <?php endif; ?>

            <div class="menu-item active" onclick="window.location.href='sertifikat.php'">
                <i class="fas fa-certificate"></i>
                <span>Laporan Sertifikat</span>
            </div>

            <div class="menu-item" onclick="window.location.href='../../../php/logout.php'">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log Out</span>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">AD</div>
                <div class="user-info">
                    <h4>Admin EduTech</h4>
                    <p>Super Administrator</p>
                </div>
                <div class="user-action">
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <div class="header">
            <div class="header-title">
                <h1>Laporan Sertifikat</h1>
                <p>Selamat datang kembali, Admin</p>
            </div>
        
        </div>

            <div class="content">
            
 
                <div class="container-fluid py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">Manajemen Sertifikat</h2>
                    
                    </div>

                    <!-- Form Pencarian dan Filter -->
                    <div class="card search-container">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Cari sertifikat..." id="searchInput">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- jarak -->
                                </div>
                                <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'pengajar') : ?>
                                    <div class="col-md-3">
                                        <button class="btn btn-primary add-certificate-btn" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                                            <i class="fas fa-plus me-2"></i>Tambah Sertifikat
                                        </button>
                                    </div>
                                <?php endif; ?>

                                
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Sertifikat dalam Card -->
                    <div class="row" id="certificatesContainer">
                        <!-- Card Sertifikat 1 -->
                        <?php foreach ($certificates as $row): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card card-certificate h-100">
                                <span class="badge certificate-badge">Aktif</span>
                              <img src="../../../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" 
                                class="card-img-top certificate-img" 
                                alt="Sertifikat <?= htmlspecialchars($row['nomor_sertifikat']) ?>">

                                <div class="card-body">
                                    <h5 class="card-title">Sertifikat <?= htmlspecialchars($row['nama_kursus']) ?></h5>
                                    <p class="card-text text-muted">
                                            <small><i class="fas fa-user me-2"></i><?= htmlspecialchars($row['nama_lengkap']) ?></small><br>

                                        <small><i class="fas fa-id-card me-2"></i>No: <?= $row['nomor_sertifikat'] ?></small><br>
                                        <small><i class="fas fa-calendar me-2"></i><?= date('d M Y', strtotime($row['tanggal_terbit'])) ?></small><br>
                                        <small><i class="fas fa-star me-2"></i>Nilai: <?= $row['nilai_akhir'] ?></small>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between">
                                       <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewCertificateModal<?= $row['certificate_id'] ?>">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                        <?php if ($role === 'admin' || $role === 'pengajar'): ?>
                                        <div>
                                        <button class="btn btn-sm btn-outline-warning me-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCertificateModal"
                                                data-id="<?= $row['certificate_id'] ?>"
                                                data-enrollment="<?= $row['enrollment_id'] ?>"
                                                data-nomor="<?= $row['nomor_sertifikat'] ?>"
                                                data-tanggal="<?= $row['tanggal_terbit'] ?>"
                                                data-nilai="<?= $row['nilai_akhir'] ?>"
                                                data-file="<?= $row['file_sertifikat_url'] ?>"
                                                data-nama="<?= $row['nama_lengkap'] ?>"
                                                data-kursus="<?= $row['nama_kursus'] ?>">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>

                                            <!-- Tombol trigger -->
                                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCertificateModal<?= $row['certificate_id'] ?>">
                                                <i class="fas fa-trash me-1"></i> Hapus
                                            </button>

                                            <!-- Modal -->
                                             

                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="modal fade" id="viewCertificateModal<?= $row['certificate_id'] ?>" tabindex="-1" aria-labelledby="viewCertificateModalLabel<?= $row['certificate_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewCertificateModalLabel<?= $row['certificate_id'] ?>">Detail Sertifikat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="../../../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" 
                                                class="img-fluid rounded mb-4" 
                                                alt="Sertifikat <?= htmlspecialchars($row['nomor_sertifikat']) ?>">

                                            <div class="row text-start">
                                                <div class="col-md-6">
                                                    <p><strong>Nomor Sertifikat:</strong> <?= htmlspecialchars($row['nomor_sertifikat']) ?></p>
                                                    <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($row['nama_lengkap']) ?></p>
                                                    <p><strong>Kursus:</strong> <?= htmlspecialchars($row['nama_kursus']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Tanggal Terbit:</strong> <?= date('d F Y', strtotime($row['tanggal_terbit'])) ?></p>
                                                    <p><strong>Nilai Akhir:</strong> <?= number_format($row['nilai_akhir'], 2) ?></p>
                                                    <p><strong>Status:</strong> <span class="badge bg-success">Aktif</span></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="../../../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" 
                                                class="btn btn-primary" 
                                                download="<?= htmlspecialchars($row['file_sertifikat_url']) ?>">
                                                <i class="fas fa-download me-2"></i>Unduh Sertifikat
                                                </a>

                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                   
            
                    </div>

                    <!-- Empty State (akan muncul jika tidak ada sertifikat) -->
                    <div class="empty-state d-none" id="emptyState">
                        <i class="fas fa-certificate fa-4x text-muted mb-3"></i>
                        <h4>Belum Ada Sertifikat</h4>
                        <p class="text-muted">Anda belum memiliki sertifikat. Klik tombol "Tambah Sertifikat" untuk membuat yang baru.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                            <i class="fas fa-plus me-2"></i>Tambah Sertifikat
                        </button>
                    </div>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
 
                <div class="modal fade" id="addCertificateModal" tabindex="-1" aria-labelledby="addCertificateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                        <form action="../../../php/sertifikat/proses_tambah.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                            <h5 class="modal-title" id="addCertificateModalLabel">Tambah Sertifikat Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            
                            <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label>ID Pendaftaran</label>
                                <select class="form-select" name="enrollment_id" required>
                                    <option value="" disabled selected>Pilih ID Pendaftaran</option>
                                    <?php foreach ($enrollments as $enroll): ?>
                                        <option value="<?= $enroll['enrollment_id'] ?>">
                                            ENR-<?= str_pad($enroll['enrollment_id'], 3, '0', STR_PAD_LEFT) ?> - <?= $enroll['nama_lengkap'] ?> (<?= $enroll['nama_kursus'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                </div>


                                <div class="col-md-6">
                                <label for="certificateNumber" class="form-label">Nomor Sertifikat</label>
                                <input type="text" class="form-control" name="nomor_sertifikat" placeholder="CERT-YYYY-NNN" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label for="issueDate" class="form-label">Tanggal Terbit</label>
                                <input type="date" class="form-control" name="tanggal_terbit" required>
                                </div>
                                <div class="col-md-6">
                                <label for="finalScore" class="form-label">Nilai Akhir</label>
                                <input type="number" step="0.01" min="0" max="100" class="form-control" name="nilai_akhir" placeholder="00.00">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="certificateFile" class="form-label">Unggah File Sertifikat</label>
                                <input class="form-control" type="file" name="file_sertifikat" accept=".pdf,.jpg,.png" required>
                                <small class="text-muted">Format: PDF, JPG, atau PNG (Maks. 5MB)</small>
                            </div>
                            </div>
                            
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Sertifikat</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>


                 <div class="modal fade" id="editCertificateModal" tabindex="-1" aria-labelledby="editCertificateModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                        <form action="../../../php/sertifikat/edit.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                            <h5 class="modal-title">Edit Sertifikat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                            <input type="hidden" name="certificate_id" id="edit_certificate_id">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label class="form-label">ID Pendaftaran</label>
                                <input type="text" class="form-control" id="edit_enrollment_info" disabled>
                                <input type="hidden" name="enrollment_id" id="edit_enrollment_id">
                                </div>
                                <div class="col-md-6">
                                <label class="form-label">Nomor Sertifikat</label>
                                <input type="text" name="nomor_sertifikat" class="form-control" id="edit_nomor_sertifikat" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                <label class="form-label">Tanggal Terbit</label>
                                <input type="date" name="tanggal_terbit" class="form-control" id="edit_tanggal_terbit" required>
                                </div>
                                <div class="col-md-6">
                                <label class="form-label">Nilai Akhir</label>
                                <input type="number" step="0.01" min="0" max="100" name="nilai_akhir" class="form-control" id="edit_nilai_akhir">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Unggah File Sertifikat Baru (Opsional)</label>
                                <input type="file" name="file_sertifikat" class="form-control" accept=".pdf,.jpg,.png">
                                <div class="mt-2">
                            <a href="../../../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                Lihat File Saat Ini
                            </a>                            </div>
                            </div>
                            </div>
                            <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
                                            <div class="modal fade" id="deleteCertificateModal<?= $row['certificate_id'] ?>" tabindex="-1" aria-labelledby="deleteCertificateModalLabel<?= $row['certificate_id'] ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="deleteCertificateModalLabel<?= $row['certificate_id'] ?>">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menghapus sertifikat ini?</p>
                                                            <p><strong>Nomor Sertifikat:</strong> <?= htmlspecialchars($row['nomor_sertifikat']) ?></p>
                                                            <p><strong>Pemilik:</strong> <?= htmlspecialchars($row['nama_lengkap']) ?> - <?= htmlspecialchars($row['nama_kursus']) ?></p>
                                                            <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                                                        </div>
                                                        <!-- FORM DELETE -->
                                                        <form action="../../../php/sertifikat/hapus.php" method="POST">
                                                            <input type="hidden" name="certificate_id" value="<?= $row['certificate_id'] ?>">
                                                            <input type="hidden" name="file_sertifikat_url" value="<?= $row['file_sertifikat_url'] ?>">
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger">Hapus Sertifikat</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                         

                
</div>
                    <script>
                    const modal = document.getElementById('editCertificateModal');
                    modal.addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;

                        document.getElementById('edit_certificate_id').value = button.getAttribute('data-id');
                        document.getElementById('edit_enrollment_id').value = button.getAttribute('data-enrollment');
                        document.getElementById('edit_enrollment_info').value = `ENR-${String(button.getAttribute('data-enrollment')).padStart(3, '0')} - ${button.getAttribute('data-nama')} (${button.getAttribute('data-kursus')})`;
                        document.getElementById('edit_nomor_sertifikat').value = button.getAttribute('data-nomor');
                        document.getElementById('edit_tanggal_terbit').value = button.getAttribute('data-tanggal');
                        document.getElementById('edit_nilai_akhir').value = button.getAttribute('data-nilai');
                        
                        const fileUrl = '../../../uploads/pelajaran/' + button.getAttribute('data-file');
                        document.getElementById('current_file_link').href = fileUrl;
                    });
                    </script>

               




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

   <script src="../../../js/dashboard.js"></script>
</body>
</html>