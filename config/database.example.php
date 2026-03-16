<?php
/**
 * Configuration de la base de données - EXEMPLE
 * RAMP-BENIN
 * 
 * Copiez ce fichier vers database.php et modifiez les paramètres
 */

// ============================================
// CONFIGURATION BASE DE DONNÉES EN LIGNE
// ============================================

// Option 1 : Base de données hébergée (cPanel, Plesk, etc.)
define('DB_HOST', 'votre-serveur.com');        // Ex: mysql.votre-serveur.com
define('DB_NAME', 'votre_nom_base');           // Nom de votre base de données
define('DB_USER', 'votre_utilisateur');        // Utilisateur MySQL
define('DB_PASS', 'votre_mot_de_passe');      // Mot de passe MySQL
define('DB_CHARSET', 'utf8mb4');

// Option 2 : Base de données cloud (PlanetScale, AWS RDS, etc.)
// Pour PlanetScale :
// define('DB_HOST', 'aws.connect.psdb.cloud');
// define('DB_NAME', 'votre_base');
// define('DB_USER', 'votre_user');
// define('DB_PASS', 'votre_password');
// define('DB_CHARSET', 'utf8mb4');

// Option 3 : Base de données locale (développement)
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'ramp_benin');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_CHARSET', 'utf8mb4');
