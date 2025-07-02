<?php
session_start();
include '../../database/koneksi.php';

if (!isset($_GET['lesson_id']) || !is_numeric($_GET['lesson_id'])) {
    $_SESSION['error'] = "ID pelajaran tidak valid.";
    header("Location: modul.php");
    exit;
}

$lesson_id = (int)$_GET['lesson_id'];

// Ambil module_id untuk redirect balik
$getModule = mysqli_query($conn, "SELECT module_id FROM lessons WHERE lesson_id = $lesson_id");
if ($getModule && mysqli_num_rows($getModule) > 0) {
    $row = mysqli_fetch_assoc($getModule);
    $module_id = $row['module_id'];

    // Cek apakah lesson ini masih dipakai di assignments
    $check = mysqli_query($conn, "SELECT COUNT(*) AS total FROM assignments WHERE lesson_id = $lesson_id");
    $total = mysqli_fetch_assoc($check)['total'];

    if ($total > 0) {
        $_SESSION['error'] = "Gagal hapus! Pelajaran ini masih dipakai di tabel tugas (assignments).";
    } else {
        // Baru lakukan delete
        $delete = mysqli_query($conn, "DELETE FROM lessons WHERE lesson_id = $lesson_id");
        if ($delete) {
            $_SESSION['success'] = "Pelajaran berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus pelajaran.";
        }
    }

    header("Location: ../../views/dashboard/manajemen_kursus/pelajaran.php?module_id=$module_id");
    exit;

} else {
    $_SESSION['error'] = "Pelajaran tidak ditemukan.";
    header("Location: modul.php");
    exit;
}
?>
