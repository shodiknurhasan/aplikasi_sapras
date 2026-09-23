-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 12:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pengaduan_sarana`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `nama`, `password`, `aktif`) VALUES
('admin_sarpras', 'Admin Sarpras', '$2y$10$Ym3XWHveXIdmGwEI87IrrOsXUh9/EcVhjK9IbdQ/PXGGzjC9yLE5u', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT '',
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `aktif`) VALUES
(1, 'Fasilitas Kelas', 'Kerusakan fasilitas yang berada di ruang kelas.', 1),
(2, 'Laboratorium Komputer', 'Kerusakan komputer dan perangkat laboratorium.', 1),
(3, 'Sanitasi & Toilet', 'Kerusakan toilet, kran, saluran air, dan sanitasi.', 1),
(4, 'Lapangan & Olahraga', 'Kerusakan fasilitas lapangan dan peralatan olahraga.', 1),
(5, 'Listrik & Elektronik', 'Kerusakan listrik dan perangkat elektronik.', 1),
(6, 'Jaringan & Internet', 'Masalah jaringan internet, Wi-Fi, dan kabel LAN.', 1),
(7, 'Meja & Kursi', 'Kerusakan meja, kursi, dan furnitur sekolah.', 1),
(8, 'wc', 'wc sekolah', 1);

-- --------------------------------------------------------

--
-- Table structure for table `penanganan`
--

CREATE TABLE `penanganan` (
  `id_penanganan` int(11) NOT NULL,
  `id_pengaduan` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `status` varchar(30) NOT NULL,
  `feedback` varchar(255) DEFAULT '',
  `catatan` varchar(255) DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penanganan`
--

INSERT INTO `penanganan` (`id_penanganan`, `id_pengaduan`, `username`, `status`, `feedback`, `catatan`, `created_at`) VALUES
(1, 1, 'admin_sarpras', 'Diverifikasi', 'Laporan diverifikasi.', '', '2026-08-01 12:00:00'),
(2, 1, 'admin_sarpras', 'Diproses', 'Teknisi sedang mengganti kabel LAN.', '', '2026-08-02 09:00:00'),
(3, 1, 'admin_sarpras', 'Selesai', 'Kabel LAN sudah diganti dan diuji.', '', '2026-08-03 10:00:00'),
(4, 2, 'admin_sarpras', 'Diverifikasi', 'Laporan diverifikasi.', '', '2026-08-05 14:00:00'),
(5, 2, 'admin_sarpras', 'Diproses', 'Menunggu sparepart kipas baru.', '', '2026-08-06 09:00:00'),
(6, 4, 'admin_sarpras', 'Diverifikasi', 'Laporan diverifikasi.', '', '2026-08-10 10:00:00'),
(7, 4, 'admin_sarpras', 'Diproses', 'Teknisi memperbaiki kran.', '', '2026-08-11 10:00:00'),
(8, 4, 'admin_sarpras', 'Selesai', 'Kran sudah diganti baru.', '', '2026-08-12 10:00:00'),
(9, 5, 'admin_sarpras', 'Diverifikasi', 'Laporan diverifikasi.', '', '2026-08-21 08:00:00'),
(10, 5, 'admin_sarpras', 'Diproses', 'Perbaikan sedang dilakukan.', '', '2026-08-26 09:00:00'),
(11, 6, 'admin_sarpras', 'Selesai', 'sedang dalam perbaikan', 'otw', '2026-09-15 18:01:18'),
(12, 5, 'admin_sarpras', 'Selesai', '', '', '2026-09-15 18:01:42'),
(13, 7, 'admin_sarpras', 'Diproses', 'perbaikan sedang di lakuka', 'tuggu saja kabarnya', '2026-09-15 18:04:53'),
(14, 8, 'admin_sarpras', 'Diproses', 'sedang dalam perbaikan', 'tunggu aja tanggal selesainya', '2026-09-15 20:42:47'),
(15, 8, 'admin_sarpras', 'Selesai', '', '', '2026-09-15 21:14:22'),
(16, 10, 'admin_sarpras', 'Selesai', '', '', '2026-09-16 06:00:49'),
(17, 11, 'admin_sarpras', 'Diproses', 'otw', '', '2026-09-16 06:13:08'),
(18, 12, 'admin_sarpras', 'Diproses', 'sedang dalam proses', 'thnggu saja', '2026-09-16 11:45:07'),
(19, 9, 'admin_sarpras', 'Selesai', 'terdeteksi ai', '', '2026-09-18 10:03:20'),
(20, 9, 'admin_sarpras', 'Selesai', '', '', '2026-09-18 10:03:24'),
(21, 15, 'admin_sarpras', 'Diproses', 'lagi di proses', 'tunggu saja tanggal mainnya', '2026-09-21 08:49:18'),
(22, 14, 'admin_sarpras', 'Selesai', 'selesai', 'mantap lah', '2026-09-23 16:13:46');

-- --------------------------------------------------------

