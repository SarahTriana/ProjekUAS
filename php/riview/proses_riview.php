<?php
session_start();
include '../../database/koneksi.php';

$student_id = $_SESSION['user_id'] ?? null;
if (!$student_id) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='../../login.php';</script>";
    exit;
}

$tipe_review = $_POST['tipe_review'] ?? '';
$rating = $_POST['rating'] ?? 0;
$komentar = $_POST['komentar'] ?? '';
$course_id = $_POST['course_id'] ?? null;
$tanggal_review = date('Y-m-d H:i:s');

$instructor_id = null;

// ✅ Jika review untuk pengajar → cari instructor_id dari tabel schedules
if ($tipe_review === 'pengajar' && $course_id) {
    $q = mysqli_query($conn, "SELECT instructor_id FROM schedules WHERE course_id = '$course_id' LIMIT 1");
    $d = mysqli_fetch_assoc($q);
    $instructor_id = $d['instructor_id'] ?? null;
}

// ✅ Simpan review
mysqli_query($conn, "
    INSERT INTO reviews (student_id, course_id, instructor_id, rating, komentar, tanggal_review, tipe_review)
    VALUES ('$student_id', '$course_id', " . ($instructor_id ? "'$instructor_id'" : "NULL") . ", '$rating', '$komentar', '$tanggal_review', '$tipe_review')
");

// ✅ Jika pengajar → update rating rata-rata instruktur
if ($tipe_review === 'pengajar' && $instructor_id) {
    $qRating = mysqli_query($conn, "
        SELECT AVG(rating) as rata2 FROM reviews 
        WHERE instructor_id = '$instructor_id' AND tipe_review = 'pengajar'
    ");
    $r = mysqli_fetch_assoc($qRating);
    $rata2 = round($r['rata2'], 1);

    mysqli_query($conn, "
        UPDATE instructors SET rating_rata_rata = '$rata2' 
        WHERE instructor_id = '$instructor_id'
    ");
}

header("Location: ../../views/review.php?id=$course_id");
exit;

?>
