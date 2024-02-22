-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 22 Feb 2024 pada 08.05
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ftsabsen-new`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `meeting`
--

CREATE TABLE `meeting` (
  `id` int(11) NOT NULL,
  `nm_pengundang` varchar(255) DEFAULT NULL,
  `nm_kegiatan` text DEFAULT NULL,
  `ruangan` varchar(100) DEFAULT NULL,
  `bukti_foto` text DEFAULT NULL,
  `pertemuan` varchar(255) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `waktu` time DEFAULT NULL,
  `notulen` text DEFAULT NULL,
  `status_ruangan` int(11) NOT NULL COMMENT '1: online, 2:hybrid, 0:offline',
  `qrcode` text DEFAULT NULL,
  `token` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `meeting`
--

INSERT INTO `meeting` (`id`, `nm_pengundang`, `nm_kegiatan`, `ruangan`, `bukti_foto`, `pertemuan`, `tanggal`, `waktu`, `notulen`, `status_ruangan`, `qrcode`, `token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'aku ido', 'MARAWIS', 'REGULER_D', '_bc3330d3-9886-4695-a68e-6243591c30e1.jpeg', '1', NULL, NULL, NULL, 0, NULL, '220006', '2024-02-17 05:42:14', '2024-02-17 12:42:14', NULL);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `meeting`
--
ALTER TABLE `meeting`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `meeting`
--
ALTER TABLE `meeting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
