-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for lab2
CREATE DATABASE IF NOT EXISTS `lab2` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `lab2`;

-- Dumping structure for table lab2.attendance
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `child_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'absent',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `late_reason` text COLLATE utf8mb4_unicode_ci,
  `checkin_time` time DEFAULT NULL,
  `checkout_time` time DEFAULT NULL,
  `drop_off_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_child_id_date_unique` (`child_id`,`date`),
  CONSTRAINT `attendance_child_id_foreign` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.attendance: ~6 rows (approximately)
DELETE FROM `attendance`;
INSERT INTO `attendance` (`id`, `child_id`, `parent_id`, `date`, `status`, `confirmed`, `confirmed_at`, `late_reason`, `checkin_time`, `checkout_time`, `drop_off_by`, `pickup_by`, `is_verified`, `notes`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, '2026-06-13', 'checkin', 0, NULL, NULL, '17:42:55', NULL, NULL, NULL, 0, NULL, '2026-06-13 09:42:55', '2026-06-13 09:42:55'),
	(3, 1, NULL, '2026-06-15', 'checkin', 0, NULL, NULL, '18:31:23', NULL, NULL, NULL, 0, NULL, '2026-06-15 10:31:23', '2026-06-15 10:31:23'),
	(4, 3, 3, '2026-07-17', 'present', 0, NULL, NULL, '00:27:43', NULL, 'Parent ID: 3', NULL, 1, NULL, '2026-07-16 08:27:43', '2026-07-16 08:27:43'),
	(5, 5, 3, '2026-07-17', 'late', 0, NULL, NULL, '02:04:19', NULL, 'Parent ID: 3', NULL, 1, NULL, '2026-07-16 10:04:19', '2026-07-16 10:04:19'),
	(8, 14, 8, '2026-07-17', 'present', 0, NULL, NULL, '18:47:26', NULL, 'Parent ID: 8', NULL, 1, NULL, '2026-07-17 02:47:26', '2026-07-17 02:47:26'),
	(9, 15, 8, '2026-07-17', 'present', 0, NULL, NULL, '18:47:26', NULL, 'Parent ID: 8', NULL, 1, NULL, '2026-07-17 02:47:26', '2026-07-17 02:47:26'),
	(10, 12, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '11:26:27', '11:36:51', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 19:26:27', '2026-07-18 19:36:51'),
	(11, 16, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '11:26:27', '11:36:51', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 19:26:27', '2026-07-18 19:36:51'),
	(12, 17, 1, '2026-07-19', 'checkout', 0, NULL, NULL, '11:43:49', '12:21:46', 'Parent ID: 1', 'Parent ID: 5', 1, NULL, '2026-07-18 19:43:49', '2026-07-18 20:21:46'),
	(13, 18, 1, '2026-07-19', 'checkout', 0, NULL, NULL, '12:35:18', '14:12:05', 'Parent ID: 1', 'Parent ID: 5', 1, NULL, '2026-07-18 20:35:18', '2026-07-18 22:12:05'),
	(14, 19, 1, '2026-07-19', 'checkout', 0, NULL, NULL, '12:37:26', '12:37:34', 'Parent ID: 1', 'Parent ID: 1', 1, NULL, '2026-07-18 20:37:26', '2026-07-18 20:37:34'),
	(15, 20, 1, '2026-07-19', 'checkout', 0, NULL, NULL, '12:43:03', '14:12:05', 'Parent ID: 1', 'Parent ID: 5', 1, NULL, '2026-07-18 20:43:03', '2026-07-18 22:12:05'),
	(16, 21, 1, '2026-07-19', 'checkout', 0, NULL, NULL, '12:45:55', '14:12:05', 'Parent ID: 1', 'Parent ID: 5', 1, NULL, '2026-07-18 20:45:55', '2026-07-18 22:12:05'),
	(17, 22, 1, '2026-07-19', 'checkout', 0, NULL, NULL, '12:52:45', '14:12:05', 'Parent ID: 1', 'Parent ID: 5', 1, NULL, '2026-07-18 20:52:45', '2026-07-18 22:12:05'),
	(18, 23, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '13:07:15', '14:12:05', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 21:07:15', '2026-07-18 22:12:05'),
	(19, 24, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '13:18:52', '14:12:05', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 21:18:52', '2026-07-18 22:12:05'),
	(21, 25, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '13:39:57', '14:12:05', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 21:39:57', '2026-07-18 22:12:05'),
	(22, 26, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '13:39:57', '14:12:05', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 21:39:57', '2026-07-18 22:12:05'),
	(23, 27, 5, '2026-07-19', 'checkout', 0, NULL, NULL, '13:39:57', '14:12:05', 'Parent ID: 5', 'Parent ID: 5', 1, NULL, '2026-07-18 21:39:57', '2026-07-18 22:12:05'),
	(24, 29, 5, '2026-07-19', 'present', 0, NULL, NULL, '16:30:37', NULL, 'Parent ID: 5', NULL, 1, NULL, '2026-07-19 00:30:37', '2026-07-19 00:30:37'),
	(25, 30, 6, '2026-07-19', 'late_checkout', 0, NULL, NULL, '17:25:57', '18:18:35', 'Parent ID: 6', 'Parent ID: 6', 1, NULL, '2026-07-19 01:25:57', '2026-07-19 02:18:35'),
	(26, 32, 1, '2026-07-19', 'present', 0, NULL, NULL, '18:29:07', NULL, 'Parent ID: 1', NULL, 1, NULL, '2026-07-19 02:29:07', '2026-07-19 02:29:07'),
	(27, 33, 1, '2026-07-20', 'late_checkout', 0, NULL, NULL, '03:31:16', '03:37:01', 'Parent ID: 1', 'Parent ID: 6', 1, NULL, '2026-07-19 11:31:16', '2026-07-19 11:37:01'),
	(28, 34, 6, '2026-07-20', 'late_checkout', 0, NULL, NULL, '04:09:07', '04:42:38', 'Parent ID: 6', 'Parent ID: 1', 1, NULL, '2026-07-19 12:09:07', '2026-07-19 12:42:38'),
	(29, 35, 6, '2026-07-20', 'late_checkout', 0, NULL, NULL, '04:56:30', '05:25:43', 'Parent ID: 6', 'Parent ID: 1', 1, NULL, '2026-07-19 12:56:30', '2026-07-19 13:25:43'),
	(30, 31, 6, '2026-07-19', 'present', 0, NULL, NULL, '04:58:43', NULL, 'Parent ID: 6', NULL, 1, NULL, '2026-07-19 12:58:43', '2026-07-19 12:58:43'),
	(31, 32, 1, '2026-07-20', 'present', 0, NULL, NULL, '05:05:43', NULL, 'Parent ID: 1', NULL, 1, NULL, '2026-07-19 13:05:43', '2026-07-19 13:05:43'),
	(32, 30, 1, '2026-07-20', 'late_checkout', 0, NULL, NULL, '05:17:48', '05:24:28', 'Parent ID: 1', 'Parent ID: 1', 1, NULL, '2026-07-19 13:17:48', '2026-07-19 13:24:28');

-- Dumping structure for table lab2.cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.cache: ~0 rows (approximately)
DELETE FROM `cache`;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
	('laravel-cache-ina@gmail.com|127.0.0.1', 'i:1;', 1784421827),
	('laravel-cache-ina@gmail.com|127.0.0.1:timer', 'i:1784421827;', 1784421827);

-- Dumping structure for table lab2.cache_locks
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.cache_locks: ~0 rows (approximately)
DELETE FROM `cache_locks`;

-- Dumping structure for table lab2.children
CREATE TABLE IF NOT EXISTS `children` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `ic_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned NOT NULL,
  `second_parent_id` bigint unsigned DEFAULT NULL,
  `guardian_id` bigint unsigned DEFAULT NULL,
  `medical_notes` text COLLATE utf8mb4_unicode_ci,
  `dietary` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `classroom_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `children_ic_number_unique` (`ic_number`),
  UNIQUE KEY `children_qr_code_unique` (`qr_code`),
  KEY `children_parent_id_foreign` (`parent_id`),
  KEY `children_guardian_id_foreign` (`guardian_id`),
  KEY `children_second_parent_id_foreign` (`second_parent_id`),
  CONSTRAINT `children_guardian_id_foreign` FOREIGN KEY (`guardian_id`) REFERENCES `guardians` (`id`) ON DELETE SET NULL,
  CONSTRAINT `children_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `children_second_parent_id_foreign` FOREIGN KEY (`second_parent_id`) REFERENCES `parents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.children: ~7 rows (approximately)
DELETE FROM `children`;
INSERT INTO `children` (`id`, `name`, `age`, `ic_number`, `dob`, `address`, `photo`, `qr_code`, `qr_code_url`, `parent_id`, `second_parent_id`, `guardian_id`, `medical_notes`, `dietary`, `is_active`, `enrollment_date`, `created_at`, `updated_at`, `classroom_id`) VALUES
	(1, 'My Support System', 2, '535646768574534', '2026-06-02', 'fgtehteh', 'children/ErDjLTtoVRd5yS7HNd2ZQhFWP8oLdA6FCrfWXP8s.png', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, '2026-06-13', '2026-06-13 07:13:29', '2026-06-13 07:13:29', 1),
	(3, 'tudent', 1, '535640768574534', '2026-07-07', 'x', NULL, 'KID-0003-1784078974-EewJT3mT', 'http://127.0.0.1:8000/scan-qr/KID-0003-1784078974-EewJT3mT', 3, NULL, 3, 'cc', 'c', 1, '2026-07-15', '2026-07-14 17:29:34', '2026-07-14 17:29:34', 1),
	(4, 'aqila', 2, '535640768594534', NULL, 'iiii', NULL, 'KID-0004-1784170843-xFVdwIWf', 'http://127.0.0.1:8000/scan-qr/KID-0004-1784170843-xFVdwIWf', 4, NULL, 4, 'jjjj', NULL, 1, '2026-07-16', '2026-07-15 19:00:43', '2026-07-15 19:00:43', 1),
	(5, 'KASSAD', 2, '02567905405943', '2026-07-08', 'OK', NULL, 'KID-0005-1784222245-YmPdnhdJ', 'http://127.0.0.1:8000/scan-qr/KID-0005-1784222245-YmPdnhdJ', 3, NULL, NULL, 'KK', 'KK', 1, '2026-07-16', '2026-07-16 09:17:25', '2026-07-16 09:17:25', 1),
	(12, 'farah', 2, '02567705405941', '2026-07-14', 'j', NULL, 'KID-0006-1784278054-LKUNHIlD', 'http://127.0.0.1:8000/scan-qr/KID-0006-1784278054-LKUNHIlD', 5, NULL, 5, 'k', 'k', 1, '2026-07-17', '2026-07-17 00:47:34', '2026-07-17 00:47:34', 1),
	(14, 'vff', 1, '02567705905940', '2026-07-13', 'j', NULL, 'KID-0013-1784279271-a7cFsny0', 'http://127.0.0.1:8000/scan-qr/KID-0013-1784279271-a7cFsny0', 8, NULL, 7, 'jj', 'jj', 1, '2026-07-17', '2026-07-17 01:07:51', '2026-07-17 01:07:51', 1),
	(15, 'pdd', 2, '02534905405940', '2026-07-03', 'k', NULL, 'KID-0015-1784280086-RwlWqGDB', 'http://127.0.0.1:8000/scan-qr/KID-0015-1784280086-RwlWqGDB', 8, 8, 7, 'gb', 'ngf', 1, '2026-07-17', '2026-07-17 01:21:26', '2026-07-17 01:44:00', 1),
	(16, 'anak', 1, '02567905475940', '2026-07-23', 'j', NULL, 'KID-0016-1784283247-zgUxmqTC', 'http://127.0.0.1:8000/scan-qr/KID-0016-1784283247-zgUxmqTC', 5, 5, 5, 'j', 'j', 1, '2026-07-17', '2026-07-17 02:14:07', '2026-07-17 02:14:07', 1),
	(17, 'NANA', 2, '535646758574534', '2026-07-13', 'UUU', NULL, 'KID-0017-1784432505-Xf5pc84h', 'http://127.0.0.1:8000/scan-qr/KID-0017-1784432505-Xf5pc84h', 5, 5, 5, 'J', 'J', 1, '2026-07-19', '2026-07-18 19:41:45', '2026-07-18 19:41:45', 1),
	(18, 'NANA', 2, '535446758574534', '2026-07-13', 'UUU', NULL, 'KID-0018-1784432518-1XzZyc8F', 'http://127.0.0.1:8000/scan-qr/KID-0018-1784432518-1XzZyc8F', 5, 5, 5, 'J', 'J', 1, '2026-07-19', '2026-07-18 19:41:58', '2026-07-18 19:41:58', 1),
	(19, 'VV', 2, '531640708574534', '2026-06-29', 'K', NULL, 'KID-0019-1784435567-N97mQ4Ep', 'http://127.0.0.1:8000/scan-qr/KID-0019-1784435567-N97mQ4Ep', 5, 5, 5, 'F', 'F', 1, '2026-07-19', '2026-07-18 20:32:47', '2026-07-18 20:32:47', 1),
	(20, 'kemba', 1, '535646758574587', '2026-07-08', 'j', NULL, 'KID-0020-1784435796-k19ER3b3', 'http://127.0.0.1:8000/scan-qr/KID-0020-1784435796-k19ER3b3', 5, 5, 5, 'i', 'i', 1, '2026-07-19', '2026-07-18 20:36:36', '2026-07-18 20:36:36', 1),
	(21, 'j', 1, '531640704574534', '2026-07-01', 'k', NULL, 'KID-0021-1784436119-NYcVNhIX', 'http://127.0.0.1:8000/scan-qr/KID-0021-1784436119-NYcVNhIX', 5, 5, 5, 'kk', 'kk', 1, '2026-07-19', '2026-07-18 20:41:59', '2026-07-18 20:41:59', 1),
	(22, 'ufiuffghsr', 2, '53564076856544534', '2026-07-13', 'kk', NULL, 'KID-0022-1784436302-rC6lp1uW', 'http://127.0.0.1:8000/scan-qr/KID-0022-1784436302-rC6lp1uW', 5, 5, 5, 'kk', 'kk', 1, '2026-07-19', '2026-07-18 20:45:02', '2026-07-18 20:45:02', 1),
	(23, 'sods', 1, '535640768574557', '2026-07-08', 'kjj', NULL, 'KID-0023-1784436693-TA1eT5Gl', 'http://127.0.0.1:8000/scan-qr/KID-0023-1784436693-TA1eT5Gl', 5, 5, 5, 'jj', 'jj', 1, '2026-07-19', '2026-07-18 20:51:33', '2026-07-18 20:51:33', 1),
	(24, 'FF', 1, '435646758574534', '2026-07-15', 'IIO', NULL, 'KID-0024-1784437115-5ER9HmJk', 'http://127.0.0.1:8000/scan-qr/KID-0024-1784437115-5ER9HmJk', 5, 5, 5, 'II', 'III', 1, '2026-07-19', '2026-07-18 20:58:35', '2026-07-18 20:58:35', 1),
	(25, 'JJ', 2, '635646768574534', '2026-07-15', 'K', NULL, 'KID-0025-1784437440-SqaBIItI', 'http://127.0.0.1:8000/scan-qr/KID-0025-1784437440-SqaBIItI', 5, 5, 5, 'K', 'K', 1, '2026-07-19', '2026-07-18 21:04:01', '2026-07-18 21:04:01', 1),
	(26, 'DD', 1, '535336758574534', '2026-07-07', 'CV', NULL, 'KID-0026-1784437881-MjKC0fjh', 'http://127.0.0.1:8000/scan-qr/KID-0026-1784437881-MjKC0fjh', 5, 5, 5, 'VFS', 'FG', 1, '2026-07-19', '2026-07-18 21:11:21', '2026-07-18 21:11:21', 1),
	(27, 'V', 2, '535800768574534', '2026-07-14', 'O', NULL, 'KID-0027-1784438264-bWJI9Irp', 'http://127.0.0.1:8000/scan-qr/KID-0027-1784438264-bWJI9Irp', 5, 5, 5, 'OOOOO', 'JJ', 1, '2026-07-19', '2026-07-18 21:17:44', '2026-07-18 21:17:44', 1),
	(29, 'DGB', 2, '535640768574567', '2026-07-09', 'DS', NULL, 'KID-0028-1784449809-KVRiYa8y', 'http://127.0.0.1:8000/scan-qr/KID-0028-1784449809-KVRiYa8y', 5, 5, 5, 'VBG', 'DGNG', 1, '2026-07-19', '2026-07-19 00:30:09', '2026-07-19 00:30:09', 1),
	(30, 'lsds', 3, '5356466548574534', '2026-07-14', 'fbgd', NULL, 'KID-0030-1784452571-FsPMd5Vf', 'http://127.0.0.1:8000/scan-qr/KID-0030-1784452571-FsPMd5Vf', 6, 6, 6, 'fbdffb', 'vbdg', 1, '2026-07-19', '2026-07-19 01:16:11', '2026-07-19 01:16:11', 2),
	(31, 'sdsd', 3, '5356465468574534', '2026-07-03', 'bg', NULL, 'KID-0031-1784454771-djcec42J', 'http://127.0.0.1:8000/scan-qr/KID-0031-1784454771-djcec42J', 6, 6, 6, 'gg', 'fht', 1, '2026-07-19', '2026-07-19 01:52:51', '2026-07-19 01:52:51', 2),
	(32, 'sdsd', 3, '5356465468574500', '2026-07-03', 'bg', NULL, 'KID-0032-1784454784-YHgkH3YD', 'http://127.0.0.1:8000/scan-qr/KID-0032-1784454784-YHgkH3YD', 6, 6, 6, 'gg', 'fht', 1, '2026-07-19', '2026-07-19 01:53:04', '2026-07-19 01:53:04', 2),
	(33, 'gy', 3, '235640768574534', '2026-07-23', 'g', NULL, 'KID-0033-1784489341-mPNfPO8t', 'http://127.0.0.1:8000/scan-qr/KID-0033-1784489341-mPNfPO8t', 6, 6, 6, 'vg', 'gg', 1, '2026-07-19', '2026-07-19 11:29:01', '2026-07-19 11:29:01', 2),
	(34, 'ss', 3, '275646758574534', '2026-07-08', 'k', NULL, 'KID-0034-1784491716-rX0UzsdT', 'http://127.0.0.1:8000/scan-qr/KID-0034-1784491716-rX0UzsdT', 6, 6, 6, 'k', 'k', 1, '2026-07-19', '2026-07-19 12:08:36', '2026-07-19 12:08:36', 2),
	(35, 'kk', 3, '935640700594534', '2026-07-22', 'k', NULL, 'KID-0035-1784494542-j8PTubAD', 'http://127.0.0.1:8000/scan-qr/KID-0035-1784494542-j8PTubAD', 6, 6, 6, 'kkkk', 'kk', 1, '2026-07-19', '2026-07-19 12:55:42', '2026-07-19 12:55:42', 2);

-- Dumping structure for table lab2.children_backup
CREATE TABLE IF NOT EXISTS `children_backup` (
  `id` bigint unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `ic_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned NOT NULL,
  `second_parent_id` bigint unsigned DEFAULT NULL,
  `guardian_id` bigint unsigned DEFAULT NULL,
  `medical_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `dietary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `classroom_id` bigint unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table lab2.children_backup: ~7 rows (approximately)
