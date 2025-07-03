<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// Simpan URL sekarang, asalkan bukan login dan logout
if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
include '../database/koneksi.php';

// Ambil ID dari session
$student_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;

 $course_id = $_GET['course_id'] ?? null;

if (!$student_id) {
    die("Silakan login terlebih dahulu.");
}

// ===========================
// 1. Ambil Jadwal Kursus Selesai
// ===========================
$dataJadwal = [];

$jadwalQuery = "
    SELECT 
      c.nama_kursus,
      c.level,
      s.tanggal_mulai,
      s.tanggal_selesai,
      s.hari_pelaksanaan,
      s.waktu_mulai,
      s.waktu_selesai,
      s.lokasi_kelas
    FROM enrollments e
    JOIN schedules s ON e.schedule_id = s.schedule_id
    JOIN courses c ON s.course_id = c.course_id
    WHERE e.student_id = $student_id 
      AND e.status_pendaftaran = 'selesai'
";

$jadwalResult = mysqli_query($conn, $jadwalQuery);
if ($jadwalResult && mysqli_num_rows($jadwalResult) > 0) {
    while ($row = mysqli_fetch_assoc($jadwalResult)) {
        $dataJadwal[] = $row;
    }
}

// ===========================
// 2. Ambil Sertifikat Berdasarkan course_id
// ===========================
$certificates = [];

if ($course_id) {
    $sertifikatQuery = "
        SELECT 
            cer.certificate_id,
            cer.nomor_sertifikat,
            cer.tanggal_terbit,
            cer.nilai_akhir,
            cer.file_sertifikat_url,
            crs.nama_kursus,
            u.nama_lengkap
        FROM certificates cer
        JOIN enrollments e ON cer.enrollment_id = e.enrollment_id
        JOIN users u ON e.student_id = u.user_id
        JOIN schedules sch ON e.schedule_id = sch.schedule_id
        JOIN courses crs ON sch.course_id = crs.course_id
        WHERE e.student_id = $student_id
          AND crs.course_id = $course_id
    ";

    $sertifikatResult = mysqli_query($conn, $sertifikatQuery);
    if ($sertifikatResult && mysqli_num_rows($sertifikatResult) > 0) {
        while ($row = mysqli_fetch_assoc($sertifikatResult)) {
            $certificates[] = $row;
        }
    }
}

// ===========================
// 3. Ambil Modul Kursus
// ===========================
// Ambil course_id dari GET atau POST
$course_id = $_GET['course_id'] ?? $_POST['course_id'] ?? null;

if (!$course_id) {
    die("Course ID tidak ditemukan di URL atau Form.");
}

// Query Modul Berdasarkan Course ID
$modulQuery = "SELECT * FROM modules WHERE course_id = $course_id ORDER BY urutan ASC";
$modulResult = mysqli_query($conn, $modulQuery);

if (!$modulResult) {
    die("Query Modul Error: " . mysqli_error($conn));
}
// ===========================
// 4. Ambil Tugas Berdasarkan course_id
// ===========================
$tugasList = [];

if ($course_id) {
    $tugasQuery = "
        SELECT 
            a.assignment_id,
            a.judul_tugas,
            a.deskripsi_tugas,
            a.tanggal_batas_akhir,
            a.poin_maksimal,
            l.nama_pelajaran
        FROM assignments a
        JOIN lessons l ON a.lesson_id = l.lesson_id
        JOIN modules m ON l.module_id = m.module_id
        JOIN courses c ON m.course_id = c.course_id
        WHERE c.course_id = $course_id
        ORDER BY a.tanggal_batas_akhir ASC
    ";

    $tugasResult = mysqli_query($conn, $tugasQuery);
    if ($tugasResult && mysqli_num_rows($tugasResult) > 0) {
        while ($row = mysqli_fetch_assoc($tugasResult)) {
            $tugasList[] = $row;
        }
    }
}
// ===========================
// 5. Ambil Forum Diskusi Berdasarkan course_id
// ===========================
$forumList = [];

if ($course_id) {
    $forumQuery = "
        SELECT f.*, u.nama_lengkap AS pembuat
        FROM forums f
        JOIN users u ON f.user_id_pembuat = u.user_id
        WHERE f.course_id = $course_id
        ORDER BY f.tanggal_buat DESC
    ";

    $forumResult = mysqli_query($conn, $forumQuery);
    if ($forumResult && mysqli_num_rows($forumResult) > 0) {
        while ($row = mysqli_fetch_assoc($forumResult)) {
            $forumList[] = $row;
        }
    }
}
$forumPosts = [];

