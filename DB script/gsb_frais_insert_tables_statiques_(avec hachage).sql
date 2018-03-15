--
-- Contenu de la table `fraisforfait`
--

INSERT INTO `fraisforfait` (`id`, `libelle`, `montant`) VALUES
('ETP', 'Forfait Etape', '110.00'),
('KM', 'Frais Kilométrique', '0.62'),
('NUI', 'Nuitée Hôtel', '80.00'),
('REP', 'Repas Restaurant', '25.00');

-- --------------------------------------------------------

--
-- Contenu de la table `etatfichefrais`
--

INSERT INTO `etatfichefrais` (`id`, `libelle`) VALUES
('CR', 'Fiche créée, saisie en cours'),
('CL', 'Fiche signée, saisie clôturée'),
('VA', 'Validée et mise en paiement'),
('RB', 'Fiche remboursée'),
('RE', 'Fiche refusée'),
('IN', 'Invalide, 12 mois écoulés');

-- --------------------------------------------------------

--
-- Contenu de la table `etatfraishorsforfait`
--

INSERT INTO `etatfraishorsforfait` (`id`, `libelle`) VALUES
('EA', 'En attente'),
('VA', 'Validé'),
('RE', 'Refusé');

-- --------------------------------------------------------

--
-- Contenu de la table `profil`
--

INSERT INTO `profil` (`id`, `libelle`) VALUES
('VIS', 'Visiteur'),
('COM', 'Comptable'),
('ADM', 'Admin');

-- --------------------------------------------------------

