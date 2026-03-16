<?php
/**
 * Gestionnaire de requêtes AJAX
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

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_project':
            $id = $_GET['id'] ?? 0;
            $projetClass = new Projet();
            $project = $projetClass->getById($id);
            echo json_encode(['success' => true, 'data' => $project]);
            break;
            
        case 'get_activities_by_project':
            $projectId = $_GET['project_id'] ?? 0;
            $activiteClass = new Activite();
            $activities = $activiteClass->getAll($projectId);
            echo json_encode(['success' => true, 'data' => $activities]);
            break;
            
        case 'get_beneficiaries_by_project':
            $projectId = $_GET['project_id'] ?? 0;
            $beneficiaireClass = new Beneficiaire();
            $beneficiaries = $beneficiaireClass->getAll($projectId);
            echo json_encode(['success' => true, 'data' => $beneficiaries]);
            break;
            
        case 'update_progress':
            $type = $_POST['type'] ?? ''; // 'project' ou 'activity'
            $id = $_POST['id'] ?? 0;
            $progress = $_POST['progress'] ?? 0;
            
            if ($type === 'project') {
                $projetClass = new Projet();
                $project = $projetClass->getById($id);
                if ($project) {
                    $projetClass->update($id, ['progress' => $progress] + $project);
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Projet non trouvé']);
                }
            } elseif ($type === 'activity') {
                $activiteClass = new Activite();
                $activity = $activiteClass->getById($id);
                if ($activity) {
                    $activiteClass->update($id, ['progress' => $progress] + $activity);
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Activité non trouvée']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Type invalide']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Action non reconnue']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

