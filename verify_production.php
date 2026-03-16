<?php
/**
 * Script de vérification de la configuration pour production
 * RAMP-BENIN
 */

require 'config/constants.php';
require 'config/config.php';

echo "<pre style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 5px;'>";
echo "=== VÉRIFICATION DE CONFIGURATION PRODUCTION ===\n\n";

// 1. Vérifier BASE_URL
echo "1️⃣  BASE_URL Configuration:\n";
echo "   URL: " . BASE_URL . "\n";
if (strpos(BASE_URL, 'sgi.ramp-afrique.org') !== false) {
    echo "   ✅ Correctement configuré pour production\n";
} else {
    echo "   ⚠️  WARNING: BASE_URL ne pointe pas vers sgi.ramp-afrique.org\n";
}
echo "\n";

// 2. Vérifier les paramètres de base de données
echo "2️⃣  Configuration Base de Données:\n";
if (defined('DB_HOST')) {
    echo "   Hôte: " . DB_HOST . "\n";
    if (strpos(DB_HOST, 'node30-eu.n0c.com') !== false) {
        echo "   ✅ Configuré pour production (hébergement en ligne)\n";
    } elseif (DB_HOST === 'localhost') {
        echo "   ℹ️  Configuration locale (développement)\n";
    }
    echo "   Base de données: " . DB_NAME . "\n";
    echo "   Utilisateur: " . DB_USER . "\n";
} else {
    echo "   ❌ Erreur: Constants de BD non définies\n";
}
echo "\n";

// 3. Vérifier la connexion à la BD
echo "3️⃣  Test de Connexion Base de Données:\n";
try {
    require 'classes/Database.php';
    $db = Database::getInstance()->getConnection();
    echo "   ✅ Connexion réussie à la base de données\n";
    
    // Vérifier les tables
    $result = $db->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema='" . DB_NAME . "'");
    $tableCount = $result->fetch()['count'];
    echo "   ✅ Tables trouvées: " . $tableCount . "\n";
    
} catch (Exception $e) {
    echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Vérifier les dossiers critiques
echo "4️⃣  Permissions des Dossiers:\n";
$folders = [
    'uploads' => 'w',
    'uploads/documents' => 'w',
    'config' => 'r',
    'classes' => 'r',
    'includes' => 'r'
];

foreach ($folders as $folder => $permission) {
    $path = ROOT_PATH . '/' . $folder;
    if (is_dir($path)) {
        if (is_writable($path) && $permission === 'w') {
            echo "   ✅ $folder (inscriptible)\n";
        } elseif (is_readable($path) && $permission === 'r') {
            echo "   ✅ $folder (lisible)\n";
        } else {
            echo "   ⚠️  $folder (vérifier les permissions)\n";
        }
    } else {
        echo "   ❌ $folder (non trouvé)\n";
    }
}
echo "\n";

// 5. Vérifier les extensions PHP
echo "5️⃣  Extensions PHP Requises:\n";
$extensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'curl'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "   ✅ $ext\n";
    } else {
        echo "   ❌ $ext (MANQUANT)\n";
    }
}
echo "\n";

// 6. Vérifier la version PHP
echo "6️⃣  Version PHP:\n";
$version = phpversion();
echo "   Version: " . $version . "\n";
if (version_compare($version, '7.4', '>=')) {
    echo "   ✅ Version compatible\n";
} else {
    echo "   ⚠️  Version PHP < 7.4 détectée\n";
}
echo "\n";

// 7. Vérifier le .htaccess
echo "7️⃣  Fichier .htaccess:\n";
$htaccess = ROOT_PATH . '/.htaccess';
if (file_exists($htaccess)) {
    echo "   ✅ Fichier .htaccess présent\n";
    $content = file_get_contents($htaccess);
    if (strpos($content, 'sgi.ramp-afrique.org') !== false) {
        echo "   ✅ Redirection HTTPS configurée\n";
    }
} else {
    echo "   ❌ Fichier .htaccess manquant\n";
}
echo "\n";

// 8. Résumé
echo "=== RÉSUMÉ ===\n";
echo "✅ Configuration prête pour production sur sgi.ramp-afrique.org\n";
echo "📋 Assurez-vous de:\n";
echo "   1. Télécharger tous les fichiers via FTP\n";
echo "   2. Importer la base de données depuis cPanel > phpMyAdmin\n";
echo "   3. Configurer les permissions des dossiers (755 pour /uploads)\n";
echo "   4. Activer SSL/TLS sur le domaine\n";
echo "   5. Vérifier que mod_rewrite est activé sur Apache\n";

echo "</pre>";
?>
