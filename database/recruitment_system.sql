-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 23 May 2025, 12:34:00
-- Sunucu sürümü: 5.7.24
-- PHP Sürümü: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `recruitment_system`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `status` enum('draft','submitted','in_review','in_test','rejected','accepted') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `current_step` int(11) DEFAULT '1',
  `cover_letter` text COLLATE utf8mb4_unicode_ci,
  `portfolio_points` int(11) DEFAULT '0',
  `education_points` int(11) DEFAULT '0',
  `certificate_points` int(11) DEFAULT '0',
  `experience_points` int(11) DEFAULT '0',
  `reference_points` int(11) DEFAULT '0',
  `test_points` int(11) DEFAULT '0',
  `total_points` int(11) DEFAULT '0',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `kvkk_accepted` tinyint(1) DEFAULT '0',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `applications`
--

INSERT INTO `applications` (`id`, `candidate_id`, `position_id`, `status`, `current_step`, `cover_letter`, `portfolio_points`, `education_points`, `certificate_points`, `experience_points`, `reference_points`, `test_points`, `total_points`, `rejection_reason`, `kvkk_accepted`, `completed_at`, `created_at`, `updated_at`) VALUES
(2, 15, 8, 'rejected', 5, NULL, 3, 1, 6, 3, 1, 31, 54, NULL, 1, NULL, '2025-05-02 13:50:19', '2025-05-02 14:29:39'),
(3, 16, 8, 'submitted', 5, NULL, 3, 3, 8, 12, 1, 36, 81, NULL, 1, NULL, '2025-05-02 13:55:09', '2025-05-02 13:59:32'),
(4, 17, 8, 'submitted', 5, NULL, 0, 1, 10, 6, 1, 35, 59, NULL, 1, NULL, '2025-05-02 14:00:47', '2025-05-02 14:04:07'),
(5, 18, 8, 'accepted', 5, NULL, 0, 3, 4, 39, 1, 39, 125, NULL, 1, NULL, '2025-05-02 14:08:06', '2025-05-02 14:29:22'),
(6, 19, 8, 'rejected', 5, NULL, 0, 1, 4, 12, 1, 30, 60, NULL, 1, NULL, '2025-05-02 14:22:29', '2025-05-02 14:30:10'),
(7, 20, 8, 'accepted', 5, NULL, 3, 3, 12, 27, 1, 31, 110, NULL, 1, NULL, '2025-05-02 14:25:39', '2025-05-02 22:17:17'),
(8, 21, 9, 'rejected', 5, NULL, 3, 1, 4, 3, 1, 33, 52, NULL, 1, NULL, '2025-05-02 14:34:54', '2025-05-02 15:06:08'),
(9, 22, 9, 'submitted', 5, NULL, 3, 3, 6, 6, 1, 44, 78, NULL, 1, NULL, '2025-05-02 14:38:07', '2025-05-02 14:42:53'),
(10, 23, 9, 'rejected', 5, NULL, 0, 3, 4, 15, 1, 34, 91, NULL, 1, NULL, '2025-05-02 14:54:18', '2025-05-02 15:16:25'),
(11, 24, 9, 'accepted', 5, NULL, 0, 3, 6, 15, 1, 37, 98, NULL, 1, NULL, '2025-05-02 14:57:01', '2025-05-02 15:16:29'),
(12, 25, 9, 'submitted', 5, NULL, 0, 3, 4, 6, 1, 32, 62, NULL, 1, NULL, '2025-05-02 15:00:39', '2025-05-02 15:02:34'),
(13, 26, 9, 'rejected', 5, NULL, 3, 1, 6, 6, 1, 35, 67, NULL, 1, NULL, '2025-05-02 15:03:02', '2025-05-02 15:17:19'),
(14, 27, 10, 'submitted', 5, NULL, 3, 3, 6, 3, 1, 39, 70, NULL, 1, NULL, '2025-05-02 20:47:56', '2025-05-02 20:51:18'),
(16, 29, 10, 'submitted', 5, NULL, 0, 1, 4, 21, 1, 28, 97, NULL, 1, NULL, '2025-05-02 20:52:57', '2025-05-02 20:55:22'),
(17, 30, 10, 'rejected', 5, NULL, 3, 3, 4, 0, 1, 33, 53, NULL, 1, NULL, '2025-05-02 20:56:01', '2025-05-02 21:09:53'),
(18, 31, 10, 'accepted', 5, NULL, 3, 3, 8, 27, 1, 42, 147, NULL, 1, NULL, '2025-05-02 20:59:26', '2025-05-02 21:09:26'),
(19, 32, 10, 'rejected', 5, NULL, 0, 1, 4, 0, 1, 36, 42, NULL, 1, NULL, '2025-05-02 21:04:33', '2025-05-02 21:09:39'),
(20, 33, 10, 'submitted', 5, NULL, 3, 1, 4, 9, 1, 34, 79, NULL, 1, NULL, '2025-05-02 21:07:15', '2025-05-02 21:09:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `github_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `portfolio_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','blacklisted') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `candidates`
--

