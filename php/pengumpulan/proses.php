<?php
include '../../database/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submission_id = intval($_POST['submission_id']);
    $assignment_id = intval($_POST['assignment_id']);
    $nilai = isset($_POST['nilai']) ? floatval($_POST['nilai']) : null;
    $feedback = isset($_POST['feedback']) ? mysqli_real_escape_string($conn, $_POST['feedback']) : '';

    // Update database
    $sql = "
      UPDATE submissions
      SET nilai = " . ($nilai !== null ? $nilai : 'NULL') . ",
          feedback_instructor = '$feedback'
      WHERE submission_id = $submission_id
    ";
    mysqli_query($conn, $sql);

    // Redirect kembali ke halaman detail pengumpulan
    header("Location: ../../views/dashboard/pengumpulan/detail_pengumpulan.php?assignment_id=" . $assignment_id);
    exit;
}
?>
