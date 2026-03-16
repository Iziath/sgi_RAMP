<?php
/**
 * Liste des activités
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Activités';
require_once __DIR__ . '/../../includes/header.php';

$activiteClass = new Activite();
$activities = $activiteClass->getAll();
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Activités</h1>
        <a href="create.php" class="btn btn-success">Nouvelle Activité</a>
    </div>
    
    <div class="card">
        <table id="activitiesTable" class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Projet</th>
                    <th>Date prévue début</th>
                    <th>Date prévue fin</th>
                    <th>Budget</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo escape($activity['title']); ?></td>
                        <td><?php echo escape($activity['project_title'] ?? '-'); ?></td>
                        <td><?php echo formatDate($activity['planned_start_date']); ?></td>
                        <td><?php echo formatDate($activity['planned_end_date']); ?></td>
                        <td><?php echo formatCurrency($activity['budget']); ?></td>
                        <td>
                            <div class="progress-container">
                                <div class="progress-wrapper">
                                    <div class="progress-bar" style="width: <?php echo $activity['progress']; ?>%;">
                                        <span class="progress-text"><?php echo $activity['progress']; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="status-badge-wrapper">
                                <?php echo getStatusBadge($activity['status'], 'activity'); ?>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="view.php?id=<?php echo $activity['id']; ?>" class="btn-icon btn-icon-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $activity['id']; ?>" class="btn-icon btn-icon-success" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $activity['id']; ?>" class="btn-icon btn-icon-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#activitiesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        order: [[0, 'asc']],
        columnDefs: [
            { 
                targets: -1, 
                orderable: false,
                searchable: false,
                className: 'actions-cell'
            }
        ],
        responsive: true,
        scrollX: true
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