DELETE FROM `children_backup`;
INSERT INTO `children_backup` (`id`, `name`, `age`, `ic_number`, `dob`, `address`, `photo`, `qr_code`, `qr_code_url`, `parent_id`, `second_parent_id`, `guardian_id`, `medical_notes`, `dietary`, `is_active`, `enrollment_date`, `created_at`, `updated_at`, `classroom_id`) VALUES
	(1, 'My Support System', 2, '535646768574534', '2026-06-02', 'fgtehteh', 'children/ErDjLTtoVRd5yS7HNd2ZQhFWP8oLdA6FCrfWXP8s.png', NULL, NULL, 1, NULL, NULL, NULL, NULL, 1, '2026-06-13', '2026-06-13 07:13:29', '2026-06-13 07:13:29', 1),
	(3, 'tudent', 1, '535640768574534', '2026-07-07', 'x', NULL, 'KID-0003-1784078974-EewJT3mT', 'http://127.0.0.1:8000/scan-qr/KID-0003-1784078974-EewJT3mT', 3, NULL, 3, 'cc', 'c', 1, '2026-07-15', '2026-07-14 17:29:34', '2026-07-14 17:29:34', 1),
	(4, 'aqila', 2, '535640768594534', NULL, 'iiii', NULL, 'KID-0004-1784170843-xFVdwIWf', 'http://127.0.0.1:8000/scan-qr/KID-0004-1784170843-xFVdwIWf', 4, NULL, 4, 'jjjj', NULL, 1, '2026-07-16', '2026-07-15 19:00:43', '2026-07-15 19:00:43', 1),
	(5, 'KASSAD', 2, '02567905405943', '2026-07-08', 'OK', NULL, 'KID-0005-1784222245-YmPdnhdJ', 'http://127.0.0.1:8000/scan-qr/KID-0005-1784222245-YmPdnhdJ', 3, NULL, NULL, 'KK', 'KK', 1, '2026-07-16', '2026-07-16 09:17:25', '2026-07-16 09:17:25', 1),
	(12, 'farah', 2, '02567705405941', '2026-07-14', 'j', NULL, 'KID-0006-1784278054-LKUNHIlD', 'http://127.0.0.1:8000/scan-qr/KID-0006-1784278054-LKUNHIlD', 5, NULL, 5, 'k', 'k', 1, '2026-07-17', '2026-07-17 00:47:34', '2026-07-17 00:47:34', 1),
	(14, 'vff', 1, '02567705905940', '2026-07-13', 'j', NULL, 'KID-0013-1784279271-a7cFsny0', 'http://127.0.0.1:8000/scan-qr/KID-0013-1784279271-a7cFsny0', 8, NULL, 7, 'jj', 'jj', 1, '2026-07-17', '2026-07-17 01:07:51', '2026-07-17 01:07:51', 1),
	(15, 'pdd', 2, '02534905405940', '2026-07-03', 'k', NULL, 'KID-0015-1784280086-RwlWqGDB', 'http://127.0.0.1:8000/scan-qr/KID-0015-1784280086-RwlWqGDB', 8, 8, 7, 'gb', 'ngf', 1, '2026-07-17', '2026-07-17 01:21:26', '2026-07-17 01:44:00', 1);

