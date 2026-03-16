<?php
/**
 * API pour les statistiques
 * RAMP-BENIN
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';

// Vérifier l'authentification
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

$projetClass = new Projet();
$activiteClass = new Activite();
$beneficiaireClass = new Beneficiaire();
$partenaireClass = new Partenaire();

// Récupérer les statistiques
$stats = [
    'projects' => $projetClass->getStats(),
    'activities' => $activiteClass->getStats(),
    'beneficiaries' => $beneficiaireClass->getStats(),
    'partners' => $partenaireClass->getStats()
];

echo json_encode($stats);

