-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 22 Décembre 2017 à 15:30
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `gsb_frais`
--

-- --------------------------------------------------------

--
-- Structure de la table `fraisforfait`
--

CREATE TABLE IF NOT EXISTS `fraisforfait` (
  `id` char(3) NOT NULL,
  `libelle` varchar(20) DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `etatfichefrais`
--

CREATE TABLE IF NOT EXISTS `etatfichefrais` (
  `id` char(2) NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `etatfraishorsforfait`
--

CREATE TABLE IF NOT EXISTS `etatfraishorsforfait` (
  `id` char(2) NOT NULL,
  `libelle` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `profil`
--

CREATE TABLE IF NOT EXISTS `profil` (
  `id` char(3) NOT NULL,
  `libelle` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` char(4) NOT NULL,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(30) DEFAULT NULL,
  `login` varchar(31) DEFAULT NULL,
  `mdp` char(60) DEFAULT NULL,
  `adresse` varchar(30) DEFAULT NULL,
  `cp` char(5) DEFAULT NULL,
  `ville` varchar(30) DEFAULT NULL,
  `dateEmbauche` date DEFAULT NULL,
  `idProfil` char(3) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idProfil`) REFERENCES `profil` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fichefrais`
--

CREATE TABLE IF NOT EXISTS `fichefrais` (
  `idUtilisateur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `nbJustificatifs` int(2) DEFAULT NULL,
  `montantValide` decimal(10,2) DEFAULT NULL,
  `dateModif` date DEFAULT NULL,
  `motifRefus` varchar(180) DEFAULT NULL,
  `idEtat` char(2) DEFAULT 'CR',
  PRIMARY KEY (`idUtilisateur`,`mois`),
  FOREIGN KEY (`idEtat`) REFERENCES `etatfichefrais` (`id`),
  FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `lignefraisforfait`
--

CREATE TABLE IF NOT EXISTS `lignefraisforfait` (
  `idUtilisateur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `idFraisForfait` char(3) NOT NULL,
  `quantite` int(3) DEFAULT NULL,
  `montantApplique` decimal(5,2) NOT NULL,
  PRIMARY KEY (`idUtilisateur`,`mois`,`idFraisForfait`),
  FOREIGN KEY (`idUtilisateur`, `mois`) REFERENCES `fichefrais` (`idUtilisateur`, `mois`),
  FOREIGN KEY (`idFraisForfait`) REFERENCES `fraisforfait` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `lignefraishorsforfait`
--

CREATE TABLE IF NOT EXISTS `lignefraishorsforfait` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUtilisateur` char(4) NOT NULL,
  `mois` char(6) NOT NULL,
  `libelle` varchar(35) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `montant` decimal(5,2) DEFAULT NULL,
  `justificatifNom` varchar(35) DEFAULT NULL,
  `justificatifFichier` char(17) DEFAULT NULL,
  `idEtat` char(2) DEFAULT 'EA',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`idUtilisateur`, `mois`) REFERENCES `fichefrais` (`idUtilisateur`, `mois`),
  FOREIGN KEY (`idEtat`) REFERENCES `etatfraishorsforfait` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;