<?php
include '../../database/koneksi.php';

// Validasi module_id
if (!isset($_GET['module_id']) || !is_numeric($_GET['module_id'])) {
    die('Parameter module_id tidak valid.');
}

$module_id = (int) $_GET['module_id'];

// Eksekusi query delete
$query = "DELETE FROM modules WHERE module_id = $module_id";
$result = mysqli_query($conn, $query);

if ($result) {
    // Redirect kembali ke halaman modul utama
    header("Location: ../../views/dashboard/manajemen_kursus/modul.php");
    exit;
} else {
    die("Gagal menghapus modul: " . mysqli_error($conn));
}
