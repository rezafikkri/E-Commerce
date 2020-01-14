-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2019 at 01:49 PM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.3

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- SET AUTOCOMMIT = 0;
-- START TRANSACTION;
-- SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: 
--
CREATE DATABASE `flowershop`;
USE `flowershop`;
-- --------------------------------------------------------

--
-- Table structure for table `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `kategori_id` varchar(36) NOT NULL,
  `nama_kategori` varchar(43) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kategori_produk`
--

INSERT INTO `kategori_produk` (`kategori_id`, `nama_kategori`) VALUES
('1c5be3c0-7a2c-4837-8131-39a08a87935e', 'lili'),
('9bc45bda-7a27-42b5-bfef-ca1ab21e3a1f', 'Anggrekk'),
('f5553102-3fc8-4f41-8cbc-c2658270a6d5', 'mawar');

-- --------------------------------------------------------

--
-- Table structure for table `pemesanan_barang`
--

CREATE TABLE `pemesanan_barang` (
  `pemesanan_id` varchar(36) NOT NULL,
  `produk_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `qty` int(11) NOT NULL,
  `country` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `subdistrict` varchar(50) NOT NULL,
  `village` varchar(50) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `status` enum('ready','sent','accepted') NOT NULL,
  `post` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pemesanan_barang`
--

INSERT INTO `pemesanan_barang` (`pemesanan_id`, `produk_id`, `user_id`, `qty`, `country`, `city`, `subdistrict`, `village`, `zip_code`, `status`, `post`) VALUES
('19b97736-c576-4708-b7c4-1a852a770713', '43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 2, 'indonesia', 'bkl', 'Ujan Mas', 'Daspetah II', '3535', 'ready', 1561041771),
('715f3e3b-a1f0-4724-b1b6-2847410c25dc', '21b7efb8-2eac-41a8-91a9-313ed3171cb0', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 3, 'indonesia', 'bkl', 'Ujan Mas', 'Daspetah II', '3535', 'ready', 1562020108),
('7dc2aa02-79d5-4e11-ac3f-656e8a027392', 'cbb0d014-3820-4a93-b9c2-908bb888ba10', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 1, 'indonesia', 'bkl', 'Ujan Mas', 'Daspetah II', '3535', 'ready', 1558522299),
('cfffcae5-429f-4ddf-be49-c87ce041f56c', '43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', 'ge2d4bh45', 1, 'indonesia', 'Bengkulu', 'Ujan mas', 'Suro ilir', '3721', 'ready', 1561040243),
('e3a1e5b2-b672-417e-ba6a-32bd2475c0a8', '967ad520-4638-4580-881c-d1a1517c3128', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 3, 'indonesia', 'bkl', 'Ujan Mas', 'Daspetah II', '3535', 'sent', 1561041796),
('f41e127b-9e39-4f42-99b0-f12ca7f2e0ba', '43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 1, 'indonesia', 'bkl', 'Ujan Mas', 'Daspetah II', '3535', 'accepted', 1561216650);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_id` varchar(36) NOT NULL,
  `kategori_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `nama_produk` varchar(32) NOT NULL,
  `url_img` varchar(100) NOT NULL,
  `url_img1` varchar(100) DEFAULT NULL,
  `url_img2` varchar(100) DEFAULT NULL,
  `url_img3` varchar(100) DEFAULT NULL,
  `harga` int(11) NOT NULL,
  `infoProduk` text,
  `ulasanProduk` text,
  `jml_stock` int(11) NOT NULL,
  `sale` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`produk_id`, `kategori_id`, `user_id`, `nama_produk`, `url_img`, `url_img1`, `url_img2`, `url_img3`, `harga`, `infoProduk`, `ulasanProduk`, `jml_stock`, `sale`) VALUES
('21b7efb8-2eac-41a8-91a9-313ed3171cb0', 'f5553102-3fc8-4f41-8cbc-c2658270a6d5', '325c7c599fb57ba ', 'Mawar love', 'http://localhost/flowershop/assets/img/produk/mawar love.jpg', 'http://localhost/flowershop/assets/img/produk/mawar dor.png', '', '', 123000, '', '', 9, 3),
('43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', '1c5be3c0-7a2c-4837-8131-39a08a87935e', '325c7c599fb57ba ', 'Bunga Daun Hijau - lili', 'http://localhost/flowershop/assets/img/produk/11.jpg', 'http://localhost/flowershop/assets/img/produk/lili cilinder.jpg', NULL, NULL, 20000, 'no', 'no', 55, 4),
('4868c4a4-c0f3-4b71-a969-b9521dcedf1f', '1c5be3c0-7a2c-4837-8131-39a08a87935e', '325c7c599fb57ba ', 'rew', 'we', '', '', '', 12, '', '', 12, 0),
('967ad520-4638-4580-881c-d1a1517c3128', 'f5553102-3fc8-4f41-8cbc-c2658270a6d5', '325c7c599fb57ba ', 'Mawar Kelopak Putih', 'http://localhost/flowershop/assets/img/produk/3.jpg', 'http://localhost/flowershop/assets/img/produk/mawar annie.jpg', 'http://localhost/flowershop/assets/img/produk/mawar biru.jpg', NULL, 150000, 'persediaan masih banyak', 'no', 22, 3),
('cbb0d014-3820-4a93-b9c2-908bb888ba10', '9bc45bda-7a27-42b5-bfef-ca1ab21e3a1f', '325c7c599fb57ba ', 'Anggrek Manis', 'http://localhost/flowershop/assets/img/produk/5.jpg', NULL, NULL, NULL, 30000, '', '', 19, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `transaksi_id` varchar(36) NOT NULL,
  `produk_id` varchar(36) NOT NULL,
  `user_id` varchar(36) NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('ordered','paid') NOT NULL,
  `post` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`transaksi_id`, `produk_id`, `user_id`, `qty`, `status`, `post`) VALUES
('165a2f22-efb9-491b-bf92-6ad6093fdd0c', '43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', 'ge2d4bh45', 1, 'paid', 1561040231),
('4058187f-b12f-4bf7-a4fa-c2ff26beef2f', '967ad520-4638-4580-881c-d1a1517c3128', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 3, 'paid', 1561041792),
('75f892c6-cc20-4bc2-82f5-75dc3a61300a', '43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 2, 'paid', 1561041675),
('88b7cc2a-ec5e-47a7-996c-d5fbd3c91224', '43f8a637-e5c9-4e9a-ae66-1f380cc93f4c', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 1, 'paid', 1561216646),
('ed495122-e4d3-4c55-9cd2-1fad39875963', 'cbb0d014-3820-4a93-b9c2-908bb888ba10', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 1, 'paid', 1561041688),
('f662b81a-0cf8-4f75-af57-5d0619735990', '21b7efb8-2eac-41a8-91a9-313ed3171cb0', '89c5c7bd-1df0-4aa0-8506-5c5002c79982', 3, 'paid', 1561930796);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` varchar(36) NOT NULL,
  `full_name` varchar(32) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(96) NOT NULL,
  `level` enum('user','admin') NOT NULL,
  `whatsapp` varchar(23) NOT NULL,
  `email` varchar(50) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `subdistrict` varchar(50) DEFAULT NULL,
  `village` varchar(50) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `full_name`, `username`, `password`, `level`, `whatsapp`, `email`, `country`, `city`, `subdistrict`, `village`, `zip_code`) VALUES
('325c7c599fb57ba ', 'Reza Sariful Fikri', 'reza', '$argon2i$v=19$m=1024,t=2,p=2$Z1VJdDFTeXQ5WThFWllDaA$8F1X/3OarAvmpuh6HMIf263ddsTtSN6KT5bW8FdJo+A', 'admin', '+620808', 'fikkri.reza@gmail.com', 'Indonesia', 'Bengkulu', '', '', ''),
('89c5c7bd-1df0-4aa0-8506-5c5002c79982', 'dian', 'dian', '$argon2i$v=19$m=1024,t=2,p=2$TzFsTkVheTBTNmk3TENWUA$CUvAH3GejGTsRPNFzTkv3ApYLsQE/OsXP5CmDvk0a9A', 'user', '+45', 're@g.co', NULL, '', '', '', ''),
('ge2d4bh45', 'Adelina damayanti', 'dea', '$2y$10$5j.VI0x1zV6SyEk8lhD2p.CTN3fzdyFjgu1xDw3BGre8VKSwPNPEu', 'user', '+64', 'dea@gmail.com', 'Indonesia', 'Bengkulu', '', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indexes for table `pemesanan_barang`
--
ALTER TABLE `pemesanan_barang`
  ADD PRIMARY KEY (`pemesanan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`transaksi_id`),
  ADD KEY `produk_id` (`produk_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pemesanan_barang`
--
ALTER TABLE `pemesanan_barang`
  ADD CONSTRAINT `pemesanan_barang_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pemesanan_barang_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_produk` (`kategori_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`produk_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
