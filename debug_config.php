<?php
/**
 * Fichier de DEBUG pour vérifier la configuration
 */

require_once __DIR__ . '/config/constants.php';

echo "<pre style='background: #222; color: #0f0; padding: 20px; font-family: monospace;'>";
echo "=== DEBUG - INFORMATIONS DE CONFIGURATION ===\n\n";

echo "📍 HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "🔗 BASE_URL: " . BASE_URL . "\n";
echo "📊 REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "🌐 SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "\n";
echo "🔌 SERVER_PORT: " . $_SERVER['SERVER_PORT'] . "\n";
echo "🔒 HTTPS: " . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'Non défini') . "\n";

echo "\n--- DÉTECTION D'ENVIRONNEMENT ---\n";
if ($_SERVER['HTTP_HOST'] === 'sgi.ramp-afrique.org' || $_SERVER['HTTP_HOST'] === 'www.sgi.ramp-afrique.org') {
    echo "✅ ENVIRONNEMENT: PRODUCTION (sgi.ramp-afrique.org)\n";
} else {
    echo "ℹ️  ENVIRONNEMENT: DÉVELOPPEMENT (localhost)\n";
}

echo "\n--- RECOMMANDATION ---\n";
if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    echo "⚠️  Vous êtes sur LOCALHOST\n";
    echo "📝 Pour accéder à la production: https://sgi.ramp-afrique.org\n";
} else {
    echo "✅ Vous êtes sur le domaine de PRODUCTION\n";
}

echo "</pre>";
?>
