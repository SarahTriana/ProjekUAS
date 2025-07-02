<?php
session_start();
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = $_POST['enrollment_id'];
    $jumlah = $_POST['jumlah_pembayaran'];
    $metode = $_POST['metode_pembayaran'];
    $kode_referensi_bank = !empty($_POST['kode_referensi_bank']) ? trim($_POST['kode_referensi_bank']) : null;

    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d H:i:s');

    // Cek apakah enrollment valid
    $cek = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE enrollment_id = ?");
    $cek->bind_param("i", $enrollment_id);
    $cek->execute();
    $res = $cek->get_result();

    if ($res->num_rows === 0) {
        die("Enrollment ID tidak valid.");
    }

    // Query dengan 6 kolom dan 6 nilai
    $stmt = $conn->prepare("INSERT INTO payments 
        (enrollment_id, jumlah_pembayaran, tanggal_pembayaran, metode_pembayaran, kode_referensi_bank, status_pembayaran) 
        VALUES (?, ?, ?, ?, ?, 'pending')");

    $stmt->bind_param("idsss", $enrollment_id, $jumlah, $tanggal, $metode, $kode_referensi_bank);

    if ($stmt->execute()) {
        $_SESSION['pesan'] = "Pembayaran berhasil dikirim!";
    } else {
        $_SESSION['pesan'] = "Gagal menyimpan pembayaran.";
    }

    $stmt->close();
    header("Location: ../../views/status_pendaftaran.php");
    exit;
}
?>
