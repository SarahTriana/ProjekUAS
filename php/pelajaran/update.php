<?php
session_start();
include '../../database/koneksi.php';

if (!isset($_POST['tipe_konten']) || !isset($_POST['lesson_id'])) {
    die("Akses tidak valid");
}

$lesson_id      = intval($_POST['lesson_id']);
$tipe           = $_POST['tipe_konten'];
$nama           = $_POST['nama_pelajaran'];
$durasi         = isset($_POST['durasi_menit']) ? intval($_POST['durasi_menit']) : 0;
$module_id      = intval($_POST['module_id']);

$allowed = [
    'teks' => ['txt'],
    'video' => ['mp4', 'mkv', 'avi'],
    'pdf' => ['pdf'],
    'link' => ['txt']
];

$upload_dir = "../../uploads/pelajaran/";
if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

// Ambil file lama dari database
$getOld = mysqli_query($conn, "SELECT konten_pelajaran FROM lessons WHERE lesson_id = $lesson_id");
$oldData = mysqli_fetch_assoc($getOld);
$fileLama = $oldData['konten_pelajaran'];
$konten = $fileLama;

// Proses sesuai tipe konten
if ($tipe === 'link') {
    $konten = trim($_POST['konten_pelajaran']);
} elseif (in_array($tipe, ['teks', 'video', 'pdf'])) {
    // Cek kalau ada file baru yang diupload
    if (isset($_FILES['konten_file']) && $_FILES['konten_file']['error'] === 0) {
        $nama_file = $_FILES['konten_file']['name'];
        $tmp_name  = $_FILES['konten_file']['tmp_name'];
        $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed[$tipe])) {
            die("Ekstensi file tidak valid untuk tipe $tipe");
        }

        $nama_unik = time() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '-', $nama_file);
        $target_path = $upload_dir . $nama_unik;

        if (!move_uploaded_file($tmp_name, $target_path)) {
            die("Gagal mengupload file.");
        }

        // ❗ File lama TIDAK DIHAPUS sesuai permintaan kamu
        $konten = $nama_unik;
    }
    // Kalau gak upload file baru → pakai file lama (tetap aman)
} else {
    die("Tipe konten tidak valid.");
}

// Query Update
$query = "UPDATE lessons SET module_id=?, nama_pelajaran=?, konten_pelajaran=?, tipe_konten=?, durasi_menit=? WHERE lesson_id=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "isssii", $module_id, $nama, $konten, $tipe, $durasi, $lesson_id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../../views/dashboard/manajemen_kursus/pelajaran.php?module_id=$module_id");
    exit;
} else {
    echo "Gagal update pelajaran: " . mysqli_error($conn);
}
?>
