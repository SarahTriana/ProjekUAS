<?php

include '../../database/koneksi.php';

$payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

$allowed_status = ['pending', 'sukses', 'gagal', 'dikembalikan'];

if ($payment_id > 0 && in_array($status, $allowed_status)) {
    $stmt = $conn->prepare("UPDATE payments SET status_pembayaran = ? WHERE payment_id = ?");
    $stmt->bind_param("si", $status, $payment_id);

    if ($stmt->execute()) {
        // Ambil enrollment_id dari payment_id
        $queryEnroll = $conn->prepare("SELECT enrollment_id FROM payments WHERE payment_id = ?");
        $queryEnroll->bind_param("i", $payment_id);
        $queryEnroll->execute();
        $queryEnroll->bind_result($enrollment_id);
        $queryEnroll->fetch();
        $queryEnroll->close();

        // Jika status pembayaran = sukses → update enrollments jadi selesai
        if ($status === 'sukses') {
            $updateEnroll = $conn->prepare("UPDATE enrollments SET status_pendaftaran = 'selesai' WHERE enrollment_id = ?");
            $updateEnroll->bind_param("i", $enrollment_id);
            $updateEnroll->execute();
            $updateEnroll->close();
        }

        header("Location: ../../views/dashboard/pembayaran/laporan_pembayaran.php?msg=Status+berhasil+diubah");
        exit;
    } else {
        echo "Gagal mengubah status: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Data tidak valid.";
}
?>
