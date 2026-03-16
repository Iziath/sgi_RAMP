<?php
/**
 * Gestion des Bénévoles
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Bénévoles';
require_once __DIR__ . '/../../includes/header.php';

if (!isAdmin() && $_SESSION['user_role'] !== 'manager') {
    redirectWithMessage('dashboard.php', 'Accès refusé.', 'error');
}

$userProfileClass = new UserProfile();
$userActivityClass = new UserActivity();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'attribuer_badge':
            $userProfileClass->update($_POST['profile_id'], [
                'badge_reconnaissance' => true
            ]);
            redirectWithMessage('benevoles.php', 'Badge attribué avec succès', 'success');
            break;
    }
}

// Filtres
$filterType = $_GET['type_engagement'] ?? '';

$filters = ['profile_type' => 'benevole'];
if ($filterType) $filters['type_engagement'] = $filterType;

$benevoles = $userProfileClass->getAll($filters);

// Calculer les heures totales pour chaque bénévole
foreach ($benevoles as &$benevole) {
    $benevole['heures_totales'] = $userActivityClass->getTotalHeures($benevole['user_id']);
}

// Statistiques
$stats = [
    'total' => count($benevoles),
    'ponctuels' => count(array_filter($benevoles, fn($b) => $b['type_engagement'] === 'ponctuel')),
    'recurrents' => count(array_filter($benevoles, fn($b) => $b['type_engagement'] === 'recurrent')),
    'avec_badge' => count(array_filter($benevoles, fn($b) => $b['badge_reconnaissance']))
];
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Bénévoles</h1>
        <a href="../../users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Bénévoles</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: var(--color-blue);"><?php echo $stats['ponctuels']; ?></div>
            <div class="stat-label">Ponctuels</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: var(--color-green);"><?php echo $stats['recurrents']; ?></div>
            <div class="stat-label">Récurrents</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color: #ffc107;"><?php echo $stats['avec_badge']; ?></div>
            <div class="stat-label">Avec badge</div>
        </div>
    </div>
    
    <!-- Filtres -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end;">
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <label class="form-label">Type d'engagement</label>
                <select name="type_engagement" class="form-control" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="ponctuel" <?php echo $filterType === 'ponctuel' ? 'selected' : ''; ?>>Ponctuel</option>
                    <option value="recurrent" <?php echo $filterType === 'recurrent' ? 'selected' : ''; ?>>Récurrent</option>
                </select>
            </div>
            <a href="benevoles.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>
    
    <div class="card">
        <table id="benevolesTable" class="table">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Type engagement</th>
                    <th>Heures totales</th>
                    <th>Badge</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($benevoles as $benevole): ?>
                    <tr>
                        <td><?php echo escape($benevole['full_name']); ?></td>
                        <td><?php echo escape($benevole['email']); ?></td>
                        <td>
                            <span class="badge <?php echo $benevole['type_engagement'] === 'recurrent' ? 'badge-success' : 'badge-info'; ?>">
                                <?php echo $benevole['type_engagement'] === 'recurrent' ? 'Récurrent' : 'Ponctuel'; ?>
                            </span>
                        </td>
                        <td><?php echo number_format($benevole['heures_totales'], 1); ?>h</td>
                        <td>
                            <?php if ($benevole['badge_reconnaissance']): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Oui</span>
                            <?php else: ?>
                                <button class="btn btn-sm btn-warning" onclick="attribuerBadge(<?php echo $benevole['id']; ?>)">Attribuer</button>
                            <?php endif; ?>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="profile.php?id=<?php echo $benevole['user_id']; ?>" class="btn-icon btn-icon-primary" title="Voir profil"><i class="fas fa-eye"></i></a>
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
    $('#benevolesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
        order: [[3, 'desc']], // Trier par heures
        columnDefs: [{ targets: -1, orderable: false, searchable: false }],
        responsive: true
    });
});

function attribuerBadge(profileId) {
    if (confirm('Attribuer un badge de reconnaissance à ce bénévole ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="attribuer_badge">
            <input type="hidden" name="profile_id" value="${profileId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

