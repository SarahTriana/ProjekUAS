<?php
session_start();
include '../../database/koneksi.php';

if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];

    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];

    // Update tabel users
    $query = "UPDATE users SET 
                nama_lengkap='$nama_lengkap', 
                email='$email', 
                telepon='$telepon', 
                alamat='$alamat' 
              WHERE user_id=$user_id";
    mysqli_query($conn, $query);

    // Ambil role user
    $getRole = mysqli_query($conn, "SELECT role FROM users WHERE user_id=$user_id");
    $roleData = mysqli_fetch_assoc($getRole);
    $role = $roleData['role'];

    if ($role == 'siswa') {
        $pendidikan = $_POST['pendidikan_terakhir'];
        $tgl_lahir = $_POST['tanggal_lahir'];

        $cek = mysqli_query($conn, "SELECT * FROM students WHERE student_id=$user_id");
        if (mysqli_num_rows($cek)) {
            mysqli_query($conn, "UPDATE students SET pendidikan_terakhir='$pendidikan', tanggal_lahir='$tgl_lahir' WHERE student_id=$user_id");
        } else {
            mysqli_query($conn, "INSERT INTO students (student_id, pendidikan_terakhir, tanggal_lahir) VALUES ($user_id, '$pendidikan', '$tgl_lahir')");
        }
    }

    if ($role == 'pengajar') {
        $spesialisasi = $_POST['spesialisasi'];
        $pengalaman = $_POST['pengalaman_mengajar_tahun'];

        $cek = mysqli_query($conn, "SELECT * FROM instructors WHERE instructor_id=$user_id");
        if (mysqli_num_rows($cek)) {
            mysqli_query($conn, "UPDATE instructors SET spesialisasi='$spesialisasi', pengalaman_mengajar_tahun=$pengalaman WHERE instructor_id=$user_id");
        } else {
            mysqli_query($conn, "INSERT INTO instructors (instructor_id, spesialisasi, pengalaman_mengajar_tahun) VALUES ($user_id, '$spesialisasi', $pengalaman)");
        }
    }

    // Kembali ke halaman profil
    header("Location: ../../views/dashboard/dashboard.php");
    exit;
}
?>
