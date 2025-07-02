<?php
session_start();
include '../../database/koneksi.php';

// Pastikan user login
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pengajar'])) {
    die("Akses ditolak.");
}

// Validasi data input
$assignment_id        = $_POST['assignment_id'] ?? null;
$lesson_id            = $_POST['lesson_id'] ?? null;
$judul_tugas          = $_POST['judul_tugas'] ?? '';
$deskripsi_tugas      = $_POST['deskripsi_tugas'] ?? '';
$tanggal_batas_akhir  = $_POST['tanggal_batas_akhir'] ?? '';
$poin_maksimal        = $_POST['poin_maksimal'] ?? null;

// Validasi dasar
if (!$assignment_id || !$lesson_id || !$judul_tugas || !$tanggal_batas_akhir || !$poin_maksimal) {
    die("Semua field wajib diisi.");
}

// Sanitasi
$assignment_id = (int) $assignment_id;
$lesson_id     = (int) $lesson_id;
$poin_maksimal = (int) $poin_maksimal;

// Query update
$query = "
    UPDATE assignments 
    SET 
        lesson_id = $lesson_id,
        judul_tugas = ?,
        deskripsi_tugas = ?,
        tanggal_batas_akhir = ?,
        poin_maksimal = ?
    WHERE assignment_id = ?
";

$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'sssii', $judul_tugas, $deskripsi_tugas, $tanggal_batas_akhir, $poin_maksimal, $assignment_id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) >= 0) {
        // Berhasil update
        header("Location: ../../views/dashboard/tugas/tugas.php?update=success");
        exit;
    } else {
        echo "Gagal mengubah data atau tidak ada perubahan.";
    }
} else {
    echo "Kesalahan query: " . mysqli_error($conn);
}
