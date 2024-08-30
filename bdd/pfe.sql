-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 30 août 2024 à 09:16
-- Version du serveur : 10.4.27-MariaDB
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pfe`
--
CREATE DATABASE IF NOT EXISTS `pfe` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `pfe`;

-- --------------------------------------------------------

--
-- Structure de la table `animations`
--

CREATE TABLE IF NOT EXISTS `animations` (
  `ID_Animation` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(100) NOT NULL,
  `Chemin_Gif_Reel` varchar(255) NOT NULL,
  `Chemin_Audio` varchar(255) NOT NULL,
  `Chemin_Gif_Fictif` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_Animation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `api_usage`
--

CREATE TABLE IF NOT EXISTS `api_usage` (
  `ID_Api` int(11) NOT NULL AUTO_INCREMENT,
  `Response_Json` varchar(500) NOT NULL,
  `Api_Url` varchar(255) NOT NULL,
  `Date` datetime NOT NULL,
  PRIMARY KEY (`ID_Api`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `autorisations_series`
--

CREATE TABLE IF NOT EXISTS `autorisations_series` (
  `ID_User` int(11) NOT NULL,
  `ID_Serie` int(11) NOT NULL,
  KEY `id_serie` (`ID_Serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avancement_series`
--

CREATE TABLE IF NOT EXISTS `avancement_series` (
  `ID_User` int(11) NOT NULL,
  `ID_Serie` int(11) NOT NULL,
  `Pourcentage` int(11) NOT NULL,
  `Derniere_Animation` int(11) NOT NULL,
  KEY `ID_User` (`ID_User`),
  KEY `ID_Serie` (`ID_Serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `ID_Role` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(20) NOT NULL,
  PRIMARY KEY (`ID_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE IF NOT EXISTS `series` (
  `ID_Serie` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_Serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `series_animations`
--

CREATE TABLE IF NOT EXISTS `series_animations` (
  `ID_Serie` int(11) NOT NULL,
  `ID_Animation` int(11) NOT NULL,
  KEY `id_animation` (`ID_Animation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID_User` int(11) NOT NULL AUTO_INCREMENT,
  `Prenom` varchar(20) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mdp` varchar(255) NOT NULL,
  `Role` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_User`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users_liaison`
--

CREATE TABLE IF NOT EXISTS `users_liaison` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ID_User_Patient` int(11) NOT NULL,
  `ID_User_Docteur` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Contraintes pour les tables déchargées
--
--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`ID_User`, `Prenom`, `Nom`, `Email`, `Mdp`, `Role`) VALUES
(1, 'Zeus', 'le dieu', 'A@gmail.com', '$2y$10$h2GIkGL0La0aHsODYnjvAezKEyd/rjIAYTK1QlV1.9r.WygKSuQXu', 1);
--
-- Contraintes pour la table `avancement_series`
--
ALTER TABLE `avancement_series`
  ADD CONSTRAINT `avancement_series_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`),
  ADD CONSTRAINT `avancement_series_ibfk_2` FOREIGN KEY (`ID_Serie`) REFERENCES `series` (`ID_Serie`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
