<?php
include '../../database/koneksi.php';
session_start();

$forum_id = $_GET['forum_id'] ?? null;

if ($forum_id) {
     $cekForumQuery = "SELECT user_id_pembuat, course_id FROM forums WHERE forum_id = $forum_id";
    $cekForumResult = mysqli_query($conn, $cekForumQuery);
    $forumData = mysqli_fetch_assoc($cekForumResult);

    if (!$forumData) {
        die("Forum tidak ditemukan.");
    }

    if ($forumData['user_id_pembuat'] != $_SESSION['user_id']) {
        die("Maaf, Anda tidak punya izin untuk menghapus topik ini.");
    }

    $course_id = $forumData['course_id'];

     $deletePosts = "DELETE FROM forumposts WHERE forum_id = $forum_id";
    mysqli_query($conn, $deletePosts);

    
    $deleteForum = "DELETE FROM forums WHERE forum_id = $forum_id";
    mysqli_query($conn, $deleteForum);

header("Location: ../../views/detail_kursus.php?course_id=$course_id&tab=forum");
    exit;
} else {
    echo "Forum ID tidak ditemukan.";
}
?>