--
-- Table structure for table `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id_pengaduan` int(11) NOT NULL,
  `nis` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `lokasi` varchar(150) NOT NULL,
  `keterangan` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('Menunggu','Diverifikasi','Diproses','Selesai','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaduan`
--

INSERT INTO `pengaduan` (`id_pengaduan`, `nis`, `id_kategori`, `lokasi`, `keterangan`, `foto`, `status`, `created_at`) VALUES
(1, 1234567, 2, 'Lab Komputer 3', 'Kabel LAN PC-05 putus, komputer tidak bisa akses internet.', NULL, 'Selesai', '2026-08-01 09:00:00'),
(2, 22230102, 1, 'Ruang XII RPL 2', 'Kipas kelas mati, tidak berputar sama sekali.', NULL, 'Diproses', '2026-08-05 10:00:00'),
(3, 1234567, 5, 'Kantin Sekolah', 'Stopkontak dekat meja utama longgar dan mengeluarkan percikan api.', NULL, 'Menunggu', '2026-08-20 13:00:00'),
(4, 22230201, 3, 'Toilet Lantai 2', 'Kran air toilet bocor terus menerus.', NULL, 'Selesai', '2026-08-10 08:30:00'),
(5, 22230301, 4, 'Lapangan Basket', 'Papan pantul ring basket sebelah utara retak dan membahayakan.', NULL, 'Selesai', '2026-08-15 11:00:00'),
(6, 1234567, 5, 'Ruang Listrik', 'Panel listrik berdengung dan sedikit berasap.', NULL, 'Selesai', '2026-08-22 07:45:00'),
(7, 22230102, 1, 'XII rpl 4', 'anunya rusak', NULL, 'Diproses', '2026-09-15 18:03:12'),
(8, 22230501, 8, 'wc depan mushola', 'ruksak toiletnya jadi aku gabisa buang air', 'pengaduan_1789479718_8600.jpeg', 'Selesai', '2026-09-15 20:41:58'),
(9, 22230501, 6, 'XII rpl 3', 'jaringan internet lambat', NULL, 'Selesai', '2026-09-15 21:11:07'),
(10, 22230501, 3, 'XII rpl 3', 'jelek dan sangat bau tolong ubah', NULL, 'Selesai', '2026-09-16 05:59:56'),
(11, 22230501, 1, 'kipas rusak ga layak untuk di pakai', 'gaenakennnnnnnnn', NULL, 'Diproses', '2026-09-16 06:12:11'),
(12, 1038981238, 6, 'DI KELAS', 'NGELEG', 'pengaduan_1789533873_5586.png', 'Diproses', '2026-09-16 11:44:33'),
(13, 22230501, 1, 'XII rpl 4', ',,,,', NULL, 'Menunggu', '2026-09-18 10:04:09'),
(14, 22230501, 8, 'wc depan mushola', 'sama iman ramdhani', NULL, 'Selesai', '2026-09-21 08:36:20'),
(15, 22230401, 4, 'lapangan sekolah', 'lapangan cat nya udah pudar what the falkkkkkk', NULL, 'Diproses', '2026-09-21 08:48:35');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `nis` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `username`, `password`, `aktif`) VALUES
(1111233, 'faikal arsyad', 'XII TKJ 1', 'faikal', '$2y$10$QFuHLnkpYa1Xe86gQViVIOo1OKrVFTF7xz1A6JajH0LEdF7fxCKBW', 1),
(1234567, 'Budi Santoso', 'XII MPLB 1', '11_budi', '$2y$10$jgvD9xYtVMatsgWpP//PSekJOzBEesX.LGRKKNegJr3SEllPjM3g.', 1),
(22230102, 'Andi Pratama', 'XII RPL 2', 'siswa_andi', '$2y$10$jgvD9xYtVMatsgWpP//PSekJOzBEesX.LGRKKNegJr3SEllPjM3g.', 1),
(22230201, 'Rizky Ramadhan', 'XII TKJ 1', 'siswa_rizky', '$2y$10$jgvD9xYtVMatsgWpP//PSekJOzBEesX.LGRKKNegJr3SEllPjM3g.', 1),
(22230301, 'Dinda Putri', 'XII DKV 1', 'siswa_dinda', '$2y$10$jgvD9xYtVMatsgWpP//PSekJOzBEesX.LGRKKNegJr3SEllPjM3g.', 1),
(22230401, 'Fajar Nugraha', 'XI RPL 1', 'siswa_fajar', '$2y$10$jgvD9xYtVMatsgWpP//PSekJOzBEesX.LGRKKNegJr3SEllPjM3g.', 1),
(22230501, 'shodik', 'XII RPL 4', 'sodik', '$2y$10$yNp.becBPMgx2CHnas2wceGxBk/R1KYdahIiL.r6OoF3PyXuTW58W', 1),
(1038981238, 'aa rida', 'XII RPL 4', 'rida', '$2y$10$wz3JDQxtJeWQ.ADOPUEZVeLOwU3dxJQ7aLcw4oS3oqIR8wffuDwGW', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `penanganan`
--
ALTER TABLE `penanganan`
  ADD PRIMARY KEY (`id_penanganan`),
  ADD KEY `id_pengaduan` (`id_pengaduan`);

--
-- Indexes for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id_pengaduan`),
  ADD KEY `id_kategori` (`id_kategori`),
  ADD KEY `pengaduan_ibfk_1` (`nis`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `penanganan`
--
ALTER TABLE `penanganan`
  MODIFY `id_penanganan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id_pengaduan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penanganan`
--
ALTER TABLE `penanganan`
  ADD CONSTRAINT `penanganan_ibfk_1` FOREIGN KEY (`id_pengaduan`) REFERENCES `pengaduan` (`id_pengaduan`);

--
-- Constraints for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pengaduan_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
