-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 08:37 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elqaisaracademy`
--

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT 'default.jpg',
  `video_url` varchar(255) DEFAULT NULL,
  `pdf_link` varchar(255) DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `target_grade` int(11) DEFAULT 3,
  `content_type` enum('lesson','revision') DEFAULT 'lesson',
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `quiz_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `title`, `description`, `subject`, `thumbnail`, `video_url`, `pdf_link`, `duration`, `price`, `target_grade`, `content_type`, `is_active`, `created_at`, `quiz_id`) VALUES
(19, 'المنهج في شوال مراجعة التقفيل', 'المنهج كامل في ساعة واحده + pdf ملخص واهم الأسئلة عشانك♥️', 'لغة عربية', '1777165709_69ed658d3e17a.png', 'Ft6QiUAvlMw', 'https://drive.google.com/file/d/1GbABdL5crxTr7WSuHFbFaubA61O5ze-Z/view?usp=drivesdk', '60', '50.00', 3, 'revision', 1, '2026-04-26 00:39:34', 5),
(22, 'النحو في شوال مراجعة التقفيل', 'النحو كامل + pdf المراجعة + اختبار', 'لغة عربية', '1777295278.jpg', 'qaeqSFRTplc', '', '', '30.00', 2, 'revision', 1, '2026-04-27 13:07:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lecture_progress`
--

CREATE TABLE `lecture_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `video_done` tinyint(1) DEFAULT 0,
  `pdf_done` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecture_progress`
--

INSERT INTO `lecture_progress` (`id`, `user_id`, `lecture_id`, `video_done`, `pdf_done`, `updated_at`) VALUES
(1, 1, 3, 1, 0, '2026-04-24 18:20:30'),
(2, 7, 3, 1, 0, '2026-04-24 19:40:20'),
(3, 13, 3, 1, 0, '2026-04-24 19:43:07'),
(5, 21, 3, 1, 0, '2026-04-24 20:04:34'),
(6, 65, 13, 0, 0, '2026-04-26 00:29:35'),
(8, 36, 19, 0, 0, '2026-04-26 12:59:32'),
(10, 55, 19, 1, 1, '2026-04-26 14:02:09'),
(12, 87, 19, 1, 1, '2026-04-26 16:55:23'),
(13, 66, 19, 0, 0, '2026-04-27 04:47:28'),
(21, 104, 19, 0, 1, '2026-04-27 12:39:01'),
(22, 100, 22, 1, 0, '2026-04-27 14:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lecture_id` int(11) DEFAULT NULL,
  `price_paid` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `user_id`, `lecture_id`, `price_paid`, `created_at`) VALUES
(5, 65, 19, '50.00', '2026-04-26 00:43:52'),
(6, 36, 19, '50.00', '2026-04-26 12:56:22'),
(7, 78, 19, '50.00', '2026-04-26 13:31:03'),
(8, 16, 19, '50.00', '2026-04-26 13:59:44'),
(9, 55, 19, '50.00', '2026-04-26 14:01:47'),
(10, 87, 19, '50.00', '2026-04-26 16:52:33'),
(11, 34, 19, '50.00', '2026-04-26 17:33:33'),
(12, 90, 19, '50.00', '2026-04-26 18:30:15'),
(13, 66, 19, '50.00', '2026-04-26 20:25:10'),
(14, 104, 19, '50.00', '2026-04-27 12:38:45'),
(15, 66, 22, '30.00', '2026-04-27 13:37:19'),
(16, 77, 22, '30.00', '2026-04-27 14:44:25'),
(17, 100, 22, '30.00', '2026-04-27 14:46:18');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `quiz_title` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `quiz_title`, `created_at`) VALUES
(5, 'الاختبار الأول', '2026-04-25 20:39:41');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(11, 5, 'توصل نيوتن إلى قانون الجاذبية عن طريق:', 'الصدفة', 'القراءة', 'التأمل والبحث', 'مساعدة الاخرين', 'c'),
(12, 5, 'معنى كلمة \"تؤتي\" في جملة (تؤتي أكلها كل حين):    ', ' تأخذ', 'تعطي', 'تزرع', 'تذبل', 'b'),
(13, 5, 'تساهم المشروعات الصغيرة في مواجهة مشكلة:', 'التلوث البيئي ', 'الزيادة السكانية', 'البطالة', 'توفير عملة صعبة', 'c'),
(14, 5, 'تعتمد المشروعات الصغيرة علي ', 'الصناعات التقليدية ', 'الشباب الصغير', 'كثرة الاموال', 'قلة الأموال', 'a'),
(15, 5, 'تشبه النخلة الابراج البشرية في', 'القوه', 'الحجم', 'الارتفاع', 'الفائدة', 'c'),
(16, 5, 'حبة القمح والتمر عند رؤيتهما في المرة الأولي كانهما', 'داخلهم حياة ', 'حصاتان كالجماد أصم', 'الذهب والياقوت', 'نبات اخصر', 'b'),
(17, 5, 'نادى نوح ابنه لكي:    ', '(أ) يساعده في بناء السفينة.       ', '(ب) يجمع الطعام. ', '(ج) يركب معه وينجو من العذاب.   ', ' (د) يحضر الألواح والمسامير.', 'c'),
(18, 5, 'علاقة \"يحفظك\" بما قبلها في قوله ﷺ (احفظ الله يحفظك):', 'تعليل ', 'نتيجة', 'تفسير', 'تفضيل', 'b'),
(19, 5, 'في نص \"خلال كريمة\"، يرى الشاعر أن العلم بلا أخلاق يؤدي إلى:', 'الغنى السريع.', 'الفشل السريع', 'الشهره الواسعه', 'السعادة الكبيرة', 'b'),
(20, 5, 'مضاد \"تؤوه\" في نص حب الوطن:', 'تضمه', 'تبعده', 'تكلمه', 'تواسيه', 'b'),
(21, 5, 'بماذا شبه الشاعر الذي يخون وطنه', 'بالخائن', 'الحيوان', 'الطير', 'التائه', 'b'),
(22, 5, 'علام يدل أو يحث الحديث الشريف في نص استعن بالله ', 'الخوف من الناس', 'الحذر', 'أهمية تربية النشأ', 'التواكل علي الله', 'c'),
(23, 5, 'اسم الفاعل من الفعل \"استخرج\" هو:', 'مخرِج.', 'مسخرِج.', 'مستخرَج.', 'خارج', 'b'),
(24, 5, '\"المؤمن معطاء الفقراء ماله\"، كلمة (معطاء) صيغة مبالغة على وزن:', 'فعول', 'فعاله', 'مفعال', 'فعال', 'd'),
(25, 5, 'اسم المفعول من الفعل \"باع\" هو:', 'بياع', 'مباع', 'مبيع', 'مبيوع', 'c'),
(26, 5, '\"المساء مأوى الطيور\"، كلمة (مأوى) هنا:', 'اسم مكان ', 'اسم زمان', 'اسم فاعل', 'اسم مفعول ', 'b'),
(27, 5, 'اسم الآلة (ساقية) هو اسم آلة:', 'مشتق قياسي', 'جامد سماعي', 'مشتق جامد', 'غير ذلك ', 'a'),
(28, 5, 'اسم التفضيل من الفعل \"ازدحم\" هو:', 'أزحم', 'أكثر ازدحاماً', 'مزدحم', 'شديد الازدحام', 'b'),
(29, 5, 'كلمة \"مقدام\" هي صيغة مبالغة من الفعل:', 'قدم', 'أقدم', 'تقدم', 'قدوم', 'c'),
(30, 5, 'افعال لا يأتي منها اسم التفضيل', 'الغير قابلة للتفاوت', 'الجامدة', 'الثلاثية', 'الغير ثلاثية', 'b');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `attempts` int(11) DEFAULT 1,
  `last_attempt` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `user_id`, `quiz_id`, `score`, `status`, `attempts`, `last_attempt`, `updated_at`) VALUES
