-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 16 mars 2026 à 04:45
-- Version du serveur : 10.6.21-MariaDB
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `zgfsqhef_sgi`
--

-- --------------------------------------------------------

--
-- Structure de la table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `planned_start_date` date DEFAULT NULL,
  `planned_end_date` date DEFAULT NULL,
  `actual_start_date` date DEFAULT NULL,
  `actual_end_date` date DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') DEFAULT 'planned',
  `progress` int(11) DEFAULT 0,
  `budget` decimal(15,2) DEFAULT 0.00,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `activities`
--

INSERT INTO `activities` (`id`, `project_id`, `title`, `description`, `planned_start_date`, `planned_end_date`, `actual_start_date`, `actual_end_date`, `status`, `progress`, `budget`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Formation de la première vague en montage et production vidéo', '', '2026-02-02', '2026-02-14', NULL, NULL, 'completed', 100, 26000.00, 1, '2026-02-24 10:31:29', '2026-02-24 10:31:29'),
(2, 3, 'Renforcement des capacités en Fund Raising et Stratégie de Mobilisation de Ressources', '', '2026-01-26', '2026-01-28', NULL, NULL, 'completed', 100, 0.00, 1, '2026-02-24 15:26:21', '2026-02-24 15:26:21');

-- --------------------------------------------------------

--
-- Structure de la table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('M','F','Other') NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `ville_de_provenance` varchar(100) DEFAULT NULL,
  `category` enum('individual','group','community') DEFAULT 'individual',
  `registration_date` date NOT NULL,
  `status` enum('active','inactive','completed') DEFAULT 'active',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `document_templates`
--

CREATE TABLE `document_templates` (
  `id` int(11) NOT NULL,
  `profile_type` enum('volontaire','stagiaire','benevole') NOT NULL,
  `type_document` varchar(100) NOT NULL,
  `nom_document` varchar(200) NOT NULL,
  `obligatoire` tinyint(1) DEFAULT 1,
  `ordre` int(11) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `document_templates`
--

INSERT INTO `document_templates` (`id`, `profile_type`, `type_document`, `nom_document`, `obligatoire`, `ordre`, `description`, `created_at`) VALUES
(1, 'volontaire', 'charte_engagement', 'Charte d\'engagement', 1, 1, 'Document ?? signer par le volontaire', '2025-12-30 18:02:14'),
(2, 'volontaire', 'cv', 'Curriculum Vitae', 1, 2, 'CV du volontaire', '2025-12-30 18:02:14'),
(3, 'volontaire', 'lettre_motivation', 'Lettre de motivation', 1, 3, 'Lettre de motivation', '2025-12-30 18:02:14'),
(4, 'volontaire', 'piece_identite', 'Pi??ce d\'identit??', 1, 4, 'Copie de la pi??ce d\'identit??', '2025-12-30 18:02:14'),
(5, 'volontaire', 'reglement_interne', 'R??glement interne', 1, 5, 'R??glement interne sign??', '2025-12-30 18:02:14'),
(6, 'stagiaire', 'convention_stage', 'Convention de stage', 1, 1, 'Convention tripartite sign??e', '2025-12-30 18:02:14'),
(7, 'stagiaire', 'cv', 'Curriculum Vitae', 1, 2, 'CV du stagiaire', '2025-12-30 18:02:14'),
(8, 'stagiaire', 'lettre_motivation', 'Lettre de motivation', 1, 3, 'Lettre de motivation', '2025-12-30 18:02:14'),
(9, 'stagiaire', 'piece_identite', 'Pi??ce d\'identit??', 1, 4, 'Copie de la pi??ce d\'identit??', '2025-12-30 18:02:14'),
(10, 'stagiaire', 'attestation_scolaire', 'Attestation de scolarit??', 1, 5, 'Attestation de l\'??tablissement', '2025-12-30 18:02:14'),
(11, 'benevole', 'engagement', 'Engagement b??n??vole', 1, 1, 'Document d\'engagement sign??', '2025-12-30 18:02:14'),
(12, 'benevole', 'piece_identite', 'Pi??ce d\'identit??', 0, 2, 'Copie de la pi??ce d\'identit?? (optionnel)', '2025-12-30 18:02:14');

-- --------------------------------------------------------

--
-- Structure de la table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `code`, `description`, `address`, `phone`, `email`, `created_at`, `updated_at`, `status`) VALUES
(1, 'RAMP-BENIN', 'RAMP-001', 'Organisation principale RAMP-BENIN', NULL, NULL, NULL, '2025-12-30 15:00:22', '2025-12-30 15:00:22', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `partners`
--

CREATE TABLE `partners` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('government','ngo','private','international') NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `partners`
--

INSERT INTO `partners` (`id`, `name`, `type`, `contact_person`, `phone`, `email`, `address`, `website`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'United Network of Young Peacebuilders (UNOY Peacebuilders)', 'international', '', '41235544', 'info@unoy.org', 'Bureaux HSK (5e étage) Waldorpstraat 17, 2521 CA, LA HAYE\r\nPOSTBUS 61489, 2506 AL, LA HAYE, PAYS-Bas', 'https://unoy.org/', 'UNOY est un réseau mondial de 100+ organisations de consolidation de la paix dirigées par des jeunes, œuvrant pour un monde sans violence, où les jeunes jouent un rôle de premier plan dans la construction de la paix et la transformation des conflits.', 'active', '2026-01-22 16:45:46', '2026-01-22 16:45:46');

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT 0.00,
  `status` enum('planning','active','completed','cancelled') DEFAULT 'planning',
  `progress` int(11) DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `organization_id`, `code`, `title`, `description`, `start_date`, `end_date`, `budget`, `status`, `progress`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'PROJ-6953E9A395F63', 'FIFA', 'cest n=bon', '2025-12-31', '2025-12-30', 0.00, 'planning', 0, 1, '2025-12-30 15:02:59', '2025-12-30 15:02:59'),
(2, 1, 'PROJ-699D7C91D689A', 'Montage et Production vidéo avec Adobe Premiere Pro', 'Dans le cadre du renforcement des capacités techniques des membres du RAMP-BENIN, notamment au sein du Département Digital Peace Building, il apparaît essentiel de doter les membres et partenaires d’outils modernes de production audiovisuelle. La vidéo constitue aujourd’hui un puissant levier de communication, de sensibilisation et d’éducation à la paix.\r\nDans un contexte marqué par la prolifération des messages de haine et de désinformation, il devient crucial de maîtriser la production de contenus positifs, engageants et crédibles. La présente formation sur le montage et la production vidéo avec Adobe Premiere Pro s’inscrit dans cette dynamique de professionnalisation de la communication pour la paix. Elle vise non seulement à outiller les membres du RAMP-BENIN, mais également à ouvrir l’opportunité à des acteurs externes (jeunes communicants, membres d’OSC, étudiants, journalistes, etc.) désireux d’apporter leur contribution à la promotion de la paix à travers le numérique. Cette formation servira aussi de base pour identifier et constituer une équipe de montage vidéo qui alimentera le futur Studio Média pour la Paix du RAMP-BENIN.\r\n\r\nLe projet vise de renforcer les compétences pratiques des participants en montage et production vidéo afin de leur permettre de concevoir des contenus audiovisuels professionnels dédiés à la promotion de la paix, de la sécurité et de la cohésion sociale.\r\n', '2026-02-02', '2026-11-30', 671500.00, 'active', 50, 1, '2026-02-24 10:25:21', '2026-02-24 10:28:34'),
(3, 1, 'PROJ-699DC2128A2AC', 'Renforcement des capacités des Responsables Programmes du  RAMP-BÉNIN en mobilisation continue de ressources', 'RAMP-BÉNIN œuvre pour la paix, les droits humains, la sécurité communautaire et la participation civique des jeunes au Bénin. Cependant, comme de nombreuses organisations émergentes, le RAMP-BÉNIN fait face à un défi majeur : la mobilisation durable de ressources financières pour soutenir et étendre ses actions.  \r\nLe renforcement des capacités internes en collecte de fonds est indispensable pour diversifier les financements,  professionnaliser la rédaction de projets et structurer une stratégie de fundraising solide et adaptée aux réalités des bailleurs internationaux. Ce mini-projet vise à renforcer ces compétences afin de permettre au RAMP-BÉNIN de devenir plus autonome, compétitif et stratégique dans la recherche de financements. ', '2026-01-12', '2026-02-12', 491850.00, 'completed', 100, 1, '2026-02-24 15:21:54', '2026-02-24 15:22:31');

-- --------------------------------------------------------

--
-- Structure de la table `project_partners`
--

CREATE TABLE `project_partners` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `partner_id` int(11) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `contribution` decimal(15,2) DEFAULT 0.00,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `project_partners`
--

INSERT INTO `project_partners` (`id`, `project_id`, `partner_id`, `role`, `contribution`, `start_date`, `end_date`, `created_at`) VALUES
(2, 3, 1, 'partenaire', 0.00, NULL, NULL, '2026-02-24 15:22:31');

-- --------------------------------------------------------

--
-- Structure de la table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `report_type` enum('progress','financial','narrative','other') DEFAULT 'progress',
  `report_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `competences` text DEFAULT NULL,
  `domaines_interet` text DEFAULT NULL,
  `disponibilite` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('admin','manager','user','membre_administration','volontaire','stagiaire','benevole') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `competences`, `domaines_interet`, `disponibilite`, `password`, `full_name`, `photo`, `role`, `created_at`, `updated_at`, `status`) VALUES
(1, 'admin', 'admin@ramp-benin.bj', NULL, NULL, NULL, '$2y$10$8GE2bnDxfq5Mw2p/Utx0xuXvddl7seiLH3kHp59QDx1/ufTZ5RvCG', 'Administrateur', NULL, 'admin', '2025-12-30 15:00:22', '2025-12-30 15:01:38', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `user_activities`
--

CREATE TABLE `user_activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `date_participation` date NOT NULL,
  `heures` double DEFAULT 0,
  `description` text DEFAULT NULL,
  `statut` enum('planifiee','en_cours','terminee','annulee') DEFAULT 'planifiee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_cycles`
--

CREATE TABLE `user_cycles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `cycle_type` enum('recrutement','integration','evaluation','fin') NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('en_cours','termine','annule') DEFAULT 'en_cours',
  `responsable_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `type_document` enum('charte_engagement','convention_stage','cv','lettre_motivation','certificat','evaluation','autre') NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin_fichier` varchar(500) NOT NULL,
  `taille_fichier` int(11) DEFAULT NULL,
  `date_upload` date NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_evaluations`
--

CREATE TABLE `user_evaluations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `type_evaluation` enum('mensuelle','trimestrielle','finale') NOT NULL,
  `periode_debut` date NOT NULL,
  `periode_fin` date NOT NULL,
  `evaluateur_id` int(11) NOT NULL,
  `note_globale` decimal(3,2) DEFAULT NULL,
  `points_forts` text DEFAULT NULL,
  `points_amelioration` text DEFAULT NULL,
  `recommandations` text DEFAULT NULL,
  `statut` enum('brouillon','finalisee') DEFAULT 'brouillon',
  `date_evaluation` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_payments`
--

CREATE TABLE `user_payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `periode` date NOT NULL,
  `type_paiement` enum('mensuel','ponctuel','prime') DEFAULT 'mensuel',
  `statut` enum('en_attente','paye','retarde','annule') DEFAULT 'en_attente',
  `date_paiement` date DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `reference_paiement` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_type` enum('volontaire','stagiaire','benevole') NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut_validation` enum('en_attente','valide','refuse') DEFAULT 'en_attente',
  `date_validation` date DEFAULT NULL,
  `valide_par` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `duree_engagement` int(11) DEFAULT NULL,
  `charte_signee` tinyint(1) DEFAULT 0,
  `date_charte_signee` date DEFAULT NULL,
  `heures_effectuees` int(11) DEFAULT 0,
  `montant_mensuel` decimal(10,2) DEFAULT NULL,
  `raison_fin` enum('fin_contrat','demission','exclusion','autre') DEFAULT NULL,
  `ecole_universite` varchar(200) DEFAULT NULL,
  `niveau_etudes` varchar(100) DEFAULT NULL,
  `convention_signee` tinyint(1) DEFAULT 0,
  `date_convention_signee` date DEFAULT NULL,
  `tuteur_id` int(11) DEFAULT NULL,
  `maitre_de_stage_id` int(11) DEFAULT NULL,
  `projet_affectation_id` int(11) DEFAULT NULL,
  `rapport_rendu` tinyint(1) DEFAULT 0,
  `duree_stage` int(11) DEFAULT NULL,
  `type_engagement` enum('ponctuel','recurrent') DEFAULT 'ponctuel',
  `badge_reconnaissance` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_activity` (`activity_id`);

--
-- Index pour la table `document_templates`
--
ALTER TABLE `document_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_template` (`profile_type`,`type_document`),
  ADD KEY `idx_profile_type` (`profile_type`);

--
-- Index pour la table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Index pour la table `partners`
--
ALTER TABLE `partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`);

