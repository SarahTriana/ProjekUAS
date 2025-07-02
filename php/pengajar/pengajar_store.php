<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];
    $spesialisasi = $_POST['spesialisasi'];
    $pengalaman = $_POST['pengalaman_mengajar_tahun'];

    // Enkripsi password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $tanggal_registrasi = date('Y-m-d H:i:s');

    // Cek apakah email sudah ada
    $cek_email = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "Email sudah terdaftar!";
        exit;
    }

    // Simpan ke tabel users
    $insert_user = "INSERT INTO users 
        (nama_lengkap, email, password_hash, telepon, alamat, tanggal_registrasi, role) 
        VALUES 
        ('$nama_lengkap', '$email', '$password_hash', '$telepon', '$alamat', '$tanggal_registrasi', 'pengajar')";

    if (mysqli_query($conn, $insert_user)) {
        $user_id = mysqli_insert_id($conn); // Ambil ID user terakhir

        // Simpan ke tabel instructors
        $insert_instruktur = "INSERT INTO instructors 
            (instructor_id, spesialisasi, pengalaman_mengajar_tahun, rating_rata_rata)
            VALUES 
            ('$user_id', '$spesialisasi', '$pengalaman', 0)";

        if (mysqli_query($conn, $insert_instruktur)) {
            header("Location: ../../views/dashboard/manajemen_user/data_pengajar.php?success=1");
            exit;
        } else {
            echo "Gagal menyimpan data instruktur: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal menyimpan data user: " . mysqli_error($conn);
    }
} else {
    echo "Akses tidak valid.";
}
?>
