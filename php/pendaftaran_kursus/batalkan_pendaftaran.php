<?php
session_start();
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = $_POST['enrollment_id'];

    // 1. Ubah status_pendaftaran jadi 'dibatalkan'
    $stmt1 = $conn->prepare("UPDATE enrollments SET status_pendaftaran = 'dibatalkan' WHERE enrollment_id = ?");
    $stmt1->bind_param("i", $enrollment_id);

    // 2. Ubah status_pembayaran jadi 'pending'
    $stmt2 = $conn->prepare("UPDATE payments SET status_pembayaran = 'pending' WHERE enrollment_id = ?");
    $stmt2->bind_param("i", $enrollment_id);

    if ($stmt1->execute() && $stmt2->execute()) {
        $_SESSION['pesan'] = "Pendaftaran dibatalkan. Menunggu keputusan admin untuk pengembalian dana.";
    } else {
        $_SESSION['pesan'] = "Gagal memperbarui data pembatalan.";
    }

    $stmt1->close();
    $stmt2->close();
    $conn->close();

    header("Location: ../../views/status_pendaftaran.php");
    exit;
}
?>
