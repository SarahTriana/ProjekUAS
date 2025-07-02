<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $pendidikan_terakhir = $_POST['pendidikan_terakhir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];

    mysqli_begin_transaction($conn);

    try {
        // Update ke tabel users
        $update_user = "UPDATE users SET 
                        nama_lengkap = '$nama_lengkap',
                        email = '$email',
                        telepon = '$telepon',
                        alamat = '$alamat'
                        WHERE user_id = '$user_id'";
        mysqli_query($conn, $update_user);

        // Update ke tabel students
        $update_student = "UPDATE students SET 
                            pendidikan_terakhir = '$pendidikan_terakhir',
                            tanggal_lahir = '$tanggal_lahir'
                            WHERE student_id = '$user_id'";
        mysqli_query($conn, $update_student);

        mysqli_commit($conn);
        header("Location: ../../views/dashboard/manajemen_user/data_siswa.php?status=update_sukses");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: ../../views/dashboard/manajemen_user/data_siswa.php?status=update_gagal");
        exit;
    }
}
?>
