<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);

// Simpan URL sekarang, asalkan bukan login dan logout
if (!in_array($currentPage, ['login.php', 'logout.php'])) {
    $_SESSION['last_page_before_login'] = $_SERVER['REQUEST_URI'];
}
include '../database/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    echo "<script>alert('Silakan login sebagai siswa untuk melihat status.'); window.location.href='../login.php';</script>";
    exit;
}

$student_id = $_SESSION['user_id'];

 

$query = "
SELECT 
    e.enrollment_id,
    e.tanggal_daftar,
    e.status_pendaftaran,
    s.hari_pelaksanaan,
    s.waktu_mulai,
    s.waktu_selesai,
    c.nama_kursus,
    c.harga,
    c.level,
    u.nama_lengkap,

    -- Tambahan dari tabel payments
    p.payment_id,
    p.jumlah_pembayaran,
    p.tanggal_pembayaran,
    p.metode_pembayaran,
    p.status_pembayaran,
    p.kode_referensi_bank

FROM enrollments e
JOIN schedules s ON e.schedule_id = s.schedule_id
JOIN courses c ON s.course_id = c.course_id
JOIN users u ON e.student_id = u.user_id
LEFT JOIN payments p ON p.enrollment_id = e.enrollment_id
WHERE e.student_id = ?
ORDER BY e.tanggal_daftar DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

?>



