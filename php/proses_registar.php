<?php
session_start();
include '../database/koneksi.php'; // koneksi ke database

// Ambil data dari form
$nama = $_POST['nama_lengkap'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$telepon = $_POST['telepon'];
$alamat = $_POST['alamat'];
$role = $_POST['role'];
$tanggal = date('Y-m-d H:i:s');

// Cek apakah email sudah terdaftar
$cek = $conn->prepare("SELECT * FROM users WHERE email = ?");
$cek->bind_param("s", $email);
$cek->execute();
$hasil = $cek->get_result();

if ($hasil->num_rows > 0) {
    echo "Email sudah digunakan.";
    exit;
}

// Insert ke tabel users
$stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password_hash, telepon, alamat, tanggal_registrasi, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $nama, $email, $password, $telepon, $alamat, $tanggal, $role);
$stmt->execute();

$user_id = $stmt->insert_id;
$_SESSION['user_id'] = $user_id;
$_SESSION['role'] = $role;

// Redirect ke form sesuai role
if ($role == 'siswa') {
    header("Location: ../views/form_siswa.php");
} elseif ($role == 'pengajar') {
    header("Location: ../views/form_pengajar.php");
} else {
    echo "Role tidak dikenali.";
}
?>