--
-- Index pour la table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_organization` (`organization_id`);

--
-- Index pour la table `project_partners`
--
ALTER TABLE `project_partners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_project_partner` (`project_id`,`partner_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Index pour la table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_type` (`report_type`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_date_participation` (`date_participation`);

--
-- Index pour la table `user_cycles`
--
ALTER TABLE `user_cycles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `responsable_id` (`responsable_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_cycle_type` (`cycle_type`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type_document` (`type_document`);

--
-- Index pour la table `user_evaluations`
--
ALTER TABLE `user_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `evaluateur_id` (`evaluateur_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type_evaluation` (`type_evaluation`);

--
-- Index pour la table `user_payments`
--
ALTER TABLE `user_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_periode` (`user_id`,`periode`,`type_paiement`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_periode` (`periode`),
  ADD KEY `idx_statut` (`statut`);

--
-- Index pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_profile` (`user_id`,`profile_type`),
  ADD KEY `valide_par` (`valide_par`),
  ADD KEY `tuteur_id` (`tuteur_id`),
  ADD KEY `projet_affectation_id` (`projet_affectation_id`),
  ADD KEY `idx_profile_type` (`profile_type`),
  ADD KEY `idx_statut_validation` (`statut_validation`),
  ADD KEY `fk_maitre_stage` (`maitre_de_stage_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `document_templates`
