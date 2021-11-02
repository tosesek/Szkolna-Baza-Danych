-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Czas generowania: 09 Cze 2020, 13:27
-- Wersja serwera: 5.7.30-0ubuntu0.18.04.1
-- Wersja PHP: 7.2.24-0ubuntu0.18.04.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `panel`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_polish_ci NOT NULL COMMENT 'Unikalna nazwa',
  `first_name` text COLLATE utf8_polish_ci NOT NULL,
  `last_name` text COLLATE utf8_polish_ci NOT NULL,
  `email` text COLLATE utf8_polish_ci NOT NULL,
  `unencrypted_password` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'PO WERYFIKACJI USTAWIC NA NULL!!!',
  `password` text COLLATE utf8_polish_ci NOT NULL COMMENT 'Hasło',
  `privileges` int(11) NOT NULL DEFAULT '0' COMMENT '0-user; 1-admin',
  `verified` int(11) NOT NULL DEFAULT '0' COMMENT '0-brak email;1-brak admin; 2-ok',
  `blocked` int(11) NOT NULL DEFAULT '0' COMMENT '0-nie; 1-tak',
  `year` int(11) NOT NULL COMMENT 'Rok przyjścia do szkoły',
  `reset_password_id` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'jednorazowy kod do zresetowania hasla',
  `activation_code` varchar(6) COLLATE utf8_polish_ci NOT NULL COMMENT 'Weryfikacja anty-spam',
  `developer` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `email`, `unencrypted_password`, `password`, `privileges`, `verified`, `blocked`, `year`, `reset_password_id`, `activation_code`, `developer`) VALUES
(1, 'admin', 'Administrator', 'Wszechczasów', 'eml@zst.pila.pl', NULL, '4028a0e356acc947fcd2bfbf00cef11e128d484a', 1, 2, 0, 1337, NULL, '000000', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
