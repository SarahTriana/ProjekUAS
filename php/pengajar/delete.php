<?php
include '../../database/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus dari tabel instructors terlebih dahulu
    $delete_instructor = "DELETE FROM instructors WHERE instructor_id = '$id'";
    $delete_user = "DELETE FROM users WHERE user_id = '$id'";

    if (mysqli_query($conn, $delete_instructor) && mysqli_query($conn, $delete_user)) {
        header("Location: ../../views/dashboard/manajemen_user/data_pengajar.php?deleted=1");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak ditemukan.";
}
?>