-- Dumping structure for table lab2.classrooms
CREATE TABLE IF NOT EXISTS `classrooms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `age_group` varchar(255) NOT NULL,
  `min_age` int NOT NULL,
  `max_age` int NOT NULL,
  `capacity` int DEFAULT '20',
  `teacher_id` bigint unsigned DEFAULT NULL,
  `start_time` time DEFAULT '08:00:00',
  `end_time` time DEFAULT '17:00:00',
  `status` enum('active','inactive') DEFAULT 'active',
  `description` text,
  `color` varchar(255) DEFAULT '#FF6B6B',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table lab2.classrooms: ~0 rows (approximately)
DELETE FROM `classrooms`;
INSERT INTO `classrooms` (`id`, `name`, `code`, `age_group`, `min_age`, `max_age`, `capacity`, `teacher_id`, `start_time`, `end_time`, `status`, `description`, `color`, `created_at`, `updated_at`) VALUES
	(1, 'NURSERY 1', 'N1', '2', 1, 2, 20, NULL, '08:00:00', '17:00:00', 'active', NULL, '#45B7D1', '2026-06-13 06:34:44', '2026-06-13 06:34:44'),
	(2, 'NURSERY 2', 'N2', '3', 1, 3, 20, 1, '08:00:00', '17:00:00', 'active', NULL, '#FF6B6B', '2026-07-19 01:15:25', '2026-07-19 01:15:25');

-- Dumping structure for table lab2.days
CREATE TABLE IF NOT EXISTS `days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `day_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.days: ~0 rows (approximately)
DELETE FROM `days`;

-- Dumping structure for table lab2.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

-- Dumping structure for table lab2.guardians
CREATE TABLE IF NOT EXISTS `guardians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('main','second','guardian') COLLATE utf8mb4_unicode_ci DEFAULT 'guardian',
  `verified` tinyint(1) DEFAULT '0',
  `emergency` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.guardians: ~5 rows (approximately)
