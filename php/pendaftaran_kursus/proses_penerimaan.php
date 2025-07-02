<?php
session_start();
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = $_POST['enrollment_id'];
    $aksi = $_POST['aksi'];

    $allowed = ['pending', 'diterima', 'ditolak'];
    if (!in_array($aksi, $allowed)) {
        $_SESSION['pesan'] = "Status tidak valid.";
        header("Location: ../../views/status_pendaftaran.php");
        exit;
    }

     if ($aksi === 'diterima') {
        $cekPembayaran = $conn->prepare("SELECT status_pembayaran FROM payments WHERE enrollment_id = ? AND status_pembayaran = 'sukses' LIMIT 1");
        $cekPembayaran->bind_param("i", $enrollment_id);
        $cekPembayaran->execute();
        $cekPembayaran->store_result();

         if ($cekPembayaran->num_rows > 0) {
            $aksi = 'selesai';
        }

        $cekPembayaran->close();
    }

    // Update status_pendaftaran sesuai hasil akhir
    $stmt = $conn->prepare("UPDATE enrollments SET status_pendaftaran = ? WHERE enrollment_id = ?");
    $stmt->bind_param("si", $aksi, $enrollment_id);

    if ($stmt->execute()) {
        $_SESSION['pesan'] = "Status pendaftaran berhasil diubah ke '$aksi'.";
    } else {
        $_SESSION['pesan'] = "Gagal mengubah status.";
    }

    $stmt->close();
    $conn->close();
header("Location: ../../views/dashboard/pendaftaran_kursus/pendaftaran.php");
    exit;
}
?>


 