--
-- Contenu de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `login`, `mdp`, `adresse`, `cp`, `ville`, `dateEmbauche`, `idProfil`) VALUES
('a131', 'Villachane', 'Louis', 'lvillachane', '$2y$10$3RCMTkRx2uj1BJyePHePu.SDYgcRTg9PCtAMkTmfeYtpoza76iFru', '8 rue des Charmes', '46000', 'Cahors', '2005-12-21', 'COM'),
('a17', 'Andre', 'David', 'dandre', '$2y$10$glLoaZY68NphaF2T.YQueeL0SGIleO1jw.GHaTHI3eylR1zz4JEoG', '1 rue Petit', '46200', 'Lalbenque', '1998-11-23', 'COM'),
('a55', 'Bedos', 'Christian', 'cbedos', '$2y$10$MRo/4Hhp7fGvDBfWtmh4q.zSKsagx.bqyHorUqN76/y35FXAUm21i', '1 rue Peranud', '46250', 'Montcuq', '1995-01-12', 'COM'),
('a93', 'Tusseau', 'Louis', 'ltusseau', '$2y$10$s4LhnorD1PVYpc0l5FAjQufG/wP3f39MDhArrg.OSfwrXW8YANsHq', '22 rue des Ternes', '46123', 'Gramat', '2000-05-01', 'VIS'),
('b13', 'Bentot', 'Pascal', 'pbentot', '$2y$10$DRak01gtADJGxxtuLDWAvuiAvCv0.x6SsZE/o4r05mzYhlUMxbzXO', '11 allée des Cerises', '46512', 'Bessines', '1992-07-09', 'VIS'),
('b16', 'Bioret', 'Luc', 'lbioret', '$2y$10$M8/zGLMbihdVncG.p2r0f.yY2GIORSyFICnMbz5SkHvOaJ3Pbj5qO', '1 Avenue gambetta', '46000', 'Cahors', '1998-05-11', 'VIS'),
('b19', 'Bunisset', 'Francis', 'fbunisset', '$2y$10$m.9HbHh4is1uO8ryiIWP2e3mV5ktj9NhYQVT1tCMBkRCFRpH87.Em', '10 rue des Perles', '93100', 'Montreuil', '1987-10-21', 'VIS'),
('b25', 'Bunisset', 'Denise', 'dbunisset', '$2y$10$MgB6mGn0uEBUd7WdPF6C3OvzzsfcsRzm1Tiqe.SfQWrakQUrTeOpa', '23 rue Manin', '75019', 'paris', '2010-12-05', 'VIS'),
('b28', 'Cacheux', 'Bernard', 'bcacheux', '$2y$10$ItrZdyPvpwOKW51NS.uQNuwLHkdib/nwfSCk6rONZRoLDy4FcF526', '114 rue Blanche', '75017', 'Paris', '2009-11-12', 'VIS'),
('b34', 'Cadic', 'Eric', 'ecadic', '$2y$10$34sP6w7cl4Ea/SdBj/Dqr.n8gqkbzfJjnuVnrZUw6p.zODPL96xF2', '123 avenue de la République', '75011', 'Paris', '2008-09-23', 'VIS'),
('b4', 'Charoze', 'Catherine', 'ccharoze', '$2y$10$A5uemUvRQu/KCIaKBcVedODELYodp/YVrBEAwv1STGNs9dqx6JNra', '100 rue Petit', '75019', 'Paris', '2005-11-12', 'VIS'),
('b50', 'Clepkens', 'Christophe', 'cclepkens', '$2y$10$cZJRR7Wc/NLauJSkOnWBmuDAsOPtna/U6FyWqhW/ifdX2dZHMifHm', '12 allée des Anges', '93230', 'Romainville', '2003-08-11', 'VIS'),
('b59', 'Cottin', 'Vincenne', 'vcottin', '$2y$10$pPlsn2a4hPHTV5C7S9bfa.8.jgGGdBv5zl4AlkoaH78zl5IFObayy', '36 rue Des Roches', '93100', 'Monteuil', '2001-11-18', 'VIS'),
('c14', 'Daburon', 'François', 'fdaburon', '$2y$10$/zltN7AYwxUwsCq4rsyzh.pawhoRgB4hk4ME.1Ww6WqkTXFFcCIYq', '13 rue de Chanzy', '94000', 'Créteil', '2002-02-11', 'VIS'),
('c3', 'De', 'Philippe', 'pde', '$2y$10$xz9OswO8Yi6otclAwED5sOMFqF.25M6gc7Iwns5NFfrAfsR9IWHWG', '13 rue Barthes', '94000', 'Créteil', '2010-12-14', 'VIS'),
('c54', 'Debelle', 'Michel', 'mdebelle', '$2y$10$d1a75IVKG9GazbfE0.RuzuMGX6K8SDu0HHGil4BYaDAAijCaRTjvK', '181 avenue Barbusse', '93210', 'Rosny', '2006-11-23', 'VIS'),
('d13', 'Debelle', 'Jeanne', 'jdebelle', '$2y$10$fxYZf/0h.fYZnBDwEzb4l.4MI3UwuXtxk8bS1Vs6r1KHQ916jTkh.', '134 allée des Joncs', '44000', 'Nantes', '2000-05-11', 'VIS'),
('d51', 'Debroise', 'Michel', 'mdebroise', '$2y$10$AFBQXGIQ171K2RCni/iLE.QhHJFVfebW2lsP006CG4OKZDMm2nVOa', '2 Bld Jourdain', '44000', 'Nantes', '2001-04-17', 'VIS'),
('e22', 'Desmarquest', 'Nathalie', 'ndesmarquest', '$2y$10$N4xYD/C7yablIxpXAcAodO4jSkIe1HOYSyfIRDaNE4whgJFAg4WSO', '14 Place d Arc', '45000', 'Orléans', '2005-11-12', 'VIS'),
('e24', 'Desnost', 'Pierre', 'pdesnost', '$2y$10$b31kv3Z.rN0i1Dn9zkBmUepGIOyxJJz6Q3tx7vusbr2qqLf/SH76e', '16 avenue des Cèdres', '23200', 'Guéret', '2001-02-05', 'VIS'),
('e39', 'Dudouit', 'Frédéric', 'fdudouit', '$2y$10$NTvgQ8o/BW1F0xxZfVoyiurggvvjK68y.1cMbgrwX2savLgLQ7/NK', '18 rue de l église', '23120', 'GrandBourg', '2000-08-01', 'VIS'),
('e49', 'Duncombe', 'Claude', 'cduncombe', '$2y$10$NmVwVQg54RC4U9TVq/pj3OiWvvNNj84LxBEtMIFP3cLD/2ab3U.PS', '19 rue de la tour', '23100', 'La souteraine', '1987-10-10', 'VIS'),
('e5', 'Enault-Pascreau', 'Céline', 'cenault', '$2y$10$tuiBIpht1UVO5v5nfyMaDOIX3UzvUVMS2lU4UyHwJdBDpYu76cEAq', '25 place de la gare', '23200', 'Gueret', '1995-09-01', 'VIS'),
('e52', 'Eynde', 'Valérie', 'veynde', '$2y$10$3Vcrw4KaOH/6gWVkJSU4M.t83zIBzvuKsa.whKD2o5y07KwHamNqK', '3 Grand Place', '13015', 'Marseille', '1999-11-01', 'VIS'),
('f21', 'Finck', 'Jacques', 'jfinck', '$2y$10$ry/n1BQzq2zPdShfFZLUT.IGwTadrw.n56Imy/LCDP.anpqOfG/96', '10 avenue du Prado', '13002', 'Marseille', '2001-11-10', 'VIS'),
('f39', 'Frémont', 'Fernande', 'ffremont', '$2y$10$aHEmjbya7F8RO5oiIX2IL.0O./qmaAoSbmEOyjSEGzmffkNJ/s4ya', '4 route de la mer', '13012', 'Allauh', '1998-10-01', 'VIS'),
('f4', 'Gest', 'Alain', 'agest', '$2y$10$0HammLGJGZLW.lgzeVIW1u6wtNB5e9f5LMKBZjbtwS5SmMAbc3016', '30 avenue de la mer', '13025', 'Berre', '1985-11-01', 'VIS');