if (!empty($forumList)) {
    foreach ($forumList as $forum) {
        $forum_id = $forum['forum_id'];

        // Ambil postingan utama (parent_post_id NULL)
        $postsQuery = "
            SELECT p.*, u.nama_lengkap AS nama_user
            FROM forumposts p
            JOIN users u ON p.user_id = u.user_id
            WHERE p.forum_id = $forum_id AND p.parent_post_id IS NULL
            ORDER BY p.tanggal_post ASC
        ";
        $postsResult = mysqli_query($conn, $postsQuery);

        while ($post = mysqli_fetch_assoc($postsResult)) {
            // Ambil balasan untuk setiap post
            $post_id = $post['post_id'];
            $replies = [];

            $repliesQuery = "
                SELECT p.*, u.nama_lengkap AS nama_user
                FROM forumposts p
                JOIN users u ON p.user_id = u.user_id
                WHERE p.parent_post_id = $post_id
                ORDER BY p.tanggal_post ASC
            ";
            $repliesResult = mysqli_query($conn, $repliesQuery);
            while ($reply = mysqli_fetch_assoc($repliesResult)) {
                $replies[] = $reply;
            }

            $post['replies'] = $replies;
            $forumPosts[$forum_id][] = $post;
        }
    }
}

$instructorIds = [];
$instructorQuery = "
    SELECT DISTINCT s.instructor_id 
    FROM schedules s
    WHERE s.course_id = $course_id
";
$resultInstruktur = mysqli_query($conn, $instructorQuery);
if ($resultInstruktur && mysqli_num_rows($resultInstruktur) > 0) {
    while ($row = mysqli_fetch_assoc($resultInstruktur)) {
        $instructorIds[] = $row['instructor_id'];
    }
}


?>

 

<?php
function getLevelColor($level) {
  $level = strtolower($level);
  switch ($level) {
    case 'pemula': return 'info';
    case 'menengah': return 'primary';
    case 'lanjutan': return 'warning';
    case 'expert': return 'danger';
    default: return 'secondary';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="img/png" href="vendor/images/icon.png" />

     <link
      href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css"
      rel="stylesheet"
    />
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link
      rel="stylesheet"
      href="https://unpkg.com/swiper/swiper-bundle.min.css"
    /> <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="vendor/style/main.css" />
    <title>BeCreative - Homepage</title>
<style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4cc9f0;
      --light-bg: #f8f9fa;
      --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
 

 
    .container {
      max-width: 1300px;
    }
    
    .header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 2rem 0;
      border-radius: 0 0 15px 15px;
      margin-bottom: 2rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .nav-tabs {
      border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
      color: #495057;
      font-weight: 500;
      border: none;
      padding: 12px 20px;
      transition: all 0.3s;
    }
    
    .nav-tabs .nav-link:hover {
      color: var(--primary-color);
      background-color: rgba(67, 97, 238, 0.1);
    }
    
    .nav-tabs .nav-link.active {
      color: var(--primary-color);
      background-color: transparent;
      border-bottom: 3px solid var(--primary-color);
      font-weight: 600;
    }
    
    .card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      margin-bottom: 1.5rem;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: var(--card-shadow);
    }
    
    .card-title {
      font-weight: 700;
      color: var(--secondary-color);
      margin-bottom: 1rem;
    }
    
    .comment-box {
      resize: none;
      border-radius: 8px;
      border: 1px solid #ced4da;
      transition: border-color 0.3s;
    }
    
    .comment-box:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 0.25rem rgba(76, 201, 240, 0.25);
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      padding: 8px 20px;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s;
    }
    
    .btn-primary:hover {
      background-color: var(--secondary-color);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .badge-tag {
      background-color: var(--accent-color);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
      margin-right: 5px;
      display: inline-block;
      margin-bottom: 5px;
    }
    
    .progress-container {
      background-color: #e9ecef;
      border-radius: 10px;
      height: 10px;
      margin: 1rem 0;
    }
    
    .progress-bar {
      background-color: var(--primary-color);
      border-radius: 10px;
    }
    
    .discussion-item {
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      transition: all 0.3s;
    }
    
    .discussion-item:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--accent-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-right: 10px;
    }
    
    .instructor-badge {
      background-color: var(--secondary-color);
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.7rem;
      margin-left: 5px;
    }
    
    .material-icons {
      margin-right: 8px;
      color: var(--primary-color);
    }
    
    .like-btn {
      color: #6c757d;
      border: none;
      background: none;
      transition: all 0.2s;
    }
    
    .like-btn:hover, .like-btn.active {
      color: #dc3545;
    }
    
    .reply-btn {
      color: var(--primary-color);
      border: none;
      background: none;
      font-size: 0.9rem;
    }
    
    .resource-card {
      border-left: 4px solid var(--accent-color);
    }
    
    .floating-action-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-color: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      z-index: 1000;
      transition: all 0.3s;
    }
    
    .floating-action-btn:hover {
      transform: scale(1.1);
      background-color: var(--secondary-color);
    }
    