DELETE FROM `guardians`;
INSERT INTO `guardians` (`id`, `parent_id`, `user_id`, `name`, `age`, `phone`, `address`, `photo`, `type`, `verified`, `emergency`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'NENEK', '56', '01133458141', 'KAMPUNG BATU 11', NULL, 'guardian', 0, 0, '2026-06-13 06:55:12', '2026-06-13 06:55:12'),
	(3, 3, 13, 'hello', '89', '01199458141', 'KAMPUNG BATU 11', NULL, 'guardian', 0, 0, '2026-07-14 10:01:36', '2026-07-14 10:01:36'),
	(4, 4, 18, 'ne', '89', '01877543232', 'KAMPUNG BATU 11', NULL, 'guardian', 0, 0, '2026-07-15 18:59:31', '2026-07-15 18:59:31'),
	(5, 5, 21, 'gu', '90', '01899767877', 'KAMPUNG BATU 11', NULL, 'guardian', 0, 0, '2026-07-16 11:02:46', '2026-07-16 11:02:46'),
	(6, 6, 24, 'u', '90', '0177739561', 'KAMPUNG BATU 11', NULL, 'guardian', 0, 0, '2026-07-16 23:51:53', '2026-07-16 23:51:53'),
	(7, 8, 27, 'huhu', '90', '01100458141', 'kampung batu 11', NULL, 'guardian', 0, 0, '2026-07-17 01:07:00', '2026-07-17 01:07:00');

