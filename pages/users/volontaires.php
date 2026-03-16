<?php
/**
 * Gestion des Volontaires
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Volontaires';
require_once __DIR__ . '/../../includes/header.php';

// Vérifier que l'utilisateur est admin ou responsable volontariat
if (!isAdmin() && $_SESSION['user_role'] !== 'manager') {
    redirectWithMessage('dashboard.php', 'Accès refusé.', 'error');
}

$userProfileClass = new UserProfile();
$projetClass = new Projet();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'validate':
            $userProfileClass->validate($_POST['profile_id'], $_SESSION['user_id']);
            redirectWithMessage('volontaires.php', 'Volontaire validé avec succès', 'success');
            break;
            
        case 'refuse':
            $userProfileClass->refuse($_POST['profile_id'], $_SESSION['user_id'], $_POST['notes'] ?? null);
            redirectWithMessage('volontaires.php', 'Volontaire refusé', 'success');
            break;
            
        case 'update_heures':
            $userProfileClass->update($_POST['profile_id'], [
                'heures_effectuees' => $_POST['heures_effectuees']
            ]);
            redirectWithMessage('volontaires.php', 'Heures mises à jour', 'success');
            break;
    }
}

// Filtres
$filterStatut = $_GET['statut_validation'] ?? '';
$filterDuree = $_GET['duree_engagement'] ?? '';

$filters = ['profile_type' => 'volontaire'];
if ($filterStatut) $filters['statut_validation'] = $filterStatut;
if ($filterDuree) $filters['duree_engagement'] = $filterDuree;

$volontaires = $userProfileClass->getAll($filters);

// Statistiques
$stats = [
    'total' => count($volontaires),
    'en_attente' => count(array_filter($volontaires, fn($v) => $v['statut_validation'] === 'en_attente')),
    'valides' => count(array_filter($volontaires, fn($v) => $v['statut_validation'] === 'valide')),
    'finissant_ce_mois' => count(array_filter($volontaires, function($v) {
        if (!$v['date_fin']) return false;
        $fin = new DateTime($v['date_fin']);
        $now = new DateTime();
        return $fin->format('Y-m') === $now->format('Y-m');
    }))
];
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Volontaires</h1>
        <div style="display: flex; gap: 0.5rem;">
            <a href="dashboard-volontariat.php" class="btn btn-info"><i class="fas fa-chart-bar"></i> Tableau de bord</a>
            <a href="../../users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Volontaires</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ffc107;"><?php echo $stats['en_attente']; ?></div>
            <div class="stat-label">En attente</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: var(--color-green);"><?php echo $stats['valides']; ?></div>
            <div class="stat-label">Validés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: var(--color-blue);"><?php echo $stats['finissant_ce_mois']; ?></div>
            <div class="stat-label">Finissant ce mois</div>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Statut validation</label>
                <select name="statut_validation" class="form-control" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="en_attente" <?php echo $filterStatut === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="valide" <?php echo $filterStatut === 'valide' ? 'selected' : ''; ?>>Validé</option>
                    <option value="refuse" <?php echo $filterStatut === 'refuse' ? 'selected' : ''; ?>>Refusé</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Durée engagement</label>
                <select name="duree_engagement" class="form-control" onchange="this.form.submit()">
                    <option value="">Toutes</option>
                    <option value="3" <?php echo $filterDuree == '3' ? 'selected' : ''; ?>>3 mois</option>
                    <option value="6" <?php echo $filterDuree == '6' ? 'selected' : ''; ?>>6 mois</option>
                    <option value="12" <?php echo $filterDuree == '12' ? 'selected' : ''; ?>>12 mois</option>
                </select>
            </div>
            <a href="volontaires.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>
    
    <div class="card">
        <table id="volontairesTable" class="table">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Durée</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Heures</th>
                    <th>Statut validation</th>
                    <th>Charte signée</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($volontaires as $volontaire): ?>
                    <tr>
                        <td><?php echo escape($volontaire['full_name']); ?></td>
                        <td><?php echo escape($volontaire['email']); ?></td>
                        <td><?php echo $volontaire['duree_engagement'] ? $volontaire['duree_engagement'] . ' mois' : '-'; ?></td>
                        <td><?php echo $volontaire['date_debut'] ? formatDate($volontaire['date_debut']) : '-'; ?></td>
                        <td><?php echo $volontaire['date_fin'] ? formatDate($volontaire['date_fin']) : '-'; ?></td>
                        <td><?php echo $volontaire['heures_effectuees'] ?? 0; ?>h</td>
                        <td>
                            <?php
                            $validationBadges = [
                                'en_attente' => 'badge-warning',
                                'valide' => 'badge-success',
                                'refuse' => 'badge-danger'
                            ];
                            $validationLabels = [
                                'en_attente' => 'En attente',
                                'valide' => 'Validé',
                                'refuse' => 'Refusé'
                            ];
                            ?>
                            <span class="badge <?php echo $validationBadges[$volontaire['statut_validation']] ?? 'badge-secondary'; ?>">
                                <?php echo $validationLabels[$volontaire['statut_validation']] ?? $volontaire['statut_validation']; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($volontaire['charte_signee']): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Oui</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-times"></i> Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <?php if ($volontaire['statut_validation'] === 'en_attente'): ?>
                                    <button class="btn-icon btn-icon-success" onclick="validateVolontaire(<?php echo $volontaire['id']; ?>)" title="Valider">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-icon btn-icon-danger" onclick="refuseVolontaire(<?php echo $volontaire['id']; ?>)" title="Refuser">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="profile.php?id=<?php echo $volontaire['user_id']; ?>" class="btn-icon btn-icon-primary" title="Voir profil">
                                    <i class="fas fa-eye"></i>
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
    $('#volontairesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
        order: [[0, 'asc']],
        columnDefs: [{ targets: -1, orderable: false, searchable: false }],
        responsive: true
    });
});

function validateVolontaire(profileId) {
    if (confirm('Valider ce volontaire ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="validate">
            <input type="hidden" name="profile_id" value="${profileId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function refuseVolontaire(profileId) {
    const notes = prompt('Raison du refus (optionnel):');
    if (notes !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="refuse">
            <input type="hidden" name="profile_id" value="${profileId}">
            <input type="hidden" name="notes" value="${notes}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

