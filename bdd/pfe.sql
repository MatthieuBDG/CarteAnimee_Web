-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 28 jan. 2024 à 22:10
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

--
-- Déchargement des données de la table `animations`
--

INSERT INTO `animations` (`ID_Animation`, `Nom`, `Chemin_Gif`, `Chemin_Audio`) VALUES
(1, 'Pikachu', 'assets/img/Td9n.gif', 'assets/music/sounds_sounds_pikachu.mp3'),
(2, 'Cat', 'assets/img/nyan-cat-gif-1.gif', 'assets/music/ANMLCat_Miaulement chat 2 (ID 1890)_LS.mp3'),
(3, 'Pluie', 'assets/img/JTCJ.gif', 'assets/music/RAINWatr_Pluie sur flaques d eau (ID 1290)_LS.mp3');

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

--
-- Déchargement des données de la table `api_usage`
--

INSERT INTO `api_usage` (`ID_Api`, `Response_Json`, `Api_Url`, `Date`) VALUES
(3, '{\"success\":false,\"error_msg\":\"Mot de passe incorrect. Veuillez r\\u00e9essayer\"}', '/PFE/api/connectaccount.php?email=matdegramont@gma', '2024-01-28 22:05:55'),
(4, '{\"success\":false,\"error_msg\":\"Mot de passe incorrect. Veuillez r\\u00e9essayer\"}', '/PFE/api/connectaccount.php', '2024-01-28 22:07:39'),
(5, '{\"success\":true,\"user\":{\"ID_User\":\"7\",\"Prenom\":\"Matthieu\",\"Nom\":\"Boub\\u00e9e de Gramont\",\"Email\":\"matdegramont@gmail.com\",\"ID_Role\":\"3\",\"Role\":\"Enfant\\/Parent\"}}', '/PFE/api/connectaccount.php', '2024-01-28 22:08:20'),
(6, '{\"success\":true,\"series\":[{\"ID_Serie\":\"1\",\"Nom\":\"Meteo\"},{\"ID_Serie\":\"2\",\"Nom\":\"Animaux\"}]}', '/PFE/api/recupseries.php', '2024-01-28 22:08:51');

-- --------------------------------------------------------

--
-- Structure de la table `autorisations_series`
--

CREATE TABLE `autorisations_series` (
  `ID_User` int(11) NOT NULL,
  `ID_Serie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `autorisations_series`
--

INSERT INTO `autorisations_series` (`ID_User`, `ID_Serie`) VALUES
(7, 1),
(7, 2),
(9, 1),
(9, 3),
(7, 3);

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

--
-- Déchargement des données de la table `series`
--

INSERT INTO `series` (`ID_Serie`, `Nom`) VALUES
(1, 'Meteo'),
(2, 'Animaux');

-- --------------------------------------------------------

--
-- Structure de la table `series_animations`
--

CREATE TABLE `series_animations` (
  `ID_Serie` int(11) NOT NULL,
  `ID_Animation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `series_animations`
--

INSERT INTO `series_animations` (`ID_Serie`, `ID_Animation`) VALUES
(2, 1),
(2, 2),
(1, 3);

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
(7, 'Matthieu', 'Boubee de Gramont', 'matdegramont@gmail.com', '$2y$10$gfFZzki3jqIupG6AVvSKT.sBbPO4FVQqwfNqKo1wJe20FVV70P5Ym', 3),
(9, 'Lucas', 'Dallas Costa', 'lucas.dallascosta@gmail.com', '$2y$10$tZ.qeOtbylc69oMkbfQYUuiHb2Q7utdPCSwr81S1ls/Rvq3Zazbh2', 3),
(10, 'Matthieu', 'Boubee de Gramont', 'mattdegramont@gmail.com', '$2y$10$gfFZzki3jqIupG6AVvSKT.sBbPO4FVQqwfNqKo1wJe20FVV70P5Ym', 2);

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
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `animations`
--
ALTER TABLE `animations`
  MODIFY `ID_Animation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `api_usage`
--
ALTER TABLE `api_usage`
  MODIFY `ID_Api` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `ID_Role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
  MODIFY `ID_Serie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `ID_User` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
