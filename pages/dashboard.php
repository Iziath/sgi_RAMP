<?php
/**
 * Tableau de bord
 * RAMP-BENIN
 */

$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/../includes/header.php';

$projetClass = new Projet();
$activiteClass = new Activite();
$beneficiaireClass = new Beneficiaire();
$partenaireClass = new Partenaire();

// Statistiques générales
$projetStats = $projetClass->getStats();
$activiteStats = $activiteClass->getStats();
$beneficiaireStats = $beneficiaireClass->getStats();
$partenaireStats = $partenaireClass->getStats();

// Projets récents
$recentProjects = array_slice($projetClass->getAll(), 0, 5);

// Activités en cours
$allActivities = $activiteClass->getAll();
$activeActivities = array_filter($allActivities, function($a) {
    return $a['status'] === 'in_progress';
});
$activeActivities = array_slice($activeActivities, 0, 5);

// Données pour graphiques
$projectsByStatus = $projetClass->getStats()['by_status'] ?? [];
?>

<div class="container-fluid">
    <h1 style="margin-bottom: 2rem; color: var(--color-blue);">Tableau de bord</h1>
    
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?php echo $projetStats['total']; ?></div>
            <div class="stat-label">Projets</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $projetStats['by_status']['active'] ?? 0; ?></div>
            <div class="stat-label">Projets Actifs</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $beneficiaireStats['total']; ?></div>
            <div class="stat-label">Bénéficiaires</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $partenaireStats['total']; ?></div>
            <div class="stat-label">Partenaires</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $activiteStats['total']; ?></div>
            <div class="stat-label">Activités</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo formatCurrency($projetStats['total_budget']); ?></div>
            <div class="stat-label">Budget Total</div>
        </div>
    </div>
    
    <!-- Graphiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Projets par Statut</h3>
            </div>
            <canvas id="projectsChart" style="max-height: 300px;"></canvas>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Activités par Statut</h3>
            </div>
            <canvas id="activitiesChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <!-- Projets récents -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Projets Récents</h3>
            <a href="projets/index.php" class="btn btn-primary btn-sm">Voir tout</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Titre</th>
                    <th>Organisation</th>
                    <th>Statut</th>
                    <th>Progression</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentProjects)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Aucun projet trouvé</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentProjects as $project): ?>
                        <tr>
                            <td><?php echo escape($project['code']); ?></td>
                            <td>
                                <a href="projets/view.php?id=<?php echo $project['id']; ?>" style="color: var(--color-blue); text-decoration: none;">
                                    <?php echo escape($project['title']); ?>
                                </a>
                            </td>
                            <td><?php echo escape($project['organization_name'] ?? '-'); ?></td>
                            <td><?php echo getStatusBadge($project['status'], 'project'); ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $project['progress']; ?>%;">
                                        <?php echo $project['progress']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo formatDate($project['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Activités en cours -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activités en Cours</h3>
            <a href="activites/index.php" class="btn btn-primary btn-sm">Voir tout</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Projet</th>
                    <th>Date prévue</th>
                    <th>Progression</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activeActivities)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucune activité en cours</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activeActivities as $activity): ?>
                        <tr>
                            <td><?php echo escape($activity['title']); ?></td>
                            <td><?php echo escape($activity['project_title'] ?? '-'); ?></td>
                            <td><?php echo formatDate($activity['planned_end_date']); ?></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $activity['progress']; ?>%;">
                                        <?php echo $activity['progress']; ?>%
                                    </div>
                                </div>
                            </td>
                            <td><?php echo getStatusBadge($activity['status'], 'activity'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Graphique des projets par statut
const projectsCtx = document.getElementById('projectsChart').getContext('2d');
new Chart(projectsCtx, {
    type: 'doughnut',
    data: {
        labels: ['Planification', 'Actif', 'Terminé', 'Annulé'],
        datasets: [{
            data: [
                <?php echo $projectsByStatus['planning'] ?? 0; ?>,
                <?php echo $projectsByStatus['active'] ?? 0; ?>,
                <?php echo $projectsByStatus['completed'] ?? 0; ?>,
                <?php echo $projectsByStatus['cancelled'] ?? 0; ?>
            ],
            backgroundColor: [
                '#ffc107',
                '#779D2E',
                '#5A6AB2',
                '#dc3545'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Graphique des activités par statut
const activitiesCtx = document.getElementById('activitiesChart').getContext('2d');
const activiteStats = <?php echo json_encode($activiteStats['by_status'] ?? []); ?>;
new Chart(activitiesCtx, {
    type: 'bar',
    data: {
        labels: ['Planifié', 'En cours', 'Terminé', 'Annulé'],
        datasets: [{
            label: 'Nombre d\'activités',
            data: [
                activiteStats['planned'] || 0,
                activiteStats['in_progress'] || 0,
                activiteStats['completed'] || 0,
                activiteStats['cancelled'] || 0
            ],
            backgroundColor: '#5A6AB2'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

