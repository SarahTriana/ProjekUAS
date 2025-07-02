<?php
session_start();
include '../../database/koneksi.php';

// Validasi input
if (!isset($_POST['module_id']) || !isset($_POST['course_id']) || !isset($_POST['nama_modul']) || !isset($_POST['deskripsi_modul']) || !isset($_POST['urutan'])) {
    die('Data tidak lengkap.');
}

$module_id = (int) $_POST['module_id'];
$course_id = (int) $_POST['course_id'];
$nama_modul = mysqli_real_escape_string($conn, $_POST['nama_modul']);
$deskripsi_modul = mysqli_real_escape_string($conn, $_POST['deskripsi_modul']);
$urutan = (int) $_POST['urutan'];

// Update data modul
$query = "UPDATE modules 
          SET course_id = $course_id, 
              nama_modul = '$nama_modul', 
              deskripsi_modul = '$deskripsi_modul', 
              urutan = $urutan 
          WHERE module_id = $module_id";

if (mysqli_query($conn, $query)) {
    header("Location: ../../views/dashboard/manajemen_kursus/modul.php?course_id=$course_id&success=1");
    exit;
} else {
    echo "Gagal mengupdate modul: " . mysqli_error($conn);
}
