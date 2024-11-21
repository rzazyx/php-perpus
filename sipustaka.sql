-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Jun 2024 pada 14.45
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipustaka`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `anggota`
--

CREATE TABLE `anggota` (
  `kodeanggota` char(9) NOT NULL,
  `nisn` char(10) NOT NULL,
  `nama` varchar(63) NOT NULL,
  `jekel` enum('Pria','Wanita') NOT NULL,
  `alamat` text NOT NULL,
  `telepon` char(14) NOT NULL,
  `email` varchar(99) NOT NULL,
  `kelas` char(5) NOT NULL,
  `username` varchar(99) NOT NULL,
  `password` char(32) NOT NULL,
  `status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `atribut`
--

CREATE TABLE `atribut` (
  `kodeatribut` int(11) NOT NULL,
  `sampul` text NOT NULL,
  `bahasa` varchar(27) NOT NULL,
  `durasi` varchar(27) NOT NULL,
  `genre` varchar(27) NOT NULL,
  `volume` char(3) NOT NULL,
  `ns` text NOT NULL,
  `halaman` int(11) NOT NULL,
  `romawi` char(9) NOT NULL,
  `ilustrasi` int(11) NOT NULL,
  `file` text NOT NULL,
  `kodepustaka` char(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `denda`
--

CREATE TABLE `denda` (
  `kodedenda` int(11) NOT NULL,
  `tglpinjam` date NOT NULL,
  `tglbatas` date NOT NULL,
  `tglkembali` date NOT NULL,
  `telat` int(11) NOT NULL,
  `denda` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `kodetransaksi` char(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detailtransaksi`
--

CREATE TABLE `detailtransaksi` (
  `kodedetail` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subjek` varchar(63) NOT NULL,
  `status` enum('0','1','2','3','4','5','6','7') NOT NULL,
  `kodepustaka` char(9) NOT NULL,
  `kodetransaksi` char(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `infosistem`
--

CREATE TABLE `infosistem` (
  `nama` varchar(18) NOT NULL,
  `logo` text NOT NULL,
  `denda` int(11) NOT NULL,
  `kepsek` varchar(63) NOT NULL,
  `nipkepsek` char(27) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `infosistem`
--

INSERT INTO `infosistem` (`nama`, `logo`, `denda`, `kepsek`, `nipkepsek`) VALUES
('SIPUSTAKA', 'logo.png', 1000, 'Nama Kepala Sekolah', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `klasifikasi`
--

CREATE TABLE `klasifikasi` (
  `kodeklasifikasi` char(7) NOT NULL,
  `klasifikasi` varchar(180) NOT NULL,
  `tingkat` int(11) NOT NULL,
  `reff` char(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `klasifikasi`
--

INSERT INTO `klasifikasi` (`kodeklasifikasi`, `klasifikasi`, `tingkat`, `reff`) VALUES
('000', 'Komputer, Informasi dan Referensi Umum', 1, '0'),
('001', 'Ilmu pengetahuan umum', 2, '000'),
('001.1', 'Kehidupan intelektual', 2, '000'),
('001.4', 'Penelitian : metode-metode penelitian', 2, '000'),
('002', 'Buku', 2, '000'),
('003', 'Sistem-sistem', 2, '000'),
('100', 'Filsafat dan Psikologi', 1, '0'),
('101', 'Teori filsafat', 2, '100'),
('102', 'Aneka ragam filsafat', 2, '100'),
('200', 'Agama', 1, '0'),
('200.1', 'Filsafat dan teori agama', 2, '200'),
('200.5', 'Terbitan berseri, majalah agama', 2, '200'),
('300', 'Ilmu Sosial', 1, '0'),
('400', 'Bahasa', 1, '0'),
('400.1', 'Filsafat dan teori', 2, '400'),
('400.4', 'Topik-topik khusus', 2, '400'),
('401', 'Sistem-sistem tulisan', 2, '400'),
('500', 'Sains dan Matematika', 1, '0'),
('600', 'Teknologi', 1, '0'),
('700', 'Kesenian dan Rekreasi', 1, '0'),
('800', 'Sastra', 1, '0'),
('900', 'Sejarah dan Geografi', 1, '0');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kunjungan`
--

CREATE TABLE `kunjungan` (
  `kodekunjungan` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `nama` varchar(99) NOT NULL,
  `kelas` char(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengarang`
--

CREATE TABLE `pengarang` (
  `kodepengarang` int(11) NOT NULL,
  `depan` varchar(63) NOT NULL,
  `tengah` varchar(63) NOT NULL,
  `belakang` varchar(63) NOT NULL,
  `kodepustaka` char(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `petugas`
--

CREATE TABLE `petugas` (
  `kodepetugas` char(9) NOT NULL,
  `nip` char(18) NOT NULL,
  `nama` varchar(63) NOT NULL,
  `jekel` enum('Pria','Wanita') NOT NULL,
  `alamat` text NOT NULL,
  `telepon` char(14) NOT NULL,
  `level` enum('0','1') NOT NULL,
  `username` varchar(99) NOT NULL,
  `password` char(32) NOT NULL,
  `status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `petugas`
--

INSERT INTO `petugas` (`kodepetugas`, `nip`, `nama`, `jekel`, `alamat`, `telepon`, `level`, `username`, `password`, `status`) VALUES
('P2023.000', '', 'Nama Ketua Pengelola', 'Wanita', 'alamat lokasi perpustakaan', '', '0', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '1'),
('P2024.001', '', 'Nama Pustakawan', 'Wanita', 'alamat pustakawan', '', '1', 'operator', 'e10adc3949ba59abbe56e057f20f883e', '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pustaka`
--

CREATE TABLE `pustaka` (
  `kodepustaka` char(9) NOT NULL,
  `jenis` enum('buku','jurnal','majalah','koran','gambar','audio','video','lain') NOT NULL,
  `label` text NOT NULL,
  `judul` text NOT NULL,
  `penerbit` varchar(63) NOT NULL,
  `kota` varchar(36) NOT NULL,
  `bulan` char(9) NOT NULL,
  `tahun` char(4) NOT NULL,
  `eksemplar` int(11) NOT NULL,
  `baris` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `kodeklasifikasi` char(7) NOT NULL,
  `koderak` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rak`
--

CREATE TABLE `rak` (
  `koderak` int(11) NOT NULL,
  `rak` varchar(18) NOT NULL,
  `baris` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `rekap`
--

CREATE TABLE `rekap` (
  `koderekap` int(11) NOT NULL,
  `waktu` datetime NOT NULL,
  `jumlah` int(11) NOT NULL,
  `j0` int(11) NOT NULL,
  `j1` int(11) NOT NULL,
  `j2` int(11) NOT NULL,
  `j3` int(11) NOT NULL,
  `j4` int(11) NOT NULL,
  `j5` int(11) NOT NULL,
  `j6` int(11) NOT NULL,
  `j7` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `kodepustaka` char(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `kodetransaksi` char(18) NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `waktu` datetime NOT NULL,
  `keterangan` text NOT NULL,
  `status` enum('0','1') NOT NULL,
  `kodeanggota` char(9) NOT NULL,
  `kodepetugas` char(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`kodeanggota`);

--
-- Indeks untuk tabel `atribut`
--
ALTER TABLE `atribut`
  ADD PRIMARY KEY (`kodeatribut`);

--
-- Indeks untuk tabel `denda`
--
ALTER TABLE `denda`
  ADD PRIMARY KEY (`kodedenda`);

--
-- Indeks untuk tabel `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  ADD PRIMARY KEY (`kodedetail`);

--
-- Indeks untuk tabel `infosistem`
--
ALTER TABLE `infosistem`
  ADD PRIMARY KEY (`nama`);

--
-- Indeks untuk tabel `klasifikasi`
--
ALTER TABLE `klasifikasi`
  ADD PRIMARY KEY (`kodeklasifikasi`);

--
-- Indeks untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  ADD PRIMARY KEY (`kodekunjungan`);

--
-- Indeks untuk tabel `pengarang`
--
ALTER TABLE `pengarang`
  ADD PRIMARY KEY (`kodepengarang`);

--
-- Indeks untuk tabel `petugas`
--
ALTER TABLE `petugas`
  ADD PRIMARY KEY (`kodepetugas`);

--
-- Indeks untuk tabel `pustaka`
--
ALTER TABLE `pustaka`
  ADD PRIMARY KEY (`kodepustaka`);

--
-- Indeks untuk tabel `rak`
--
ALTER TABLE `rak`
  ADD PRIMARY KEY (`koderak`);

--
-- Indeks untuk tabel `rekap`
--
ALTER TABLE `rekap`
  ADD PRIMARY KEY (`koderekap`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`kodetransaksi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `atribut`
--
ALTER TABLE `atribut`
  MODIFY `kodeatribut` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `denda`
--
ALTER TABLE `denda`
  MODIFY `kodedenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detailtransaksi`
--
ALTER TABLE `detailtransaksi`
  MODIFY `kodedetail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kunjungan`
--
ALTER TABLE `kunjungan`
  MODIFY `kodekunjungan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengarang`
--
ALTER TABLE `pengarang`
  MODIFY `kodepengarang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rak`
--
ALTER TABLE `rak`
  MODIFY `koderak` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `rekap`
--
ALTER TABLE `rekap`
  MODIFY `koderekap` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