-- Dumping structure for table lab2.halls
CREATE TABLE IF NOT EXISTS `halls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lecture_hall_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lecture_hall_place` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.halls: ~0 rows (approximately)
DELETE FROM `halls`;

-- Dumping structure for table lab2.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.jobs: ~0 rows (approximately)
DELETE FROM `jobs`;

-- Dumping structure for table lab2.job_batches
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.job_batches: ~0 rows (approximately)
DELETE FROM `job_batches`;

-- Dumping structure for table lab2.lecturer_groups
CREATE TABLE IF NOT EXISTS `lecturer_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `part` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.lecturer_groups: ~0 rows (approximately)
DELETE FROM `lecturer_groups`;

-- Dumping structure for table lab2.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.migrations: ~12 rows (approximately)
DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '0001_01_01_000001_create_cache_table', 1),
	(3, '0001_01_01_000002_create_jobs_table', 1),
	(4, '2026_04_21_021132_create_subject_table', 1),
	(5, '2026_04_21_021948_create_halls_table', 1),
	(6, '2026_04_21_022059_create_days_table', 1),
	(7, '2026_04_21_022137_create_lecturer_groups_table', 1),
	(8, '2026_04_21_022221_alter_add_table', 1),
	(9, '2026_04_21_023004_create_student_timetables_table', 1),
	(10, '2026_04_21_023040_alter_column_in_student_timetables_table', 1),
	(11, '2026_05_19_123622_add_role_to_users_table', 1),
	(12, '2026_05_19_124155_add_role_column_to_users_table_fix', 1),
	(13, '2026_06_13_093430_create_parents_table', 1),
	(14, '2026_06_13_110323_create_children_table', 2),
	(15, '2026_06_13_151052_change_nursery_type_to_classroom_id_in_children_table', 3),
	(16, '2026_06_13_155711_create_attendance_table', 4),
	(17, '2026_07_15_010915_add_qr_code_to_children_table', 5),
	(18, '2026_07_15_011314_add_columns_to_guardians_table', 5),
	(19, '2026_07_15_205026_add_confirmation_to_attendance_table', 6),
	(20, '2026_07_17_100503_fix_children_second_parent_id', 6),
	(21, '2026_07_18_170455_create_timer_settings_table', 7),
	(22, '2026_07_19_002054_create_simulation_clock_table', 8),
	(23, '2026_07_19_005914_add_checkin_columns_to_simulation_clock_table', 9),
	(24, '2026_07_19_024202_create_simulation_clock_table', 10);

-- Dumping structure for table lab2.parents
CREATE TABLE IF NOT EXISTS `parents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('main','second','guardian') COLLATE utf8mb4_unicode_ci DEFAULT 'main',
  `verified` tinyint(1) DEFAULT '0',
  `emergency` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.parents: ~9 rows (approximately)
DELETE FROM `parents`;
INSERT INTO `parents` (`id`, `user_id`, `name`, `age`, `phone`, `address`, `photo`, `type`, `verified`, `emergency`, `created_at`, `updated_at`) VALUES
	(1, NULL, 'NORAZILA', '16', '012242344534', 'KAMPUNG BATU 11\r\nJEMENTAH', 'parents/2is4YCAref3ToHWqEnAL6EjST9Fv7fuvLZrxjQpV.png', 'main', 0, 0, '2026-06-13 06:55:12', '2026-06-13 06:55:12'),
	(3, 11, 'HAI', '35', '01567899087', 'KAMPUNG BATU 11\r\nJEMENTAH', NULL, 'main', 1, 1, '2026-07-14 09:49:31', '2026-07-14 09:49:31'),
	(4, 16, 'hasana', '35', '01877564432', 'KAMPUNG BATU 11\r\nJEMENTAH', NULL, 'main', 1, 1, '2026-07-15 18:59:30', '2026-07-15 18:59:30'),
	(5, 19, 'tiya', '35', '01655453232', 'KAMPUNG BATU 11', NULL, 'main', 1, 1, '2026-07-16 11:02:44', '2026-07-16 11:02:44'),
	(6, 22, 'JJJ', '35', '01788999090', 'f', NULL, 'main', 1, 1, '2026-07-16 23:51:52', '2026-07-16 23:51:52'),
	(7, 99, 'tu', '89', '0123456789', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, NULL, NULL),
	(8, 25, 'kamarul', '35', '0177739574', 'KAMPUNG BATU 11', NULL, 'main', 1, 1, '2026-07-17 01:06:59', '2026-07-17 01:06:59'),
	(9, 99, 'tu', NULL, '0123456789', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, NULL, NULL),
	(10, NULL, 'MUHAMAD', NULL, '01133458141', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, NULL, NULL);

