<?php
include '../../database/koneksi.php';

$nama = $_POST['nama_lengkap'];
$email = $_POST['email'];
$telepon = $_POST['telepon'];
$alamat = $_POST['alamat'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$pendidikan = $_POST['pendidikan_terakhir'];
$tgl_lahir = $_POST['tanggal_lahir'];
$tgl_reg = date('Y-m-d H:i:s');

mysqli_begin_transaction($conn);

try {
    // Insert ke tabel users
    $sql_user = "INSERT INTO users (nama_lengkap, email, password_hash, telepon, alamat, tanggal_registrasi, role)
                 VALUES (?, ?, ?, ?, ?, ?, 'siswa')";
    $stmt_user = mysqli_prepare($conn, $sql_user);
    mysqli_stmt_bind_param($stmt_user, "ssssss", $nama, $email, $password, $telepon, $alamat, $tgl_reg);
    if (!mysqli_stmt_execute($stmt_user)) {
        throw new Exception("Gagal insert users: " . mysqli_error($conn));
    }
    $user_id = mysqli_insert_id($conn);

    // Insert ke tabel students
    $sql_siswa = "INSERT INTO students (student_id, pendidikan_terakhir, tanggal_lahir)
                  VALUES (?, ?, ?)";
    $stmt_siswa = mysqli_prepare($conn, $sql_siswa);
    mysqli_stmt_bind_param($stmt_siswa, "iss", $user_id, $pendidikan, $tgl_lahir);
    if (!mysqli_stmt_execute($stmt_siswa)) {
        throw new Exception("Gagal insert students: " . mysqli_error($conn));
    }

    mysqli_commit($conn);
    header("Location: ../../views/dashboard/manajemen_user/data_siswa.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Gagal menyimpan data siswa! Error: " . $e->getMessage();
}
?>
