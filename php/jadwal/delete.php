<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.html");
    exit;
}

include '../../database/koneksi.php';

if (isset($_GET['id'])) {
    $schedule_id = $_GET['id'];

    // Eksekusi DELETE
    $delete = mysqli_query($conn, "DELETE FROM schedules WHERE schedule_id = '$schedule_id'");

    if ($delete) {
        $_SESSION['success'] = "Jadwal berhasil dihapus.";
    } else {
        $_SESSION['error'] = "Gagal menghapus jadwal: " . mysqli_error($conn);
    }
}

header("Location: ../../views/dashboard/jadwal/jadwal.php");
exit;