-- Dumping structure for table lab2.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.password_reset_tokens: ~0 rows (approximately)
DELETE FROM `password_reset_tokens`;

-- Dumping structure for table lab2.second_parents
CREATE TABLE IF NOT EXISTS `second_parents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('main','second','guardian') COLLATE utf8mb4_unicode_ci DEFAULT 'second',
  `verified` tinyint(1) DEFAULT '0',
  `emergency` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.second_parents: ~7 rows (approximately)
DELETE FROM `second_parents`;
INSERT INTO `second_parents` (`id`, `parent_id`, `user_id`, `name`, `age`, `phone`, `address`, `photo`, `type`, `verified`, `emergency`, `created_at`, `updated_at`) VALUES
	(1, 10, NULL, 'MUHAMAD', '34', '01133458141', 'KAMPUNG BATU 11', 'parents/51uliOJTVC2S3WO5HZhA8JAarFjqXovpEXq7qrTQ.png', 'second', 0, 0, '2026-06-13 06:55:12', '2026-06-13 06:55:12'),
	(3, 3, 12, 'bye', '34', '01133459876', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, '2026-07-14 10:01:36', '2026-07-14 10:01:36'),
	(4, 4, 17, 'nini', '34', '01156789800', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, '2026-07-15 18:59:30', '2026-07-15 18:59:30'),
	(5, 5, 20, 'yuti', '89', '01256768890', 'kampung batu 11', NULL, 'second', 0, 0, '2026-07-16 11:02:45', '2026-07-16 11:02:45'),
	(6, 6, 23, 'tu', '89', '01677889903', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, '2026-07-16 23:51:53', '2026-07-16 23:51:53'),
	(7, 9, 99, 'tu', NULL, '0123456789', 'KAMPUNG BATU 11', NULL, 'second', 0, 0, NULL, NULL),
	(8, 8, 26, 'kakakaakak', '89', '01133457654', 'kampung batu 11', NULL, 'second', 0, 0, '2026-07-17 01:07:00', '2026-07-17 01:07:00');

-- Dumping structure for table lab2.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.sessions: ~5 rows (approximately)
DELETE FROM `sessions`;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
	('3zkvCimqhRt5BSRrRk7ZTyfuuOBg3XeN00CgJYE2', 24, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJtWk5RdUlvZDAzek8xRzdCUEtkMnNrVFRJYllPZnVnQmduVFc2ZU5SIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BhcmVudFwvZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9raW9za1wvYWRkLWFub3RoZXJcLzM1Iiwicm91dGUiOiJraW9zay5hZGQuYW5vdGhlciJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MjR9', 1784497648),
	('h7CgdpacuSFWxVurhFptqGR6KWmgUIIJlSqgWLUd', 23, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJhQVYwZEJGN2NEQ28xWUpJd0hqOWRkSEFzQlJTUWt6YmVxcWRCb2ZMIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BhcmVudFwvZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9raW9zayIsInJvdXRlIjoia2lvc2suaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIzfQ==', 1784495417),
	('hOwJPlchBexOYcC2qiMmkg5rclFFNHcPrfBYN10l', 23, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJMSjFxVG9BdzZYc1RCUDEwQ2hXMnR6RjVHSGVmRWNBWmJuOUF0cjc0IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3BhcmVudFwvZGFzaGJvYXJkIiwicm91dGUiOiJwYXJlbnQuZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyM30=', 1784496295),
	('HYkp38uRULlnwTNPNawGQW3JaJWbeGgmPd6tsGti', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJobVhiSlNramNzN1Q0SVpsYUxaa1BCcEhreEYwVFpwVWc0S2hCTjNkIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2F0dGVuZGFuY2VcL2V4cG9ydC1zaW5nbGVcLzMyIiwicm91dGUiOiJhdHRlbmRhbmNlLmV4cG9ydC5zaW5nbGUifSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1784497641);

-- Dumping structure for table lab2.simulation_clock
CREATE TABLE IF NOT EXISTS `simulation_clock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `simulation_time` datetime NOT NULL,
  `morning_start` time NOT NULL DEFAULT '07:00:00',
  `morning_end` time NOT NULL DEFAULT '07:30:00',
  `evening_start` time NOT NULL DEFAULT '17:00:00',
  `evening_end` time NOT NULL DEFAULT '17:30:00',
  `use_simulation` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.simulation_clock: ~1 rows (approximately)
DELETE FROM `simulation_clock`;
INSERT INTO `simulation_clock` (`id`, `simulation_time`, `morning_start`, `morning_end`, `evening_start`, `evening_end`, `use_simulation`, `created_at`, `updated_at`) VALUES
	(1, '2026-07-19 02:42:45', '07:00:00', '07:30:00', '17:00:00', '17:30:00', 0, '2026-07-18 18:42:45', '2026-07-18 18:42:45');

-- Dumping structure for table lab2.student_timetables
CREATE TABLE IF NOT EXISTS `student_timetables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `day_id` bigint unsigned DEFAULT NULL,
  `hall_id` bigint unsigned DEFAULT NULL,
  `lecturer_group_id` bigint unsigned DEFAULT NULL,
  `time_from` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_timetables_subject_id_foreign` (`subject_id`),
  KEY `student_timetables_day_id_foreign` (`day_id`),
  KEY `student_timetables_hall_id_foreign` (`hall_id`),
  KEY `student_timetables_user_id_foreign` (`user_id`),
  KEY `student_timetables_lecturer_group_id_foreign` (`lecturer_group_id`),
  CONSTRAINT `student_timetables_day_id_foreign` FOREIGN KEY (`day_id`) REFERENCES `days` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_timetables_hall_id_foreign` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_timetables_lecturer_group_id_foreign` FOREIGN KEY (`lecturer_group_id`) REFERENCES `lecturer_groups` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_timetables_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `student_timetables_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.student_timetables: ~0 rows (approximately)
DELETE FROM `student_timetables`;

-- Dumping structure for table lab2.subjects
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lecturer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.subjects: ~0 rows (approximately)
DELETE FROM `subjects`;

-- Dumping structure for table lab2.teachers
CREATE TABLE IF NOT EXISTS `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `age` int NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `photo` varchar(255) DEFAULT NULL,
  `classroom_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','inactive','on_leave') DEFAULT 'active',
  `qualifications` text,
  `join_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table lab2.teachers: ~1 rows (approximately)
