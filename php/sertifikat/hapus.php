<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak diizinkan.");
}

$certificate_id = $_POST['certificate_id'];
$file = $_POST['file_sertifikat_url'];

$upload_dir = __DIR__ . '/../../uploads/sertifikat/';

// Hapus file jika ada
if ($file && file_exists($upload_dir . $file)) {
    unlink($upload_dir . $file);
}

// Hapus data dari database
$query = "DELETE FROM certificates WHERE certificate_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $certificate_id);
mysqli_stmt_execute($stmt);

header("Location: ../../views/dashboard/sertifikat/sertifikat.php");
exit;
?>
