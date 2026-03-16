<?php
/**
 * Voir une activité
 * RAMP-BENIN
 */

$pageTitle = 'Détails de l\'Activité';
require_once __DIR__ . '/../../includes/header.php';

$activiteClass = new Activite();

$id = $_GET['id'] ?? 0;
$activity = $activiteClass->getById($id);

if (!$activity) {
    redirectWithMessage('index.php', 'Activité non trouvée', 'error');
}
?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--color-blue);"><?php echo escape($activity['title']); ?></h1>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary">Modifier</a>
            <a href="index.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations de l'Activité</h3>
        </div>
        <table class="table">
            <tr>
                <th>Projet</th>
                <td>
                    <a href="../projets/view.php?id=<?php echo $activity['project_id']; ?>" style="color: var(--color-blue);">
                        <?php echo escape($activity['project_title'] ?? '-'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo escape($activity['description'] ?? 'Aucune description'); ?></td>
            </tr>
            <tr>
                <th>Date prévue début</th>
                <td><?php echo formatDate($activity['planned_start_date']); ?></td>
            </tr>
            <tr>
                <th>Date prévue fin</th>
                <td><?php echo formatDate($activity['planned_end_date']); ?></td>
            </tr>
            <tr>
                <th>Date réelle début</th>
                <td><?php echo formatDate($activity['actual_start_date']); ?></td>
            </tr>
            <tr>
                <th>Date réelle fin</th>
                <td><?php echo formatDate($activity['actual_end_date']); ?></td>
            </tr>
            <tr>
                <th>Budget</th>
                <td><?php echo formatCurrency($activity['budget']); ?></td>
            </tr>
            <tr>
                <th>Progression</th>
                <td>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $activity['progress']; ?>%;">
                            <?php echo $activity['progress']; ?>%
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Statut</th>
                <td><?php echo getStatusBadge($activity['status'], 'activity'); ?></td>
            </tr>
            <tr>
                <th>Créé par</th>
                <td><?php echo escape($activity['created_by_name'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Date de création</th>
                <td><?php echo formatDate($activity['created_at']); ?></td>
            </tr>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

