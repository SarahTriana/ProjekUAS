<?php
include '../../database/koneksi.php';
session_start();

$user_id = $_SESSION['user_id'];
$forum_id = $_POST['forum_id'];
$parent_post_id = $_POST['parent_post_id'] ?? null;
$konten_post = mysqli_real_escape_string($conn, $_POST['konten_post']);
$tanggal_post = date('Y-m-d H:i:s');

$query = "INSERT INTO forumposts (forum_id, user_id, konten_post, tanggal_post, parent_post_id)
          VALUES ('$forum_id', '$user_id', '$konten_post', '$tanggal_post', " . ($parent_post_id ? "'$parent_post_id'" : "NULL") . ")";

mysqli_query($conn, $query);


// Ambil course_id dari forum_id
$getCourseQuery = "SELECT course_id FROM forums WHERE forum_id = $forum_id";
$getCourseResult = mysqli_query($conn, $getCourseQuery);
$courseRow = mysqli_fetch_assoc($getCourseResult);
$course_id = $courseRow['course_id'] ?? 0;

// Redirect balik ke halaman detail kursus
header("Location: ../../views/dashboard/forum/forum_diskusi.php?course_id=$course_id");
exit;
exit;
?>