</style>

  </head>
  <body>
     <nav class="navbar navbar-expand-lg navbar-light fixed-top">
      <div class="container">
       <a class="navbar-brand fw-bold text-primary" href="#" style="font-size: 1.8rem; letter-spacing: 1px;">
    <i class="bi bi-lightbulb-fill me-2"></i> IT Learning
</a>

        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNavAltMarkup"
          aria-controls="navbarNavAltMarkup"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        <div
          class="collapse navbar-collapse align-items-center"
          id="navbarNavAltMarkup"
        >
     <div class="navbar-nav ms-auto me-3">
            <a class="nav-link " href="index.php">Home</a>
            <a class="nav-link " href="pendaftaran_kursus.php">Kursus</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
              <a class="nav-link active" href="jadwal.php">jadwal saya</a>
                <a class="nav-link" href="status_pendaftaran.php">Status Pendaftaran</a>
                <a class="nav-link" href="ptofil.php">Profil saya</a>
            <?php endif; ?>
            
        </div>
           
             <div class="navbar-auth d-lg-flex align-items-center mt-4 mt-md-0 text-center">
              <?php if (isset($_SESSION['user_id'])): ?>
                <form action="../php/logout.php" method="POST" class="d-inline">
                  <button type="submit" class="btn btn-danger ms-sm-4 mt-3 mt-sm-0 d-block d-sm-inline">Logout</button>
                </form>
                           <?php else: ?>
                <a class="nav-link me-0 me-lg-3 p-0" href="login.php">Masuk</a>
                <a href="registar.php" class="btn-second ms-sm-4 mt-3 mt-sm-0 d-block d-sm-inline">Daftar Sekarang</a>
              <?php endif; ?>
            </div>
        </div>
      </div>
    </nav>
 

 
 
 
 

<div class="container py-5" style="margin-top: 160px;">
 
  <ul class="nav nav-tabs mb-4 mt-4" id="pelajaranTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="materi-tab" data-bs-toggle="tab" data-bs-target="#materi" type="button" role="tab">
        <i class="fas fa-book me-1"></i> Materi
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tugas-tab" data-bs-toggle="tab" data-bs-target="#tugas" type="button" role="tab">
        <i class="fas fa-tasks me-1"></i> Tugas
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="forum-tab" data-bs-toggle="tab" data-bs-target="#forum" type="button" role="tab">
        <i class="fas fa-comments me-1"></i> Forum Diskusi
      </button>
    </li>
     <li class="nav-item" role="presentation">
    <button class="nav-link" id="resources-tab" data-bs-toggle="tab" data-bs-target="#sertifikat" type="button" role="tab">
      <i class="fas fa-link me-1"></i> Sertifikat
    </button>
  </li>
  </ul>

