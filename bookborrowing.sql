-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 12:26 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookborrowing`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `bookmarked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`id`, `member_id`, `book_id`, `bookmarked_at`) VALUES
(45, 20220138, NULL, '2024-11-20 04:13:29'),
(49, 20220143, 228, '2024-11-22 20:09:39'),
(61, 20220147, NULL, '2024-12-06 16:56:50'),
(66, 20220148, 293, '2024-12-13 05:56:37'),
(67, 20220149, 292, '2024-12-13 14:38:33'),
(70, 20220151, 338, '2024-12-16 04:11:01'),
(71, 20220182, 293, '2024-12-19 10:56:08'),
(75, 20220140, NULL, '2024-12-20 11:07:32'),
(79, 20220140, NULL, '2024-12-20 15:56:47'),
(84, 20220140, NULL, '2024-12-21 05:55:18'),
(87, 20220189, 319, '2024-12-21 10:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `book_comments`
--

CREATE TABLE `book_comments` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_comments`
--

INSERT INTO `book_comments` (`id`, `book_id`, `member_id`, `rating`, `comment`, `created_at`) VALUES
(5, 336, 20220147, 3, '\"The Night Circus\" by Erin Morgenstern is a spellbinding tale of magic, love, and rivalry that captivates readers from the first page. Set in a mesmerizing, dreamlike circus that only opens at night, the novel weaves an intricate web of enchanting descriptions and intriguing characters. The story follows two young magicians, Celia and Marco, bound in a fierce competition orchestrated by their mentors. Morgenstern’s vivid imagery and poetic prose bring the circus to life, immersing readers in its mysterious ambiance. While the nonlinear timeline may be challenging for some, it adds to the novel’s ethereal quality, making it a memorable read. A must-read for fans of fantasy and romance, The Night Circus invites readers to lose themselves in its world of wonder and imagination.', '2024-12-06 16:53:12'),
(6, 297, 20220140, 5, 'Battle for Your Brain by Nita Farahany is a groundbreaking and thought-provoking exploration of the ethical, legal, and societal implications of neurotechnology. Farahany examines the rapid advancements in brain-computer interfaces and other technologies capable of decoding and influencing human thoughts, raising urgent questions about privacy, autonomy, and the future of free will. With compelling anecdotes and accessible scientific explanations, the book delves into scenarios like workplace surveillance and military applications, painting a vivid picture of the potential benefits and dangers of this technology. Both a warning and a call to action, Battle for Your Brain challenges readers to confront the possibility of a future where mental privacy is no longer guaranteed, making it an essential read for anyone concerned about the intersection of technology and human rights.', '2024-12-06 17:01:53'),
(7, 327, 20220140, 4, '*A Shield of Sorrow* is a poignant and beautifully written tale that explores themes of grief, resilience, and sacrifice. The story follows a protagonist grappling with profound loss while navigating a world filled with both danger and fleeting moments of hope. The author’s evocative prose brings the emotional weight of the narrative to life, immersing readers in a journey that is as heartbreaking as it is inspiring. With richly developed characters and a deeply human story, *A Shield of Sorrow* captures the raw essence of perseverance and the enduring strength of the human spirit, leaving readers reflective long after the final page.', '2024-12-13 18:46:05'),
(8, 292, 20220140, 3, 'Dr. Binay Kumar Singh\'s dedication to empowering seafarers through employment opportunities and guidance is both inspiring and impactful. ', '2024-12-19 15:27:39'),
(9, 313, 20220140, 4, 'The book English Communication Arts & Skill offers a comprehensive guide to mastering the fundamentals of effective communication in English. Its structured approach to developing reading, writing, listening, and speaking skills is well-suited for students, educators, and professionals.', '2024-12-20 04:57:34'),
(10, 310, 20220140, 5, 'Its a good book it tackles everything about the pathologic diseases', '2024-12-20 18:15:38');

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

CREATE TABLE `borrowed_books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `book_type` varchar(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `expiry_days` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`book_id`, `title`, `member_id`, `borrow_date`, `due_date`, `status`, `book_type`, `name`, `expiry_days`) VALUES
(297, 'Battle For Your Brain', 20220188, '2024-12-21', '2024-12-28', 'borrowed', 'ebook', 'joyce', 7),
(312, 'Tech. & Livelihood Education', 20220183, '2024-12-21', '2024-12-18', 'borrowed', 'physical', 'Hford', 5);

-- --------------------------------------------------------

--
-- Table structure for table `condemned_books`
--

CREATE TABLE `condemned_books` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `genre` varchar(255) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `condemned_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `condemn_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `manage_books`
--

CREATE TABLE `manage_books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `publication_date` date DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  `book_type` enum('physical','ebook') NOT NULL DEFAULT 'physical',
  `pdf` varchar(255) DEFAULT NULL,
  `encrypted_pdf` varchar(255) DEFAULT NULL,
  `expiry_days` int(11) DEFAULT 7,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isbn` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manage_books`
--

INSERT INTO `manage_books` (`book_id`, `title`, `author`, `genre`, `publication_date`, `publisher`, `status`, `image`, `description`, `date_added`, `book_type`, `pdf`, `encrypted_pdf`, `expiry_days`, `created_at`, `updated_at`, `isbn`) VALUES
(292, 'From College To Ship', 'Dr Binay Singh', 'Educational', '2022-04-09', 'TWAGAA International', 'available', '6747177095145.png', 'A successful maritime entrepreneur and former seafarer, Dr Binay Kumar Singh has devoted himself to providing employment opportunities within the maritime sector and fostering a better life for everyone who devotes themselves to the sea. From College to Ship presents his take on some of the most daunting challenges seafarers are facing today, explaining how graduating cadets can navigate the job market and settle into rewarding careers.', '2024-11-27 00:00:00', 'ebook', '6747177095149.pdf', NULL, 5, '2024-11-27 12:58:24', '2024-12-21 12:00:31', '9392849478'),
(293, 'The Long Dead', 'John Dean', 'Thriller', '2006-10-06', 'Robert Hale Ltd', 'borrowed', '674718ea8b742.png', 'An unmarked grave, an old promise and a maverick detective... When an archaeological investigation of an old prisoner of war camp turns up several bodies in unmarked graves, it should be an open and shut case for DCI John Blizzard. But, on a foggy November morning, his instinct that something is not quite right is confirmed when forensics show that while most of the victims died during a flu epidemic after the Second World War, one of the deaths was far more recent. Who is this man? Why was he killed? As Blizzard and his team investigate, they begin to uncover a cruel crime, and a promise of revenge that goes back generations. Worse, the killer is at large and now motivated to strike again. Can Blizzard piece together the puzzle and let old wounds finally heal? Set in the fictional port of Hafton in northern England, THE LONG DEAD is a contemporary British whodunnit. If you are a reader who likes having to guess the identity of the murderer, you’ll enjoy picking up on the various clues. With snappy dialogue and convincing characters, this is a page turner that the whole family will appreciate.', '2024-11-27 00:00:00', 'ebook', '674718ea8b746.pdf', NULL, 14, '2024-11-27 13:04:42', '2024-12-16 12:02:47', '9781846178955'),
(295, 'Broken Trust', 'Laura Rise', 'Thriller', '2024-10-22', 'Laura Rise', 'available', '67471a05386a4.png', 'When victims with prosthetic limbs fall prey to an insidious killer, former FBI agent-turned-small town police officer Ivy Pane must take action. Against all odds and her own disability, can she stop the killer before another life is lost?', '2024-11-27 00:00:00', 'ebook', '67471a05386a8.pdf', NULL, 5, '2024-11-27 13:09:25', '2024-12-16 11:48:32', '9781459223813'),
(297, 'Battle For Your Brain', 'Nita A. Farahany', 'Science', '2023-03-14', ' St. Martin\'s Press', 'borrowed', '67471ba26cf63.png', 'A new dawn of brain tracking and hacking is coming. Will you be prepared for what comes next? Imagine a world where your brain can be interrogated to learn your political beliefs, your thoughts can be used as evidence of a crime, and your own feelings can be held against you. A world where people who suffer from epilepsy receive alerts moments before a seizure, and the average person can peer into their own mind to eliminate painful memories or cure addictions. Neuroscience has already made all of this possible today, and neurotechnology will soon become the “universal controller” for all of our interactions with technology. This can benefit humanity immensely, but without safeguards, it can seriously threaten our fundamental human rights to privacy, freedom of thought, and self-determination.', '2024-11-27 00:00:00', 'ebook', '67471ba26cf67.pdf', NULL, 7, '2024-11-27 13:16:18', '2024-12-21 17:50:57', '9781250272966'),
(298, 'Lost And Lassoed', 'Lyla Sage', 'Romance', '2024-11-05', 'Random House Publishing Group', 'available', '67471c27bc8c5.png', 'She thrives in chaos. He prefers routine. The only thing they have in common? How much they hate each other. From the author of Done and Dusted and Swift and Saddled, the highly anticipated next book in the Rebel Blue Ranch series, a small town romance featuring enemies to lovers and forced proximity. Teddy Andersen doesn\'t have a plan. She\'s never needed one before. She\'s always been more of a go with the flow type of girl, but for some reason, the flow doesn\'t seem to be going her way this time. Her favorite vintage suede jacket has a hole in it, her sewing machine is broken, and her best friend just got engaged. Suddenly, everything feels like it\'s starting to change. Teddy\'s used to being a leader, but now she feels like she\'s getting left behind, wondering if the life she lives in the small town she loves is enough for her anymore.', '2024-11-27 00:00:00', 'ebook', '67471c27bc8ca.pdf', NULL, 7, '2024-11-27 13:18:31', '2024-12-21 18:18:01', '9780593732458'),
(300, 'The Sun And The Star', 'Rick Riordan & Mark Oshiro', 'Fantasy', '2023-05-02', 'Penguin Random House Children\'s UK', 'available', '67471d85976be.png', 'Demigods Nico di Angelo and Will Solace must endure the terrors of Tartarus to rescue an old friend in this thrilling adventure co-written by New York Times #1 best-selling author Rick Riordan and award-winning author Mark Oshiro. As the son of Hades, Nico di Angelo has been through so much, from the premature deaths of his mother and sister, to being outed against his will, to losing his friend Jason during the trials of Apollo. But there is a ray of sunshine in his life–literally: his boyfriend, Will Solace, the son of Apollo. Together the two demigods can overcome any obstacle or foe. At least, that’s been the case so far...', '2024-11-27 00:00:00', 'ebook', '67471d85976c3.pdf', NULL, 5, '2024-11-27 13:24:21', '2024-12-16 11:48:32', '1368081150'),
(301, 'A.I For Dummies', 'John Paul Mueller', 'Educational', '2018-03-16', 'Alan Turing', 'available', '67471fb15ad0f.png', 'Step into the future with AI The term \"Artificial Intelligence\" has been around since the 1950s, but a lot has changed since then. Today, AI is referenced in the news, books, movies, and TV shows, and the exact definition is often misinterpreted. Artificial Intelligence For Dummies provides a clear introduction to AI and how it’s being used today. Inside, you’ll get a clear overview of the technology, the common misconceptions surrounding it, and a fascinating look at its applications in everything from self-driving cars and drones to its contributions in the medical field. The world of AI is fascinating— and this hands-on guide makes it more accessible than ever!', '2024-11-27 00:00:00', 'ebook', '67471fb15ad16.pdf', NULL, 14, '2024-11-27 13:33:37', '2024-12-20 00:09:24', '1119796768'),
(307, 'Reading In Philippine History', 'John Lee P. Candelaria', 'History', '1946-11-04', 'C & E Publishing, Inc.', 'borrowed', '674ed652eab48.png', 'Readings in Philippine History aims to equip students with critical thinking and reading skills by applying historical methodologies in the study of Philippine history. This book\'s emphasis on the use of primary sources corresponds to the thrust of the new General Education Curriculum to view the past in the lens of eyewitnesses. This book\'s approach is focused on the analysis of the context, content, and perspective of selected primary sources, through which students of history could be able to gain a better understanding of the past, deepening their sense of identity, and locating themselves in the greater narrative of the nation.', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 09:58:42', '2024-12-16 07:44:15', ' 9789719818694'),
(308, 'Mapeh In Action 8th Edition', ' Solis, Ronald V,  Libiran, Pinky Olivar', 'Physical Education', '2015-06-18', ' Rex Book Store, 2015', 'borrowed', '674ed8c80aacf.png', 'The MAPEH curriculum aims to develop students\' cultural identity and artistic integrity through music, arts, dances, and composition. The curriculum also aims to help students become literate in music, arts, physical education, and health', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:09:12', '2024-12-03 21:01:50', '9789712370847'),
(309, 'Amazing World of Computers', 'Clifford P. Esteban', 'Educational', '2014-10-15', ' Phoenix Publishing House', 'available', '674edc27d84c7.png', 'The Amazing World of Computers is an educational series published by Phoenix Publishing House, designed to teach students about computers, their various components, and the principles of information technology (IT), while also helping them develop a deeper understanding of how technology impacts modern life and society.', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:23:35', '2024-12-19 23:49:19', '9789710629695'),
(310, 'Pathologic Basis of Disease', 'Jon C. Aster', 'Medical Textbook', '2020-06-15', 'Elsevier', 'available', '674edd6e028f4.png', 'Covers the hot topics you need to know about, including novel therapies for hepatitis C, classification of lymphomas, unfolded protein response, non-apoptotic pathways of cell death, coronavirus infections, liquid biopsy for cancer detection, regulation of iron absorption, clonal hematopoiesis and atherosclerosis', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:29:02', '2024-12-14 02:03:18', '9780323531139'),
(311, 'Essential English', 'Carolina T. Gonzales', 'English Literature', '2017-12-05', ' Rex Book Store', 'available', '674ede1c5e717.png', 'Essential English: Worktext in Literature and Language is a worktext that combines reading and language in one resource. It is part of an integrated English series for grades 7–10. The book was written by Carolina T. Gonzales and coordinated by Pilar R. Yu. It was published in 2015 by Rex Book Store in Manila.', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:31:56', '2024-12-21 17:21:36', '9789712370502'),
(312, 'Tech. & Livelihood Education', 'Susana V. Guinea, Ma. Gilmina G.', 'Educational', '2016-05-18', 'Phoenix Publishing House.', 'borrowed', '674edecc4ce22.png', 'Technology and Livelihood Education (TLE) is one of the learning areas of the Secondary Education Curriculum used in Philippine secondary schools. As a subject in high school, its component areas are: Home Economics, Agri-Fishery Arts, Industrial Arts, and Information and Communication Technology.', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:34:52', '2024-12-21 17:16:47', '978-971-9656-19-7'),
(313, 'English Communication Arts & Skill', 'Lapid, Milagros G', 'English Literature', '2016-01-04', ' Phoenix Publishing House, Inc', 'available', '674edf89b4f74.png', ' ECAS promises a framework and discussions that address contemporary and current issues and concerns germane to language, cultures, identities, and texts, with Philippine literature as the framing lens. While it continues to bring in the classics, the vision and perspective of the Series have taken some readjustments, tackling the materials with a certain \"edge\" and purposiveness, and with an essentially 21st century stance, out-look, and projected outcome.\"', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:38:01', '2024-12-14 01:58:14', '9789710646432'),
(314, 'Realistic Mathematics', 'Eolyn G. Gromio', 'Mathematics', '2015-11-19', 'Phoenix Publishing House.', 'available', '674ee0e193d32.png', '\r\nRealistic mathematics learning refers to problems whose situations are related to the real world and allows the incorporation of mathematical concepts, methods, and results in the solving process known as mathematical context problems', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:43:45', '2024-12-19 23:49:22', '978-971-06-6055-1'),
(315, 'Soaring 21st Mathematics', 'Simon L. Chua, ', 'Business Mathematics', '2018-06-13', 'Phoenix Publishing House.', 'borrowed', '674ee1abe86df.png', 'The Textbook provides well-designed lessons boosted with challenging examples and concepts appropriate for high school students. The Teachers Wraparound edition features the connection between the textbook and the supplementary materials namely Learning Guide, Solution Set, and Curriculum Map.', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:47:07', '2024-12-20 00:17:30', '9789710646265'),
(316, 'Exploring Life Through Science', 'John Donnie A. Ramos, Josefina Ma. Ferriols-Pavico', 'Chemistry', '2014-06-25', 'Phoenix Publishing House.', 'available', '674ee233882f3.png', 'It emphasizes on interdisciplinary world-class science, practical technology, environment-centered science instruction, health issues, and the inculcation of Filipino pride. This book brings into the classrooms of young Filipinos the latest and relevant innovations in the sciences.', '2024-12-03 00:00:00', 'physical', NULL, NULL, 5, '2024-12-03 10:49:23', '2024-12-03 18:49:23', '978-971-06-3555-9'),
(317, 'Phyton Essential For Dummies', 'John C. Shovic & Alan Simpson', 'Educational', '2024-03-27', 'Wiley', 'available', '674eedcbe1f94.png', 'Python Essentials For Dummies is a quick reference to all the core concepts in Python, the multifaceted general-purpose language used for everything from building websites to creating apps. This book gets right to the point, with no excess review, wordy explanations, or fluff, making it perfect as a desk reference on the job or as a brush-up as you expand your skills in related areas. Focusing on just the essential topics you need to know to brush up or level up your Python skill, this is the reliable little book you can always turn to for answers.', '2024-12-03 00:00:00', 'ebook', '674eedcbe1f99.pdf', NULL, 7, '2024-12-03 11:38:51', '2024-12-16 11:48:32', '1394263473'),
(318, 'J.S Essential For Dummies', 'Paul McFedries', 'Educational', '2024-03-27', 'Wiley', 'available', '674eee639082e.png', 'JavaScript Essentials For Dummies is your quick reference to all the core concepts about JavaScript—the dynamic scripting language that is often the final step in creating powerful websites. This no-nonsense book gets right to the point, eliminating review material, wordy explanations, and fluff. Find out all you need to know about the foundations of JavaScript, swiftly and crystal clear. Perfect for a brush-up on the basics or as an everyday desk reference on the job, this is the reliable little book you can always turn to for answers.', '2024-12-03 00:00:00', 'ebook', '674eee6390832.pdf', NULL, 5, '2024-12-03 11:41:23', '2024-12-03 19:44:31', '9783943075212'),
(319, 'Energy Saving For Dummies', 'Michael Grosvenor', 'Educational', '2012-02-14', 'Wiley', 'available', '674eeef248fc8.png', 'Use energy more efficiently and help get the planet\'s balance back on an even keelDo you want to make sure your energy usage is sustainable? Energy-Saving Tips For Dummies provides practical methods to reduce your energy consumption in all aspects of your life -- from every room in the home, to at work and your travel choices.Discover how Make simple changes to reduce home energy bills Choose energy-efficient appliances Work at cutting energy use in your workplace Drive more efficiently Explore other transport options', '2024-12-03 00:00:00', 'ebook', '674eeef248fcc.pdf', NULL, 5, '2024-12-03 11:43:46', '2024-12-03 19:43:46', '9780470376027'),
(327, 'A Shield of Sorrow', 'Kate Avery Ellison', 'Fantasy', '2018-04-18', 'Kate Avery Ellison', 'available', '675c938058b58.jpg', 'After a heartbreaking parting, Briand finds herself in the north, and Kael returns to the south... Both are heartbroken. Both are hurt. Both are stubbornly determined to move on. Briand seeks the guardians to find more answers about her powers and slowly builds up a reputation as a vigilante hero among the villagers, while Prince Jehn looks for a new home for the court in exile amid assassination attempts and the fevered arguments of his court. When Kael and the prince find themselves in peril, who will save them?', '2024-12-13 20:05:20', 'ebook', '675c938058b62.pdf', NULL, 5, '2024-12-13 20:05:20', '2024-12-19 23:56:50', '9798351522937'),
(328, 'Never Simple', 'Liz Scheier', 'Memoir', '2022-02-22', 'Henry Holt and Co.', 'available', '675c9576b9662.jpg', 'This gripping and darkly funny memoir “is a testament to the undeniable, indestructible love between a mother and a daughter” (Isaac Mizrahi).\r\n\r\nLiz Scheier’s mother was a news junkie, a hilarious storyteller, a fast-talking charmer you couldn’t look away from, a single mother whose devotion crossed the line into obsession, and―when in the grips of the mental illness that plagued her―a masterful liar. On an otherwise uneventful afternoon when Scheier was eighteen, her mother sauntered into the room and dropped two bombshells. First, that she had been married for most of the previous two decades to a man Liz had never heard of and, second, that the man she had claimed was Liz’s dead father was entirely fictional. She’d made him up―his name, the stories, everything.\r\n\r\nThose big lies were the start, but not the end; it had taken dozens of smaller lies to support them, and by the time she was done she had built a fairy-tale, half-true life for the two of them. Judith Scheier’s charm was more than matched by her eccentricity, and Liz had always known there was something wrong in their home. After all, other mothers didn’t raise a child single-handedly with no visible source of income, or hide their children behind fake Social Security numbers, or host giant parties in a one-bedroom Manhattan apartment only to throw raging tantrums when the door closed behind the guests.\r\n\r\nNow, decades later, armed with clues to her father’s identity―and as her mother’s worsening dementia reveals truths she never intended to share―Liz attempts to uncover the real answers to the mysteries underpinning her childhood. Trying to construct a “normal” life out of decidedly abnormal roots, she navigates her own circuitous path to a bizarre breakup, an unexpected romance, and the birth of her son and daughter. Along the way, Liz wrestles with questions of what we owe our parents even when they fail us, and of how to share her mother’s hilarity, limitless love, and creativity with children―without passing down the trauma of her mental illness. Never Simple is the story of enduring the legacy of a hard-to-love parent with compassion, humor, and, ultimately, self-preservation.', '2024-12-13 20:13:42', 'ebook', '675c9576b9666.pdf', NULL, 5, '2024-12-13 20:13:42', '2024-12-14 04:13:42', '9781250215167'),
(329, 'Olga Dies Dreaming', 'Xochitl Gonzalez', 'Science Fiction', '2022-01-04', 'Berkley', 'available', '675c961a96541.jpg', 'It\'s 2017, and Olga and her brother, Pedro \"Prieto\" Acevedo, are bold-faced names in their hometown of New York. Prieto is a popular congressman representing their gentrifying Latinx neighborhood in Brooklyn while Olga is the tony wedding planner for Manhattan\'s powerbrokers. Despite their alluring public lives, behind closed doors things are far less rosy. Sure, Olga can orchestrate the love stories of the 1%, but she can\'t seem to find her own...until she meets Matteo, who forces her to confront the effects of long-held family secrets... Twenty-seven years ago, their mother, Blanca, a Young Lord-turned-radical, abandoned her children to advance a militant political cause, leaving them to be raised by their grandmother. Now, with the winds of hurricane season, Blanca has come barreling back into their lives. Set against the backdrop of New York City in the months surrounding the most devastating hurricane in Puerto Rico\'s history, Olga Dies Dreaming is a story that examines political corruption, familial strife and the very notion of the American dream--all while asking what it really means to weather a storm.', '2024-12-13 20:16:26', 'ebook', '675c961a96548.pdf', NULL, 5, '2024-12-13 20:16:26', '2024-12-20 00:00:48', '9781250786186'),
(330, 'Sierra Six (Gray Man Series #11)', 'Mark Greaney', 'Thriller', '2022-02-15', 'Berkley', 'available', '675c96a6ce45c.jpg', 'Before he was the Gray Man, Court Gentry was Sierra Six, the junior member of a CIA action team. In their first mission they took out a terrorist leader, at a terrible price. Years have passed. The Gray Man is on a simple mission when he sees a ghost: the long-dead terrorist, but he\'s remarkably energetic for a dead man. A decade of time hasn\'t changed the Gray Man. He isn\'t one to leave a job unfinished or a blood debt unpaid.', '2024-12-13 20:18:46', 'ebook', '675c96a6ce466.pdf', NULL, 5, '2024-12-13 20:18:46', '2024-12-18 23:53:12', '9780593098998'),
(331, 'Heartstopper, Volume 4', 'Alice Oseman', 'Romance', '2021-05-06', 'Hachette Children\'s Group', 'available', '675c973fb7e41.jpg', 'Boy meets boy. Boys become friends. Boys fall in love. The bestselling LGBTQ+ graphic novel about life, love, and everything that happens in between: this is the fourth volume of HEARTSTOPPER, for fans of The Art of Being Normal, Holly Bourne and Love, Simon. Charlie didn\'t think Nick could ever like him back, but now they\'re officially boyfriends. Charlie\'s beginning to feel ready to say those three little words: I love you. Nick\'s been feeling the same, but he\'s got a lot on his mind - not least coming out to his dad, and the fact that Charlie might have an eating disorder. As summer turns to autumn and a new school year begins, Charlie and Nick are about to learn a lot about what love means. Heartstopper is about love, friendship, loyalty and mental illness. It encompasses all the small stories of Nick and Charlie\'s lives that together make up something larger, which speaks to all of us. This is the fourth volume of Heartstopper, which has now been optioned for television by See-Saw Films.', '2024-12-13 20:21:19', 'ebook', '675c973fb7e4a.pdf', NULL, 5, '2024-12-13 20:21:19', '2024-12-18 23:52:40', '9781444952797'),
(332, 'A Court of Mist and Fury', 'Sarah J. Maas', 'Romance', '2016-05-03', 'Bloomsbury USA', 'available', '675c97d591ae7.jpg', 'The seductive and stunning #1 New York Times bestselling sequel to Sarah J. Maas\'s spellbinding A Court of Thorns and Roses . Feyre has undergone more trials than one human woman can carry in her heart. Though she\'s now been granted the powers and lifespan of the High Fae, she is haunted by her time Under the Mountain and the terrible deeds she performed to save the lives of Tamlin and his people. As her marriage to Tamlin approaches, Feyre\'s hollowness and nightmares consume her. She finds herself split into two different one who upholds her bargain with Rhysand, High Lord of the feared Night Court, and one who lives out her life in the Spring Court with Tamlin. While Feyre navigates a dark web of politics, passion, and dazzling power, a greater evil looms. She might just be the key to stopping it, but only if she can harness her harrowing gifts, heal her fractured soul, and decide how she wishes to shape her future-and the future of a world in turmoil. Bestselling author Sarah J. Maas\'s masterful storytelling brings this second book in her dazzling, sexy, action-packed series to new heights.', '2024-12-13 20:23:49', 'ebook', '675c97d591af1.pdf', NULL, 5, '2024-12-13 20:23:49', '2024-12-18 23:52:56', '9781619635197'),
(333, 'Spare', 'Prince Harry, The Duke of Sussex', 'Memoir', '2023-01-10', 'Random House', 'available', '675c987f35170.jpg', 'It was one of the most searing images of the twentieth century: two young boys, two princes, walking behind their mother’s coffin as the world watched in sorrow—and horror. As Princess Diana was laid to rest, billions wondered what Prince William and Prince Harry must be thinking and feeling—and how their lives would play out from that point on. For Harry, this is that story at last. Before losing his mother, twelve-year-old Prince Harry was known as the carefree one, the happy-go-lucky Spare to the more serious Heir. Grief changed everything. He struggled at school, struggled with anger, with loneliness—and, because he blamed the press for his mother’s death, he struggled to accept life in the spotlight. At twenty-one, he joined the British Army. The discipline gave him structure, and two combat tours made him a hero at home. But he soon felt more lost than ever, suffering from post-traumatic stress and prone to crippling panic attacks. Above all, he couldn’t find true love. Then he met Meghan. The world was swept away by the couple’s cinematic romance and rejoiced in their fairy-tale wedding. But from the beginning, Harry and Meghan were preyed upon by the press, subjected to waves of abuse, racism, and lies. Watching his wife suffer, their safety and mental health at risk, Harry saw no other way to prevent the tragedy of history repeating itself but to flee his mother country. Over the centuries, leaving the Royal Family was an act few had dared. The last to try, in fact, had been his mother. . . . For the first time, Prince Harry tells his own story, chronicling his journey with raw, unflinching honesty. A landmark publication, Spare is full of insight, revelation, self-examination, and hard-won wisdom about the eternal power of love over grief.', '2024-12-13 20:26:39', 'ebook', '675c987f35175.pdf', NULL, 5, '2024-12-13 20:26:39', '2024-12-18 23:55:30', '9780593593806'),
(334, 'The Art Thief', 'Michael Finkel', 'True Crime', '2023-06-13', 'Knopf', 'available', '675c990591596.jpg', 'One of the most remarkable true-crime narratives of the twenty-first century: the story of the world’s most prolific art thief, Stéphane Breitwieser.\r\n\r\nIn this spellbinding portrait of obsession and flawed genius, the best-selling author of The Stranger in the Woods brings us into Breitwieser’s strange world—unlike most thieves, he never stole for money, keeping all his treasures in a single room where he could admire them.\r\n\r\nFor centuries, works of art have been stolen in countless ways from all over the world, but no one has been quite as successful at it as the master thief Stéphane Breitwieser. Carrying out more than two hundred heists over nearly eight years—in museums and cathedrals all over Europe—Breitwieser, along with his girlfriend who worked as his lookout, stole more than three hundred objects, until it all fell apart in spectacular fashion.\r\n\r\nIn The Art Thief, Michael Finkel brings us into Breitwieser’s strange and fascinating world. Unlike most thieves, Breitwieser never stole for money. Instead, he displayed all his treasures in a pair of secret rooms where he could admire them to his heart’s content. Possessed of a remarkable athleticism and an innate ability to circumvent practically any security system, Breitwieser managed to pull off a breathtaking number of audacious thefts. Yet these strange talents bred a growing disregard for risk and an addict’s need to score, leading Breitwieser to ignore his girlfriend’s pleas to stop—until one final act of hubris brought everything crashing down.\r\n\r\nThis is a riveting story of art, crime, love, and an insatiable hunger to possess beauty at any cost.', '2024-12-13 20:28:53', 'ebook', '675c99059159e.pdf', NULL, 5, '2024-12-13 20:28:53', '2024-12-14 04:28:53', '9780307598982'),
(335, 'The Body Keeps the Score', 'Bessel van der Kolk', 'Psychology', '2015-09-08', 'Penguin Books', 'available', '675c99b1088b9.jpg', 'Trauma is a fact of life. Veterans and their families deal with the painful aftermath of combat; one in five Americans has been molested; one in four grew up with alcoholics; one in three couples have engaged in physical violence. Dr. Bessel van der Kolk, one of the world’s foremost experts on trauma, has spent over three decades working with survivors. In The Body Keeps the Score, he uses recent scientific advances to show how trauma literally reshapes both body and brain, compromising sufferers’ capacities for pleasure, engagement, self-control, and trust. He explores innovative treatments—from neurofeedback and meditation to sports, drama, and yoga—that offer new paths to recovery by activating the brain’s natural neuroplasticity. Based on Dr. van der Kolk’s own research and that of other leading specialists, The Body Keeps the Score exposes the tremendous power of our relationships both to hurt and to heal—and offers new hope for reclaiming lives.', '2024-12-13 20:31:45', 'ebook', '675c99b1088c1.pdf', NULL, 5, '2024-12-13 20:31:45', '2024-12-18 23:57:02', '9780143127741'),
(336, 'The Night Circus', 'Erin Morgenstern', 'Fantasy, Romance', '2011-09-13', 'Doubleday', 'borrowed', '675c9a82c1bc7.jpg', 'The circus arrives without warning. No announcements precede it. It is simply there, when yesterday it was not. Within the black-and-white striped canvas tents is an utterly unique experience full of breathtaking amazements. It is called Le Cirque des Rêves, and it is only open at night.\r\n\r\nBut behind the scenes, a fierce competition is underway—a duel between two young magicians, Celia and Marco, who have been trained since childhood expressly for this purpose by their mercurial instructors. Unbeknownst to them, this is a game in which only one can be left standing, and the circus is but the stage for a remarkable battle of imagination and will. Despite themselves, however, Celia and Marco tumble headfirst into love—a deep, magical love that makes the lights flicker and the room grow warm whenever they so much as brush hands.\r\n\r\nTrue love or not, the game must play out, and the fates of everyone involved, from the cast of extraordinary circus performers to the patrons, hang in the balance, suspended as precariously as the daring acrobats overhead.\r\n\r\nWritten in rich, seductive prose, this spell-casting novel is a feast for the senses and the heart.', '2024-12-13 20:35:14', 'ebook', '675c9a82c1bd0.pdf', NULL, 5, '2024-12-13 20:35:14', '2024-12-16 12:08:48', '9780385534635'),
(337, 'The Fault in Our Stars', 'John Green', 'Young Adult', '2012-01-10', 'Dutton Books', 'available', '675c9afdc6876.jpg', 'Despite the tumor-shrinking medical miracle that has bought her a few years, Hazel has never been anything but terminal, her final chapter inscribed upon diagnosis. But when a gorgeous plot twist named Augustus Waters suddenly appears at Cancer Kid Support Group, Hazel\'s story is about to be completely rewritten.\r\n\r\nInsightful, bold, irreverent, and raw, The Fault in Our Stars is award-winning author John Green\'s most ambitious and heartbreaking work yet, brilliantly exploring the funny, thrilling, and tragic business of being alive and in love.', '2024-12-13 20:37:17', 'ebook', '675c9afdc687f.pdf', NULL, 5, '2024-12-13 20:37:17', '2024-12-19 23:53:06', '9780525478812'),
(338, 'Gone Girl', 'Gillian Flynn', 'Thriller', '2012-06-05', 'Crown Publishing', 'available', '675c9bac9b84d.jpg', 'Who are you? What have we done to each other? These are the questions Nick Dunne finds himself asking on the morning of his fifth wedding anniversary when his wife Amy suddenly disappears. The police suspect Nick. Amy\'s friends reveal that she was afraid of him, that she kept secrets from him. He swears it isn\'t true. A police examination of his computer shows strange searches. He says they weren\'t made by him. And then there are the persistent calls on his mobile phone. So what did happen to Nick\'s beautiful wife?', '2024-12-13 20:40:12', 'physical', NULL, NULL, 5, '2024-12-13 20:40:12', '2024-12-19 23:57:06', '9780307588371'),
(339, 'A Discovery of Witches', 'Deborah Harkness', 'Fantasy', '2011-02-08', 'Viking Penguin', 'available', '675c9c2202efc.jpg', 'A richly inventive novel about a centuries-old vampire, a spellbound witch, and the mysterious manuscript that draws them together. Deep in the stacks of Oxford\'s Bodleian Library, young scholar Diana Bishop unwittingly calls up a bewitched alchemical manuscript in the course of her research. Descended from an old and distinguished line of witches, Diana wants nothing to do with sorcery; so after a furtive glance and a few notes, she banishes the book to the stacks. But her discovery sets a fantastical underworld stirring, and a horde of daemons, witches, and vampires soon descends upon the library. Diana has stumbled upon a coveted treasure lost for centuries-and she is the only creature who can break its spell. Debut novelist Deborah Harkness has crafted a mesmerizing and addictive read, equal parts history and magic, romance and suspense. Diana is a bold heroine who meets her equal in vampire geneticist Matthew Clairmont, and gradually warms up to him as their alliance deepens into an intimacy that violates age-old taboos. This smart, sophisticated story harks back to the novels of Anne Rice, but it is as contemporary and sensual as the Twilight series-with an extra serving of historical realism.', '2024-12-13 20:42:10', 'physical', NULL, NULL, 5, '2024-12-13 20:42:10', '2024-12-19 23:51:25', '9780670022410'),
(340, 'Teaching Mathematics In The Primary Grades, OBE & PPST-Based', 'Genesis G. Camarista, Ian B. Oranio', 'Mathematics', '2017-04-16', 'Lorimar Publishing', 'available', '675c9e50bbbdb.jpg', 'A comprehensive guide designed to strengthen elementary education teachers\' grasp of mathematical concepts and teaching strategies.', '2024-12-13 20:51:28', 'physical', NULL, NULL, 5, '2024-12-13 20:51:28', '2024-12-14 04:51:28', '9789715427249'),
(341, 'Introduction to Philosophy of Education', 'Purita P. Bilbao', 'Philosophy', '2014-04-08', 'Lorimar Publishing', 'available', '675c9fe5d358c.jpg', 'This book explores the philosophical foundations of education, examining how philosophical ideas shape educational systems and practices.', '2024-12-13 20:58:13', 'physical', NULL, NULL, 5, '2024-12-13 20:58:13', '2024-12-14 04:58:13', '9789715425436'),
(342, 'Understanding the Child and Adolescent', 'Maria Rita D. Lucas', 'Child Development', '2019-06-17', 'Lorimar Publishing', 'available', '675ca05d8161e.jpg', 'This book provides insights into the developmental stages of children and adolescents, exploring physical, emotional, and cognitive growth.', '2024-12-13 21:00:13', 'physical', NULL, NULL, 5, '2024-12-13 21:00:13', '2024-12-14 05:00:13', '9789715428772'),
(343, 'The Goldfinch', 'Donna Tartt', 'Thriller', '2013-10-22', 'Little, Brown and Company', 'available', '675ca1901dabf.jpg', 'Theo Decker, a 13-year-old New Yorker, miraculously survives an accident that kills his mother. Abandoned by his father, Theo is taken in by the family of a wealthy friend. Bewildered by his strange new home on Park Avenue, disturbed by schoolmates who don\'t know how to talk to him, and tormented above all by a longing for his mother, he clings to the one thing that reminds him of her: a small, mysteriously captivating painting that ultimately draws Theo into a wealthy and insular art community.\r\n\r\nAs an adult, Theo moves silkily between the drawing rooms of the rich and the dusty labyrinth of an antiques store where he works. He is alienated and in love — and at the center of a narrowing, ever more dangerous circle.\r\n\r\nThe Goldfinch is a mesmerizing, stay-up-all-night and tell-all-your-friends triumph, an old-fashioned story of loss and obsession, survival and self-invention. From the streets of New York to the dark corners of the art underworld, this \"soaring masterpiece\" examines the devastating impact of grief and the ruthless machinations of fate (Ron Charles, Washington Post).', '2024-12-13 21:05:20', 'physical', NULL, NULL, 5, '2024-12-13 21:05:20', '2024-12-21 11:52:24', '9780316055437'),
(345, 'I\'m Glad My Mom Died', 'Jennete McCurdy', 'Memoir', '2022-08-08', 'Simon C Schuster', 'available', '675ca2bfe2adf.jpg', 'A former child actress and star of Carly and Sam C Cat, about her difficult relationship with her mother and her struggles with eating disorders and addiction.', '2024-12-13 21:10:23', 'ebook', '675ca2bfe2ae9.pdf', NULL, 5, '2024-12-13 21:10:23', '2024-12-19 23:58:07', '978-1-9821-8582-4'),
(346, 'Six of Crows', 'Leigh Bardugo', 'Fantasy', '2015-10-06', 'Henry Hold and Company', 'available', '675ca5835de88.jpg', 'Ketterdam: a bustling hub of international trade where anything can be had for the right price—and no one knows that better than criminal prodigy Kaz Brekker. Kaz is offered a chance at a deadly heist that could make him rich beyond his wildest dreams\r\n', '2024-12-13 21:22:11', 'ebook', NULL, NULL, 5, '2024-12-13 21:22:11', '2024-12-14 05:37:54', '978-1627795098'),
(347, 'Beach Read', 'Emily Henry', 'Romance', '2020-05-19', 'Penguin', 'available', '675ca767c0de9.jpg', 'A romance writer who no longer believes in love and a literary writer stuck in a rut engage in a summer-long challenge that may just upend everything they believe about happily ever after.', '2024-12-13 21:30:15', 'ebook', '675ca767c0df3.pdf', NULL, 5, '2024-12-13 21:30:15', '2024-12-14 05:30:15', '9781984806734'),
(348, 'The Outsider', 'Stephen King', 'Horror', '2018-05-22', 'Scribner', 'available', '675caa40060a5.jpg', 'The Outsiders is a novel about Ponyboy Curtis, a fourteen-year-old who lives with his two brothers, Darry and\r\n\r\nSoda. It focuses on the class conflict between the Greasers and the Socs. Ponyboy is part of the Greasers, known for their greasy hair, fighting, and shoplifting.', '2024-12-13 21:42:24', 'ebook', '675caa40060af.pdf', NULL, 5, '2024-12-13 21:42:24', '2024-12-14 05:42:24', '9781501180996'),
(350, 'Normal People', 'Sally Rooney', 'Science Fiction', '2018-08-28', 'Faber C Faber', 'available', '675cab0abc6ff.jpg', 'Normal People is a 2018 novel by Irish writer Sally Rooney that explores the complex relationship between two teenagers from different social classes.', '2024-12-13 21:45:46', 'ebook', '675cab0abc70a.pdf', NULL, 7, '2024-12-13 21:45:46', '2024-12-20 00:11:32', '9781984822178'),
(351, 'The Help', 'Kathryn Stockett', 'Historical Fiction', '2009-02-10', 'Amy Einhorn Books', 'available', '675cb447555c9.jpg', 'Twenty-two-year-old Skeeter has just returned home after graduating from Ole Miss. She may have a degree, but it is 1962, Mississippi, and her mother will not be happy till Skeeter has a ring on her finger. Skeeter would normally find solace with her beloved maid Constantine, the woman who raised her, but Constantine has disappeared and no one will tell Skeeter where she has gone.\r\n\r\nAibileen is a black maid, a wise, regal woman raising her seventeenth white child. Something has shifted inside her after the loss of her own son, who died while his bosses looked the other way. She is devoted to the little girl she looks after, though she knows both their hearts may be broken.\r\n\r\nMinny, Aibileen\'s best friend, is short, fat, and perhaps the sassiest woman in Mississippi. She can cook like nobody\'s business, but she can\'t mind her tongue, so she\'s lost yet another job. Minny finally finds a position working for someone too new to town to know her reputation. But her new boss has secrets of her own.\r\n\r\nSeemingly as different from one another as can be, these women will nonetheless come together for a clandestine project that will put them all at risk. And why? Because they are suffocating within the lines that define their town and their times. And sometimes lines are made to be crossed.\r\n\r\nIn pitch-perfect voices, Kathryn Stockett creates three extraordinary women whose determination to start a movement of their own forever changes a town, and the way women, mothers, daughters, caregivers, friends, view one another. A deeply moving novel filled with poignancy, humor, and hope, The Help is a timeless and universal story about the lines we abide by, and the ones we don\'t.', '2024-12-13 22:25:11', 'physical', NULL, NULL, 5, '2024-12-13 22:25:11', '2024-12-14 06:25:11', '9780399155345'),
(352, 'Cinder (The Lunar Chronicles #1)', 'Marissa Meyer', 'Science Fiction', '2012-01-03', 'Feiwel & Friends', 'available', '675cb52622fd6.jpg', 'Humans and androids crowd the raucous streets of New Beijing. A deadly plague ravages the population. From space, a ruthless Lunar people watch, waiting to make their move. No one knows that Earth’s fate hinges on one girl. . . . Cinder, a gifted mechanic, is a cyborg.\r\n\r\nShe’s a second-class citizen with a mysterious past, reviled by her stepmother and blamed for her stepsister’s illness. But when her life becomes intertwined with the handsome Prince Kai’s, she suddenly finds herself at the center of an intergalactic struggle, and a forbidden attraction. Caught between duty and freedom, loyalty and betrayal, she must uncover secrets about her past in order to protect her world’s future.', '2024-12-13 22:28:54', 'physical', NULL, NULL, 5, '2024-12-13 22:28:54', '2024-12-19 23:57:26', '9780312641894'),
(353, 'Blitzscalling', 'Reid Hoffman', 'Entrepreneurship', '2018-10-09', 'HarperCollins Publishers', 'available', '675cc973705b0.jpg', 'It is a book by Reid Hoffman and Chris Yeh that describes a strategy for growing a company quickly and gaining a competitive advantage.\r\nThe secret is a set of techniques for scaling up at a dizzying pace that blows competitors out of the water. The objective of Blitz scaling is not to go from zero to one, but from one to one billion as quickly as possible.', '2024-12-13 23:55:31', 'ebook', '675cc973705b5.pdf', NULL, 5, '2024-12-13 23:55:31', '2024-12-14 07:55:31', '9780008303631'),
(354, 'The Cruel Prince', 'Holly Black', 'Young Adult', '2018-01-02', 'Little, Brown Books for Young Readers', 'available', '675ccba889af6.jpg', 'It is about a human girl who is taken to live in the world of Faerie after her parents are murdered.', '2024-12-14 00:04:56', 'ebook', '675ccba889afc.pdf', NULL, 5, '2024-12-14 00:04:56', '2024-12-14 08:04:56', '9780316310277'),
(355, 'How to Sell A Haunted House', 'Grady Hendrix', 'Horror', '2023-01-17', 'Berkley Books', 'available', '675ccddf37394.jpg', 'It is about estranged siblings Louise and Mark who return to their family home to sell it after their parents die.\r\n\r\nGrady Hendrix takes on the haunted house in a thrilling new novel that explores the way your past—and your family—can haunt you like nothing else', '2024-12-14 00:14:23', 'ebook', '675ccddf3739c.pdf', NULL, 5, '2024-12-14 00:14:23', '2024-12-14 08:14:23', '9780593201268'),
(356, 'Sleeping in the Sun', 'Joan Howard', 'Fantasy', '2024-10-22', 'She Writes Press', 'available', '675ccfc4894b8.jpg', 'When two visitors arrive to the boarding house in India where an American boy is coming of age during the British Raj, truths unravel, disrupting his life and challenging the family’s sense of home. A unique historical angle ideal for fans of The Poisonwood Bible and The Inheritance of Loss. \r\n \r\nIn the last years of the British Raj, an American missionary family stays on in Midnapore, India. Though the Hintons enjoy white privileges, they have never been accepted by British society and instead run a boarding house on the outskirts of town where wayward native Indians come to find relief. \r\n', '2024-12-14 00:22:28', 'ebook', NULL, NULL, 7, '2024-12-14 00:22:28', '2024-12-20 00:21:29', '9781647427993'),
(357, 'Get a Life, Chloe Brown', 'Talia Hibbert', 'Romance', '2019-11-05', 'Avon Romance', 'available', '675ccfc9dc0a0.jpg', 'Chloe Brown is a chronically ill computer geek with a goal, a plan, and a list. After almost—but not quite—dying, she\'s come up with seven directives to help her “Get a Life”, and she\'s already completed the first: finally moving out of her glamorous family\'s mansion. The next items? Enjoy a drunken night out.\r\n', '2024-12-14 00:22:33', 'ebook', '675ccfc9dc0aa.pdf', NULL, 5, '2024-12-14 00:22:33', '2024-12-14 08:22:33', '9780062941206 '),
(358, 'A Phoenix First Must Burn', 'Patrice Caldwell ', 'Science Fiction', '2020-03-10', 'Viking Books for Young Readers', 'available', '675cd0be59df8.jpg', 'A Phoenix First Must Burn will take you on a journey from folktales retold to futuristic societies and everything in between.\r\nFilled with stories of love and betrayal, strength and resistance, this collection contains an array of complex and true-to-life characters in which you cannot help but see yourself reflected.', '2024-12-14 00:26:38', 'ebook', '675cd0be59e01.pdf', NULL, 5, '2024-12-14 00:26:38', '2024-12-14 08:26:38', '9781984835673'),
(360, 'Where the dead brides gather', 'Nuzo Ono', 'Horror', '2024-10-22', 'Titan Books', 'available', '675cd107f36f6.jpg', 'A powerful Nigeria-set tale of possession, malevolent ghosts, family tensions, secrets and murder from the recipient of the Bram Stoker Award for Lifetime Achievement and \'Queen of African Horror\'. \r\n \r\nBata, an eleven-year-old girl tormented by nightmares, wakes up one night to find herself standing sentinel before her cousin\'s door. Her skin, hair, and eyes have turned a dazzling white colour, which even the medicine-man can\'t heal. Her cousin is to get married the next morning, but only if she can escape the murderous attack of a ghost-bride, who used to be engaged to her groom. \r\n \r\nThrough the night, Bata battles the vengeful ghost and finally vanquishes it before collapsing. On awakening, she has no recollection of the events. And when the medicine-man tries to exorcise the entities clinging to her body as a result of her supernatural possession, Bata dies on the exorcism mat. There begins her journey. She is taken into Ibaja-La, the realm of dead brides, by Mmuọ-Ka-Mmuọ, the ghost-collector of the spirit realm. There she meets the ghosts of brides from every culture who died tragically before their weddings; both the kind and the malevolent. Bata is given secret powers to fight the evil ghost-brides before being sent back to the human realm, where she must learn to harness her new abilities as she strives to protect those whom she loves. \r\n\r\n ', '2024-12-14 00:27:51', 'ebook', NULL, NULL, 5, '2024-12-14 00:27:51', '2024-12-14 08:27:51', '9781835420622'),
(361, 'Percy Jackson The Staff of Serapis', 'Rick Riordan', 'Fantasy', '2023-06-09', 'Disney Book Group', 'available', '675cd2fc1c124.jpg', 'In this adventure, Annabeth encounters more oddities in the subway than usual, including a two-headed monster and a younger blond girl who reminds her a little of herself. This is the story fans have asked for, in which Annabeth Chase teams up with Sadie Kane. The demigod daughter of Athena and the young magician from Brooklyn House take on a larger-than-life foe from the ancient world. Perhaps even more disturbing than the power-hungry god they encounter is the revelation that he is being controlled by someone—someone all too familiar to Sadie.\r\n', '2024-12-14 00:36:12', 'ebook', '675cd2fc1c131.pdf', NULL, 5, '2024-12-14 00:36:12', '2024-12-14 08:36:12', '9788580576351'),
(362, 'The Republic of Salt', 'Ariel Kaplan', 'Fantasy', '2024-02-10', 'Erewhon Books', 'available', '675d78330f2dd.jpg', ': In this riveting sequel to The Pomegranate Gate, Toba, Naftaly, and their allies must defend a city under siege—while the desperate deals they’ve made begin to unravel around them. \r\n \r\nAfter a near-disastrous confrontation with La Caceria, Toba and Asmel are trapped on the human side of the gate, pursued by the Courser and a possessed Inquisitor. In the Mazik world, Naftaly’s visions are getting worse, predicting the prosperous gate city of Zayit in flames and overrun by La Caceria. Zayit is notorious for its trade in salt, a substance toxic to the near-immortal Maziks; if the Cacador can control the salt, he will be nearly unstoppable. But the stolen killstone, the key to the Cacador’s destruction, could eliminate the threat—if only Barsilay could find and use it. \r\n \r\nDeadly allies and even more dangerous bargains might be the only path to resist La Caceria’s ruthless conquest of both the mortal world and the Maziks’, but the cost is steep and the threat is near. A twisty, clever entry in The Mirror Realm Cycle, The Republic of Salt asks what personal morals weigh in the face of widespread danger and how best to care for one another.', '2024-12-14 12:21:07', 'ebook', NULL, NULL, 7, '2024-12-14 12:21:07', '2024-12-20 00:02:19', '9781837861309'),
(363, 'The Sworn Sword', 'George R. R. Martin', 'Fantasy', '2003-01-01', 'Del Rey/PenguinRandomHouse', 'available', '675d78e1952bc.jpg', 'he Sworn Sword is a novella by George R. R. Martin that first appeared in the Legends II anthology series. This is the second in the series of Dunk and Egg stories. ', '2024-12-14 12:24:01', 'ebook', '675d78e1952c3.pdf', NULL, 5, '2024-12-14 12:24:01', '2024-12-20 00:04:53', '9782818708309'),
(364, 'Broken Trust', 'Laura Rise', 'Thriller', '2024-10-24', 'Laura Rise', 'available', '675d79fdab17b.jpg', 'When victims with prosthetic limbs fall prey to an insidious killer, former FBI agent-turned-small town police officer Ivy Pane must take action. Against all odds and her own disability, can she stop the killer before another life is lost?', '2024-12-14 12:28:45', 'ebook', '675d79fdab182.pdf', NULL, 5, '2024-12-14 12:28:45', '2024-12-14 20:28:45', '9781094332949'),
(365, 'Black Enough', 'Ibi Zoboi', 'Anthology', '2019-01-08', 'HarperCollins', 'available', '675e90a074cc0.jpg', ' With more than a dozen short stories about being young and black in America, “Black Enough” will make other young black people feel represented by showcasing the beauty and uniqueness of the different members of the black community.', '2024-12-15 08:17:36', 'ebook', '675e90a074cca.pdf', NULL, 7, '2024-12-15 08:17:36', '2024-12-15 16:17:36', '9780062698735'),
(366, 'Fresh Ink', 'Lamar Giles', 'Anthology', '2018-08-14', 'Crown Books for Young Readers', 'available', '675ed697ad775.jpg', 'It\'s a story of a few dead young black men, tagging walls in the afterlife as they discuss their memorials, how they died, and how they keep their own memories alive with their tags.', '2024-12-15 13:16:07', 'ebook', '675ed697ad77d.pdf', NULL, 7, '2024-12-15 13:16:07', '2024-12-15 21:16:07', '‎978-1524766283');
INSERT INTO `manage_books` (`book_id`, `title`, `author`, `genre`, `publication_date`, `publisher`, `status`, `image`, `description`, `date_added`, `book_type`, `pdf`, `encrypted_pdf`, `expiry_days`, `created_at`, `updated_at`, `isbn`) VALUES
(367, 'The Midnight Library', 'Matt Haig', 'Fantasy', '2020-08-13', 'Canongate Books', 'available', '675ee22e792b6.jpg', 'The Midnight Library is a 2020 work of fiction by the New York Times bestselling British author Matt Haig. In this novel, Haig addresses weighty topics through a main character, Nora Seed, who attempts suicide and must explore what it means to live while in the gray area between life and death.', '2024-12-15 14:05:34', 'ebook', '675ee22e792bc.pdf', NULL, 7, '2024-12-15 14:05:34', '2024-12-15 22:05:34', '978-1786892720'),
(368, 'A Thousand Beginnings and Endings', 'Ellen Oh', 'Anthology', '2018-06-26', 'Greenwillow Books', 'available', '675ee4a83fa59.jpg', 'A Thousand Beginnings and Endings is an anthology that is filled with rich and fascinating retellings of East and South Asian mythology + folklore! The stories within this anthology cover and explore a number of themes all while allowing each author to bring a unique spin to their favorite folktales C stories!', '2024-12-15 14:16:08', 'ebook', '675ee4a83fa61.pdf', NULL, 7, '2024-12-15 14:16:08', '2024-12-15 22:16:08', '9780062671165');

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `message_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`message_id`, `name`, `email`, `message`, `sent_at`) VALUES
(1, 'jobert', 'ding@gmail.com', 'okay po', '2024-12-03 11:33:16'),
(2, 'hah', 'asda@gmail.com', 'hadf', '2024-12-03 11:37:28'),
(3, 'hah', 'derrth@gmail.com', 'check123', '2024-12-03 11:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `time` datetime NOT NULL DEFAULT current_timestamp(),
  `member_id` int(11) NOT NULL,
  `is_unread` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `time`, `member_id`, `is_unread`) VALUES
