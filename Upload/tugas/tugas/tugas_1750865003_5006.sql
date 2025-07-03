-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 23, 2025 at 03:21 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_krususk`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int NOT NULL,
  `lesson_id` int DEFAULT NULL,
  `judul_tugas` varchar(255) DEFAULT NULL,
  `deskripsi_tugas` text,
  `tanggal_batas_akhir` datetime DEFAULT NULL,
  `poin_maksimal` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int NOT NULL,
  `enrollment_id` int DEFAULT NULL,
  `nomor_sertifikat` varchar(100) DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `nilai_akhir` decimal(4,2) DEFAULT NULL,
  `file_sertifikat_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`certificate_id`, `enrollment_id`, `nomor_sertifikat`, `tanggal_terbit`, `nilai_akhir`, `file_sertifikat_url`) VALUES
(9, 5, '1234', '2025-06-22', '85.00', 'sertifikat_1750602152.png'),
(11, 5, 'CERT-YYYY-888', '2025-06-23', '99.00', 'sertifikat_1750685654.png');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int NOT NULL,
  `nama_kursus` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  `durasi_jam` int DEFAULT NULL,
  `harga` decimal(10,2) DEFAULT NULL,
  `level` enum('Dasar','Menengah','Lanjut') DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `nama_kursus`, `deskripsi`, `durasi_jam`, `harga`, `level`, `status_aktif`) VALUES