(6, 66, 5, 2, 'failed', 2, NULL, '2026-04-27 15:11:34');

-- --------------------------------------------------------

--
-- Table structure for table `recharge_codes`
--

CREATE TABLE `recharge_codes` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `used_by` int(11) DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `recharge_codes`
--

INSERT INTO `recharge_codes` (`id`, `code`, `amount`, `is_used`, `used_by`, `used_at`, `created_at`, `request_id`) VALUES
(21, 'KAISER-3EA1B5', '10.00', 1, NULL, NULL, '2026-04-23 18:53:08', NULL),
(22, 'KAISER-57CA52', '10.00', 1, NULL, NULL, '2026-04-23 18:54:36', NULL),
(23, 'KAISER-EB9F9D', '9.00', 1, NULL, NULL, '2026-04-24 09:06:21', NULL),
(24, 'KAISER-99FE23', '100.00', 1, NULL, NULL, '2026-04-24 16:32:23', 3),
(25, 'KAISER-2D0F30', '190.00', 1, NULL, NULL, '2026-04-24 18:14:03', 4),
(26, 'KAISER-47106D', '100.00', 1, NULL, NULL, '2026-04-24 19:54:29', NULL),
(27, 'KAISER-88683F', '100.00', 1, NULL, NULL, '2026-04-24 20:13:51', NULL),
(28, 'KAISER-34D361', '200.00', 1, NULL, NULL, '2026-04-24 20:14:14', NULL),
(29, 'KAISER-242F92', '100.00', 1, NULL, NULL, '2026-04-24 20:24:11', NULL),
(30, 'KAISER-7E1450', '100.00', 1, NULL, NULL, '2026-04-24 20:24:20', NULL),
(31, 'KAISER-20B845', '200.00', 1, NULL, NULL, '2026-04-24 21:01:07', NULL),
(32, 'KAISER-83B4B3', '200.00', 1, NULL, NULL, '2026-04-24 21:24:18', NULL),
(33, 'KAISER-4A07F9', '200.00', 1, NULL, NULL, '2026-04-24 21:27:07', NULL),
(34, 'KAISER-E4EE98', '50.00', 1, NULL, NULL, '2026-04-26 00:20:15', 5),
(35, 'KAISER-0F1F9E', '50.00', 1, NULL, NULL, '2026-04-26 13:10:31', NULL),
(36, 'KAISER-06D32F', '100.00', 0, NULL, NULL, '2026-04-26 16:16:59', 8),
(37, 'KAISER-E36547', '100.00', 0, NULL, NULL, '2026-04-26 16:17:31', 7),
(38, 'KAISER-932A11', '100.00', 1, NULL, NULL, '2026-04-26 16:29:56', 6),
(39, 'KAISER-35716B', '120.00', 1, NULL, NULL, '2026-04-26 20:28:50', 9),
(40, 'KAISER-1BC747', '100.00', 1, NULL, NULL, '2026-04-26 21:25:34', 10);

