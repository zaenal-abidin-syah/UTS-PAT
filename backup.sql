/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.11-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: bukuDatabase
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `anggota`
--

DROP TABLE IF EXISTS `anggota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `anggota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_daftar` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anggota`
--

LOCK TABLES `anggota` WRITE;
/*!40000 ALTER TABLE `anggota` DISABLE KEYS */;
INSERT INTO `anggota` VALUES
(1,'Ahmad Fauzi','ahmad.fauzi@example.com','Jl. Merpati No. 12, Jakarta','2025-01-05'),
(2,'Siti Aminah','siti.aminah@example.com','Jl. Mangga Dua No. 34, Jakarta','2025-02-10'),
(3,'Budi Santoso','budi.santoso@example.com','Jl. Melati No. 7, Bandung','2025-01-20'),
(4,'Dewi Lestari','dewi.lestari@example.com','Jl. Cemara No. 89, Surabaya','2025-03-01'),
(5,'Eko Prasetyo','eko.prasetyo@example.com','Jl. Kenanga No. 5, Yogyakarta','2025-02-18'),
(6,'Fitri Handayani','fitri.handayani@example.com','Jl. Anggrek No. 21, Semarang','2025-01-30'),
(7,'Gilang Ramadhan','gilang.ramadhan@example.com','Jl. Dahlia No. 14, Medan','2025-03-05'),
(8,'Hendra Wijaya','hendra.wijaya@example.com','Jl. Flamboyan No. 3, Makassar','2025-02-25'),
(9,'Indah Permata','indah.permata@example.com','Jl. Kenari No. 66, Palembang','2025-01-15'),
(10,'Joko Susilo','joko.susilo@example.com','Jl. Angkasa No. 9, Balikpapan','2025-03-10');
/*!40000 ALTER TABLE `anggota` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buku`
--

DROP TABLE IF EXISTS `buku`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `buku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(255) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `tahun_terbit` int(11) DEFAULT NULL,
  `jumlah_stok` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buku`
--

LOCK TABLES `buku` WRITE;
/*!40000 ALTER TABLE `buku` DISABLE KEYS */;
INSERT INTO `buku` VALUES
(1,'Laskar Pelangi','Andrea Hirata','9786020243808',2005,5),
(2,'Bumi Manusia','Pramoedya Ananta Toer','9786024201171',1980,3),
(3,'Negeri 5 Menara','Ahmad Fuadi','9786020240210',2009,4),
(4,'Harry Potter and the Philosopher\'s Stone','J.K. Rowling','9780747532699',1997,6),
(5,'Atomic Habits','James Clear','9780735211292',2018,2),
(6,'The Alchemist','Paulo Coelho','9780061122415',1988,7),
(7,'Sapiens: A Brief History of Humankind','Yuval Noah Harari','9780099590088',2011,5),
(8,'Rich Dad Poor Dad','Robert T. Kiyosaki','9781612680194',1997,4),
(9,'Thinking, Fast and Slow','Daniel Kahneman','9780374533557',2011,3),
(10,'To Kill a Mockingbird','Harper Lee','9780060935467',1960,2);
/*!40000 ALTER TABLE `buku` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman`
--

DROP TABLE IF EXISTS `peminjaman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buku_id` int(11) NOT NULL,
  `anggota_id` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','dikembalikan') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_peminjaman_buku` (`buku_id`),
  KEY `idx_peminjaman_anggota` (`anggota_id`),
  CONSTRAINT `fk_peminjaman_anggota` FOREIGN KEY (`anggota_id`) REFERENCES `anggota` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_peminjaman_buku` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman`
--

LOCK TABLES `peminjaman` WRITE;
/*!40000 ALTER TABLE `peminjaman` DISABLE KEYS */;
INSERT INTO `peminjaman` VALUES
(1,1,2,'2025-04-01','2025-04-10','dikembalikan'),
(2,3,5,'2025-04-03',NULL,'dipinjam'),
(3,2,1,'2025-03-20','2025-03-27','dikembalikan'),
(4,4,3,'2025-04-05',NULL,'dipinjam'),
(5,6,4,'2025-02-15','2025-02-25','dikembalikan'),
(6,7,7,'2025-03-30',NULL,'dipinjam'),
(7,5,8,'2025-01-10','2025-01-20','dikembalikan'),
(8,8,6,'2025-04-10',NULL,'dipinjam'),
(9,9,9,'2025-02-05','2025-02-15','dikembalikan'),
(10,10,10,'2025-03-01','2025-03-10','dikembalikan');
/*!40000 ALTER TABLE `peminjaman` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-21 14:06:02
