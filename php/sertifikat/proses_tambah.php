<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak diizinkan.");
}

$enrollment_id = $_POST['enrollment_id'];
$nomor = $_POST['nomor_sertifikat'];
$tanggal = $_POST['tanggal_terbit'];
$nilai = $_POST['nilai_akhir'] ?? null;

// GUNAKAN path yang sesuai untuk move_uploaded_file
$upload_dir = __DIR__ . "/../../uploads/sertifikat/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$fileName = null;

if (!empty($_FILES['file_sertifikat']['name'])) {
    $file = $_FILES['file_sertifikat'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = 'sertifikat_' . time() . '.' . $ext;
    $target = $upload_dir . $newName;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $fileName = $newName;
    } else {
        die("Gagal upload file.");
    }
}

$query = "INSERT INTO certificates (enrollment_id, nomor_sertifikat, tanggal_terbit, nilai_akhir, file_sertifikat_url)
          VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issss", $enrollment_id, $nomor, $tanggal, $nilai, $fileName);
mysqli_stmt_execute($stmt);

header("Location: ../../views/dashboard/sertifikat/sertifikat.php");
exit;
