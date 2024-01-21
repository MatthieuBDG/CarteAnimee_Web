-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 21 jan. 2024 à 17:01
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

-- --------------------------------------------------------

--
-- Structure de la table `animations`
--

CREATE TABLE `animations` (
  `ID_Animation` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Chemin_Gif` varchar(255) NOT NULL,
  `Chemin_Audio` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `autorisations_series`
--

CREATE TABLE `autorisations_series` (
  `ID_User` int(11) NOT NULL,
  `ID_Serie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `ID_Role` int(11) NOT NULL,
  `Nom` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`ID_Role`, `Nom`) VALUES
(1, 'Administrateur '),
(2, 'Docteur'),
(3, 'Enfant/Parent');

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE `series` (
  `ID_Serie` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `series_animations`
--

CREATE TABLE `series_animations` (
  `ID_Serie` int(11) NOT NULL,
  `ID_Animation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `ID_User` int(11) NOT NULL,
  `Prenom` varchar(20) NOT NULL,
  `Nom` varchar(50) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mdp` varchar(255) NOT NULL,
  `Role` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`ID_User`, `Prenom`, `Nom`, `Email`, `Mdp`, `Role`) VALUES
(7, 'Matthieu', 'Boubée de Gramont', 'matdegramont@gmail.com', '$2y$10$gfFZzki3jqIupG6AVvSKT.sBbPO4FVQqwfNqKo1wJe20FVV70P5Ym', 1),
(9, 'Lucas', 'Dallas Costa', 'lucas.dallascosta@gmail.com', '$2y$10$tZ.qeOtbylc69oMkbfQYUuiHb2Q7utdPCSwr81S1ls/Rvq3Zazbh2', 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animations`
--
ALTER TABLE `animations`
  ADD PRIMARY KEY (`ID_Animation`);

--
-- Index pour la table `autorisations_series`
--
ALTER TABLE `autorisations_series`
  ADD PRIMARY KEY (`ID_User`,`ID_Serie`),
  ADD KEY `id_serie` (`ID_Serie`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`ID_Role`);

--
-- Index pour la table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`ID_Serie`);

--
-- Index pour la table `series_animations`
--
ALTER TABLE `series_animations`
  ADD PRIMARY KEY (`ID_Serie`,`ID_Animation`),
  ADD KEY `id_animation` (`ID_Animation`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_User`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animations`
--
ALTER TABLE `animations`
  MODIFY `ID_Animation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `ID_Role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
  MODIFY `ID_Serie` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `autorisations_series`
--
ALTER TABLE `autorisations_series`
  ADD CONSTRAINT `autorisations_series_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `users` (`ID_User`),
  ADD CONSTRAINT `autorisations_series_ibfk_2` FOREIGN KEY (`ID_Serie`) REFERENCES `series` (`ID_Serie`);

--
-- Contraintes pour la table `series_animations`
--
ALTER TABLE `series_animations`
  ADD CONSTRAINT `series_animations_ibfk_1` FOREIGN KEY (`ID_Serie`) REFERENCES `series` (`ID_Serie`),
  ADD CONSTRAINT `series_animations_ibfk_2` FOREIGN KEY (`ID_Animation`) REFERENCES `animations` (`ID_Animation`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
