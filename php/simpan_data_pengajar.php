<?php
session_start();
include '../database/koneksi.php';

if ($_SESSION['role'] !== 'pengajar') {
    echo "Akses tidak diizinkan.";
    exit;
}

$spesialisasi = $_POST['spesialisasi'];
$pengalaman = $_POST['pengalaman_mengajar_tahun'];
$rating = $_POST['rating_rata_rata'] ?: null;
$user_id = $_SESSION['user_id'];

// Simpan ke tabel instructors
$stmt = $conn->prepare("INSERT INTO instructors (instructor_id, spesialisasi, pengalaman_mengajar_tahun, rating_rata_rata) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isid", $user_id, $spesialisasi, $pengalaman, $rating);
$stmt->execute();

header("Location: ../views/dashboard/dashboard.php");
?>
