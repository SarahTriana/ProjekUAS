<?php
session_start();
include '../../database/koneksi.php';

if (!isset($_POST['tipe_konten'])) {
    die("Akses tidak valid");
}

$tipe = $_POST['tipe_konten'];
$nama = $_POST['nama_pelajaran'];
$durasi = isset($_POST['durasi_menit']) ? intval($_POST['durasi_menit']) : 0;
$module_id = intval($_POST['module_id']);

$allowed = [
    'teks' => ['txt'],
    'video' => ['mp4', 'mkv', 'avi'],
    'pdf' => ['pdf'],
    'link' => ['txt']
];

$upload_dir = "../../uploads/pelajaran/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$konten = "";

if ($tipe === 'link') {
    $konten = trim($_POST['konten_pelajaran']);
} elseif (in_array($tipe, ['teks', 'video', 'pdf'])) {
    if (!isset($_FILES['konten_file']) || $_FILES['konten_file']['error'] !== 0) {
        die("File tidak berhasil diunggah.");
    }

    $nama_file = $_FILES['konten_file']['name'];
    $tmp_name = $_FILES['konten_file']['tmp_name'];
    $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed[$tipe])) {
        die("Ekstensi file tidak valid untuk tipe $tipe");
    }

    $nama_unik = time() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '-', $nama_file);
    $target_path = $upload_dir . $nama_unik;

    if (!move_uploaded_file($tmp_name, $target_path)) {
        die("Gagal mengupload file.");
    }

    $konten = $nama_unik;
} else {
    die("Tipe konten tidak valid.");
}

$query = "INSERT INTO lessons (module_id, nama_pelajaran, konten_pelajaran, tipe_konten, durasi_menit)
          VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "isssi", $module_id, $nama, $konten, $tipe, $durasi);
mysqli_stmt_execute($stmt);

header("Location: ../../views/dashboard/manajemen_kursus/pelajaran.php?module_id=$module_id");
exit;
?>
