<?php
session_start();
include '../../database/koneksi.php';

// Validasi role (hanya admin atau pengajar)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pengajar'])) {
    header("Location: ../../views/login.html");
    exit;
}

// Ambil data dari form
$lesson_id          = $_POST['lesson_id'] ?? null;
$judul_tugas        = mysqli_real_escape_string($conn, $_POST['judul_tugas'] ?? '');
$deskripsi_tugas    = mysqli_real_escape_string($conn, $_POST['deskripsi_tugas'] ?? '');
$tanggal_batas      = $_POST['tanggal_batas_akhir'] ?? null;
$poin_maksimal      = (int) ($_POST['poin_maksimal'] ?? 0);

// Validasi input sederhana
if (!$lesson_id || !$judul_tugas || !$tanggal_batas) {
    $_SESSION['error'] = "Semua kolom wajib diisi.";
    header("Location: ../../views/dashboard/tugas/tugas.php");
    exit;
}

// Simpan ke database
$query = "
    INSERT INTO assignments (lesson_id, judul_tugas, deskripsi_tugas, tanggal_batas_akhir, poin_maksimal)
    VALUES ('$lesson_id', '$judul_tugas', '$deskripsi_tugas', '$tanggal_batas', '$poin_maksimal')
";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Tugas berhasil ditambahkan.";
} else {
    $_SESSION['error'] = "Gagal menyimpan tugas: " . mysqli_error($conn);
}

header("Location: ../../views/dashboard/tugas/tugas.php");
exit;
?>
