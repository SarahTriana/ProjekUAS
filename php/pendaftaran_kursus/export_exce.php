<?php
include '../../database/koneksi.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=pendaftaran_kursus.xls");

echo "<table border='1'>";
echo "<tr>
<th>Nama Siswa</th>
<th>Kursus</th>
<th>Hari</th>
<th>Tanggal</th>
<th>Tanggal Daftar</th>
<th>Status</th>
</tr>";

$query = "
  SELECT enrollments.*, users.nama_lengkap, courses.nama_kursus, schedules.hari_pelaksanaan,
         schedules.tanggal_mulai, schedules.tanggal_selesai
  FROM enrollments
  JOIN users ON enrollments.student_id = users.user_id
  JOIN schedules ON enrollments.schedule_id = schedules.schedule_id
  JOIN courses ON schedules.course_id = courses.course_id
";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
    <td>{$row['nama_lengkap']}</td>
    <td>{$row['nama_kursus']}</td>
    <td>{$row['hari_pelaksanaan']}</td>
    <td>".date('d-m-Y', strtotime($row['tanggal_mulai']))." - ".date('d-m-Y', strtotime($row['tanggal_selesai']))."</td>
    <td>".date('d-m-Y H:i', strtotime($row['tanggal_daftar']))."</td>
    <td>{$row['status_pendaftaran']}</td>
    </tr>";
}
echo "</table>";
?>
