<?php
session_start();
include '../../database/koneksi.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Kamu harus login untuk membuat forum.");
}

$course_id = $_POST['course_id'] ?? null;
$judul_topik = mysqli_real_escape_string($conn, $_POST['judul_topik']);
$deskripsi_topik = mysqli_real_escape_string($conn, $_POST['deskripsi_topik']);
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d H:i:s');

if (!$course_id || !$judul_topik || !$deskripsi_topik) {
    die("Semua field wajib diisi.");
}

$sql = "INSERT INTO forums (course_id, judul_topik, deskripsi_topik, user_id_pembuat, tanggal_buat)
        VALUES ('$course_id', '$judul_topik', '$deskripsi_topik', '$user_id', '$tanggal')";

if (mysqli_query($conn, $sql)) {
    // ✅ Kembali ke halaman detail kursus, bawa lagi course_id
header("Location: ../../views/detail_kursus.php?course_id=$course_id&tab=forum");
    exit;
} else {
    echo "Gagal membuat forum: " . mysqli_error($conn);
}
?>
