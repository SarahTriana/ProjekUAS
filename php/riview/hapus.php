<?php
session_start();
include '../../database/koneksi.php';

$student_id = $_SESSION['user_id'] ?? null;

if (!$student_id) {
    echo "<script>alert('Silakan login terlebih dahulu'); window.location='../../auth/login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
    $review_id = $_POST['review_id'];

    // Ambil info review sebelum dihapus
    $q = mysqli_query($conn, "SELECT * FROM reviews WHERE review_id = '$review_id' AND student_id = '$student_id'");
    $review = mysqli_fetch_assoc($q);

    if (!$review) {
        echo "<script>alert('Review tidak ditemukan atau Anda tidak memiliki izin.'); window.history.back();</script>";
        exit;
    }

    $tipe_review = $review['tipe_review'];
    $instructor_id = $review['instructor_id'] ?? null;

    // Hapus review
    mysqli_query($conn, "DELETE FROM reviews WHERE review_id = '$review_id' AND student_id = '$student_id'");

    // Jika ulasan untuk pengajar, update ulang rating rata-rata instruktur
    if ($tipe_review === 'pengajar' && $instructor_id) {
        $qRating = mysqli_query($conn, "
            SELECT AVG(rating) AS rata2 
            FROM reviews 
            WHERE instructor_id = '$instructor_id' AND tipe_review = 'pengajar'
        ");
        $r = mysqli_fetch_assoc($qRating);
        $rata2 = round($r['rata2'] ?? 0, 1);

        mysqli_query($conn, "
            UPDATE instructors 
            SET rating_rata_rata = '$rata2' 
            WHERE instructor_id = '$instructor_id'
        ");
    }

   header("Location: ../../views/review.php?id=" . $review['course_id']);
exit;

} else {
    echo "<script>alert('Permintaan tidak valid.'); window.history.back();</script>";
    exit;
}
