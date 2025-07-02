<?php
include '../../database/koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM courses WHERE course_id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location:  ../../views/dashboard/manajemen_kursus/daftar_kursus.php?msg=sukses_hapus");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
