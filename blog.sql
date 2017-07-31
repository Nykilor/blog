-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 31 Lip 2017, 07:52
-- Wersja serwera: 10.1.13-MariaDB
-- Wersja PHP: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `blog`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `author`
--

CREATE TABLE `author` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(30) NOT NULL,
  `img_path` varchar(255) NOT NULL DEFAULT 'resources/img/default.png',
  `about` text NOT NULL,
  `access_type` int(11) NOT NULL DEFAULT '1',
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `author`
--

INSERT INTO `author` (`id`, `name`, `email`, `img_path`, `about`, `access_type`, `login`, `password`) VALUES
(1, 'Adam', 'smigi1997@gmail.com', 'resources/img/new.JPG', 'A simple description of the author.', 1, 'root', '$2y$10$uJLaUHwPmC5fxN3.QZKfheOGa4DTH0j2hELONhVzqn1rr2sNStPni');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `author_ip` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `comment`
--

INSERT INTO `comment` (`id`, `post_id`, `content`, `author`, `author_ip`, `date`) VALUES
(1, 1, 'I love it!', 'Totaly not Adam.', '::1', '2017-07-31 07:06:01'),
(2, 1, 'I love what i see!', 'Webperson', '::1', '2017-07-31 07:20:00'),
(3, 1, '&#60;script&#62;alert(&#39;xss&#39;);&#60;/script&#62;', 'Hackerman', '::1', '2017-07-31 07:31:35'),
(4, 3, 'Is it true!?', 'Seeker', '::1', '2017-07-31 07:35:05'),
(5, 4, 'No no no', 'Adam', '::1', '2017-07-31 07:47:47');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `content_short` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` varchar(50) NOT NULL,
  `title_slug` varchar(255) NOT NULL COMMENT 'Title slug',
  `thumbnail_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `post`
--

INSERT INTO `post` (`id`, `author_id`, `content`, `content_short`, `date`, `title`, `title_slug`, `thumbnail_path`) VALUES
(1, 1, 'This is the first post ever posted on this website.', 'First post.', '2017-07-31 06:46:24', 'First post !', 'first-post', NULL),
(2, 1, 'The order by does really work!', 'You won&#39;t believe what is written here!', '2017-07-31 07:22:57', 'Second post!', 'second-post', NULL),
(3, 1, 'As you see it is!', 'Who would believe!', '2017-07-31 07:28:30', 'Admin got freedom in <b>tags</b>!', 'admin-got-freedom-in-b-tags-b', NULL),
(4, 1, 'Did it do it?', 'TURURU', '2017-07-31 07:47:36', 'Github broke me?', 'github-broke-me', NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `UserLogin` (`login`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `COMMENT` (`post_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `title` (`title`),
  ADD UNIQUE KEY `slug` (`title_slug`),
  ADD KEY `authorId` (`author_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `author`
--
ALTER TABLE `author`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT dla tabeli `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ograniczenia dla tabeli `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `author` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
