-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Giu 26, 2018 alle 14:09
-- Versione del server: 10.1.32-MariaDB
-- Versione PHP: 7.2.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shuttledb`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `booking`
--

DROP TABLE IF EXISTS `BOOKING`;
CREATE TABLE `BOOKING` (
  `username` varchar(255) NOT NULL,
  `departure` varchar(255) NOT NULL,
  `arrival` varchar(255) NOT NULL,
  `nPeople` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `booking`
--

INSERT INTO `BOOKING` (`username`, `departure`, `arrival`, `nPeople`) VALUES
('u1@p.it', 'FF', 'KK', 4),
('u2@p.it', 'BB', 'EE', 1),
('u3@p.it', 'DD', 'EE', 1),
('u4@p.it', 'AL', 'DD', 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `itinerary`
--

DROP TABLE IF EXISTS `ITINERARY`;
CREATE TABLE `ITINERARY` (
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `itinerary`
--

INSERT INTO `ITINERARY` (`name`) VALUES
('AL'),
('BB'),
('DD'),
('EE'),
('FF'),
('KK');

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

DROP TABLE IF EXISTS `USER`;
CREATE TABLE `USER` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `USER` (`username`, `password`) VALUES
('u1@p.it', '$2y$10$OUqQzvsAm93A5aFXB/94Lea4GyHyhCNDp.O3rd2uv1.Mv14HdKg5W'),
('u2@p.it', '$2y$10$c8RJ/3KNvRt62xIrZUN9Hu0BgqVVriSoPCi2.iahDbGXIAZz.LsWO'),
('u3@p.it', '$2y$10$9ds1XS1tauBZ8NkXHdKYfuWq.pVnIar6v4Ds7bm5rR8W2rlGI2xnu'),
('u4@p.it', '$2y$10$xhnkK4X0a8LUWaW.JAHWNeioJAJvC4a.2KrvYtFy/wwVjXbcl74NW');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `booking`
--
ALTER TABLE `BOOKING`
  ADD PRIMARY KEY (`username`);

--
-- Indici per le tabelle `itinerary`
--
ALTER TABLE `ITINERARY`
  ADD PRIMARY KEY (`name`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `USER`
  ADD PRIMARY KEY (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