<!DOCTYPE html>
<html lang="en">
  <head>
     <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="img/png" href="vendor/images/icon.png" />
    <link rel="stylesheet" href="vendor/style/main.css" />
    <title>BeCreative - Homepage</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-pending {
            color: #ffc107;
        }
        .status-diterima {
            color: #198754;
        }
        .status-ditolak {
            color: #dc3545;
        }
        .status-selesai {
            color: #0d6efd;
        }
        .status-dibatalkan {
            color: #6c757d;
        }
        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .btn-responsive {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
            .dropdown-responsive {
                display: block;
                width: 100%;
            }
            .dropdown-menu-responsive {
                width: 100%;
            }
        }
          .payment-methods .btn {
              transition: all 0.3s ease;
            }
            .payment-methods .btn:hover {
              transform: translateY(-2px);
            }
            .modal-content {
              border-radius: 12px;
              overflow: hidden;
            }
            .form-control:focus, .form-select:focus {
              box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            }
              .icon-circle {
              width: 40px;
              height: 40px;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
            }
            .bg-gradient-info {
              background: linear-gradient(135deg, #17a2b8, #1abc9c);
            }
            .transaction-details {
              font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .card {
              border-radius: 12px;
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
  <div class="navbar-nav ms-auto me-3">
            <a class="nav-link active" href="index.php">Home</a>
            <a class="nav-link" href="pendaftaran_kursus.php">Kursus</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
              <a class="nav-link" href="jadwal.php">jadwal saya</a>
                <a class="nav-link active" href="status_pendaftaran.php">Status Pendaftaran</a>
                <a class="nav-link" href="profil.php">Profil saya</a>
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
 <br>
 <br>
 <br>
 <br>
<div class="container mt-3 mt-md-4">
    <div class="row mb-3 mb-md-4">
        <div class="col">
            <h2 class="h4 h3-md"><i class="fas fa-clipboard-list me-2"></i>Status Pendaftaran Kursus</h2>
            <p class="text-muted small">Berikut adalah status pendaftaran kursus Anda</p>
        </div>
    </div>

    <div class="row">
    <!-- Kolom utama -->
    <div class="col-lg-12 mb-4">
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info">Belum ada pendaftaran kursus.</div>
        <?php endif; ?>

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-4">
                <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div>
                        <span class="mb-2 mb-md-0 d-block fw-semibold">Detail Pendaftaran</span>
                        <span class="text-muted small">Nama Pendaftar: <?= htmlspecialchars($row['nama_lengkap']) ?></span>
                    </div>
                    <span class="badge bg-primary">ID: #ENR<?= $row['enrollment_id'] ?></span>
                </div>

                <div class="card-body">
                    <!-- Informasi Kursus dan Jadwal -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="h6 h5-md"><?= htmlspecialchars($row['nama_kursus']) ?></h5>
                            <p class="text-muted mb-1 small"><i class="far fa-calendar-alt me-2"></i>Jadwal: <?= htmlspecialchars($row['hari_pelaksanaan']) ?>, <?= date('H:i', strtotime($row['waktu_mulai'])) ?> - <?= date('H:i', strtotime($row['waktu_selesai'])) ?></p>
                            <p class="text-muted small"><i class="far fa-clock me-2"></i>Waktu Daftar: <?= date('d M Y', strtotime($row['tanggal_daftar'])) ?></p>
                        </div>
                      <div class="col-md-6">
                        <p class="mb-1 small"><strong>Tanggal Daftar:</strong> <?= date('d M Y, H:i', strtotime($row['tanggal_daftar'])) ?></p>
                        <p class="mb-1 small"><strong>Status:</strong> 
                          <?php if ($row['status_pendaftaran'] === 'diterima'): ?>
                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Diterima</span>
                          <?php elseif ($row['status_pendaftaran'] === 'pending'): ?>
                            <span class="text-warning"><i class="fas fa-clock me-1"></i>Menunggu Konfirmasi</span>
                          <?php elseif ($row['status_pendaftaran'] === 'dibatalkan'): ?>
                            <span class="text-danger"><i class="fas fa-ban me-1"></i>Dibatalkan</span>
                          <?php elseif ($row['status_pendaftaran'] === 'selesai'): ?>
                            <span class="text-primary"><i class="fas fa-graduation-cap me-1"></i>Selesai</span>
                          <?php else: ?>
                            <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Ditolak</span>
                          <?php endif; ?>
                        </p>
                      </div>

                    </div>

                    <!-- Informasi Pembayaran dan Level Kursus -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="small mb-2"><i class="fas fa-money-bill-wave me-2"></i>Informasi Pembayaran</h6>
                                <p class="mb-1"><strong>Total Biaya:</strong></p>
                                <h5 class="text-primary">Rp <?= number_format($row['harga'], 0, ',', '.') ?></h5>
                                <p class="mb-0"><strong>Level Kursus:</strong> <span class="text-danger"><?= htmlspecialchars($row['level']) ?></span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <h6 class="small mb-2"><i class="fas fa-history me-2"></i>Riwayat Status Pendaftaran</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-warning me-2"></i>
                                            <span class="small">Pendaftaran diajukan</span>
                                        </div>
                                        <small class="text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal_daftar'])) ?></small>
                                    </li>

                                    <?php if ($row['status_pendaftaran'] === 'diterima'): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                <span class="small">Pendaftaran diterima</span>
                                            </div>
                                            <small class="text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal_daftar'] . ' +1 day')) ?></small>
                                        </li>
                                    <?php elseif ($row['status_pendaftaran'] === 'ditolak'): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                                <span class="small">Pendaftaran ditolak</span>
                                            </div>
                                            <small class="text-muted"><?= date('d M Y, H:i', strtotime($row['tanggal_daftar'] . ' +1 day')) ?></small>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                        <?php if ($row['status_pendaftaran'] === 'diterima'): ?>
  <div class="alert alert-info small">
    <i class="fas fa-info-circle me-2"></i>
    Silakan lakukan pembayaran untuk mengkonfirmasi keikutsertaan Anda dalam kursus ini.
  </div>

<?php elseif ($row['status_pendaftaran'] === 'pending'): ?>
  <div class="alert alert-warning small">
    <i class="fas fa-clock me-2"></i>
    Pendaftaran Anda sedang menunggu konfirmasi dari admin. Mohon bersabar.
  </div>

<?php elseif ($row['status_pendaftaran'] === 'ditolak'): ?>
  <div class="alert alert-danger small">
    <i class="fas fa-times-circle me-2"></i>
    Maaf, pendaftaran Anda telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut.
  </div>

<?php elseif ($row['status_pendaftaran'] === 'selesai'): ?>
  <div class="alert alert-success small">
    <i class="fas fa-check-circle me-2"></i>
    Pendaftaran Anda telah selesai. Selamat mengikuti kursus!
  </div>
<?php endif; ?>

                                  <!-- Tombol aksi dan pembayaran hanya jika diterima -->
                               <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row gap-2">

                                <?php if (!empty($row['payment_id'])): ?>
                                  <!-- Sudah pernah bayar -->
                                  <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalRiwayat<?= $row['enrollment_id'] ?>">
                                    <i class="fas fa-receipt me-2"></i> Lihat Riwayat Transaksi
                                  </button>

                                  <?php if (in_array($row['status_pembayaran'], ['gagal'])): ?>
                                    <!-- Hanya tampilkan bayar ulang jika status terakhir gagal/dikembalikan -->
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPembayaran<?= $row['enrollment_id'] ?>">
                                      <i class="fas fa-credit-card me-2"></i> Lakukan Pembayaran Ulang
                                    </button>
                                  <?php endif; ?>

                                <?php elseif ($row['status_pendaftaran'] === 'diterima'): ?>
                                  <!-- Belum ada pembayaran dan status diterima -->
                                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPembayaran<?= $row['enrollment_id'] ?>">
                                    <i class="fas fa-credit-card me-2"></i> Lakukan Pembayaran
                                  </button>
                                <?php endif; ?>

                                <?php if (in_array($row['status_pendaftaran'], ['pending', 'diterima'])): ?>
                                  <form action="../php/pendaftaran_kursus/batalkan_pendaftaran.php" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pendaftaran ini?');">
                                    <input type="hidden" name="enrollment_id" value="<?= $row['enrollment_id'] ?>">
                                    <button type="submit" class="btn btn-danger">
                                      <i class="fas fa-times-circle me-2"></i> Batalkan
                                    </button>
                                  </form>
                                <?php endif; ?>

                              </div>


 
                                <div class="modal fade" id="modalPembayaran<?= $row['enrollment_id'] ?>" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered">
                                    <form action="../php/pembayaran/proses_pembayaran.php" method="POST" class="modal-content border-0 shadow-lg">
                                      <!-- Modal Header -->
                                      <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title fw-semibold">
                                          <i class="fas fa-credit-card me-2"></i> Pembayaran Kursus
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      
                                      <!-- Modal Body -->
                                      <div class="modal-body p-4">
                                        <input type="hidden" name="enrollment_id" value="<?= $row['enrollment_id'] ?>">
                                        
                                        <!-- Payment Amount -->
                                        <div class="mb-4">
                                          <label class="form-label fw-medium">Jumlah Pembayaran <span class="text-danger">*</span></label>
                                          <div class="input-group border rounded-3">
                                            <span class="input-group-text bg-light border-0">
                                              <i class="fas fa-money-bill-wave text-primary"></i>
                                            </span>
                                            <input type="number" name="jumlah_pembayaran" class="form-control border-0 py-2" 
                                                  placeholder="Masukkan nominal" required>
                                            <span class="input-group-text bg-light border-0">IDR</span>
                                          </div>
                                          <div class="form-text text-end">Minimal pembayaran: Rp 100.000</div>
                                        </div>

                                        <!-- Payment Method -->
                                        <div class="mb-4">
                                          <label class="form-label fw-medium">Metode Pembayaran <span class="text-danger">*</span></label>
                                          <div class="d-flex flex-wrap gap-2 payment-methods">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="transfer<?= $row['enrollment_id'] ?>" value="Transfer" required>
                                            <label class="btn btn-outline-primary rounded-pill" for="transfer<?= $row['enrollment_id'] ?>">
                                              <i class="fas fa-university me-1"></i> Transfer Bank
                                            </label>

                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="cash<?= $row['enrollment_id'] ?>" value="Cash">
                                            <label class="btn btn-outline-success rounded-pill" for="cash<?= $row['enrollment_id'] ?>">
                                              <i class="fas fa-money-bill-wave me-1"></i> Tunai
                                            </label>

                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="qris<?= $row['enrollment_id'] ?>" value="QRIS">
                                            <label class="btn btn-outline-info rounded-pill" for="qris<?= $row['enrollment_id'] ?>">
                                              <i class="fas fa-qrcode me-1"></i> QRIS
                                            </label>
                                          </div>
                                        </div>

                                        <!-- Bank Reference -->
                                        <div class="mb-3">
                                          <label class="form-label fw-medium">Kode Referensi</label>
                                          <div class="input-group border rounded-3">
                                            <span class="input-group-text bg-light border-0">
                                              <i class="fas fa-receipt text-muted"></i>
                                            </span>
                                            <input type="text" name="kode_referensi_bank" class="form-control border-0 py-2" 
                                                  placeholder="Contoh: TRX-0921ABC">
                                          </div>
                                          <div class="form-text">Masukkan kode referensi jika menggunakan transfer bank</div>
                                        </div>
                                      </div>

                                      <!-- Modal Footer -->
                                      <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                          <i class="fas fa-times me-1"></i> Batal
                                        </button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                          <i class="fas fa-paper-plane me-1"></i> Konfirmasi Pembayaran
                                        </button>
                                      </div>
                                    </form>
                                  </div>
                                </div>


                           <div class="modal fade" id="modalRiwayat<?= $row['enrollment_id'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                              <div class="modal-content border-0 shadow-lg">
                                <!-- Modal Header -->
                                <div class="modal-header bg-gradient-info text-white">
                                  <h5 class="modal-title fw-semibold">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Detail Transaksi
                                  </h5>
                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                
                                <!-- Modal Body -->
                                <div class="modal-body p-4" id="printArea<?= $row['enrollment_id'] ?>">
                                  <div class="transaction-details">
                                    <!-- Transaction Card -->
                                    <div class="card border-0 shadow-sm mb-4">
                                      <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                          <span class="badge bg-opacity-10 bg-white text-dark fs-6 fw-normal">
                                            <i class="fas fa-hashtag text-primary me-1"></i> #<?= $row['enrollment_id'] ?>
                                          </span>
                                          <?php
                                            $badgeClass = match($row['status_pembayaran']) {
                                              'sukses' => 'success',
                                              'pending' => 'warning',
                                              'gagal' => 'danger',
                                              'dikembalikan' => 'secondary',
                                              default => 'dark'
                                            };
                                          ?>
                                          <span class="badge bg-<?= $badgeClass ?> bg-opacity-10 text-<?= $badgeClass ?> py-2 px-3 rounded-pill">
                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                            <?= ucfirst($row['status_pembayaran']) ?>
                                          </span>
                                        </div>
                                        
                                        <div class="transaction-info">
                                          <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-primary bg-opacity-10 text-primary me-3">
                                              <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div>
                                              <p class="mb-0 text-muted small">Tanggal Pembayaran</p>
                                              <p class="mb-0 fw-semibold"><?= date('d M Y H:i', strtotime($row['tanggal_pembayaran'])) ?></p>
                                            </div>
                                          </div>
                                          
                                          <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-success bg-opacity-10 text-success me-3">
                                              <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div>
                                              <p class="mb-0 text-muted small">Jumlah Pembayaran</p>
                                              <p class="mb-0 fw-semibold">Rp <?= number_format($row['jumlah_pembayaran'], 0, ',', '.') ?></p>
                                            </div>
                                          </div>
                                          
                                          <div class="d-flex align-items-center mb-3">
                                            <div class="icon-circle bg-info bg-opacity-10 text-info me-3">
                                              <i class="fas fa-credit-card"></i>
                                            </div>
                                            <div>
                                              <p class="mb-0 text-muted small">Metode Pembayaran</p>
                                              <p class="mb-0 fw-semibold"><?= $row['metode_pembayaran'] ?></p>
                                            </div>
                                          </div>
                                          
                                          <?php if (!empty($row['kode_referensi_bank'])): ?>
                                          <div class="d-flex align-items-center">
                                            <div class="icon-circle bg-warning bg-opacity-10 text-warning me-3">
                                              <i class="fas fa-receipt"></i>
                                            </div>
                                            <div>
                                              <p class="mb-0 text-muted small">Kode Referensi</p>
                                              <p class="mb-0 fw-semibold"><?= $row['kode_referensi_bank'] ?></p>
                                            </div>
                                          </div>
                                          <?php endif; ?>
                                        </div>
                                      </div>
                                    </div>
                                    
                                    <!-- Additional Notes -->
                                    <div class="alert alert-light border">
                                      <div class="d-flex">
                                        <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                                        <div>
                                          <p class="mb-1 small fw-semibold">Informasi Tambahan</p>
                                          <p class="small text-muted mb-0">
                                            Silakan hubungi admin jika terdapat ketidaksesuaian data pembayaran.
                                          </p>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                                <!-- Modal Footer -->
                                <div class="modal-footer border-0 bg-light">
                                  <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i> Tutup
                                  </button>
                                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="printDiv('printArea<?= $row['enrollment_id'] ?>')">
                                  <i class="fas fa-print me-1"></i> Cetak
                                </button>

                                </div>
                              </div>
                            </div>
                          </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</div>
<!-- Modal Pembayaran -->
 

    <!-- Bootstrap JS Bundle with Popper -->
     <script>
        // Hanya untuk tampilan - tombol pembayaran
        document.getElementById('btnPembayaran').addEventListener('click', function() {
            alert('Fitur pembayaran akan diarahkan ke halaman pembayaran.');
        });
    </script>
 
<script>
function printDiv(divId) {
  const content = document.getElementById(divId).innerHTML;
  const myWindow = window.open('', '', 'width=800,height=600');
  myWindow.document.write(`
    <html>
      <head>
        <title>Cetak Transaksi</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; }
          .icon-circle { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; }
          .badge { font-size: 14px !important; }
        </style>
      </head>
      <body onload="window.print(); setTimeout(() => window.close(), 100);">
        ${content}
      </body>
    </html>
  `);
  myWindow.document.close();
}
</script>

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
    <!-- FOOTER END -->

    <!-- Jquery -->
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
      integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <!-- Boostrap Script -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
      crossorigin="anonymous"
    ></script>

    <!-- Main Script -->
    <script src="vendor/js/main.js"></script>
  </body>
</html>