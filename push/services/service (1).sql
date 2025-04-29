-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 28 avr. 2025 à 16:29
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `service`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL,
  `nom_categorie` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id_categorie`, `nom_categorie`, `description`) VALUES
(1, 'dddd', 'vvvvvvvvvvvvvvvv');

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id_service` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `eco_friendly` tinyint(1) DEFAULT NULL,
  `id_categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id_service`, `service_name`, `service_description`, `price`, `eco_friendly`, `id_categorie`) VALUES
(1, 'zzz', 'hhhhh', 3000.00, 0, 1),
(2, 'vvvv', '         ddddd', 3000.00, 1, 1),
(3, 'dddddddd', 'ccccc', 3333.00, 0, 1),
(4, 'sssssss', 'ccccc', 2020.00, 1, 1),
(5, 'zzzzzz', 'eeeeeeeeeeeee', 1000.00, 0, 1),
(6, 'ppzzzz', 'vvvvvvvv', 1000.00, 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `user_service_recommendation`
--

CREATE TABLE `user_service_recommendation` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_service` int(11) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_service_recommendation`
--

INSERT INTO `user_service_recommendation` (`id`, `id_user`, `id_service`, `discounted_price`) VALUES
(1, 1, 1, NULL),
(2, 1, 2, NULL),
(3, 1, 4, NULL),
(4, 1, 3, NULL),
(5, 1, 5, 900.00),
(6, 1, 6, 900.00);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id_service`),
  ADD KEY `fk_services_categorie` (`id_categorie`);

--
-- Index pour la table `user_service_recommendation`
--
ALTER TABLE `user_service_recommendation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usr_srv_service` (`id_service`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id_categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id_service` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user_service_recommendation`
--
ALTER TABLE `user_service_recommendation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_services_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user_service_recommendation`
--
ALTER TABLE `user_service_recommendation`
  ADD CONSTRAINT `fk_usr_srv_service` FOREIGN KEY (`id_service`) REFERENCES `services` (`id_service`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
