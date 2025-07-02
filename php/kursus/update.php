 
<?php
include '../../database/koneksi.php';

$id = $_POST['course_id'];
$nama_kursus = $_POST['nama_kursus'];
$deskripsi = $_POST['deskripsi'];
$durasi_jam = $_POST['durasi_jam'];
$harga = $_POST['harga'];
$level = $_POST['level'];
$status_aktif = isset($_POST['status_aktif']) ? 1 : 0;

$query = "UPDATE courses SET 
            nama_kursus = '$nama_kursus',
            deskripsi = '$deskripsi',
            durasi_jam = '$durasi_jam',
            harga = '$harga',
            level = '$level',
            status_aktif = '$status_aktif'
          WHERE course_id = '$id'";

mysqli_query($conn, $query);

        header("Location:../../views/dashboard/manajemen_kursus/daftar_kursus.php?msg=sukses_edit");
exit;
?>
