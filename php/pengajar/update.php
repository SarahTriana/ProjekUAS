<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['user_id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $spesialisasi = $_POST['spesialisasi'];
    $pengalaman = $_POST['pengalaman_mengajar_tahun'];

    // Update tabel users
    $update_user = "UPDATE users SET 
                    nama_lengkap = '$nama_lengkap',
                    email = '$email',
                    telepon = '$telepon',
                    alamat = '$alamat'
                    WHERE user_id = '$id'";

    // Update tabel instructors
    $update_instructor = "UPDATE instructors SET 
                          spesialisasi = '$spesialisasi',
                          pengalaman_mengajar_tahun = '$pengalaman'
                          WHERE instructor_id = '$id'";

    if (mysqli_query($conn, $update_user) && mysqli_query($conn, $update_instructor)) {
        header("Location: ../../views/dashboard/manajemen_user/data_pengajar.php?success=1");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($conn);
    }
} else {
    echo "Akses tidak valid.";
}
?>