DELETE FROM `teachers`;
INSERT INTO `teachers` (`id`, `name`, `position`, `age`, `phone`, `email`, `address`, `photo`, `classroom_id`, `status`, `qualifications`, `join_date`, `created_at`, `updated_at`) VALUES
	(1, 'NUR HASANATUN NASUHA MD AZLEE', 'Head Teacher', 35, '01133458141', 'hasanatunnasuha@gmail.com', 'KAMPUNG BATU 11\r\nJEMENTAH', 'teachers/JzYXOcLFzfYWGNldPDS8HxjnkzOT6WQDIVjQ1EWC.jpg', 1, 'active', NULL, NULL, '2026-06-13 06:46:37', '2026-06-13 06:46:37');

-- Dumping structure for table lab2.timer_settings
CREATE TABLE IF NOT EXISTS `timer_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `day_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `morning_start` time NOT NULL DEFAULT '07:00:00',
  `morning_end` time NOT NULL DEFAULT '07:30:00',
  `afternoon_start` time NOT NULL DEFAULT '12:00:00',
  `afternoon_end` time NOT NULL DEFAULT '12:30:00',
  `evening_start` time NOT NULL DEFAULT '17:00:00',
  `evening_end` time NOT NULL DEFAULT '17:30:00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `timer_settings_day_name_unique` (`day_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.timer_settings: ~8 rows (approximately)
DELETE FROM `timer_settings`;
INSERT INTO `timer_settings` (`id`, `day_name`, `morning_start`, `morning_end`, `afternoon_start`, `afternoon_end`, `evening_start`, `evening_end`, `is_active`, `created_at`, `updated_at`) VALUES
	(1, 'Monday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 11:54:44', '2026-07-19 11:34:42'),
	(2, '_token', '07:00:00', '07:30:00', '12:00:00', '12:30:00', '17:00:00', '17:30:00', 1, '2026-07-18 17:58:46', '2026-07-18 17:58:46'),
	(3, 'Tuesday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 17:58:46', '2026-07-19 11:34:42'),
	(4, 'Wednesday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 17:58:46', '2026-07-19 11:34:42'),
	(5, 'Thursday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 17:58:46', '2026-07-19 11:34:42'),
	(6, 'Friday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 17:58:46', '2026-07-19 11:34:42'),
	(7, 'Saturday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 17:58:46', '2026-07-19 11:34:42'),
	(8, 'Sunday', '03:00:00', '03:30:00', '12:00:00', '12:30:00', '03:31:00', '18:30:00', 1, '2026-07-18 17:58:46', '2026-07-19 11:34:42');

