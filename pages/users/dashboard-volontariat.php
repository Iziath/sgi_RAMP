<?php
/**
 * Tableau de bord Responsable Volontariat
 * RAMP-BENIN
 */

$pageTitle = 'Tableau de bord Volontariat';
require_once __DIR__ . '/../../includes/header.php';

// Vérifier que l'utilisateur est admin ou responsable volontariat
if (!isAdmin() && $_SESSION['user_role'] !== 'manager') {
    redirectWithMessage('dashboard.php', 'Accès refusé.', 'error');
}

$userProfileClass = new UserProfile();
$db = Database::getInstance()->getConnection();

// Récupérer tous les volontaires
$volontaires = $userProfileClass->getAll(['profile_type' => 'volontaire']);

// Statistiques détaillées
$stats = [
    'total' => count($volontaires),
    'en_attente' => count(array_filter($volontaires, fn($v) => $v['statut_validation'] === 'en_attente')),
    'valides' => count(array_filter($volontaires, fn($v) => $v['statut_validation'] === 'valide')),
    'refuses' => count(array_filter($volontaires, fn($v) => $v['statut_validation'] === 'refuse')),
    'heures_totales' => array_sum(array_column($volontaires, 'heures_effectuees')),
    'finissant_ce_mois' => [],
    'evaluations_en_retard' => 0
];

// Volontaires finissant ce mois
$now = new DateTime();
foreach ($volontaires as $v) {
    if ($v['date_fin']) {
        $fin = new DateTime($v['date_fin']);
        if ($fin->format('Y-m') === $now->format('Y-m')) {
            $stats['finissant_ce_mois'][] = $v;
        }
    }
}

// Évolution sur 6 mois
$evolution = [];
for ($i = 5; $i >= 0; $i--) {
    $date = new DateTime();
    $date->modify("-$i months");
    $mois = $date->format('Y-m');
    
    $count = $db->prepare("
        SELECT COUNT(*) 
        FROM user_profiles 
        WHERE profile_type = 'volontaire' 
        AND DATE_FORMAT(created_at, '%Y-%m') <= ?
    ");
    $count->execute([$mois]);
    $evolution[$date->format('M Y')] = $count->fetchColumn();
}

// Répartition par durée
$repartitionDuree = [
    '3' => count(array_filter($volontaires, fn($v) => $v['duree_engagement'] == 3)),
    '6' => count(array_filter($volontaires, fn($v) => $v['duree_engagement'] == 6)),
    '12' => count(array_filter($volontaires, fn($v) => $v['duree_engagement'] == 12))
];
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Tableau de bord Volontariat</h1>
        <div style="display: flex; gap: 0.5rem;">
            <a href="volontaires.php" class="btn btn-success"><i class="fas fa-users"></i> Gérer Volontaires</a>
            <a href="../../users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    
    <!-- Statistiques principales -->
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Volontaires</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ffc107;"><?php echo $stats['en_attente']; ?></div>
            <div class="stat-label">En attente validation</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: var(--color-green);"><?php echo $stats['valides']; ?></div>
            <div class="stat-label">Actifs</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: var(--color-blue);"><?php echo $stats['heures_totales']; ?></div>
            <div class="stat-label">Heures totales</div>
        </div>
    </div>
    
    <!-- Alertes -->
    <?php if ($stats['en_attente'] > 0 || count($stats['finissant_ce_mois']) > 0): ?>
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1rem;">⚠️ Alertes</h3>
        <?php if ($stats['en_attente'] > 0): ?>
            <div class="alert alert-warning">
                <strong><?php echo $stats['en_attente']; ?> volontaire(s) en attente de validation</strong>
                <a href="volontaires.php?statut_validation=en_attente" class="btn btn-sm btn-warning" style="margin-left: 1rem;">Voir</a>
            </div>
        <?php endif; ?>
        <?php if (count($stats['finissant_ce_mois']) > 0): ?>
            <div class="alert alert-info">
                <strong><?php echo count($stats['finissant_ce_mois']); ?> volontaire(s) finissant ce mois</strong>
                <ul style="margin-top: 0.5rem;">
                    <?php foreach ($stats['finissant_ce_mois'] as $v): ?>
                        <li><?php echo escape($v['full_name']); ?> - Fin: <?php echo formatDate($v['date_fin']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Graphiques -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="card">
            <h3 style="margin-bottom: 1rem;">Évolution (6 derniers mois)</h3>
            <canvas id="evolutionChart" height="200"></canvas>
        </div>
        <div class="card">
            <h3 style="margin-bottom: 1rem;">Répartition par durée</h3>
            <canvas id="dureeChart" height="200"></canvas>
        </div>
    </div>
    
    <!-- Liste des volontaires -->
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Liste des Volontaires</h3>
        <table id="volontairesTable" class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Durée</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Heures</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($volontaires as $v): ?>
                    <tr>
                        <td><?php echo escape($v['full_name']); ?></td>
                        <td><?php echo escape($v['email']); ?></td>
                        <td><?php echo $v['duree_engagement'] ? $v['duree_engagement'] . ' mois' : '-'; ?></td>
                        <td><?php echo $v['date_debut'] ? formatDate($v['date_debut']) : '-'; ?></td>
                        <td><?php echo $v['date_fin'] ? formatDate($v['date_fin']) : '-'; ?></td>
                        <td><?php echo $v['heures_effectuees'] ?? 0; ?>h</td>
                        <td>
                            <span class="badge <?php 
                                echo $v['statut_validation'] === 'valide' ? 'badge-success' : 
                                    ($v['statut_validation'] === 'refuse' ? 'badge-danger' : 'badge-warning'); 
                            ?>">
                                <?php 
                                echo $v['statut_validation'] === 'valide' ? 'Validé' : 
                                    ($v['statut_validation'] === 'refuse' ? 'Refusé' : 'En attente'); 
                                ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="volontaires.php" class="btn-icon btn-icon-primary" title="Voir"><i class="fas fa-eye"></i></a>
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
    $('#volontairesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
        order: [[0, 'asc']],
        responsive: true
    });
    
    // Graphique évolution
    const evolutionCtx = document.getElementById('evolutionChart');
    new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($evolution)); ?>,
            datasets: [{
                label: 'Nombre de volontaires',
                data: <?php echo json_encode(array_values($evolution)); ?>,
                borderColor: 'rgb(90, 106, 178)',
                backgroundColor: 'rgba(90, 106, 178, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Graphique répartition durée
    const dureeCtx = document.getElementById('dureeChart');
    new Chart(dureeCtx, {
        type: 'doughnut',
        data: {
            labels: ['3 mois', '6 mois', '12 mois'],
            datasets: [{
                data: [<?php echo $repartitionDuree['3']; ?>, <?php echo $repartitionDuree['6']; ?>, <?php echo $repartitionDuree['12']; ?>],
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(119, 157, 46, 0.8)',
                    'rgba(90, 106, 178, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

