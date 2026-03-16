<?php
/**
 * Script d'installation
 * RAMP-BENIN
 * 
 * Ce script permet de créer la base de données et les tables
 * ATTENTION: À supprimer après installation en production
 */

// Désactiver en production
if (file_exists(__DIR__ . '/.production')) {
    die('Installation désactivée en production');
}

require_once __DIR__ . '/config/database.php';

// Paramètres de connexion (sans base de données pour la créer)
$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$dbname = DB_NAME;

try {
    // Connexion sans base de données
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    
    // Lire et exécuter le schéma SQL
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    
    // Supprimer les commentaires et les commandes CREATE DATABASE/USE
    $schema = preg_replace('/--.*$/m', '', $schema);
    $schema = preg_replace('/CREATE DATABASE.*?;/i', '', $schema);
    $schema = preg_replace('/USE.*?;/i', '', $schema);
    
    // Exécuter les requêtes
    $statements = explode(';', $schema);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Ignorer les erreurs de tables existantes
                if (strpos($e->getMessage(), 'already exists') === false) {
                    throw $e;
                }
            }
        }
    }
    
    echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Installation - RAMP-BENIN</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #779D2E; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info { background: #d1ecf1; border: 1px solid #5A6AB2; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0; }
        h1 { color: #5A6AB2; }
        .btn { display: inline-block; padding: 10px 20px; background: #5A6AB2; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #4a5a9f; }
    </style>
</head>
<body>
    <h1>Installation RAMP-BENIN</h1>
    <div class='success'>
        <strong>✓ Installation réussie !</strong><br>
        La base de données et les tables ont été créées avec succès.
    </div>
    <div class='info'>
        <strong>Compte administrateur par défaut :</strong><br>
        Nom d'utilisateur: <strong>admin</strong><br>
        Mot de passe: <strong>admin123</strong><br><br>
        <strong>⚠️ IMPORTANT :</strong> Changez ce mot de passe après la première connexion !
    </div>
    <div class='info'>
        <strong>⚠️ SÉCURITÉ :</strong><br>
        Pour des raisons de sécurité, supprimez ou renommez ce fichier (install.php) après l'installation.
    </div>
    <a href='login.php' class='btn'>Accéder à l'application</a>
</body>
</html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Erreur d'installation - RAMP-BENIN</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .error { background: #f8d7da; border: 1px solid #dc3545; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        h1 { color: #dc3545; }
    </style>
</head>
<body>
    <h1>Erreur d'installation</h1>
    <div class='error'>
        <strong>✗ Erreur lors de l'installation :</strong><br>
        " . htmlspecialchars($e->getMessage()) . "<br><br>
        Veuillez vérifier :
        <ul>
            <li>Les paramètres de connexion dans config/database.php</li>
            <li>Que MySQL est démarré</li>
            <li>Que l'utilisateur MySQL a les permissions nécessaires</li>
        </ul>
    </div>
</body>
</html>";
}

