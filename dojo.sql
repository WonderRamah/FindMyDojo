-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 10:44 PM
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
-- Database: `dojo`
--

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `country_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`, `country_id`, `created_at`) VALUES
(1, 'New York', 1, '2025-12-13 02:37:58'),
(2, 'Los Angeles', 1, '2025-12-13 02:37:58'),
(3, 'London', 2, '2025-12-13 02:37:58'),
(4, 'Tokyo', 3, '2025-12-13 02:37:58'),
(5, 'Rio de Janeiro', 4, '2025-12-13 02:37:58'),
(6, 'Accra', 5, '2025-12-13 02:37:58');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `country_code` char(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_id`, `country_name`, `country_code`, `created_at`) VALUES
(1, 'United States', 'US', '2025-12-13 02:37:58'),
(2, 'United Kingdom', 'GB', '2025-12-13 02:37:58'),
(3, 'Japan', 'JP', '2025-12-13 02:37:58'),
(4, 'Brazil', 'BR', '2025-12-13 02:37:58'),
(5, 'Ghana', 'GH', '2025-12-13 02:37:58'),
(6, 'Algeria', 'DZ', '2025-12-17 23:20:54'),
(7, 'Angola', 'AO', '2025-12-17 23:20:54'),
(8, 'Benin', 'BJ', '2025-12-17 23:20:54'),
(9, 'Botswana', 'BW', '2025-12-17 23:20:54'),
(10, 'Burkina Faso', 'BF', '2025-12-17 23:20:54'),
(11, 'Burundi', 'BI', '2025-12-17 23:20:54'),
(12, 'Cameroon', 'CM', '2025-12-17 23:20:54'),
(13, 'Cape Verde', 'CV', '2025-12-17 23:20:54'),
(14, 'Central African Republic', 'CF', '2025-12-17 23:20:54'),
(15, 'Chad', 'TD', '2025-12-17 23:20:54'),
(16, 'Egypt', 'EG', '2025-12-17 23:20:54'),
(17, 'Ethiopia', 'ET', '2025-12-17 23:20:54'),
(18, 'Kenya', 'KE', '2025-12-17 23:20:54'),
(19, 'Liberia', 'LR', '2025-12-17 23:20:54'),
(20, 'Libya', 'LY', '2025-12-17 23:20:54'),
(21, 'Madagascar', 'MG', '2025-12-17 23:20:54'),
(22, 'Malawi', 'MW', '2025-12-17 23:20:54'),
(23, 'Mali', 'ML', '2025-12-17 23:20:54'),
(24, 'Morocco', 'MA', '2025-12-17 23:20:54'),
(25, 'Mozambique', 'MZ', '2025-12-17 23:20:54'),
(26, 'Namibia', 'NA', '2025-12-17 23:20:54'),
(27, 'Niger', 'NE', '2025-12-17 23:20:54'),
(28, 'Nigeria', 'NG', '2025-12-17 23:20:54'),
(29, 'Rwanda', 'RW', '2025-12-17 23:20:54'),
(30, 'Senegal', 'SN', '2025-12-17 23:20:54'),
(31, 'Sierra Leone', 'SL', '2025-12-17 23:20:54'),
(32, 'Somalia', 'SO', '2025-12-17 23:20:54'),
(33, 'South Africa', 'ZA', '2025-12-17 23:20:54'),
(34, 'South Sudan', 'SS', '2025-12-17 23:20:54'),
(35, 'Sudan', 'SD', '2025-12-17 23:20:54'),
(36, 'Tanzania', 'TZ', '2025-12-17 23:20:54'),
(37, 'Togo', 'TG', '2025-12-17 23:20:54'),
(38, 'Tunisia', 'TN', '2025-12-17 23:20:54'),
(39, 'Uganda', 'UG', '2025-12-17 23:20:54'),
(40, 'Zambia', 'ZM', '2025-12-17 23:20:54'),
(41, 'Zimbabwe', 'ZW', '2025-12-17 23:20:54'),
(42, 'Argentina', 'AR', '2025-12-17 23:20:54'),
(43, 'Bahamas', 'BS', '2025-12-17 23:20:54'),
(44, 'Barbados', 'BB', '2025-12-17 23:20:54'),
(45, 'Belize', 'BZ', '2025-12-17 23:20:54'),
(46, 'Bolivia', 'BO', '2025-12-17 23:20:54'),
(47, 'Canada', 'CA', '2025-12-17 23:20:54'),
(48, 'Chile', 'CL', '2025-12-17 23:20:54'),
(49, 'Colombia', 'CO', '2025-12-17 23:20:54'),
(50, 'Costa Rica', 'CR', '2025-12-17 23:20:54'),
(51, 'Cuba', 'CU', '2025-12-17 23:20:54'),
(52, 'Dominican Republic', 'DO', '2025-12-17 23:20:54'),
(53, 'Ecuador', 'EC', '2025-12-17 23:20:54'),
(54, 'El Salvador', 'SV', '2025-12-17 23:20:54'),
(55, 'Guatemala', 'GT', '2025-12-17 23:20:54'),
(56, 'Haiti', 'HT', '2025-12-17 23:20:54'),
(57, 'Honduras', 'HN', '2025-12-17 23:20:54'),
(58, 'Jamaica', 'JM', '2025-12-17 23:20:54'),
(59, 'Mexico', 'MX', '2025-12-17 23:20:54'),
(60, 'Nicaragua', 'NI', '2025-12-17 23:20:54'),
(61, 'Panama', 'PA', '2025-12-17 23:20:54'),
(62, 'Paraguay', 'PY', '2025-12-17 23:20:54'),
(63, 'Peru', 'PE', '2025-12-17 23:20:54'),
(64, 'Trinidad and Tobago', 'TT', '2025-12-17 23:20:54'),
(65, 'Uruguay', 'UY', '2025-12-17 23:20:54'),
(66, 'Venezuela', 'VE', '2025-12-17 23:20:54'),
(67, 'Afghanistan', 'AF', '2025-12-17 23:20:54'),
(68, 'Armenia', 'AM', '2025-12-17 23:20:54'),
(69, 'Azerbaijan', 'AZ', '2025-12-17 23:20:54'),
(70, 'Bahrain', 'BH', '2025-12-17 23:20:54'),
(71, 'Bangladesh', 'BD', '2025-12-17 23:20:54'),
(72, 'Bhutan', 'BT', '2025-12-17 23:20:54'),
(73, 'Brunei', 'BN', '2025-12-17 23:20:54'),
(74, 'Cambodia', 'KH', '2025-12-17 23:20:54'),
(75, 'China', 'CN', '2025-12-17 23:20:54'),
(76, 'Cyprus', 'CY', '2025-12-17 23:20:54'),
(77, 'Georgia', 'GE', '2025-12-17 23:20:54'),
(78, 'India', 'IN', '2025-12-17 23:20:54'),
(79, 'Indonesia', 'ID', '2025-12-17 23:20:54'),
(80, 'Iran', 'IR', '2025-12-17 23:20:54'),
(81, 'Iraq', 'IQ', '2025-12-17 23:20:54'),
(82, 'Israel', 'IL', '2025-12-17 23:20:54'),
(83, 'Jordan', 'JO', '2025-12-17 23:20:54'),
(84, 'Kazakhstan', 'KZ', '2025-12-17 23:20:54'),
(85, 'Kuwait', 'KW', '2025-12-17 23:20:54'),
(86, 'Kyrgyzstan', 'KG', '2025-12-17 23:20:54'),
(87, 'Laos', 'LA', '2025-12-17 23:20:54'),
(88, 'Lebanon', 'LB', '2025-12-17 23:20:54'),
(89, 'Malaysia', 'MY', '2025-12-17 23:20:54'),
(90, 'Maldives', 'MV', '2025-12-17 23:20:54'),
(91, 'Mongolia', 'MN', '2025-12-17 23:20:54'),
(92, 'Myanmar', 'MM', '2025-12-17 23:20:54'),
(93, 'Nepal', 'NP', '2025-12-17 23:20:54'),
(94, 'North Korea', 'KP', '2025-12-17 23:20:54'),
(95, 'Oman', 'OM', '2025-12-17 23:20:54'),
(96, 'Pakistan', 'PK', '2025-12-17 23:20:54'),
(97, 'Philippines', 'PH', '2025-12-17 23:20:54'),
(98, 'Qatar', 'QA', '2025-12-17 23:20:54'),
(99, 'Saudi Arabia', 'SA', '2025-12-17 23:20:54'),
(100, 'Singapore', 'SG', '2025-12-17 23:20:54'),
(101, 'South Korea', 'KR', '2025-12-17 23:20:54'),
(102, 'Sri Lanka', 'LK', '2025-12-17 23:20:54'),
(103, 'Syria', 'SY', '2025-12-17 23:20:54'),
(104, 'Taiwan', 'TW', '2025-12-17 23:20:54'),
(105, 'Tajikistan', 'TJ', '2025-12-17 23:20:54'),
(106, 'Turkmenistan', 'TM', '2025-12-17 23:20:54'),
(107, 'United Arab Emirates', 'AE', '2025-12-17 23:20:54'),
(108, 'Uzbekistan', 'UZ', '2025-12-17 23:20:54'),
(109, 'Vietnam', 'VN', '2025-12-17 23:20:54'),
(110, 'Yemen', 'YE', '2025-12-17 23:20:54'),
(111, 'Albania', 'AL', '2025-12-17 23:20:54'),
(112, 'Andorra', 'AD', '2025-12-17 23:20:54'),
(113, 'Austria', 'AT', '2025-12-17 23:20:54'),
(114, 'Belarus', 'BY', '2025-12-17 23:20:54'),
(115, 'Belgium', 'BE', '2025-12-17 23:20:54'),
(116, 'Bosnia and Herzegovina', 'BA', '2025-12-17 23:20:54'),
(117, 'Bulgaria', 'BG', '2025-12-17 23:20:54'),
(118, 'Croatia', 'HR', '2025-12-17 23:20:54'),
(119, 'Czech Republic', 'CZ', '2025-12-17 23:20:54'),
(120, 'Denmark', 'DK', '2025-12-17 23:20:54'),
(121, 'Estonia', 'EE', '2025-12-17 23:20:54'),
(122, 'Finland', 'FI', '2025-12-17 23:20:54'),
(123, 'France', 'FR', '2025-12-17 23:20:54'),
(124, 'Germany', 'DE', '2025-12-17 23:20:54'),
(125, 'Greece', 'GR', '2025-12-17 23:20:54'),
(126, 'Hungary', 'HU', '2025-12-17 23:20:54'),
(127, 'Iceland', 'IS', '2025-12-17 23:20:54'),
(128, 'Ireland', 'IE', '2025-12-17 23:20:54'),
(129, 'Italy', 'IT', '2025-12-17 23:20:54'),
(130, 'Kosovo', 'XK', '2025-12-17 23:20:54'),
(131, 'Latvia', 'LV', '2025-12-17 23:20:54'),
(132, 'Lithuania', 'LT', '2025-12-17 23:20:54'),
(133, 'Luxembourg', 'LU', '2025-12-17 23:20:54'),
(134, 'Malta', 'MT', '2025-12-17 23:20:54'),
(135, 'Moldova', 'MD', '2025-12-17 23:20:54'),
(136, 'Monaco', 'MC', '2025-12-17 23:20:54'),
(137, 'Montenegro', 'ME', '2025-12-17 23:20:54'),
(138, 'Netherlands', 'NL', '2025-12-17 23:20:54'),
(139, 'Norway', 'NO', '2025-12-17 23:20:54'),
(140, 'Poland', 'PL', '2025-12-17 23:20:54'),
(141, 'Portugal', 'PT', '2025-12-17 23:20:54'),
(142, 'Romania', 'RO', '2025-12-17 23:20:54'),
(143, 'Russia', 'RU', '2025-12-17 23:20:54'),
(144, 'Serbia', 'RS', '2025-12-17 23:20:54'),
(145, 'Slovakia', 'SK', '2025-12-17 23:20:54'),
(146, 'Slovenia', 'SI', '2025-12-17 23:20:54'),
(147, 'Spain', 'ES', '2025-12-17 23:20:54'),
(148, 'Sweden', 'SE', '2025-12-17 23:20:54'),
(149, 'Switzerland', 'CH', '2025-12-17 23:20:54'),
(150, 'Ukraine', 'UA', '2025-12-17 23:20:54'),
(151, 'Australia', 'AU', '2025-12-17 23:20:54'),
(152, 'Fiji', 'FJ', '2025-12-17 23:20:54'),
(153, 'New Zealand', 'NZ', '2025-12-17 23:20:54'),
(154, 'Papua New Guinea', 'PG', '2025-12-17 23:20:54'),
(155, 'Samoa', 'WS', '2025-12-17 23:20:54'),
(156, 'Solomon Islands', 'SB', '2025-12-17 23:20:54'),
(157, 'Tonga', 'TO', '2025-12-17 23:20:54'),
(158, 'Vanuatu', 'VU', '2025-12-17 23:20:54');

-- --------------------------------------------------------

--
-- Table structure for table `dojos`
--

CREATE TABLE `dojos` (
  `dojo_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `dojo_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text NOT NULL,
  `city_id` int(11) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `monthly_price` decimal(8,2) DEFAULT NULL,
  `has_free_trial` tinyint(1) DEFAULT 0,
  `trial_details` varchar(255) DEFAULT NULL,
  `dojo_image` varchar(255) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dojos`
--

INSERT INTO `dojos` (`dojo_id`, `owner_id`, `dojo_name`, `description`, `address`, `city_id`, `phone`, `email`, `website`, `monthly_price`, `has_free_trial`, `trial_details`, `dojo_image`, `is_approved`, `created_at`, `updated_at`) VALUES
(2, 6, 'Ashesi Karate Club', 'The Ashesi Karate Club practices Shotokan Karate, focusing on discipline, self-defense, and physical fitness. The club holds training sessions twice a week, open to members who want to build strength, focus, and martial arts skills in a supportive environment.', '1 University Ave in Berekuso, Ghana', 6, '+233 50 349 3263', 'ashesi@dojo.com', '', NULL, 1, 'All classes are free', 'dojo_6_2_1766026006.jpg', 1, '2025-12-18 01:50:37', '2025-12-18 03:31:06');

-- --------------------------------------------------------

--
-- Table structure for table `dojo_schedule`
--

CREATE TABLE `dojo_schedule` (
  `schedule_id` int(11) NOT NULL,
  `dojo_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `class_type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dojo_schedule`
--

INSERT INTO `dojo_schedule` (`schedule_id`, `dojo_id`, `day_of_week`, `start_time`, `end_time`, `class_type`) VALUES
(4, 2, 'Friday', '16:00:00', '17:00:00', 'Shotokan Kata');

-- --------------------------------------------------------

--
-- Table structure for table `dojo_styles`
--

CREATE TABLE `dojo_styles` (
  `dojo_style_id` int(11) NOT NULL,
  `dojo_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dojo_styles`
--

INSERT INTO `dojo_styles` (`dojo_style_id`, `dojo_id`, `style_id`, `created_at`) VALUES
(4, 2, 1, '2025-12-18 14:13:43');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `dojo_id` int(11) NOT NULL,
  `event_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `location` text DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `current_participants` int(11) DEFAULT 0,
  `event_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `dojo_id`, `event_name`, `description`, `event_date`, `event_time`, `location`, `max_participants`, `current_participants`, `event_image`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'End of semester grading', '....', '2025-12-31', '17:00:00', 'Ashesi Univervity GYM', NULL, 0, NULL, 1, '2025-12-18 20:23:29', '2025-12-18 20:23:29'),
(2, 2, 'Summer karate tournament', 'Organise to challenge students.', '2026-06-01', '21:00:00', 'Ashesi Univervity GYM', 100, 0, NULL, 1, '2025-12-18 20:44:38', '2025-12-18 20:44:38');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`profile_id`, `user_id`, `first_name`, `last_name`, `phone`, `bio`, `profile_image`, `city_id`, `created_at`, `updated_at`) VALUES
(2, 2, 'Test1', 'User', '+233 345 253 36', 'Konitchiwa, this is Test, nice to meet ya :)', 'profile_2_1766061651.jpg', NULL, '2025-12-17 01:48:10', '2025-12-18 12:40:51'),
(5, 6, 'Owner', 'User', '+23380717277', 'I love martial arts specially karate!', 'profile_6_1766009829.jpg', NULL, '2025-12-17 16:23:24', '2025-12-17 22:17:09'),
(8, 9, 'System', 'Administrator', '', 'HI', NULL, NULL, '2025-12-17 23:15:46', '2025-12-18 12:49:49'),
(9, 10, 'Naras', 'Pandas', '', '', 'profile_10_1766071557.jpg', NULL, '2025-12-18 15:25:26', '2025-12-18 15:25:57');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `dojo_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `dojo_id`, `user_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(1, 2, 6, 4, 'Love the energy', '2025-12-18 13:47:01', '2025-12-18 13:47:01');

-- --------------------------------------------------------

--
-- Table structure for table `styles`
--

CREATE TABLE `styles` (
  `style_id` int(11) NOT NULL,
  `style_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `styles`
--

INSERT INTO `styles` (`style_id`, `style_name`, `description`, `created_at`) VALUES
(1, 'Karate', 'Japanese martial art focusing on striking techniques', '2025-12-13 02:37:58'),
(2, 'Judo', 'Japanese martial art emphasizing throws and grappling', '2025-12-13 02:37:58'),
(3, 'Brazilian Jiu-Jitsu', 'Ground fighting and submission grappling', '2025-12-13 02:37:58'),
(4, 'Taekwondo', 'Korean martial art known for high kicks', '2025-12-13 02:37:58'),
(5, 'Muay Thai', 'Thai martial art using punches, kicks, elbows, and knees', '2025-12-13 02:37:58'),
(6, 'Boxing', 'Western combat sport using punches', '2025-12-13 02:37:58'),
(7, 'Kickboxing', 'Stand-up combat sport combining boxing and kicking', '2025-12-13 02:37:58'),
(8, 'Kung Fu', 'Chinese martial arts with diverse styles', '2025-12-13 02:37:58'),
(9, 'Aikido', 'Japanese martial art focusing on harmony and redirecting force', '2025-12-17 23:20:54'),
(10, 'Arnis', 'Filipino stick fighting martial art', '2025-12-17 23:20:54'),
(11, 'Baguazhang', 'Chinese internal martial art with circular movements', '2025-12-17 23:20:54'),
(12, 'Bojutsu', 'Japanese staff fighting art', '2025-12-17 23:20:54'),
(13, 'Bokator', 'Ancient Cambodian martial art with strikes and weapons', '2025-12-17 23:20:54'),
(14, 'Catch Wrestling', 'Submission-based wrestling with joint locks', '2025-12-17 23:20:54'),
(15, 'Combat Sambo', 'Russian military fighting system', '2025-12-17 23:20:54'),
(16, 'Combatives', 'Military hand-to-hand combat system', '2025-12-17 23:20:54'),
(17, 'Dambe', 'West African boxing with wrapped fists', '2025-12-17 23:20:54'),
(18, 'Defendu', 'British close-quarters combat system', '2025-12-17 23:20:54'),
(19, 'Escrima', 'Filipino martial art specializing in stick and blade fighting', '2025-12-17 23:20:54'),
(20, 'Freestyle Wrestling', 'Olympic wrestling allowing holds above and below waist', '2025-12-17 23:20:54'),
(21, 'Greco-Roman Wrestling', 'Wrestling only allowing holds above waist', '2025-12-17 23:20:54'),
(22, 'Hapkido', 'Korean martial art with joint locks and kicks', '2025-12-17 23:20:54'),
(23, 'Hwa Rang Do', 'Korean martial art with comprehensive techniques', '2025-12-17 23:20:54'),
(24, 'Iaido', 'Japanese art of sword drawing and cutting', '2025-12-17 23:20:54'),
(25, 'Jeet Kune Do', 'Bruce Lee martial philosophy emphasizing practicality', '2025-12-17 23:20:54'),
(26, 'K-1', 'Japanese kickboxing with limited clinch work', '2025-12-17 23:20:54'),
(27, 'Kali', 'Filipino martial art focusing on weapons combat', '2025-12-17 23:20:54'),
(28, 'Kenpo', 'Martial art combining Chinese and Japanese techniques', '2025-12-17 23:20:54'),
(29, 'Keysi Fighting Method', 'Spanish self-defense with instinctive movements', '2025-12-17 23:20:54'),
(30, 'Kobudo', 'Okinawan weapons martial art', '2025-12-17 23:20:54'),
(31, 'Kyudo', 'Japanese archery focusing on form and meditation', '2025-12-17 23:20:54'),
(32, 'Luta Livre', 'Brazilian submission wrestling without gi', '2025-12-17 23:20:54'),
(33, 'Mixed Martial Arts', 'Full-contact sport combining striking and grappling', '2025-12-17 23:20:54'),
(34, 'Ninjutsu', 'Japanese martial art of espionage', '2025-12-17 23:20:54'),
(35, 'Nunchaku', 'Martial art using two sticks connected by chain', '2025-12-17 23:20:54'),
(36, 'Pencak Silat', 'Indonesian martial art with strikes and weapons', '2025-12-17 23:20:54'),
(37, 'Sanda', 'Chinese kickboxing combining striking and wrestling', '2025-12-17 23:20:54'),
(38, 'Savate', 'French kickboxing with graceful kicks', '2025-12-17 23:20:54'),
(39, 'Shaolin Kung Fu', 'Ancient Chinese martial art from Shaolin Temple', '2025-12-17 23:20:54'),
(40, 'Shootfighting', 'Hybrid martial art with kickboxing and grappling', '2025-12-17 23:20:54'),
(41, 'Shotokan Karate', 'Traditional Japanese karate with strong stances', '2025-12-17 23:20:54'),
(42, 'Shuai Jiao', 'Chinese wrestling focusing on throws', '2025-12-17 23:20:54'),
(43, 'Sumo', 'Japanese wrestling forcing opponents out of ring', '2025-12-17 23:20:54'),
(44, 'Systema', 'Russian martial art emphasizing natural movement', '2025-12-17 23:20:54'),
(45, 'Tang Soo Do', 'Korean martial art with kicks and traditional forms', '2025-12-17 23:20:54'),
(46, 'Vovinam', 'Vietnamese martial art combining hard and soft techniques', '2025-12-17 23:20:54'),
(47, 'Wrestling', 'Grappling sport with takedowns and pins', '2025-12-17 23:20:54'),
(48, 'Wushu', 'Modern Chinese martial arts with performance elements', '2025-12-17 23:20:54'),
(49, 'Xing Yi Quan', 'Chinese internal martial art with linear movements', '2025-12-17 23:20:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','dojo_owner','admin') DEFAULT 'student',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'test@findmydojo.com', '$2y$10$1bcELcKtiuHzdE8BsyqL6u2XkRl7.VlFrFKaeq1KWqOYte3tczWgC', 'student', 1, '2025-12-17 01:48:10', '2025-12-17 01:48:10'),
(6, 'owner@findmydojo.com', '$2y$10$unMJwVo0aScHcr5jboBnQesCtZLiaSySK2K8HayESDbD5CnVP47va', 'dojo_owner', 1, '2025-12-17 16:23:24', '2025-12-17 16:23:24'),
(9, 'admin@findmydojo.com', '$2y$10$OBHNdsL6HjIDz0jNxLPujOS38TUCPUfqbszXjsAYxNSm.JcIBmNZC', 'admin', 1, '2025-12-17 23:15:46', '2025-12-17 23:15:46'),
(10, 'panda@findmydojo.com', '$2y$10$fzEHacWLgW58qNqc8vQvxOGm1KSKpsxcVtPRXkxfX3DJQnn9tTXz.', 'student', 1, '2025-12-18 15:25:26', '2025-12-18 15:25:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `unique_city_country` (`city_name`,`country_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`),
  ADD UNIQUE KEY `country_name` (`country_name`),
  ADD UNIQUE KEY `country_code` (`country_code`);

--
-- Indexes for table `dojos`
--
ALTER TABLE `dojos`
  ADD PRIMARY KEY (`dojo_id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_city` (`city_id`),
  ADD KEY `idx_approved` (`is_approved`);

--
-- Indexes for table `dojo_schedule`
--
ALTER TABLE `dojo_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `dojo_id` (`dojo_id`);

--
-- Indexes for table `dojo_styles`
--
ALTER TABLE `dojo_styles`
  ADD PRIMARY KEY (`dojo_style_id`),
  ADD UNIQUE KEY `unique_dojo_style` (`dojo_id`,`style_id`),
  ADD KEY `style_id` (`style_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `idx_dojo` (`dojo_id`),
  ADD KEY `idx_date` (`event_date`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `unique_user_dojo_review` (`user_id`,`dojo_id`),
  ADD KEY `idx_dojo` (`dojo_id`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `styles`
--
ALTER TABLE `styles`
  ADD PRIMARY KEY (`style_id`),
  ADD UNIQUE KEY `style_name` (`style_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `dojos`
--
ALTER TABLE `dojos`
  MODIFY `dojo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dojo_schedule`
--
ALTER TABLE `dojo_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dojo_styles`
--
ALTER TABLE `dojo_styles`
  MODIFY `dojo_style_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `styles`
--
ALTER TABLE `styles`
  MODIFY `style_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`country_id`) ON DELETE CASCADE;

--
-- Constraints for table `dojos`
--
ALTER TABLE `dojos`
  ADD CONSTRAINT `dojos_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dojos_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`);

--
-- Constraints for table `dojo_schedule`
--
ALTER TABLE `dojo_schedule`
  ADD CONSTRAINT `dojo_schedule_ibfk_1` FOREIGN KEY (`dojo_id`) REFERENCES `dojos` (`dojo_id`) ON DELETE CASCADE;

--
-- Constraints for table `dojo_styles`
--
ALTER TABLE `dojo_styles`
  ADD CONSTRAINT `dojo_styles_ibfk_1` FOREIGN KEY (`dojo_id`) REFERENCES `dojos` (`dojo_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dojo_styles_ibfk_2` FOREIGN KEY (`style_id`) REFERENCES `styles` (`style_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`dojo_id`) REFERENCES `dojos` (`dojo_id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `profiles_ibfk_2` FOREIGN KEY (`city_id`) REFERENCES `cities` (`city_id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`dojo_id`) REFERENCES `dojos` (`dojo_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
