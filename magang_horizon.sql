-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 12, 2026 at 07:40 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `magang_horizon`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint UNSIGNED NOT NULL,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `event`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'User admin was created', 'App\\Models\\User', 1, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"admin\", \"email\": \"admin@magang-horizon.test\", \"username\": \"admin\"}}', NULL, '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(2, 'default', 'User mahasiswa01 was created', 'App\\Models\\User', 2, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"mahasiswa\", \"email\": \"mahasiswa01@magang-horizon.test\", \"username\": \"mahasiswa01\"}}', NULL, '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(3, 'default', 'created', 'App\\Models\\Mahasiswa', 1, 'created', NULL, NULL, '{\"attributes\": {\"nim\": \"2024001\", \"prodi\": \"Teknik Informatika\", \"cv_file_path\": null, \"nama_lengkap\": \"Ahmad Fauzi\"}}', NULL, '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(4, 'default', 'User ptmajumapan was created', 'App\\Models\\User', 3, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"supervisor_industri\", \"email\": \"industri@magang-horizon.test\", \"username\": \"ptmajumapan\"}}', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(5, 'default', 'created', 'App\\Models\\Industri', 1, 'created', NULL, NULL, '{\"attributes\": {\"alamat\": \"Jl. Sudirman No. 123, Jakarta\", \"kontak_person\": \"Budi Santoso\", \"nama_perusahaan\": \"PT Maju Mapan\"}}', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(6, 'default', 'User dosenpembimbing01 was created', 'App\\Models\\User', 4, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"dosen_pembimbing\", \"email\": \"dosen1@magang-horizon.test\", \"username\": \"dosenpembimbing01\"}}', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(7, 'default', 'User dosenprodi01 was created', 'App\\Models\\User', 5, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"dosen_prodi\", \"email\": \"dosen2@magang-horizon.test\", \"username\": \"dosenprodi01\"}}', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(8, 'default', 'User galang was created', 'App\\Models\\User', 6, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"mahasiswa\", \"email\": \"galang@krw.horizon.ac.id\", \"username\": \"galang\"}}', NULL, '2026-05-07 15:05:27', '2026-05-07 15:05:27'),
(9, 'default', 'created', 'App\\Models\\Mahasiswa', 2, 'created', NULL, NULL, '{\"attributes\": {\"nim\": \"4112755201240001\", \"prodi\": \"S1 Informatika\", \"cv_file_path\": null, \"nama_lengkap\": \"Galang Sukmagama\"}}', NULL, '2026-05-07 15:05:28', '2026-05-07 15:05:28'),
(10, 'default', 'updated', 'App\\Models\\Mahasiswa', 2, 'updated', 'App\\Models\\User', 6, '{\"old\": {\"cv_file_path\": null}, \"attributes\": {\"cv_file_path\": \"documents/cv/2/4bpwuS0l4yC5mkjM4EhQHZoqIPJ4gO72fxpcfmNk.pdf\"}}', NULL, '2026-05-07 15:05:44', '2026-05-07 15:05:44'),
(11, 'document', 'Document uploaded: Curriculum Vitae', 'App\\Models\\Document', 1, NULL, 'App\\Models\\User', 6, '{\"type\": \"cv\", \"original_name\": \"2_Group Assignment.pdf\"}', NULL, '2026-05-07 15:05:44', '2026-05-07 15:05:44'),
(12, 'default', 'User sctv was created', 'App\\Models\\User', 7, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"supervisor_industri\", \"email\": \"sctv@gmail.com\", \"username\": \"sctv\"}}', NULL, '2026-05-07 15:06:44', '2026-05-07 15:06:44'),
(13, 'default', 'created', 'App\\Models\\Industri', 2, 'created', NULL, NULL, '{\"attributes\": {\"alamat\": \"Jakarta\", \"kontak_person\": \"Christy\", \"nama_perusahaan\": \"SCTV\"}}', NULL, '2026-05-07 15:06:45', '2026-05-07 15:06:45'),
(14, 'default', 'User oman was created', 'App\\Models\\User', 8, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"dosen_pembimbing\", \"email\": \"oman@krw.horizon.ac.id\", \"username\": \"oman\"}}', NULL, '2026-05-07 15:09:17', '2026-05-07 15:09:17'),
(15, 'default', 'User anwar was created', 'App\\Models\\User', 9, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"dosen_prodi\", \"email\": \"anwar@krw.horizon.ac.id\", \"username\": \"anwar\"}}', NULL, '2026-05-07 15:10:57', '2026-05-07 15:10:57'),
(16, 'default', 'Pendaftaran #1 was created', 'App\\Models\\Pendaftaran', 1, 'created', 'App\\Models\\User', 6, '{\"attributes\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}}', NULL, '2026-05-07 15:11:27', '2026-05-07 15:11:27'),
(17, 'default', 'Pendaftaran #2 was created', 'App\\Models\\Pendaftaran', 2, 'created', 'App\\Models\\User', 6, '{\"attributes\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}}', NULL, '2026-05-07 15:11:32', '2026-05-07 15:11:32'),
(18, 'default', 'Pendaftaran #1 was updated', 'App\\Models\\Pendaftaran', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}, \"attributes\": {\"status_seleksi\": \"diterima\", \"keterangan_industri\": \"123\"}}', NULL, '2026-05-07 15:11:54', '2026-05-07 15:11:54'),
(19, 'default', 'Magang Aktif #1 was created', 'App\\Models\\MagangAktif', 1, 'created', 'App\\Models\\User', 7, '{\"attributes\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"tanggal_selesai\": null, \"supervisor_kampus_id\": null, \"supervisor_industri_id\": null}}', NULL, '2026-05-07 15:11:55', '2026-05-07 15:11:55'),
(20, 'default', 'Magang Aktif #1 was updated', 'App\\Models\\MagangAktif', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 15:13:17', '2026-05-07 15:13:17'),
(21, 'internship', 'Industry agreement uploaded, awaiting student response', 'App\\Models\\MagangAktif', 1, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-07 15:13:17', '2026-05-07 15:13:17'),
(22, 'default', 'Magang Aktif #1 was updated', 'App\\Models\\MagangAktif', 1, 'updated', 'App\\Models\\User', 6, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 15:14:58', '2026-05-07 15:14:58'),
(23, 'internship', 'Student accepted and signed agreement', 'App\\Models\\MagangAktif', 1, NULL, 'App\\Models\\User', 6, '[]', NULL, '2026-05-07 15:14:58', '2026-05-07 15:14:58'),
(24, 'default', 'Pendaftaran #2 was updated', 'App\\Models\\Pendaftaran', 2, 'updated', 'App\\Models\\User', 3, '{\"old\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}, \"attributes\": {\"status_seleksi\": \"diterima\", \"keterangan_industri\": \"123\"}}', NULL, '2026-05-07 15:15:32', '2026-05-07 15:15:32'),
(25, 'default', 'Magang Aktif #2 was created', 'App\\Models\\MagangAktif', 2, 'created', 'App\\Models\\User', 3, '{\"attributes\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"tanggal_selesai\": null, \"supervisor_kampus_id\": null, \"supervisor_industri_id\": null}}', NULL, '2026-05-07 15:15:32', '2026-05-07 15:15:32'),
(26, 'default', 'Magang Aktif #2 was updated', 'App\\Models\\MagangAktif', 2, 'updated', 'App\\Models\\User', 3, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 15:15:50', '2026-05-07 15:15:50'),
(27, 'internship', 'Industry agreement uploaded but auto-rejected (student already accepted another)', 'App\\Models\\MagangAktif', 2, NULL, 'App\\Models\\User', 3, '[]', NULL, '2026-05-07 15:15:50', '2026-05-07 15:15:50'),
(28, 'default', 'Magang Aktif #1 was updated', 'App\\Models\\MagangAktif', 1, 'updated', 'App\\Models\\User', 1, '{\"old\": {\"supervisor_kampus_id\": null}, \"attributes\": {\"supervisor_kampus_id\": 3}}', NULL, '2026-05-07 15:32:08', '2026-05-07 15:32:08'),
(29, 'default', 'Magang Aktif #1 was updated', 'App\\Models\\MagangAktif', 1, 'updated', 'App\\Models\\User', 9, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 15:41:57', '2026-05-07 15:41:57'),
(30, 'default', 'User galang2 was created', 'App\\Models\\User', 10, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"mahasiswa\", \"email\": \"galang2@krw.horizon.ac.id\", \"username\": \"galang2\"}}', NULL, '2026-05-07 16:17:41', '2026-05-07 16:17:41'),
(31, 'default', 'created', 'App\\Models\\Mahasiswa', 3, 'created', NULL, NULL, '{\"attributes\": {\"nim\": \"4112755201240002\", \"prodi\": \"S1 Informatika\", \"cv_file_path\": null, \"nama_lengkap\": \"Sukmagama\"}}', NULL, '2026-05-07 16:17:41', '2026-05-07 16:17:41'),
(32, 'default', 'updated', 'App\\Models\\Mahasiswa', 3, 'updated', 'App\\Models\\User', 10, '{\"old\": {\"cv_file_path\": null}, \"attributes\": {\"cv_file_path\": \"documents/cv/3/DDuytLZHUcWQwHyzD51V1OKx7l88ujGaAil1N8WJ.pdf\"}}', NULL, '2026-05-07 16:17:54', '2026-05-07 16:17:54'),
(33, 'document', 'Document uploaded: Curriculum Vitae', 'App\\Models\\Document', 2, NULL, 'App\\Models\\User', 10, '{\"type\": \"cv\", \"original_name\": \"2_Group Assignment.pdf\"}', NULL, '2026-05-07 16:17:54', '2026-05-07 16:17:54'),
(34, 'default', 'Pendaftaran #3 was created', 'App\\Models\\Pendaftaran', 3, 'created', 'App\\Models\\User', 10, '{\"attributes\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}}', NULL, '2026-05-07 16:18:02', '2026-05-07 16:18:02'),
(35, 'default', 'Pendaftaran #3 was updated', 'App\\Models\\Pendaftaran', 3, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}, \"attributes\": {\"status_seleksi\": \"diterima\", \"keterangan_industri\": \"123\"}}', NULL, '2026-05-07 16:18:19', '2026-05-07 16:18:19'),
(36, 'default', 'Magang Aktif #3 was created', 'App\\Models\\MagangAktif', 3, 'created', 'App\\Models\\User', 7, '{\"attributes\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"tanggal_selesai\": null, \"supervisor_kampus_id\": null, \"supervisor_industri_id\": null}}', NULL, '2026-05-07 16:18:19', '2026-05-07 16:18:19'),
(37, 'default', 'Magang Aktif #3 was updated', 'App\\Models\\MagangAktif', 3, 'updated', 'App\\Models\\User', 7, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 16:18:32', '2026-05-07 16:18:32'),
(38, 'internship', 'Industry agreement uploaded, awaiting student response', 'App\\Models\\MagangAktif', 3, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-07 16:18:32', '2026-05-07 16:18:32'),
(39, 'default', 'Magang Aktif #3 was updated', 'App\\Models\\MagangAktif', 3, 'updated', 'App\\Models\\User', 10, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 16:19:01', '2026-05-07 16:19:01'),
(40, 'internship', 'Student accepted and signed agreement', 'App\\Models\\MagangAktif', 3, NULL, 'App\\Models\\User', 10, '[]', NULL, '2026-05-07 16:19:01', '2026-05-07 16:19:01'),
(41, 'default', 'Magang Aktif #3 was updated', 'App\\Models\\MagangAktif', 3, 'updated', 'App\\Models\\User', 1, '{\"old\": {\"supervisor_kampus_id\": null}, \"attributes\": {\"supervisor_kampus_id\": 3}}', NULL, '2026-05-07 16:20:23', '2026-05-07 16:20:23'),
(42, 'default', 'User galang3 was created', 'App\\Models\\User', 11, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"mahasiswa\", \"email\": \"galang3@krw.horizon.ac.id\", \"username\": \"galang3\"}}', NULL, '2026-05-07 16:30:57', '2026-05-07 16:30:57'),
(43, 'default', 'created', 'App\\Models\\Mahasiswa', 4, 'created', NULL, NULL, '{\"attributes\": {\"nim\": \"4112755201240003\", \"prodi\": \"S1 Sistem Informasi\", \"cv_file_path\": null, \"nama_lengkap\": \"Gama\"}}', NULL, '2026-05-07 16:30:57', '2026-05-07 16:30:57'),
(44, 'default', 'updated', 'App\\Models\\Mahasiswa', 4, 'updated', 'App\\Models\\User', 11, '{\"old\": {\"cv_file_path\": null}, \"attributes\": {\"cv_file_path\": \"documents/cv/4/9lOqzbtzUr1p2mpaDKyLwBlIbM5n1AHEpvR0SRGu.pdf\"}}', NULL, '2026-05-07 16:31:05', '2026-05-07 16:31:05'),
(45, 'document', 'Document uploaded: Curriculum Vitae', 'App\\Models\\Document', 3, NULL, 'App\\Models\\User', 11, '{\"type\": \"cv\", \"original_name\": \"2_Dialog_Scenario 2 (Maria and Boss).pdf\"}', NULL, '2026-05-07 16:31:05', '2026-05-07 16:31:05'),
(46, 'default', 'Pendaftaran #4 was created', 'App\\Models\\Pendaftaran', 4, 'created', 'App\\Models\\User', 11, '{\"attributes\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}}', NULL, '2026-05-07 16:31:11', '2026-05-07 16:31:11'),
(47, 'default', 'Pendaftaran #4 was updated', 'App\\Models\\Pendaftaran', 4, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}, \"attributes\": {\"status_seleksi\": \"diterima\", \"keterangan_industri\": \"123\"}}', NULL, '2026-05-07 16:31:32', '2026-05-07 16:31:32'),
(48, 'default', 'Magang Aktif #4 was created', 'App\\Models\\MagangAktif', 4, 'created', 'App\\Models\\User', 7, '{\"attributes\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"tanggal_selesai\": null, \"supervisor_kampus_id\": null, \"supervisor_industri_id\": null}}', NULL, '2026-05-07 16:31:32', '2026-05-07 16:31:32'),
(49, 'default', 'Magang Aktif #4 was updated', 'App\\Models\\MagangAktif', 4, 'updated', 'App\\Models\\User', 7, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 16:31:38', '2026-05-07 16:31:38'),
(50, 'internship', 'Industry agreement uploaded, awaiting student response', 'App\\Models\\MagangAktif', 4, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-07 16:31:39', '2026-05-07 16:31:39'),
(51, 'default', 'Magang Aktif #4 was updated', 'App\\Models\\MagangAktif', 4, 'updated', 'App\\Models\\User', 11, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-07 16:32:03', '2026-05-07 16:32:03'),
(52, 'internship', 'Student accepted and signed agreement', 'App\\Models\\MagangAktif', 4, NULL, 'App\\Models\\User', 11, '[]', NULL, '2026-05-07 16:32:03', '2026-05-07 16:32:03'),
(53, 'default', 'Magang Aktif #4 was updated', 'App\\Models\\MagangAktif', 4, 'updated', 'App\\Models\\User', 1, '{\"old\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"supervisor_kampus_id\": null}, \"attributes\": {\"tanggal_mulai\": \"2026-05-06T17:00:00.000000Z\", \"status_tahapan\": \"pelaksanaan\", \"supervisor_kampus_id\": 3}}', NULL, '2026-05-07 16:32:20', '2026-05-07 16:32:20'),
(54, 'logbook', 'Logbook approved by industry supervisor', 'App\\Models\\Logbook', 1, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-07 16:59:47', '2026-05-07 16:59:47'),
(55, 'completion_letter', 'Completion letter data saved by industry supervisor', 'App\\Models\\Sertifikat', 1, NULL, 'App\\Models\\User', 7, '{\"posisi\": \"Frontend Developer\", \"magang_id\": 4}', NULL, '2026-05-08 00:32:50', '2026-05-08 00:32:50'),
(56, 'signature', 'Digital signature stored', 'App\\Models\\Signature', 1, NULL, 'App\\Models\\User', 8, '[]', NULL, '2026-05-08 04:40:05', '2026-05-08 04:40:05'),
(57, 'signature', 'Digital signature stored', 'App\\Models\\Signature', 2, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-08 11:32:34', '2026-05-08 11:32:34'),
(58, 'default', 'User galang4 was created', 'App\\Models\\User', 12, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"mahasiswa\", \"email\": \"galang4@krw.horizon.ac.id\", \"username\": \"galang4\"}}', NULL, '2026-05-08 14:39:12', '2026-05-08 14:39:12'),
(59, 'default', 'created', 'App\\Models\\Mahasiswa', 5, 'created', NULL, NULL, '{\"attributes\": {\"nim\": \"4112755201240004\", \"prodi\": \"S1 Informatika\", \"cv_file_path\": null, \"nama_lengkap\": \"Galang 4\"}}', NULL, '2026-05-08 14:39:13', '2026-05-08 14:39:13'),
(60, 'default', 'updated', 'App\\Models\\Mahasiswa', 5, 'updated', 'App\\Models\\User', 12, '{\"old\": {\"cv_file_path\": null}, \"attributes\": {\"cv_file_path\": \"documents/cv/5/mBWOqzpGJxh8OtbKe20wVLByhX0jCGgIrtWpOEQl.pdf\"}}', NULL, '2026-05-08 14:57:18', '2026-05-08 14:57:18'),
(61, 'document', 'Document uploaded: Curriculum Vitae', 'App\\Models\\Document', 4, NULL, 'App\\Models\\User', 12, '{\"type\": \"cv\", \"original_name\": \"2_Dialog_Scenario 2 (Maria and Boss).pdf\"}', NULL, '2026-05-08 14:57:18', '2026-05-08 14:57:18'),
(62, 'default', 'Pendaftaran #5 was created', 'App\\Models\\Pendaftaran', 5, 'created', 'App\\Models\\User', 12, '{\"attributes\": {\"status_seleksi\": \"pending\", \"keterangan_industri\": null}}', NULL, '2026-05-08 14:57:25', '2026-05-08 14:57:25'),
(63, 'default', 'Pendaftaran #5 was updated', 'App\\Models\\Pendaftaran', 5, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status_seleksi\": \"pending\"}, \"attributes\": {\"status_seleksi\": \"diterima\"}}', NULL, '2026-05-08 14:57:42', '2026-05-08 14:57:42'),
(64, 'default', 'Magang Aktif #5 was created', 'App\\Models\\MagangAktif', 5, 'created', 'App\\Models\\User', 7, '{\"attributes\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"tanggal_selesai\": null, \"supervisor_kampus_id\": null, \"supervisor_industri_id\": null}}', NULL, '2026-05-08 14:57:42', '2026-05-08 14:57:42'),
(65, 'default', 'Magang Aktif #5 was updated', 'App\\Models\\MagangAktif', 5, 'updated', 'App\\Models\\User', 7, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-08 14:57:58', '2026-05-08 14:57:58'),
(66, 'internship', 'Industry agreement uploaded, awaiting student response', 'App\\Models\\MagangAktif', 5, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-08 14:57:59', '2026-05-08 14:57:59'),
(67, 'default', 'Magang Aktif #5 was updated', 'App\\Models\\MagangAktif', 5, 'updated', 'App\\Models\\User', 12, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-08 15:04:21', '2026-05-08 15:04:21'),
(68, 'internship', 'Student accepted and signed agreement', 'App\\Models\\MagangAktif', 5, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-08 15:04:21', '2026-05-08 15:04:21'),
(69, 'default', 'Magang Aktif #5 was updated', 'App\\Models\\MagangAktif', 5, 'updated', 'App\\Models\\User', 1, '{\"old\": {\"tanggal_mulai\": null, \"status_tahapan\": \"persiapan\", \"supervisor_kampus_id\": null, \"supervisor_industri_id\": null}, \"attributes\": {\"tanggal_mulai\": \"2026-05-07T17:00:00.000000Z\", \"status_tahapan\": \"pelaksanaan\", \"supervisor_kampus_id\": 3, \"supervisor_industri_id\": 7}}', NULL, '2026-05-08 16:33:55', '2026-05-08 16:33:55'),
(70, 'completion_letter', 'Completion letter data saved by industry supervisor', 'App\\Models\\Sertifikat', 2, NULL, 'App\\Models\\User', 7, '{\"posisi\": \"UI/UX Designer\", \"magang_id\": 5}', NULL, '2026-05-08 16:44:05', '2026-05-08 16:44:05'),
(71, 'default', 'updated', 'App\\Models\\Mahasiswa', 5, 'updated', 'App\\Models\\User', 12, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-08 17:59:02', '2026-05-08 17:59:02'),
(72, 'default', 'updated', 'App\\Models\\Mahasiswa', 5, 'updated', 'App\\Models\\User', 12, '{\"old\": {\"bio\": null, \"skills\": null, \"linkedin_url\": null}, \"attributes\": {\"bio\": \"Web Developer\", \"skills\": \"Fullstack Developer, Laravel Developer, React Developer\", \"linkedin_url\": \"https://www.linkedin.com/in/galang-sukmagama-42ba99338\"}}', NULL, '2026-05-09 16:10:21', '2026-05-09 16:10:21'),
(73, 'logbook', 'Logbook approved by industry supervisor', 'App\\Models\\Logbook', 2, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-09 16:16:06', '2026-05-09 16:16:06'),
(74, 'default', 'Evaluation #1 was created', 'App\\Models\\InternshipEvaluation', 1, 'created', 'App\\Models\\User', 7, '{\"attributes\": {\"status\": \"draft\", \"nilai_akhir\": null, \"tanggal_evaluasi\": \"2026-05-09T17:00:00.000000Z\", \"catatan_supervisor\": \"TES123\"}}', NULL, '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(75, 'default', 'Evaluation #1 was updated', 'App\\Models\\InternshipEvaluation', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"nilai_akhir\": null}, \"attributes\": {\"nilai_akhir\": \"87.60\"}}', NULL, '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(76, 'evaluation', 'Evaluation saved as draft', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 7, '{\"status\": \"draft\"}', NULL, '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(77, 'evaluation', 'Evaluation saved as draft', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 7, '{\"status\": \"draft\"}', NULL, '2026-05-10 09:14:21', '2026-05-10 09:14:21'),
(78, 'default', 'Evaluation #1 was updated', 'App\\Models\\InternshipEvaluation', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status\": \"draft\"}, \"attributes\": {\"status\": \"submitted\"}}', NULL, '2026-05-10 09:14:49', '2026-05-10 09:14:49'),
(79, 'evaluation', 'Evaluation submitted', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-10 09:14:49', '2026-05-10 09:14:49'),
(80, 'default', 'Evaluation #1 was updated', 'App\\Models\\InternshipEvaluation', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status\": \"submitted\"}, \"attributes\": {\"status\": \"finalized\"}}', NULL, '2026-05-10 09:15:10', '2026-05-10 09:15:10'),
(81, 'grading', 'Industry grade submitted', 'App\\Models\\Penilaian', 1, NULL, 'App\\Models\\User', 7, '{\"nilai_industri\": 87.6}', NULL, '2026-05-10 09:15:10', '2026-05-10 09:15:10'),
(82, 'evaluation', 'Evaluation finalized — score synced to penilaians', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 7, '{\"nilai_akhir\": 87.6}', NULL, '2026-05-10 09:15:10', '2026-05-10 09:15:10'),
(83, 'declaration', 'Declaration of Originality uploaded', 'App\\Models\\DeclarationOfOriginality', 1, NULL, 'App\\Models\\User', 12, '{\"original_name\": \"1_EXPERIENCE GROUP TASK GWEP.pdf\"}', NULL, '2026-05-10 14:24:06', '2026-05-10 14:24:06'),
(84, 'declaration', 'Declaration of Originality approved', 'App\\Models\\DeclarationOfOriginality', 1, NULL, 'App\\Models\\User', 8, '[]', NULL, '2026-05-10 15:10:11', '2026-05-10 15:10:11'),
(85, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-10 23:24:12', '2026-05-10 23:24:12'),
(86, 'laporan', 'Report review: Perlu Revisi', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 8, '{\"status\": \"revisi\", \"catatan\": \"Tes123\"}', NULL, '2026-05-11 02:52:07', '2026-05-11 02:52:07'),
(87, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 02:52:37', '2026-05-11 02:52:37'),
(88, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 02:53:48', '2026-05-11 02:53:48'),
(89, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 02:54:21', '2026-05-11 02:54:21'),
(90, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 03:02:24', '2026-05-11 03:02:24'),
(91, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 03:11:23', '2026-05-11 03:11:23'),
(92, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 04:49:47', '2026-05-11 04:49:47'),
(93, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 05:43:13', '2026-05-11 05:43:13'),
(94, 'laporan', 'Final report uploaded', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 05:45:08', '2026-05-11 05:45:08'),
(95, 'laporan', 'Report review: Disetujui', 'App\\Models\\LaporanAkhir', 1, NULL, 'App\\Models\\User', 8, '{\"status\": \"disetujui\", \"catatan\": \"Tes123\"}', NULL, '2026-05-11 05:48:15', '2026-05-11 05:48:15'),
(96, 'clearance', 'Clearance Issued By Company uploaded by industry', 'App\\Models\\InternshipClearance', 1, NULL, 'App\\Models\\User', 7, '{\"original_name\": \"2_Group Assignment.pdf\"}', NULL, '2026-05-11 07:16:08', '2026-05-11 07:16:08'),
(97, 'clearance', 'Clearance Issued By Company submitted for review by student', 'App\\Models\\InternshipClearance', 1, NULL, 'App\\Models\\User', 12, '[]', NULL, '2026-05-11 07:27:08', '2026-05-11 07:27:08'),
(98, 'clearance', 'Clearance Issued By Company approved', 'App\\Models\\InternshipClearance', 1, NULL, 'App\\Models\\User', 8, '[]', NULL, '2026-05-11 07:34:17', '2026-05-11 07:34:17'),
(99, 'default', 'Portfolio Evaluation #1 was created', 'App\\Models\\PortfolioEvaluation', 1, 'created', 'App\\Models\\User', 7, '{\"attributes\": {\"status\": \"draft\", \"comments\": \"ju-q93ur-2u3r9\", \"overall_score\": \"0.00\", \"qualification_result\": \"not_qualified\"}}', NULL, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(100, 'default', 'Portfolio Evaluation #1 was updated', 'App\\Models\\PortfolioEvaluation', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"overall_score\": \"0.00\"}, \"attributes\": {\"overall_score\": \"56.00\"}}', NULL, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(101, 'portfolio-evaluation', 'Portfolio evaluation saved as draft', 'App\\Models\\PortfolioEvaluation', 1, NULL, 'App\\Models\\User', 7, '{\"status\": \"draft\"}', NULL, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(102, 'default', 'Portfolio Evaluation #1 was updated', 'App\\Models\\PortfolioEvaluation', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status\": \"draft\"}, \"attributes\": {\"status\": \"submitted\"}}', NULL, '2026-05-11 08:48:37', '2026-05-11 08:48:37'),
(103, 'portfolio-evaluation', 'Portfolio evaluation submitted', 'App\\Models\\PortfolioEvaluation', 1, NULL, 'App\\Models\\User', 7, '[]', NULL, '2026-05-11 08:48:37', '2026-05-11 08:48:37'),
(104, 'default', 'Portfolio Evaluation #1 was updated', 'App\\Models\\PortfolioEvaluation', 1, 'updated', 'App\\Models\\User', 7, '{\"old\": {\"status\": \"submitted\"}, \"attributes\": {\"status\": \"finalized\"}}', NULL, '2026-05-11 08:49:00', '2026-05-11 08:49:00'),
(105, 'portfolio-evaluation', 'Portfolio evaluation finalized', 'App\\Models\\PortfolioEvaluation', 1, NULL, 'App\\Models\\User', 7, '{\"overall_score\": \"56.00\", \"qualification_result\": \"not_qualified\"}', NULL, '2026-05-11 08:49:00', '2026-05-11 08:49:00'),
(106, 'default', 'Internship Evaluation #1 was created', 'App\\Models\\InternshipEvaluation', 1, 'created', 'App\\Models\\User', 8, '{\"attributes\": {\"status\": \"draft\", \"pass_status\": \"fail\", \"overall_score\": \"0.00\", \"evaluation_date\": \"2026-05-11T17:00:00.000000Z\"}}', NULL, '2026-05-12 00:02:42', '2026-05-12 00:02:42'),
(107, 'default', 'Internship Evaluation #1 was updated', 'App\\Models\\InternshipEvaluation', 1, 'updated', 'App\\Models\\User', 8, '{\"old\": {\"pass_status\": \"fail\", \"overall_score\": \"0.00\"}, \"attributes\": {\"pass_status\": \"pass\", \"overall_score\": \"79.00\"}}', NULL, '2026-05-12 00:02:42', '2026-05-12 00:02:42'),
(108, 'internship-evaluation', 'Internship evaluation saved as draft', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 8, '{\"status\": \"draft\"}', NULL, '2026-05-12 00:02:42', '2026-05-12 00:02:42'),
(109, 'default', 'Internship Evaluation #1 was updated', 'App\\Models\\InternshipEvaluation', 1, 'updated', 'App\\Models\\User', 8, '{\"old\": {\"status\": \"draft\"}, \"attributes\": {\"status\": \"submitted\"}}', NULL, '2026-05-12 00:02:51', '2026-05-12 00:02:51'),
(110, 'internship-evaluation', 'Internship evaluation submitted', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 8, '[]', NULL, '2026-05-12 00:02:51', '2026-05-12 00:02:51'),
(111, 'default', 'Internship Evaluation #1 was updated', 'App\\Models\\InternshipEvaluation', 1, 'updated', 'App\\Models\\User', 8, '{\"old\": {\"status\": \"submitted\"}, \"attributes\": {\"status\": \"finalized\"}}', NULL, '2026-05-12 00:07:05', '2026-05-12 00:07:05'),
(112, 'internship-evaluation', 'Internship evaluation finalized', 'App\\Models\\InternshipEvaluation', 1, NULL, 'App\\Models\\User', 8, '{\"pass_status\": \"pass\", \"overall_score\": \"79.00\"}', NULL, '2026-05-12 00:07:05', '2026-05-12 00:07:05'),
(113, 'default', 'User sukmagama was created', 'App\\Models\\User', 13, 'created', NULL, NULL, '{\"attributes\": {\"role\": \"mahasiswa\", \"email\": \"sukma1@krw.horizon.ac.id\", \"username\": \"sukmagama\"}}', NULL, '2026-05-12 04:19:41', '2026-05-12 04:19:41'),
(114, 'default', 'created', 'App\\Models\\Mahasiswa', 6, 'created', NULL, NULL, '{\"attributes\": {\"bio\": null, \"nim\": \"4112755201240005\", \"prodi\": \"S1 Teknologi Informasi\", \"skills\": null, \"cv_file_path\": null, \"linkedin_url\": null, \"nama_lengkap\": \"Sukmagama\"}}', NULL, '2026-05-12 04:19:41', '2026-05-12 04:19:41'),
(115, 'default', 'updated', 'App\\Models\\Mahasiswa', 6, 'updated', 'App\\Models\\User', 13, '{\"old\": {\"cv_file_path\": null}, \"attributes\": {\"cv_file_path\": \"documents/cv/6/dY9KtkDjUhY0eZ8iDOYJhMx41xV2Yo47jlh6IDD9.pdf\"}}', NULL, '2026-05-12 04:20:20', '2026-05-12 04:20:20'),
(116, 'document', 'Document uploaded: Curriculum Vitae', 'App\\Models\\Document', 5, NULL, 'App\\Models\\User', 13, '{\"type\": \"cv\", \"original_name\": \"3_Listening.pdf\"}', NULL, '2026-05-12 04:20:21', '2026-05-12 04:20:21'),
(117, 'default', 'updated', 'App\\Models\\Mahasiswa', 6, 'updated', 'App\\Models\\User', 13, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-12 06:00:33', '2026-05-12 06:00:33'),
(118, 'default', 'updated', 'App\\Models\\Mahasiswa', 6, 'updated', 'App\\Models\\User', 13, '{\"old\": [], \"attributes\": []}', NULL, '2026-05-12 06:01:55', '2026-05-12 06:01:55');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('magang-horizon-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:27:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:18:\"pendaftaran.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"pendaftaran.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:18:\"pendaftaran.review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"magang.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:3;i:2;i:4;i:3;i:5;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:24:\"magang.assign-supervisor\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:17:\"magang.transition\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:14:\"logbook.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:12:\"logbook.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"logbook.approve\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"logbook.check\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:14:\"laporan.upload\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:12:\"laporan.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:4:{i:0;i:1;i:1;i:2;i:2;i:4;i:3;i:5;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:14:\"laporan.review\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:4;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:24:\"penilaian.grade-industry\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:22:\"penilaian.grade-campus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:5;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:16:\"penilaian.verify\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:14:\"penilaian.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:19:\"sertifikat.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:15:\"sertifikat.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:15:\"document.upload\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:17:\"document.download\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:5:{i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:15:\"document.delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:16:\"signature.create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:14:\"signature.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:11:\"user.manage\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:9:\"user.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:13:\"activity.view\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:5:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"student\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:8:\"industry\";s:1:\"c\";s:3:\"web\";}i:3;a:3:{s:1:\"a\";i:4;s:1:\"b\";s:12:\"supervisor_1\";s:1:\"c\";s:3:\"web\";}i:4;a:3:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"supervisor_2\";s:1:\"c\";s:3:\"web\";}}}', 1778630242);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `declaration_of_originalities`
--

CREATE TABLE `declaration_of_originalities` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_aktif_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `reviewer_id` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `rejection_note` text COLLATE utf8mb4_unicode_ci,
  `uploaded_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `declaration_of_originalities`
--

INSERT INTO `declaration_of_originalities` (`id`, `magang_aktif_id`, `file_path`, `original_filename`, `status`, `reviewer_id`, `reviewed_at`, `rejection_note`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(1, 5, 'declarations/5/SgklBcQoaNGZA2HlEhJIhGKzYQW3OIeuIdbXH7B5.pdf', '1_EXPERIENCE GROUP TASK GWEP.pdf', 'approved', 8, '2026-05-10 15:10:11', NULL, '2026-05-10 14:24:06', '2026-05-10 14:24:06', '2026-05-10 15:10:11');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint UNSIGNED NOT NULL,
  `documentable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documentable_id` bigint UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint UNSIGNED NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `documentable_type`, `documentable_id`, `type`, `file_path`, `original_name`, `file_size`, `mime_type`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Mahasiswa', 2, 'cv', 'documents/cv/2/4bpwuS0l4yC5mkjM4EhQHZoqIPJ4gO72fxpcfmNk.pdf', '2_Group Assignment.pdf', 44036, 'application/pdf', 6, '2026-05-07 15:05:44', '2026-05-07 15:05:44'),
(2, 'App\\Models\\Mahasiswa', 3, 'cv', 'documents/cv/3/DDuytLZHUcWQwHyzD51V1OKx7l88ujGaAil1N8WJ.pdf', '2_Group Assignment.pdf', 44036, 'application/pdf', 10, '2026-05-07 16:17:54', '2026-05-07 16:17:54'),
(3, 'App\\Models\\Mahasiswa', 4, 'cv', 'documents/cv/4/9lOqzbtzUr1p2mpaDKyLwBlIbM5n1AHEpvR0SRGu.pdf', '2_Dialog_Scenario 2 (Maria and Boss).pdf', 47332, 'application/pdf', 11, '2026-05-07 16:31:05', '2026-05-07 16:31:05'),
(4, 'App\\Models\\Mahasiswa', 5, 'cv', 'documents/cv/5/mBWOqzpGJxh8OtbKe20wVLByhX0jCGgIrtWpOEQl.pdf', '2_Dialog_Scenario 2 (Maria and Boss).pdf', 47332, 'application/pdf', 12, '2026-05-08 14:57:18', '2026-05-08 14:57:18'),
(5, 'App\\Models\\Mahasiswa', 6, 'cv', 'documents/cv/6/dY9KtkDjUhY0eZ8iDOYJhMx41xV2Yo47jlh6IDD9.pdf', '3_Listening.pdf', 55767, 'application/pdf', 13, '2026-05-12 04:20:20', '2026-05-12 04:20:20');

-- --------------------------------------------------------

--
-- Table structure for table `dosens`
--

CREATE TABLE `dosens` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dosen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dosens`
--

INSERT INTO `dosens` (`id`, `user_id`, `nip`, `nama_dosen`, `created_at`, `updated_at`) VALUES
(1, 4, '198501012020011001', 'Dr. Siti Aminah', '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(2, 5, '198801012020011002', 'Prof. Andi Rahman', '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(3, 8, '1213450001', 'Muhammad Jembar Jomantara', '2026-05-07 15:09:17', '2026-05-07 15:09:17'),
(4, 9, '1213450002', 'Anwar Hilman', '2026-05-07 15:10:57', '2026-05-07 15:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `industris`
--

CREATE TABLE `industris` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nama_perusahaan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `kontak_person` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `geofence_radius` int NOT NULL DEFAULT '500',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `industris`
--

INSERT INTO `industris` (`id`, `user_id`, `nama_perusahaan`, `alamat`, `kontak_person`, `latitude`, `longitude`, `geofence_radius`, `created_at`, `updated_at`) VALUES
(1, 3, 'PT Maju Mapan', 'Jl. Sudirman No. 123, Jakarta', 'Budi Santoso', NULL, NULL, 500, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(2, 7, 'SCTV', 'Jakarta', 'Christy', NULL, NULL, 500, '2026-05-07 15:06:44', '2026-05-07 15:06:44');

-- --------------------------------------------------------

--
-- Table structure for table `internship_clearances`
--

CREATE TABLE `internship_clearances` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_aktif_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'uploaded',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `reviewer_id` bigint UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `rejection_note` text COLLATE utf8mb4_unicode_ci,
  `uploaded_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internship_clearances`
--

INSERT INTO `internship_clearances` (`id`, `magang_aktif_id`, `file_path`, `original_filename`, `status`, `submitted_at`, `reviewer_id`, `reviewed_at`, `rejection_note`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(1, 5, 'internship-clearances/5/lu6T74z81xmBbeylhkpKoUZoDU44TolWnkMpcgxw.pdf', '2_Group Assignment.pdf', 'approved', '2026-05-11 07:27:08', 8, '2026-05-11 07:34:17', NULL, '2026-05-11 07:16:08', '2026-05-11 07:16:08', '2026-05-11 07:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `internship_evaluations`
--

CREATE TABLE `internship_evaluations` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_aktif_id` bigint UNSIGNED NOT NULL,
  `evaluator_id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluation_date` date NOT NULL,
  `overall_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `pass_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fail',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internship_evaluations`
--

INSERT INTO `internship_evaluations` (`id`, `magang_aktif_id`, `evaluator_id`, `company_name`, `department`, `position`, `evaluation_date`, `overall_score`, `pass_status`, `status`, `finalized_at`, `created_at`, `updated_at`) VALUES
(1, 5, 8, 'SCTV', 'Kaprodi', 'Web Development', '2026-05-12', '79.00', 'pass', 'finalized', '2026-05-12 00:07:05', '2026-05-12 00:02:41', '2026-05-12 00:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `internship_evaluation_comments`
--

CREATE TABLE `internship_evaluation_comments` (
  `id` bigint UNSIGNED NOT NULL,
  `internship_evaluation_id` bigint UNSIGNED NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `feedback` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internship_evaluation_comments`
--

INSERT INTO `internship_evaluation_comments` (`id`, `internship_evaluation_id`, `comments`, `feedback`, `created_at`, `updated_at`) VALUES
(1, 1, 'TES123', 'Mantap', '2026-05-12 00:02:42', '2026-05-12 00:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `internship_evaluation_scores`
--

CREATE TABLE `internship_evaluation_scores` (
  `id` bigint UNSIGNED NOT NULL,
  `internship_evaluation_id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `selected_rating` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numeric_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `internship_evaluation_scores`
--

INSERT INTO `internship_evaluation_scores` (`id`, `internship_evaluation_id`, `category`, `selected_rating`, `numeric_score`, `created_at`, `updated_at`) VALUES
(1, 1, 'performance_rating', 'exceeds', '36.00', '2026-05-12 00:02:42', '2026-05-12 00:02:42'),
(2, 1, 'relevance', 'exceeds', '15.00', '2026-05-12 00:02:42', '2026-05-12 00:02:42'),
(3, 1, 'extent', 'exceptional', '20.00', '2026-05-12 00:02:42', '2026-05-12 00:02:42'),
(4, 1, 'portfolio', 'exceeds', '8.00', '2026-05-12 00:02:42', '2026-05-12 00:02:42');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"06e4c6b9-6383-4b79-ba2c-a71913ad73fe\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"88f70ac6-8686-45a0-bf67-67884ed23db7\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778166718,\"delay\":null}', 0, NULL, 1778166718, 1778166718),
(2, 'default', '{\"uuid\":\"b3af3935-aa7e-4283-a3ab-1bc8112a5566\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"88f70ac6-8686-45a0-bf67-67884ed23db7\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778166718,\"delay\":null}', 0, NULL, 1778166718, 1778166718),
(3, 'default', '{\"uuid\":\"da351cc3-c88e-4ec1-bb6d-3913ecbc957f\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"116d135c-d72d-44c9-bbc3-66be6434de0b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778166797,\"delay\":null}', 0, NULL, 1778166797, 1778166797),
(4, 'default', '{\"uuid\":\"7af59d57-a0cd-40ab-af9d-cc35d3b73ccb\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"116d135c-d72d-44c9-bbc3-66be6434de0b\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778166797,\"delay\":null}', 0, NULL, 1778166797, 1778166797),
(5, 'default', '{\"uuid\":\"d1c85f1b-df18-4969-9a76-24fbeb301bfc\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"2df0df6d-1870-41e3-a71f-8ddc95866685\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778166898,\"delay\":null}', 0, NULL, 1778166898, 1778166898),
(6, 'default', '{\"uuid\":\"0fbbf2d2-0a91-4e40-9507-3545d7c78cad\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"2df0df6d-1870-41e3-a71f-8ddc95866685\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778166898,\"delay\":null}', 0, NULL, 1778166898, 1778166898),
(7, 'default', '{\"uuid\":\"d9d06985-0000-4e98-b0dd-f2b38dccfab7\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"e042df26-ad4d-49d5-9abd-12e912d75132\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778166932,\"delay\":null}', 0, NULL, 1778166932, 1778166932),
(8, 'default', '{\"uuid\":\"f40dfcc0-9599-4bc6-8947-250425d51a9e\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:6;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"e042df26-ad4d-49d5-9abd-12e912d75132\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778166932,\"delay\":null}', 0, NULL, 1778166932, 1778166932),
(9, 'default', '{\"uuid\":\"55a1f978-4561-41b5-b237-da204da76116\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"rejected\\\";s:2:\\\"id\\\";s:36:\\\"2c677fa0-e092-46ff-b11b-f3e79dc850d8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778166950,\"delay\":null}', 0, NULL, 1778166950, 1778166950),
(10, 'default', '{\"uuid\":\"4dea8ea1-a8f5-482f-878b-0a5765ebca0f\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"rejected\\\";s:2:\\\"id\\\";s:36:\\\"2c677fa0-e092-46ff-b11b-f3e79dc850d8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778166950,\"delay\":null}', 0, NULL, 1778166950, 1778166950),
(11, 'default', '{\"uuid\":\"f1548685-6de2-4fdb-a373-a147ab69a921\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:10;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"62979be1-cbcf-45b4-a43a-7ab6beccc5c7\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778170699,\"delay\":null}', 0, NULL, 1778170699, 1778170699),
(12, 'default', '{\"uuid\":\"07a2fc68-a612-4e90-87e5-18452b319819\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:10;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"62979be1-cbcf-45b4-a43a-7ab6beccc5c7\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778170699,\"delay\":null}', 0, NULL, 1778170699, 1778170699),
(13, 'default', '{\"uuid\":\"7a93d825-2077-408e-8406-12f887914dc3\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:10;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"7ffcb7cf-a57a-4a52-81ac-97d7bd69dfb0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778170712,\"delay\":null}', 0, NULL, 1778170712, 1778170712),
(14, 'default', '{\"uuid\":\"42355305-40a1-46c5-93be-fda667f60551\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:10;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"7ffcb7cf-a57a-4a52-81ac-97d7bd69dfb0\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778170712,\"delay\":null}', 0, NULL, 1778170712, 1778170712),
(15, 'default', '{\"uuid\":\"ea1ba93e-3c31-48f7-aec4-68c02caf6303\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"ddc3545d-42a4-47cb-bf61-e6333a89dc44\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778170742,\"delay\":null}', 0, NULL, 1778170742, 1778170742),
(16, 'default', '{\"uuid\":\"87470433-06c9-4489-9c76-60b0c36f2736\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"ddc3545d-42a4-47cb-bf61-e6333a89dc44\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778170742,\"delay\":null}', 0, NULL, 1778170742, 1778170742),
(17, 'default', '{\"uuid\":\"be89dcf4-e042-450a-9b88-f844006a7b6c\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"5f4207d4-c5f4-4491-aa84-a436cb9dc0b8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778171492,\"delay\":null}', 0, NULL, 1778171492, 1778171492),
(18, 'default', '{\"uuid\":\"4cfbcc5e-9bb1-499c-bfe6-0ea8a2f8cf5b\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"5f4207d4-c5f4-4491-aa84-a436cb9dc0b8\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778171492,\"delay\":null}', 0, NULL, 1778171492, 1778171492),
(19, 'default', '{\"uuid\":\"10553c43-bcc0-4980-97d7-fe9f8360ed57\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"0c6d5f1b-456b-48c9-bb2e-fe9c7a91695c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778171499,\"delay\":null}', 0, NULL, 1778171499, 1778171499),
(20, 'default', '{\"uuid\":\"813e2af0-c8cf-4ef7-a447-0760def165a2\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:11;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"0c6d5f1b-456b-48c9-bb2e-fe9c7a91695c\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778171499,\"delay\":null}', 0, NULL, 1778171499, 1778171499),
(21, 'default', '{\"uuid\":\"94f21e45-3f62-41ff-a895-8609e5ca4ee7\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"0c2c0ea0-e98d-4632-aec0-93c64f9b3f1e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778171524,\"delay\":null}', 0, NULL, 1778171524, 1778171524),
(22, 'default', '{\"uuid\":\"67cb2baa-6fd9-46bb-8e8c-f2693a2b2dd4\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"0c2c0ea0-e98d-4632-aec0-93c64f9b3f1e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778171524,\"delay\":null}', 0, NULL, 1778171524, 1778171524),
(23, 'default', '{\"uuid\":\"9234c774-8a78-4ce7-8cf8-d66d4001f8d0\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:12;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"c2395ba3-8397-48c2-8f60-4d88baa5b24e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778252266,\"delay\":null}', 0, NULL, 1778252266, 1778252266),
(24, 'default', '{\"uuid\":\"e9f77900-fbc3-4257-8fb8-77a20a5ff49e\",\"displayName\":\"App\\\\Notifications\\\\ApplicationStatusNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:12;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\ApplicationStatusNotification\\\":2:{s:60:\\\"\\u0000App\\\\Notifications\\\\ApplicationStatusNotification\\u0000pendaftaran\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\Pendaftaran\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"mahasiswa\\\";i:1;s:14:\\\"mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"c2395ba3-8397-48c2-8f60-4d88baa5b24e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778252266,\"delay\":null}', 0, NULL, 1778252266, 1778252266),
(25, 'default', '{\"uuid\":\"0da2d1e5-8a7b-4906-854c-c3a79e0eed99\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:12;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"1ac32348-5834-4de7-b30a-e16568b8b929\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778252279,\"delay\":null}', 0, NULL, 1778252279, 1778252279),
(26, 'default', '{\"uuid\":\"d503dc4b-c77b-4897-b68b-c0da00ed53d1\",\"displayName\":\"App\\\\Notifications\\\\AgreementUploadedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:12;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementUploadedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementUploadedNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:21:\\\"pendaftaran.mahasiswa\\\";i:2;s:26:\\\"pendaftaran.mahasiswa.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"1ac32348-5834-4de7-b30a-e16568b8b929\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778252279,\"delay\":null}', 0, NULL, 1778252279, 1778252279),
(27, 'default', '{\"uuid\":\"0cf55a98-c47c-4fbb-9c59-4545bdd09ef2\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"12f716b7-16d1-4de0-8692-dd1b3fc51c4e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778252661,\"delay\":null}', 0, NULL, 1778252661, 1778252661),
(28, 'default', '{\"uuid\":\"7ec02806-8bee-44cb-83cf-c2a05c7d0b70\",\"displayName\":\"App\\\\Notifications\\\\AgreementResponseNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:47:\\\"App\\\\Notifications\\\\AgreementResponseNotification\\\":3:{s:55:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000magang\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:22:\\\"App\\\\Models\\\\MagangAktif\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:3:{i:0;s:11:\\\"pendaftaran\\\";i:1;s:20:\\\"pendaftaran.industri\\\";i:2;s:25:\\\"pendaftaran.industri.user\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:63:\\\"\\u0000App\\\\Notifications\\\\AgreementResponseNotification\\u0000responseStatus\\\";s:8:\\\"accepted\\\";s:2:\\\"id\\\";s:36:\\\"12f716b7-16d1-4de0-8692-dd1b3fc51c4e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778252661,\"delay\":null}', 0, NULL, 1778252661, 1778252661),
(29, 'default', '{\"uuid\":\"b158f889-9a23-4217-b91d-352834a9aed1\",\"displayName\":\"App\\\\Notifications\\\\LogbookSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:46:\\\"App\\\\Notifications\\\\LogbookSubmittedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\LogbookSubmittedNotification\\u0000logbook\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Logbook\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"d3c64907-94e0-4107-aac8-b48247c63d32\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778343301,\"delay\":null}', 0, NULL, 1778343301, 1778343301),
(30, 'default', '{\"uuid\":\"db8594dc-80ae-4219-8842-fe385d9765b1\",\"displayName\":\"App\\\\Notifications\\\\LogbookSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:46:\\\"App\\\\Notifications\\\\LogbookSubmittedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\LogbookSubmittedNotification\\u0000logbook\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Logbook\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"d3c64907-94e0-4107-aac8-b48247c63d32\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778343301,\"delay\":null}', 0, NULL, 1778343301, 1778343301),
(31, 'default', '{\"uuid\":\"85edf099-68c7-4130-9314-c9703b701be2\",\"displayName\":\"App\\\\Notifications\\\\LogbookSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:46:\\\"App\\\\Notifications\\\\LogbookSubmittedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\LogbookSubmittedNotification\\u0000logbook\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Logbook\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"9c7ad0d2-6da7-44cc-8974-848f2fbb41d9\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:8:\\\"database\\\";}}\",\"batchId\":null},\"createdAt\":1778567770,\"delay\":null}', 0, NULL, 1778567770, 1778567770);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(32, 'default', '{\"uuid\":\"8d92134f-0f66-405c-b8b6-85d003da9277\",\"displayName\":\"App\\\\Notifications\\\\LogbookSubmittedNotification\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:7;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:46:\\\"App\\\\Notifications\\\\LogbookSubmittedNotification\\\":2:{s:55:\\\"\\u0000App\\\\Notifications\\\\LogbookSubmittedNotification\\u0000logbook\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Logbook\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"9c7ad0d2-6da7-44cc-8974-848f2fbb41d9\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\",\"batchId\":null},\"createdAt\":1778567770,\"delay\":null}', 0, NULL, 1778567770, 1778567770);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_akhirs`
--

CREATE TABLE `laporan_akhirs` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_id` bigint UNSIGNED NOT NULL,
  `file_laporan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_letter_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval_kampus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `catatan_revisi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `laporan_akhirs`
--

INSERT INTO `laporan_akhirs` (`id`, `magang_id`, `file_laporan`, `approval_letter_file`, `status_approval_kampus`, `catatan_revisi`, `created_at`, `updated_at`) VALUES
(1, 5, 'documents/laporan/HtBGeIAYraGTWCcif1EAGLzeckU9o7Xyu1LuTDHv.pdf', 'approval_letters/approval_letter_4112755201240004_1778478493.pdf', 'disetujui', 'Tes123', '2026-05-10 18:07:34', '2026-05-11 05:48:15');

-- --------------------------------------------------------

--
-- Table structure for table `logbooks`
--

CREATE TABLE `logbooks` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_id` bigint UNSIGNED NOT NULL,
  `tanggal_waktu` datetime NOT NULL,
  `kegiatan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_presensi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `is_approved_industri` tinyint(1) NOT NULL DEFAULT '0',
  `komentar_industri` text COLLATE utf8mb4_unicode_ci,
  `is_checked_kampus` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `logbooks`
--

INSERT INTO `logbooks` (`id`, `magang_id`, `tanggal_waktu`, `kegiatan`, `status_presensi`, `is_approved_industri`, `komentar_industri`, `is_checked_kampus`, `created_at`, `updated_at`) VALUES
(1, 4, '2026-05-07 23:33:00', 'Hi', 'hadir', 1, '123', 0, '2026-05-07 16:33:38', '2026-05-07 16:59:47'),
(2, 5, '2026-05-09 23:14:00', '123', 'hadir', 1, '123', 0, '2026-05-09 16:14:58', '2026-05-09 16:16:06'),
(3, 5, '2026-05-12 13:35:00', 'Urusan keluarga', 'izin', 0, NULL, 0, '2026-05-12 06:36:06', '2026-05-12 06:36:06');

-- --------------------------------------------------------

--
-- Table structure for table `magang_aktifs`
--

CREATE TABLE `magang_aktifs` (
  `id` bigint UNSIGNED NOT NULL,
  `pendaftaran_id` bigint UNSIGNED NOT NULL,
  `file_agreement_industri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_agreement_mahasiswa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_agreement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_tolak_agreement` text COLLATE utf8mb4_unicode_ci,
  `sk_pembimbing_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supervisor_kampus_id` bigint UNSIGNED DEFAULT NULL,
  `supervisor_industri_id` bigint UNSIGNED DEFAULT NULL,
  `status_tahapan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persiapan',
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `magang_aktifs`
--

INSERT INTO `magang_aktifs` (`id`, `pendaftaran_id`, `file_agreement_industri`, `file_agreement_mahasiswa`, `status_agreement`, `alasan_tolak_agreement`, `sk_pembimbing_path`, `supervisor_kampus_id`, `supervisor_industri_id`, `status_tahapan`, `tanggal_mulai`, `tanggal_selesai`, `created_at`, `updated_at`) VALUES
(1, 1, 'documents/agreements/industri/1/qYU7dN849nfOdGmkxEwKGNbvPiPbV9sJ6u0AwIPG.pdf', 'documents/agreements/mahasiswa/1/1L9En5f4XjQo09mrXvjzWzP7t56txa0juQSjM7jh.pdf', 'accepted', NULL, 'sk-pembimbing/S7h80cakR4M1xcP3RJvXxrZR3sawUQAh9IIL8RWd.pdf', 3, NULL, 'persiapan', NULL, NULL, '2026-05-07 15:11:55', '2026-05-07 15:41:57'),
(2, 2, 'documents/agreements/industri/2/EFPIjFxVzzhmJpJQ6dghuXuV9QeOkQLyVYQjLL7l.pdf', NULL, 'rejected', 'Otomatis ditolak karena mahasiswa telah menyetujui agreement dari perusahaan lain.', NULL, NULL, NULL, 'persiapan', NULL, NULL, '2026-05-07 15:15:32', '2026-05-07 15:15:50'),
(3, 3, 'documents/agreements/industri/3/PDYZvyYn1aW0eVsjZPn1ye6TQiH2PE6x9vR13N4U.pdf', 'documents/agreements/mahasiswa/3/Kk95vbahwjnsOYFfWLpsDKvsFSYOeQLEk0YSaqhU.pdf', 'accepted', NULL, NULL, 3, NULL, 'persiapan', NULL, NULL, '2026-05-07 16:18:19', '2026-05-07 16:20:23'),
(4, 4, 'documents/agreements/industri/4/RhieQTIVvLBKKeSJ8fAL1L2Dr3vbKZ6T6KnoPEX3.pdf', 'documents/agreements/mahasiswa/4/HrPedH5ZutBgww6In9OVn052bUyGcf4BtOc82eAR.pdf', 'accepted', NULL, NULL, 3, NULL, 'pelaksanaan', '2026-05-07', NULL, '2026-05-07 16:31:32', '2026-05-07 16:32:20'),
(5, 5, 'documents/agreements/industri/5/rBw70mMkIEY4yVd74LBmCOw0xWwwFF1DcCcQ8FB3.pdf', 'documents/agreements/mahasiswa/5/rf7UeEdcUy66e4y8SqulEIRCxNSZC1PSKRbcH2px.pdf', 'accepted', NULL, NULL, 3, 7, 'pelaksanaan', '2026-05-08', NULL, '2026-05-08 14:57:42', '2026-05-08 16:33:54');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswas`
--

CREATE TABLE `mahasiswas` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prodi` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nomor_hp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `skills` text COLLATE utf8mb4_unicode_ci,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mahasiswas`
--

INSERT INTO `mahasiswas` (`id`, `user_id`, `nim`, `nama_lengkap`, `prodi`, `nomor_hp`, `profile_photo_path`, `bio`, `skills`, `linkedin_url`, `cv_file_path`, `created_at`, `updated_at`) VALUES
(1, 2, '2024001', 'Ahmad Fauzi', 'Teknik Informatika', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(2, 6, '4112755201240001', 'Galang Sukmagama', 'S1 Informatika', NULL, NULL, NULL, NULL, NULL, 'documents/cv/2/4bpwuS0l4yC5mkjM4EhQHZoqIPJ4gO72fxpcfmNk.pdf', '2026-05-07 15:05:28', '2026-05-07 15:05:44'),
(3, 10, '4112755201240002', 'Sukmagama', 'S1 Informatika', NULL, NULL, NULL, NULL, NULL, 'documents/cv/3/DDuytLZHUcWQwHyzD51V1OKx7l88ujGaAil1N8WJ.pdf', '2026-05-07 16:17:41', '2026-05-07 16:17:54'),
(4, 11, '4112755201240003', 'Gama', 'S1 Sistem Informasi', NULL, NULL, NULL, NULL, NULL, 'documents/cv/4/9lOqzbtzUr1p2mpaDKyLwBlIbM5n1AHEpvR0SRGu.pdf', '2026-05-07 16:30:57', '2026-05-07 16:31:05'),
(5, 12, '4112755201240004', 'Galang 4', 'S1 Informatika', '081910097010', 'profile-photos/5/eUX6LNYj8wxbqHi8vfpWtKg4auhDQAvMKLEma2A3.png', 'Web Developer', 'Fullstack Developer, Laravel Developer, React Developer', 'https://www.linkedin.com/in/galang-sukmagama-42ba99338', 'documents/cv/5/mBWOqzpGJxh8OtbKe20wVLByhX0jCGgIrtWpOEQl.pdf', '2026-05-08 14:39:13', '2026-05-09 16:10:21'),
(6, 13, '4112755201240005', 'Sukmagama', 'S1 Teknologi Informasi', NULL, NULL, NULL, NULL, NULL, 'documents/cv/6/dY9KtkDjUhY0eZ8iDOYJhMx41xV2Yo47jlh6IDD9.pdf', '2026-05-12 04:19:41', '2026-05-12 06:01:55');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_24_000001_create_mahasiswas_table', 1),
(5, '2026_04_24_000002_create_industris_table', 1),
(6, '2026_04_24_000003_create_dosens_table', 1),
(7, '2026_04_24_000004_create_pendaftarans_table', 1),
(8, '2026_04_24_000005_create_magang_aktifs_table', 1),
(9, '2026_04_24_000006_create_logbooks_table', 1),
(10, '2026_04_24_000007_create_laporan_akhirs_table', 1),
(11, '2026_04_24_000008_create_penilaians_table', 1),
(12, '2026_04_24_000009_create_sertifikats_table', 1),
(13, '2026_04_24_000010_create_signatures_table', 1),
(14, '2026_04_24_000011_create_documents_table', 1),
(15, '2026_04_24_000012_create_permission_tables', 1),
(16, '2026_04_24_000013_create_activity_log_table', 1),
(17, '2026_04_24_000014_create_notifications_table', 1),
(18, '2026_04_27_154049_create_periodes_table', 1),
(19, '2026_04_29_000001_add_agreement_status_to_magang_aktifs', 1),
(20, '2026_05_07_110605_create_pembimbing_assignments_table', 1),
(21, '2026_05_07_110605_create_surat_keputusan_pembimbings_table', 1),
(22, '2026_05_08_000001_add_completion_letter_fields_to_sertifikats_table', 2),
(23, '2026_05_09_000001_add_profile_fields_to_mahasiswas_table', 3),
(24, '2026_05_10_000001_create_internship_evaluations_table', 4),
(25, '2026_05_10_000002_create_evaluation_scores_table', 4),
(26, '2026_05_11_000001_create_declaration_of_originalities_table', 5),
(27, '2026_05_10_232856_add_approval_fields_to_laporan_akhirs_table', 6),
(28, '2026_05_11_064351_add_approval_letter_file_to_laporan_akhirs_table', 6),
(29, '2026_05_11_100001_create_internship_clearances_table', 7),
(30, '2026_05_11_150001_create_portfolio_evaluations_table', 8),
(31, '2026_05_12_000001_rename_internship_to_performance_evaluations', 9),
(32, '2026_05_12_000002_create_new_internship_evaluations_table', 10);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(5, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 8),
(5, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 12),
(2, 'App\\Models\\User', 13);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembimbing_assignments`
--

CREATE TABLE `pembimbing_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_aktif_id` bigint UNSIGNED NOT NULL,
  `dosen_id` bigint UNSIGNED NOT NULL,
  `assigned_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pembimbing_assignments`
--

INSERT INTO `pembimbing_assignments` (`id`, `magang_aktif_id`, `dosen_id`, `assigned_by`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, '2026-05-07 15:32:08', '2026-05-07 15:32:08'),
(2, 3, 3, 1, '2026-05-07 16:20:24', '2026-05-07 16:20:24'),
(3, 4, 3, 1, '2026-05-07 16:32:20', '2026-05-07 16:32:20'),
(4, 5, 3, 1, '2026-05-08 16:33:55', '2026-05-08 16:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftarans`
--

CREATE TABLE `pendaftarans` (
  `id` bigint UNSIGNED NOT NULL,
  `mahasiswa_id` bigint UNSIGNED NOT NULL,
  `industri_id` bigint UNSIGNED NOT NULL,
  `status_seleksi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `keterangan_industri` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pendaftarans`
--

INSERT INTO `pendaftarans` (`id`, `mahasiswa_id`, `industri_id`, `status_seleksi`, `keterangan_industri`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'diterima', '123', '2026-05-07 15:11:27', '2026-05-07 15:11:54'),
(2, 2, 1, 'diterima', '123', '2026-05-07 15:11:32', '2026-05-07 15:15:32'),
(3, 3, 2, 'diterima', '123', '2026-05-07 16:18:02', '2026-05-07 16:18:19'),
(4, 4, 2, 'diterima', '123', '2026-05-07 16:31:11', '2026-05-07 16:31:32'),
(5, 5, 2, 'diterima', NULL, '2026-05-08 14:57:25', '2026-05-08 14:57:42');

-- --------------------------------------------------------

--
-- Table structure for table `penilaians`
--

CREATE TABLE `penilaians` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_id` bigint UNSIGNED NOT NULL,
  `nilai_industri` decimal(5,2) DEFAULT NULL,
  `nilai_kampus` decimal(5,2) DEFAULT NULL,
  `status_verifikasi_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `penilaians`
--

INSERT INTO `penilaians` (`id`, `magang_id`, `nilai_industri`, `nilai_kampus`, `status_verifikasi_admin`, `created_at`, `updated_at`) VALUES
(1, 5, '87.60', NULL, 0, '2026-05-10 09:15:10', '2026-05-10 09:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `performance_evaluations`
--

CREATE TABLE `performance_evaluations` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_id` bigint UNSIGNED NOT NULL,
  `supervisor_id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `catatan_supervisor` text COLLATE utf8mb4_unicode_ci,
  `tanggal_evaluasi` date DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_evaluations`
--

INSERT INTO `performance_evaluations` (`id`, `magang_id`, `supervisor_id`, `status`, `nilai_akhir`, `catatan_supervisor`, `tanggal_evaluasi`, `finalized_at`, `created_at`, `updated_at`) VALUES
(1, 5, 7, 'finalized', '87.60', 'TES123', '2026-05-10', '2026-05-10 09:15:10', '2026-05-10 08:53:38', '2026-05-10 09:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `performance_evaluation_scores`
--

CREATE TABLE `performance_evaluation_scores` (
  `id` bigint UNSIGNED NOT NULL,
  `performance_evaluation_id` bigint UNSIGNED NOT NULL,
  `komponen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `performance_evaluation_scores`
--

INSERT INTO `performance_evaluation_scores` (`id`, `performance_evaluation_id`, `komponen`, `nilai`, `created_at`, `updated_at`) VALUES
(1, 1, 'pengetahuan_kemahiran', '80.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(2, 1, 'produktivitas', '85.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(3, 1, 'kualitas_kerja', '90.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(4, 1, 'komunikasi_presentasi', '95.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(5, 1, 'kehadiran_ketepatan_waktu', '99.99', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(6, 1, 'inisiatif_kreativitas', '75.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(7, 1, 'kemampuan_dibimbing', '90.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(8, 1, 'kemampuan_beradaptasi', '93.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(9, 1, 'keterampilan_interpersonal', '88.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39'),
(10, 1, 'penampilan', '80.00', '2026-05-10 08:53:39', '2026-05-10 08:53:39');

-- --------------------------------------------------------

--
-- Table structure for table `periodes`
--

CREATE TABLE `periodes` (
  `id` bigint UNSIGNED NOT NULL,
  `tahun_akademik` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('ganjil','genap','pendek') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_buka` date NOT NULL,
  `tanggal_tutup` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periodes`
--

INSERT INTO `periodes` (`id`, `tahun_akademik`, `semester`, `tanggal_buka`, `tanggal_tutup`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2026/2027', 'ganjil', '2026-05-07', '2026-08-07', 1, '2026-05-07 15:04:22', '2026-05-07 15:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'pendaftaran.create', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(2, 'pendaftaran.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(3, 'pendaftaran.review', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(4, 'magang.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(5, 'magang.assign-supervisor', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(6, 'magang.transition', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(7, 'logbook.create', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(8, 'logbook.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(9, 'logbook.approve', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(10, 'logbook.check', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(11, 'laporan.upload', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(12, 'laporan.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(13, 'laporan.review', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(14, 'penilaian.grade-industry', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(15, 'penilaian.grade-campus', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(16, 'penilaian.verify', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(17, 'penilaian.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(18, 'sertifikat.generate', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(19, 'sertifikat.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(20, 'document.upload', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(21, 'document.download', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(22, 'document.delete', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(23, 'signature.create', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(24, 'signature.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(25, 'user.manage', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(26, 'user.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(27, 'activity.view', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_evaluations`
--

CREATE TABLE `portfolio_evaluations` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_aktif_id` bigint UNSIGNED NOT NULL,
  `evaluator_id` bigint UNSIGNED NOT NULL,
  `evaluator_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluation_date` date NOT NULL,
  `overall_score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `qualification_result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_qualified',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `comments` text COLLATE utf8mb4_unicode_ci,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_evaluations`
--

INSERT INTO `portfolio_evaluations` (`id`, `magang_aktif_id`, `evaluator_id`, `evaluator_type`, `company_name`, `department`, `position`, `evaluation_date`, `overall_score`, `qualification_result`, `status`, `comments`, `finalized_at`, `created_at`, `updated_at`) VALUES
(1, 5, 7, 'industry_supervisor', 'SCTV', 'Kaprodi', 'Kaprodi', '2026-05-11', '56.00', 'not_qualified', 'finalized', 'ju-q93ur-2u3r9', '2026-05-11 08:49:00', '2026-05-11 08:48:26', '2026-05-11 08:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_evaluation_scores`
--

CREATE TABLE `portfolio_evaluation_scores` (
  `id` bigint UNSIGNED NOT NULL,
  `portfolio_evaluation_id` bigint UNSIGNED NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selected_rating` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numeric_score` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `portfolio_evaluation_scores`
--

INSERT INTO `portfolio_evaluation_scores` (`id`, `portfolio_evaluation_id`, `category`, `sub_category`, `selected_rating`, `numeric_score`, `created_at`, `updated_at`) VALUES
(1, 1, 'portfolio_contents', 'experience', 'exceptional', 60, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(2, 1, 'portfolio_contents', 'projects', 'exceeds', 45, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(3, 1, 'portfolio_contents', 'certifications', 'exceptional', 60, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(4, 1, 'portfolio_contents', 'activities', 'nears', 15, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(5, 1, 'format_organization', NULL, 'below', 1, '2026-05-11 08:48:26', '2026-05-11 08:48:26'),
(6, 1, 'academic_integrity', NULL, 'meets', 10, '2026-05-11 08:48:26', '2026-05-11 08:48:26');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(2, 'student', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(3, 'industry', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(4, 'supervisor_1', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(5, 'supervisor_2', 'web', '2026-05-07 15:03:28', '2026-05-07 15:03:28');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(1, 2),
(2, 2),
(7, 2),
(8, 2),
(11, 2),
(12, 2),
(17, 2),
(19, 2),
(20, 2),
(21, 2),
(23, 2),
(24, 2),
(2, 3),
(3, 3),
(4, 3),
(8, 3),
(9, 3),
(14, 3),
(17, 3),
(20, 3),
(21, 3),
(4, 4),
(8, 4),
(10, 4),
(12, 4),
(13, 4),
(17, 4),
(21, 4),
(4, 5),
(12, 5),
(15, 5),
(17, 5),
(21, 5);

-- --------------------------------------------------------

--
-- Table structure for table `sertifikats`
--

CREATE TABLE `sertifikats` (
  `id` bigint UNSIGNED NOT NULL,
  `magang_id` bigint UNSIGNED NOT NULL,
  `nomor_sertifikat` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_sertifikat_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_terbit` date DEFAULT NULL,
  `posisi_magang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi_tugas` text COLLATE utf8mb4_unicode_ci,
  `komentar_penutup` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sertifikats`
--

INSERT INTO `sertifikats` (`id`, `magang_id`, `nomor_sertifikat`, `file_sertifikat_path`, `tanggal_terbit`, `posisi_magang`, `departemen`, `deskripsi_tugas`, `komentar_penutup`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, NULL, NULL, 'Frontend Developer', 'TES123', 'TES123', 'MANTAP', '2026-05-08 00:32:50', '2026-05-08 00:32:50'),
(2, 5, NULL, NULL, NULL, 'UI/UX Designer', 'Designer Departemen', 'Membuat mockup dan prototype bla bla bla...', 'Mantap karyawan', '2026-05-08 16:44:05', '2026-05-08 16:44:05');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('1KzrMQqY09DAVhAAQykCvJwIdD5UHeybF85SmVFl', NULL, '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36 Edg/148.0.0.0', 'eyJfdG9rZW4iOiI1TFNJQUV6cHJoMVRSNGM3am85R0E0dlVoVTJzQWlSZDB3T3owWWd2IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbWFnYW5nLWhvcml6b24udGVzdFwvbG9naW4iLCJyb3V0ZSI6ImxvZ2luIn19', 1778568902);

-- --------------------------------------------------------

--
-- Table structure for table `signatures`
--

CREATE TABLE `signatures` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `signature_image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `signatures`
--

INSERT INTO `signatures` (`id`, `user_id`, `signature_image_path`, `signed_at`, `created_at`, `updated_at`) VALUES
(1, 8, 'signatures/8/sig_69fd692343628.png', '2026-05-08 04:40:04', '2026-05-08 04:40:04', '2026-05-08 04:40:04'),
(2, 7, 'signatures/7/sig_69fdc9d22a4e0.png', '2026-05-08 11:32:34', '2026-05-08 11:32:34', '2026-05-08 11:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `surat_keputusan_pembimbings`
--

CREATE TABLE `surat_keputusan_pembimbings` (
  `id` bigint UNSIGNED NOT NULL,
  `assignment_id` bigint UNSIGNED NOT NULL,
  `nomor_sk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_sk` date NOT NULL,
  `file_sk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `surat_keputusan_pembimbings`
--

INSERT INTO `surat_keputusan_pembimbings` (`id`, `assignment_id`, `nomor_sk`, `tanggal_sk`, `file_sk`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 1, '001-SK-2026', '2026-05-07', 'sk-pembimbing/S7h80cakR4M1xcP3RJvXxrZR3sawUQAh9IIL8RWd.pdf', 9, '2026-05-07 15:41:57', '2026-05-07 15:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mahasiswa',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@magang-horizon.test', NULL, '$2y$12$lJ1u4BkiYm66kJSOq.uJS.pPoVlctvjHNf6gvKvAa5UEKjs5/FMam', 'admin', NULL, '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(2, 'mahasiswa01', 'mahasiswa01@magang-horizon.test', NULL, '$2y$12$p1BBkrHUyzmcrap6cqOuLe8g26gZDyBdehTuXTqkCobVSHgkMuKj.', 'mahasiswa', NULL, '2026-05-07 15:03:28', '2026-05-07 15:03:28'),
(3, 'ptmajumapan', 'industri@magang-horizon.test', NULL, '$2y$12$KSmEbcV1UBuQdkdxS99oWOyD.nIsrZd7MOHwU3l84OyCogHm0o1HK', 'supervisor_industri', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(4, 'dosenpembimbing01', 'dosen1@magang-horizon.test', NULL, '$2y$12$pNjk03Ln.SDcK.zry2gdo.OpEZXWiGgoATgB553CFjnOToDG2p9sO', 'dosen_pembimbing', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(5, 'dosenprodi01', 'dosen2@magang-horizon.test', NULL, '$2y$12$7eFFIjeT7I2kmdZJgWdmNuFfDLk7N.pa/TrwX5qN8YC1cgGsvpTnq', 'dosen_prodi', NULL, '2026-05-07 15:03:29', '2026-05-07 15:03:29'),
(6, 'galang', 'galang@krw.horizon.ac.id', NULL, '$2y$12$yZK/E3dExQeZ1wQEQ65NNevdEe4AT1VY1UFtGXi/VKSs1ShoOuu66', 'mahasiswa', NULL, '2026-05-07 15:05:27', '2026-05-07 15:05:27'),
(7, 'sctv', 'sctv@gmail.com', NULL, '$2y$12$HSC4RUGYWdkrziruaLDf2OVAARDzBzn.r1a26YPKRGxWbFkTXsogG', 'supervisor_industri', NULL, '2026-05-07 15:06:44', '2026-05-07 15:06:44'),
(8, 'oman', 'oman@krw.horizon.ac.id', NULL, '$2y$12$/CmobiZXZKMbD615bsTog.9oVXgX1pgi3PuFBwMs8OzcZ2O1qPgoi', 'dosen_pembimbing', NULL, '2026-05-07 15:09:17', '2026-05-07 15:09:17'),
(9, 'anwar', 'anwar@krw.horizon.ac.id', NULL, '$2y$12$1pGBzpm2AjLLk/P6B9rYge3XCkOuefAUPb6rkxNc9rOWvPy9OHTrC', 'dosen_prodi', NULL, '2026-05-07 15:10:57', '2026-05-07 15:10:57'),
(10, 'galang2', 'galang2@krw.horizon.ac.id', NULL, '$2y$12$PsNIt2Oi7HY31UsvmMmWX.ftLX6hU.TuvatMrmmJVMRKEnu0fY/Pm', 'mahasiswa', NULL, '2026-05-07 16:17:41', '2026-05-07 16:17:41'),
(11, 'galang3', 'galang3@krw.horizon.ac.id', NULL, '$2y$12$RlFNs/arXdmxG5wN7hHYv.UIuhEbvAXdqVTsbHolwy0G8..uhc/3m', 'mahasiswa', NULL, '2026-05-07 16:30:57', '2026-05-07 16:30:57'),
(12, 'galang4', 'galang4@krw.horizon.ac.id', NULL, '$2y$12$ntfu/OZsRI1mZIz./tq6YuuriC7GhP2xo7NYF/MCjsDaMFG7bFNdS', 'mahasiswa', NULL, '2026-05-08 14:39:11', '2026-05-08 14:39:11'),
(13, 'sukmagama', 'sukma1@krw.horizon.ac.id', NULL, '$2y$12$h2KfSKCdKuPEvt4CGKc5C.cAqZNHu1Ov9xqsgdgfXUv0Kcur8VrFW', 'mahasiswa', NULL, '2026-05-12 04:19:41', '2026-05-12 04:19:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `declaration_of_originalities`
--
ALTER TABLE `declaration_of_originalities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `declaration_of_originalities_magang_aktif_id_unique` (`magang_aktif_id`),
  ADD KEY `declaration_of_originalities_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  ADD KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `documents_documentable_type_documentable_id_type_index` (`documentable_type`,`documentable_id`,`type`);

--
-- Indexes for table `dosens`
--
ALTER TABLE `dosens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dosens_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `dosens_nip_unique` (`nip`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `industris`
--
ALTER TABLE `industris`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `industris_user_id_unique` (`user_id`);

--
-- Indexes for table `internship_clearances`
--
ALTER TABLE `internship_clearances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `internship_clearances_magang_aktif_id_unique` (`magang_aktif_id`),
  ADD KEY `internship_clearances_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `internship_evaluations`
--
ALTER TABLE `internship_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `internship_evaluations_magang_aktif_id_unique` (`magang_aktif_id`),
  ADD KEY `internship_evaluations_evaluator_id_index` (`evaluator_id`),
  ADD KEY `internship_evaluations_status_index` (`status`);

--
-- Indexes for table `internship_evaluation_comments`
--
ALTER TABLE `internship_evaluation_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `internship_evaluation_comments_internship_evaluation_id_foreign` (`internship_evaluation_id`);

--
-- Indexes for table `internship_evaluation_scores`
--
ALTER TABLE `internship_evaluation_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `internship_eval_score_unique` (`internship_evaluation_id`,`category`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_akhirs`
--
ALTER TABLE `laporan_akhirs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `laporan_akhirs_magang_id_unique` (`magang_id`);

--
-- Indexes for table `logbooks`
--
ALTER TABLE `logbooks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logbooks_magang_id_tanggal_waktu_index` (`magang_id`,`tanggal_waktu`);

--
-- Indexes for table `magang_aktifs`
--
ALTER TABLE `magang_aktifs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `magang_aktifs_pendaftaran_id_unique` (`pendaftaran_id`),
  ADD KEY `magang_aktifs_supervisor_kampus_id_foreign` (`supervisor_kampus_id`),
  ADD KEY `magang_aktifs_supervisor_industri_id_foreign` (`supervisor_industri_id`);

--
-- Indexes for table `mahasiswas`
--
ALTER TABLE `mahasiswas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mahasiswas_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `mahasiswas_nim_unique` (`nim`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pembimbing_assignments`
--
ALTER TABLE `pembimbing_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembimbing_assignments_magang_aktif_id_foreign` (`magang_aktif_id`),
  ADD KEY `pembimbing_assignments_dosen_id_foreign` (`dosen_id`),
  ADD KEY `pembimbing_assignments_assigned_by_foreign` (`assigned_by`);

--
-- Indexes for table `pendaftarans`
--
ALTER TABLE `pendaftarans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pendaftarans_industri_id_foreign` (`industri_id`),
  ADD KEY `pendaftarans_mahasiswa_id_industri_id_status_seleksi_index` (`mahasiswa_id`,`industri_id`,`status_seleksi`);

--
-- Indexes for table `penilaians`
--
ALTER TABLE `penilaians`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `penilaians_magang_id_unique` (`magang_id`);

--
-- Indexes for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `internship_evaluations_magang_id_unique` (`magang_id`),
  ADD KEY `internship_evaluations_supervisor_id_index` (`supervisor_id`),
  ADD KEY `internship_evaluations_status_index` (`status`);

--
-- Indexes for table `performance_evaluation_scores`
--
ALTER TABLE `performance_evaluation_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `evaluation_scores_evaluation_id_komponen_unique` (`performance_evaluation_id`,`komponen`);

--
-- Indexes for table `periodes`
--
ALTER TABLE `periodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `portfolio_evaluations`
--
ALTER TABLE `portfolio_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portfolio_evaluations_magang_aktif_id_unique` (`magang_aktif_id`),
  ADD KEY `portfolio_evaluations_evaluator_id_foreign` (`evaluator_id`);

--
-- Indexes for table `portfolio_evaluation_scores`
--
ALTER TABLE `portfolio_evaluation_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `portfolio_score_unique` (`portfolio_evaluation_id`,`category`,`sub_category`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sertifikats`
--
ALTER TABLE `sertifikats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sertifikats_magang_id_unique` (`magang_id`),
  ADD UNIQUE KEY `sertifikats_nomor_sertifikat_unique` (`nomor_sertifikat`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `signatures`
--
ALTER TABLE `signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `signatures_user_id_index` (`user_id`);

--
-- Indexes for table `surat_keputusan_pembimbings`
--
ALTER TABLE `surat_keputusan_pembimbings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surat_keputusan_pembimbings_assignment_id_foreign` (`assignment_id`),
  ADD KEY `surat_keputusan_pembimbings_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `declaration_of_originalities`
--
ALTER TABLE `declaration_of_originalities`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dosens`
--
ALTER TABLE `dosens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `industris`
--
ALTER TABLE `industris`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `internship_clearances`
--
ALTER TABLE `internship_clearances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `internship_evaluations`
--
ALTER TABLE `internship_evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `internship_evaluation_comments`
--
ALTER TABLE `internship_evaluation_comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `internship_evaluation_scores`
--
ALTER TABLE `internship_evaluation_scores`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `laporan_akhirs`
--
ALTER TABLE `laporan_akhirs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logbooks`
--
ALTER TABLE `logbooks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `magang_aktifs`
--
ALTER TABLE `magang_aktifs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mahasiswas`
--
ALTER TABLE `mahasiswas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `pembimbing_assignments`
--
ALTER TABLE `pembimbing_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pendaftarans`
--
ALTER TABLE `pendaftarans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `penilaians`
--
ALTER TABLE `penilaians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `performance_evaluation_scores`
--
ALTER TABLE `performance_evaluation_scores`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `periodes`
--
ALTER TABLE `periodes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `portfolio_evaluations`
--
ALTER TABLE `portfolio_evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `portfolio_evaluation_scores`
--
ALTER TABLE `portfolio_evaluation_scores`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sertifikats`
--
ALTER TABLE `sertifikats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `signatures`
--
ALTER TABLE `signatures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `surat_keputusan_pembimbings`
--
ALTER TABLE `surat_keputusan_pembimbings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `declaration_of_originalities`
--
ALTER TABLE `declaration_of_originalities`
  ADD CONSTRAINT `declaration_of_originalities_magang_aktif_id_foreign` FOREIGN KEY (`magang_aktif_id`) REFERENCES `magang_aktifs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `declaration_of_originalities_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `dosens`
--
ALTER TABLE `dosens`
  ADD CONSTRAINT `dosens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `industris`
--
ALTER TABLE `industris`
  ADD CONSTRAINT `industris_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `internship_clearances`
--
ALTER TABLE `internship_clearances`
  ADD CONSTRAINT `internship_clearances_magang_aktif_id_foreign` FOREIGN KEY (`magang_aktif_id`) REFERENCES `magang_aktifs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `internship_clearances_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `internship_evaluations`
--
ALTER TABLE `internship_evaluations`
  ADD CONSTRAINT `internship_evaluations_evaluator_id_foreign` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `internship_evaluations_magang_aktif_id_foreign` FOREIGN KEY (`magang_aktif_id`) REFERENCES `magang_aktifs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `internship_evaluation_comments`
--
ALTER TABLE `internship_evaluation_comments`
  ADD CONSTRAINT `internship_evaluation_comments_internship_evaluation_id_foreign` FOREIGN KEY (`internship_evaluation_id`) REFERENCES `internship_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `internship_evaluation_scores`
--
ALTER TABLE `internship_evaluation_scores`
  ADD CONSTRAINT `internship_evaluation_scores_internship_evaluation_id_foreign` FOREIGN KEY (`internship_evaluation_id`) REFERENCES `internship_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laporan_akhirs`
--
ALTER TABLE `laporan_akhirs`
  ADD CONSTRAINT `laporan_akhirs_magang_id_foreign` FOREIGN KEY (`magang_id`) REFERENCES `magang_aktifs` (`id`);

--
-- Constraints for table `logbooks`
--
ALTER TABLE `logbooks`
  ADD CONSTRAINT `logbooks_magang_id_foreign` FOREIGN KEY (`magang_id`) REFERENCES `magang_aktifs` (`id`);

--
-- Constraints for table `magang_aktifs`
--
ALTER TABLE `magang_aktifs`
  ADD CONSTRAINT `magang_aktifs_pendaftaran_id_foreign` FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftarans` (`id`),
  ADD CONSTRAINT `magang_aktifs_supervisor_industri_id_foreign` FOREIGN KEY (`supervisor_industri_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `magang_aktifs_supervisor_kampus_id_foreign` FOREIGN KEY (`supervisor_kampus_id`) REFERENCES `dosens` (`id`);

--
-- Constraints for table `mahasiswas`
--
ALTER TABLE `mahasiswas`
  ADD CONSTRAINT `mahasiswas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembimbing_assignments`
--
ALTER TABLE `pembimbing_assignments`
  ADD CONSTRAINT `pembimbing_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembimbing_assignments_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `dosens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembimbing_assignments_magang_aktif_id_foreign` FOREIGN KEY (`magang_aktif_id`) REFERENCES `magang_aktifs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pendaftarans`
--
ALTER TABLE `pendaftarans`
  ADD CONSTRAINT `pendaftarans_industri_id_foreign` FOREIGN KEY (`industri_id`) REFERENCES `industris` (`id`),
  ADD CONSTRAINT `pendaftarans_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswas` (`id`);

--
-- Constraints for table `penilaians`
--
ALTER TABLE `penilaians`
  ADD CONSTRAINT `penilaians_magang_id_foreign` FOREIGN KEY (`magang_id`) REFERENCES `magang_aktifs` (`id`);

--
-- Constraints for table `performance_evaluations`
--
ALTER TABLE `performance_evaluations`
  ADD CONSTRAINT `internship_evaluations_magang_id_foreign` FOREIGN KEY (`magang_id`) REFERENCES `magang_aktifs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `internship_evaluations_supervisor_id_foreign` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `performance_evaluation_scores`
--
ALTER TABLE `performance_evaluation_scores`
  ADD CONSTRAINT `evaluation_scores_evaluation_id_foreign` FOREIGN KEY (`performance_evaluation_id`) REFERENCES `performance_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_evaluations`
--
ALTER TABLE `portfolio_evaluations`
  ADD CONSTRAINT `portfolio_evaluations_evaluator_id_foreign` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `portfolio_evaluations_magang_aktif_id_foreign` FOREIGN KEY (`magang_aktif_id`) REFERENCES `magang_aktifs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `portfolio_evaluation_scores`
--
ALTER TABLE `portfolio_evaluation_scores`
  ADD CONSTRAINT `portfolio_evaluation_scores_portfolio_evaluation_id_foreign` FOREIGN KEY (`portfolio_evaluation_id`) REFERENCES `portfolio_evaluations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sertifikats`
--
ALTER TABLE `sertifikats`
  ADD CONSTRAINT `sertifikats_magang_id_foreign` FOREIGN KEY (`magang_id`) REFERENCES `magang_aktifs` (`id`);

--
-- Constraints for table `signatures`
--
ALTER TABLE `signatures`
  ADD CONSTRAINT `signatures_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `surat_keputusan_pembimbings`
--
ALTER TABLE `surat_keputusan_pembimbings`
  ADD CONSTRAINT `surat_keputusan_pembimbings_assignment_id_foreign` FOREIGN KEY (`assignment_id`) REFERENCES `pembimbing_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `surat_keputusan_pembimbings_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
