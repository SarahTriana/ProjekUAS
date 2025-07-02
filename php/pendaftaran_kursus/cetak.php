<?php
include '../../database/koneksi.php';

$query = "
  SELECT enrollments.*, users.nama_lengkap, courses.nama_kursus, schedules.hari_pelaksanaan,
         schedules.tanggal_mulai, schedules.tanggal_selesai
  FROM enrollments
  JOIN users ON enrollments.student_id = users.user_id
  JOIN schedules ON enrollments.schedule_id = schedules.schedule_id
  JOIN courses ON schedules.course_id = courses.course_id
  ORDER BY enrollments.tanggal_daftar DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Cetak Pendaftaran Kursus</title>
  <style>
    body { font-family: Arial; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 8px; text-align: center; }
    th { background-color: #f2f2f2; }
    h2 { text-align: center; }
    @media print {
      .noprint { display: none; }
    }
  </style>
</head>
<body>

<h2>Laporan Pendaftaran Kursus</h2>

<table>
  <thead>
    <tr>
      <th>Nama Siswa</th>
      <th>Kursus</th>
      <th>Hari</th>
      <th>Tanggal</th>
      <th>Tanggal Daftar</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
    <tr>
      <td><?= $row['nama_lengkap'] ?></td>
      <td><?= $row['nama_kursus'] ?></td>
      <td><?= $row['hari_pelaksanaan'] ?></td>
      <td><?= date('d-m-Y', strtotime($row['tanggal_mulai'])) ?> - <?= date('d-m-Y', strtotime($row['tanggal_selesai'])) ?></td>
      <td><?= date('d-m-Y H:i', strtotime($row['tanggal_daftar'])) ?></td>
      <td><?= ucfirst($row['status_pendaftaran']) ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<br>
<div class="noprint" style="text-align: center;">
  <button onclick="window.print()">Cetak Sekarang</button>
</div>

</body>
</html>
