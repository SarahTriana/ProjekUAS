<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak diizinkan.");
}

$certificate_id = $_POST['certificate_id'];
$enrollment_id  = $_POST['enrollment_id'];
$nomor          = $_POST['nomor_sertifikat'];
$tanggal        = $_POST['tanggal_terbit'];
$nilai          = $_POST['nilai_akhir'] ?? null;

// Ambil file lama
$cek     = mysqli_query($conn, "SELECT file_sertifikat_url FROM certificates WHERE certificate_id = $certificate_id");
$lama    = mysqli_fetch_assoc($cek);
$oldFile = $lama['file_sertifikat_url'];

$upload_dir = __DIR__ . "/../../uploads/sertifikat/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$finalFileName = $oldFile;

if (!empty($_FILES['file_sertifikat']['name'])) {
    $file    = $_FILES['file_sertifikat'];
    $ext     = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = 'sertifikat_' . time() . '.' . $ext;
    $target  = $upload_dir . $newName;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        // Hapus file lama jika ada
        if ($oldFile && file_exists($upload_dir . $oldFile)) {
            unlink($upload_dir . $oldFile);
        }
        $finalFileName = $newName;
    } else {
        die("Gagal upload file.");
    }
}

// Update database
$query = "UPDATE certificates SET 
            enrollment_id = ?, 
            nomor_sertifikat = ?, 
            tanggal_terbit = ?, 
            nilai_akhir = ?, 
            file_sertifikat_url = ?
          WHERE certificate_id = ?";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issssi", $enrollment_id, $nomor, $tanggal, $nilai, $finalFileName, $certificate_id);
mysqli_stmt_execute($stmt);

// Redirect
header("Location: ../../views/dashboard/sertifikat/sertifikat.php");
exit;
