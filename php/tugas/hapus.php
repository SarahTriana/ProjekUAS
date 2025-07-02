<?php
session_start();
include '../../database/koneksi.php';

// Cek apakah pengguna sudah login dan memiliki peran yang sesuai
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'pengajar'])) {
    header("Location: ../../views/login.html");
    exit;
}

// Validasi parameter
if (!isset($_GET['assignment_id']) || !is_numeric($_GET['assignment_id'])) {
    die("Parameter tidak valid.");
}

$assignment_id = (int)$_GET['assignment_id'];

// Cek apakah tugas ada di database
$check = mysqli_query($conn, "SELECT * FROM assignments WHERE assignment_id = $assignment_id");
if (mysqli_num_rows($check) === 0) {
    die("Tugas tidak ditemukan.");
}

// Hapus tugas
$delete = mysqli_query($conn, "DELETE FROM assignments WHERE assignment_id = $assignment_id");

if ($delete) {
    // Redirect kembali ke halaman daftar tugas
    header("Location: ../../views/dashboard/tugas/tugas.php?msg=sukses");
    exit;
} else {
    die("Gagal menghapus tugas: " . mysqli_error($conn));
}
