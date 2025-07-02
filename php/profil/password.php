<?php
session_start();
include '../../database/koneksi.php'; // ganti path sesuai strukturmu

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo "<script>alert('Silakan login terlebih dahulu.'); window.location='../../auth/login.php';</script>";
        exit;
    }

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['new_password_confirmation'];

    // Cek validasi konfirmasi password
    if ($new !== $confirm) {
        echo "<script>alert('Konfirmasi password baru tidak cocok.'); history.back();</script>";
        exit;
    }

    // Ambil password hash lama
    $query = $conn->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result()->fetch_assoc();

    if (!$result || !password_verify($current, $result['password_hash'])) {
        echo "<script>alert('Password saat ini salah.'); history.back();</script>";
        exit;
    }

    // Simpan password baru
    $newHash = password_hash($new, PASSWORD_BCRYPT);
    $update = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $update->bind_param("si", $newHash, $user_id);
    $update->execute();

 header("Location: ../../views/dashboard/dashboard.php");
    exit;}
?>
