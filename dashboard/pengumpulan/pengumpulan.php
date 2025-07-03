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
// Hanya pengajar atau admin yang boleh akses

include '../../../database/koneksi.php';

$user_id = (int)$_SESSION['user_id'];
$role    = $_SESSION['role'];

// Default: belum ada filter course
$courseIds = [];

// Jika bukan admin, ambil daftar kursus yang dia ajar
if ($role !== 'admin') {
    $resSched = mysqli_query($conn, "SELECT DISTINCT course_id FROM schedules WHERE instructor_id = $user_id");
    $courseIds = array_column(mysqli_fetch_all($resSched, MYSQLI_ASSOC), 'course_id');
}

// Bangun query assignment
if ($role === 'admin' || !empty($courseIds)) {
    $query = "
      SELECT a.assignment_id, a.judul_tugas, a.tanggal_batas_akhir, a.poin_maksimal,
             l.nama_pelajaran
      FROM assignments a
      JOIN lessons l ON a.lesson_id = l.lesson_id
      JOIN modules m ON l.module_id = m.module_id
    ";
    
    if ($role !== 'admin') {
        $in = implode(',', $courseIds);
        $query .= " WHERE m.course_id IN ($in)";
    }
    
    $query .= " ORDER BY a.tanggal_batas_akhir ASC";
    $result = mysqli_query($conn, $query);
    $totalTasks = mysqli_num_rows($result);
} else {
    // Pengajar tidak mengajar kursus apapun
    $result = [];
    $totalTasks = 0;
}
?>


 


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTech - Sistem Pendaftaran Kursus Komputer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../css/dasboard.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .tes {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1200px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            color: white;
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 3rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: white;
            border-radius: 2px;
        }

        .task-card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .task-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--primary-gradient);
        }

        .task-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .task-card:nth-child(3n+1)::before {
            background: var(--primary-gradient);
        }

        .task-card:nth-child(3n+2)::before {
            background: var(--secondary-gradient);
        }

        .task-card:nth-child(3n+3)::before {
            background: var(--success-gradient);
        }

        .card-body {
            padding: 2rem;
            position: relative;
        }

        .card-title {
            color: #2d3748;
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            line-height: 1.4;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 0;
            border-left: 3px solid transparent;
            padding-left: 1rem;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            background: rgba(102, 126, 234, 0.1);
            border-left-color: #667eea;
            border-radius: 0 8px 8px 0;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
            color: white;
            flex-shrink: 0;
        }

        .subject-icon {
            background: var(--primary-gradient);
        }

        .deadline-icon {
            background: var(--secondary-gradient);
        }

        .points-icon {
            background: var(--warning-gradient);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .info-value {
            color: #2d3748;
            font-size: 1rem;
            font-weight: 500;
        }

        .deadline-urgent {
            color: #e53e3e !important;
            font-weight: 700;
        }

        .deadline-warning {
            color: #d69e2e !important;
            font-weight: 600;
        }

        .deadline-normal {
            color: #38a169 !important;
        }

        .action-btn {
            background: var(--primary-gradient);
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            color: white;
            text-decoration: none;
        }

        .task-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .empty-state {
            text-align: center;
            color: white;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .filter-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filter-tab {
            padding: 0.8rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .filter-tab.active,
        .filter-tab:hover {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                padding: 1rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .task-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }

        .loading-animation {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                <div class="dropdown-item" onclick="window.location.href='../tugas/tugas.php'">Tugas & Penilaian</div>
                <div class="dropdown-item" onclick="window.location.href='submission.php'">Submissions</div>
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
                <h1>Daftar Pengumpulan tugas</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content tes">
            
                <div class="main-container">
                <h1 class="page-title">
                    <i class="fas fa-tasks me-3"></i>Daftar Tugas
                </h1>

                <div class="task-stats">
                    <div class="stat-item">
                    <span class="stat-number" id="totalTasks"><?= $totalTasks ?></span>
                    <span class="stat-label">Total Tugas</span>
                    </div>
                </div>

                <?php if ($totalTasks > 0): ?>
                    <div class="row g-4">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                        // Cek apakah sudah lewat deadline
                        $isUrgent = (strtotime($row['tanggal_batas_akhir']) < time());
                        ?>
                        <div class="col-md-6 col-lg-4 task-item fade-in">
                        <div class="card task-card h-100">
                            <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['judul_tugas']) ?></h5>

                            <div class="info-item">
                                <div class="info-icon subject-icon"><i class="fas fa-book"></i></div>
                                <div class="info-content">
                                <div class="info-label">Pelajaran</div>
                                <div class="info-value"><?= htmlspecialchars($row['nama_pelajaran']) ?></div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon deadline-icon"><i class="fas fa-clock"></i></div>
                                <div class="info-content">
                                <div class="info-label">Deadline</div>
                                <div class="info-value <?= $isUrgent ? 'deadline-urgent' : '' ?>">
                                    <?= date('d M Y H:i', strtotime($row['tanggal_batas_akhir'])) ?>
                                </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon points-icon"><i class="fas fa-star"></i></div>
                                <div class="info-content">
                                <div class="info-label">Poin Maksimal</div>
                                <div class="info-value"><?= (int)$row['poin_maksimal'] ?></div>
                                </div>
                            </div>

                            <div class="mt-auto">
                                <a href="detail_pengumpulan.php?assignment_id=<?= $row['assignment_id'] ?>" class="action-btn btn btn-primary w-100">
                                <i class="fas fa-eye me-2"></i>Lihat Pengumpulan
                                </a>
                            </div>
                            </div>
                        </div>
                        </div>
                    <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state" id="emptyState">
                    <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                    <h3>Tidak ada tugas ditemukan</h3>
                    <p>Belum ada tugas yang tersedia saat ini.</p>
                    </div>
                <?php endif; ?>
                </div>

    
        </div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

        <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>
 
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

   <script src="../../../js/dashboard.js"></script>
</body>
</html>