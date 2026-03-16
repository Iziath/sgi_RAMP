<?php
/**
 * Voir un projet
 * RAMP-BENIN
 */

$pageTitle = 'Détails du Projet';
require_once __DIR__ . '/../../includes/header.php';

$projetClass = new Projet();
$activiteClass = new Activite();
$beneficiaireClass = new Beneficiaire();

$id = $_GET['id'] ?? 0;
$project = $projetClass->getById($id);

if (!$project) {
    redirectWithMessage('index.php', 'Projet non trouvé', 'error');
}

// Récupérer les activités du projet
$activities = $activiteClass->getAll($id);

// Récupérer les bénéficiaires du projet
$beneficiaries = $beneficiaireClass->getAll($id);
?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--color-blue);"><?php echo escape($project['title']); ?></h1>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary">Modifier</a>
            <a href="index.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Informations principales -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Informations du Projet</h3>
            </div>
            <table class="table">
                <tr>
                    <th>Code</th>
                    <td><?php echo escape($project['code']); ?></td>
                </tr>
                <tr>
                    <th>Organisation</th>
                    <td><?php echo escape($project['organization_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?php echo escape($project['description'] ?? 'Aucune description'); ?></td>
                </tr>
                <tr>
                    <th>Date de début</th>
                    <td><?php echo formatDate($project['start_date']); ?></td>
                </tr>
                <tr>
                    <th>Date de fin</th>
                    <td><?php echo formatDate($project['end_date']); ?></td>
                </tr>
                <tr>
                    <th>Budget</th>
                    <td><?php echo formatCurrency($project['budget']); ?></td>
                </tr>
                <tr>
                    <th>Progression</th>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $project['progress']; ?>%;">
                                <?php echo $project['progress']; ?>%
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Statut</th>
                    <td><?php echo getStatusBadge($project['status'], 'project'); ?></td>
                </tr>
                <tr>
                    <th>Créé par</th>
                    <td><?php echo escape($project['created_by_name'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Date de création</th>
                    <td><?php echo formatDate($project['created_at']); ?></td>
                </tr>
            </table>
        </div>
        
        <!-- Actions rapides -->
        <div>
            <div class="card" style="margin-bottom: 1rem;">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="../activites/create.php?project_id=<?php echo $id; ?>" class="btn btn-primary">Nouvelle Activité</a>
                    <a href="../beneficiaires/create.php?project_id=<?php echo $id; ?>" class="btn btn-success">Nouveau Bénéficiaire</a>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistiques</h3>
                </div>
                <div>
                    <p><strong>Activités:</strong> <?php echo count($activities); ?></p>
                    <p><strong>Bénéficiaires:</strong> <?php echo count($beneficiaries); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activités -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Activités</h3>
            <a href="../activites/create.php?project_id=<?php echo $id; ?>" class="btn btn-primary btn-sm">Ajouter</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Date prévue début</th>
                    <th>Date prévue fin</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activities)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucune activité</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?php echo escape($activity['title']); ?></td>
                            <td><?php echo formatDate($activity['planned_start_date']); ?></td>
                            <td><?php echo formatDate($activity['planned_end_date']); ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $activity['progress']; ?>%;">
                                        <?php echo $activity['progress']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo getStatusBadge($activity['status'], 'activity'); ?></td>
                            <td>
                                <a href="../activites/view.php?id=<?php echo $activity['id']; ?>" class="btn btn-sm btn-primary">Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