-- --------------------------------------------------------

--
-- Table structure for table `recharge_requests`
--

CREATE TABLE `recharge_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `screenshot` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recharge_requests`
--

INSERT INTO `recharge_requests` (`id`, `user_id`, `amount`, `screenshot`, `status`, `created_at`, `is_read`) VALUES
(1, 1, '100.00', 'PAY_1777017537_8497.png', 'approved', '2026-04-24 07:58:57', 1),
(2, 2, '100.00', 'PAY_1777036110_6420.png', 'approved', '2026-04-24 13:08:30', 1),
(3, 2, '100.00', 'PAY_1777046898_2895.jpeg', 'approved', '2026-04-24 16:08:18', 1),
(4, 5, '190.00', 'PAY_1777054085_5518.jpeg', 'approved', '2026-04-24 18:08:04', 1),
(5, 65, '50.00', 'PAY_1777162220_9309.jpeg', 'approved', '2026-04-26 00:10:20', 1),
(6, 87, '100.00', 'PAY_1777219299_4030.jpeg', 'approved', '2026-04-26 16:01:39', 1),
(7, 87, '100.00', 'PAY_1777219514_5453.png', 'approved', '2026-04-26 16:05:14', 1),
(8, 87, '100.00', 'PAY_1777219855_8639.png', 'approved', '2026-04-26 16:10:55', 1),
(9, 66, '120.00', 'PAY_1777235312_9474.jpeg', 'approved', '2026-04-26 20:28:32', 1),
(10, 96, '100.00', 'PAY_1777238707_5174.jpg', 'approved', '2026-04-26 21:25:06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `balance` decimal(10,2) DEFAULT 0.00,
  `role` enum('student','admin') DEFAULT 'student',
  `student_grade` int(11) DEFAULT 3,
  `avatar_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `phone`, `password`, `balance`, `role`, `student_grade`, `avatar_url`) VALUES
(6, 'رقيه محمد سلامه ', '01029145179', '$2y$10$eYxYUm50ONE4wwOhbZsMFuV.kIKI9ACwMCSuot8D4MIwJ5j4F.CwC', '0.00', 'student', 3, NULL),
(7, 'سامي حمدي خاطر', '01150092993', '$2y$10$RCseAUgR.NPfmDUo.vm66uMIgeZDtply8nHBOhXhkz9oIdKog3s5u', '0.00', 'student', 2, NULL),
(8, 'تسنیم حماده صلاح ', '01556705834', '$2y$10$iWf6MV8hGdTBgJc6SJYiDujg3eWpBvhckXsAt1oL2flcmnIQIxvGe', '0.00', 'student', 2, NULL),
(9, 'نور رجب شيبه', '01126171939', '$2y$10$t7TnsrWf/kUKYKncT0UzQOLGDKgt2eh/pbOCgyWYuCoEhu33ayhbO', '0.00', 'student', 1, NULL),
(10, 'مرام احمد عبده', '01121651870', '$2y$10$Ka7n8eon0hncxzo2.Q.JfOzhNi8utLMZCW91NHhl5LJRL3VdCcNeC', '0.00', 'student', 2, NULL),
(11, 'حنين محمود متولي ', '01027635305', '$2y$10$EpaDVe4CeBs43bRFsbkaC.3gy.R1iSvPd9B2HnCMavFs.IrgmP/JW', '0.00', 'student', 3, NULL),
(12, 'يوسف كمال توفيق احمد توفيق ', '01149274584', '$2y$10$hKsArBF2y3e.d/gGH4eVzuhWxZrevq9.PIOOzTr34QLuDdhXSuTDW', '0.00', 'student', 2, NULL),
(13, 'شهاب احمد علي ', '01115687617', '$2y$10$hZDlVCzNL343xS9lSqOkB.F9C78nzQJtMtByPnNtyN1PEQILPY2rq', '0.00', 'student', 4, NULL),
(14, 'سما عبد الخالق السيد', '01033334701', '$2y$10$9.vMKFXvWKPrVb3QWkBf1u7wUxfgGCsBUByszOHmgsu5HDLQvhaNy', '0.00', 'student', 2, NULL),
(15, ' ندى محمد ', '01010630243', '$2y$10$R/tlHAH/0cH69L9cezT0c.9mzObXT/qL37dFm9YU3mrwh0l.qkpaW', '0.00', 'student', 3, NULL),
(16, 'هايدي محمد علي', '01010411477', '$2y$10$d56z0znHUeVBSnbT/suPrur/wtRNUCAe4ccNu7.TY9gA2kh5vSw8.', '50.00', 'student', 3, NULL),
(17, 'مي فوزي تهامي ', '01089304771', '$2y$10$iKw5JNyzgeh3QU3BFoFqIu8bwY2DZN5xQ9r47/hz7EgEJ14p0.PTK', '0.00', 'student', 2, NULL),
(18, 'كاتي كرم ابراهيم ', '01202393163', '$2y$10$osj.zTe9dm63yo9n50zSTOEzWWjGPetII6zZbkYkCR7UGJfOF28Mq', '0.00', 'student', 3, NULL),
(19, 'مريم محمد ابراهيم ', '01027375779', '$2y$10$yMDqJn7Kr1Q.BmoZ9rGvQe1yU03C7mYa9DgktKrhTgE./OHXKGjdW', '0.00', 'student', 2, NULL),
(20, 'رحمه خليفه ناجي ', '01131159722', '$2y$10$qynSqGeKVNg4JNWK7VUEIOSAh4NY7ndJkybmXhBDALY4fr/Zzn6z.', '0.00', 'student', 2, NULL),
(21, 'محمد محمود جميل', '01033078506', '$2y$10$SN8FgvDONslMJO02MwF1..fNmWMRUg4OJKCxgTds7qZ3RplG91806', '0.00', 'student', 3, NULL),
(22, 'رحمه احمد محمد ', '01001404852', '$2y$10$KsdrED8e4cV9IjpEG1szu.Equ56M5hxdzNR8Be5Ss1e79esVFKPbO', '0.00', 'student', 2, NULL),
(23, 'هدى احمد كامل ', '01154005771', '$2y$10$t3D8uMaEPVS.N2hD2bEDCOWGFCeyvgcmgZvHvKT8w/xujADxRzYpS', '0.00', 'student', 3, NULL),
(24, 'شمس صلاح اسماعيل', '01092585854', '$2y$10$eqtBw5.LIEGpohgDtlsZHusQKJGjP8tgoaFBIKmXTbW7.wK0S14Wm', '0.00', 'student', 2, NULL),
(25, 'أحمد محمد عبد الشافي', '01507760929', '$2y$10$IaIpmTJaUy1RukzmDGigneXxgii9c3SSw3bjUsMbM9zksNIlVTLCW', '0.00', 'student', 3, NULL),
(26, 'دعاء وليد عبد العظيم ', '01156895705', '$2y$10$.LI8WM.vP/YE9yEMFGRje.2ulvud1WcgikSAtT1s.s.oMadmGvnA2', '0.00', 'student', 3, NULL),
(27, 'رقيه محمد محمود ', '01060441394', '$2y$10$RGA97F5UNwkp0rs8aKhzHuQ.kJIPqg9EO7JP9ikTA6WhppteHyobC', '0.00', 'student', 3, NULL),
(28, 'آلاء صالح البدري ', '01066572725', '$2y$10$UdFk1bIOKKG76XPOnn073uMgV6qNkgNxTUv8Bc.tOahkuyLZIKAN.', '0.00', 'student', 2, NULL),
(29, 'دعاء وليد عبد العظيم ', '01123257174', '$2y$10$818mw.r63tSNXQPmaeOULuk9//tlghe72bTZMBJTEgOf61KIcBv5W', '0.00', 'student', 3, NULL),
(30, 'فريدة محمود سيد ', '01004454432', '$2y$10$rZvQw52y5aib7OCKj9BvjOe.mfK5w3zpGOrbvtpnJx.JGSOTm3J6G', '0.00', 'student', 3, NULL),
(31, 'فريدة محمود سيد ', '01282497920', '$2y$10$SKDanr3mIqkGwzYU38WYHe9HO.A.7ygff7P8k8fneQQS4U9KCUZJC', '0.00', 'student', 2, NULL),
(32, 'علياء عماد عويس القرنى ', '01554380609', '$2y$10$0ZX0ap.pIkwwILajVZ9Ep.B5tUavHTPFkz0mGQCD8mkqNcuFnsxAm', '0.00', 'student', 2, NULL),
(33, 'محمد علي محمود ', '01151432042', '$2y$10$vp392E3P/ja.q40rS.ztJOpdxxsIuZcRyp/s/mwUW7FLGRMkMDiRm', '0.00', 'student', 4, NULL),
(34, 'جني سعد محمود', '01288879791', '$2y$10$GEkHOVsscJ.XVlzf2yB0qe8GAkDty2DldTCwhxXSxsK27xQHCH5I.', '150.00', 'student', 3, NULL),
(36, 'ساره محمود جاد الرب', '01109117497', '$2y$10$N.NXkprG7BXp3Bg92yk8mu1zNbFrIJavmvx6uxBmuQmUrSFqqVQ7.', '150.00', 'student', 3, NULL),
(37, 'يوسف رضا محمد صلاح ', '01096579081', '$2y$10$X4Ml4NUzgDyTNYaaTX0IieSNd3Z33PYMT9KbgtjvDl2kcmJY5onBi', '200.00', 'student', 3, NULL),
(38, 'Mohammed Ahmed abdo', '01268495182', '$2y$10$mSW6qI1JfUI3RuTDc4lSKeEheeK.CpC4SmpW8yKj/ARvY.TZPo5CG', '0.00', 'student', 2, NULL),
(39, 'نسرين محمد احمد', '01224380347', '$2y$10$9HbKSzNQkk1aueNFly.phuZ8C.KUuH6KGQkJTxNKzXEIteNese3uu', '0.00', 'student', 2, NULL),
(41, 'شهد احمد محمد ', '01119049832', '$2y$10$77csWyG.B/XKaf1rEVKVGuMx.lCbYlqkVCm8.kD5HA0FYJuH3vsKi', '0.00', 'student', 2, NULL),
(42, 'Basmala Atef Mohammed ', '01062397943', '$2y$10$.EBOI5RL.kt58lFjFz3uVeDAh6Bz8bDrfwFv1mkQ/BYh7dUz94YKe', '0.00', 'student', 2, NULL),
(43, 'منى ليزا', '01067489642', '$2y$10$VF9tiWaGYjlmBKheMkFIxeBmUUbtkfkp2aUBj.1d6/Ashnf9U.FG2', '0.00', 'student', 3, NULL),
(44, 'احمد محمد خميس', '01109136758', '$2y$10$/7M/x.JWDhINek0.fdWSuuE9AmvgOVVGFLvUPis2/SLgAqVtjJJF.', '0.00', 'student', 4, NULL),
(45, 'جنا احمد محمد شبيب ', '01207520738', '$2y$10$KHITRvKTskRFqRXoisfm9etmLbHXHtFJuOqDWU5YvbobDb1up8F5W', '0.00', 'student', 2, NULL),
(46, 'AHMED HAMADA ALI', '01121800826', '$2y$10$Tf99R2kemN0ASJKCDOr69uVeyTI8Cz5ur/gkf0FB7alhCBTUmH21i', '0.00', 'student', 2, NULL),
(47, 'Fatma Ahmed', '01070425316', '$2y$10$ZN9k3qhNETS4V1tlIH7iDuUykjff92wOVzjO6ZK6l4APBb3pHilVa', '0.00', 'student', 2, NULL),
(48, 'هاجر محمود محمود جاد الكريم ', '01026459734', '$2y$10$srLEDgZ6iVKfUnLIBJI3x.J0BIvol0GELq6VjI1Z56gbA7pLuY8D6', '0.00', 'student', 3, NULL),
(49, 'Keroles samuel yacp', '01554715029', '$2y$10$VseQ2d5pwLHlcNsIs/FBje44OLoQfrvcTqcDLEasTzdfs8n00T2RS', '0.00', 'student', 3, NULL),
(50, 'محمد احمد محروس ', '01273236899', '$2y$10$cNRYFMbAnptjZaRbGnRGcOSWhUuwmpnJvmzYUo1c2AE58LEs57eHO', '0.00', 'student', 4, NULL),
(51, 'omarsalem', '01020289677', '$2y$10$pTRPdUixUmysu0HqHD7my.rnGBkPkSjP5lEb6NetJn3JK69zzKuym', '0.00', 'student', 2, NULL),
(52, 'بدري علي بدري ', '01109035204', '$2y$10$xpk8RnxLT77I7r2kb4Lpau4Qo6yXmIjZf7.kz4.ReF9htRv8I6Ema', '0.00', 'student', 3, NULL),
(53, 'جنى صلاح احمد', '01025476601', '$2y$10$Tlin5.QEnlw0T1e1NxNrOe9rG02Ybufxte.JVDrV3XH09jr71yH5.', '0.00', 'student', 2, NULL),
(54, 'Mohamed Amr', '01032720102', '$2y$10$j5miaqa3uyhs1eltVb2dP.3u4JlsrN8HRcbgOrujylgK0Tp9X0TEq', '0.00', 'student', 2, NULL),
(55, 'جنى عبدالرحمن محمد ', '01055737106', '$2y$10$QoHMyqjbHsIqyMqzG5kSrOzmzsXkka2PvyIztU.hv1WkLaWC2eE2y', '150.00', 'student', 3, NULL),
(56, 'اندرو روماني شكري ', '01272815909', '$2y$10$WA/I0Ib7XVnlk4DnPXY6a.2jawaaVOOl89bStnLaA5YihjvBqoc1W', '0.00', 'student', 2, NULL),
(57, 'جنى أحمد جمعه ', '01033765748', '$2y$10$Gv08JfABvvmZxszJjzWIU.yFBlYjfFSPLE0S6Hmd.BvxaAtmcXtj.', '0.00', 'student', 2, NULL),
(58, 'فاطمة احمد محمود ', '01153573347', '$2y$10$1kEyViCetDDNZNorTJRLYeOpJREigSt9rdov8lcANTqJfxWqzeg1K', '0.00', 'student', 2, NULL),
(59, 'Nour Hassan', '01127309520', '$2y$10$aVlvmYtb.nYsZngpXNUQvupDme2ch4dRGlWkemqYj6ZMssUhXU3v2', '0.00', 'student', 3, NULL),
(60, 'ملك ياسر منصور ', '01114546284', '$2y$10$GqykPvP8Q64.AK0QMmijse9ltK6EYDxNMtk7PRktpqtgErdYYALr2', '0.00', 'student', 3, NULL),
(61, 'شهد حسين أحمد ', '01128775960', '$2y$10$1mqHizzit7PzXOX01tmeeuh/ep756tQZ550sO3U/Rye52aM1rZcLu', '0.00', 'student', 4, NULL),
(62, 'حبيبه احمد سليم ', '01145369855', '$2y$10$im6eIVRA8P6lovyi0fmwTOoZP325dm5O.RGi0R6bl/s1MAr4lGYNu', '0.00', 'student', 3, NULL),
(63, 'قاسم هاشم محمود', '01005491986', '$2y$10$SD9PWNf58TcdQsOSegCpMeiq1WMZlNuVL26BxdTxxnvxjBzts3YzW', '0.00', 'student', 2, NULL),
(64, 'حبيبة حسن محمد ', '01508943519', '$2y$10$ejOu7B/sgKuInLAeFievge4oOJgw5DgdMfTGciLkjJ9T9So16esKS', '0.00', 'student', 3, NULL),
(65, 'fc25', '01009737635', '$2y$10$3h7Np4VdMkpK2WIXsrf3t.wtmJmyKowK2rnkMJUjB6LjfHqRC4/XG', '14950.00', 'student', 1, 'https://res.cloudinary.com/deltlycbz/image/upload/v1777250972/students/avatars/z3fpr6lonjtrflebn0rp.jpg'),
(66, 'القيصر أحمد إبراهيم', '01099534259', '$2y$10$ypFiFTvdauD5PZDmzKf4lOPvExk.1agiWtBqaw8lFkSQucFWrkkB.', '9000040.00', 'admin', 6, 'https://res.cloudinary.com/deltlycbz/image/upload/v1777250285/students/avatars/ydzd2wg6gdwwsgkgklms.jpg'),
(67, 'زين عبد المعطي بدري', '01102688139', '$2y$10$PLIblenV6FIyfr8Cyz6xkuUKv9XVehGHV0QQuUxXxbWc3nfw1PSD.', '0.00', 'student', 3, NULL),
(68, 'Ahmed Shaban Ahmed', '01008993210', '$2y$10$p2HIYb4T3UtUSnv/lBWfxe5M/6AjxrlvUWGZP0pN6nDKMYX5OZwAS', '0.00', 'student', 3, NULL),
(69, 'جنى احمد عادل', '01027021046', '$2y$10$pVj9ACJulS9D8LYmpgRNX.C4QvwvwrsfhTJ.Zw0PMTv4FwoD5RCzK', '0.00', 'student', 3, NULL),
(70, 'Ezzeldenosama', '01116864923', '$2y$10$O/oomgYSA6vRlk8OtdfC6uj.9FtMCrIO3YjRPZbpNCqQqmk/CV.am', '100.00', 'student', 2, NULL),
(71, 'ادم حسام محسن', '01154295551', '$2y$10$qS2VO3KLWhTvRCpyIOy/r.hgSDmxulXxNtpS.biDSJPCFFkw72Hjq', '0.00', 'student', 3, NULL),
(72, 'جنا طاهر العزومي', '01097733412', '$2y$10$Jj6r9NI7kb7LfjZ8Qn0xVO71ALq3nsJlXoEpOfLynaDzPEYpI3TEa', '0.00', 'student', 3, NULL),
(73, 'Hamza Mohamed Elbarody', '01281861339', '$2y$10$593IQT4Rt4Q6pQ5IBgg52udU0IKGz2YyTdFMnEbcIA0S7ZYM6cYPq', '0.00', 'student', 2, NULL),
(74, 'آيات ياسر محمد', '01020567635', '$2y$10$NXEJ9vJhf75xO5y5nIyk.OBxvxCp9kCJw88E62lsaZ.6VxTlEWDZ2', '0.00', 'student', 2, NULL),
(75, 'aya mohamed nadi', '01515689560', '$2y$10$s203gFr6BFYC/0isYfZ1/uvuO6lbqV.PIds2E5ys.sCbWXNZmBWza', '0.00', 'student', 2, NULL),
(76, 'كيرلس ملاك وجيه', '01211703011', '$2y$10$SdCAhr26AGLPhOBt2P1Iq.C3KW0aXgnA4Pi5b8f8/6oRV9vTP7Hrq', '0.00', 'student', 4, NULL),
(77, 'حبيبه سامح صلاح', '01287037654', '$2y$10$cm2rjp6Lioj81WgdtqfkFuvnr6DK09XvW06Jyt4iAqqf2V0g2/Mcm', '70.00', 'student', 2, NULL),
(78, 'فائزه اشرف علي', '01225160626', '$2y$10$g4UCxFSI/wMjwK5MKozD9OR3V8yuTnH5xj3jc8qC.L5XVmAnWncae', '0.00', 'student', 3, NULL),
(79, 'محمد مصطفى', '01033394433', '$2y$10$FRjxfjK/chBkjNEsmS1IouniHTU34wqAlz3EJItO02Z4bppe5QmHq', '0.00', 'student', 3, NULL),
(80, 'Ghada Abdelhadi Abbas', '01108918604', '$2y$10$ISucKiVO.ix6eFTJSg1lPe1QjhFD01b0XBLyRpoKyViBLo59Sugem', '0.00', 'student', 4, NULL),
(81, 'ملك محمد ربيع', '01115158948', '$2y$10$PLGUcxLv0Am6f79XA7uCNuOQAoUDjq50uI1gVz/09/xfn/qw44QZy', '0.00', 'student', 3, NULL),
(82, 'Salma gamal', '01111729079', '$2y$10$PcI6r6IT.DBy/ITOJROs4eMKDSE4FQZQxMaua3.oY2HQepqZTPhC2', '0.00', 'student', 4, NULL),
(83, 'عبدالله حسين مساعد', '01033570564', '$2y$10$9.Pn0PfmLSdzY30xJhgV1e1a5VwsYtWArXa.KIZDSLBwlC4NbeAv.', '0.00', 'student', 4, NULL),
(84, 'احمد محمد علي', '01130213933', '$2y$10$Qw0RBWoSwSa2IPNLk7h0z.rN/NaOjNEqKymSW2P0CPU/SMUhwr52m', '0.00', 'student', 4, NULL),
(85, 'ريهام محمد نجيب', '01115829840', '$2y$10$U4yaAIpUyH8Z4rMY7L6WauMA3hIuz97.QScURC0GcL5QhKc9c0Xcq', '0.00', 'student', 2, NULL),
(86, '01555944638', '01555944638', '$2y$10$PKag48yBqCu7cTm3x9LWJe9uuNz0QstmkGOjH4.Us5P.cz6oY3XSm', '0.00', 'student', 3, NULL),
(87, 'رحمة سيد مدبولي السيد احمد', '01101582683', '$2y$10$Tp8AWuyJOrvELjsMToAI0.AI7Pt2..f59WYlJygTq0gaJ0r.xBf5m', '50.00', 'student', 3, NULL),
(88, 'كريم اشرف محمد عبد الهادي', '01207946082', '$2y$10$weh6vp8rdkuBm18uq3SID.0a5gH2EA8xIz.uVyeQwsaAwfHlcL4fS', '0.00', 'student', 3, NULL),
(89, 'reem Mohammed Saeed', '01159185146', '$2y$10$TSmiK37fnMELuNIoTAfiXeOkwfL0XVzHNOyVyMSLzg4IolDIAPKiy', '0.00', 'student', 3, NULL),
(90, 'مُنى احمد عيسى', '01030649724', '$2y$10$fJiQMNZZswRmOD5Q9K4GTua2P.exGLDenOV7nAMyA827X6QM6Ff.y', '50.00', 'student', 3, NULL),
(91, 'امال رضوان محمد', '01120912086', '$2y$10$bmufdWKOaZFc5z9gkff/8.2yJsU4Vi4lGtYsDjPu0pM60H1PLjws.', '0.00', 'student', 3, NULL),
(92, 'محمد ياسر عامر', '01014773278', '$2y$10$ZeqV5ViTLbnnFqj8SpZ0B.54W9k5nGgHoYGMVH.Yz8r636vYFxuBW', '0.00', 'student', 3, NULL),
(93, 'طالب', '01550690854', '$2y$10$bJzcEwm7ThRADeeVYKWiw.fV37lx69t3JyKQ6TjjN8ZZuOzSDxqJO', '0.00', 'student', 3, NULL),
(94, 'طالب', '01551490891', '$2y$10$TOozgpY47blJg2QW3qAAkeef6mtxrYBLqA3vQBkBs54kXLOoGO36S', '0.00', 'student', 3, NULL),
(95, 'جني خاطر عبد الغفار', '01023779291', '$2y$10$G57dWmIsxssvKQIZBsCyRejm9xJPOZwn5jOG1GCzgu0DjECyY66SW', '0.00', 'student', 3, NULL),
(96, 'ملك احمد السيد', '01122886170', '$2y$10$ySSC1Ec5W2zR9kt4TYiOm.5XIAQN.zR7Zis1miESGAGqput4pP3Du', '100.00', 'student', 3, NULL),
(97, 'رودينا محمد محمد', '01025752864', '$2y$10$sBSEhCrtW0fQydgmwiubWOqQPAGrvr/fk8rYMbRLWzLhah7C3Zn1K', '0.00', 'student', 2, NULL),
(99, 'ندي هشام سعد', '01128311566', '$2y$10$PS1x.kOCSoW6ZjYd5klCR.nHCNZnBru8poKOTq1C5VuoqucYhCAD2', '0.00', 'student', 4, NULL),
(100, 'روان كامل كرم', '01044125454', '$2y$10$SkFqXptBAJxcONCfBWKqn.y7upTosKW779lcWjjzi/wpxVE2rl5em', '70.00', 'student', 2, NULL),
(101, 'سلفيا باسم', '01227085578', '$2y$10$QHWzW6/4zxyMMyULPEbeNeRTkClQiSNO8XjA6nCMzleyPzqvzjIVi', '0.00', 'student', 3, NULL),
(102, 'سلمى ياسر منير', '01002291445', '$2y$10$Q5EIqIPgLUJgNOiHcmooju8ZPbkfuXacyEJnHJaY/Kfrd7il6lAMy', '0.00', 'student', 3, NULL),
(103, 'كوثر ابراهيم سيد', '01140418967', '$2y$10$dxNxYipcUa80s1JklpRWXOwh505Ng4sC/sh5wtjXJvlzBmeTXpqje', '0.00', 'student', 6, NULL),
(104, 'ملك الشحات محمد', '01018692180', '$2y$10$PZz12HLUjWPyC.Wqs56RE.FKyCilrTr8iehbJKar1/k0eQV0mmatO', '150.00', 'student', 3, NULL),
(105, 'سما محمود فارس', '01147003399', '$2y$10$I6P/2OJj7qfWL.085THpg.A40Y01QsoHeMxspfvRFzgTEowyRnbLy', '0.00', 'student', 3, NULL),
(106, 'مريم منصور معوض', '01142239307', '$2y$10$9GmKWwU89F96AdM0b0vx6uVuRXIS9OuMvpn4zzdi9OCP3T.hitok2', '0.00', 'student', 3, NULL),
(107, 'رحمه عبدالرحيم نادي', '01001633809', '$2y$10$Qj3lnGxdHkSuRy4rhFgQDOPQ/6WfyOxcUOJjqly.befr9o7ZAqpHG', '0.00', 'student', 3, NULL),
(108, 'مؤمن محمد الصباحي', '01156002940', '$2y$10$.BM1G8OjvkK9y783G8eN1Ow7VFXUekjZBWorvQE6bS7f7qeUCKMq.', '0.00', 'student', 3, NULL),
(109, 'محمد هاني محمد', '01000346031', '$2y$10$BN32ZLeFLMn3icuKKkRnK.p2uvESXD1XvDVXtR88fa5KGsuT3TcAS', '0.00', 'student', 3, NULL),
(110, 'كنوز حمادة السيد ابو هاني', '01274327511', '$2y$10$35pRSo9PnxET.ACloTnWtuCH8WsYWbWPbTHYJkqtlnkIzhvvXU.em', '0.00', 'student', 3, NULL),
(111, 'ريتاج عبدالله', '01030129132', '$2y$10$k18Y/mpC0OTt90RBOKc67OLOCmX8GVGS07F5rsTe8KyXmF23zkpqK', '0.00', 'student', 2, NULL),
(112, 'ايلين محمد احمد', '01009356650', '$2y$10$RYPXILFCy5NhPpS6JvFy..0N9HDg6upN85HMXB4yG7KZ2DxIqkKtO', '0.00', 'student', 2, NULL),
(113, 'احمد سمير احمد', '01040933265', '$2y$10$mqLMtfgagFYXGM1j8KIu0eTK9p9BnX.J/ypDqlOYTVg6QX9EBqI82', '0.00', 'student', 3, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lecture_progress`
--
ALTER TABLE `lecture_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progress` (`user_id`,`lecture_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_quiz` (`user_id`,`quiz_id`);

--
-- Indexes for table `recharge_codes`
--
ALTER TABLE `recharge_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `request_id` (`request_id`),
  ADD KEY `idx_request_id` (`request_id`);

--
-- Indexes for table `recharge_requests`
--
ALTER TABLE `recharge_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lecture_progress`
--
ALTER TABLE `lecture_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `recharge_codes`
--
ALTER TABLE `recharge_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `recharge_requests`
--
ALTER TABLE `recharge_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
