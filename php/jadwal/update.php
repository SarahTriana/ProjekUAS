<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.html");
    exit;
}

include '../../database/koneksi.php';

// Pastikan semua data terkirim
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id              = $_POST['schedule_id'];
    $course_id       = $_POST['course_id'];
    $instructor_id   = $_POST['instructor_id'];
    $tanggal_mulai   = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $waktu_mulai     = $_POST['waktu_mulai'];
    $waktu_selesai   = $_POST['waktu_selesai'];
    $hari            = $_POST['hari_pelaksanaan'];
    $kapasitas       = $_POST['kapasitas_maksimal'];
    $lokasi          = $_POST['lokasi_kelas'];

    $update = mysqli_query($conn, "
        UPDATE schedules SET 
            course_id = '$course_id',
            instructor_id = '$instructor_id',
            tanggal_mulai = '$tanggal_mulai',
            tanggal_selesai = '$tanggal_selesai',
            waktu_mulai = '$waktu_mulai',
            waktu_selesai = '$waktu_selesai',
            hari_pelaksanaan = '$hari',
            kapasitas_maksimal = '$kapasitas',
            lokasi_kelas = '$lokasi'
        WHERE schedule_id = '$id'
    ");

    if ($update) {
        $_SESSION['success'] = "Jadwal berhasil diperbarui.";
    } else {
        $_SESSION['error'] = "Gagal memperbarui jadwal: " . mysqli_error($conn);
    }
}

header("Location: ../../views/dashboard/jadwal/jadwal.php");
exit;