<div class="tab-content" id="pelajaranTabContent">

    <!-- MATERI -->
    <div class="tab-pane fade show active" id="materi" role="tabpanel">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="container mt-4 pt-2">
                    <h4 class="fw-bold mb-4">📘 Modul & Pelajaran</h4>

                    <?php if (mysqli_num_rows($modulResult) > 0): ?>
                        <?php while ($modul = mysqli_fetch_assoc($modulResult)): ?>
                            <div class="card mb-4 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <strong><?= htmlspecialchars($modul['nama_modul']) ?></strong>
                                </div>
                                <div class="card-body">
                                    <p class="mb-3"><?= nl2br(htmlspecialchars($modul['deskripsi_modul'])) ?></p>

                                    <?php
                                    $module_id = $modul['module_id'];
                                    $lessonsQuery = "SELECT * FROM lessons WHERE module_id = $module_id ORDER BY lesson_id ASC";
                                    $lessonsResult = mysqli_query($conn, $lessonsQuery);
                                    ?>

                                    <?php if (mysqli_num_rows($lessonsResult) > 0): ?>
                                        <?php while ($lesson = mysqli_fetch_assoc($lessonsResult)): ?>
                                            <div class="card mb-5 border-0 border-start border-4 border-primary">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <h5 class="card-title">
                                                                <i class="fas fa-play-circle text-primary me-2"></i>
                                                                <?= htmlspecialchars($lesson['nama_pelajaran']) ?>
                                                            </h5>
                                                            <p class="card-text"><?= htmlspecialchars($lesson['konten_pelajaran']) ?></p>
                                                        </div>
                                                        <span class="badge bg-primary text-light">
                                                            <?= ucfirst($lesson['tipe_konten']) ?> <?= $lesson['durasi_menit'] ?> menit
                                                        </span>
                                                    </div>

                                                    <div class="mt-3 d-flex flex-column gap-2">
                                                        <?php if ($lesson['tipe_konten'] === 'video'): ?>
                                                            <video controls class="rounded shadow-sm" style="width: 100%; max-width: 720px; height: auto;">
                                                                <source src="../uploads/pelajaran/<?= htmlspecialchars($lesson['konten_pelajaran']) ?>" type="video/mp4">
                                                                Browser Anda tidak mendukung video.
                                                            </video>
                                                        <?php elseif ($lesson['tipe_konten'] === 'pdf'): ?>
                                                            <iframe src="../uploads/pelajaran/<?= htmlspecialchars($lesson['konten_pelajaran']) ?>" 
                                                                    class="border rounded shadow-sm"
                                                                    style="width: 100%; height: 400px; max-width: 720px;">
                                                            </iframe>
                                                        <?php elseif ($lesson['tipe_konten'] === 'link'): ?>
                                                            <a href="<?= htmlspecialchars($lesson['konten_pelajaran']) ?>" target="_blank" class="btn btn-outline-info btn-sm w-auto">
                                                                <i class="fas fa-external-link-alt me-1"></i> Kunjungi Materi
                                                            </a>
                                                        <?php elseif ($lesson['tipe_konten'] === 'teks'): ?>
                                                            <div class="p-3 bg-light border rounded" style="max-width: 720px;">
                                                                <?= nl2br(htmlspecialchars($lesson['konten_pelajaran'])) ?>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if ($lesson['tipe_konten'] !== 'link' && $lesson['tipe_konten'] !== 'teks'): ?>
                                                            <div class="text-start mt-2">
                                                                <a href="../uploads/pelajaran/<?= htmlspecialchars($lesson['konten_pelajaran']) ?>" 
                                                                   class="btn btn-sm btn-outline-secondary px-2 py-1" style="font-size: 0.75rem;" download>
                                                                    <i class="fas fa-download me-1"></i> Unduh
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <p class="text-muted">Belum ada pelajaran untuk modul ini.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">Belum ada modul untuk kursus ini.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- TUGAS -->
    <div class="tab-pane fade" id="tugas" role="tabpanel">
        <?php if (count($tugasList) > 0): ?>
            <?php foreach ($tugasList as $tugas): ?>
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title">
                                    <i class="fas fa-tasks text-primary me-2"></i>
                                    <?= htmlspecialchars($tugas['judul_tugas']) ?>
                                </h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars($tugas['deskripsi_tugas'])) ?></p>
                                <p class="text-muted mb-0"><i class="fas fa-book-open me-1"></i> Pelajaran: <?= htmlspecialchars($tugas['nama_pelajaran']) ?></p>
                            </div>
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-clock me-1"></i> Deadline: <?= date("d M Y", strtotime($tugas['tanggal_batas_akhir'])) ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <h6><i class="fas fa-star text-primary me-2"></i>Poin Maksimal: <?= (int)$tugas['poin_maksimal'] ?></h6>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-primary"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalKumpulTugas"
                                        data-assignment-id="<?= $tugas['assignment_id'] ?>">
                                    <i class="fas fa-upload me-1"></i> Kumpulkan Tugas
                                </button>

                                <a href="detail_tugas.php?assignment_id=<?= $tugas['assignment_id'] ?>" class="btn btn-info text-white">
                                    <i class="fas fa-eye me-1"></i> Lihat Tugas Selengkapnya
                                </a>

                                <button class="btn btn-outline-secondary">
                                    <i class="fas fa-question-circle me-1"></i> Kerjakan Sebelum Waktu Habis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Belum ada tugas untuk kursus ini.
            </div>
        <?php endif; ?>
    </div>
    <!-- Modal Kumpul Tugas -->
    <div class="modal fade" id="modalKumpulTugas" tabindex="-1" aria-labelledby="modalKumpulTugasLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="../php/submissions/store.php" enctype="multipart/form-data" class="modal-content">
                  <input type="hidden" name="course_id" value="<?= $course_id ?>">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalKumpulTugasLabel"><i class="fas fa-upload me-2"></i>Pengumpulan Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="assignment_id" id="modal_assignment_id">
                    <input type="hidden" name="student_id" value="<?= $_SESSION['user_id'] ?>">

                    <div class="mb-3">
                        <label for="file_submission" class="form-label">File Tugas (PDF, DOCX, ZIP, dll)</label>
                        <input type="file" name="file_submission" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Kirim</button>
                </div>
            </form>
        </div>
    </div>


    <!-- FORUM -->
    <div class="tab-pane fade" id="forum" role="tabpanel">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-comments text-primary me-2"></i>Forum Diskusi</h5>
                <p class="text-muted">Ajukan pertanyaan atau diskusikan materi dengan peserta lain</p>

                <!-- Form Kirim Komentar -->
              <div class="mb-4">
                <form action="../php/forum/forum.php" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course_id; ?>">

                    <div class="mb-2">
                        <label for="judul_topik" class="form-label fw-bold">Judul Topik</label>
                        <input type="text" class="form-control" id="judul_topik" name="judul_topik" placeholder="Masukkan Judul Topik" required>
                    </div>

                    <div class="mb-2">
                        <label for="deskripsi_topik" class="form-label fw-bold">Deskripsi Topik / Pertanyaan</label>
                        <textarea class="form-control comment-box" id="deskripsi_topik" name="deskripsi_topik" rows="3" placeholder="Apa yang ingin kamu tanyakan?" required></textarea>
                    </div>

                    <div class="d-flex justify-content-between mt-2">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1"><i class="fas fa-paperclip"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="fas fa-code"></i></button>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i> Kirim</button>
                    </div>
                </form>
            </div>



            <?php foreach ($forumList as $forum): ?>
                      <div class="d-flex mb-3">
                          <!-- Forum Topik -->
                          <div class="flex-shrink-0">
                              <div class="user-avatar bg-primary">
                                  <?= strtoupper(substr($forum['pembuat'], 0, 1)); ?>
                              </div>
                          </div>
                          <div class="flex-grow-1 ms-3 discussion-item">
                              <div class="d-flex justify-content-between align-items-center">
                                  <h6 class="mb-0"><?= htmlspecialchars($forum['pembuat']); ?>
                                      <small class="text-muted">· <?= date('d M Y, H:i', strtotime($forum['tanggal_buat'])); ?></small>
                                  </h6>

                                  <?php if ($_SESSION['user_id'] == $forum['user_id_pembuat']): ?>
                                      <form action="../php/forum/delete.php" method="GET" onsubmit="return confirm('Yakin mau hapus topik ini?')">
                                          <input type="hidden" name="forum_id" value="<?= $forum['forum_id']; ?>">
                                          <input type="hidden" name="course_id" value="<?= $course_id; ?>">
                                          <button type="submit" class="btn btn-sm btn-danger">
                                              <i class="fas fa-trash-alt"></i> Hapus
                                          </button>
                                      </form>
                                  <?php endif; ?>
                              </div>

                              <div class="p-3 mb-3 rounded shadow-sm bg-white border-start border-4 border-primary">
                                  <h5 class="fw-bold text-primary mb-2">
                                      <i class="fas fa-comment-dots me-2"></i> <?= nl2br(htmlspecialchars($forum['judul_topik'])); ?>
                                  </h5>
                                  <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($forum['deskripsi_topik'])); ?></p>
                              </div>

                             <h6 class="fw-bold mb-2">Komentar:</h6>

                          <?php if (!empty($forumPosts[$forum['forum_id']])): ?>
                              <!-- Tombol Toggle -->
                              <button class="btn btn-sm btn-outline-secondary mb-2" type="button"
                                  data-bs-toggle="collapse"
                                  data-bs-target="#collapseForum<?= $forum['forum_id']; ?>"
                                  aria-expanded="false"
                                  aria-controls="collapseForum<?= $forum['forum_id']; ?>">
                                  Tampilkan komentar (<?= count($forumPosts[$forum['forum_id']]); ?>)
                              </button>

                              <!-- Isi Komentar -->
                              <div class="collapse" id="collapseForum<?= $forum['forum_id']; ?>">
                                  <?php foreach ($forumPosts[$forum['forum_id']] as $post): ?>
                                      <?php $isInstructor = in_array($post['user_id'], $instructorIds); ?>
                                      <div class="d-flex mb-3">
                                          <div class="flex-shrink-0">
                                              <div class="user-avatar bg-success">
                                                  <?= strtoupper(substr($post['nama_user'], 0, 1)); ?>
                                              </div>
                                          </div>
                                          <div class="flex-grow-1 ms-3">
                                              <div class="d-flex justify-content-between align-items-center">
                                                  <h6 class="mb-0">
                                                      <?= htmlspecialchars($post['nama_user']); ?>
                                                      <?php if ($isInstructor): ?>
                                                          <span class="badge bg-success ms-2">Pengajar</span>
                                                      <?php endif; ?>
                                                      <small class="text-muted ms-2">· <?= date('d M Y, H:i', strtotime($post['tanggal_post'])); ?></small>
                                                  </h6>
                                              </div>
                                              <div class="p-2 rounded bg-light border mt-1">
                                                  <p class="mb-1"><?= nl2br(htmlspecialchars($post['konten_post'])); ?></p>
                                              </div>
                                          </div>
                                      </div>
                                  <?php endforeach; ?>
                              </div>
                          <?php else: ?>
                              <p class="text-muted">Belum ada komentar. Jadilah yang pertama!</p>
                          <?php endif; ?>


                              <!-- Form Kirim Komentar -->
                              <h6 class="fw-bold mt-4">Tulis Komentar:</h6>
                              <form action="../php/forum/post_reply.php" method="POST" class="mt-2">
                                  <input type="hidden" name="forum_id" value="<?= $forum['forum_id']; ?>">
                                  <textarea name="konten_post" class="form-control form-control-sm mb-2" rows="3" placeholder="Tulis komentar..."></textarea>
                                  <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-paper-plane me-1"></i> Kirim</button>
                              </form>
                          </div>
                      </div>
                <?php endforeach; ?>



                <!-- Komentar lain -->
            </div>
        </div>
    </div>

    <!-- SERTIFIKAT -->
    <div class="tab-pane fade" id="sertifikat" role="tabpanel">
        <div class="card shadow-sm mb-3 resource-card">
            <div class="card-body">
                <div class="row g-4">
                    <?php if (!empty($certificates)): ?>
                        <?php foreach ($certificates as $row): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card card-certificate h-100 shadow-sm border-0 rounded-4">
                                    <span class="badge bg-success position-absolute m-3">Aktif</span>
                                    <img src="../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" 
                                         class="card-img-top certificate-img" 
                                         alt="Sertifikat <?= htmlspecialchars($row['nomor_sertifikat']) ?>" 
                                         style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">Sertifikat <?= htmlspecialchars($row['nama_kursus']) ?></h5>
                                        <p class="card-text text-muted">
                                            <small><i class="fas fa-user me-2"></i><?= htmlspecialchars($row['nama_lengkap']) ?></small><br>
                                            <small><i class="fas fa-id-card me-2"></i>No: <?= $row['nomor_sertifikat'] ?></small><br>
                                            <small><i class="fas fa-calendar me-2"></i><?= date('d M Y', strtotime($row['tanggal_terbit'])) ?></small><br>
                                            <small><i class="fas fa-star me-2"></i>Nilai: <?= $row['nilai_akhir'] ?></small>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-white border-0 d-flex justify-content-between">
                                        <a href="../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" 
                                           class="btn btn-sm btn-outline-primary" download>
                                            <i class="fas fa-download me-1"></i> Unduh
                                        </a>
                                        <a href="../uploads/sertifikat/<?= htmlspecialchars($row['file_sertifikat_url']) ?>" 
                                           class="btn btn-sm btn-outline-secondary" target="_blank">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <div class="alert alert-warning">Belum ada sertifikat yang tersedia.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div> <!-- Tutup tab-content -->


