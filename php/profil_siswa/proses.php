<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../../database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    die('User belum login.');
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Proses Edit Profil
    if (isset($_POST['update_profil'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

        // Update tabel users
        mysqli_query($conn, "UPDATE users SET 
            nama_lengkap='$nama', 
            email='$email', 
            telepon='$telepon', 
            alamat='$alamat' 
            WHERE user_id=$userId
        ");

        // Cek role siswa
        $resultRole = mysqli_query($conn, "SELECT role FROM users WHERE user_id=$userId");
        $role = mysqli_fetch_assoc($resultRole)['role'];

        if ($role === 'siswa') {
            $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
            $pendidikan = mysqli_real_escape_string($conn, $_POST['pendidikan_terakhir']);

            mysqli_query($conn, "UPDATE students SET 
                tanggal_lahir='$tanggal_lahir', 
                pendidikan_terakhir='$pendidikan' 
                WHERE student_id=$userId
            ");
        }

        $_SESSION['pesan'] = "Profil berhasil diperbarui.";
        header("Location: ../../views/profil.php");
        exit;
    }

    // Proses Ubah Password
    if (isset($_POST['ubah_password'])) {
        $passwordLama = $_POST['password_lama'];
        $passwordBaru = $_POST['password_baru'];
        $konfirmasiPassword = $_POST['konfirmasi_password'];

        // Ambil password hash lama
        $result = mysqli_query($conn, "SELECT password_hash FROM users WHERE user_id=$userId");
        $data = mysqli_fetch_assoc($result);

        if (!password_verify($passwordLama, $data['password_hash'])) {
            $_SESSION['error'] = "Password lama salah!";
            header("Location: ../../views/profil.php");
            exit;
        }

        if ($passwordBaru !== $konfirmasiPassword) {
            $_SESSION['error'] = "Konfirmasi password tidak sama!";
            header("Location: ../../views/profil.php");
            exit;
        }

        $newHash = password_hash($passwordBaru, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password_hash='$newHash' WHERE user_id=$userId");

        $_SESSION['pesan'] = "Password berhasil diubah.";
        header("Location: ../../views/profil.php");
        exit;
    }
}

echo "Tidak ada proses yang dijalankan.";
?>
