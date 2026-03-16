<?php
/**
 * Configuration générale
 * RAMP-BENIN
 */

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration de l'application
define('APP_NAME', 'RAMP-BENIN');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'https://sgi.ramp-afrique.org');

// Chemins
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Charte graphique
define('COLOR_WHITE', '#FFFFFF');
define('COLOR_BLACK', '#000000');
define('COLOR_BLUE', '#5A6AB2');
define('COLOR_GREEN', '#779D2E');

// Configuration de sécurité
define('SESSION_LIFETIME', 3600); // 1 heure
define('PASSWORD_MIN_LENGTH', 8);

// Timezone
date_default_timezone_set('Africa/Porto-Novo');

// Encodage
ini_set('default_charset', 'UTF-8');
// Vérifier et activer mbstring si disponible
if (extension_loaded('mbstring') && function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
} elseif (!extension_loaded('mbstring')) {
    // Avertissement si mbstring n'est pas chargé (non bloquant)
    error_log('Warning: mbstring extension is not loaded. Some features may not work correctly.');
}

// Inclusion de la connexion à la base de données
require_once ROOT_PATH . '/config/database.php';

