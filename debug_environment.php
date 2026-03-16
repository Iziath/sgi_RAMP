<?php
/**
 * Script de DEBUG - À exécuter sur le serveur en ligne
 * https://sgi.ramp-afrique.org/debug_environment.php
 */

echo "<pre style='background: #222; color: #0f0; padding: 20px; font-family: monospace; border: 2px solid #0f0;'>";
echo "=== DEBUG SERVEUR EN LIGNE ===\n\n";

echo "📍 HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Non défini') . "\n";
echo "🌐 SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'Non défini') . "\n";
echo "🔒 HTTPS: " . ($_SERVER['HTTPS'] ?? 'Non défini') . "\n";
echo "🔌 SERVER_PORT: " . ($_SERVER['SERVER_PORT'] ?? 'Non défini') . "\n";
echo "📝 REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Non défini') . "\n";
echo "🌍 Protocole: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP') . "\n";

echo "\n--- RÉSULTAT DE LA DÉTECTION ---\n";

// Simuler la détection
$host = $_SERVER['HTTP_HOST'] ?? '';
$isProduction = (
    strpos($host, 'sgi.ramp-afrique.org') !== false ||
    strpos($host, 'ramp-afrique.org') !== false ||
    ($_SERVER['HTTPS'] ?? 'off') === 'on'
);

echo "Host contient 'sgi.ramp-afrique.org': " . (strpos($host, 'sgi.ramp-afrique.org') !== false ? '✅ OUI' : '❌ NON') . "\n";
echo "Host contient 'ramp-afrique.org': " . (strpos($host, 'ramp-afrique.org') !== false ? '✅ OUI' : '❌ NON') . "\n";
echo "HTTPS actif: " . ((($_SERVER['HTTPS'] ?? 'off') === 'on') ? '✅ OUI' : '❌ NON') . "\n";

echo "\n--- RÉSULTAT FINAL ---\n";
if ($isProduction) {
    echo "✅ ENVIRONNEMENT: PRODUCTION\n";
    echo "BASE_URL: https://sgi.ramp-afrique.org\n";
} else {
    echo "ℹ️  ENVIRONNEMENT: DÉVELOPPEMENT/LOCAL\n";
    echo "BASE_URL: http://localhost:8000\n";
}

echo "\n--- CONSEIL ---\n";
if (!$isProduction && strpos($host, 'ramp-afrique') !== false) {
    echo "⚠️  ATTENTION: Le domaine contient 'ramp-afrique' mais n'est pas reconnu comme production\n";
    echo "📝 Vérifiez que le HOST exactement est correct\n";
}

echo "</pre>";
?>
