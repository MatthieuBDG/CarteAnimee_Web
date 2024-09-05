-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 05 sep. 2024 à 17:28
-- Version du serveur : 10.6.19-MariaDB
-- Version de PHP : 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mahq1168_PFE`
--

-- --------------------------------------------------------

--
-- Structure de la table `animations`
--

CREATE TABLE `animations` (
  `ID_Animation` int(11) NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Chemin_Gif_Reel` varchar(255) NOT NULL,
  `Chemin_Audio` varchar(255) NOT NULL,
  `Chemin_Gif_Fictif` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `api_usage`
--

CREATE TABLE `api_usage` (
  `ID_Api` int(11) NOT NULL,
  `Response_Json` varchar(500) NOT NULL,
  `Api_Url` varchar(255) NOT NULL,
  `Date` datetime NOT NULL
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
-- Structure de la table `avancement_series`
--

CREATE TABLE `avancement_series` (
  `ID_User` int(11) NOT NULL,
  `ID_Serie` int(11) NOT NULL,
  `Pourcentage` int(11) NOT NULL,
  `Derniere_Animation` int(11) NOT NULL
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
(1, 'Administrateur'),
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
(1, 'Admin', 'PFE', 'A@gmail.com', '$2y$10$h2GIkGL0La0aHsODYnjvAezKEyd/rjIAYTK1QlV1.9r.WygKSuQXu', 1);

-- --------------------------------------------------------

--
-- Structure de la table `users_liaison`
--

CREATE TABLE `users_liaison` (
  `ID` int(11) NOT NULL,
  `ID_User_Patient` int(11) NOT NULL,
  `ID_User_Docteur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `animations`
--
ALTER TABLE `animations`
  ADD PRIMARY KEY (`ID_Animation`);

--
-- Index pour la table `api_usage`
--
ALTER TABLE `api_usage`
  ADD PRIMARY KEY (`ID_Api`);

--
-- Index pour la table `autorisations_series`
--
ALTER TABLE `autorisations_series`
  ADD KEY `id_serie` (`ID_Serie`);

--
-- Index pour la table `avancement_series`
--
ALTER TABLE `avancement_series`
  ADD KEY `ID_User` (`ID_User`),
  ADD KEY `ID_Serie` (`ID_Serie`);

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
  ADD KEY `id_animation` (`ID_Animation`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_User`);

--
-- Index pour la table `users_liaison`
--
ALTER TABLE `users_liaison`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animations`
--
ALTER TABLE `animations`
  MODIFY `ID_Animation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `api_usage`
--
ALTER TABLE `api_usage`
  MODIFY `ID_Api` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `users_liaison`
--
ALTER TABLE `users_liaison`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

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
