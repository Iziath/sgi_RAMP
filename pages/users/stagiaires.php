<?php
/**
 * Gestion des Stagiaires
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Stagiaires';
require_once __DIR__ . '/../../includes/header.php';

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
            redirectWithMessage('stagiaires.php', 'Stagiaire validé avec succès', 'success');
            break;
            
        case 'refuse':
            $userProfileClass->refuse($_POST['profile_id'], $_SESSION['user_id'], $_POST['notes'] ?? null);
            redirectWithMessage('stagiaires.php', 'Stagiaire refusé', 'success');
            break;
    }
}

// Filtres
$filterStatut = $_GET['statut_validation'] ?? '';
$filterProjet = $_GET['projet_id'] ?? '';

$filters = ['profile_type' => 'stagiaire'];
if ($filterStatut) $filters['statut_validation'] = $filterStatut;

$stagiaires = $userProfileClass->getAll($filters);
if ($filterProjet) {
    $stagiaires = array_filter($stagiaires, fn($s) => $s['projet_affectation_id'] == $filterProjet);
}

$projets = $projetClass->getAll();

// Statistiques
$stats = [
    'total' => count($stagiaires),
    'en_attente' => count(array_filter($stagiaires, fn($s) => $s['statut_validation'] === 'en_attente')),
    'valides' => count(array_filter($stagiaires, fn($s) => $s['statut_validation'] === 'valide')),
    'rapport_rendu' => count(array_filter($stagiaires, fn($s) => $s['rapport_rendu']))
];
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Stagiaires</h1>
        <a href="../../users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Total Stagiaires</div>
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
            <div class="stat-value" style="color: var(--color-blue);"><?php echo $stats['rapport_rendu']; ?></div>
            <div class="stat-label">Rapport rendu</div>
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
                <label class="form-label">Projet</label>
                <select name="projet_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Tous les projets</option>
                    <?php foreach ($projets as $projet): ?>
                        <option value="<?php echo $projet['id']; ?>" <?php echo $filterProjet == $projet['id'] ? 'selected' : ''; ?>>
                            <?php echo escape($projet['code'] . ' - ' . $projet['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <a href="stagiaires.php" class="btn btn-secondary">Réinitialiser</a>
        </form>
    </div>
    
    <div class="card">
        <table id="stagiairesTable" class="table">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>École/Université</th>
                    <th>Niveau</th>
                    <th>Projet</th>
                    <th>Tuteur</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Convention</th>
                    <th>Rapport</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stagiaires as $stagiaire): ?>
                    <tr>
                        <td><?php echo escape($stagiaire['full_name']); ?></td>
                        <td><?php echo escape($stagiaire['email']); ?></td>
                        <td><?php echo escape($stagiaire['ecole_universite'] ?? '-'); ?></td>
                        <td><?php echo escape($stagiaire['niveau_etudes'] ?? '-'); ?></td>
                        <td><?php echo escape($stagiaire['projet_title'] ?? '-'); ?></td>
                        <td><?php echo escape($stagiaire['tuteur_name'] ?? '-'); ?></td>
                        <td><?php echo $stagiaire['date_debut'] ? formatDate($stagiaire['date_debut']) : '-'; ?></td>
                        <td><?php echo $stagiaire['date_fin'] ? formatDate($stagiaire['date_fin']) : '-'; ?></td>
                        <td>
                            <?php if ($stagiaire['convention_signee']): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Oui</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-times"></i> Non</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($stagiaire['rapport_rendu']): ?>
                                <span class="badge badge-success"><i class="fas fa-check"></i> Oui</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><i class="fas fa-times"></i> Non</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?php 
                                echo $stagiaire['statut_validation'] === 'valide' ? 'badge-success' : 
                                    ($stagiaire['statut_validation'] === 'refuse' ? 'badge-danger' : 'badge-warning'); 
                            ?>">
                                <?php 
                                echo $stagiaire['statut_validation'] === 'valide' ? 'Validé' : 
                                    ($stagiaire['statut_validation'] === 'refuse' ? 'Refusé' : 'En attente'); 
                                ?>
                            </span>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <?php if ($stagiaire['statut_validation'] === 'en_attente'): ?>
                                    <button class="btn-icon btn-icon-success" onclick="validateStagiaire(<?php echo $stagiaire['id']; ?>)" title="Valider"><i class="fas fa-check"></i></button>
                                    <button class="btn-icon btn-icon-danger" onclick="refuseStagiaire(<?php echo $stagiaire['id']; ?>)" title="Refuser">✗</button>
                                <?php endif; ?>
                                <a href="profile.php?id=<?php echo $stagiaire['user_id']; ?>" class="btn-icon btn-icon-primary" title="Voir profil"><i class="fas fa-eye"></i></a>
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
    $('#stagiairesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
        order: [[0, 'asc']],
        columnDefs: [{ targets: -1, orderable: false, searchable: false }],
        responsive: true
    });
});

function validateStagiaire(profileId) {
    if (confirm('Valider ce stagiaire ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input type="hidden" name="action" value="validate"><input type="hidden" name="profile_id" value="${profileId}">`;
        document.body.appendChild(form);
        form.submit();
    }
}

function refuseStagiaire(profileId) {
    const notes = prompt('Raison du refus (optionnel):');
    if (notes !== null) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input type="hidden" name="action" value="refuse"><input type="hidden" name="profile_id" value="${profileId}"><input type="hidden" name="notes" value="${notes}">`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

