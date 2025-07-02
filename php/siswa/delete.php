<?php
include '../../database/koneksi.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    mysqli_begin_transaction($conn);
    try {
        // Hapus dari tabel students terlebih dahulu
        $delete_student = "DELETE FROM students WHERE student_id = '$user_id'";
        mysqli_query($conn, $delete_student);

        // Hapus dari tabel users
        $delete_user = "DELETE FROM users WHERE user_id = '$user_id' AND role = 'siswa'";
        mysqli_query($conn, $delete_user);

        mysqli_commit($conn);
        header("Location: ../../views/dashboard/manajemen_user/data_siswa.php?status=hapus_sukses");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: ../../views/dashboard/manajemen_user/data_siswa.php?status=hapus_gagal");
        exit;
    }
}
?>