(17, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 18:41:42', 20220140, 0),
(18, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-25 18:48:20', 20220140, 0),
(19, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 18:52:14', 20220140, 0),
(20, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-25 18:52:28', 20220140, 0),
(21, 'Book returned: \"Harry Potter\" by 20220140', '2024-11-25 18:53:15', 20220140, 0),
(22, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-25 18:53:19', 20220140, 0),
(23, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 18:54:45', 20220140, 0),
(24, 'Book returned: \"FERMAT\'S LAST THEOREM\" by 20220140', '2024-11-25 18:55:19', 20220140, 0),
(25, 'Book returned: \"Harry Potter\" by 20220140', '2024-11-25 18:55:21', 20220140, 0),
(26, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-25 18:55:43', 20220140, 0),
(28, 'Book returned: \"FERMAT\'S LAST THEOREM\" by 20220140', '2024-11-25 19:00:37', 20220140, 0),
(29, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-25 19:33:17', 20220140, 0),
(30, 'Ochea request to borrow \"BOOK BORROWING\"', '2024-11-25 19:34:26', 20220140, 0),
(31, 'Book returned: \"BOOK BORROWING\" by 20220140', '2024-11-25 19:45:14', 20220140, 0),
(35, 'Ochea request to borrow \"BOOK BORROWING\"', '2024-11-25 20:06:41', 20220140, 0),
(36, 'admin request to borrow \"Harry Potter\"', '2024-11-25 20:36:55', 20220123, 0),
(37, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:32:04', 20220140, 0),
(38, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:36:00', 20220140, 0),
(39, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:42:03', 20220140, 0),
(40, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:44:39', 20220140, 0),
(41, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:46:37', 20220140, 0),
(42, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:49:23', 20220140, 0),
(43, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-25 23:51:42', 20220140, 0),
(44, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:52:23', 20220140, 0),
(45, 'Ochea request to borrow \"Harry Potter\"', '2024-11-25 23:53:06', 20220140, 0),
(46, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-26 00:17:46', 20220140, 0),
(47, 'Ochea request to borrow \"FERMAT\'S LAST THEOREM\"', '2024-11-26 01:43:14', 20220140, 0),
(50, 'Ochea request to borrow \"The Code Book\"', '2024-11-26 10:48:59', 20220140, 0),
(51, 'Ochea request to borrow \"The Hobbit\"', '2024-11-26 10:49:54', 20220140, 0),
(52, 'Ochea request to borrow \"Harry Potter\"', '2024-11-26 10:50:25', 20220140, 0),
(53, 'Book returned: \"Harry Potter\" by 20220140', '2024-11-26 10:52:02', 20220140, 0),
(57, 'Book returned: \"fafafa\" by 20220140', '2024-11-27 03:56:09', 20220140, 0),
(58, 'Book returned: \"asdf\" by 20220140', '2024-11-27 03:56:14', 20220140, 0),
(59, 'Ochea request to borrow \"fafafa\"', '2024-11-27 03:59:18', 20220140, 0),
(60, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 11:58:04', 20220140, 0),
(61, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 12:03:29', 20220140, 0),
(62, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 12:10:39', 20220140, 0),
(63, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 15:17:46', 20220140, 0),
(64, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 15:21:52', 20220140, 0),
(65, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 15:41:02', 20220140, 0),
(66, 'Michael James request to borrow \"The Long Dead\"', '2024-11-27 15:43:27', 20220140, 0),
(67, 'Michael James request to borrow \"afafa\"', '2024-11-27 16:18:54', 20220140, 0),
(68, 'Michael James request to borrow \"The Long Dead\"', '2024-11-27 16:44:37', 20220140, 0),
(69, 'Michael James request to borrow \"From College To Ship\"', '2024-11-27 16:45:12', 20220140, 0),
(70, 'Michael James request to borrow \"The Long Dead\"', '2024-11-27 17:29:34', 20220140, 0),
(71, 'Michael James request to borrow \"Broken Trust\"', '2024-11-27 22:50:51', 20220140, 0),
(72, 'Michael James request to borrow \"Broken Trust\"', '2024-11-27 22:53:48', 20220140, 0),
(73, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-11-27 22:56:11', 20220140, 0),
(74, 'Michael James request to borrow \"The Long Dead\"', '2024-11-29 03:42:04', 20220140, 0),
(75, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 20:05:11', 20220140, 0),
(76, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 20:05:28', 20220140, 0),
(77, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 20:14:39', 20220140, 0),
(78, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 20:15:00', 20220140, 0),
(79, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:16:45', 20220140, 0),
(80, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:16:45', 20220140, 0),
(81, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:16:45', 20220140, 0),
(82, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:16:45', 20220140, 0),
(83, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:18:05', 20220140, 0),
(84, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:18:05', 20220140, 0),
(85, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:18:05', 20220140, 0),
(86, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:18:05', 20220140, 0),
(87, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-02 21:19:15', 20220140, 0),
(88, 'Michael James request to borrow \"Where The Dead Brides Gather\"', '2024-12-02 21:19:53', 20220140, 0),
(90, 'Michael James request to borrow \"Mapeh In Action 8th Edition\"', '2024-12-03 14:01:42', 20220140, 0),
(91, 'Michael James request to borrow \"The Sun And The Star\"', '2024-12-04 19:06:59', 20220140, 0),
(92, 'Michael James request to borrow \"Broken Trust\"', '2024-12-07 00:44:27', 20220140, 0),
(93, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-07 00:45:18', 20220140, 0),
(94, 'Michael James request to borrow \"From College To Ship\"', '2024-12-08 07:10:50', 20220140, 0),
(95, 'Jinxmoke request to borrow \"From College To Ship\"', '2024-12-08 07:15:22', 20220147, 0),
(96, 'Michael James request to borrow \"Battle For Your Brain\"', '2024-12-08 08:01:58', 20220140, 0),
(97, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-08 09:06:32', 20220140, 0),
(98, 'Michael James request to borrow \"The Republic Of Salt\"', '2024-12-10 19:52:22', 20220140, 0),
(99, 'Michael James request to borrow \"From College To Ship\"', '2024-12-11 10:02:34', 20220140, 0),
(100, 'Michael James request to borrow \"Pathologic Basis of Disease\"', '2024-12-11 23:33:14', 20220140, 0),
(101, 'Michael James request to borrow \"English Communication Arts & Skill\"', '2024-12-12 01:51:46', 20220140, 0),
(102, 'Michael James request to borrow \"From College To Ship\"', '2024-12-12 15:42:50', 20220140, 0),
(103, 'Michael James request to borrow \"Amazing World of Computers\"', '2024-12-12 16:33:32', 20220140, 0),
(104, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-12 16:34:29', 20220140, 0),
(105, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-12 18:52:32', 20220140, 0),
(106, 'Michael James request to borrow \"Lost and Lassoed\"', '2024-12-12 18:53:13', 20220140, 0),
(107, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:23:50', 20220140, 0),
(108, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:23:50', 20220140, 0),
(109, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:23:50', 20220140, 0),
(110, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:24:19', 20220140, 0),
(111, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:24:19', 20220140, 0),
(112, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:24:19', 20220140, 0),
(113, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 13:24:31', 20220140, 0),
(114, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 13:24:31', 20220140, 0),
(115, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 13:24:31', 20220140, 0),
(116, 'Michael James request to borrow \"Phyton Essential For Dummies\"', '2024-12-13 13:26:41', 20220140, 0),
(117, 'Michael James request to borrow \"Phyton Essential For Dummies\"', '2024-12-13 13:26:41', 20220140, 0),
(118, 'Michael James request to borrow \"Realistic Mathematics\"', '2024-12-13 13:27:10', 20220140, 0),
(119, 'Michael James request to borrow \"Realistic Mathematics\"', '2024-12-13 13:27:10', 20220140, 0),
(120, 'Michael James request to borrow \"The Sun And The Star\"', '2024-12-13 13:29:47', 20220140, 0),
(121, 'Michael James request to borrow \"The Sun And The Star\"', '2024-12-13 13:29:47', 20220140, 0),
(122, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:30:02', 20220140, 0),
(123, 'Michael James request to borrow \"A.I For Dummies\"', '2024-12-13 13:30:03', 20220140, 0),
(124, 'Michael James request to borrow \"Lost and Lassoed\"', '2024-12-13 13:30:08', 20220140, 0),
(125, 'Michael James request to borrow \"Lost and Lassoed\"', '2024-12-13 13:30:09', 20220140, 0),
(126, 'Michael James request to borrow \"Lost And Lassoed\"', '2024-12-13 15:43:52', 20220140, 0),
(127, 'Michael James request to borrow \"Lost And Lassoed\"', '2024-12-13 15:43:53', 20220140, 0),
(128, 'Michael James request to borrow \"Broken Trust\"', '2024-12-13 15:45:09', 20220140, 0),
(129, 'Michael James request to borrow \"Broken Trust\"', '2024-12-13 15:45:09', 20220140, 0),
(130, 'Michael James request to borrow \"Lost And Lassoed\"', '2024-12-13 15:55:22', 20220140, 0),
(131, 'Michael James request to borrow \"Lost And Lassoed\"', '2024-12-13 15:55:22', 20220140, 0),
(132, 'Michael James request to borrow \"Broken Trust\"', '2024-12-13 17:24:18', 20220140, 0),
(133, 'Michael James request to borrow \"Broken Trust\"', '2024-12-13 17:24:18', 20220140, 0),
(134, 'Michael James request to borrow \"Broken Trust\"', '2024-12-13 17:24:18', 20220140, 0),
(135, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:24:34', 20220140, 0),
(136, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:24:34', 20220140, 0),
(137, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:24:34', 20220140, 0),
(138, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:30:33', 20220140, 0),
(139, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:30:33', 20220140, 0),
(140, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:30:33', 20220140, 0),
(141, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-13 17:40:59', 20220140, 0),
(142, 'Michael James request to borrow \"Pathologic Basis of Disease\"', '2024-12-13 17:41:12', 20220140, 0),
(143, 'Michael James request to borrow \"Soaring 21st Mathematics\"', '2024-12-13 20:38:09', 20220140, 0),
(144, 'Michael James request to borrow \"Soaring 21st Mathematics\"', '2024-12-13 20:38:09', 20220140, 0),
(145, 'Michael James request to borrow \"The Long Dead\"', '2024-12-13 22:03:57', 20220140, 0),
(146, 'Michael James request to borrow \"The Sun And The Star\"', '2024-12-13 22:04:22', 20220140, 0),
(147, 'Michael James request to borrow \"The Sun And The Star\"', '2024-12-13 22:04:28', 20220140, 0),
(148, 'Michael James request to borrow \"The Sun And The Star\"', '2024-12-13 22:05:11', 20220140, 0),
(149, 'Michael James request to borrow \"Heartstopper, Volume 4\"', '2024-12-13 22:05:42', 20220140, 0),
(150, 'Michael James request to borrow \"The Long Dead\"', '2024-12-13 22:37:48', 20220140, 0),
(151, 'Michael James request to borrow \"Soaring 21st Mathematics\"', '2024-12-13 23:25:48', 20220140, 0),
(152, 'Michael James request to borrow \"Pathologic Basis of Disease\"', '2024-12-14 02:57:14', 20220140, 0),
(153, 'Michael James request to borrow \"Pathologic Basis of Disease\"', '2024-12-14 02:57:14', 20220140, 0),
(154, 'Michael James request to borrow \"Pathologic Basis of Disease\"', '2024-12-14 02:57:14', 20220140, 0),
(155, 'Michael James request to borrow \"The Long Dead\"', '2024-12-14 04:03:16', 20220140, 0),
(156, 'Michael James request to borrow \"The Long Dead\"', '2024-12-14 04:03:16', 20220140, 0),
(157, 'Michael James request to borrow \"The Long Dead\"', '2024-12-14 04:03:16', 20220140, 0),
(158, 'Michael James request to borrow \"Battle For Your Brain\"', '2024-12-14 04:28:50', 20220140, 0),
(159, 'Michael James request to borrow \"Battle For Your Brain\"', '2024-12-14 04:28:50', 20220140, 0),
(160, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:34:58', 20220149, 0),
(161, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:34:58', 20220149, 0),
(162, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:34:58', 20220149, 0),
(163, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:34:58', 20220149, 0),
(164, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:35:01', 20220149, 0),
(165, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:35:01', 20220149, 0),
(166, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:35:01', 20220149, 0),
(167, 'Paolo request to borrow \"Realistic Mathematics\"', '2024-12-14 04:35:01', 20220149, 0),
(168, 'Michael Ochea request to borrow \"Battle For Your Brain\"', '2024-12-15 04:08:49', 20220140, 0),
(187, 'New user registered: james', '2024-12-15 14:30:17', 20220171, 0),
(189, 'New user registered: kei', '2024-12-15 16:14:40', 20220173, 0),
(190, 'kei request to borrow \"Battle For Your Brain\"', '2024-12-15 08:16:38', 20220173, 0),
(191, 'kei request to borrow \"Battle For Your Brain\"', '2024-12-15 08:16:53', 20220173, 0),
(195, 'Michael Ochea request to borrow \"Reading In Philippine History\"', '2024-12-15 23:44:07', 20220140, 0),
(196, 'james request to borrow \"Reading In Philippine History\"', '2024-12-15 23:44:09', 20220171, 0),
(197, 'james request to borrow \"A Shield of Sorrow\"', '2024-12-15 23:47:33', 20220171, 0),
(198, 'Michael Ochea request to borrow \"A Shield of Sorrow\"', '2024-12-15 23:47:50', 20220140, 0),
(204, 'New user registered: Denji', '2024-12-16 13:38:19', 20220175, 0),
(205, 'New user registered: nash', '2024-12-16 16:33:32', 20220176, 0),
(206, 'Michael Ochea request to borrow \"From College To Ship\"', '2024-12-16 08:53:39', 20220140, 0),
(207, 'Michael Ochea request to borrow \"From College To Ship\"', '2024-12-16 08:53:39', 20220140, 0),
(208, 'Michael Ochea request to borrow \"From College To Ship\"', '2024-12-16 08:53:40', 20220140, 0),
(209, 'Michael Ochea request to borrow \"From College To Ship\"', '2024-12-16 08:53:40', 20220140, 0),
(212, 'Michael Ochea request to borrow \"Broken Trust\"', '2024-12-16 09:38:50', 20220140, 0),
(213, 'Michael Ochea request to borrow \"Broken Trust\"', '2024-12-16 09:38:50', 20220140, 0),
(214, 'Michael Ochea request to borrow \"Broken Trust\"', '2024-12-16 09:38:50', 20220140, 0),
(219, 'Michael Ochea request to borrow \"Energy Saving For Dummies\"', '2024-12-19 15:29:54', 20220140, 0),
(220, 'Michael Ochea request to borrow \"J.S Essential For Dummies\"', '2024-12-19 15:30:54', 20220140, 0),
(221, 'Michael Ochea request to borrow \"Soaring 21st Mathematics\"', '2024-12-19 16:17:20', 20220140, 0),
(222, 'Jinxmoke request to borrow \"A.I For Dummies\"', '2024-12-19 18:22:26', 20220147, 0),
(223, 'Jinxmoke request to borrow \"Phyton Essential For Dummies\"', '2024-12-19 18:22:51', 20220147, 0),
(224, 'Michael James request to borrow \"J.S Essential For Dummies\"', '2024-12-19 18:23:29', 20220140, 0),
(225, 'New user registered: Hford', '2024-12-20 13:43:43', 20220183, 0),
(226, 'Overdue Book: Soaring 21st Mathematics', '2024-12-20 14:16:40', 20220140, 0),
(356, 'Michael James request to borrow \"Reading In Philippine History\"', '2024-12-20 15:20:05', 20220140, 0),
(357, 'Michael James request to borrow \"From College To Ship\"', '2024-12-20 15:21:59', 20220140, 0),
(358, 'New user registered: kiddo', '2024-12-21 00:56:34', 20220184, 0),
(359, 'kiddo request to borrow \"Battle For Your Brain\"', '2024-12-20 18:18:51', 20220184, 0),
(360, 'Michael James request to borrow \"Broken Trust\"', '2024-12-20 20:02:20', 20220140, 0),
(361, 'Michael James request to borrow \"Lost And Lassoed\"', '2024-12-20 21:03:40', 20220140, 0),
(362, 'Overdue Book: From College To Ship', '2024-12-21 11:58:28', 20220149, 0),
(363, 'Overdue Book: Essential English', '2024-12-21 11:58:28', 20220140, 0),
(386, 'Michael Ochea request to borrow \"Percy Jackson The Staff of Serapis\"', '2024-12-21 06:01:39', 20220140, 0),
(387, 'Michael Ochea request to borrow \"Lost And Lassoed\"', '2024-12-21 07:19:57', 20220140, 0),
(388, 'Michael Ochea request to borrow \"Lost And Lassoed\"', '2024-12-21 07:19:57', 20220140, 0),
(389, 'New user registered: jinxmoke', '2024-12-21 16:21:55', 20220185, 0),
(390, 'New user registered: Lawrence', '2024-12-21 17:07:28', 20220186, 0),
(391, 'Hford request to borrow \"Tech. & Livelihood Education\"', '2024-12-21 09:16:33', 20220183, 0),
(392, 'Hford request to borrow \"Tech. & Livelihood Education\"', '2024-12-21 09:16:33', 20220183, 0),
(393, 'New user registered: joyce', '2024-12-21 17:45:13', 20220187, 0),
(394, 'New user registered: joyce', '2024-12-21 17:48:31', 20220188, 0),
(395, 'joyce request to borrow \"Battle For Your Brain\"', '2024-12-21 09:50:20', 20220188, 0),
(396, 'New user registered: james', '2024-12-21 18:03:40', 20220189, 0),
(397, 'New user registered: paolo', '2024-12-21 18:56:21', 20220190, 0),
(398, 'New user registered: thirdy', '2025-02-10 19:40:05', 20220191, 1),
(399, 'New user registered: thirdy', '2025-02-10 19:41:32', 20220192, 1),
(400, 'New user registered: Poseidon', '2025-02-10 23:22:03', 20220193, 0),
(401, 'New user registered: james', '2025-02-13 23:43:34', 20220194, 0);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `expires_at` datetime NOT NULL,
  `reset_code` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `member_id`, `expires_at`, `reset_code`, `email`) VALUES
(85, 20220189, '2024-12-21 11:12:23', '5a0669b00cfa3ccc', 'estradanoellejames.bsit@gmail.com'),
(93, 20220140, '2025-02-14 06:31:08', 'cf0fe2a21ce51322', 'michaeljamesochea12@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `pending_requests`
--

CREATE TABLE `pending_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `member_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returned_books`
--

CREATE TABLE `returned_books` (
  `book_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `borrow_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returned_books`
--

INSERT INTO `returned_books` (`book_id`, `title`, `member_id`, `borrow_date`, `return_date`, `name`) VALUES
(292, 'From College To Ship', 20220140, '2024-11-01', '2024-11-08', 'Michael James'),
(292, 'From College To Ship', 20220147, '2024-11-09', '2024-11-14', 'Jinxmoke'),
(313, 'English Communication Arts & Skill', 20220140, '2024-12-12', '2024-12-14', 'Michael James'),
(310, 'Pathologic Basis of Disease', 20220140, '2024-12-12', '2024-12-14', 'Michael James'),
(315, 'Soaring 21st Mathematics', 20220140, '2024-12-14', '2024-12-14', 'Michael James'),
(295, 'Broken Trust', 20220140, '2024-12-07', '2024-12-13', 'Michael James'),
(308, 'Mapeh In Action 8th Edition', 20220140, '2024-12-03', '2024-12-13', 'Michael James'),
(293, 'The Long Dead', 20220140, '2024-11-29', '2024-12-13', 'Michael James'),
(297, 'Battle For Your Brain', 20220140, '2024-12-08', '2024-12-13', 'Michael James'),
(312, 'Tech. & Livelihood Education', 20220140, '2024-12-14', '2024-12-14', 'Michael James'),
(292, 'From College To Ship', 20220140, '2024-12-12', '2024-12-16', 'Michael Ochea'),
(293, 'The Long Dead', 20220140, '2024-12-14', '2024-12-16', 'Michael Ochea'),
(295, 'Broken Trust', 20220140, '2024-12-14', '2024-12-16', 'Michael Ochea'),
(300, 'The Sun And The Star', 20220140, '2024-12-14', '2024-12-16', 'Michael Ochea'),
(317, 'Phyton Essential For Dummies', 20220140, '2024-12-15', '2024-12-16', 'Michael Ochea'),
(317, 'Phyton Essential For Dummies', 20220171, '2024-12-15', '2024-12-16', 'james'),
(327, 'A Shield of Sorrow', 20220140, '2024-12-16', '2024-12-16', 'Michael Ochea'),
(314, 'Realistic Mathematics', 20220149, '2024-12-14', '2024-12-19', 'Paolo'),
(292, 'From College To Ship', 20220140, '2024-12-16', '2024-12-19', 'Michael Ochea'),
(297, 'Battle For Your Brain', 20220173, '2024-12-15', '2024-12-19', 'kei'),
(298, 'Lost And Lassoed', 20220148, '2024-12-13', '2024-12-19', 'YUI'),
(307, 'Reading In Philippine History', 20220171, '2024-12-16', '2024-12-19', 'james'),
(297, 'Battle For Your Brain', 20220184, '2024-12-21', '2024-12-21', 'kiddo'),
(315, 'Soaring 21st Mathematics', 20220140, '2024-12-20', '2024-12-21', 'Michael Ochea'),
(298, 'Lost And Lassoed', 20220140, '2024-12-21', '2024-12-21', 'Michael Ochea'),
(343, 'The Goldfinch', 20220149, '2024-12-21', '2024-12-21', 'Paolo'),
(292, 'From College To Ship', 20220149, '2024-12-21', '2024-12-21', 'Paolo'),
(297, 'Battle For Your Brain', 20220140, '2024-12-21', '2024-12-21', 'Michael Ochea'),
(311, 'Essential English', 20220140, '2024-12-21', '2024-12-21', 'Michael Ochea'),
(298, 'Lost And Lassoed', 20220140, '2024-12-21', '2024-12-21', 'Michael Ochea');

-- --------------------------------------------------------

--
-- Table structure for table `testimonial`
--

CREATE TABLE `testimonial` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonial`
--

INSERT INTO `testimonial` (`id`, `name`, `content`, `profile_picture`, `member_id`, `created_at`) VALUES
(10, 'Michael James', 'The library borrowing system is user-friendly, efficient, and offers a seamless experience for both eBook and physical.', '67516134c75df.jpg', 20220140, '2024-12-05 08:20:02'),
(11, 'Jinxmoke', 'The Borrowing Experience is pretty seamless and also, kudos to the librarian for being friendly', '67516389a0b5f.jpg', 20220147, '2024-12-05 08:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `tokens`
--

CREATE TABLE `tokens` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`id`, `member_id`, `book_id`, `token`, `expiry`, `created_at`) VALUES
(3, 20220140, 292, '97a3124011d74248e493f73ad88f850c', '2024-12-18 10:02:32', '2024-12-11 09:02:32'),
(4, 20220140, 292, '9489be1a8b9f4375192a9a09a716fac2', '2024-12-19 15:42:49', '2024-12-12 14:42:49'),
(5, 20220140, 300, '3cbf51d161b4beaf7f1f2231400db3bf', '2024-12-19 18:01:57', '2024-12-12 18:01:57'),
(6, 20220140, 301, '110159dcbb00cf059dfda0297bcb682d', '2024-12-19 18:52:30', '2024-12-12 18:52:30'),
(7, 20220140, 298, '71bb59b35aaa90ab76552952b0a7aa44', '2024-12-19 18:53:12', '2024-12-12 18:53:12'),
(8, 20220148, 298, '92ac93cf85ce64242a886942c9f2c655', '2024-12-20 05:57:06', '2024-12-13 05:57:06'),
(9, 20220140, 300, '2616b597049fb689d65160efa52beb4a', '2024-12-20 13:13:49', '2024-12-13 13:13:49'),
(10, 20220140, 293, '0137d9041f107eafe276235aadda4905', '2024-12-20 22:01:39', '2024-12-13 22:01:39'),
(11, 20220140, 295, '9fc40f6eedde6c41365a0d558d1ad8e3', '2024-12-20 22:01:46', '2024-12-13 22:01:46'),
(12, 20220140, 300, 'fc843967ae2c3c803f2542f4bfc2920a', '2024-12-20 22:01:54', '2024-12-13 22:01:54'),
(13, 20220140, 300, '57457338c8e0e444ce0b8cdad8e529cd', '2024-12-20 22:02:00', '2024-12-13 22:02:00'),
(14, 20220140, 293, 'ba1ddd794e6475b3046994bc60180482', '2024-12-20 22:03:56', '2024-12-13 22:03:56'),
(15, 20220140, 300, '425afbe22b5b32d94f5de83b6411d708', '2024-12-20 22:04:20', '2024-12-13 22:04:20'),
(16, 20220140, 300, 'fce844c1078f767fb091eef729e9dded', '2024-12-20 22:04:27', '2024-12-13 22:04:27'),
(17, 20220140, 300, '5c92658c09881b4b7630e163189ccd4f', '2024-12-20 22:05:10', '2024-12-13 22:05:10'),
(18, 20220140, 331, '1b321cd0adacdc11191ef2892ce43925', '2024-12-20 22:05:41', '2024-12-13 22:05:41'),
(19, 20220140, 293, '2e9f4fc659f1b7c0d8b27f59306303f2', '2024-12-20 22:37:47', '2024-12-13 22:37:47'),
(20, 20220140, 300, '886a6ba95be4dda882afe184041610d1', '2024-12-20 23:19:42', '2024-12-13 23:19:42'),
(21, 20220140, 295, '023c9bb33498b64a9d0cbc06eabd8ea6', '2024-12-20 23:19:48', '2024-12-13 23:19:48'),
(22, 20220140, 293, '1f7aa2306b364276805ceb73b1ff8843', '2024-12-21 04:03:15', '2024-12-14 04:03:15'),
(23, 20220140, 297, '242641a5c55c73e03a260c54c514aa02', '2024-12-21 04:28:48', '2024-12-14 04:28:48'),
(24, 20220140, 347, 'abf033ffbfffd513e055878458926580', '2024-12-22 04:08:06', '2024-12-15 04:08:06'),
(25, 20220140, 297, '8166b90289002f1657bb90ba95b20675', '2024-12-22 04:08:48', '2024-12-15 04:08:48'),
(26, 20220173, 297, 'a0a49bebf48df288f1392868b52e4e90', '2024-12-22 08:16:36', '2024-12-15 08:16:36'),
(27, 20220173, 297, 'a23aec06c1ff40febff29b0e04239c4d', '2024-12-22 08:16:51', '2024-12-15 08:16:51'),
(29, 20220140, 317, '60153a8ec4accdf6c53d158b96a5b293', '2024-12-22 10:27:19', '2024-12-15 10:27:19'),
(30, 20220171, 317, '5fec9191c18de54cb5a33253658e23ba', '2024-12-22 10:27:29', '2024-12-15 10:27:29'),
(31, 20220171, 327, '431df6fe2e5dbab3c90683e8ae206ebe', '2024-12-22 23:47:31', '2024-12-15 23:47:31'),
(32, 20220140, 327, '2d50d6a105c93155deec1e4e85cba48a', '2024-12-22 23:47:49', '2024-12-15 23:47:49'),
(36, 20220140, 292, 'e41f54e788b6167bf10c191cb02eb11d', '2024-12-23 08:53:38', '2024-12-16 08:53:38'),
(37, 20220140, 295, 'bddd360a939aca29fffe9e52d2946f2b', '2024-12-23 09:38:49', '2024-12-16 09:38:49'),
(38, 20220140, 319, 'fff349210208199cd0696bd9243263a7', '2024-12-26 15:29:52', '2024-12-19 15:29:52'),
(39, 20220140, 318, '773299fc3b961e4b190b0c474b60d4d6', '2024-12-26 15:30:53', '2024-12-19 15:30:53'),
(40, 20220149, 292, 'f041e4c6d9e853334e4c5b773d82bdfd', '2024-12-27 16:31:04', '2024-12-20 16:31:04'),
(41, 20220184, 297, '375de935f519225dab9ac90114288822', '2024-12-27 18:18:49', '2024-12-20 18:18:49'),
(42, 20220140, 295, '8df8f08037d58da6b6b34ef344cbbbde', '2024-12-27 20:02:19', '2024-12-20 20:02:19'),
(43, 20220140, 298, '4e21feb03f3f6dc3dd49520f8901d44b', '2024-12-27 21:03:38', '2024-12-20 21:03:38'),
(44, 20220140, 297, '7797ce971e58d04e0fc8c333962b618e', '2024-12-28 04:01:09', '2024-12-21 04:01:09'),
(45, 20220140, 361, '4c60f48f05396a5822d458ed25f09855', '2024-12-28 06:01:38', '2024-12-21 06:01:38'),
(46, 20220140, 298, '1de359d6e78bd5a319095a51d11c4925', '2024-12-28 07:19:55', '2024-12-21 07:19:55'),
(47, 20220188, 297, '0548c655ff6e65ea1bb383c2be004ddf', '2024-12-28 09:50:19', '2024-12-21 09:50:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `member_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `is_enabled` tinyint(4) DEFAULT 1,
  `status` enum('enabled','disabled') DEFAULT 'enabled',
  `contact` varchar(15) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `has_accepted_policy` tinyint(1) DEFAULT 0,
  `fines` int(11) DEFAULT 0,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`member_id`, `name`, `email`, `password`, `role`, `is_enabled`, `status`, `contact`, `profile_picture`, `address`, `has_accepted_policy`, `fines`, `email_verified`, `verification_token`) VALUES
(20220123, 'admin', 'admin@gmail.com', '4297f44b13955235245b2497399d7a93', 'admin', 1, 'enabled', NULL, NULL, NULL, 0, 0, 1, NULL),
(20220140, 'Michael Ochea', 'michaeljamesochea12@gmail.com', '8ef1f27ff72fa0f029ca95b743af47e2', 'user', 1, 'enabled', '09058963059', '6765ddb77adfb.jpg', '2nd Avenue Caloocan City', 1, 2, 1, NULL),
(20220147, 'Jinxmoke', 'jinxmoke.02@gmail.com', '0538a08c030beddf0684ad65cd70dd22', 'user', 1, 'enabled', '09384928491', '67516389a0b5f.jpg', '4th avenue caloocan city', 1, 0, 1, NULL),
(20220148, 'YUI', 'yui@gmail.com', '1b973e7de04517d481248b1433b6ef78', 'user', 1, 'enabled', '09123456789', NULL, '137 10th st. 10th ave.', 1, 0, 1, NULL),
(20220149, 'Paolo', 'rosalespaolo.bsit@gmail.com', '02525be651369329c7ba9ca0b76f8b24', 'user', 1, 'enabled', '09052744170', '675c416547f9c.jpg', '14 Lanzones Road, Potrero, Malabon City', 1, 0, 1, NULL),
(20220150, 'eych', 'eych@gmail.com', '94dbb977817c5aafd9bd682fb6780927', 'user', 1, 'enabled', '09453084462', NULL, 'Caloocan City', 0, 0, 1, NULL),
(20220171, 'james', 'jamesochea123@gmail.com', '8ef1f27ff72fa0f029ca95b743af47e2', 'user', 1, 'enabled', '09058963059', NULL, 'caloocan city', 1, 0, 1, NULL),
(20220173, 'kei', 'keilaballad@gmail.com', '5b66b16bb1ae0afba169c43f679c42ee', 'user', 1, 'enabled', '09063258357', NULL, 'San Mateo, Rizal', 1, 0, 1, NULL),
(20220175, 'Denji', 'panuganchristianrafael.bsit@gmail.com', 'd42e6b69da530ce05ccd810a60390e6e', 'user', 1, 'enabled', '09291390548', NULL, 'Caloocan city', 0, 0, 1, NULL),
(20220176, 'nash', 'jinxmoke.01@gmail.com', '8ef1f27ff72fa0f029ca95b743af47e2', 'user', 1, 'enabled', '09058963059', NULL, 'caloocqn', 0, 0, 0, 'e5dd674822d479e9e732fc0a4c1b6135e7c04947ce7dc90a79b7989988be47ed'),
(20220183, 'Hford', 'efondohford.bsit@gmail.com', '9243e56ec76ee1d50b2575b2277c4b55', 'user', 1, 'enabled', '09453084462', '6765080ddfe02.jpg', 'Caloocan City', 0, 1, 1, NULL),
(20220184, 'kiddo', 'kiddpuyat@gmail.com', '28a10d33baf59a23ebfcf90c2d52e729', 'user', 1, 'enabled', '09162032929', '67668971c1c6b.jpg', '2nd avenue caloocan city', 0, 0, 1, NULL),
(20220185, 'jinxmoke', 'jinxmokesakalam@gmail.com', '8ef1f27ff72fa0f029ca95b743af47e2', 'user', 1, 'enabled', '09058963059', NULL, 'Caloocan', 0, 0, 1, NULL),
(20220186, 'Lawrence', 'lawrenceochea123@gmail.com', '8ef1f27ff72fa0f029ca95b743af47e2', 'user', 1, 'enabled', '09058963059', NULL, 'caloocqn', 0, 0, 1, NULL),
(20220187, 'joyce', 'joyceannepunla854@gamil.com', '01956f3185109ba3e9ec5518fefb1b99', 'user', 1, 'enabled', '09389870738', NULL, 'Arania st. Reparo Caloocan City', 0, 0, 0, 'b0300a052fd33f55006359bac266c5071c9d346da14560b4d5ff33a06c372d7c'),
(20220188, 'joyce', 'joyceannepunla854@gmail.com', '01956f3185109ba3e9ec5518fefb1b99', 'user', 1, 'enabled', '09389870738', NULL, 'Arania st. Reparo Caloocan City', 0, 0, 1, NULL),
(20220189, 'james', 'estradanoellejames.bsit@gmail.com', 'ad9f4e6665d9887c627d92766d4f9410', 'user', 1, 'enabled', '09958948397', '676692fb5be31.png', 'caloocan city', 0, 0, 1, NULL),
(20220190, 'paolo', 'paolorosales101203@gmail.com', '02525be651369329c7ba9ca0b76f8b24', 'user', 1, 'enabled', '09389870738', NULL, 'Arania st. Reparo Caloocan City', 0, 0, 1, NULL),
(20220191, 'thirdy', 'ghiandejesus@gmail.com', 'c59162d7180ecd6690fd10f302b01dd3', 'user', 1, 'enabled', '09090909', NULL, 'klsddakwnmdklaw', 0, 0, 0, '0d37b0edb3ca8d62dd186e56fab9be9ebb50e307bb20c7669bc9986ebcae07df'),
(20220192, 'thirdy', 'dejesuselmerghian.bsit@gmail.com', 'c59162d7180ecd6690fd10f302b01dd3', 'user', 1, 'enabled', '0909090909', NULL, 'fjseghfjse', 0, 0, 1, NULL),
(20220193, 'Poseidon', 'christianreafael@gmail.com', '72f9ca11483fa1b86c69e448360df2a5', 'user', 1, 'enabled', '09295536730', NULL, '231312313street', 0, 0, 0, 'fe42a92bf1a2c0cfc2a149e649775539aec1068cb0ef944594c13cf5cab2e2ea'),
(20220194, 'james', 'fafafa@gmail.com', '47b739a3a60399a2078197d8042f16cc', 'user', 1, 'enabled', '98392929291', NULL, 'ljaljdlajdljald', 0, 0, 0, '4305c79380e72de41457357610f8d66deb849ceb4e53c1b82e4a04b19875e307');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`member_id`,`book_id`);

--
-- Indexes for table `book_comments`
--
ALTER TABLE `book_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  ADD PRIMARY KEY (`book_id`,`member_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
