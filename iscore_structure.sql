-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 22 Janvier 2013 à 10:45
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `iscore`
--

-- --------------------------------------------------------

--
-- Structure de la table `competence`
--

CREATE TABLE IF NOT EXISTS `competence` (
  `competence` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `instrument` int(11) NOT NULL,
  PRIMARY KEY (`competence`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `concert`
--

CREATE TABLE IF NOT EXISTS `concert` (
  `concert` int(10) NOT NULL AUTO_INCREMENT,
  `dateConcert` date NOT NULL DEFAULT '0000-00-00',
  `nom` varchar(255) CHARACTER SET latin1 NOT NULL,
  `racineUserInstrumentConcert` int(10) NOT NULL,
  `isTemplate` tinyint(1) NOT NULL,
  PRIMARY KEY (`concert`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `instrument`
--

CREATE TABLE IF NOT EXISTS `instrument` (
  `instrument` int(10) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET latin1 NOT NULL,
  `categorie` int(10) NOT NULL,
  PRIMARY KEY (`instrument`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user` int(10) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) CHARACTER SET latin1 NOT NULL,
  `nom` varchar(255) CHARACTER SET latin1 NOT NULL,
  `prenom` varchar(255) CHARACTER SET latin1 NOT NULL,
  `rangAdmin` int(1) NOT NULL DEFAULT '0',
  `password` varchar(10) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Structure de la table `userinstrumentconcert`
--

CREATE TABLE IF NOT EXISTS `userinstrumentconcert` (
  `userInstrumentConcert` int(10) NOT NULL AUTO_INCREMENT,
  `lft` int(10) NOT NULL,
  `rght` int(10) NOT NULL,
  `parent_id` int(10) NOT NULL,
  `concert_id` int(10) NOT NULL,
  `instrument` int(10) NOT NULL,
  `user` int(10) NOT NULL,
  `role` enum('1','2','3','4','5') CHARACTER SET latin1 NOT NULL DEFAULT '1',
  `nom` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`userInstrumentConcert`),
  KEY `concert_id` (`concert_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=12 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `userinstrumentconcert`
--
ALTER TABLE `userinstrumentconcert`
  ADD CONSTRAINT `userinstrumentconcert_ibfk_1` FOREIGN KEY (`concert_id`) REFERENCES `concert` (`concert`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
