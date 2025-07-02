<?php
include '../../database/koneksi.php';

// Validasi form input
$course_id        = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$nama_modul       = isset($_POST['nama_modul']) ? trim($_POST['nama_modul']) : '';
$deskripsi_modul  = isset($_POST['deskripsi_modul']) ? trim($_POST['deskripsi_modul']) : '';
$urutan           = isset($_POST['urutan']) ? (int)$_POST['urutan'] : 0;

if ($course_id <= 0 || $nama_modul === '' || $urutan <= 0) {
    die('Data modul tidak lengkap atau tidak valid.');
}

// Gunakan prepared statement agar aman dari SQL injection
$stmt = mysqli_prepare($conn, "INSERT INTO modules (course_id, nama_modul, deskripsi_modul, urutan) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "issi", $course_id, $nama_modul, $deskripsi_modul, $urutan);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../../views/dashboard/manajemen_kursus/modul.php?course_id=$course_id&success=1");
    exit;
} else {
    echo "Gagal menyimpan modul: " . mysqli_error($conn);
}
