<?php
/**
 * Script de test de connexion à la base de données
 * RAMP-BENIN
 * 
 * Utilisez ce fichier pour tester votre connexion à la base de données
 * Supprimez-le après utilisation pour des raisons de sécurité
 */

require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Connexion - RAMP-BENIN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            background: #d4edda;
            border: 1px solid #779D2E;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #5A6AB2;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        h1 {
            color: #5A6AB2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test de Connexion à la Base de Données</h1>
        
        <?php
        // Afficher les informations de configuration (masquées)
        echo '<div class="info">';
        echo '<strong>Configuration actuelle :</strong><br>';
        echo 'Host: ' . htmlspecialchars(DB_HOST) . '<br>';
        if (defined('DB_PORT') && DB_PORT) {
            echo 'Port: ' . htmlspecialchars(DB_PORT) . '<br>';
        }
        echo 'Database: ' . htmlspecialchars(DB_NAME) . '<br>';
        echo 'User: ' . htmlspecialchars(DB_USER) . '<br>';
        echo 'Charset: ' . htmlspecialchars(DB_CHARSET) . '<br>';
        echo '</div>';
        
        // Tester la connexion
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
            echo '<div class="success">';
            echo '✅ <strong>Connexion réussie !</strong><br>';
            echo 'La connexion à la base de données fonctionne correctement.';
            echo '</div>';
            
            // Tester les tables
            echo '<h2>📊 Vérification des Tables</h2>';
            
            $tables = ['users', 'organizations', 'projects', 'activities', 'beneficiaries', 'partners'];
            $existingTables = [];
            $missingTables = [];
            
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    $existingTables[] = $table;
                } else {
                    $missingTables[] = $table;
                }
            }
            
            if (empty($missingTables)) {
                echo '<div class="success">';
                echo '✅ Toutes les tables existent dans la base de données.';
                echo '</div>';
            } else {
                echo '<div class="warning">';
                echo '⚠️ <strong>Tables manquantes :</strong><br>';
                echo implode(', ', $missingTables) . '<br><br>';
                echo 'Vous devez importer le schéma : <code>database/schema.sql</code>';
                echo '</div>';
            }
            
            if (!empty($existingTables)) {
                echo '<h3>Tables existantes :</h3>';
                echo '<ul>';
                foreach ($existingTables as $table) {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
                    $count = $stmt->fetch()['count'];
                    echo "<li><strong>$table</strong> : $count enregistrement(s)</li>";
                }
                echo '</ul>';
            }
            
            // Informations sur la version MySQL
            $stmt = $pdo->query("SELECT VERSION() as version");
            $version = $stmt->fetch()['version'];
            echo '<div class="info">';
            echo '<strong>Version MySQL :</strong> ' . htmlspecialchars($version);
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '❌ <strong>Erreur de connexion :</strong><br>';
            echo htmlspecialchars($e->getMessage());
            echo '</div>';
            
            echo '<div class="info">';
            echo '<strong>Vérifications à faire :</strong><br>';
            echo '<ul>';
            echo '<li>Le serveur MySQL est-il accessible ?</li>';
            echo '<li>Les identifiants sont-ils corrects ?</li>';
            echo '<li>Le nom de la base de données existe-t-il ?</li>';
            echo '<li>Votre IP est-elle autorisée (si restriction) ?</li>';
            echo '<li>Le port 3306 est-il ouvert ?</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
        
        <div class="warning">
            <strong>⚠️ Sécurité :</strong><br>
            Supprimez ce fichier (<code>test_connection.php</code>) après utilisation pour des raisons de sécurité.
        </div>
    </div>
</body>
</html>

