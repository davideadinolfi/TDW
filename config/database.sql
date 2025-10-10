-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Creato il: Ott 10, 2025 alle 11:07
-- Versione del server: 9.1.0
-- Versione PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tdw`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `caratteristiche`
--

DROP TABLE IF EXISTS `caratteristiche`;
CREATE TABLE IF NOT EXISTS `caratteristiche` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tipo` int NOT NULL,
  `nome_caratteristica` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_tipo` (`id_tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `caratteristiche`
--

INSERT INTO `caratteristiche` (`id`, `id_tipo`, `nome_caratteristica`) VALUES
(1, 1, 'frequenza'),
(2, 1, 'TDP'),
(3, 2, 'frequenza'),
(4, 2, 'VRAM'),
(5, 2, 'RTX');

-- --------------------------------------------------------

--
-- Struttura della tabella `corrieri`
--

DROP TABLE IF EXISTS `corrieri`;
CREATE TABLE IF NOT EXISTS `corrieri` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `corrieri`
--

INSERT INTO `corrieri` (`id`, `nome`) VALUES
(1, 'poste italiane'),
(2, 'BRT');

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppi`
--

DROP TABLE IF EXISTS `gruppi`;
CREATE TABLE IF NOT EXISTS `gruppi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  `descrizione` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `gruppi`
--

