<?php
session_start();
include '../database/koneksi.php';

if ($_SESSION['role'] !== 'siswa') {
    echo "Akses tidak diizinkan.";
    exit;
}

$pendidikan = $_POST['pendidikan_terakhir'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$user_id = $_SESSION['user_id'];

// Simpan ke tabel students
$stmt = $conn->prepare("INSERT INTO students (student_id, pendidikan_terakhir, tanggal_lahir) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $pendidikan, $tanggal_lahir);
$stmt->execute();

header("Location: ../views/index.php");
?>
