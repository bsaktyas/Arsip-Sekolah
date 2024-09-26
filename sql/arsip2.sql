-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2024 at 12:30 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arsip2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `email`, `username`, `password`, `photo`) VALUES
(6, 'bima', 'bima@gmail.com', 'bimas', '$2y$10$g1UDA3VeLR6Na4GOnFj0NegYpBB56Ak.OlQzkN4a4NHaS6MeLcnSO', '1727346600dwdw2.png'),
(10, 'dwa', 'bima.sakti278@gmail.com', 'ada', '$2y$10$DMoKJ4DxTXThfX8Qvkm5qurPGIHmeWAvXe7THgV4erWFeT6Px5KLC', '1727346399840723_1200.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_surat`
--

CREATE TABLE `jenis_surat` (
  `id` smallint(6) NOT NULL,
  `jenis` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `jenis_surat`
--

INSERT INTO `jenis_surat` (`id`, `jenis`) VALUES
(1, 'oyisam');

-- --------------------------------------------------------

--
-- Table structure for table `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id` bigint(20) NOT NULL,
  `no_surat` varchar(255) DEFAULT NULL,
  `perihal` varchar(255) DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `kepada` varchar(255) DEFAULT NULL,
  `dari` varchar(255) DEFAULT NULL,
  `id_jenis` smallint(6) DEFAULT NULL,
  `keterangan` text DEFAULT 'Surat Keluar',
  `tgl_surat` timestamp NOT NULL DEFAULT current_timestamp(),
  `isi_surat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id` bigint(20) NOT NULL,
  `no_surat` varchar(255) DEFAULT NULL,
  `perihal` varchar(255) DEFAULT NULL,
  `lampiran` varchar(255) DEFAULT NULL,
  `kepada` varchar(255) DEFAULT NULL,
  `dari` varchar(255) DEFAULT NULL,
  `id_jenis` smallint(6) DEFAULT NULL,
  `keterangan` text DEFAULT 'Surat Masuk',
  `tgl_surat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isi_surat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `surat_masuk`
--

INSERT INTO `surat_masuk` (`id`, `no_surat`, `perihal`, `lampiran`, `kepada`, `dari`, `id_jenis`, `keterangan`, `tgl_surat`, `isi_surat`) VALUES
(20, 'dwa', 'awd', '1727341285pngtree-temple-clipart-cartoon-image-of-a-temple-in-a-forest-vector-png-image_6825218.png', '123', '123321', 1, 'Surat Masuk', '2021-06-18 17:00:00', '<p>dwadwa</p>'),
(21, 'dadwa', 'awdadsdas', '1727341308pngtree-temple-clipart-cartoon-image-of-a-temple-in-a-forest-vector-png-image_6825218.png', 'dada', 'dad', 1, 'Surat Masuk', '0000-00-00 00:00:00', '<p>adwda</p>');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenis_surat`
--
ALTER TABLE `jenis_surat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jenis_surat`
--
ALTER TABLE `jenis_surat`
  MODIFY `id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
