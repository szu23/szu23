-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Cze 08, 2026 at 10:52 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog`
--
CREATE DATABASE IF NOT EXISTS `blog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `blog`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `glosy`
--

DROP TABLE IF EXISTS `glosy`;
CREATE TABLE `glosy` (
  `id` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_posta` int(11) NOT NULL,
  `glosowanie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glosy`
--

INSERT INTO `glosy` (`id`, `id_uzytkownika`, `id_posta`, `glosowanie`) VALUES
(1, 7, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `kategorie`
--

DROP TABLE IF EXISTS `kategorie`;
CREATE TABLE `kategorie` (
  `nazwa_kategorii` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorie`
--

INSERT INTO `kategorie` (`nazwa_kategorii`) VALUES
('Technologia'),
('Podróże'),
('Programowanie'),
('DIY'),
('Motoryzacja');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `komentarze`
--

DROP TABLE IF EXISTS `komentarze`;
CREATE TABLE `komentarze` (
  `id_komentarza` int(11) NOT NULL,
  `id_uzytkownika` int(11) NOT NULL,
  `id_posta` int(11) NOT NULL,
  `tresc_komentarza` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `komentarze`
--

INSERT INTO `komentarze` (`id_komentarza`, `id_uzytkownika`, `id_posta`, `tresc_komentarza`) VALUES
(1, 7, 1, 'Bardzo dobry post!');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `posty`
--

DROP TABLE IF EXISTS `posty`;
CREATE TABLE `posty` (
  `id_posta` int(11) NOT NULL,
  `id_uzytkownika` int(11) DEFAULT NULL,
  `tytul` varchar(255) NOT NULL,
  `tresc` varchar(10000) NOT NULL,
  `kategoria` varchar(255) NOT NULL,
  `data_utworzenia` date NOT NULL DEFAULT current_timestamp(),
  `zdjecie` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posty`
--

INSERT INTO `posty` (`id_posta`, `id_uzytkownika`, `tytul`, `tresc`, `kategoria`, `data_utworzenia`, `zdjecie`) VALUES
(1, 7, 'Jak technologia zmienia nasze codzienne życie?', 'Technologia jest dziś obecna prawie wszędzie. Korzystamy z niej podczas nauki, pracy, robienia zakupów, komunikowania się ze znajomymi oraz spędzania wolnego czasu. Smartfony, komputery, Internet i aplikacje mobilne stały się dla wielu osób czymś zupełnie normalnym.\r\nJednym z największych plusów rozwoju technologii jest szybki dostęp do informacji. Wystarczy kilka sekund, aby znaleźć potrzebne wiadomości, obejrzeć poradnik lub skontaktować się z kimś z drugiego końca świata. Dzięki temu nauka i praca stały się znacznie wygodniejsze.\r\nTechnologia ma jednak także swoje minusy. Zbyt częste korzystanie z telefonu lub komputera może prowadzić do rozproszenia uwagi i marnowania czasu. Dlatego warto korzystać z nowoczesnych rozwiązań rozsądnie i pamiętać o odpoczynku od ekranu.\r\nMoim zdaniem technologia bardzo ułatwia życie, ale najważniejsze jest to, aby używać jej świadomie. Powinna pomagać człowiekowi, a nie całkowicie zastępować normalne relacje, odpoczynek i aktywność poza Internetem.', 'Technologia', '2026-06-08', 'uploads/1780947129_6a2718b921875.jpg');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `uzytkownicy`
--

DROP TABLE IF EXISTS `uzytkownicy`;
CREATE TABLE `uzytkownicy` (
  `id_uzytkownika` int(11) NOT NULL,
  `nazwa_uzytkownika` varchar(50) NOT NULL,
  `haslo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uzytkownicy`
--

INSERT INTO `uzytkownicy` (`id_uzytkownika`, `nazwa_uzytkownika`, `haslo`) VALUES
(6, 'admin', '$2y$10$TS1uaCc7S6HB4F1/kkTiEewVoCZxb6iCzjeJdT.7fOJkUm/2f1b2m'),
(7, 'user1', '$2y$10$tT6WUx8T0tQy5MDMzOkCM.5qur6L3zEepFKRHDRPdv7vJrT0XD6rK');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `glosy`
--
ALTER TABLE `glosy`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_uzytkownika` (`id_uzytkownika`,`id_posta`),
  ADD KEY `id_posta` (`id_posta`);

--
-- Indeksy dla tabeli `komentarze`
--
ALTER TABLE `komentarze`
  ADD PRIMARY KEY (`id_komentarza`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`),
  ADD KEY `id_posta` (`id_posta`);

--
-- Indeksy dla tabeli `posty`
--
ALTER TABLE `posty`
  ADD PRIMARY KEY (`id_posta`),
  ADD KEY `id_uzytkownika` (`id_uzytkownika`);

--
-- Indeksy dla tabeli `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  ADD PRIMARY KEY (`id_uzytkownika`),
  ADD UNIQUE KEY `nazwa` (`nazwa_uzytkownika`),
  ADD UNIQUE KEY `nazwa_uzytkownika` (`nazwa_uzytkownika`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `glosy`
--
ALTER TABLE `glosy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `komentarze`
--
ALTER TABLE `komentarze`
  MODIFY `id_komentarza` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `posty`
--
ALTER TABLE `posty`
  MODIFY `id_posta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `uzytkownicy`
--
ALTER TABLE `uzytkownicy`
  MODIFY `id_uzytkownika` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `glosy`
--
ALTER TABLE `glosy`
  ADD CONSTRAINT `glosy_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`),
  ADD CONSTRAINT `glosy_ibfk_2` FOREIGN KEY (`id_posta`) REFERENCES `posty` (`id_posta`);

--
-- Constraints for table `komentarze`
--
ALTER TABLE `komentarze`
  ADD CONSTRAINT `komentarze_ibfk_1` FOREIGN KEY (`id_posta`) REFERENCES `posty` (`id_posta`),
  ADD CONSTRAINT `komentarze_ibfk_2` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`);

--
-- Constraints for table `posty`
--
ALTER TABLE `posty`
  ADD CONSTRAINT `posty_ibfk_1` FOREIGN KEY (`id_uzytkownika`) REFERENCES `uzytkownicy` (`id_uzytkownika`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
