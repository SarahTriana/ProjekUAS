<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['pengajar', 'admin'])) {
    header("Location: ../../../views/login.php");
    exit;
}

include '../../../database/koneksi.php';

$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Ambil list forum topik dari course ini
$queryForum = "
    SELECT f.*, u.nama_lengkap AS pembuat
    FROM forums f
    JOIN users u ON f.user_id_pembuat = u.user_id
    WHERE f.course_id = $course_id
    ORDER BY f.tanggal_buat DESC
";
$resultForum = mysqli_query($conn, $queryForum);
$forumList = mysqli_fetch_all($resultForum, MYSQLI_ASSOC);

// Ambil semua posts (balasan) dari forum2 ini
$forumIds = array_column($forumList, 'forum_id');
$forumPosts = [];
$instructorIds = [];

// Ambil daftar pengajar (untuk badge "Pengajar")
$resInstructor = mysqli_query($conn, "SELECT user_id FROM users WHERE role = 'pengajar'");
$instructorIds = array_column(mysqli_fetch_all($resInstructor, MYSQLI_ASSOC), 'user_id');

if (!empty($forumIds)) {
    $in = implode(',', $forumIds);

    $queryPosts = "
        SELECT p.*, u.nama_lengkap
        FROM forumposts p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.forum_id IN ($in)
        ORDER BY p.tanggal_post ASC
    ";
    $resultPosts = mysqli_query($conn, $queryPosts);
    while ($post = mysqli_fetch_assoc($resultPosts)) {
        $forumPosts[$post['forum_id']][] = $post;
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
                <div class="dropdown-item" onclick="window.location.href='forum.php'">Forum Diskusi</div>
                <div class="dropdown-item" onclick="window.location.href='../tugas/tugas.php'">Tugas & Penilaian</div>
                <div class="dropdown-item" onclick="window.location.href='../pengumpulan/pengempulan.php'">Submissions</div>
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
                <h1>Forum Diskusi</h1>
                <p>Selamat datang kembali</p>
            </div>
        
        </div>

        <div class="content tes">
 
 <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-comments text-primary me-2"></i>Forum Diskusi</h5>
                <p class="text-muted">Ajukan pertanyaan atau diskusikan materi dengan peserta lain</p>

                <!-- Form Kirim Komentar -->
              <div class="mb-4">
                <form action="../../../php/forum_dashboard/forum.php" method="POST">
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
                                      <form action="../../../php/forum_dashboard/delete.php" method="GET" onsubmit="return confirm('Yakin mau hapus topik ini?')">
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
                              <form action="../../../php/forum_dashboard/post_reply.php" method="POST" class="mt-2">
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
        
        <div class="footer">
            <p>© 2023 EduTech - Sistem Pendaftaran Kursus Komputer. All rights reserved.</p>
        </div>
    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->

   <script src="../../../js/dashboard.js"></script>
</body>
</html>