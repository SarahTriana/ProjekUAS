<?php
session_start();
include '../../database/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    echo "<script>alert('Akses ditolak. Silakan login sebagai siswa.'); window.location.href='../../views/login.html';</script>";
    exit;
}
date_default_timezone_set('Asia/Jakarta'); // <-- Tambahkan ini

$student_id = $_SESSION['user_id'];
$schedule_id = $_POST['schedule_id'];
$tanggal_daftar = date('Y-m-d H:i:s');

// Cek apakah sudah daftar sebelumnya
$cek = mysqli_query($conn, "
    SELECT * FROM enrollments 
    WHERE student_id = '$student_id' 
    AND schedule_id = '$schedule_id'
    AND status_pendaftaran NOT IN ('ditolak', 'dibatalkan')
");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Kamu sudah mendaftar di jadwal ini.');history.back();</script>";
    exit;
}

// Simpan pendaftaran
mysqli_query($conn, "INSERT INTO enrollments (student_id, schedule_id, tanggal_daftar, status_pendaftaran)
VALUES ('$student_id', '$schedule_id', '$tanggal_daftar', 'pending')");

echo "<script>alert('Pendaftaran berhasil! Menunggu konfirmasi.');window.location='../../views/pendaftaran_kursus.php';</script>";
?>
