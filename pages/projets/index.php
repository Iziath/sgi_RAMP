<?php
/**
 * Liste des projets
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Projets';
require_once __DIR__ . '/../../includes/header.php';

$projetClass = new Projet();
$projects = $projetClass->getAll();

// Récupérer les organisations pour le filtre
$db = Database::getInstance()->getConnection();
$organizations = $db->query("SELECT id, name FROM organizations WHERE status = 'active'")->fetchAll();
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Projets</h1>
        <a href="create.php" class="btn btn-success">Nouveau Projet</a>
    </div>
    
    <div class="card">
        <table id="projectsTable" class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Titre</th>
                    <th>Organisation</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Budget</th>
                    <th>Progression</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td><?php echo escape($project['code']); ?></td>
                        <td><?php echo escape($project['title']); ?></td>
                        <td><?php echo escape($project['organization_name'] ?? '-'); ?></td>
                        <td><?php echo formatDate($project['start_date']); ?></td>
                        <td><?php echo formatDate($project['end_date']); ?></td>
                        <td><?php echo formatCurrency($project['budget']); ?></td>
                        <td>
                            <div class="progress-container">
                                <div class="progress-wrapper">
                                    <div class="progress-bar" style="width: <?php echo $project['progress']; ?>%;">
                                        <span class="progress-text"><?php echo $project['progress']; ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="status-badge-wrapper">
                                <?php echo getStatusBadge($project['status'], 'project'); ?>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="view.php?id=<?php echo $project['id']; ?>" class="btn-icon btn-icon-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn-icon btn-icon-success" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $project['id']; ?>" class="btn-icon btn-icon-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')" title="Supprimer">
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
    $('#projectsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        order: [[0, 'desc']],
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