--
ALTER TABLE `document_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `partners`
--
ALTER TABLE `partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `project_partners`
--
ALTER TABLE `project_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_cycles`
--
ALTER TABLE `user_cycles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_evaluations`
--
ALTER TABLE `user_evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_payments`
--
ALTER TABLE `user_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activities_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `beneficiaries_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_beneficiary_activity` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `project_partners`
--
ALTER TABLE `project_partners`
  ADD CONSTRAINT `project_partners_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_partners_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `partners` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `reports_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `user_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_activities_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_activities_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_cycles`
--
ALTER TABLE `user_cycles`
  ADD CONSTRAINT `user_cycles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cycles_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `user_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_cycles_ibfk_3` FOREIGN KEY (`responsable_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_documents`
--
ALTER TABLE `user_documents`
  ADD CONSTRAINT `user_documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_documents_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `user_profiles` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_documents_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_evaluations`
--
ALTER TABLE `user_evaluations`
  ADD CONSTRAINT `user_evaluations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_evaluations_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `user_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_evaluations_ibfk_3` FOREIGN KEY (`evaluateur_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `user_payments`
--
ALTER TABLE `user_payments`
  ADD CONSTRAINT `user_payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_payments_ibfk_2` FOREIGN KEY (`profile_id`) REFERENCES `user_profiles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_payments_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_maitre_stage` FOREIGN KEY (`maitre_de_stage_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_profiles_ibfk_2` FOREIGN KEY (`valide_par`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_profiles_ibfk_3` FOREIGN KEY (`tuteur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_profiles_ibfk_4` FOREIGN KEY (`projet_affectation_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
