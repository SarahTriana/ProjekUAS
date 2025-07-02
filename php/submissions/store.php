<?php
session_start();
include '../../database/koneksi.php';

// Validasi data POST
$assignment_id = $_POST['assignment_id'] ?? null;
$student_id = $_POST['student_id'] ?? null;
$file = $_FILES['file_submission'] ?? null;

if (!$assignment_id || !$student_id || !$file) {
    die("Data tidak lengkap.");
}

// Upload file ke folder uploads/submissions/
$target_dir = "../../uploads/tugas/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

$original_name = basename($file['name']);
$ext = pathinfo($original_name, PATHINFO_EXTENSION);
$unique_name = 'tugas_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
$target_file = $target_dir . $unique_name;

if (move_uploaded_file($file['tmp_name'], $target_file)) {
    // Simpan ke database
    $tanggal_submit = date('Y-m-d H:i:s');
    $file_submission_url = $unique_name;

    $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, tanggal_submit, file_submission_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $assignment_id, $student_id, $tanggal_submit, $file_submission_url);

    if ($stmt->execute()) {
        header("Location: ../../views/detail_kursus.php?course_id=$_POST[course_id]&status=success");
        exit;
    } else {
        echo "Gagal menyimpan data tugas.";
    }
} else {
    echo "Gagal mengupload file.";
}
?>