INSERT INTO `gruppi` (`id`, `nome`, `descrizione`) VALUES
(1, 'utenti', 'utenti'),
(2, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppi_servizi`
--

DROP TABLE IF EXISTS `gruppi_servizi`;
CREATE TABLE IF NOT EXISTS `gruppi_servizi` (
  `id_gruppo` int NOT NULL,
  `id_servizio` int NOT NULL,
  PRIMARY KEY (`id_gruppo`,`id_servizio`),
  KEY `id_servizio` (`id_servizio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `gruppi_servizi`
--

INSERT INTO `gruppi_servizi` (`id_gruppo`, `id_servizio`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `item_carrello`
--

DROP TABLE IF EXISTS `item_carrello`;
CREATE TABLE IF NOT EXISTS `item_carrello` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL,
  `id_prodotto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`),
  KEY `id_prodotto` (`id_prodotto`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `item_ordini`
--

DROP TABLE IF EXISTS `item_ordini`;
CREATE TABLE IF NOT EXISTS `item_ordini` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ordine` int NOT NULL,
  `id_prodotto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_prodotto` (`id_prodotto`),
  KEY `id_ordine` (`id_ordine`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `item_ordini`
--

INSERT INTO `item_ordini` (`id`, `id_ordine`, `id_prodotto`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 2, 3),
(4, 2, 1),
(5, 3, 1),
(6, 3, 1),
(7, 4, 3),
(8, 4, 2),
(9, 5, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `liste`
--

DROP TABLE IF EXISTS `liste`;
CREATE TABLE IF NOT EXISTS `liste` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL,
  `nome` varchar(64) NOT NULL,
  `descrizione` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `liste`
--

INSERT INTO `liste` (`id`, `id_utente`, `nome`, `descrizione`) VALUES
(6, 1, 'lista random', 'ciaociao'),
(7, 1, 'lista vuota', 'vuota');

-- --------------------------------------------------------

--
-- Struttura della tabella `liste_prodotti`
--

DROP TABLE IF EXISTS `liste_prodotti`;
CREATE TABLE IF NOT EXISTS `liste_prodotti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_lista` int NOT NULL,
  `id_prodotto` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_prodotto` (`id_prodotto`),
  KEY `id_lista` (`id_lista`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `liste_prodotti`
--

INSERT INTO `liste_prodotti` (`id`, `id_lista`, `id_prodotto`) VALUES
(7, 6, 2),
(8, 6, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `ordini`
--

DROP TABLE IF EXISTS `ordini`;
CREATE TABLE IF NOT EXISTS `ordini` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL,
  `id_corriere` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`),
  KEY `id_corriere` (`id_corriere`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `ordini`
--

INSERT INTO `ordini` (`id`, `id_utente`, `id_corriere`, `created_at`) VALUES
(1, 1, 2, '2025-10-06 22:24:52'),
(2, 1, 2, '2025-10-08 16:14:17'),
(3, 1, 2, '2025-10-08 16:14:44'),
(4, 1, 2, '2025-10-08 16:15:10'),
(5, 1, 1, '2025-10-08 16:15:41');

-- --------------------------------------------------------

--
-- Struttura della tabella `prodotti`
--

DROP TABLE IF EXISTS `prodotti`;
CREATE TABLE IF NOT EXISTS `prodotti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venditore` int NOT NULL,
  `nome` varchar(64) NOT NULL,
  `descrizione` varchar(1024) DEFAULT NULL,
  `prezzo` double NOT NULL,
  `immagine` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_venditore` (`id_venditore`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `prodotti`
--

INSERT INTO `prodotti` (`id`, `id_venditore`, `nome`, `descrizione`, `prezzo`, `immagine`) VALUES
(1, 3, 'i7 12700k', 'Intel Core i7-12700 processore 25 MB Cache intelligente', 250, '12700k.jpg'),
(2, 1, 'AMD Ryzen™ 7 5800X', 'AMD Ryzen™ 7 5800X', 225, '51HqC0rU9HL.jpg'),
(3, 2, 'RTX 5080', 'ASUS PRIME NVIDIA GeForce RTX 5080 OC Edition, Scheda Grafica 16 GB GDDR7, 256 Bit', 1000, '5080.jfif');

-- --------------------------------------------------------

--
-- Struttura della tabella `recensioni_prodotti`
--

DROP TABLE IF EXISTS `recensioni_prodotti`;
CREATE TABLE IF NOT EXISTS `recensioni_prodotti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL,
  `id_prodotto` int NOT NULL,
  `contenuto` varchar(1024) DEFAULT NULL,
  `voto` int NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_prodotto` (`id_prodotto`),
  KEY `id_utente` (`id_utente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `recensioni_prodotti`
--

INSERT INTO `recensioni_prodotti` (`id`, `id_utente`, `id_prodotto`, `contenuto`, `voto`, `data`) VALUES
(1, 1, 2, 'daje', 3, '2025-10-06 20:25:00');

-- --------------------------------------------------------

--
-- Struttura della tabella `recensioni_venditori`
--

DROP TABLE IF EXISTS `recensioni_venditori`;
CREATE TABLE IF NOT EXISTS `recensioni_venditori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utente` int NOT NULL,
  `id_venditore` int NOT NULL,
  `contenuto` varchar(1024) DEFAULT NULL,
  `voto` int NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_utente` (`id_utente`),
  KEY `id_venditore` (`id_venditore`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `recensioni_venditori`
--

INSERT INTO `recensioni_venditori` (`id`, `id_utente`, `id_venditore`, `contenuto`, `voto`, `data`) VALUES
(1, 1, 1, 'mid', 1, '2025-10-06 20:25:10');

-- --------------------------------------------------------

--
-- Struttura della tabella `servizi`
--

DROP TABLE IF EXISTS `servizi`;
CREATE TABLE IF NOT EXISTS `servizi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  `descrizione` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `servizi`
--

INSERT INTO `servizi` (`id`, `nome`, `descrizione`) VALUES
(1, 'utente', 'servizio utenti'),
(2, 'admin', 'servizio admin\r\n');

-- --------------------------------------------------------

--
-- Struttura della tabella `specifiche`
--

DROP TABLE IF EXISTS `specifiche`;
CREATE TABLE IF NOT EXISTS `specifiche` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_caratteristica` int NOT NULL,
  `id_prodotto` int NOT NULL,
  `specifica` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_caratteristica` (`id_caratteristica`),
  KEY `id_prodotto` (`id_prodotto`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `specifiche`
--

INSERT INTO `specifiche` (`id`, `id_caratteristica`, `id_prodotto`, `specifica`) VALUES
(1, 1, 1, '4GHZ'),
(2, 1, 2, '3.9GHZ'),
(3, 2, 1, '95W'),
(4, 2, 2, '95W'),
(5, 3, 3, '2000mhz'),
(6, 4, 3, '16GB'),
(7, 5, 3, 'SI');

-- --------------------------------------------------------

--
-- Struttura della tabella `tipi`
--

DROP TABLE IF EXISTS `tipi`;
CREATE TABLE IF NOT EXISTS `tipi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `tipi`
--

INSERT INTO `tipi` (`id`, `nome`) VALUES
(1, 'CPU'),
(2, 'scheda video');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

DROP TABLE IF EXISTS `utenti`;
CREATE TABLE IF NOT EXISTS `utenti` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `nome` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `email`, `nome`, `password`) VALUES
(1, 'test@gmail.com', 'test', '$2y$10$3g49wn0R2KVO5oxDuWGGGeztVmLGk6GGCb4THIU/2Nk4hX03p94G2');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti_gruppi`
--

DROP TABLE IF EXISTS `utenti_gruppi`;
CREATE TABLE IF NOT EXISTS `utenti_gruppi` (
  `id_utente` int NOT NULL,
  `id_gruppo` int NOT NULL,
  PRIMARY KEY (`id_utente`,`id_gruppo`),
  KEY `id_gruppo` (`id_gruppo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `utenti_gruppi`
--

INSERT INTO `utenti_gruppi` (`id_utente`, `id_gruppo`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `venditori`
--

DROP TABLE IF EXISTS `venditori`;
CREATE TABLE IF NOT EXISTS `venditori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dump dei dati per la tabella `venditori`
--

INSERT INTO `venditori` (`id`, `nome`) VALUES
(1, 'AMD'),
(2, 'nvidia'),
(3, 'intel');

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `caratteristiche`
--
ALTER TABLE `caratteristiche`
  ADD CONSTRAINT `caratteristiche_ibfk_1` FOREIGN KEY (`id_tipo`) REFERENCES `tipi` (`id`);

--
-- Limiti per la tabella `gruppi_servizi`
--
ALTER TABLE `gruppi_servizi`
  ADD CONSTRAINT `gruppi_servizi_ibfk_1` FOREIGN KEY (`id_gruppo`) REFERENCES `gruppi` (`id`),
  ADD CONSTRAINT `gruppi_servizi_ibfk_2` FOREIGN KEY (`id_servizio`) REFERENCES `servizi` (`id`);

--
-- Limiti per la tabella `item_carrello`
--
ALTER TABLE `item_carrello`
  ADD CONSTRAINT `item_carrello_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `item_carrello_ibfk_2` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`);

--
-- Limiti per la tabella `item_ordini`
--
ALTER TABLE `item_ordini`
  ADD CONSTRAINT `item_ordini_ibfk_1` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`),
  ADD CONSTRAINT `item_ordini_ibfk_2` FOREIGN KEY (`id_ordine`) REFERENCES `ordini` (`id`);

--
-- Limiti per la tabella `liste`
--
ALTER TABLE `liste`
  ADD CONSTRAINT `liste_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`);

--
-- Limiti per la tabella `liste_prodotti`
--
ALTER TABLE `liste_prodotti`
  ADD CONSTRAINT `liste_prodotti_ibfk_1` FOREIGN KEY (`id_lista`) REFERENCES `liste` (`id`);

--
-- Limiti per la tabella `ordini`
--
ALTER TABLE `ordini`
  ADD CONSTRAINT `ordini_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `ordini_ibfk_2` FOREIGN KEY (`id_corriere`) REFERENCES `corrieri` (`id`);

--
-- Limiti per la tabella `prodotti`
--
ALTER TABLE `prodotti`
  ADD CONSTRAINT `prodotti_ibfk_1` FOREIGN KEY (`id_venditore`) REFERENCES `venditori` (`id`),
  ADD CONSTRAINT `prodotti_ibfk_2` FOREIGN KEY (`id_venditore`) REFERENCES `venditori` (`id`);

--
-- Limiti per la tabella `recensioni_prodotti`
--
ALTER TABLE `recensioni_prodotti`
  ADD CONSTRAINT `recensioni_prodotti_ibfk_1` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`),
  ADD CONSTRAINT `recensioni_prodotti_ibfk_2` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`);

--
-- Limiti per la tabella `recensioni_venditori`
--
ALTER TABLE `recensioni_venditori`
  ADD CONSTRAINT `recensioni_venditori_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `recensioni_venditori_ibfk_2` FOREIGN KEY (`id_venditore`) REFERENCES `venditori` (`id`);

--
-- Limiti per la tabella `specifiche`
--
ALTER TABLE `specifiche`
  ADD CONSTRAINT `specifiche_ibfk_1` FOREIGN KEY (`id_caratteristica`) REFERENCES `caratteristiche` (`id`),
  ADD CONSTRAINT `specifiche_ibfk_2` FOREIGN KEY (`id_prodotto`) REFERENCES `prodotti` (`id`);

--
-- Limiti per la tabella `utenti_gruppi`
--
ALTER TABLE `utenti_gruppi`
  ADD CONSTRAINT `utenti_gruppi_ibfk_1` FOREIGN KEY (`id_utente`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `utenti_gruppi_ibfk_2` FOREIGN KEY (`id_gruppo`) REFERENCES `gruppi` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