INSERT INTO `candidates` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `city`, `address`, `birth_date`, `gender`, `photo_path`, `cv_path`, `linkedin_url`, `github_url`, `portfolio_url`, `status`, `created_at`, `updated_at`) VALUES
(15, NULL, 'Defne', 'Yılmaz', 'defne@gmail.com', '64257172', 'izmir', NULL, NULL, NULL, 'uploads/photos/6814cd9b3e66d.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 13:50:19', '2025-05-02 13:50:19'),
(16, NULL, 'Asel', 'Yıldırım', 'asel@gmail.com', '463734537', 'İstanbul', NULL, NULL, NULL, 'uploads/photos/6814cebda9252.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 13:55:09', '2025-05-02 13:55:09'),
(17, NULL, 'Zeynep', 'Karalar', 'zeynep@gmail.com', '9684465', 'istanbul', NULL, NULL, NULL, 'uploads/photos/6814d00f55057.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 14:00:47', '2025-05-02 20:44:33'),
(18, NULL, 'Efe', 'Karahanlı', 'efe@gmail.com', '945646548', 'izmir', NULL, NULL, NULL, 'uploads/photos/6814d1c6a5acb.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 14:08:06', '2025-05-02 20:44:47'),
(19, NULL, 'Can', 'Kemal', 'can@gmail.com', '65468456', 'bursa', NULL, NULL, NULL, 'uploads/photos/6814d525dbdba.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 14:22:29', '2025-05-02 20:45:00'),
(20, NULL, 'Kaan', 'Yıldız', 'kaan@gmail.com', '54654798', 'istanbul', NULL, NULL, NULL, 'uploads/photos/6814d5e311019.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 14:25:39', '2025-05-02 20:45:21'),
(21, NULL, 'Ali', 'Yılmaz', 'aliasf@gmail.com', '654654685', 'izmir', NULL, NULL, NULL, 'uploads/photos/6814d80e70f9f.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 14:34:54', '2025-05-02 20:45:31'),
(22, NULL, 'Elisa', 'Yıldırım', 'elisa@gmail.com', '6498455', 'izmir', NULL, NULL, NULL, 'uploads/photos/6814d8cef297b.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 14:38:06', '2025-05-02 20:44:01'),
(23, NULL, 'Lina', 'Öztürk', 'lina@gmail.com', '41561651', 'istanbul', NULL, NULL, NULL, 'uploads/photos/6814dc9a65e25.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 14:54:18', '2025-05-02 20:44:01'),
(24, NULL, 'Duru', 'Kara', 'durrjsf@gmail.com', '46546516', 'izmir', NULL, NULL, NULL, 'uploads/photos/6814dd3d0a07e.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 14:57:01', '2025-05-02 20:44:01'),
(25, NULL, 'Yiğit', 'Ak', 'yigit@gmail.com', '2653255', 'istanbul', NULL, NULL, NULL, 'uploads/photos/6814de17ea9d6.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 15:00:39', '2025-05-02 20:44:01'),
(26, NULL, 'Arda', 'Keskin', 'adrfad@gmail.com', '2344205', 'izmir', NULL, NULL, NULL, 'uploads/photos/6814dea6311f1.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 15:03:02', '2025-05-02 20:44:01'),
(27, NULL, 'Ateş', 'Tan', 'ates@gmail.com', '56465125', 'İzmir', NULL, NULL, NULL, 'uploads/photos/68152f7cc74f8.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 20:47:56', '2025-05-02 20:47:56'),
(29, NULL, 'Dora', 'Alperen', 'dora@gmail.com', '3056843096', 'Bursa', NULL, NULL, NULL, 'uploads/photos/681530a94bc10.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 20:52:57', '2025-05-02 20:52:57'),
(30, NULL, 'Metin', 'Oğuz', 'metin@gmail.com', '2853749024', 'İstanbul', NULL, NULL, NULL, 'uploads/photos/68153161de079.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 20:56:01', '2025-05-02 20:56:01'),
(31, NULL, 'Buğlem', 'De Armas', 'buglem@gmail.com', '28945728', 'İzmir', NULL, NULL, NULL, 'uploads/photos/6815322e204cd.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 20:59:26', '2025-05-02 20:59:26'),
(32, NULL, 'Ada', 'Demir', 'ada@gmail.com', '348584693', 'İzmir', NULL, NULL, NULL, 'uploads/photos/6815336169ab1.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-02 21:04:33', '2025-05-02 21:04:33'),
(33, NULL, 'Öykü', 'Bakır', 'oyku@gmail.com', '23857285', 'Bursa', NULL, NULL, NULL, 'uploads/photos/6815340336296.jpg', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', 'https://www.linkedin.com/in/eyupakan/', 'active', '2025-05-02 21:07:15', '2025-05-02 21:07:15'),
(34, NULL, 'test', 'test', 'eyupakannn@gmail.com', '0551111111', 'izmir', NULL, NULL, NULL, 'uploads/photos/photo_68306271f1e5b.png', NULL, 'https://www.linkedin.com/in/eyupakan/', 'https://www.github.com/in/eyupakan/', '', 'active', '2025-05-23 11:56:34', '2025-05-23 11:56:34');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `candidate_references`
--

CREATE TABLE `candidate_references` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `candidate_references`
--

INSERT INTO `candidate_references` (`id`, `candidate_id`, `name`, `company`, `position`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(14, 15, 'Defne', 'xyz', 'yönetici', 'xyz@gmail.com', '6465146465', '2025-05-02 13:52:55', '2025-05-02 13:52:55'),
(15, 16, 'asel', 'abc', 'senior developer', 'asel21@gmail.com', '6846546486', '2025-05-02 13:57:29', '2025-05-02 13:57:29'),
(16, 17, 'zeynep', 'def', 'stajyer', 'zeynep@gmail.com', '94656548', '2025-05-02 14:02:41', '2025-05-02 14:02:41'),
(17, 18, 'Eyüp Akan', 'test', 'adfhafh', 'afdsdfs@gmail.com', '54654865', '2025-05-02 14:10:18', '2025-05-02 14:10:18'),
(18, 19, 'Eyüp Akan', 'abac', 'adfdsag', 'eyupakan@gmail.com', '76756355', '2025-05-02 14:23:50', '2025-05-02 14:23:50'),
(19, 20, 'adfhadf', 'dfhsdf', 'dsfhdfsh', 'sfagojsfk@gmail.com', '646451326', '2025-05-02 14:27:30', '2025-05-02 14:27:30'),
(20, 21, 'Eyüp Akan', 'srgasfg', 'afgsfh', 'adhadfh@gmail.com', '49841545', '2025-05-02 14:35:57', '2025-05-02 14:35:57'),
(21, 22, 'Eyüp Akan', 'test', 'test', 'fshfahaa@gmail.com', '4366746345', '2025-05-02 14:39:23', '2025-05-02 14:39:23'),
(22, 23, 'Eyüp Akan', 'test', 'asgasfg', 'zeynep@gmail.com', '45678675', '2025-05-02 14:55:11', '2025-05-02 14:55:11'),
(23, 24, 'Eyüp Akan', 'test', 'afdhfd', 'asel21@gmail.com', '3145363451', '2025-05-02 14:58:00', '2025-05-02 14:58:00'),
(24, 25, 'Eyüp Akan', 'xyz', 'yönetici', 'asfgafsg@gmail.com', '324623645', '2025-05-02 15:01:27', '2025-05-02 15:01:27'),
(25, 26, 'Eyüp Akan', 'xyz', 'asgasfg', 'asel21@gmail.com', '536253634', '2025-05-02 15:04:07', '2025-05-02 15:04:07'),
(26, 27, 'Eyüp Akan', 'test', 'test', 'asel21@gmail.com', '435142653', '2025-05-02 20:49:11', '2025-05-02 20:49:11'),
(27, 29, 'Eyüp Akan', 'xyz', 'test', 'eyupakan@gmail.com', '342563462547563', '2025-05-02 20:54:11', '2025-05-02 20:54:11'),
(28, 30, 'Eyüp Akan', 'xyz', 'test', 'asfgafsg@gmail.com', '343556345652', '2025-05-02 20:56:56', '2025-05-02 20:56:56'),
(29, 31, 'Eyüp Akan', 'xyz', 'test', 'asfgafsg@gmail.com', '3258948', '2025-05-02 21:00:54', '2025-05-02 21:00:54'),
(30, 32, 'Eyüp Akan', 'xyz', 'test', 'eyupakan@gmail.com', '345362456345', '2025-05-02 21:05:21', '2025-05-02 21:05:21'),
(31, 33, 'Eyüp Akan', 'xyz', 'yönetici', 'xyz@gmail.com', '3623564545', '2025-05-02 21:08:05', '2025-05-02 21:08:05'),
(32, 34, 'Eyüp Akan', 'test', 'test', 'test@gmail.com', '0505999999', '2025-05-23 11:58:28', '2025-05-23 11:58:28');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `candidate_skills`
--

CREATE TABLE `candidate_skills` (
  `candidate_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci NOT NULL,
  `years_of_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issuing_organization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `credential_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credential_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `certificates`
--

INSERT INTO `certificates` (`id`, `candidate_id`, `name`, `issuing_organization`, `issue_date`, `expiry_date`, `credential_id`, `credential_url`, `created_at`, `updated_at`) VALUES
(21, 15, 'excel', 'udemy', '2025-02-20', NULL, NULL, NULL, '2025-05-02 13:51:37', '2025-05-02 13:51:37'),
(22, 15, 'python', 'btk akademi', '2025-01-22', NULL, NULL, NULL, '2025-05-02 13:51:37', '2025-05-02 13:51:37'),
(23, 15, 'c#', 'udemy', '2025-04-25', NULL, NULL, NULL, '2025-05-02 13:51:37', '2025-05-02 13:51:37'),
(24, 16, 'php', 'btk akademi', '2025-03-07', NULL, NULL, NULL, '2025-05-02 13:56:28', '2025-05-02 13:56:28'),
(25, 16, 'c#', 'turkcell', '2025-02-05', NULL, NULL, NULL, '2025-05-02 13:56:28', '2025-05-02 13:56:28'),
(26, 16, 'python', 'udemy', '2025-04-28', NULL, NULL, NULL, '2025-05-02 13:56:28', '2025-05-02 13:56:28'),
(27, 16, 'flutter', 'btk akademi', '2025-03-14', NULL, NULL, NULL, '2025-05-02 13:56:28', '2025-05-02 13:56:28'),
(28, 17, 'excel', 'udemy', '2025-04-28', NULL, NULL, NULL, '2025-05-02 14:01:56', '2025-05-02 14:01:56'),
(29, 17, 'sql', 'turkcell', '2025-03-12', NULL, NULL, NULL, '2025-05-02 14:01:57', '2025-05-02 14:01:57'),
(30, 17, 'python', 'udemy', '2025-04-12', NULL, NULL, NULL, '2025-05-02 14:01:57', '2025-05-02 14:01:57'),
(31, 17, 'flutter', 'udemy', '2025-01-07', NULL, NULL, NULL, '2025-05-02 14:01:57', '2025-05-02 14:01:57'),
(32, 17, 'linux', 'udemy', '2025-03-07', NULL, NULL, NULL, '2025-05-02 14:01:57', '2025-05-02 14:01:57'),
(33, 18, 'php', 'udemy', '2022-05-02', NULL, NULL, NULL, '2025-05-02 14:09:17', '2025-05-02 14:09:17'),
(34, 18, 'sql', 'turkcell', '2024-07-12', NULL, NULL, NULL, '2025-05-02 14:09:17', '2025-05-02 14:09:17'),
(35, 19, 'excel', 'udemy', '2024-08-07', NULL, NULL, NULL, '2025-05-02 14:23:09', '2025-05-02 14:23:09'),
(36, 19, 'c#', 'btk akademi', '2025-03-13', NULL, NULL, NULL, '2025-05-02 14:23:09', '2025-05-02 14:23:09'),
(37, 20, 'excel', 'btk', '2024-07-18', NULL, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(38, 20, 'sql', 'turkcell', '2025-04-20', NULL, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(39, 20, 'c#', 'udemy', '2025-03-13', NULL, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(40, 20, 'flutter', 'udemy', '2025-01-14', NULL, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(41, 20, 'linux', 'udemy', '2025-03-12', NULL, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(42, 20, 'html5&css', 'udemy', '2025-03-21', NULL, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(43, 21, 'python', 'btk', '2025-03-05', NULL, NULL, NULL, '2025-05-02 14:35:30', '2025-05-02 14:35:30'),
(44, 21, 'sql', 'turkcell', '2025-03-13', NULL, NULL, NULL, '2025-05-02 14:35:30', '2025-05-02 14:35:30'),
(45, 22, 'excel', 'btk', '2025-04-10', NULL, NULL, NULL, '2025-05-02 14:38:43', '2025-05-02 14:38:43'),
(46, 22, 'c#', 'btk akademi', '2025-02-06', NULL, NULL, NULL, '2025-05-02 14:38:43', '2025-05-02 14:38:43'),
(47, 22, 'python', 'udemy', '2025-03-11', NULL, NULL, NULL, '2025-05-02 14:38:43', '2025-05-02 14:38:43'),
(48, 23, 'php', 'udemy', '2025-02-26', NULL, NULL, NULL, '2025-05-02 14:54:47', '2025-05-02 14:54:47'),
(49, 23, 'sql', 'turkcell', '2025-03-13', NULL, NULL, NULL, '2025-05-02 14:54:47', '2025-05-02 14:54:47'),
(50, 24, 'git ve github', 'btk', '2025-04-15', NULL, NULL, NULL, '2025-05-02 14:57:36', '2025-05-02 14:57:36'),
(51, 24, 'sql', 'btk akademi', '2025-04-28', NULL, NULL, NULL, '2025-05-02 14:57:36', '2025-05-02 14:57:36'),
(52, 24, 'c#', 'udemy', '2025-02-26', NULL, NULL, NULL, '2025-05-02 14:57:36', '2025-05-02 14:57:36'),
(53, 25, 'php', 'btk', '2025-04-29', NULL, NULL, NULL, '2025-05-02 15:01:05', '2025-05-02 15:01:05'),
(54, 25, 'sql', 'btk akademi', '2025-03-05', NULL, NULL, NULL, '2025-05-02 15:01:05', '2025-05-02 15:01:05'),
(55, 26, 'excel', 'btk akademi', '2025-04-29', NULL, NULL, NULL, '2025-05-02 15:03:37', '2025-05-02 15:03:37'),
(56, 26, 'c#', 'btk akademi', '2025-04-08', NULL, NULL, NULL, '2025-05-02 15:03:37', '2025-05-02 15:03:37'),
(57, 26, 'c#', 'udemy', '2025-03-12', NULL, NULL, NULL, '2025-05-02 15:03:37', '2025-05-02 15:03:37'),
(58, 27, 'php', 'udemy', '2025-04-30', NULL, NULL, NULL, '2025-05-02 20:48:38', '2025-05-02 20:48:38'),
(59, 27, 'c#', 'btk akademi', '2025-04-08', NULL, NULL, NULL, '2025-05-02 20:48:38', '2025-05-02 20:48:38'),
(60, 27, 'python', 'udemy', '2025-03-06', NULL, NULL, NULL, '2025-05-02 20:48:38', '2025-05-02 20:48:38'),
(61, 29, 'excel', 'btk', '2025-03-04', NULL, NULL, NULL, '2025-05-02 20:53:42', '2025-05-02 20:53:42'),
(62, 29, 'javascript', 'btk akademi', '2025-04-17', NULL, NULL, NULL, '2025-05-02 20:53:42', '2025-05-02 20:53:42'),
(63, 30, 'git ve github', 'btk', '2025-03-07', NULL, NULL, NULL, '2025-05-02 20:56:31', '2025-05-02 20:56:31'),
(64, 30, 'python', 'btk akademi', '2025-04-19', NULL, NULL, NULL, '2025-05-02 20:56:31', '2025-05-02 20:56:31'),
(65, 31, 'git ve github', 'btk', '2025-03-12', NULL, NULL, NULL, '2025-05-02 21:00:10', '2025-05-02 21:00:10'),
(66, 31, 'python', 'turkcell', '2025-04-28', NULL, NULL, NULL, '2025-05-02 21:00:10', '2025-05-02 21:00:10'),
(67, 31, 'c#', 'udemy', '2025-02-13', NULL, NULL, NULL, '2025-05-02 21:00:10', '2025-05-02 21:00:10'),
(68, 31, 'flutter', 'btk akademi', '2025-04-12', NULL, NULL, NULL, '2025-05-02 21:00:10', '2025-05-02 21:00:10'),
(69, 32, 'php', 'btk', '2025-04-16', NULL, NULL, NULL, '2025-05-02 21:04:58', '2025-05-02 21:04:58'),
(70, 32, 'python', 'btk akademi', '2025-03-13', NULL, NULL, NULL, '2025-05-02 21:04:58', '2025-05-02 21:04:58'),
(71, 33, 'git ve github', 'btk', '2025-04-30', NULL, NULL, NULL, '2025-05-02 21:07:44', '2025-05-02 21:07:44'),
(72, 33, 'javascript', 'btk akademi', '2025-04-30', NULL, NULL, NULL, '2025-05-02 21:07:44', '2025-05-02 21:07:44'),
(73, 34, 'python', 'udemy', '2025-03-13', NULL, NULL, NULL, '2025-05-23 11:57:38', '2025-05-23 11:57:38'),
(74, 34, 'c#', 'btk akademi', '2025-05-06', NULL, NULL, NULL, '2025-05-23 11:57:38', '2025-05-23 11:57:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `educations`
--

CREATE TABLE `educations` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `school_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree` enum('high_school','associate','bachelor','master','doctorate') COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_of_study` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT '0',
  `gpa` decimal(3,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `educations`
--

INSERT INTO `educations` (`id`, `candidate_id`, `school_name`, `degree`, `field_of_study`, `start_date`, `end_date`, `is_current`, `gpa`, `description`, `created_at`, `updated_at`) VALUES
(14, 15, 'deü', 'bachelor', 'yazılım mühendisliği', '2021-09-02', NULL, 1, NULL, NULL, '2025-05-02 13:51:37', '2025-05-02 13:51:37'),
(15, 16, 'bakırçay üniversitesi', 'bachelor', 'ybs', '2020-09-02', '2024-07-02', 0, NULL, NULL, '2025-05-02 13:56:28', '2025-05-02 13:56:28'),
(16, 17, 'idü', 'bachelor', 'ybs', '2022-09-02', NULL, 1, NULL, NULL, '2025-05-02 14:01:56', '2025-05-02 14:01:56'),
(17, 18, 'idü', 'bachelor', 'bilgisayar mühendisliği', '2019-09-02', '2023-07-12', 0, NULL, NULL, '2025-05-02 14:09:17', '2025-05-02 14:09:17'),
(18, 19, 'deü', 'bachelor', 'ybs', '2022-09-02', NULL, 1, NULL, NULL, '2025-05-02 14:23:09', '2025-05-02 14:23:09'),
(19, 20, 'idü', 'bachelor', 'yazılım mühendisliği', '2021-09-02', '2025-05-09', 0, NULL, NULL, '2025-05-02 14:26:58', '2025-05-02 14:26:58'),
(20, 21, 'idü', 'bachelor', 'yazılım mühendisliği', '2022-01-02', NULL, 1, NULL, NULL, '2025-05-02 14:35:30', '2025-05-02 14:35:30'),
(21, 22, 'deü', 'bachelor', 'bilgisayar mühendisliği', '2022-01-02', '2025-04-30', 0, NULL, NULL, '2025-05-02 14:38:43', '2025-05-02 14:38:43'),
(22, 23, 'idü', 'bachelor', 'yazılım mühendisliği', '2022-01-02', '2025-04-28', 0, NULL, NULL, '2025-05-02 14:54:47', '2025-05-02 14:54:47'),
(23, 24, 'bakırçay üniversitesi', 'bachelor', 'yazılım mühendisliği', '2021-02-02', '2025-04-29', 0, NULL, NULL, '2025-05-02 14:57:36', '2025-05-02 14:57:36'),
(24, 25, 'deü', 'bachelor', 'yazılım mühendisliği', '2022-01-02', '2025-04-30', 0, NULL, NULL, '2025-05-02 15:01:05', '2025-05-02 15:01:05'),
(25, 26, 'deü', 'bachelor', 'bilgisayar mühendisliği', '2024-11-28', NULL, 1, NULL, NULL, '2025-05-02 15:03:37', '2025-05-02 15:03:37'),
(26, 27, 'deü', 'bachelor', 'ybs', '2021-09-02', '2025-04-30', 0, NULL, NULL, '2025-05-02 20:48:38', '2025-05-02 20:48:38'),
(27, 29, 'deü', 'bachelor', 'yazılım mühendisliği', '2023-09-22', NULL, 1, NULL, NULL, '2025-05-02 20:53:42', '2025-05-02 20:53:42'),
(28, 30, 'bakırçay üniversitesi', 'bachelor', 'ybs', '2022-06-02', '2025-04-28', 0, NULL, NULL, '2025-05-02 20:56:31', '2025-05-02 20:56:31'),
(29, 31, 'deü', 'bachelor', 'ybs', '2021-09-17', '2025-05-01', 0, NULL, NULL, '2025-05-02 21:00:10', '2025-05-02 21:00:10'),
(30, 32, 'bakırçay üniversitesi', 'bachelor', 'bilgisayar mühendisliği', '2021-09-03', NULL, 1, NULL, NULL, '2025-05-02 21:04:58', '2025-05-02 21:04:58'),
(31, 33, 'deü', 'bachelor', 'yazılım mühendisliği', '2020-09-03', NULL, 1, NULL, NULL, '2025-05-02 21:07:44', '2025-05-02 21:07:44'),
(32, 34, 'İzmir Demokrasi Üniversitesi', 'bachelor', 'Yönetim Bilişim Sistemleri', '2021-09-20', NULL, 1, NULL, NULL, '2025-05-23 11:57:38', '2025-05-23 11:57:38');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `experiences`
--

CREATE TABLE `experiences` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT '0',
  `duration_months` int(11) DEFAULT '0',
  `responsibilities` text COLLATE utf8mb4_unicode_ci,
  `achievements` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `experiences`
--

INSERT INTO `experiences` (`id`, `candidate_id`, `company_name`, `position`, `location`, `start_date`, `end_date`, `is_current`, `duration_months`, `responsibilities`, `achievements`, `created_at`, `updated_at`) VALUES
(14, 15, 'xyz', 'stajyer', NULL, '2024-09-01', '2024-10-01', 0, 1, 'yazılım geliştirme', NULL, '2025-05-02 13:52:55', '2025-05-02 13:52:55'),
(15, 16, 'abc', 'jr. backend developer', NULL, '2024-10-29', '2025-03-05', 0, 4, 'web sitesi geliştirdim', NULL, '2025-05-02 13:57:29', '2025-05-02 13:57:29'),
(16, 17, 'def', 'stajyer', NULL, '2024-11-01', '2025-01-31', 0, 2, 'stajyerlik', NULL, '2025-05-02 14:02:41', '2025-05-02 14:02:41'),
(17, 18, 'ghj', 'backend developer', NULL, '2024-01-02', '2025-02-02', 0, 13, 'dshfaf', NULL, '2025-05-02 14:10:18', '2025-05-02 14:10:18'),
(18, 19, 'def', 'stajyer', NULL, '2024-12-12', '2025-04-29', 0, 4, 'stajyer', NULL, '2025-05-02 14:23:50', '2025-05-02 14:23:50'),
(19, 20, 'xyz', 'backend developer', NULL, '2024-04-01', '2025-01-30', 0, 9, 'dfashfd', NULL, '2025-05-02 14:27:30', '2025-05-02 14:27:30'),
(20, 21, 'fsagaf', 'sgndg', NULL, '2025-03-05', '2025-04-17', 0, 1, 'dfhasfh', NULL, '2025-05-02 14:35:57', '2025-05-02 14:35:57'),
(21, 22, 'xyz', 'stajyer', NULL, '2025-01-30', '2025-04-18', 0, 2, 'dafadfhd', NULL, '2025-05-02 14:39:23', '2025-05-02 14:39:23'),
(22, 23, 'xyz', 'adfhadfh', NULL, '2024-10-31', '2025-04-29', 0, 5, '', NULL, '2025-05-02 14:55:11', '2025-05-02 14:55:11'),
(23, 24, 'def', 'stajyer', NULL, '2024-10-07', '2025-03-13', 0, 5, '', NULL, '2025-05-02 14:58:00', '2025-05-02 14:58:00'),
(24, 25, 'def', 'adfhadfh', NULL, '2025-02-24', '2025-04-29', 0, 2, '', NULL, '2025-05-02 15:01:27', '2025-05-02 15:01:27'),
(25, 26, 'def', 'backend developer', NULL, '2025-01-29', '2025-04-14', 0, 2, '', NULL, '2025-05-02 15:04:07', '2025-05-02 15:04:07'),
(26, 27, 'xyz', 'stajyer', NULL, '2025-03-04', '2025-04-18', 0, 1, 'stajyer', NULL, '2025-05-02 20:49:11', '2025-05-02 20:49:11'),
(27, 29, 'xyz', 'stajyer', NULL, '2024-04-09', '2024-11-23', 0, 7, 'stajyer', NULL, '2025-05-02 20:54:11', '2025-05-02 20:54:11'),
(28, 30, 'abc', 'jr. backend developer', NULL, '2025-04-07', '2025-04-30', 0, 0, 'deneme sürümü', NULL, '2025-05-02 20:56:56', '2025-05-02 20:56:56'),
(29, 31, 'def', 'stajyer', NULL, '2024-06-04', '2025-03-14', 0, 9, 'deneme ', NULL, '2025-05-02 21:00:54', '2025-05-02 21:00:54'),
(30, 32, 'def', 'stajyer', NULL, '2025-04-12', '2025-05-01', 0, 0, '', NULL, '2025-05-02 21:05:21', '2025-05-02 21:05:21'),
(31, 33, 'ghj', 'stajyer', NULL, '2025-01-16', '2025-04-30', 0, 3, '', NULL, '2025-05-02 21:08:05', '2025-05-02 21:08:05'),
(32, 34, 'test', 'test', NULL, '2024-05-01', '2025-05-13', 0, 12, 'test', NULL, '2025-05-23 11:58:28', '2025-05-23 11:58:28');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `interview_date` datetime NOT NULL,
  `interview_type` enum('online','in_person') NOT NULL DEFAULT 'in_person',
  `meeting_link` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `interviews`
--

INSERT INTO `interviews` (`id`, `application_id`, `interview_date`, `interview_type`, `meeting_link`, `location`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 11, '2025-05-16 15:12:00', 'online', 'https://www.xxx.com', '', 'cancelled', 'asgs', '2025-05-02 15:13:25', '2025-05-06 10:31:35');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `positions`
--

CREATE TABLE `positions` (
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `requirements` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `portfolio_point` int(11) DEFAULT '0',
  `certificate_point` int(11) DEFAULT '0',
  `education_point` int(11) DEFAULT '0',
  `reference_point` int(11) DEFAULT '0',
  `experience_point` int(11) DEFAULT '0',
  `driver_license_point` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `positions`
--

INSERT INTO `positions` (`id`, `title`, `description`, `requirements`, `status`, `created_by`, `created_at`, `updated_at`, `portfolio_point`, `certificate_point`, `education_point`, `reference_point`, `experience_point`, `driver_license_point`) VALUES
(8, 'Jr. Backend Developer', 'Backend sistemleri geliştirecek deneyimli yazılımcı aranmaktadır.', 'php,sql,web bilgisi', 'active', NULL, '2025-04-30 22:06:39', '2025-05-23 11:51:55', 1, 3, 1, 1, 2, 0),
(9, 'Jr. Veri Bilimci', 'Veri işleme süreçlerine yardımcı olacak kişiler aranıyor.', 'python,sql,istatistik, veri analizi', 'active', NULL, '2025-04-30 22:08:29', '2025-05-23 11:51:38', 1, 2, 1, 1, 3, 0),
(10, 'Jr. Full Stack Developer', 'Jr. Full Stack Developer arıyoruz.', 'php, html&css, sql', 'active', NULL, '2025-05-01 14:57:49', '2025-05-01 14:57:49', 4, 1, 1, 1, 3, 0);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `position_tests`
--

CREATE TABLE `position_tests` (
  `position_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `required` tinyint(1) DEFAULT '1',
  `order_number` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `position_tests`
--

INSERT INTO `position_tests` (`position_id`, `test_id`, `required`, `order_number`, `created_at`) VALUES
(8, 9, 1, 0, '2025-05-23 11:51:55'),
(8, 10, 1, 0, '2025-05-23 11:51:55'),
(8, 11, 1, 0, '2025-05-23 11:51:55'),
(8, 15, 1, 0, '2025-05-23 11:51:55'),
(9, 12, 1, 0, '2025-05-23 11:51:38'),
(9, 13, 1, 0, '2025-05-23 11:51:38'),
(9, 14, 1, 0, '2025-05-23 11:51:38'),
(9, 15, 1, 0, '2025-05-23 11:51:38'),
(10, 9, 1, 0, '2025-05-01 14:57:49'),
(10, 10, 1, 0, '2025-05-01 14:57:49'),
(10, 15, 1, 0, '2025-05-01 14:57:49'),
(10, 16, 1, 0, '2025-05-01 14:57:49');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) DEFAULT '0',
  `order_number` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `question_options`
--

INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `is_correct`, `order_number`, `created_at`, `updated_at`) VALUES
(1, 11, 'var $isim = \"Ahmet\";', 0, 0, '2025-04-30 21:26:36', '2025-04-30 21:26:36'),
(2, 11, 'let isim = \"Ahmet\";', 0, 0, '2025-04-30 21:26:36', '2025-04-30 21:26:36'),
(3, 11, '$isim = \"Ahmet\";', 0, 0, '2025-04-30 21:26:36', '2025-04-30 21:26:36'),
(4, 11, 'isim := \"Ahmet\";', 0, 0, '2025-04-30 21:26:36', '2025-04-30 21:26:36'),
(9, 13, 'count()', 0, 0, '2025-04-30 21:28:24', '2025-04-30 21:28:24'),
(10, 13, 'size()', 0, 0, '2025-04-30 21:28:24', '2025-04-30 21:28:24'),
(11, 13, 'lenght()', 0, 0, '2025-04-30 21:28:24', '2025-04-30 21:28:24'),
(12, 13, 'total()', 0, 0, '2025-04-30 21:28:24', '2025-04-30 21:28:24'),
(13, 12, 'repeat', 0, 0, '2025-04-30 21:28:51', '2025-04-30 21:28:51'),
(14, 12, 'loop', 0, 0, '2025-04-30 21:28:51', '2025-04-30 21:28:51'),
(15, 12, 'foreach', 0, 0, '2025-04-30 21:28:51', '2025-04-30 21:28:51'),
(16, 12, 'iterate', 0, 0, '2025-04-30 21:28:51', '2025-04-30 21:28:51'),
(17, 14, '.html', 0, 0, '2025-04-30 21:29:30', '2025-04-30 21:29:30'),
(18, 14, '.js', 0, 0, '2025-04-30 21:29:30', '2025-04-30 21:29:30'),
(19, 14, '.php', 0, 0, '2025-04-30 21:29:30', '2025-04-30 21:29:30'),
(20, 14, '.py', 0, 0, '2025-04-30 21:29:30', '2025-04-30 21:29:30'),
(21, 15, 'define function foo()', 0, 0, '2025-04-30 21:30:13', '2025-04-30 21:30:13'),
(22, 15, 'function foo() ', 0, 0, '2025-04-30 21:30:13', '2025-04-30 21:30:13'),
(23, 15, 'func foo()', 0, 0, '2025-04-30 21:30:13', '2025-04-30 21:30:13'),
(24, 15, 'method foo()', 0, 0, '2025-04-30 21:30:13', '2025-04-30 21:30:13'),
(25, 21, 'ADD', 0, 0, '2025-04-30 21:34:34', '2025-04-30 21:34:34'),
(26, 21, 'INSERT', 0, 0, '2025-04-30 21:34:34', '2025-04-30 21:34:34'),
(27, 21, 'UPDATE', 0, 0, '2025-04-30 21:34:34', '2025-04-30 21:34:34'),
(28, 21, 'CREATE', 0, 0, '2025-04-30 21:34:34', '2025-04-30 21:34:34'),
(29, 22, 'MAKE TABLE', 0, 0, '2025-04-30 21:35:11', '2025-04-30 21:35:11'),
(30, 22, 'NEW TABLE', 0, 0, '2025-04-30 21:35:11', '2025-04-30 21:35:11'),
(31, 22, 'CREATE TABLE', 0, 0, '2025-04-30 21:35:11', '2025-04-30 21:35:11'),
(32, 22, 'SET TABLE', 0, 0, '2025-04-30 21:35:11', '2025-04-30 21:35:11'),
(33, 23, 'REMOVE', 0, 0, '2025-04-30 21:35:40', '2025-04-30 21:35:40'),
(34, 23, 'DELETE', 0, 0, '2025-04-30 21:35:40', '2025-04-30 21:35:40'),
(35, 23, 'CLEAR', 0, 0, '2025-04-30 21:35:40', '2025-04-30 21:35:40'),
(36, 23, 'DROP', 0, 0, '2025-04-30 21:35:40', '2025-04-30 21:35:40'),
(37, 24, 'SELECT * FROM students;', 0, 0, '2025-04-30 21:36:17', '2025-04-30 21:36:17'),
(38, 24, 'GET ALL students;', 0, 0, '2025-04-30 21:36:17', '2025-04-30 21:36:17'),
(39, 24, 'SHOW students;', 0, 0, '2025-04-30 21:36:17', '2025-04-30 21:36:17'),
(40, 24, 'FETCH * students;', 0, 0, '2025-04-30 21:36:17', '2025-04-30 21:36:17'),
(41, 25, 'INSERT COLUMN', 0, 0, '2025-04-30 21:36:52', '2025-04-30 21:36:52'),
(42, 25, 'UPDATE FIELD', 0, 0, '2025-04-30 21:36:52', '2025-04-30 21:36:52'),
(43, 25, 'ADD COLUMN', 0, 0, '2025-04-30 21:36:52', '2025-04-30 21:36:52'),
(44, 25, 'MODIFY TABLE', 0, 0, '2025-04-30 21:36:52', '2025-04-30 21:36:52'),
(45, 31, 'FTP', 0, 0, '2025-04-30 21:40:35', '2025-04-30 21:40:35'),
(46, 31, 'SMTP', 0, 0, '2025-04-30 21:40:35', '2025-04-30 21:40:35'),
(47, 31, 'HTTP', 0, 0, '2025-04-30 21:40:35', '2025-04-30 21:40:35'),
(48, 31, 'SSH', 0, 0, '2025-04-30 21:40:35', '2025-04-30 21:40:35'),
(49, 32, '200', 0, 0, '2025-04-30 21:41:02', '2025-04-30 21:41:02'),
(50, 32, '301', 0, 0, '2025-04-30 21:41:02', '2025-04-30 21:41:02'),
(51, 32, '403', 0, 0, '2025-04-30 21:41:02', '2025-04-30 21:41:02'),
(52, 32, '404', 0, 0, '2025-04-30 21:41:02', '2025-04-30 21:41:02'),
(53, 33, 'HTML, CSS, JavaScript', 0, 0, '2025-04-30 21:41:40', '2025-04-30 21:41:40'),
(54, 33, 'PHP, SQL, Java', 0, 0, '2025-04-30 21:41:40', '2025-04-30 21:41:40'),
(55, 33, ' Python, HTML, C++', 0, 0, '2025-04-30 21:41:40', '2025-04-30 21:41:40'),
(56, 33, ' XML, Ruby, Bash', 0, 0, '2025-04-30 21:41:40', '2025-04-30 21:41:40'),
(57, 34, 'GET', 0, 0, '2025-04-30 21:42:05', '2025-04-30 21:42:05'),
(58, 34, 'POST', 0, 0, '2025-04-30 21:42:05', '2025-04-30 21:42:05'),
(59, 34, 'PUT', 0, 0, '2025-04-30 21:42:05', '2025-04-30 21:42:05'),
(60, 34, 'DELETE', 0, 0, '2025-04-30 21:42:05', '2025-04-30 21:42:05'),
(61, 35, 'www.example', 0, 0, '2025-04-30 21:42:39', '2025-04-30 21:42:39'),
(62, 35, 'http//example.com', 0, 0, '2025-04-30 21:42:39', '2025-04-30 21:42:39'),
(63, 35, 'https://www.example.com', 0, 0, '2025-04-30 21:42:39', '2025-04-30 21:42:39'),
(64, 35, 'C:\\web\\index.html', 0, 0, '2025-04-30 21:42:39', '2025-04-30 21:42:39'),
(65, 41, 'echo', 0, 0, '2025-04-30 21:46:24', '2025-04-30 21:46:24'),
(66, 41, 'printf', 0, 0, '2025-04-30 21:46:24', '2025-04-30 21:46:24'),
(67, 41, 'print', 0, 0, '2025-04-30 21:46:24', '2025-04-30 21:46:24'),
(68, 41, 'wrtie', 0, 0, '2025-04-30 21:46:24', '2025-04-30 21:46:24'),
(69, 42, 'list = {1, 2, 3}', 0, 0, '2025-04-30 21:46:57', '2025-04-30 21:46:57'),
(70, 42, 'list = (1, 2, 3)', 0, 0, '2025-04-30 21:46:57', '2025-04-30 21:46:57'),
(71, 42, 'list = [1, 2, 3]', 0, 0, '2025-04-30 21:46:57', '2025-04-30 21:46:57'),
(72, 42, 'list = <1, 2, 3>', 0, 0, '2025-04-30 21:46:57', '2025-04-30 21:46:57'),
(73, 43, '=', 0, 0, '2025-04-30 21:47:27', '2025-04-30 21:47:27'),
(74, 43, '==', 0, 0, '2025-04-30 21:47:27', '2025-04-30 21:47:27'),
(75, 43, ':=', 0, 0, '2025-04-30 21:47:27', '2025-04-30 21:47:27'),
(76, 43, '!=', 0, 0, '2025-04-30 21:47:27', '2025-04-30 21:47:27'),
(77, 44, 'throw', 0, 0, '2025-04-30 21:48:29', '2025-04-30 21:48:29'),
(78, 44, 'raise', 0, 0, '2025-04-30 21:48:29', '2025-04-30 21:48:29'),
(79, 44, 'error', 0, 0, '2025-04-30 21:48:29', '2025-04-30 21:48:29'),
(80, 44, 'except', 0, 0, '2025-04-30 21:48:29', '2025-04-30 21:48:29'),
(85, 45, '1,2,3,4,5', 0, 0, '2025-04-30 21:50:00', '2025-04-30 21:50:00'),
(86, 45, '0,1,2,3,4,5', 0, 0, '2025-04-30 21:50:00', '2025-04-30 21:50:00'),
(87, 45, '1,2,3,4', 0, 0, '2025-04-30 21:50:00', '2025-04-30 21:50:00'),
(88, 45, '0,1,2,3,4', 0, 0, '2025-04-30 21:50:00', '2025-04-30 21:50:00'),
(89, 51, 'read_data()', 0, 0, '2025-04-30 21:52:58', '2025-04-30 21:52:58'),
(90, 51, 'load_csv()', 0, 0, '2025-04-30 21:52:58', '2025-04-30 21:52:58'),
(91, 51, 'read_csv() ', 0, 0, '2025-04-30 21:52:58', '2025-04-30 21:52:58'),
(92, 51, 'open_csv()', 0, 0, '2025-04-30 21:52:58', '2025-04-30 21:52:58'),
(93, 52, 'df.get(“Column”)', 0, 0, '2025-04-30 21:53:27', '2025-04-30 21:53:27'),
(94, 52, 'df.Column', 0, 0, '2025-04-30 21:53:27', '2025-04-30 21:53:27'),
(95, 52, 'df[\"Column\"] ', 0, 0, '2025-04-30 21:53:27', '2025-04-30 21:53:27'),
(96, 52, ' df->Column', 0, 0, '2025-04-30 21:53:27', '2025-04-30 21:53:27'),
(97, 53, 'drop_nulls()', 0, 0, '2025-04-30 21:53:52', '2025-04-30 21:53:52'),
(98, 53, 'remove_na()', 0, 0, '2025-04-30 21:53:52', '2025-04-30 21:53:52'),
(99, 53, 'dropna() ', 0, 0, '2025-04-30 21:53:52', '2025-04-30 21:53:52'),
(100, 53, 'clearna()', 0, 0, '2025-04-30 21:53:52', '2025-04-30 21:53:52'),
(101, 54, 'Grafik çizer', 0, 0, '2025-04-30 21:54:22', '2025-04-30 21:54:22'),
(102, 54, 'Tüm verileri siler', 0, 0, '2025-04-30 21:54:22', '2025-04-30 21:54:22'),
(103, 54, 'Sayısal özet istatistikleri gösterir', 0, 0, '2025-04-30 21:54:22', '2025-04-30 21:54:22'),
(104, 54, ' Verileri sıralar', 0, 0, '2025-04-30 21:54:22', '2025-04-30 21:54:22'),
(105, 55, 'groupby()', 0, 0, '2025-04-30 21:54:51', '2025-04-30 21:54:51'),
(106, 55, 'summarize()', 0, 0, '2025-04-30 21:54:51', '2025-04-30 21:54:51'),
(107, 55, 'aggregate()', 0, 0, '2025-04-30 21:54:51', '2025-04-30 21:54:51'),
(108, 55, 'collect()', 0, 0, '2025-04-30 21:54:51', '2025-04-30 21:54:51'),
(109, 61, 'Standart sapma', 0, 0, '2025-04-30 21:57:41', '2025-04-30 21:57:41'),
(110, 61, 'Medyan', 0, 0, '2025-04-30 21:57:41', '2025-04-30 21:57:41'),
(111, 61, 'Varyans', 0, 0, '2025-04-30 21:57:41', '2025-04-30 21:57:41'),
(112, 61, 'Korelasyon', 0, 0, '2025-04-30 21:57:41', '2025-04-30 21:57:41'),
(113, 62, 'Ortalama', 0, 0, '2025-04-30 21:58:10', '2025-04-30 21:58:10'),
(114, 62, 'Varyans', 0, 0, '2025-04-30 21:58:10', '2025-04-30 21:58:10'),
(115, 62, 'Mod', 0, 0, '2025-04-30 21:58:10', '2025-04-30 21:58:10'),
(116, 62, 'Medyan', 0, 0, '2025-04-30 21:58:10', '2025-04-30 21:58:10'),
(117, 63, 'Mod', 0, 0, '2025-04-30 21:58:41', '2025-04-30 21:58:41'),
(118, 63, 'Varyans', 0, 0, '2025-04-30 21:58:41', '2025-04-30 21:58:41'),
(119, 63, 'Ortalama', 0, 0, '2025-04-30 21:58:41', '2025-04-30 21:58:41'),
(120, 63, 'Örneklem', 0, 0, '2025-04-30 21:58:41', '2025-04-30 21:58:41'),
(121, 64, 'Hipotez reddedilmez', 0, 0, '2025-04-30 21:59:20', '2025-04-30 21:59:20'),
(122, 64, 'Test geçersiz', 0, 0, '2025-04-30 21:59:20', '2025-04-30 21:59:20'),
(123, 64, 'Anlamlı sonuç vardır', 0, 0, '2025-04-30 21:59:20', '2025-04-30 21:59:20'),
(124, 64, 'Daha fazla veri gerekir', 0, 0, '2025-04-30 21:59:20', '2025-04-30 21:59:20'),
(129, 69, 'Toplantıyı terk ederim', 0, 0, '2025-04-30 22:01:46', '2025-04-30 22:01:46'),
(130, 69, 'Tartışmaya girerim', 0, 0, '2025-04-30 22:01:46', '2025-04-30 22:01:46'),
(131, 69, 'Söz isteyip fikrimi yeniden açıklarım', 0, 0, '2025-04-30 22:01:46', '2025-04-30 22:01:46'),
(132, 69, 'Sessiz kalırım ve bir dahaki toplantıya bırakırım', 0, 0, '2025-04-30 22:01:46', '2025-04-30 22:01:46'),
(133, 70, 'Savunmaya geçerim', 0, 0, '2025-04-30 22:02:16', '2025-04-30 22:02:16'),
(134, 70, 'Geri bildirimi dikkatle dinler, gerekirse düzeltme yaparım ', 0, 0, '2025-04-30 22:02:16', '2025-04-30 22:02:16'),
(135, 70, 'Göz ardı ederim', 0, 0, '2025-04-30 22:02:16', '2025-04-30 22:02:16'),
(136, 70, 'Başkasına suç atarım', 0, 0, '2025-04-30 22:02:16', '2025-04-30 22:02:16'),
(137, 71, 'Yetişememeyi gizlerim', 0, 0, '2025-04-30 22:02:49', '2025-04-30 22:02:49'),
(138, 71, 'İş arkadaşlarımdan yardım isterim ', 0, 0, '2025-04-30 22:02:49', '2025-04-30 22:02:49'),
(139, 71, 'Her şeyi aceleye getiririm', 0, 0, '2025-04-30 22:02:49', '2025-04-30 22:02:49'),
(140, 71, 'İşi tamamen bırakırım', 0, 0, '2025-04-30 22:02:49', '2025-04-30 22:02:49'),
(141, 72, 'Kendi fikrimde diretirim', 0, 0, '2025-04-30 22:03:17', '2025-04-30 22:03:17'),
(142, 72, 'Onu dinler, değerlendirir ve birlikte karar vermeye çalışırım', 0, 0, '2025-04-30 22:03:17', '2025-04-30 22:03:17'),
(143, 72, 'Yöneticiye şikayet ederim', 0, 0, '2025-04-30 22:03:17', '2025-04-30 22:03:17'),
(144, 72, 'Tartışmayı uzatırım', 0, 0, '2025-04-30 22:03:17', '2025-04-30 22:03:17'),
(145, 73, 'İtiraz ederim', 0, 0, '2025-04-30 22:03:50', '2025-04-30 22:03:50'),
(146, 73, 'İnternetten araştırma yapar, öğrenmeye çalışırım', 0, 0, '2025-04-30 22:03:50', '2025-04-30 22:03:50'),
(147, 73, 'Başkasına devrederim', 0, 0, '2025-04-30 22:03:50', '2025-04-30 22:03:50'),
(148, 73, 'Projeden çekilirim', 0, 0, '2025-04-30 22:03:50', '2025-04-30 22:03:50'),
(149, 77, '<head>', 0, 0, '2025-05-01 14:53:00', '2025-05-01 14:53:00'),
(150, 77, '<meta>', 0, 0, '2025-05-01 14:53:00', '2025-05-01 14:53:00'),
(151, 77, '<title>', 0, 0, '2025-05-01 14:53:00', '2025-05-01 14:53:00'),
(152, 77, '<header>', 0, 0, '2025-05-01 14:53:00', '2025-05-01 14:53:00'),
(153, 78, '#', 0, 0, '2025-05-01 14:53:27', '2025-05-01 14:53:27'),
(154, 78, '.', 0, 0, '2025-05-01 14:53:27', '2025-05-01 14:53:27'),
(155, 78, '@', 0, 0, '2025-05-01 14:53:27', '2025-05-01 14:53:27'),
(156, 78, '*', 0, 0, '2025-05-01 14:53:27', '2025-05-01 14:53:27'),
(157, 79, '<input>', 0, 0, '2025-05-01 14:53:58', '2025-05-01 14:53:58'),
(158, 79, '<form>', 0, 0, '2025-05-01 14:53:58', '2025-05-01 14:53:58'),
(159, 79, '<label>', 0, 0, '2025-05-01 14:53:58', '2025-05-01 14:53:58'),
(160, 79, '<fieldset>', 0, 0, '2025-05-01 14:53:58', '2025-05-01 14:53:58'),
(161, 80, 'font-color: red;', 0, 0, '2025-05-01 14:54:26', '2025-05-01 14:54:26'),
(162, 80, 'color: red;', 0, 0, '2025-05-01 14:54:26', '2025-05-01 14:54:26'),
(163, 80, 'text-style: red;', 0, 0, '2025-05-01 14:54:26', '2025-05-01 14:54:26'),
(164, 80, 'text-color: red;', 0, 0, '2025-05-01 14:54:26', '2025-05-01 14:54:26'),
(165, 81, 'Arka plan rengi değiştirir', 0, 0, '2025-05-01 14:54:54', '2025-05-01 14:54:54'),
(166, 81, 'Elemanları döndürür', 0, 0, '2025-05-01 14:54:54', '2025-05-01 14:54:54'),
(167, 81, 'Elemanları yatay/dikey hizalamayı sağlar', 0, 0, '2025-05-01 14:54:54', '2025-05-01 14:54:54'),
(168, 81, 'Sayfaya bağlantı ekler', 0, 0, '2025-05-01 14:54:54', '2025-05-01 14:54:54');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `time_limit` int(11) NOT NULL DEFAULT '60',
  `passing_score` int(11) NOT NULL DEFAULT '60',
  `total_points` int(11) NOT NULL DEFAULT '0',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `tests`
--

INSERT INTO `tests` (`id`, `title`, `description`, `time_limit`, `passing_score`, `total_points`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(9, 'php', 'php bilgi ölçüm testi', 30, 10, 0, 'active', NULL, '2025-04-30 21:25:50', '2025-04-30 21:33:54'),
(10, 'SQL', 'SQL bilgi ölçüm testi', 30, 10, 0, 'active', NULL, '2025-04-30 21:33:42', '2025-04-30 21:33:42'),
(11, 'Temel Web Bilgisi', 'Temel Web Bilgisi ölçüm testi', 20, 10, 0, 'active', NULL, '2025-04-30 21:39:56', '2025-04-30 21:39:56'),
(12, 'Python', 'Python bilgi ölçüm testi', 30, 10, 0, 'active', NULL, '2025-04-30 21:44:54', '2025-04-30 21:44:54'),
(13, 'Veri Analizi & Manipülasyonu', 'Veri Analizi & Manipülasyonu bilgi ölçüm testi', 30, 10, 0, 'active', NULL, '2025-04-30 21:52:14', '2025-04-30 21:52:14'),
(14, 'Temel İstatistik ve Matematik', 'Temel İstatistik ve Matematik bilgi ölçüm testi', 30, 10, 0, 'active', NULL, '2025-04-30 21:57:02', '2025-04-30 21:57:02'),
(15, 'Soft Skill', 'Soft Skill Testi', 30, 10, 0, 'active', NULL, '2025-04-30 22:01:09', '2025-04-30 22:01:09'),
(16, 'HTML&CSS', 'HTML&CSS bilgi ölçüm testi', 30, 15, 0, 'active', NULL, '2025-05-01 14:52:09', '2025-05-01 14:52:09');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `test_answers`
--

CREATE TABLE `test_answers` (
  `id` int(11) NOT NULL,
  `test_result_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `given_answer` text COLLATE utf8mb4_unicode_ci,
  `is_correct` tinyint(1) DEFAULT '0',
  `points_earned` int(11) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `test_answers`
--

INSERT INTO `test_answers` (`id`, `test_result_id`, `question_id`, `given_answer`, `is_correct`, `points_earned`, `created_at`) VALUES
(39, 5, 11, '3', 0, 0, '2025-05-02 13:53:19'),
(40, 5, 12, '15', 0, 0, '2025-05-02 13:53:19'),
(41, 5, 13, '11', 0, 0, '2025-05-02 13:53:19'),
(42, 5, 14, '20', 0, 0, '2025-05-02 13:53:19'),
(43, 5, 15, '24', 0, 0, '2025-05-02 13:53:19'),
(44, 5, 16, 'true', 0, 0, '2025-05-02 13:53:19'),
(45, 5, 17, 'false', 0, 0, '2025-05-02 13:53:19'),
(46, 5, 18, 'true', 1, 4, '2025-05-02 13:53:19'),
(47, 5, 19, 'function', 1, 7, '2025-05-02 13:53:19'),
(48, 5, 20, 'afgasg', 1, 8, '2025-05-02 13:53:19'),
(49, 6, 69, '131', 0, 0, '2025-05-02 13:53:37'),
(50, 6, 70, '134', 0, 0, '2025-05-02 13:53:37'),
(51, 6, 71, '140', 0, 0, '2025-05-02 13:53:37'),
(52, 6, 72, '143', 0, 0, '2025-05-02 13:53:37'),
(53, 6, 73, '147', 0, 0, '2025-05-02 13:53:37'),
(54, 6, 74, 'false', 1, 1, '2025-05-02 13:53:37'),
(55, 6, 75, 'true', 1, 1, '2025-05-02 13:53:37'),
(56, 6, 76, 'true', 0, 0, '2025-05-02 13:53:37'),
(57, 7, 21, '26', 0, 0, '2025-05-02 13:54:00'),
(58, 7, 22, '32', 0, 0, '2025-05-02 13:54:00'),
(59, 7, 23, '36', 0, 0, '2025-05-02 13:54:00'),
(60, 7, 24, '39', 0, 0, '2025-05-02 13:54:00'),
(61, 7, 25, '44', 0, 0, '2025-05-02 13:54:00'),
(62, 7, 26, 'true', 0, 0, '2025-05-02 13:54:00'),
(63, 7, 27, 'false', 0, 0, '2025-05-02 13:54:00'),
(64, 7, 28, 'true', 0, 0, '2025-05-02 13:54:00'),
(65, 7, 29, 'update', 1, 9, '2025-05-02 13:54:00'),
(66, 7, 30, 'where', 1, 9, '2025-05-02 13:54:00'),
(67, 8, 31, '47', 0, 0, '2025-05-02 13:54:20'),
(68, 8, 32, '52', 0, 0, '2025-05-02 13:54:20'),
(69, 8, 33, '53', 0, 0, '2025-05-02 13:54:20'),
(70, 8, 34, '58', 0, 0, '2025-05-02 13:54:20'),
(71, 8, 35, '63', 0, 0, '2025-05-02 13:54:20'),
(72, 8, 36, 'true', 0, 0, '2025-05-02 13:54:20'),
(73, 8, 37, 'false', 1, 3, '2025-05-02 13:54:20'),
(74, 8, 38, 'true', 1, 3, '2025-05-02 13:54:20'),
(75, 8, 39, 'dsfha', 1, 7, '2025-05-02 13:54:20'),
(76, 8, 40, 'dafhfdh', 1, 7, '2025-05-02 13:54:20'),
(77, 9, 11, '3', 0, 0, '2025-05-02 13:58:02'),
(78, 9, 12, '14', 0, 0, '2025-05-02 13:58:02'),
(79, 9, 13, '11', 0, 0, '2025-05-02 13:58:02'),
(80, 9, 14, '19', 0, 0, '2025-05-02 13:58:02'),
(81, 9, 15, '22', 0, 0, '2025-05-02 13:58:02'),
(82, 9, 16, 'true', 0, 0, '2025-05-02 13:58:02'),
(83, 9, 17, 'true', 1, 3, '2025-05-02 13:58:02'),
(84, 9, 18, 'false', 0, 0, '2025-05-02 13:58:02'),
(85, 9, 19, 'function', 1, 7, '2025-05-02 13:58:02'),
(86, 9, 20, 'log', 1, 8, '2025-05-02 13:58:02'),
(87, 10, 69, '131', 0, 0, '2025-05-02 13:58:20'),
(88, 10, 70, '134', 0, 0, '2025-05-02 13:58:20'),
(89, 10, 71, '138', 0, 0, '2025-05-02 13:58:20'),
(90, 10, 72, '142', 0, 0, '2025-05-02 13:58:20'),
(91, 10, 73, '146', 0, 0, '2025-05-02 13:58:20'),
(92, 10, 74, 'false', 1, 1, '2025-05-02 13:58:20'),
(93, 10, 75, 'true', 1, 1, '2025-05-02 13:58:20'),
(94, 10, 76, 'false', 1, 1, '2025-05-02 13:58:20'),
(95, 11, 21, '27', 0, 0, '2025-05-02 13:58:38'),
(96, 11, 22, '31', 0, 0, '2025-05-02 13:58:38'),
(97, 11, 23, '34', 0, 0, '2025-05-02 13:58:38'),
(98, 11, 24, '39', 0, 0, '2025-05-02 13:58:38'),
(99, 11, 25, '43', 0, 0, '2025-05-02 13:58:38'),
(100, 11, 26, 'false', 1, 3, '2025-05-02 13:58:38'),
(101, 11, 27, 'true', 1, 3, '2025-05-02 13:58:38'),
(102, 11, 28, 'false', 1, 3, '2025-05-02 13:58:38'),
(103, 11, 29, 'dsghs', 1, 9, '2025-05-02 13:58:38'),
(104, 11, 30, 'hdsgha', 1, 9, '2025-05-02 13:58:38'),
(105, 12, 31, '47', 0, 0, '2025-05-02 13:59:05'),
(106, 12, 32, '52', 0, 0, '2025-05-02 13:59:05'),
(107, 12, 33, '53', 0, 0, '2025-05-02 13:59:05'),
(108, 12, 34, '58', 0, 0, '2025-05-02 13:59:05'),
(109, 12, 35, '63', 0, 0, '2025-05-02 13:59:05'),
(110, 12, 36, 'true', 0, 0, '2025-05-02 13:59:05'),
(111, 12, 37, 'false', 1, 3, '2025-05-02 13:59:05'),
(112, 12, 38, 'true', 1, 3, '2025-05-02 13:59:05'),
(113, 12, 39, 'input', 1, 7, '2025-05-02 13:59:05'),
(114, 12, 40, 'css', 1, 7, '2025-05-02 13:59:05'),
(115, 13, 11, '3', 0, 0, '2025-05-02 14:03:01'),
(116, 13, 12, '15', 0, 0, '2025-05-02 14:03:01'),
(117, 13, 13, '11', 0, 0, '2025-05-02 14:03:01'),
(118, 13, 14, '19', 0, 0, '2025-05-02 14:03:01'),
(119, 13, 15, '23', 0, 0, '2025-05-02 14:03:01'),
(120, 13, 16, 'true', 0, 0, '2025-05-02 14:03:01'),
(121, 13, 17, 'true', 1, 3, '2025-05-02 14:03:01'),
(122, 13, 18, 'true', 1, 4, '2025-05-02 14:03:01'),
(123, 13, 19, 'ddgnsg', 1, 7, '2025-05-02 14:03:01'),
(124, 13, 20, 'error', 1, 8, '2025-05-02 14:03:01'),
(125, 14, 69, '131', 0, 0, '2025-05-02 14:03:20'),
(126, 14, 70, '134', 0, 0, '2025-05-02 14:03:20'),
(127, 14, 71, '138', 0, 0, '2025-05-02 14:03:20'),
(128, 14, 72, '142', 0, 0, '2025-05-02 14:03:20'),
(129, 14, 73, '146', 0, 0, '2025-05-02 14:03:20'),
(130, 14, 74, 'false', 1, 1, '2025-05-02 14:03:20'),
(131, 14, 75, 'true', 1, 1, '2025-05-02 14:03:20'),
(132, 14, 76, 'false', 1, 1, '2025-05-02 14:03:20'),
(133, 15, 21, '27', 0, 0, '2025-05-02 14:03:43'),
(134, 15, 22, '31', 0, 0, '2025-05-02 14:03:43'),
(135, 15, 23, '35', 0, 0, '2025-05-02 14:03:43'),
(136, 15, 24, '38', 0, 0, '2025-05-02 14:03:43'),
(137, 15, 25, '43', 0, 0, '2025-05-02 14:03:43'),
(138, 15, 26, 'true', 0, 0, '2025-05-02 14:03:44'),
(139, 15, 27, 'true', 1, 3, '2025-05-02 14:03:44'),
(140, 15, 28, 'false', 1, 3, '2025-05-02 14:03:44'),
(141, 15, 29, 'update', 1, 9, '2025-05-02 14:03:44'),
(142, 15, 30, 'where', 1, 9, '2025-05-02 14:03:44'),
(143, 16, 31, '47', 0, 0, '2025-05-02 14:04:05'),
(144, 16, 32, '52', 0, 0, '2025-05-02 14:04:05'),
(145, 16, 33, '53', 0, 0, '2025-05-02 14:04:05'),
(146, 16, 34, '58', 0, 0, '2025-05-02 14:04:05'),
(147, 16, 35, '63', 0, 0, '2025-05-02 14:04:05'),
(148, 16, 36, 'true', 0, 0, '2025-05-02 14:04:05'),
(149, 16, 37, 'true', 0, 0, '2025-05-02 14:04:05'),
(150, 16, 38, 'true', 1, 3, '2025-05-02 14:04:05'),
(151, 16, 39, 'form', 1, 7, '2025-05-02 14:04:05'),
(152, 16, 40, 'css', 1, 7, '2025-05-02 14:04:05'),
(153, 17, 11, '3', 0, 0, '2025-05-02 14:10:58'),
(154, 17, 12, '15', 0, 0, '2025-05-02 14:10:58'),
(155, 17, 13, '11', 0, 0, '2025-05-02 14:10:58'),
(156, 17, 14, '19', 0, 0, '2025-05-02 14:10:58'),
(157, 17, 15, '22', 0, 0, '2025-05-02 14:10:58'),
(158, 17, 16, 'false', 1, 6, '2025-05-02 14:10:58'),
(159, 17, 17, 'true', 1, 3, '2025-05-02 14:10:58'),
(160, 17, 18, 'true', 1, 4, '2025-05-02 14:10:58'),
(161, 17, 19, 'funciton', 1, 7, '2025-05-02 14:10:58'),
(162, 17, 20, 'var_dump', 1, 8, '2025-05-02 14:10:58'),
(163, 18, 69, '131', 0, 0, '2025-05-02 14:11:15'),
(164, 18, 70, '134', 0, 0, '2025-05-02 14:11:15'),
(165, 18, 71, '138', 0, 0, '2025-05-02 14:11:15'),
(166, 18, 72, '142', 0, 0, '2025-05-02 14:11:15'),
(167, 18, 73, '146', 0, 0, '2025-05-02 14:11:15'),
(168, 18, 74, 'false', 1, 1, '2025-05-02 14:11:15'),
(169, 18, 75, 'false', 0, 0, '2025-05-02 14:11:15'),
(170, 18, 76, 'false', 1, 1, '2025-05-02 14:11:15'),
(171, 19, 21, '27', 0, 0, '2025-05-02 14:11:54'),
(172, 19, 22, '31', 0, 0, '2025-05-02 14:11:54'),
(173, 19, 23, '34', 0, 0, '2025-05-02 14:11:54'),
(174, 19, 24, '37', 0, 0, '2025-05-02 14:11:54'),
(175, 19, 25, '43', 0, 0, '2025-05-02 14:11:54'),
(176, 19, 26, 'false', 1, 3, '2025-05-02 14:11:54'),
(177, 19, 27, 'true', 1, 3, '2025-05-02 14:11:54'),
(178, 19, 28, 'false', 1, 3, '2025-05-02 14:11:54'),
(179, 19, 29, 'update', 1, 9, '2025-05-02 14:11:54'),
(180, 19, 30, 'where', 1, 9, '2025-05-02 14:11:54'),
(181, 20, 31, '47', 0, 0, '2025-05-02 14:12:17'),
(182, 20, 32, '52', 0, 0, '2025-05-02 14:12:17'),
(183, 20, 33, '53', 0, 0, '2025-05-02 14:12:17'),
(184, 20, 34, '60', 0, 0, '2025-05-02 14:12:17'),
(185, 20, 35, '63', 0, 0, '2025-05-02 14:12:17'),
(186, 20, 36, 'true', 0, 0, '2025-05-02 14:12:17'),
(187, 20, 37, 'false', 1, 3, '2025-05-02 14:12:17'),
(188, 20, 38, 'true', 1, 3, '2025-05-02 14:12:17'),
(189, 20, 39, 'form', 1, 7, '2025-05-02 14:12:17'),
(190, 20, 40, 'css', 1, 7, '2025-05-02 14:12:17'),
(191, 21, 11, '3', 0, 0, '2025-05-02 14:24:08'),
(192, 21, 12, '15', 0, 0, '2025-05-02 14:24:08'),
(193, 21, 13, '11', 0, 0, '2025-05-02 14:24:08'),
(194, 21, 14, '19', 0, 0, '2025-05-02 14:24:08'),
(195, 21, 15, '23', 0, 0, '2025-05-02 14:24:08'),
(196, 21, 16, 'true', 0, 0, '2025-05-02 14:24:08'),
(197, 21, 17, 'true', 1, 3, '2025-05-02 14:24:08'),
(198, 21, 18, 'true', 1, 4, '2025-05-02 14:24:08'),
(199, 21, 19, 'xgndg', 1, 7, '2025-05-02 14:24:08'),
(200, 21, 20, 'dsghdh', 1, 8, '2025-05-02 14:24:08'),
(201, 22, 69, '131', 0, 0, '2025-05-02 14:24:24'),
(202, 22, 70, '134', 0, 0, '2025-05-02 14:24:24'),
(203, 22, 71, '138', 0, 0, '2025-05-02 14:24:24'),
(204, 22, 72, '142', 0, 0, '2025-05-02 14:24:24'),
(205, 22, 73, '146', 0, 0, '2025-05-02 14:24:24'),
(206, 22, 74, 'true', 0, 0, '2025-05-02 14:24:24'),
(207, 22, 75, 'true', 1, 1, '2025-05-02 14:24:24'),
(208, 22, 76, 'true', 0, 0, '2025-05-02 14:24:24'),
(209, 23, 21, '27', 0, 0, '2025-05-02 14:24:44'),
(210, 23, 22, '31', 0, 0, '2025-05-02 14:24:44'),
(211, 23, 23, '34', 0, 0, '2025-05-02 14:24:44'),
(212, 23, 24, '37', 0, 0, '2025-05-02 14:24:44'),
(213, 23, 25, '43', 0, 0, '2025-05-02 14:24:44'),
(214, 23, 26, 'true', 0, 0, '2025-05-02 14:24:44'),
(215, 23, 27, 'true', 1, 3, '2025-05-02 14:24:44'),
(216, 23, 28, 'true', 0, 0, '2025-05-02 14:24:44'),
(217, 23, 29, 'sargafg', 1, 9, '2025-05-02 14:24:44'),
(218, 23, 30, 'where', 1, 9, '2025-05-02 14:24:44'),
(219, 24, 31, '47', 0, 0, '2025-05-02 14:25:02'),
(220, 24, 32, '52', 0, 0, '2025-05-02 14:25:02'),
(221, 24, 33, '53', 0, 0, '2025-05-02 14:25:02'),
(222, 24, 34, '60', 0, 0, '2025-05-02 14:25:02'),
(223, 24, 35, '63', 0, 0, '2025-05-02 14:25:02'),
(224, 24, 36, 'true', 0, 0, '2025-05-02 14:25:02'),
(225, 24, 37, 'true', 0, 0, '2025-05-02 14:25:02'),
(226, 24, 38, 'true', 1, 3, '2025-05-02 14:25:02'),
(227, 24, 39, 'form', 1, 7, '2025-05-02 14:25:02'),
(228, 24, 40, 'adfha', 1, 7, '2025-05-02 14:25:02'),
(229, 25, 11, '3', 0, 0, '2025-05-02 14:27:47'),
(230, 25, 12, '15', 0, 0, '2025-05-02 14:27:47'),
(231, 25, 13, '11', 0, 0, '2025-05-02 14:27:47'),
(232, 25, 14, '19', 0, 0, '2025-05-02 14:27:47'),
(233, 25, 15, '22', 0, 0, '2025-05-02 14:27:47'),
(234, 25, 16, 'true', 0, 0, '2025-05-02 14:27:47'),
(235, 25, 17, 'false', 0, 0, '2025-05-02 14:27:47'),
(236, 25, 18, 'true', 1, 4, '2025-05-02 14:27:47'),
(237, 25, 19, 'sfgnsdg', 1, 7, '2025-05-02 14:27:47'),
(238, 25, 20, 'sdghadh', 1, 8, '2025-05-02 14:27:47'),
(239, 26, 69, '131', 0, 0, '2025-05-02 14:28:05'),
(240, 26, 70, '134', 0, 0, '2025-05-02 14:28:05'),
(241, 26, 71, '138', 0, 0, '2025-05-02 14:28:05'),
(242, 26, 72, '142', 0, 0, '2025-05-02 14:28:05'),
(243, 26, 73, '146', 0, 0, '2025-05-02 14:28:05'),
(244, 26, 74, 'true', 0, 0, '2025-05-02 14:28:05'),
(245, 26, 75, 'true', 1, 1, '2025-05-02 14:28:05'),
(246, 26, 76, 'false', 1, 1, '2025-05-02 14:28:05'),
(247, 27, 21, '27', 0, 0, '2025-05-02 14:28:28'),
(248, 27, 22, '31', 0, 0, '2025-05-02 14:28:28'),
(249, 27, 23, '35', 0, 0, '2025-05-02 14:28:28'),
(250, 27, 24, '38', 0, 0, '2025-05-02 14:28:28'),
(251, 27, 25, '43', 0, 0, '2025-05-02 14:28:28'),
(252, 27, 26, 'true', 0, 0, '2025-05-02 14:28:28'),
(253, 27, 27, 'true', 1, 3, '2025-05-02 14:28:28'),
(254, 27, 28, 'true', 0, 0, '2025-05-02 14:28:28'),
(255, 27, 29, 'update', 1, 9, '2025-05-02 14:28:28'),
(256, 27, 30, 'afgas', 1, 9, '2025-05-02 14:28:28'),
(257, 28, 31, '47', 0, 0, '2025-05-02 14:28:46'),
(258, 28, 32, '52', 0, 0, '2025-05-02 14:28:46'),
(259, 28, 33, '53', 0, 0, '2025-05-02 14:28:46'),
(260, 28, 34, '58', 0, 0, '2025-05-02 14:28:46'),
(261, 28, 35, '63', 0, 0, '2025-05-02 14:28:46'),
(262, 28, 36, 'true', 0, 0, '2025-05-02 14:28:46'),
(263, 28, 37, 'true', 0, 0, '2025-05-02 14:28:46'),
(264, 28, 38, 'true', 1, 3, '2025-05-02 14:28:46'),
(265, 28, 39, 'form', 1, 7, '2025-05-02 14:28:46'),
(266, 28, 40, 'dafgag', 1, 7, '2025-05-02 14:28:46'),
(267, 29, 41, '67', 0, 0, '2025-05-02 14:36:29'),
(268, 29, 42, '71', 0, 0, '2025-05-02 14:36:29'),
(269, 29, 43, '74', 0, 0, '2025-05-02 14:36:29'),
(270, 29, 44, '78', 0, 0, '2025-05-02 14:36:29'),
(271, 29, 45, '88', 0, 0, '2025-05-02 14:36:29'),
(272, 29, 46, 'true', 1, 3, '2025-05-02 14:36:29'),
(273, 29, 47, 'false', 1, 3, '2025-05-02 14:36:29'),
(274, 29, 48, 'true', 0, 0, '2025-05-02 14:36:29'),
(275, 29, 49, 'raise', 1, 7, '2025-05-02 14:36:29'),
(276, 29, 50, 'for', 1, 6, '2025-05-02 14:36:29'),
(277, 30, 69, '131', 0, 0, '2025-05-02 14:36:42'),
(278, 30, 70, '134', 0, 0, '2025-05-02 14:36:42'),
(279, 30, 71, '140', 0, 0, '2025-05-02 14:36:42'),
(280, 30, 72, '143', 0, 0, '2025-05-02 14:36:42'),
(281, 30, 73, '146', 0, 0, '2025-05-02 14:36:42'),
(282, 30, 74, 'true', 0, 0, '2025-05-02 14:36:42'),
(283, 30, 75, 'true', 1, 1, '2025-05-02 14:36:42'),
(284, 30, 76, 'true', 0, 0, '2025-05-02 14:36:42'),
(285, 31, 61, '110', 0, 0, '2025-05-02 14:37:09'),
(286, 31, 62, '115', 0, 0, '2025-05-02 14:37:09'),
(287, 31, 63, '119', 0, 0, '2025-05-02 14:37:09'),
(288, 31, 64, '123', 0, 0, '2025-05-02 14:37:09'),
(289, 31, 65, 'true', 1, 3, '2025-05-02 14:37:09'),
(290, 31, 66, 'true', 0, 0, '2025-05-02 14:37:09'),
(291, 31, 67, 'adfad', 1, 7, '2025-05-02 14:37:09'),
(292, 31, 68, 'dhdfh', 1, 7, '2025-05-02 14:37:09'),
(293, 32, 51, '91', 0, 0, '2025-05-02 14:37:30'),
(294, 32, 52, '95', 0, 0, '2025-05-02 14:37:30'),
(295, 32, 53, '99', 0, 0, '2025-05-02 14:37:30'),
(296, 32, 54, '103', 0, 0, '2025-05-02 14:37:30'),
(297, 32, 55, '105', 0, 0, '2025-05-02 14:37:30'),
(298, 32, 56, 'true', 0, 0, '2025-05-02 14:37:30'),
(299, 32, 57, 'false', 0, 0, '2025-05-02 14:37:30'),
(300, 32, 58, 'true', 1, 3, '2025-05-02 14:37:30'),
(301, 32, 59, 'zghadh', 1, 7, '2025-05-02 14:37:30'),
(302, 32, 60, 'sghsd', 1, 7, '2025-05-02 14:37:30'),
(303, 33, 41, '67', 0, 0, '2025-05-02 14:40:10'),
(304, 33, 42, '70', 0, 0, '2025-05-02 14:40:10'),
(305, 33, 43, '75', 0, 0, '2025-05-02 14:40:10'),
(306, 33, 44, '79', 0, 0, '2025-05-02 14:40:10'),
(307, 33, 45, '88', 0, 0, '2025-05-02 14:40:10'),
(308, 33, 46, 'true', 1, 3, '2025-05-02 14:40:10'),
(309, 33, 47, 'false', 1, 3, '2025-05-02 14:40:10'),
(310, 33, 48, 'false', 1, 3, '2025-05-02 14:40:10'),
(311, 33, 49, 'try', 1, 7, '2025-05-02 14:40:10'),
(312, 33, 50, 'for', 1, 6, '2025-05-02 14:40:10'),
(313, 34, 69, '131', 0, 0, '2025-05-02 14:40:32'),
(314, 34, 70, '134', 0, 0, '2025-05-02 14:40:32'),
(315, 34, 71, '138', 0, 0, '2025-05-02 14:40:32'),
(316, 34, 72, '142', 0, 0, '2025-05-02 14:40:32'),
(317, 34, 73, '146', 0, 0, '2025-05-02 14:40:32'),
(318, 34, 74, 'false', 1, 1, '2025-05-02 14:40:32'),
(319, 34, 75, 'true', 1, 1, '2025-05-02 14:40:32'),
(320, 34, 76, 'false', 1, 1, '2025-05-02 14:40:32'),
(321, 35, 61, '110', 0, 0, '2025-05-02 14:41:55'),
(322, 35, 62, '115', 0, 0, '2025-05-02 14:41:55'),
(323, 35, 63, '118', 0, 0, '2025-05-02 14:41:55'),
(324, 35, 64, '123', 0, 0, '2025-05-02 14:41:55'),
(325, 35, 65, 'true', 1, 3, '2025-05-02 14:41:55'),
(326, 35, 66, 'false', 1, 3, '2025-05-02 14:41:55'),
(327, 35, 67, 'varyans', 1, 7, '2025-05-02 14:41:55'),
(328, 35, 68, 'ortalama', 1, 7, '2025-05-02 14:41:55'),
(329, 36, 51, '91', 0, 0, '2025-05-02 14:42:50'),
(330, 36, 52, '95', 0, 0, '2025-05-02 14:42:50'),
(331, 36, 53, '99', 0, 0, '2025-05-02 14:42:50'),
(332, 36, 54, '103', 0, 0, '2025-05-02 14:42:50'),
(333, 36, 55, '105', 0, 0, '2025-05-02 14:42:50'),
(334, 36, 56, 'false', 1, 3, '2025-05-02 14:42:50'),
(335, 36, 57, 'true', 1, 3, '2025-05-02 14:42:50'),
(336, 36, 58, 'true', 1, 3, '2025-05-02 14:42:50'),
(337, 36, 59, 'drop', 1, 7, '2025-05-02 14:42:50'),
(338, 36, 60, 'mean', 1, 7, '2025-05-02 14:42:50'),
(339, 37, 41, '67', 0, 0, '2025-05-02 14:55:32'),
(340, 37, 42, '71', 0, 0, '2025-05-02 14:55:32'),
(341, 37, 43, '74', 0, 0, '2025-05-02 14:55:32'),
(342, 37, 44, '79', 0, 0, '2025-05-02 14:55:32'),
(343, 37, 45, '88', 0, 0, '2025-05-02 14:55:32'),
(344, 37, 46, 'true', 1, 3, '2025-05-02 14:55:32'),
(345, 37, 47, 'false', 1, 3, '2025-05-02 14:55:32'),
(346, 37, 48, 'true', 0, 0, '2025-05-02 14:55:32'),
(347, 37, 49, 'dgbhdf', 1, 7, '2025-05-02 14:55:32'),
(348, 37, 50, 'adfhah', 1, 6, '2025-05-02 14:55:32'),
(349, 38, 69, '131', 0, 0, '2025-05-02 14:55:52'),
(350, 38, 70, '134', 0, 0, '2025-05-02 14:55:52'),
(351, 38, 71, '138', 0, 0, '2025-05-02 14:55:52'),
(352, 38, 72, '143', 0, 0, '2025-05-02 14:55:52'),
(353, 38, 73, '148', 0, 0, '2025-05-02 14:55:52'),
(354, 38, 74, 'true', 0, 0, '2025-05-02 14:55:52'),
(355, 38, 75, 'true', 1, 1, '2025-05-02 14:55:52'),
(356, 38, 76, 'false', 1, 1, '2025-05-02 14:55:52'),
(357, 39, 61, '110', 0, 0, '2025-05-02 14:56:08'),
(358, 39, 62, '115', 0, 0, '2025-05-02 14:56:08'),
(359, 39, 63, '119', 0, 0, '2025-05-02 14:56:08'),
(360, 39, 64, '123', 0, 0, '2025-05-02 14:56:08'),
(361, 39, 65, 'true', 1, 3, '2025-05-02 14:56:08'),
(362, 39, 66, 'true', 0, 0, '2025-05-02 14:56:08'),
(363, 39, 67, 'zcgba', 1, 7, '2025-05-02 14:56:08'),
(364, 39, 68, 'dfhfs', 1, 7, '2025-05-02 14:56:08'),
(365, 40, 51, '91', 0, 0, '2025-05-02 14:56:28'),
(366, 40, 52, '95', 0, 0, '2025-05-02 14:56:28'),
(367, 40, 53, '99', 0, 0, '2025-05-02 14:56:28'),
(368, 40, 54, '103', 0, 0, '2025-05-02 14:56:28'),
(369, 40, 55, '106', 0, 0, '2025-05-02 14:56:28'),
(370, 40, 56, 'true', 0, 0, '2025-05-02 14:56:28'),
(371, 40, 57, 'true', 1, 3, '2025-05-02 14:56:28'),
(372, 40, 58, 'false', 0, 0, '2025-05-02 14:56:28'),
(373, 40, 59, 'xnsfjd', 1, 7, '2025-05-02 14:56:28'),
(374, 40, 60, 'hfsjghdh', 1, 7, '2025-05-02 14:56:28'),
(375, 41, 41, '67', 0, 0, '2025-05-02 14:58:27'),
(376, 41, 42, '69', 0, 0, '2025-05-02 14:58:27'),
(377, 41, 43, '73', 0, 0, '2025-05-02 14:58:27'),
(378, 41, 44, '78', 0, 0, '2025-05-02 14:58:27'),
(379, 41, 45, '88', 0, 0, '2025-05-02 14:58:27'),
(380, 41, 46, 'true', 1, 3, '2025-05-02 14:58:27'),
(381, 41, 47, 'true', 0, 0, '2025-05-02 14:58:27'),
(382, 41, 48, 'true', 0, 0, '2025-05-02 14:58:27'),
(383, 41, 49, 'try', 1, 7, '2025-05-02 14:58:27'),
(384, 41, 50, 'for', 1, 6, '2025-05-02 14:58:27'),
(385, 42, 69, '131', 0, 0, '2025-05-02 14:58:42'),
(386, 42, 70, '134', 0, 0, '2025-05-02 14:58:42'),
(387, 42, 71, '138', 0, 0, '2025-05-02 14:58:42'),
(388, 42, 72, '142', 0, 0, '2025-05-02 14:58:42'),
(389, 42, 73, '146', 0, 0, '2025-05-02 14:58:42'),
(390, 42, 74, 'true', 0, 0, '2025-05-02 14:58:42'),
(391, 42, 75, 'false', 0, 0, '2025-05-02 14:58:42'),
(392, 42, 76, 'false', 1, 1, '2025-05-02 14:58:42'),
(393, 43, 61, '111', 0, 0, '2025-05-02 14:59:04'),
(394, 43, 62, '114', 0, 0, '2025-05-02 14:59:04'),
(395, 43, 63, '119', 0, 0, '2025-05-02 14:59:04'),
(396, 43, 64, '122', 0, 0, '2025-05-02 14:59:04'),
(397, 43, 65, 'true', 1, 3, '2025-05-02 14:59:04'),
(398, 43, 66, 'false', 1, 3, '2025-05-02 14:59:04'),
(399, 43, 67, 'adfga', 1, 7, '2025-05-02 14:59:04'),
(400, 43, 68, 'dfhah', 1, 7, '2025-05-02 14:59:04'),
(401, 44, 51, '91', 0, 0, '2025-05-02 14:59:26'),
(402, 44, 52, '95', 0, 0, '2025-05-02 14:59:26'),
(403, 44, 53, '99', 0, 0, '2025-05-02 14:59:26'),
(404, 44, 54, '103', 0, 0, '2025-05-02 14:59:26'),
(405, 44, 55, '105', 0, 0, '2025-05-02 14:59:26'),
(406, 44, 56, 'false', 1, 3, '2025-05-02 14:59:26'),
(407, 44, 57, 'true', 1, 3, '2025-05-02 14:59:26'),
(408, 44, 58, 'true', 1, 3, '2025-05-02 14:59:26'),
(409, 44, 59, 'ghddfh', 1, 7, '2025-05-02 14:59:26'),
(410, 44, 60, 'adfhadf', 1, 7, '2025-05-02 14:59:26'),
(411, 45, 51, '91', 0, 0, '2025-05-02 15:01:44'),
(412, 45, 52, '95', 0, 0, '2025-05-02 15:01:44'),
(413, 45, 53, '99', 0, 0, '2025-05-02 15:01:44'),
(414, 45, 54, '103', 0, 0, '2025-05-02 15:01:44'),
(415, 45, 55, '107', 0, 0, '2025-05-02 15:01:44'),
(416, 45, 56, 'true', 0, 0, '2025-05-02 15:01:44'),
(417, 45, 57, 'true', 1, 3, '2025-05-02 15:01:44'),
(418, 45, 58, 'true', 1, 3, '2025-05-02 15:01:44'),
(419, 45, 59, 'etgae', 1, 7, '2025-05-02 15:01:44'),
(420, 45, 60, 'etyaery', 1, 7, '2025-05-02 15:01:44'),
(421, 46, 61, '111', 0, 0, '2025-05-02 15:01:59'),
(422, 46, 62, '115', 0, 0, '2025-05-02 15:01:59'),
(423, 46, 63, '120', 0, 0, '2025-05-02 15:01:59'),
(424, 46, 64, '123', 0, 0, '2025-05-02 15:01:59'),
(425, 46, 65, 'true', 1, 3, '2025-05-02 15:01:59'),
(426, 46, 66, 'true', 0, 0, '2025-05-02 15:01:59'),
(427, 46, 67, 'sfghst', 1, 7, '2025-05-02 15:01:59'),
(428, 46, 68, 'hsrthtdsh', 1, 7, '2025-05-02 15:01:59'),
(429, 47, 41, '67', 0, 0, '2025-05-02 15:02:18'),
(430, 47, 42, '71', 0, 0, '2025-05-02 15:02:18'),
(431, 47, 43, '73', 0, 0, '2025-05-02 15:02:18'),
(432, 47, 44, '79', 0, 0, '2025-05-02 15:02:18'),
(433, 47, 45, '88', 0, 0, '2025-05-02 15:02:18'),
(434, 47, 46, 'true', 1, 3, '2025-05-02 15:02:18'),
(435, 47, 47, 'false', 1, 3, '2025-05-02 15:02:18'),
(436, 47, 48, 'true', 0, 0, '2025-05-02 15:02:18'),
(437, 47, 49, 'ssthbsg', 1, 7, '2025-05-02 15:02:18'),
(438, 47, 50, 'sghdh', 1, 6, '2025-05-02 15:02:18'),
(439, 48, 69, '131', 0, 0, '2025-05-02 15:02:32'),
(440, 48, 70, '134', 0, 0, '2025-05-02 15:02:32'),
(441, 48, 71, '138', 0, 0, '2025-05-02 15:02:32'),
(442, 48, 72, '142', 0, 0, '2025-05-02 15:02:32'),
(443, 48, 73, '146', 0, 0, '2025-05-02 15:02:32'),
(444, 48, 74, 'true', 0, 0, '2025-05-02 15:02:32'),
(445, 48, 75, 'false', 0, 0, '2025-05-02 15:02:32'),
(446, 48, 76, 'true', 0, 0, '2025-05-02 15:02:32'),
(447, 49, 41, '67', 0, 0, '2025-05-02 15:04:26'),
(448, 49, 42, '71', 0, 0, '2025-05-02 15:04:26'),
(449, 49, 43, '73', 0, 0, '2025-05-02 15:04:26'),
(450, 49, 44, '79', 0, 0, '2025-05-02 15:04:26'),
(451, 49, 45, '86', 0, 0, '2025-05-02 15:04:26'),
(452, 49, 46, 'true', 1, 3, '2025-05-02 15:04:26'),
(453, 49, 47, 'true', 0, 0, '2025-05-02 15:04:26'),
(454, 49, 48, 'true', 0, 0, '2025-05-02 15:04:26'),
(455, 49, 49, 'try', 1, 7, '2025-05-02 15:04:26'),
(456, 49, 50, 'dsfadf', 1, 6, '2025-05-02 15:04:26'),
(457, 50, 69, '131', 0, 0, '2025-05-02 15:04:42'),
(458, 50, 70, '134', 0, 0, '2025-05-02 15:04:42'),
(459, 50, 71, '138', 0, 0, '2025-05-02 15:04:42'),
(460, 50, 72, '142', 0, 0, '2025-05-02 15:04:42'),
(461, 50, 73, '146', 0, 0, '2025-05-02 15:04:42'),
(462, 50, 74, 'true', 0, 0, '2025-05-02 15:04:42'),
(463, 50, 75, 'true', 1, 1, '2025-05-02 15:04:42'),
(464, 50, 76, 'false', 1, 1, '2025-05-02 15:04:42'),
(465, 51, 61, '111', 0, 0, '2025-05-02 15:04:57'),
(466, 51, 62, '115', 0, 0, '2025-05-02 15:04:57'),
(467, 51, 63, '119', 0, 0, '2025-05-02 15:04:57'),
(468, 51, 64, '123', 0, 0, '2025-05-02 15:04:57'),
(469, 51, 65, 'true', 1, 3, '2025-05-02 15:04:57'),
(470, 51, 66, 'true', 0, 0, '2025-05-02 15:04:57'),
(471, 51, 67, 'sdfhbdg', 1, 7, '2025-05-02 15:04:57'),
(472, 51, 68, 'dshdf', 1, 7, '2025-05-02 15:04:57'),
(473, 52, 51, '91', 0, 0, '2025-05-02 15:05:16'),
(474, 52, 52, '95', 0, 0, '2025-05-02 15:05:16'),
(475, 52, 53, '99', 0, 0, '2025-05-02 15:05:16'),
(476, 52, 54, '103', 0, 0, '2025-05-02 15:05:16'),
(477, 52, 55, '105', 0, 0, '2025-05-02 15:05:16'),
(478, 52, 56, 'true', 0, 0, '2025-05-02 15:05:16'),
(479, 52, 57, 'true', 1, 3, '2025-05-02 15:05:16'),
(480, 52, 58, 'true', 1, 3, '2025-05-02 15:05:16'),
(481, 52, 59, 'dgshdsg', 1, 7, '2025-05-02 15:05:16'),
(482, 52, 60, 'hdsghd', 1, 7, '2025-05-02 15:05:16'),
(483, 53, 77, '151', 0, 0, '2025-05-02 20:50:07'),
(484, 53, 78, '154', 0, 0, '2025-05-02 20:50:07'),
(485, 53, 79, '157', 0, 0, '2025-05-02 20:50:07'),
(486, 53, 80, '162', 0, 0, '2025-05-02 20:50:07'),
(487, 53, 81, '167', 0, 0, '2025-05-02 20:50:07'),
(488, 53, 82, 'true', 1, 3, '2025-05-02 20:50:07'),
(489, 53, 83, 'false', 1, 3, '2025-05-02 20:50:07'),
(490, 53, 84, 'true', 1, 3, '2025-05-02 20:50:07'),
(491, 53, 85, 'property', 1, 7, '2025-05-02 20:50:07'),
(492, 53, 86, 'div', 1, 6, '2025-05-02 20:50:07'),
(493, 54, 11, '3', 0, 0, '2025-05-02 20:50:38'),
(494, 54, 12, '15', 0, 0, '2025-05-02 20:50:38'),
(495, 54, 13, '11', 0, 0, '2025-05-02 20:50:38'),
(496, 54, 14, '19', 0, 0, '2025-05-02 20:50:38'),
(497, 54, 15, '22', 0, 0, '2025-05-02 20:50:38'),
(498, 54, 16, 'true', 0, 0, '2025-05-02 20:50:38'),
(499, 54, 17, 'true', 1, 3, '2025-05-02 20:50:38'),
(500, 54, 18, 'true', 1, 4, '2025-05-02 20:50:38'),
(501, 54, 19, 'function', 1, 7, '2025-05-02 20:50:38'),
(502, 54, 20, 'var_dump', 1, 8, '2025-05-02 20:50:38'),
(503, 55, 69, '131', 0, 0, '2025-05-02 20:50:54'),
(504, 55, 70, '134', 0, 0, '2025-05-02 20:50:54'),
(505, 55, 71, '138', 0, 0, '2025-05-02 20:50:54'),
(506, 55, 72, '142', 0, 0, '2025-05-02 20:50:54'),
(507, 55, 73, '146', 0, 0, '2025-05-02 20:50:54'),
(508, 55, 74, 'false', 1, 1, '2025-05-02 20:50:54'),
(509, 55, 75, 'true', 1, 1, '2025-05-02 20:50:54'),
(510, 55, 76, 'false', 1, 1, '2025-05-02 20:50:54'),
(511, 56, 21, '27', 0, 0, '2025-05-02 20:51:15'),
(512, 56, 22, '31', 0, 0, '2025-05-02 20:51:15'),
(513, 56, 23, '34', 0, 0, '2025-05-02 20:51:15'),
(514, 56, 24, '37', 0, 0, '2025-05-02 20:51:15'),
(515, 56, 25, '43', 0, 0, '2025-05-02 20:51:15'),
(516, 56, 26, 'false', 1, 3, '2025-05-02 20:51:15'),
(517, 56, 27, 'true', 1, 3, '2025-05-02 20:51:15'),
(518, 56, 28, 'false', 1, 3, '2025-05-02 20:51:15'),
(519, 56, 29, 'sfkgslş', 1, 9, '2025-05-02 20:51:15'),
(520, 56, 30, 'spfjkgfsdl', 1, 9, '2025-05-02 20:51:15'),
(521, 57, 77, '151', 0, 0, '2025-05-02 20:54:26'),
(522, 57, 78, '154', 0, 0, '2025-05-02 20:54:26'),
(523, 57, 79, '158', 0, 0, '2025-05-02 20:54:26'),
(524, 57, 80, '163', 0, 0, '2025-05-02 20:54:26'),
(525, 57, 81, '166', 0, 0, '2025-05-02 20:54:26'),
(526, 57, 82, 'true', 1, 3, '2025-05-02 20:54:26'),
(527, 57, 83, 'true', 0, 0, '2025-05-02 20:54:26'),
(528, 57, 84, 'false', 0, 0, '2025-05-02 20:54:26'),
(529, 57, 85, 'dhfsg', 1, 7, '2025-05-02 20:54:26'),
(530, 57, 86, 'sfgdg', 1, 6, '2025-05-02 20:54:26'),
(531, 58, 11, '3', 0, 0, '2025-05-02 20:54:43'),
(532, 58, 12, '15', 0, 0, '2025-05-02 20:54:43'),
(533, 58, 13, '11', 0, 0, '2025-05-02 20:54:43'),
(534, 58, 14, '19', 0, 0, '2025-05-02 20:54:43'),
(535, 58, 15, '22', 0, 0, '2025-05-02 20:54:43'),
(536, 58, 16, 'true', 0, 0, '2025-05-02 20:54:43'),
(537, 58, 17, 'true', 1, 3, '2025-05-02 20:54:43'),
(538, 58, 18, 'true', 1, 4, '2025-05-02 20:54:43'),
(539, 58, 19, 'dthshdth', 1, 7, '2025-05-02 20:54:43'),
(540, 58, 20, 'shdgshjs', 1, 8, '2025-05-02 20:54:43'),
(541, 59, 69, '131', 0, 0, '2025-05-02 20:54:59'),
(542, 59, 70, '134', 0, 0, '2025-05-02 20:54:59'),
(543, 59, 71, '138', 0, 0, '2025-05-02 20:54:59'),
(544, 59, 72, '142', 0, 0, '2025-05-02 20:54:59'),
(545, 59, 73, '146', 0, 0, '2025-05-02 20:54:59'),
(546, 59, 74, 'true', 0, 0, '2025-05-02 20:54:59'),
(547, 59, 75, 'false', 0, 0, '2025-05-02 20:54:59'),
(548, 59, 76, 'true', 0, 0, '2025-05-02 20:54:59'),
(549, 60, 21, '27', 0, 0, '2025-05-02 20:55:19'),
(550, 60, 22, '32', 0, 0, '2025-05-02 20:55:19'),
(551, 60, 23, '34', 0, 0, '2025-05-02 20:55:19'),
(552, 60, 24, '37', 0, 0, '2025-05-02 20:55:19'),
(553, 60, 25, '42', 0, 0, '2025-05-02 20:55:19'),
(554, 60, 26, 'true', 0, 0, '2025-05-02 20:55:19'),
(555, 60, 27, 'true', 1, 3, '2025-05-02 20:55:19'),
(556, 60, 28, 'true', 0, 0, '2025-05-02 20:55:19'),
(557, 60, 29, 'dfhgsh', 1, 9, '2025-05-02 20:55:19'),
(558, 60, 30, 'where', 1, 9, '2025-05-02 20:55:19'),
(559, 61, 77, '151', 0, 0, '2025-05-02 20:57:10'),
(560, 61, 78, '154', 0, 0, '2025-05-02 20:57:10'),
(561, 61, 79, '157', 0, 0, '2025-05-02 20:57:10'),
(562, 61, 80, '162', 0, 0, '2025-05-02 20:57:10'),
(563, 61, 81, '167', 0, 0, '2025-05-02 20:57:10'),
(564, 61, 82, 'true', 1, 3, '2025-05-02 20:57:10'),
(565, 61, 83, 'false', 1, 3, '2025-05-02 20:57:10'),
(566, 61, 84, 'true', 1, 3, '2025-05-02 20:57:10'),
(567, 61, 85, 'sgthadh', 1, 7, '2025-05-02 20:57:10'),
(568, 61, 86, 'sdfhdsh', 1, 6, '2025-05-02 20:57:10'),
(569, 62, 11, '2', 0, 0, '2025-05-02 20:57:26'),
(570, 62, 12, '15', 0, 0, '2025-05-02 20:57:26'),
(571, 62, 13, '11', 0, 0, '2025-05-02 20:57:26'),
(572, 62, 14, '19', 0, 0, '2025-05-02 20:57:26'),
(573, 62, 15, '22', 0, 0, '2025-05-02 20:57:26'),
(574, 62, 16, 'true', 0, 0, '2025-05-02 20:57:26'),
(575, 62, 17, 'true', 1, 3, '2025-05-02 20:57:26'),
(576, 62, 18, 'true', 1, 4, '2025-05-02 20:57:26'),
(577, 62, 19, 'shsdgh', 1, 7, '2025-05-02 20:57:26'),
(578, 62, 20, 'sdghdsht', 1, 8, '2025-05-02 20:57:26'),
(579, 63, 69, '131', 0, 0, '2025-05-02 20:57:40'),
(580, 63, 70, '134', 0, 0, '2025-05-02 20:57:40'),
(581, 63, 71, '138', 0, 0, '2025-05-02 20:57:40'),
(582, 63, 72, '142', 0, 0, '2025-05-02 20:57:40'),
(583, 63, 73, '146', 0, 0, '2025-05-02 20:57:40'),
(584, 63, 74, 'true', 0, 0, '2025-05-02 20:57:40'),
(585, 63, 75, 'true', 1, 1, '2025-05-02 20:57:40'),
(586, 63, 76, 'true', 0, 0, '2025-05-02 20:57:40'),
(587, 64, 21, '27', 0, 0, '2025-05-02 20:57:56'),
(588, 64, 22, '31', 0, 0, '2025-05-02 20:57:56'),
(589, 64, 23, '34', 0, 0, '2025-05-02 20:57:56'),
(590, 64, 24, '37', 0, 0, '2025-05-02 20:57:56'),
(591, 64, 25, '42', 0, 0, '2025-05-02 20:57:56'),
(592, 64, 26, 'true', 0, 0, '2025-05-02 20:57:56'),
(593, 64, 27, 'true', 1, 3, '2025-05-02 20:57:56'),
(594, 64, 28, 'true', 0, 0, '2025-05-02 20:57:56'),
(595, 64, 29, 'dhtadh', 1, 9, '2025-05-02 20:57:56'),
(596, 64, 30, 'dghaht', 1, 9, '2025-05-02 20:57:56'),
(597, 65, 77, '151', 0, 0, '2025-05-02 21:01:45'),
(598, 65, 78, '154', 0, 0, '2025-05-02 21:01:45'),
(599, 65, 79, '158', 0, 0, '2025-05-02 21:01:45'),
(600, 65, 80, '162', 0, 0, '2025-05-02 21:01:45'),
(601, 65, 81, '167', 0, 0, '2025-05-02 21:01:45'),
(602, 65, 82, 'true', 1, 3, '2025-05-02 21:01:45'),
(603, 65, 83, 'false', 1, 3, '2025-05-02 21:01:45'),
(604, 65, 84, 'true', 1, 3, '2025-05-02 21:01:45'),
(605, 65, 85, 'property', 1, 7, '2025-05-02 21:01:45'),
(606, 65, 86, 'div', 1, 6, '2025-05-02 21:01:45'),
(607, 66, 11, '3', 0, 0, '2025-05-02 21:02:20'),
(608, 66, 12, '15', 0, 0, '2025-05-02 21:02:20'),
(609, 66, 13, '11', 0, 0, '2025-05-02 21:02:20'),
(610, 66, 14, '19', 0, 0, '2025-05-02 21:02:20'),
(611, 66, 15, '22', 0, 0, '2025-05-02 21:02:20'),
(612, 66, 16, 'false', 1, 6, '2025-05-02 21:02:20'),
(613, 66, 17, 'true', 1, 3, '2025-05-02 21:02:20'),
(614, 66, 18, 'true', 1, 4, '2025-05-02 21:02:20'),
(615, 66, 19, 'function', 1, 7, '2025-05-02 21:02:20'),
(616, 66, 20, 'var_dump', 1, 8, '2025-05-02 21:02:20'),
(617, 67, 69, '131', 0, 0, '2025-05-02 21:02:44'),
(618, 67, 70, '134', 0, 0, '2025-05-02 21:02:44'),
(619, 67, 71, '138', 0, 0, '2025-05-02 21:02:44'),
(620, 67, 72, '142', 0, 0, '2025-05-02 21:02:44'),
(621, 67, 73, '146', 0, 0, '2025-05-02 21:02:44'),
(622, 67, 74, 'false', 1, 1, '2025-05-02 21:02:44'),
(623, 67, 75, 'true', 1, 1, '2025-05-02 21:02:44'),
(624, 67, 76, 'false', 1, 1, '2025-05-02 21:02:44'),
(625, 68, 21, '26', 0, 0, '2025-05-02 21:03:22'),
(626, 68, 22, '31', 0, 0, '2025-05-02 21:03:22'),
(627, 68, 23, '34', 0, 0, '2025-05-02 21:03:22'),
(628, 68, 24, '37', 0, 0, '2025-05-02 21:03:22'),
(629, 68, 25, '43', 0, 0, '2025-05-02 21:03:22'),
(630, 68, 26, 'false', 1, 3, '2025-05-02 21:03:22'),
(631, 68, 27, 'true', 1, 3, '2025-05-02 21:03:22'),
(632, 68, 28, 'false', 1, 3, '2025-05-02 21:03:22'),
(633, 68, 29, 'UPDATE', 1, 9, '2025-05-02 21:03:22'),
(634, 68, 30, 'WHERE', 1, 9, '2025-05-02 21:03:22'),
(635, 69, 77, '151', 0, 0, '2025-05-02 21:05:42'),
(636, 69, 78, '154', 0, 0, '2025-05-02 21:05:42'),
(637, 69, 79, '158', 0, 0, '2025-05-02 21:05:42'),
(638, 69, 80, '162', 0, 0, '2025-05-02 21:05:42'),
(639, 69, 81, '167', 0, 0, '2025-05-02 21:05:42'),
(640, 69, 82, 'true', 1, 3, '2025-05-02 21:05:42'),
(641, 69, 83, 'false', 1, 3, '2025-05-02 21:05:42'),
(642, 69, 84, 'true', 1, 3, '2025-05-02 21:05:42'),
(643, 69, 85, 'sghg', 1, 7, '2025-05-02 21:05:42'),
(644, 69, 86, 'sghsdhg', 1, 6, '2025-05-02 21:05:42'),
(645, 70, 11, '3', 0, 0, '2025-05-02 21:05:57'),
(646, 70, 12, '15', 0, 0, '2025-05-02 21:05:57'),
(647, 70, 13, '11', 0, 0, '2025-05-02 21:05:57'),
(648, 70, 14, '19', 0, 0, '2025-05-02 21:05:57'),
(649, 70, 15, '22', 0, 0, '2025-05-02 21:05:57'),
(650, 70, 16, 'true', 0, 0, '2025-05-02 21:05:57'),
(651, 70, 17, 'true', 1, 3, '2025-05-02 21:05:57'),
(652, 70, 18, 'true', 1, 4, '2025-05-02 21:05:57'),
(653, 70, 19, 'dfhshdh', 1, 7, '2025-05-02 21:05:57'),
(654, 70, 20, 'sdghdfh', 1, 8, '2025-05-02 21:05:57'),
(655, 71, 69, '131', 0, 0, '2025-05-02 21:06:10'),
(656, 71, 70, '134', 0, 0, '2025-05-02 21:06:10'),
(657, 71, 71, '138', 0, 0, '2025-05-02 21:06:10'),
(658, 71, 72, '142', 0, 0, '2025-05-02 21:06:10'),
(659, 71, 73, '146', 0, 0, '2025-05-02 21:06:10'),
(660, 71, 74, 'true', 0, 0, '2025-05-02 21:06:10'),
(661, 71, 75, 'true', 1, 1, '2025-05-02 21:06:10'),
(662, 71, 76, 'true', 0, 0, '2025-05-02 21:06:10'),
(663, 72, 21, '27', 0, 0, '2025-05-02 21:06:29'),
(664, 72, 22, '31', 0, 0, '2025-05-02 21:06:29'),
(665, 72, 23, '34', 0, 0, '2025-05-02 21:06:29'),
(666, 72, 24, '37', 0, 0, '2025-05-02 21:06:29'),
(667, 72, 25, '42', 0, 0, '2025-05-02 21:06:29'),
(668, 72, 26, 'false', 1, 3, '2025-05-02 21:06:29'),
(669, 72, 27, 'true', 1, 3, '2025-05-02 21:06:29'),
(670, 72, 28, 'false', 1, 3, '2025-05-02 21:06:29'),
(671, 72, 29, 'shadghf', 1, 9, '2025-05-02 21:06:29'),
(672, 72, 30, 'ghsdhd', 1, 9, '2025-05-02 21:06:29'),
(673, 73, 77, '151', 0, 0, '2025-05-02 21:08:19'),
(674, 73, 78, '154', 0, 0, '2025-05-02 21:08:19'),
(675, 73, 79, '158', 0, 0, '2025-05-02 21:08:19'),
(676, 73, 80, '162', 0, 0, '2025-05-02 21:08:19'),
(677, 73, 81, '167', 0, 0, '2025-05-02 21:08:19'),
(678, 73, 82, 'true', 1, 3, '2025-05-02 21:08:19'),
(679, 73, 83, 'true', 0, 0, '2025-05-02 21:08:19'),
(680, 73, 84, 'true', 1, 3, '2025-05-02 21:08:19'),
(681, 73, 85, 'dfhsgh', 1, 7, '2025-05-02 21:08:19'),
(682, 73, 86, 'fgjg', 1, 6, '2025-05-02 21:08:19'),
(683, 74, 11, '3', 0, 0, '2025-05-02 21:08:33'),
(684, 74, 12, '15', 0, 0, '2025-05-02 21:08:33'),
(685, 74, 13, '11', 0, 0, '2025-05-02 21:08:33'),
(686, 74, 14, '19', 0, 0, '2025-05-02 21:08:33'),
(687, 74, 15, '22', 0, 0, '2025-05-02 21:08:33'),
(688, 74, 16, 'true', 0, 0, '2025-05-02 21:08:33'),
(689, 74, 17, 'true', 1, 3, '2025-05-02 21:08:33'),
(690, 74, 18, 'true', 1, 4, '2025-05-02 21:08:33'),
(691, 74, 19, 'shgfhsgd', 1, 7, '2025-05-02 21:08:33'),
(692, 74, 20, 'hsdghd', 1, 8, '2025-05-02 21:08:33'),
(693, 75, 69, '131', 0, 0, '2025-05-02 21:08:48'),
(694, 75, 70, '134', 0, 0, '2025-05-02 21:08:48'),
(695, 75, 71, '138', 0, 0, '2025-05-02 21:08:48'),
(696, 75, 72, '142', 0, 0, '2025-05-02 21:08:48'),
(697, 75, 73, '146', 0, 0, '2025-05-02 21:08:48'),
(698, 75, 74, 'true', 0, 0, '2025-05-02 21:08:48'),
(699, 75, 75, 'true', 1, 1, '2025-05-02 21:08:48'),
(700, 75, 76, 'false', 1, 1, '2025-05-02 21:08:48'),
(701, 76, 21, '27', 0, 0, '2025-05-02 21:09:04'),
(702, 76, 22, '31', 0, 0, '2025-05-02 21:09:04'),
(703, 76, 23, '34', 0, 0, '2025-05-02 21:09:04'),
(704, 76, 24, '37', 0, 0, '2025-05-02 21:09:04'),
(705, 76, 25, '42', 0, 0, '2025-05-02 21:09:04'),
(706, 76, 26, 'true', 0, 0, '2025-05-02 21:09:04'),
(707, 76, 27, 'true', 1, 3, '2025-05-02 21:09:04'),
(708, 76, 28, 'false', 1, 3, '2025-05-02 21:09:04'),
(709, 76, 29, 'fshgfshs', 1, 9, '2025-05-02 21:09:04'),
(710, 76, 30, 'fgjsj', 1, 9, '2025-05-02 21:09:04');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `test_questions`
--

CREATE TABLE `test_questions` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `question_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('multiple_choice','true_false','text') COLLATE utf8mb4_unicode_ci NOT NULL,
  `points` int(11) NOT NULL DEFAULT '1',
  `correct_answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_number` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `test_questions`
--

INSERT INTO `test_questions` (`id`, `test_id`, `question_text`, `question_type`, `points`, `correct_answer`, `order_number`, `created_at`, `updated_at`) VALUES
(11, 9, 'PHP\'de bir değişken nasıl tanımlanır?', 'multiple_choice', 7, '$isim = \"Ahmet\";', 0, '2025-04-30 21:26:36', '2025-04-30 21:26:36'),
(12, 9, 'Aşağıdakilerden hangisi bir PHP döngüsüdür?', 'multiple_choice', 5, 'foreach', 0, '2025-04-30 21:27:24', '2025-04-30 21:28:51'),
(13, 9, 'PHP’de dizinin eleman sayısını öğrenmek için hangi fonksiyon kullanılır?', 'multiple_choice', 6, 'count()', 0, '2025-04-30 21:28:24', '2025-04-30 21:28:24'),
(14, 9, 'PHP dosyaları genellikle hangi uzantıyla kaydedilir?', 'multiple_choice', 3, '.php', 0, '2025-04-30 21:29:30', '2025-04-30 21:29:30'),
(15, 9, 'PHP’de bir fonksiyon nasıl tanımlanır?', 'multiple_choice', 6, 'function foo() ', 0, '2025-04-30 21:30:13', '2025-04-30 21:30:13'),
(16, 9, 'PHP, istemci taraflı çalışan bir programlama dilidir.', 'true_false', 6, 'false', 0, '2025-04-30 21:30:30', '2025-04-30 21:30:30'),
(17, 9, 'PHP ile veritabanı bağlantısı yapılabilir.', 'true_false', 3, 'true', 0, '2025-04-30 21:30:42', '2025-04-30 21:30:42'),
(18, 9, ' PHP’de tüm değişkenler dolar işareti ($) ile başlar.', 'true_false', 4, 'true', 0, '2025-04-30 21:30:59', '2025-04-30 21:30:59'),
(19, 9, 'PHP’de bir fonksiyon tanımlamak için kullanılan anahtar kelime nedir?', 'text', 7, 'function', 0, '2025-04-30 21:31:22', '2025-04-30 21:31:22'),
(20, 9, 'PHP’de hata ayıklama sırasında hata mesajlarını ekrana yazdırmak için hangi fonksiyon kullanılır?', 'text', 8, 'var_dump', 0, '2025-04-30 21:32:18', '2025-04-30 21:32:18'),
(21, 10, 'Veritabanında yeni veri eklemek için hangi SQL komutu kullanılır?', 'multiple_choice', 6, 'INSERT', 0, '2025-04-30 21:34:34', '2025-04-30 21:34:34'),
(22, 10, 'Aşağıdakilerden hangisi SQL’de bir tablo oluşturmak için kullanılır?', 'multiple_choice', 6, 'CREATE TABLE', 0, '2025-04-30 21:35:11', '2025-04-30 21:35:11'),
(23, 10, 'SQL’de bir veriyi silmek için kullanılan komut hangisidir?', 'multiple_choice', 6, 'DELETE', 0, '2025-04-30 21:35:40', '2025-04-30 21:35:40'),
(24, 10, '\"students\" tablosundaki tüm kayıtları listelemek için hangi komut doğrudur?', 'multiple_choice', 6, 'SELECT * FROM students;', 0, '2025-04-30 21:36:17', '2025-04-30 21:36:17'),
(25, 10, 'SQL\'de tabloya yeni bir sütun eklemek için hangi komut kullanılır?', 'multiple_choice', 6, 'ADD COLUMN', 0, '2025-04-30 21:36:52', '2025-04-30 21:36:52'),
(26, 10, 'SQL büyük/küçük harf duyarlıdır.', 'true_false', 3, 'false', 0, '2025-04-30 21:37:17', '2025-04-30 21:37:17'),
(27, 10, 'SQL’de birden fazla tablo birleştirilebilir.', 'true_false', 3, 'true', 0, '2025-04-30 21:37:31', '2025-04-30 21:37:31'),
(28, 10, 'PRIMARY KEY değeri tekrar edebilir.', 'true_false', 3, 'false', 0, '2025-04-30 21:37:42', '2025-04-30 21:37:42'),
(29, 10, 'SQL’de veri güncellemek için kullanılan komut nedir?', 'text', 9, 'UPDATE', 0, '2025-04-30 21:38:03', '2025-04-30 21:38:03'),
(30, 10, 'SQL’de koşullu filtreleme yapmak için hangi anahtar kelime kullanılır?', 'text', 9, 'WHERE', 0, '2025-04-30 21:38:23', '2025-04-30 21:38:23'),
(31, 11, 'Web tarayıcısı ile sunucu arasındaki iletişimi sağlayan protokol aşağıdakilerden hangisidir?', 'multiple_choice', 5, 'HTTP', 0, '2025-04-30 21:40:35', '2025-04-30 21:40:35'),
(32, 11, 'Aşağıdaki HTTP durum kodlarından hangisi \"Bulunamadı\" anlamına gelir?', 'multiple_choice', 5, '404', 0, '2025-04-30 21:41:02', '2025-04-30 21:41:02'),
(33, 11, 'Bir web sayfasının kullanıcıya gösterilmesini sağlayan istemci tarafı dilleri hangileridir?', 'multiple_choice', 5, 'HTML, CSS, JavaScript', 0, '2025-04-30 21:41:40', '2025-04-30 21:41:40'),
(34, 11, 'Web’de bir form gönderildiğinde hangi HTTP metodu genellikle veri göndermek için kullanılır?', 'multiple_choice', 5, 'POST', 0, '2025-04-30 21:42:05', '2025-04-30 21:42:05'),
(35, 11, ' Aşağıdakilerden hangisi bir web adresidir?', 'multiple_choice', 5, 'https://www.example.com', 0, '2025-04-30 21:42:39', '2025-04-30 21:42:39'),
(36, 11, 'HTML, sunucu taraflı bir programlama dilidir.', 'true_false', 3, 'false', 0, '2025-04-30 21:42:53', '2025-04-30 21:42:53'),
(37, 11, ' HTTP istekleri sadece GET ve POST’tan oluşur.', 'true_false', 3, 'false', 0, '2025-04-30 21:43:06', '2025-04-30 21:43:06'),
(38, 11, 'CSS, bir web sayfasının stilini ve görünümünü belirler.', 'true_false', 3, 'true', 0, '2025-04-30 21:43:17', '2025-04-30 21:43:17'),
(39, 11, 'Web tarayıcısının, kullanıcıdan veri almasını sağlayan HTML öğesi nedir?', 'text', 7, 'form', 0, '2025-04-30 21:43:30', '2025-04-30 21:43:52'),
(40, 11, 'Web sayfalarında görünüm tasarımı için kullanılan dil nedir?', 'text', 7, 'css', 0, '2025-04-30 21:43:43', '2025-04-30 21:43:43'),
(41, 12, 'Python’da ekrana çıktı vermek için hangi komut kullanılır?', 'multiple_choice', 5, 'print', 0, '2025-04-30 21:46:24', '2025-04-30 21:46:24'),
(42, 12, 'Aşağıdakilerden hangisi Python’da liste tanımlamasıdır?', 'multiple_choice', 5, 'list = [1, 2, 3]', 0, '2025-04-30 21:46:57', '2025-04-30 21:46:57'),
(43, 12, 'Python’da koşullu ifadede eşitlik kontrolü nasıl yapılır?', 'multiple_choice', 5, '==', 0, '2025-04-30 21:47:27', '2025-04-30 21:47:27'),
(44, 12, 'Aşağıdakilerden hangisi Python’da hata fırlatma ifadesidir?', 'multiple_choice', 5, 'raise', 0, '2025-04-30 21:48:29', '2025-04-30 21:48:29'),
(45, 12, 'range(5) ifadesi hangi sayıları üretir?', 'multiple_choice', 5, '0,1,2,3,4', 0, '2025-04-30 21:49:02', '2025-04-30 21:50:00'),
(46, 12, 'python yorumlanan (interpreted) bir dildir.', 'true_false', 3, 'true', 0, '2025-04-30 21:50:24', '2025-04-30 21:50:31'),
(47, 12, 'Python’da her satırın sonunda noktalı virgül (;) zorunludur.', 'true_false', 3, 'false', 0, '2025-04-30 21:50:43', '2025-04-30 21:50:43'),
(48, 12, 'Python\'da bir değişkenin türü sabittir, sonradan değiştirilemez.', 'true_false', 3, 'false', 0, '2025-04-30 21:50:55', '2025-04-30 21:50:55'),
(49, 12, 'Python’da hata yakalamak için kullanılan anahtar kelime nedir?', 'text', 7, 'try', 0, '2025-04-30 21:51:09', '2025-04-30 21:51:09'),
(50, 12, 'Python’da döngü oluşturmak için kullanılan anahtar kelime nedir?', 'text', 6, 'for', 0, '2025-04-30 21:51:27', '2025-04-30 21:51:27'),
(51, 13, 'Pandas kütüphanesinde bir CSV dosyasını okumak için hangi fonksiyon kullanılır?', 'multiple_choice', 5, 'read_csv() ', 0, '2025-04-30 21:52:58', '2025-04-30 21:52:58'),
(52, 13, 'Aşağıdakilerden hangisi bir DataFrame’in sütunlarına erişmenin doğru yoludur?', 'multiple_choice', 5, 'df[\"Column\"] ', 0, '2025-04-30 21:53:27', '2025-04-30 21:53:27'),
(53, 13, 'Bir sütundaki eksik (NaN) verileri silmek için hangi komut kullanılır?', 'multiple_choice', 5, 'dropna() ', 0, '2025-04-30 21:53:52', '2025-04-30 21:53:52'),
(54, 13, 'df.describe() komutu ne işe yarar?', 'multiple_choice', 5, 'Sayısal özet istatistikleri gösterir', 0, '2025-04-30 21:54:22', '2025-04-30 21:54:22'),
(55, 13, 'Aşağıdaki hangi pandas fonksiyonu ile veriler grup bazlı özetlenebilir?', 'multiple_choice', 5, 'groupby()', 0, '2025-04-30 21:54:51', '2025-04-30 21:54:51'),
(56, 13, 'Pandas ile sadece CSV dosyaları okunabilir.', 'true_false', 3, 'false', 0, '2025-04-30 21:55:12', '2025-04-30 21:55:12'),
(57, 13, 'NaN değerler sayısal işlemlerde hata oluşturabilir.', 'true_false', 3, 'true', 0, '2025-04-30 21:55:22', '2025-04-30 21:55:22'),
(58, 13, 'fillna() metodu eksik verileri sabit bir değerle doldurmak için kullanılır.', 'true_false', 3, 'true', 0, '2025-04-30 21:55:39', '2025-04-30 21:55:39'),
(59, 13, 'Pandas’ta satır veya sütunları silmek için kullanılan fonksiyon nedir?', 'text', 7, 'drop', 0, '2025-04-30 21:55:53', '2025-04-30 21:55:53'),
(60, 13, 'Sütunlardaki ortalamayı hesaplamak için kullanılan fonksiyon nedir?', 'text', 7, 'mean', 0, '2025-04-30 21:56:06', '2025-04-30 21:56:06'),
(61, 14, 'Aşağıdakilerden hangisi merkezi eğilim ölçülerinden biridir?', 'multiple_choice', 5, 'Medyan', 0, '2025-04-30 21:57:41', '2025-04-30 21:57:41'),
(62, 14, 'Bir veri setindeki en sık görülen değere ne ad verilir?', 'multiple_choice', 5, 'Mod', 0, '2025-04-30 21:58:10', '2025-04-30 21:58:10'),
(63, 14, 'Aşağıdakilerden hangisi değişkenliğin (dağılımın) ölçüsüdür?', 'multiple_choice', 5, 'Varyans', 0, '2025-04-30 21:58:41', '2025-04-30 21:58:41'),
(64, 14, 'p-değeri 0.03 ise ve anlamlılık seviyesi 0.05 ise hangi yorum doğrudur?', 'multiple_choice', 1, 'Anlamlı sonuç vardır', 0, '2025-04-30 21:59:20', '2025-04-30 21:59:20'),
(65, 14, 'Standart sapma sıfırsa, tüm değerler birbirine eşittir.', 'true_false', 3, 'true', 0, '2025-04-30 21:59:36', '2025-04-30 21:59:36'),
(66, 14, 'Medyan, her zaman moddan büyüktür.', 'true_false', 3, 'false', 0, '2025-04-30 21:59:50', '2025-04-30 21:59:50'),
(67, 14, 'Verilerin ortalama değer etrafında ne kadar dağıldığını ölçen kavram nedir?', 'text', 7, 'varyans', 0, '2025-04-30 22:00:05', '2025-04-30 22:00:05'),
(68, 14, 'Normal dağılım eğrisinin tepe noktası hangi ölçüye denk gelir?', 'text', 7, 'ortalama', 0, '2025-04-30 22:00:20', '2025-04-30 22:00:20'),
(69, 15, 'Bir ekip toplantısında, fikrinizin dikkate alınmadığını hissediyorsunuz. Ne yaparsınız?', 'multiple_choice', 2, 'Söz isteyip fikrimi yeniden açıklarım', 0, '2025-04-30 22:01:42', '2025-04-30 22:01:46'),
(70, 15, 'Bir iş arkadaşınız bir hatayı fark etti ve size geri bildirim verdi. Nasıl tepki verirsiniz?', 'multiple_choice', 2, 'Geri bildirimi dikkatle dinler, gerekirse düzeltme yaparım ', 0, '2025-04-30 22:02:16', '2025-04-30 22:02:16'),
(71, 15, 'Sıkışık bir teslim tarihine yetişemeyeceğinizi fark ettiniz. Ne yaparsınız?', 'multiple_choice', 2, 'İş arkadaşlarımdan yardım isterim ', 0, '2025-04-30 22:02:49', '2025-04-30 22:02:49'),
(72, 15, 'Takım arkadaşınız sizden farklı bir çözüm önerdi. Ne yaparsınız?', 'multiple_choice', 2, 'Onu dinler, değerlendirir ve birlikte karar vermeye çalışırım', 0, '2025-04-30 22:03:17', '2025-04-30 22:03:17'),
(73, 15, 'Yeni bir teknoloji veya araç projeye dahil edildi. Daha önce hiç kullanmadınız. Ne yaparsınız?', 'multiple_choice', 2, 'İnternetten araştırma yapar, öğrenmeye çalışırım', 0, '2025-04-30 22:03:50', '2025-04-30 22:03:50'),
(74, 15, 'Etkili iletişim sadece sözlü iletişimle ilgilidir.', 'true_false', 1, 'false', 0, '2025-04-30 22:04:01', '2025-04-30 22:04:01'),
(75, 15, ' Takım çalışması, yalnızca görev paylaşımı değil aynı zamanda sorumluluk paylaşımıdır.', 'true_false', 1, 'true', 0, '2025-04-30 22:04:16', '2025-04-30 22:04:16'),
(76, 15, 'Bir işte zaman yönetimi, işin kalitesinden daha önemlidir.', 'true_false', 1, 'false', 0, '2025-04-30 22:04:31', '2025-04-30 22:04:31'),
(77, 16, 'HTML’de bir sayfa başlığı hangi etikette tanımlanır?', 'multiple_choice', 5, '<title>', 0, '2025-05-01 14:53:00', '2025-05-01 14:53:00'),
(78, 16, 'CSS’de bir elementi sınıf adına göre seçmek için hangi sembol kullanılır?', 'multiple_choice', 5, '.', 0, '2025-05-01 14:53:27', '2025-05-01 14:53:27'),
(79, 16, 'HTML5\'te form verilerini sunucuya göndermek için kullanılan öğe hangisidir?', 'multiple_choice', 5, '<form>', 0, '2025-05-01 14:53:58', '2025-05-01 14:53:58'),
(80, 16, 'Aşağıdaki CSS ifadelerinden hangisi yazı rengini kırmızı yapar?', 'multiple_choice', 5, 'color: red;', 0, '2025-05-01 14:54:26', '2025-05-01 14:54:26'),
(81, 16, '\"display: flex;\" özelliği ne işe yarar?', 'multiple_choice', 5, 'Elemanları yatay/dikey hizalamayı sağlar', 0, '2025-05-01 14:54:54', '2025-05-01 14:54:54'),
(82, 16, '<section> etiketi HTML5’e özgüdür.', 'true_false', 3, 'true', 0, '2025-05-01 14:55:07', '2025-05-01 14:55:07'),
(83, 16, 'CSS’de \"margin\" öğesi içeriğin iç boşluğunu belirler.', 'true_false', 3, 'false', 0, '2025-05-01 14:55:28', '2025-05-01 14:55:47'),
(84, 16, '<div> etiketi blok seviyesinde bir HTML öğesidir.', 'true_false', 3, 'true', 0, '2025-05-01 14:56:01', '2025-05-01 14:56:01'),
(85, 16, 'CSS kurallarında renk, yazı tipi, boşluk gibi özellikleri tanımlayan bölüme ne ad verilir?', 'text', 7, 'property', 0, '2025-05-01 14:56:22', '2025-05-01 14:56:22'),
(86, 16, 'Web sayfalarında yapısal gruplar oluşturmak için kullanılan en yaygın HTML etiketi nedir?', 'text', 6, 'div', 0, '2025-05-01 14:56:40', '2025-05-01 14:56:40');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `test_results`
--

CREATE TABLE `test_results` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `score` int(11) DEFAULT '0',
  `status` enum('not_started','in_progress','completed','expired') COLLATE utf8mb4_unicode_ci DEFAULT 'not_started',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `test_results`
--

INSERT INTO `test_results` (`id`, `application_id`, `test_id`, `start_time`, `end_time`, `score`, `status`, `created_at`, `updated_at`) VALUES
(5, 2, 9, '2025-05-02 13:52:58', '2025-05-02 13:53:19', 35, 'completed', '2025-05-02 13:52:58', '2025-05-02 13:53:19'),
(6, 2, 15, '2025-05-02 13:53:23', '2025-05-02 13:53:37', 15, 'completed', '2025-05-02 13:53:23', '2025-05-02 13:53:37'),
(7, 2, 10, '2025-05-02 13:53:39', '2025-05-02 13:54:00', 32, 'completed', '2025-05-02 13:53:39', '2025-05-02 13:54:00'),
(8, 2, 11, '2025-05-02 13:54:03', '2025-05-02 13:54:20', 42, 'completed', '2025-05-02 13:54:03', '2025-05-02 13:54:20'),
(9, 3, 9, '2025-05-02 13:57:32', '2025-05-02 13:58:02', 33, 'completed', '2025-05-02 13:57:32', '2025-05-02 13:58:02'),
(10, 3, 15, '2025-05-02 13:58:05', '2025-05-02 13:58:20', 23, 'completed', '2025-05-02 13:58:05', '2025-05-02 13:58:20'),
(11, 3, 10, '2025-05-02 13:58:22', '2025-05-02 13:58:38', 47, 'completed', '2025-05-02 13:58:22', '2025-05-02 13:58:38'),
(12, 3, 11, '2025-05-02 13:58:41', '2025-05-02 13:59:05', 42, 'completed', '2025-05-02 13:58:41', '2025-05-02 13:59:05'),
(13, 4, 9, '2025-05-02 14:02:43', '2025-05-02 14:03:01', 40, 'completed', '2025-05-02 14:02:43', '2025-05-02 14:03:01'),
(14, 4, 15, '2025-05-02 14:03:04', '2025-05-02 14:03:20', 23, 'completed', '2025-05-02 14:03:04', '2025-05-02 14:03:20'),
(15, 4, 10, '2025-05-02 14:03:23', '2025-05-02 14:03:44', 42, 'completed', '2025-05-02 14:03:23', '2025-05-02 14:03:44'),
(16, 4, 11, '2025-05-02 14:03:46', '2025-05-02 14:04:05', 35, 'completed', '2025-05-02 14:03:46', '2025-05-02 14:04:05'),
(17, 5, 9, '2025-05-02 14:10:20', '2025-05-02 14:10:58', 51, 'completed', '2025-05-02 14:10:20', '2025-05-02 14:10:58'),
(18, 5, 15, '2025-05-02 14:11:00', '2025-05-02 14:11:15', 15, 'completed', '2025-05-02 14:11:00', '2025-05-02 14:11:15'),
(19, 5, 10, '2025-05-02 14:11:17', '2025-05-02 14:11:54', 47, 'completed', '2025-05-02 14:11:17', '2025-05-02 14:11:54'),
(20, 5, 11, '2025-05-02 14:11:57', '2025-05-02 14:12:17', 42, 'completed', '2025-05-02 14:11:57', '2025-05-02 14:12:17'),
(21, 6, 9, '2025-05-02 14:23:52', '2025-05-02 14:24:08', 40, 'completed', '2025-05-02 14:23:52', '2025-05-02 14:24:08'),
(22, 6, 15, '2025-05-02 14:24:11', '2025-05-02 14:24:24', 8, 'completed', '2025-05-02 14:24:11', '2025-05-02 14:24:24'),
(23, 6, 10, '2025-05-02 14:24:27', '2025-05-02 14:24:44', 37, 'completed', '2025-05-02 14:24:27', '2025-05-02 14:24:44'),
(24, 6, 11, '2025-05-02 14:24:47', '2025-05-02 14:25:02', 35, 'completed', '2025-05-02 14:24:47', '2025-05-02 14:25:02'),
(25, 7, 9, '2025-05-02 14:27:32', '2025-05-02 14:27:47', 35, 'completed', '2025-05-02 14:27:32', '2025-05-02 14:27:47'),
(26, 7, 15, '2025-05-02 14:27:49', '2025-05-02 14:28:05', 15, 'completed', '2025-05-02 14:27:49', '2025-05-02 14:28:05'),
(27, 7, 10, '2025-05-02 14:28:07', '2025-05-02 14:28:28', 37, 'completed', '2025-05-02 14:28:07', '2025-05-02 14:28:28'),
(28, 7, 11, '2025-05-02 14:28:31', '2025-05-02 14:28:46', 35, 'completed', '2025-05-02 14:28:31', '2025-05-02 14:28:46'),
(29, 8, 12, '2025-05-02 14:35:59', '2025-05-02 14:36:29', 40, 'completed', '2025-05-02 14:35:59', '2025-05-02 14:36:29'),
(30, 8, 15, '2025-05-02 14:36:32', '2025-05-02 14:36:42', 8, 'completed', '2025-05-02 14:36:32', '2025-05-02 14:36:42'),
(31, 8, 14, '2025-05-02 14:36:46', '2025-05-02 14:37:09', 47, 'completed', '2025-05-02 14:36:46', '2025-05-02 14:37:09'),
(32, 8, 13, '2025-05-02 14:37:12', '2025-05-02 14:37:30', 35, 'completed', '2025-05-02 14:37:12', '2025-05-02 14:37:30'),
(33, 9, 12, '2025-05-02 14:39:25', '2025-05-02 14:40:10', 47, 'completed', '2025-05-02 14:39:25', '2025-05-02 14:40:10'),
(34, 9, 15, '2025-05-02 14:40:16', '2025-05-02 14:40:32', 23, 'completed', '2025-05-02 14:40:16', '2025-05-02 14:40:32'),
(35, 9, 14, '2025-05-02 14:40:37', '2025-05-02 14:41:55', 56, 'completed', '2025-05-02 14:40:37', '2025-05-02 14:41:55'),
(36, 9, 13, '2025-05-02 14:41:58', '2025-05-02 14:42:50', 48, 'completed', '2025-05-02 14:41:58', '2025-05-02 14:42:50'),
(37, 10, 12, '2025-05-02 14:55:13', '2025-05-02 14:55:32', 40, 'completed', '2025-05-02 14:55:13', '2025-05-02 14:55:32'),
(38, 10, 15, '2025-05-02 14:55:34', '2025-05-02 14:55:52', 15, 'completed', '2025-05-02 14:55:34', '2025-05-02 14:55:52'),
(39, 10, 14, '2025-05-02 14:55:55', '2025-05-02 14:56:08', 47, 'completed', '2025-05-02 14:55:55', '2025-05-02 14:56:08'),
(40, 10, 13, '2025-05-02 14:56:10', '2025-05-02 14:56:28', 35, 'completed', '2025-05-02 14:56:10', '2025-05-02 14:56:28'),
(41, 11, 12, '2025-05-02 14:58:02', '2025-05-02 14:58:27', 34, 'completed', '2025-05-02 14:58:02', '2025-05-02 14:58:27'),
(42, 11, 15, '2025-05-02 14:58:29', '2025-05-02 14:58:42', 8, 'completed', '2025-05-02 14:58:29', '2025-05-02 14:58:42'),
(43, 11, 14, '2025-05-02 14:58:45', '2025-05-02 14:59:04', 56, 'completed', '2025-05-02 14:58:45', '2025-05-02 14:59:04'),
(44, 11, 13, '2025-05-02 14:59:07', '2025-05-02 14:59:26', 48, 'completed', '2025-05-02 14:59:07', '2025-05-02 14:59:26'),
(45, 12, 13, '2025-05-02 15:01:30', '2025-05-02 15:01:44', 42, 'completed', '2025-05-02 15:01:30', '2025-05-02 15:01:44'),
(46, 12, 14, '2025-05-02 15:01:47', '2025-05-02 15:01:59', 47, 'completed', '2025-05-02 15:01:47', '2025-05-02 15:01:59'),
(47, 12, 12, '2025-05-02 15:02:01', '2025-05-02 15:02:18', 40, 'completed', '2025-05-02 15:02:01', '2025-05-02 15:02:18'),
(48, 12, 15, '2025-05-02 15:02:21', '2025-05-02 15:02:32', 0, 'completed', '2025-05-02 15:02:21', '2025-05-02 15:02:32'),
(49, 13, 12, '2025-05-02 15:04:09', '2025-05-02 15:04:26', 34, 'completed', '2025-05-02 15:04:09', '2025-05-02 15:04:26'),
(50, 13, 15, '2025-05-02 15:04:29', '2025-05-02 15:04:42', 15, 'completed', '2025-05-02 15:04:29', '2025-05-02 15:04:42'),
(51, 13, 14, '2025-05-02 15:04:45', '2025-05-02 15:04:57', 47, 'completed', '2025-05-02 15:04:45', '2025-05-02 15:04:57'),
(52, 13, 13, '2025-05-02 15:05:00', '2025-05-02 15:05:16', 42, 'completed', '2025-05-02 15:05:00', '2025-05-02 15:05:16'),
(53, 14, 16, '2025-05-02 20:49:13', '2025-05-02 20:50:07', 47, 'completed', '2025-05-02 20:49:13', '2025-05-02 20:50:07'),
(54, 14, 9, '2025-05-02 20:50:10', '2025-05-02 20:50:38', 40, 'completed', '2025-05-02 20:50:10', '2025-05-02 20:50:38'),
(55, 14, 15, '2025-05-02 20:50:41', '2025-05-02 20:50:54', 23, 'completed', '2025-05-02 20:50:41', '2025-05-02 20:50:54'),
(56, 14, 10, '2025-05-02 20:50:57', '2025-05-02 20:51:15', 47, 'completed', '2025-05-02 20:50:57', '2025-05-02 20:51:15'),
(57, 16, 16, '2025-05-02 20:54:12', '2025-05-02 20:54:26', 34, 'completed', '2025-05-02 20:54:12', '2025-05-02 20:54:26'),
(58, 16, 9, '2025-05-02 20:54:29', '2025-05-02 20:54:43', 40, 'completed', '2025-05-02 20:54:29', '2025-05-02 20:54:43'),
(59, 16, 15, '2025-05-02 20:54:46', '2025-05-02 20:54:59', 0, 'completed', '2025-05-02 20:54:46', '2025-05-02 20:54:59'),
(60, 16, 10, '2025-05-02 20:55:02', '2025-05-02 20:55:19', 37, 'completed', '2025-05-02 20:55:02', '2025-05-02 20:55:19'),
(61, 17, 16, '2025-05-02 20:56:57', '2025-05-02 20:57:10', 47, 'completed', '2025-05-02 20:56:57', '2025-05-02 20:57:10'),
(62, 17, 9, '2025-05-02 20:57:13', '2025-05-02 20:57:26', 40, 'completed', '2025-05-02 20:57:13', '2025-05-02 20:57:26'),
(63, 17, 15, '2025-05-02 20:57:28', '2025-05-02 20:57:40', 8, 'completed', '2025-05-02 20:57:28', '2025-05-02 20:57:40'),
(64, 17, 10, '2025-05-02 20:57:43', '2025-05-02 20:57:56', 37, 'completed', '2025-05-02 20:57:43', '2025-05-02 20:57:56'),
(65, 18, 16, '2025-05-02 21:00:55', '2025-05-02 21:01:45', 47, 'completed', '2025-05-02 21:00:55', '2025-05-02 21:01:45'),
(66, 18, 9, '2025-05-02 21:01:47', '2025-05-02 21:02:20', 51, 'completed', '2025-05-02 21:01:47', '2025-05-02 21:02:20'),
(67, 18, 15, '2025-05-02 21:02:24', '2025-05-02 21:02:44', 23, 'completed', '2025-05-02 21:02:24', '2025-05-02 21:02:44'),
(68, 18, 10, '2025-05-02 21:02:46', '2025-05-02 21:03:22', 47, 'completed', '2025-05-02 21:02:46', '2025-05-02 21:03:22'),
(69, 19, 16, '2025-05-02 21:05:22', '2025-05-02 21:05:42', 47, 'completed', '2025-05-02 21:05:22', '2025-05-02 21:05:42'),
(70, 19, 9, '2025-05-02 21:05:44', '2025-05-02 21:05:57', 40, 'completed', '2025-05-02 21:05:44', '2025-05-02 21:05:57'),
(71, 19, 15, '2025-05-02 21:05:59', '2025-05-02 21:06:10', 8, 'completed', '2025-05-02 21:05:59', '2025-05-02 21:06:10'),
(72, 19, 10, '2025-05-02 21:06:13', '2025-05-02 21:06:29', 47, 'completed', '2025-05-02 21:06:13', '2025-05-02 21:06:29'),
(73, 20, 16, '2025-05-02 21:08:06', '2025-05-02 21:08:19', 40, 'completed', '2025-05-02 21:08:06', '2025-05-02 21:08:19'),
(74, 20, 9, '2025-05-02 21:08:21', '2025-05-02 21:08:33', 40, 'completed', '2025-05-02 21:08:21', '2025-05-02 21:08:33'),
(75, 20, 15, '2025-05-02 21:08:36', '2025-05-02 21:08:48', 15, 'completed', '2025-05-02 21:08:36', '2025-05-02 21:08:48'),
(76, 20, 10, '2025-05-02 21:08:51', '2025-05-02 21:09:04', 42, 'completed', '2025-05-02 21:08:51', '2025-05-02 21:09:04');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','candidate') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `first_name`, `last_name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', 'admin', 'Admin', 'User', 'admin', 'active', '2025-04-25 21:29:26', '2025-04-25 22:16:46');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Tablo için indeksler `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `candidate_references`
--
ALTER TABLE `candidate_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Tablo için indeksler `candidate_skills`
--
ALTER TABLE `candidate_skills`
  ADD PRIMARY KEY (`candidate_id`,`skill_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- Tablo için indeksler `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Tablo için indeksler `educations`
--
ALTER TABLE `educations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Tablo için indeksler `experiences`
--
ALTER TABLE `experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Tablo için indeksler `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Tablo için indeksler `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Tablo için indeksler `position_tests`
--
ALTER TABLE `position_tests`
  ADD PRIMARY KEY (`position_id`,`test_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Tablo için indeksler `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Tablo için indeksler `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Tablo için indeksler `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Tablo için indeksler `test_answers`
--
ALTER TABLE `test_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_result_id` (`test_result_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Tablo için indeksler `test_questions`
--
ALTER TABLE `test_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_id` (`test_id`);

--
-- Tablo için indeksler `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Tablo için AUTO_INCREMENT değeri `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Tablo için AUTO_INCREMENT değeri `candidate_references`
--
ALTER TABLE `candidate_references`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Tablo için AUTO_INCREMENT değeri `educations`
--
ALTER TABLE `educations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `experiences`
--
ALTER TABLE `experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Tablo için AUTO_INCREMENT değeri `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;

--
-- Tablo için AUTO_INCREMENT değeri `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `test_answers`
--
ALTER TABLE `test_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=711;

--
-- Tablo için AUTO_INCREMENT değeri `test_questions`
--
ALTER TABLE `test_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- Tablo için AUTO_INCREMENT değeri `test_results`
--
ALTER TABLE `test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `candidate_references`
--
ALTER TABLE `candidate_references`
  ADD CONSTRAINT `candidate_references_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `candidate_skills`
--
ALTER TABLE `candidate_skills`
  ADD CONSTRAINT `candidate_skills_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `candidate_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `educations`
--
ALTER TABLE `educations`
  ADD CONSTRAINT `educations_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `experiences`
--
ALTER TABLE `experiences`
  ADD CONSTRAINT `experiences_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `position_tests`
--
ALTER TABLE `position_tests`
  ADD CONSTRAINT `position_tests_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `position_tests_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `test_questions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `tests_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Tablo kısıtlamaları `test_answers`
--
ALTER TABLE `test_answers`
  ADD CONSTRAINT `test_answers_ibfk_1` FOREIGN KEY (`test_result_id`) REFERENCES `test_results` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `test_questions` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `test_questions`
--
ALTER TABLE `test_questions`
  ADD CONSTRAINT `test_questions_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `test_results_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
