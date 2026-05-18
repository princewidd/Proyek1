-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 08, 2026 at 02:15 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kedai_koffee_pink`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `ID_Detail` int NOT NULL,
  `ID_Pesanan` int NOT NULL,
  `ID_Menu` int NOT NULL,
  `Qty` int NOT NULL,
  `Harga_satuan` decimal(10,2) NOT NULL,
  `Sub_total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`ID_Detail`, `ID_Pesanan`, `ID_Menu`, `Qty`, `Harga_satuan`, `Sub_total`) VALUES
(1, 1, 2, 1, '8000.00', '8000.00'),
(2, 1, 5, 1, '10000.00', '10000.00'),
(3, 1, 6, 1, '15000.00', '15000.00'),
(4, 2, 7, 1, '15000.00', '15000.00'),
(5, 3, 8, 2, '17000.00', '34000.00'),
(6, 3, 18, 1, '16000.00', '16000.00');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `ID_Menu` int NOT NULL,
  `Nama_menu` varchar(100) NOT NULL,
  `Kategori` varchar(50) DEFAULT NULL,
  `Harga` decimal(10,2) NOT NULL,
  `Stok` tinyint(1) NOT NULL DEFAULT '1',
  `Gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`ID_Menu`, `Nama_menu`, `Kategori`, `Harga`, `Stok`, `Gambar`) VALUES
(1, 'Burger Beef', 'Burger', '5000.00', 1, NULL),
(2, 'Burger Daging', 'Burger', '8000.00', 1, NULL),
(3, 'Kebab Kecil', 'Kebab', '5000.00', 1, NULL),
(4, 'Kebab Sedang', 'Kebab', '8000.00', 1, NULL),
(5, 'Kebab Besar', 'Kebab', '10000.00', 1, NULL),
(6, 'Kebab Spesial', 'Kebab', '15000.00', 1, NULL),
(7, 'Kebab Super Jumbo', 'Kebab', '17000.00', 1, NULL),
(8, 'Corndog Original', 'Corndog', '5000.00', 1, NULL),
(9, 'Corndog + Toping', 'Corndog', '7000.00', 1, NULL),
(10, 'Lumpia Telur', 'Lainnya', '5000.00', 1, NULL),
(11, 'Kentang Tornado', 'Kentang Tornado', '5000.00', 1, NULL),
(12, 'Roti Bakar Strawberi', 'Roti Bakar', '14000.00', 1, NULL),
(13, 'Roti Bakar Blueberi', 'Roti Bakar', '14000.00', 1, NULL),
(14, 'Roti Bakar Nanas', 'Roti Bakar', '14000.00', 1, NULL),
(15, 'Roti Bakar Coklat', 'Roti Bakar', '16000.00', 1, NULL),
(16, 'Roti Bakar Kacang', 'Roti Bakar', '16000.00', 1, NULL),
(17, 'Roti Bakar Choco Cruncy', 'Roti Bakar', '22000.00', 1, NULL),
(18, 'Roti Bakar Tiramisu Cruncy', 'Roti Bakar', '22000.00', 1, NULL),
(19, 'Roti Bakar Blueberi Zam', 'Roti Bakar', '22000.00', 1, NULL),
(20, 'Roti Bakar Strawberi Zam', 'Roti Bakar', '22000.00', 1, NULL),
(21, 'Roti Bakar Chesse Cruncy', 'Roti Bakar', '24000.00', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `ID_Pelanggan` int NOT NULL,
  `Nama` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`ID_Pelanggan`, `Nama`) VALUES
(1, 'Widhi Saputra'),
(2, 'Fakhri'),
(3, 'Dijeh');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `ID_Pesanan` int NOT NULL,
  `ID_Pelanggan` int NOT NULL,
  `Tanggal` date NOT NULL,
  `Total_harga` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`ID_Pesanan`, `ID_Pelanggan`, `Tanggal`, `Total_harga`) VALUES
(1, 1, '2026-02-24', '33000.00'),
(2, 2, '2026-02-24', '15000.00'),
(3, 3, '2026-02-25', '50000.00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`ID_Detail`),
  ADD KEY `ID_Pesanan` (`ID_Pesanan`),
  ADD KEY `ID_Menu` (`ID_Menu`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`ID_Menu`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`ID_Pelanggan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`ID_Pesanan`),
  ADD KEY `ID_Pelanggan` (`ID_Pelanggan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `ID_Detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `ID_Menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `ID_Pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `ID_Pesanan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`ID_Pesanan`) REFERENCES `pesanan` (`ID_Pesanan`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`ID_Menu`) REFERENCES `menu` (`ID_Menu`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`ID_Pelanggan`) REFERENCES `pelanggan` (`ID_Pelanggan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