</div>

    
         <footer class="footer section-margin">
            <div class="container">
              <div
                class="row row-content justify-content-between justify-content-md-start"
              >
                <div class="col-lg-2 col-md-6">
                  <img src="vendor/images/logo.png" alt="" />
                  <a href="#" class="email mt-4 d-inline-block text-white"
                    >help@becreative.com</a
                  >
                  <p class="phone text-white">(0321) 887372</p>
                  <div class="icons mt-4">
                    <a href="#"><i class="bx bxl-whatsapp"></i></a>
                    <a href="#"><i class="bx bxl-instagram-alt mx-2"></i></a>
                    <a href="#"><i class="bx bxl-facebook-circle"></i></a>
                  </div>
                </div>
                <div class="col-lg-2 offset-lg-2 col-md-3 mt-4 mt-sm-0">
                  <h3>Payment</h3>
                  <ul>
                    <li><img src="vendor/images/bca.png" alt="" /></li>
                    <li><img src="vendor/images/bri.png" alt="" /></li>
                    <li><img src="vendor/images/bni.png" alt="" /></li>
                    <li><img src="vendor/images/mandiri.png" alt="" /></li>
                  </ul>
                </div>
                <div class="col-lg-2 col-md-3 mt-4 mt-sm-0">
                  <h3>Information</h3>
                  <ul>
                    <li>Office Hours</li>
                    <li>Requirements</li>
                    <li>About us</li>
                  </ul>
                </div>
                <div class="col-lg-2 col-md-3 mt-4 mt-sm-0">
                  <h3>Helpfull Link</h3>
                  <ul>
                    <li>Service</li>
                    <li>Support</li>
                    <li>Terms & Condition</li>
                    <li>Privacy Policy</li>
                  </ul>
                </div>
                <div class="col-lg-2 col-md-3 mt-4 mt-sm-0">
                  <h3>Address</h3>
                  <p class="text-white">
                    Jl Gatot Subroto No. 123 Blok. A23 Malang, Jawa Timur
                  </p>
                  <a href="#" class="maps text-white">Google Map</a>
                </div>
              </div>
              <div class="row text-center">
                <div class="col-12">
                  <p class="text-white">
                    &copy;Copyright 2022 all right reserved | Built by Mardha Mardiya
                  </p>
                </div>
              </div>
            </div>
          </footer>
      <script>
          document.addEventListener("DOMContentLoaded", function() {
              const urlParams = new URLSearchParams(window.location.search);
              const targetTab = urlParams.get('tab');

              if (targetTab) {
                  const targetButton = document.querySelector(`[data-bs-target="#${targetTab}"]`);
                  if (targetButton) {
                      const tab = new bootstrap.Tab(targetButton);
                      tab.show();
                  }
              }
          });
      </script>
      <script>
          document.addEventListener('DOMContentLoaded', function () {
            var modalTugas = document.getElementById('modalKumpulTugas');
            modalTugas.addEventListener('show.bs.modal', function (event) {
              var button = event.relatedTarget;
              var assignmentId = button.getAttribute('data-assignment-id');
              modalTugas.querySelector('#modal_assignment_id').value = assignmentId;
            });
          });
      </script>
      <script>
        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Like button functionality
        document.querySelectorAll('.like-btn').forEach(button => {
          button.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
              icon.classList.remove('far');
              icon.classList.add('fas');
              // Increment like count
              const count = parseInt(this.textContent.trim());
              this.innerHTML = `<i class="fas fa-heart"></i> ${count + 1}`;
            } else {
              icon.classList.remove('fas');
              icon.classList.add('far');
              // Decrement like count
              const count = parseInt(this.textContent.trim());
              this.innerHTML = `<i class="far fa-heart"></i> ${count - 1}`;
            }
          });
        });
      </script>
     <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
      integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
     <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
     <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>
     <script src="vendor/js/main.js"></script>
  </body>
</html>