-- Dumping structure for table lab2.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8mb4_unicode_ci,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'student',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table lab2.users: ~25 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `age`, `email`, `email_verified_at`, `password`, `phone_number`, `address`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'diana', NULL, 'diana@gmail.com', NULL, '$2y$12$bN5tG.udD8DB3VeQeO1jWOVaWPEEH39wTWaOe.se.DtgdF6GD98VS', '01334435546', 'KAMPUNG BATU 11', 'admin', 'u8BQpGCB0Fmv7WT74En2SYpE4vomoM4wSOGRXbvndaHsFM5bhdUvH6d9aFIh', '2026-06-13 06:32:04', '2026-06-13 06:32:04'),
	(2, 'jia', NULL, 'jia@gmail.com', NULL, '$2y$12$svSwla.m9JVgogd1wG7VgOEr.1VzIOrNXSllWcBCiNXbx.esQtLiK', '01533458141', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-13 08:42:34', '2026-06-13 08:42:34'),
	(3, 'NURIA', NULL, 'nuria@gmail.com', NULL, '$2y$12$PBb2kLPw7g6IqFXW2rOqveOhqJ8xDv90dPFhn.hoDbeIy.HVys3E6', '01334558132', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-13 08:44:07', '2026-06-13 08:44:07'),
	(4, 'nj', NULL, 'nj@gmail.com', NULL, '$2y$12$kut.BvebFo0L.CJZW2rBleyqSXg.kWzEdjtWyQ0dgLsn4.C947HYu', '01933456781', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-13 09:52:10', '2026-06-13 09:52:10'),
	(5, 'nini', NULL, 'nini@gmail.com', NULL, '$2y$12$dxKLigx9gEMk.YzTmYj7jeJCaUYyP9l8kHgrUhtfSjhrCPNn9diOa', '01344568141', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-13 21:10:41', '2026-06-13 21:10:41'),
	(6, 'ki', NULL, 'ki@gmail.com', NULL, '$2y$12$WkPc12SulSUL0ZK/WTSXV.zyelJNQ5w/NnYGKVoO.E9p.JO/G.Wxi', '01233458765', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-13 22:04:05', '2026-06-13 22:04:05'),
	(7, 'lsl', NULL, 'ls@gmail.com', NULL, '$2y$12$F4XtwwvvIwM9H/HMIDA7iuzcjLTfHs1lfx4DUMoDtUjyOjDYiPF5a', '0142357768', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-15 10:29:00', '2026-06-15 10:29:00'),
	(8, 'na', NULL, 'na@gmail.com', NULL, '$2y$12$7LqBjYY9WKW71gMvpdNFIuAgrlDlYfWJgRo2hyiKvm3ggC5sFxFwW', '01933458154', 'KAMPUNG BATU 11', 'admin', NULL, '2026-06-15 18:30:06', '2026-06-15 18:30:06'),
	(9, 'kia', NULL, 'kia@gmail.com', NULL, '$2y$12$iauEf/oSE2SePW2LHq3pLucC33V5LuDRWMgF0WWdizyCsmM3Sltpq', '0123345942', 'KAMPUNG BATU 11', 'admin', NULL, '2026-07-14 08:02:12', '2026-07-14 08:02:12'),
	(10, 'FARHANA', NULL, 'farhana@gmail.com', NULL, '$2y$12$giygbwLSo/FHtSTBJd1J7eCzXz8YcAZesinu5aX62Y2LKsuc7ICei', NULL, NULL, 'parent1', NULL, '2026-07-14 09:29:19', '2026-07-14 09:29:19'),
	(11, 'HAI', NULL, 'hai@gmail.com', NULL, '$2y$12$p7ufHeyJof5z55NwBJDn8.2b.CSyFXj6f1mpljwg9j2HApz5f3Cwu', NULL, NULL, 'parent1', 'OQ0v1EYT4EbOycTYTbEBy1khMg3WHaDN0kNITPx8J5L4xkWxfyBR9jF0Qvhz', '2026-07-14 09:49:31', '2026-07-14 10:11:55'),
	(12, 'bye', NULL, 'bye@gmail.com', NULL, '$2y$12$g/jgL.GT8npO27KNXDbAA.x4Ep2EFXbfGOUQ5tcqPFp3PJc7VVIZi', NULL, NULL, 'parent2', NULL, '2026-07-14 10:01:36', '2026-07-14 10:11:55'),
	(13, 'hello', NULL, 'hello@gmail.com', NULL, '$2y$12$W2YoFuvxfYXdvxa5iQPcmefuoHAGnE2uKyoTxTcaKbjKhxYkEqRWW', NULL, NULL, 'guardian', NULL, '2026-07-14 10:01:36', '2026-07-14 10:11:56'),
	(16, 'hasana', NULL, 'hasana@gmail.com', NULL, '$2y$12$UVXs9Mo6vabuHUaN7I5JquN8NvJK74ZbBZTa5.IKv9ukxNhtIYDzS', NULL, NULL, 'parent1', NULL, '2026-07-15 18:59:30', '2026-07-15 18:59:30'),
	(17, 'nini', NULL, 'n9w9@gmail.com', NULL, '$2y$12$7sqQDt6pEGvLnYr.P.2tmOhT4u849yaeJ51jxTA.sNA0bDDWQv1Ai', NULL, NULL, 'parent2', NULL, '2026-07-15 18:59:30', '2026-07-15 18:59:30'),
	(18, 'ne', NULL, 'n9w@gmail.com', NULL, '$2y$12$V1yQGpL8AlkNhBy9sr0TueMzjyYI9zJUmct0Mt1E7J5buZ5QfwOr6', NULL, NULL, 'guardian', NULL, '2026-07-15 18:59:31', '2026-07-15 18:59:31'),
	(19, 'tiya', NULL, 'tiya@gmail.com', NULL, '$2y$12$ZiLebP/oR0RZ36T.TxrLaejCawsxv8QY3EGIbtFt2ftE5twIXCMp6', NULL, NULL, 'parent1', NULL, '2026-07-16 11:02:44', '2026-07-16 11:02:44'),
	(20, 'yuti', NULL, 'yuti@gmail.com', NULL, '$2y$12$DkZgzKDqUo/3pcK0I9Wc7./5CyROu0RSg9N/6fnlX/P7kTQbD59Wm', NULL, NULL, 'parent2', 'z0Mx5g76CfsnoMyyWnidxkfifgF2VGWZn3CsVYv94HuIjj35XVbGS8nrEUqz', '2026-07-16 11:02:45', '2026-07-16 11:02:45'),
	(21, 'gu', NULL, 'gu@gmail.com', NULL, '$2y$12$5m8TC7u/STX.WAJzYGSKUO6yGFzdkE8VObmhxB2mPUP5u4IDrzHOG', NULL, NULL, 'guardian', 'jbE9ksgaKqvSw8BzSyfybt6hNYad4GzneCFvUf1AokV55oginPH2dMqi3tPN', '2026-07-16 11:02:46', '2026-07-16 11:02:46'),
	(22, 'JJJ', NULL, 'ja@gmail.com', NULL, '$2y$12$lpgdw3hs7wgHj2i3YV1iWOJiaXrztVwqq.JvihGiChJyKtD5EWvC2', NULL, NULL, 'parent1', NULL, '2026-07-16 23:51:52', '2026-07-16 23:51:52'),
	(23, 'tu', NULL, 'tu@gmail.com', NULL, '$2y$12$IPQJFxYXObVOS5iUb2MXO.qgo8dHZpTsXODwwKnPMRx.L3Vmq8vsu', NULL, NULL, 'parent2', NULL, '2026-07-16 23:51:52', '2026-07-16 23:51:52'),
	(24, 'u', NULL, 'i@gmail.com', NULL, '$2y$12$HSYqKBUL3lh6rEO7HbQENexYGTx.pBCv8Ty3ADJ3V5s7A1ikgduEC', NULL, NULL, 'guardian', NULL, '2026-07-16 23:51:53', '2026-07-16 23:51:53'),
	(25, 'kamarul', NULL, 'kamarul@gmail.com', NULL, '$2y$12$lqMit4Se4gInden3m99FnukkbMj12eZfoK/.yzgJWiI7wFHJxELpG', NULL, NULL, 'parent1', NULL, '2026-07-17 01:06:59', '2026-07-17 01:06:59'),
	(26, 'kakakaakak', NULL, 'kaka@gmail.com', NULL, '$2y$12$T.pDDH/jvNfeKH2afehWiO3Ssf64.HEDZjwOAAfg/UTS2m72PB40m', NULL, NULL, 'parent2', NULL, '2026-07-17 01:07:00', '2026-07-17 01:07:00'),
	(27, 'huhu', NULL, 'hu@gmail.com', NULL, '$2y$12$1I2nAQ10tqxhcUpmvDh5n.miMtODa/w8suJHE2DdqSD3.L6HmR9JK', NULL, NULL, 'guardian', NULL, '2026-07-17 01:07:00', '2026-07-17 01:07:00');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
