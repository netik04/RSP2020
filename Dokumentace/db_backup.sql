-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 01, 2020 at 07:15 PM
-- Server version: 8.0.21
-- PHP Version: 7.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `studaci`
--

-- --------------------------------------------------------

--
-- Table structure for table `casopis`
--

CREATE TABLE `casopis` (
  `id_casopisu` int UNSIGNED NOT NULL,
  `datum_uzaverky` date NOT NULL,
  `tema` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `kapacita` tinyint UNSIGNED NOT NULL,
  `zobrazit` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `casopis`
--

INSERT INTO `casopis` (`id_casopisu`, `datum_uzaverky`, `tema`, `kapacita`, `zobrazit`) VALUES
(1, '2020-11-30', 'Operační systémy, Hardware, Switche', 10, 1),
(2, '2021-01-01', 'Ošetřovatelství, porodní asistence a další zdravotnické obory ', 8, 0),
(4, '2020-12-03', 'ekonomika&comma; management&comma; marketing&comma; statistika&comma; opera&ccaron;n&iacute; v&yacute;zkum&comma; finan&ccaron;n&iacute; matematika&comma; poji&scaron;&tcaron;ovnistv&iacute;&comma; cestovn&iacute; ruch&comma; region&aacute;ln&iacute; rozvoj&comma; ve&rcaron;ejn&aacute; spr&aacute;va', 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `clanek`
--

CREATE TABLE `clanek` (
  `id_clanku` int UNSIGNED NOT NULL,
  `nazev` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `id_casopisu` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `clanek`
--

INSERT INTO `clanek` (`id_clanku`, `nazev`, `id_casopisu`) VALUES
(1, 'Jak CISCO dominuje trhu switchů', 1),
(2, 'Téměř polovina lidí používá nepodporované operační systémy', 1),
(4, 'Problematika porodní asistence v komunitní péči o ženu a dítě v ČR', 2),
(5, 'Technick&eacute; vzorce', 4);

-- --------------------------------------------------------

--
-- Table structure for table `helpdesk`
--

CREATE TABLE `helpdesk` (
  `id` int UNSIGNED NOT NULL,
  `zprava` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `login` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `id_otazky` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `helpdesk`
--

INSERT INTO `helpdesk` (`id`, `zprava`, `login`, `id_otazky`) VALUES
(1, 'Dobrý den,\r\nproč je tu funkce psaní zpráv? Dostávám absolutně zbytečné zprávy.', 'votypka', NULL),
(2, 'Dobrý den,\r\nzde vám nikdo nepomůže.', 'skocdopol', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pise`
--

CREATE TABLE `pise` (
  `login` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `id_clanku` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pise`
--

INSERT INTO `pise` (`login`, `id_clanku`) VALUES
('Uzivatel', 1),
('votypka', 1),
('votypka', 2),
('Kubista', 4),
('Uzivatel', 4),
('franta007', 5);

-- --------------------------------------------------------

--
-- Table structure for table `posudek`
--

CREATE TABLE `posudek` (
  `id_clanku` int UNSIGNED NOT NULL,
  `verze` int UNSIGNED NOT NULL,
  `akt_zaj_prin` tinyint UNSIGNED DEFAULT NULL,
  `jazyk_styl_prinos` tinyint UNSIGNED DEFAULT NULL,
  `originalita` tinyint UNSIGNED DEFAULT NULL,
  `odbor_uroven` tinyint UNSIGNED DEFAULT NULL,
  `otevrena_odpoved` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `datum_vytvoreni` datetime DEFAULT NULL,
  `datum_uzaverky` datetime NOT NULL,
  `osobni_revize` tinyint(1) NOT NULL DEFAULT '0',
  `vyjadreni_autora` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `login_recenzenta` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `posudek`
--

INSERT INTO `posudek` (`id_clanku`, `verze`, `akt_zaj_prin`, `jazyk_styl_prinos`, `originalita`, `odbor_uroven`, `otevrena_odpoved`, `datum_vytvoreni`, `datum_uzaverky`, `osobni_revize`, `vyjadreni_autora`, `login_recenzenta`) VALUES
(1, 1, 2, 1, 4, 5, 'Zaj&iacute;mav&yacute; &ccaron;l&aacute;nek ale ned&aacute; se &ccaron;&iacute;st&period; P&rcaron;epracujte si to&period;', '2020-12-01 17:15:26', '2020-12-22 00:00:00', 0, NULL, 'recenzent2'),
(1, 1, 1, 2, 4, 5, 'Zaj&iacute;mav&eacute; ale pusob&iacute; to jako by ten &ccaron;l&aacute;nek psalo d&iacute;t&ecaron;&period; Po p&rcaron;epracovan&iacute;&comma; navrhuji p&rcaron;ijmout&period;', '2020-12-01 17:12:06', '2020-12-22 00:00:00', 1, NULL, 'voprsal'),
(1, 2, 1, 2, 2, 1, 'U&zcaron; je to dobr&eacute;&period; P&rcaron;ijmout', '2020-12-01 17:29:03', '2020-12-08 00:00:00', 0, NULL, 'voprsal'),
(2, 1, 2, 2, 2, 1, 'Navrhuji p&rcaron;ijmout&period; &colon;&rpar;', '2020-12-01 17:17:42', '2020-12-16 00:00:00', 0, NULL, 'Macek69'),
(2, 1, 1, 2, 3, 2, 'Dobr&yacute; &ccaron;l&aacute;nek navrhuji p&rcaron;ijmout&period;', '2020-12-01 17:16:27', '2020-12-16 00:00:00', 0, NULL, 'recenzent2'),
(4, 1, 4, 4, 3, 2, 'Nehod&iacute; se&period; Zam&iacute;tnout', '2020-12-01 17:18:18', '2020-12-08 00:00:00', 0, NULL, 'Macek69'),
(4, 1, 4, 5, 3, 1, 'Nehod&iacute; se do tohoto &ccaron;&iacute;sla&period; Navrhuji zam&iacute;tnout&period; M&uring;&zcaron;ete to zkusit znovu v jin&eacute;m &ccaron;&iacute;sle &ccaron;asopisu&period;', '2020-12-01 17:13:05', '2020-12-08 00:00:00', 0, NULL, 'voprsal'),
(5, 1, 3, 1, 2, 4, 'Nen&iacute; to &scaron;patn&yacute;&comma; ale tyhle vzorce u&zcaron; nechci nikdy vid&ecaron;t', '2020-12-01 19:07:39', '2021-01-07 00:00:00', 0, NULL, 'Macek69'),
(5, 1, 1, 2, 2, 1, 'Dobr&yacute; vzorce&period; Schvalte to &scaron;&eacute;fe &semi;&rpar;', '2020-12-01 19:07:46', '2021-01-07 00:00:00', 0, NULL, 'recenzent2');

-- --------------------------------------------------------

--
-- Table structure for table `uzivatel`
--

CREATE TABLE `uzivatel` (
  `login` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `heslo` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `role` enum('administrator','sefredaktor','redaktor','recenzent','autor') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `jmeno` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `prijmeni` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `telefon` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `uzivatel`
--

INSERT INTO `uzivatel` (`login`, `heslo`, `role`, `jmeno`, `prijmeni`, `email`, `telefon`) VALUES
('admin', '18e9b032272e015b09a58f02661b1cc86ca315439bfbd31cafae6d4e11b2bf35', 'administrator', 'Jakub', 'Z&iacute;ka', 'zika02&commat;student&period;vspj&period;cz', '7777'),
('DarthSidious', 'cb343389a5ed6cac3d20dd327fb63cf9075fa3962421cba18ac24a87fd8856eb', 'sefredaktor', 'Sheev', 'Palpatine', 'sidious&commat;dark&period;side', '777777777'),
('franta007', 'd6bf4ba24a85a82ffd639e6d7d54894101facdbc6efedbc4765f5b6d8b7ac884', 'autor', 'Franta', '&Ccaron;ern&yacute;', 'fc@cz.cz', ''),
('Kubista', '7283fbe1a31850ff1ff7a3522c61a5fe6a931661a85df17318d79372814b387a', 'autor', 'Jakub', 'Pojer', 'kubista&commat;neumi&period;kodovat', '666 666 666'),
('Macek69', '57837d552b12c8655ef1ee2a418b0ff9715d77ad6078083d964da93fc9dd7a67', 'recenzent', 'Milan', 'Macek', 'jeToFaktMacek@gmail.com', '696969696969'),
('recenzent2', 'ae8aad51ff276deed12c7354e6d2f1eef244c2859c439cdc9264bf525929ced3', 'recenzent', 'John', 'Deer', 'doe@something.com', ''),
('skocdopol', 'e8b44c3a2d578759163dd1a25f5615d6fe5c1c8300eccbdee208eb0678aa50ac', 'redaktor', 'Karel', 'Sko&ccaron;dopolov&aacute;', 'pole&commat;email&period;cz', '&plus;420563459785'),
('Uzivatel', '2193B69A8CC613C898B58C43FA046929E81B08D97ECD21E1F1E52471BE60168A', 'autor', 'Uzi', 'Vatelov&aacute;', 'Uzivatelova&commat;uzi&period;vatel', '000 000 000'),
('voprsal', 'fcf88f2229d36b194139da4564c2b9b9395ec23f13ad7ee7d1233facf3231826', 'recenzent', 'Borisa', 'Vopr&scaron;&aacute;lek', 'boris69&commat;rusmail&period;com', '&plus;420999310420'),
('votypka', 'DF97EAAD183F6C6BE38B5CBAC0BB355F7358D9E3A0D0BDC0134BC694F37748BF', 'autor', 'Karel', 'Vot&yacute;pka', 'votyp420&commat;nejakyemail&period;com', '&plus;420456789123'),
('[deleted]', '0', NULL, 'Smazaný', 'Uživatel', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `verze`
--

CREATE TABLE `verze` (
  `id_clanku` int UNSIGNED NOT NULL,
  `verze` int UNSIGNED NOT NULL,
  `stav_autor` enum('Podáno','Přijato redakcí','Předáno recenzentům','Posudky doručeny','Vráceno k úpravě','Schváleno','Zamítnuto') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `stav_redaktor` enum('Nově podaný','Čeká na stanovení recenzentů','Probíhá recenzní řízení','1. posudek doručen redakci','2. posudek doručen redakci','Posudky odeslány autorovi','Probíhá úprava textu autorem','Příspěvek je přijat k vydání','Příspěvek zamítnut','Existuje nová verze') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `datum` date NOT NULL,
  `cesta` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sefredaktor` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `verze`
--

INSERT INTO `verze` (`id_clanku`, `verze`, `stav_autor`, `stav_redaktor`, `datum`, `cesta`, `sefredaktor`) VALUES
(1, 1, 'Vráceno k úpravě', 'Existuje nová verze', '2020-10-25', 'clanky/cisco-switche.pdf', 0),
(1, 2, 'Schváleno', 'Příspěvek je přijat k vydání', '2020-12-01', 'clanky/cisco-switche_v2.pdf', 0),
(2, 1, 'Schváleno', 'Příspěvek je přijat k vydání', '2020-10-27', 'clanky/nepodporovane_OS.pdf', 0),
(4, 1, 'Zamítnuto', 'Příspěvek zamítnut', '2020-11-09', 'clanky/Problematika_PA_ v_komunitni_peci.pdf', 1),
(5, 1, 'Schváleno', 'Příspěvek je přijat k vydání', '2020-12-01', 'clanky/vzorce TECHNICI_Nepopsane.pdf', 0);

-- --------------------------------------------------------

--
-- Table structure for table `zprava`
--

CREATE TABLE `zprava` (
  `id_clanku` int UNSIGNED NOT NULL,
  `verze` int UNSIGNED NOT NULL,
  `datum_cas` datetime NOT NULL,
  `text_zpravy` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `interni` tinyint(1) NOT NULL,
  `login` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `duvod` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `zprava`
--

INSERT INTO `zprava` (`id_clanku`, `verze`, `datum_cas`, `text_zpravy`, `interni`, `login`, `duvod`) VALUES
(1, 1, '2020-10-27 15:28:29', 'Zdravím redaktore,\r\nsmažte Votýpku, neumí psát články\r\n-V', 1, 'voprsal', 0),
(1, 1, '2020-10-27 15:30:24', 'Zdravím redaktore,\r\nodebral bych Vopršálkovi recenzentní práva, neví co dělá.\r\n-V', 0, 'votypka', 0),
(1, 1, '2020-10-27 15:33:42', 'Zdravím, \r\nnikoho mazat nebudu, je to jediný autor, kterého máme.\r\n-S', 1, 'skocdopol', 0),
(1, 1, '2020-11-08 19:36:53', 'Souhlasím, bohužel nemáme dostatek recenzentů. -S', 0, 'skocdopol', 0),
(1, 1, '2020-11-08 19:37:00', 'Dobrý den, jak probíhá řízení článku?', 0, 'votypka', 0),
(1, 1, '2020-12-01 18:18:36', 'Posudky odeslány', 0, 'skocdopol', 3),
(1, 1, '2020-12-01 18:19:47', 'Váš článek nedosahuje kvalit našeho časopisu. přepracujte si to. LOL', 0, 'skocdopol', 1),
(1, 2, '2020-12-01 18:29:18', 'Posudky odeslány', 0, 'skocdopol', 3),
(1, 2, '2020-12-01 18:29:20', 'Článek schválen! Děkujeme za váš příspěvek.', 0, 'skocdopol', 4),
(1, 2, '2020-12-01 19:00:21', 'Dobr&yacute; den&comma; do&scaron;el m&uring;j posudek v po&rcaron;&aacute;dku&quest;', 1, 'voprsal', 0),
(1, 2, '2020-12-01 19:08:39', 'Ano do&scaron;el&period; Dobr&aacute; pr&aacute;ce', 1, 'skocdopol', 0),
(2, 1, '2020-12-01 18:35:12', 'Posudky odeslány', 0, 'skocdopol', 3),
(2, 1, '2020-12-01 18:46:06', 'Dobr&eacute; to je &colon;D', 0, 'skocdopol', 0),
(2, 1, '2020-12-01 19:02:50', 'Na 1&period; pokus&quest; NICE', 1, 'DarthSidious', 0),
(2, 1, '2020-12-01 19:03:06', 'YOP byla to dobr&aacute; pr&aacute;ce &colon;D', 1, 'skocdopol', 0),
(2, 1, '2020-12-01 19:05:12', 'Článek schválen! Děkujeme za váš příspěvek.', 0, 'skocdopol', 4),
(4, 1, '2020-12-01 18:21:08', 'Posudky odeslány', 0, 'skocdopol', 3),
(4, 1, '2020-12-01 18:22:48', 'Jak napsali recenzenti, Váš článek se nehodí do tohoto čísla. Zkuste to jindy.', 0, 'skocdopol', 2),
(5, 1, '2020-12-01 20:08:30', 'Posudky odeslány', 0, 'skocdopol', 3),
(5, 1, '2020-12-01 20:10:22', 'Článek schválen! Děkujeme za váš příspěvek.', 0, 'skocdopol', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `casopis`
--
ALTER TABLE `casopis`
  ADD PRIMARY KEY (`id_casopisu`);

--
-- Indexes for table `clanek`
--
ALTER TABLE `clanek`
  ADD PRIMARY KEY (`id_clanku`) USING BTREE,
  ADD KEY `odeslat_do_casopisu` (`id_casopisu`);

--
-- Indexes for table `helpdesk`
--
ALTER TABLE `helpdesk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uzivatel-helpdesk` (`login`),
  ADD KEY `otazka-odpoved` (`id_otazky`);

--
-- Indexes for table `pise`
--
ALTER TABLE `pise`
  ADD PRIMARY KEY (`login`,`id_clanku`),
  ADD KEY `pise_clanek` (`id_clanku`);

--
-- Indexes for table `posudek`
--
ALTER TABLE `posudek`
  ADD PRIMARY KEY (`id_clanku`,`verze`,`login_recenzenta`),
  ADD KEY `recenzent` (`login_recenzenta`);

--
-- Indexes for table `uzivatel`
--
ALTER TABLE `uzivatel`
  ADD PRIMARY KEY (`login`);

--
-- Indexes for table `verze`
--
ALTER TABLE `verze`
  ADD PRIMARY KEY (`id_clanku`,`verze`),
  ADD UNIQUE KEY `cesta` (`cesta`);

--
-- Indexes for table `zprava`
--
ALTER TABLE `zprava`
  ADD PRIMARY KEY (`id_clanku`,`verze`,`datum_cas`),
  ADD KEY `odesilatel_zpravy` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `casopis`
--
ALTER TABLE `casopis`
  MODIFY `id_casopisu` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `clanek`
--
ALTER TABLE `clanek`
  MODIFY `id_clanku` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `helpdesk`
--
ALTER TABLE `helpdesk`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clanek`
--
ALTER TABLE `clanek`
  ADD CONSTRAINT `odeslat_do_casopisu` FOREIGN KEY (`id_casopisu`) REFERENCES `casopis` (`id_casopisu`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `helpdesk`
--
ALTER TABLE `helpdesk`
  ADD CONSTRAINT `otazka-odpoved` FOREIGN KEY (`id_otazky`) REFERENCES `helpdesk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uzivatel-helpdesk` FOREIGN KEY (`login`) REFERENCES `uzivatel` (`login`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pise`
--
ALTER TABLE `pise`
  ADD CONSTRAINT `pise_clanek` FOREIGN KEY (`id_clanku`) REFERENCES `clanek` (`id_clanku`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `uzivatel_pise` FOREIGN KEY (`login`) REFERENCES `uzivatel` (`login`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `posudek`
--
ALTER TABLE `posudek`
  ADD CONSTRAINT `prirazeni_clanku` FOREIGN KEY (`id_clanku`,`verze`) REFERENCES `verze` (`id_clanku`, `verze`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recenzent` FOREIGN KEY (`login_recenzenta`) REFERENCES `uzivatel` (`login`) ON UPDATE CASCADE;

--
-- Constraints for table `verze`
--
ALTER TABLE `verze`
  ADD CONSTRAINT `verze_clanku` FOREIGN KEY (`id_clanku`) REFERENCES `clanek` (`id_clanku`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `zprava`
--
ALTER TABLE `zprava`
  ADD CONSTRAINT `odesilatel_zpravy` FOREIGN KEY (`login`) REFERENCES `uzivatel` (`login`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `prirazeno_clanku` FOREIGN KEY (`id_clanku`,`verze`) REFERENCES `verze` (`id_clanku`, `verze`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
