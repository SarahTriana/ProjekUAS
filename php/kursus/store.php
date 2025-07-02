<?php
session_start();
include '../../database/koneksi.php';

$nama_kursus = $_POST['nama_kursus'];
$deskripsi = $_POST['deskripsi'];
$durasi_jam = $_POST['durasi_jam'];
$harga = $_POST['harga'];
$level = $_POST['level'];
$status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

$sql = "INSERT INTO courses (nama_kursus, deskripsi, durasi_jam, harga, level, status_aktif) 
        VALUES ('$nama_kursus', '$deskripsi', $durasi_jam, $harga, '$level', $status_aktif)";

if (mysqli_query($conn, $sql)) {
    header("Location: ../../views/dashboard/manajemen_kursus/daftar_kursus.php?success=1");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
