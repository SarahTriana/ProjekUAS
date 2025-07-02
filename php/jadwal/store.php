<?php
include '../../database/koneksi.php';

$course_id = $_POST['course_id'];
$instructor_id = $_POST['instructor_id'];
$tanggal_mulai = $_POST['tanggal_mulai'];
$tanggal_selesai = $_POST['tanggal_selesai'];
$waktu_mulai = $_POST['waktu_mulai'];
$waktu_selesai = $_POST['waktu_selesai'];
$hari_pelaksanaan = $_POST['hari_pelaksanaan'];
$kapasitas_maksimal = $_POST['kapasitas_maksimal'];
$lokasi_kelas = $_POST['lokasi_kelas'];

$query = "INSERT INTO schedules (course_id, instructor_id, tanggal_mulai, tanggal_selesai, waktu_mulai, waktu_selesai, hari_pelaksanaan, kapasitas_maksimal, lokasi_kelas)
          VALUES ('$course_id', '$instructor_id', '$tanggal_mulai', '$tanggal_selesai', '$waktu_mulai', '$waktu_selesai', '$hari_pelaksanaan', '$kapasitas_maksimal', '$lokasi_kelas')";

if (mysqli_query($conn, $query)) {
    header("Location: ../../views/dashboard/jadwal/jadwal.php?success=1");
} else {
    echo "Gagal menambahkan jadwal: " . mysqli_error($conn);
}
?>
