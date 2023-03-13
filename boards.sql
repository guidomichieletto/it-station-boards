-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boards`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `annunci`
--

CREATE TABLE `annunci` (
  `IDAnnuncio` int(11) NOT NULL,
  `CodAnnuncio` varchar(20) NOT NULL,
  `Testo` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `annunci`
--

INSERT INTO `annunci` (`IDAnnuncio`, `CodAnnuncio`, `Testo`) VALUES
(1, 'P1', 'Il treno _TIPOTRENO , _NUMERO , di _IMPRESA , delle ore _ORAPARTENZA , per _DESTINAZIONE , è in partenza , _RITARDO dal binario _BINARIO'),
(2, 'A1', 'Il treno _TIPOTRENO , _NUMERO , di _IMPRESA , delle ore _ORAARRIVO , proveniente da _PROVENIENZA _DESTINAZIONE , è in arrivo al binario _BINARIO . Attenzione! Allontanarsi dalla linea gialla');

-- --------------------------------------------------------

--
-- Struttura della tabella `societa`
--

CREATE TABLE `societa` (
  `IDSocieta` int(11) NOT NULL,
  `NomeSocieta` varchar(255) NOT NULL,
  `ImgSocieta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `societa`
--

INSERT INTO `societa` (`IDSocieta`, `NomeSocieta`, `ImgSocieta`) VALUES
(1, 'Trenitalia', 'trenitalia'),
(2, 'Nuovo Trasporto Viaggiatori', 'italo'),
(3, 'OBB', 'obb'),
(4, 'Trenord', 'trenord'),
(5, 'SAD', 'sad');

-- --------------------------------------------------------

--
-- Struttura della tabella `stazioni`
--

CREATE TABLE `stazioni` (
  `IDStazione` int(11) NOT NULL,
  `NomeStazione` varchar(255) NOT NULL,
  `NomeDisplay` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `stazioni`
--

INSERT INTO `stazioni` (`IDStazione`, `NomeStazione`, `NomeDisplay`) VALUES
(1, 'Venezia Santa Lucia', 'VENEZIA SL'),
(2, 'Venezia Mestre', 'VENEZIA MESTRE'),
(3, 'Treviso Centrale', 'TREVISO CLE');

-- --------------------------------------------------------

--
-- Struttura della tabella `tipitreno`
--

CREATE TABLE `tipitreno` (
  `IDTipoTreno` int(11) NOT NULL,
  `NomeTipoTreno` varchar(255) NOT NULL,
  `ImgTipoTreno` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `tipitreno`
--

INSERT INTO `tipitreno` (`IDTipoTreno`, `NomeTipoTreno`, `ImgTipoTreno`) VALUES
(1, 'Regionale', 'r'),
(2, 'Regionale Veloce', 'rv'),
(3, 'Intercity', 'ic'),
(4, 'Alta Velocità', 'av'),
(5, 'Eurocity', 'ec'),
(6, 'EuroStar City', 'escity');

-- --------------------------------------------------------

--
-- Struttura della tabella `treni`
--

CREATE TABLE `treni` (
  `NumTreno` int(11) NOT NULL,
  `IDSocieta` int(11) NOT NULL,
  `IDTipoTreno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `treni`
--

INSERT INTO `treni` (`NumTreno`, `IDSocieta`, `IDTipoTreno`) VALUES
(4501, 1, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `trenigiorno`
--

CREATE TABLE `trenigiorno` (
  `IDTrenoGiorno` int(11) NOT NULL,
  `NumTreno` int(11) NOT NULL,
  `Giorno` date NOT NULL,
  `Ritardo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `trenigiorno`
--

INSERT INTO `trenigiorno` (`IDTrenoGiorno`, `NumTreno`, `Giorno`, `Ritardo`) VALUES
(1, 4501, '2023-03-13', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `trenistazioni`
--

CREATE TABLE `trenistazioni` (
  `IDStazTreno` int(11) NOT NULL,
  `NumTreno` int(11) NOT NULL,
  `IDStazione` int(11) NOT NULL,
  `TipoStazione` int(11) NOT NULL,
  `OrarioPrevistoArrivo` time NOT NULL,
  `OrarioPrevistoPartenza` time NOT NULL,
  `OrarioRealeArrivo` time NOT NULL,
  `OrarioRealePartenza` time NOT NULL,
  `BinarioPrevisto` int(11) NOT NULL,
  `BinarioReale` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `trenistazioni`
--

INSERT INTO `trenistazioni` (`IDStazTreno`, `NumTreno`, `IDStazione`, `TipoStazione`, `OrarioPrevistoArrivo`, `OrarioPrevistoPartenza`, `OrarioRealeArrivo`, `OrarioRealePartenza`, `BinarioPrevisto`, `BinarioReale`) VALUES
(1, 4501, 1, 1, '09:45:00', '09:45:00', '09:45:00', '09:45:00', 1, 1),
(2, 4501, 2, 2, '10:00:00', '10:00:00', '10:00:00', '10:00:00', 1, 1),
(3, 4501, 3, 3, '00:00:00', '00:00:00', '00:00:00', '00:00:00', 1, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `annunci`
--
ALTER TABLE `annunci`
  ADD PRIMARY KEY (`IDAnnuncio`);

--
-- Indici per le tabelle `societa`
--
ALTER TABLE `societa`
  ADD PRIMARY KEY (`IDSocieta`);

--
-- Indici per le tabelle `stazioni`
--
ALTER TABLE `stazioni`
  ADD PRIMARY KEY (`IDStazione`);

--
-- Indici per le tabelle `tipitreno`
--
ALTER TABLE `tipitreno`
  ADD PRIMARY KEY (`IDTipoTreno`);

--
-- Indici per le tabelle `treni`
--
ALTER TABLE `treni`
  ADD PRIMARY KEY (`NumTreno`);

--
-- Indici per le tabelle `trenigiorno`
--
ALTER TABLE `trenigiorno`
  ADD PRIMARY KEY (`IDTrenoGiorno`);

--
-- Indici per le tabelle `trenistazioni`
--
ALTER TABLE `trenistazioni`
  ADD PRIMARY KEY (`IDStazTreno`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `annunci`
--
ALTER TABLE `annunci`
  MODIFY `IDAnnuncio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `societa`
--
ALTER TABLE `societa`
  MODIFY `IDSocieta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT per la tabella `stazioni`
--
ALTER TABLE `stazioni`
  MODIFY `IDStazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `tipitreno`
--
ALTER TABLE `tipitreno`
  MODIFY `IDTipoTreno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `trenigiorno`
--
ALTER TABLE `trenigiorno`
  MODIFY `IDTrenoGiorno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT per la tabella `trenistazioni`
--
ALTER TABLE `trenistazioni`
  MODIFY `IDStazTreno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