(1, 'Belajar Html', 'Tes Saja', 2, '75000.00', 'Dasar', 1),
(3, 'Belajar CSS', 'Belajar Mengenal Syntax CSSS', 2, '50000.00', 'Dasar', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `schedule_id` int DEFAULT NULL,
  `tanggal_daftar` datetime DEFAULT NULL,
  `status_pendaftaran` enum('pending','diterima','ditolak','selesai','dibatalkan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `schedule_id`, `tanggal_daftar`, `status_pendaftaran`) VALUES
(1, 15, 3, '2025-06-20 15:16:18', 'diterima'),
(2, 10, 2, '2025-06-21 07:11:00', 'ditolak'),
(3, 10, 3, '2025-06-21 14:22:07', 'diterima'),
(4, 16, 3, '2025-06-22 12:52:09', 'dibatalkan'),
(5, 16, 2, '2025-06-22 12:53:55', 'selesai'),
(7, 16, 3, '2025-06-22 13:18:58', 'ditolak'),
(8, 16, 3, '2025-06-22 14:31:27', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `forumposts`
--

CREATE TABLE `forumposts` (
  `post_id` int NOT NULL,
  `forum_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `konten_post` text,
  `tanggal_post` datetime DEFAULT NULL,
  `parent_post_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forums`
--

CREATE TABLE `forums` (
  `forum_id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `judul_topik` varchar(255) DEFAULT NULL,
  `deskripsi_topik` text,
  `user_id_pembuat` int DEFAULT NULL,
  `tanggal_buat` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `instructor_id` int NOT NULL,
  `spesialisasi` varchar(255) DEFAULT NULL,
  `pengalaman_mengajar_tahun` int DEFAULT NULL,
  `rating_rata_rata` decimal(2,1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`instructor_id`, `spesialisasi`, `pengalaman_mengajar_tahun`, `rating_rata_rata`) VALUES
(3, 'Web Development ui/ux', 2, '0.0'),
(14, 'Frontend', 5, '1.0');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int NOT NULL,
  `module_id` int DEFAULT NULL,
  `nama_pelajaran` varchar(255) DEFAULT NULL,
  `konten_pelajaran` varchar(255) DEFAULT NULL,
  `tipe_konten` enum('teks','video','pdf','link') DEFAULT NULL,
  `durasi_menit` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `module_id`, `nama_pelajaran`, `konten_pelajaran`, `tipe_konten`, `durasi_menit`) VALUES
(7, 1, 'Membuat Desain', '../../uploads/pelajaran/1750239794_design.pdf', 'pdf', 0),
(10, 1, 'Membuat Desain', '1750243819_bandicam 2025-05-16 17-08-16-603.mp4', 'video', 10),
(12, 1, 'tes', '1750243599_Materi-Ujian-Bu-Ersya.txt', 'teks', 0);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `module_id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `nama_modul` varchar(255) DEFAULT NULL,
  `deskripsi_modul` text,
  `urutan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `course_id`, `nama_modul`, `deskripsi_modul`, `urutan`) VALUES
(1, 3, 'Belajar Framwork CSS', 'Belajar Dan Mendalami Framwork CSS', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `enrollment_id` int DEFAULT NULL,
  `jumlah_pembayaran` decimal(10,2) DEFAULT NULL,
  `tanggal_pembayaran` datetime DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status_pembayaran` enum('pending','sukses','gagal','dikembalikan') DEFAULT NULL,
  `kode_referensi_bank` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `enrollment_id`, `jumlah_pembayaran`, `tanggal_pembayaran`, `metode_pembayaran`, `status_pembayaran`, `kode_referensi_bank`) VALUES
(7, 1, '50000.00', '2025-06-21 20:27:00', 'Transfer', 'dikembalikan', 'WKK-1234AAD'),
(9, 5, '75000.00', '2025-06-22 12:54:50', 'Cash', 'sukses', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `komentar` text,
  `tanggal_review` datetime DEFAULT NULL,
  `tipe_review` enum('kursus','pengajar') DEFAULT NULL,
  `instructor_id` int DEFAULT NULL
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `student_id`, `course_id`, `rating`, `komentar`, `tanggal_review`, `tipe_review`, `instructor_id`) VALUES
(13, 16, 3, 4, 'keren', '2025-06-23 09:12:43', 'kursus', NULL),
(14, 15, 3, 3, 'bagus cuman ternya agak sulit ya', '2025-06-23 09:13:31', 'kursus', NULL),
(15, 16, 3, 1, 'GALAKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKKK', '2025-06-23 14:04:57', 'pengajar', 14);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `schedule_id` int NOT NULL,
  `course_id` int DEFAULT NULL,
  `instructor_id` int DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `waktu_selesai` time DEFAULT NULL,
  `hari_pelaksanaan` varchar(50) DEFAULT NULL,
  `kapasitas_maksimal` int DEFAULT NULL,
  `lokasi_kelas` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`schedule_id`, `course_id`, `instructor_id`, `tanggal_mulai`, `tanggal_selesai`, `waktu_mulai`, `waktu_selesai`, `hari_pelaksanaan`, `kapasitas_maksimal`, `lokasi_kelas`) VALUES
(2, 1, 3, '2025-06-20', '2025-06-20', '18:11:00', '18:11:00', 'senin - jumat', 20, 'online'),
(3, 3, 14, '2025-06-20', '2025-06-30', '19:04:00', '23:04:00', 'jumat - minggu', 20, 'online');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int NOT NULL,
  `pendidikan_terakhir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `pendidikan_terakhir`, `tanggal_lahir`) VALUES
(4, 'SMK', '2025-06-14'),
(10, 'Smk', '2025-06-15'),
(15, 'SMK', '2025-06-20'),
(16, 'SMA/SMK', '2025-06-01');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `submission_id` int NOT NULL,
  `assignment_id` int DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `tanggal_submit` datetime DEFAULT NULL,
  `file_submission_url` varchar(255) DEFAULT NULL,
  `nilai` decimal(4,2) DEFAULT NULL,
  `feedback_instructor` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text,
  `tanggal_registrasi` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `role` enum('siswa','pengajar','admin') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nama_lengkap`, `email`, `password_hash`, `telepon`, `alamat`, `tanggal_registrasi`, `last_login`, `role`) VALUES
(1, 'tes', 'a@gmail.com', '$2y$10$WkSQ2wjaAW9usHuJ.j3oXuyZ.VmVOobDhMVUUJSOm19ndlKJYoZIa', '098890098890', 'tes', '2025-06-14 00:17:18', NULL, 'siswa'),
(3, 'ahmad syaifudin', 'ahmad@gmail.com', '$2y$10$FL/bq35I5oVusghBKpjo3uaqb6C9m0kSyvx58wq/a04LPYZvmNEq2', '082230574355', 'tesss', '2025-06-14 00:31:40', '2025-06-23 21:08:48', 'pengajar'),
(4, 'Hindaka Pratama', 'admin@gmail.com', '$2y$10$mexKgX5b/0jf6Q1bJ/t8PejTtTURsYUEwLdwYYZMVf8fWHDm361Aa', '082230574355', 'Jakarta Pusat\r\n ', '2025-06-14 01:15:29', '2025-06-23 21:56:25', 'admin'),
(10, 'Ahmad Arjuna', 'Juna@gmail.com', '$2y$10$niiTzwbVqxDC.4Ug.YDn7eM1RX6DUnyaw4OnGWHCkLdQd0/2YcEqS', '07689098765', 'Bandung ', '2025-06-15 11:09:12', '2025-06-21 16:51:11', 'siswa'),
(14, 'zidan', 'zid@gmail.com', '$2y$10$G63t1GKuzMhTwMzsrNenp.emopS.IcLsGM88d4fXJPiFQG.GnHXbW', '07689098765', 'seban', '2025-06-20 12:03:48', '2025-06-23 21:54:50', 'pengajar'),
(15, 'zidan', 'zido@gmail.com', '$2y$10$1a/rGq9/he9DkzXRK0xL8ePMJ38qD9qXroK.EZkpRJT9auMsW35eu', '07689098765', 'seban', '2025-06-20 15:08:52', '2025-06-23 20:44:13', 'siswa'),
(16, 'Wahyu', 's@s', '$2y$10$XT.5hSvcW3v9xaOIishmG.VFsne9WvAwpzYu1i6YEWYNPSuvdi0wi', '07689098765', 's', '2025-06-22 05:51:44', '2025-06-23 21:35:01', 'siswa');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `nomor_sertifikat` (`nomor_sertifikat`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `forumposts`
--
ALTER TABLE `forumposts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `forum_id` (`forum_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_post_id` (`parent_post_id`);

--
-- Indexes for table `forums`
--
ALTER TABLE `forums`
  ADD PRIMARY KEY (`forum_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id_pembuat` (`user_id_pembuat`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`instructor_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `forumposts`
--
ALTER TABLE `forumposts`
  MODIFY `post_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forums`
--
ALTER TABLE `forums`
  MODIFY `forum_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `schedule_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `submission_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`);

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`schedule_id`);

--
-- Constraints for table `forumposts`
--
ALTER TABLE `forumposts`
  ADD CONSTRAINT `forumposts_ibfk_1` FOREIGN KEY (`forum_id`) REFERENCES `forums` (`forum_id`),
  ADD CONSTRAINT `forumposts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `forumposts_ibfk_3` FOREIGN KEY (`parent_post_id`) REFERENCES `forumposts` (`post_id`);

--
-- Constraints for table `forums`
--
ALTER TABLE `forums`
  ADD CONSTRAINT `forums_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `forums_ibfk_2` FOREIGN KEY (`user_id_pembuat`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `instructors_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`);

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`instructor_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`),
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
