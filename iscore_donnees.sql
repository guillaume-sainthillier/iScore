-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 22 Janvier 2013 à 10:48
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `iscore`
--

--
-- Contenu de la table `competence`
--

INSERT INTO `competence` VALUES(1, 1, 8);
INSERT INTO `competence` VALUES(2, 2, 13);
INSERT INTO `competence` VALUES(3, 3, 12);

--
-- Contenu de la table `concert`
--

INSERT INTO `concert` VALUES(1, '2012-12-13', 'Premier concert ', 1, 0);

--
-- Contenu de la table `instrument`
--

INSERT INTO `instrument` VALUES(1, 'instrument à corde', 0);
INSERT INTO `instrument` VALUES(2, 'guitare', 1);
INSERT INTO `instrument` VALUES(3, 'guitare basse', 1);
INSERT INTO `instrument` VALUES(4, 'vent', 0);
INSERT INTO `instrument` VALUES(5, 'flûte de pan', 4);
INSERT INTO `instrument` VALUES(6, 'flûte à bec', 4);
INSERT INTO `instrument` VALUES(7, 'flûte à bec', 4);
INSERT INTO `instrument` VALUES(8, 'flûte traversière', 4);
INSERT INTO `instrument` VALUES(10, 'instruments à percussion', 0);
INSERT INTO `instrument` VALUES(12, 'batterie', 10);
INSERT INTO `instrument` VALUES(13, 'djembé', 10);
INSERT INTO `instrument` VALUES(14, 'instrument à clavier', 0);
INSERT INTO `instrument` VALUES(15, 'piano', 14);
INSERT INTO `instrument` VALUES(16, 'organ', 14);
INSERT INTO `instrument` VALUES(17, 'violon', 1);
INSERT INTO `instrument` VALUES(18, 'harpe', 1);
INSERT INTO `instrument` VALUES(19, 'violoncelle', 1);
INSERT INTO `instrument` VALUES(20, 'contrebasse', 1);
INSERT INTO `instrument` VALUES(21, 'triangle', 10);

--
-- Contenu de la table `user`
--

INSERT INTO `user` VALUES(1, 'Démo', 'DEMO', 'demo', 5, 'demo');

--
-- Contenu de la table `userinstrumentconcert`
--

INSERT INTO `userinstrumentconcert` VALUES(1, 1, 22, 0, 1, 0, 3, '1', '');
INSERT INTO `userinstrumentconcert` VALUES(2, 2, 9, 1, 1, 0, 2, '2', '');
INSERT INTO `userinstrumentconcert` VALUES(3, 10, 15, 1, 1, 0, 1, '2', '');
INSERT INTO `userinstrumentconcert` VALUES(4, 16, 21, 1, 1, 0, 5, '2', '');
INSERT INTO `userinstrumentconcert` VALUES(5, 3, 4, 2, 1, 2, 6, '3', '');
INSERT INTO `userinstrumentconcert` VALUES(6, 6, 7, 2, 1, 17, 7, '3', '');
INSERT INTO `userinstrumentconcert` VALUES(7, 7, 8, 2, 1, 17, 0, '3', '');
INSERT INTO `userinstrumentconcert` VALUES(8, 11, 12, 3, 1, 2, 0, '3', '');
INSERT INTO `userinstrumentconcert` VALUES(9, 13, 14, 3, 1, 2, 0, '3', '');
INSERT INTO `userinstrumentconcert` VALUES(10, 17, 18, 4, 1, 20, 0, '3', '');
INSERT INTO `userinstrumentconcert` VALUES(11, 19, 20, 4, 1, 21, 0, '3', '');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
