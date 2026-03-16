<?php
/**
 * Profil utilisateur détaillé
 * RAMP-BENIN
 */

$pageTitle = 'Profil Utilisateur';
require_once __DIR__ . '/../../includes/header.php';

$userId = $_GET['id'] ?? 0;
$userClass = new User();
$userProfileClass = new UserProfile();
$userActivityClass = new UserActivity();
$userEvaluationClass = new UserEvaluation();

$user = $userClass->getById($userId);
if (!$user) {
    redirectWithMessage('../../users.php', 'Utilisateur non trouvé', 'error');
}

// Récupérer le profil si c'est un volontaire, stagiaire ou bénévole
$profile = null;
$profileTypes = ['volontaire', 'stagiaire', 'benevole'];
if (in_array($user['role'], $profileTypes)) {
    $profile = $userProfileClass->getByUserId($userId, $user['role']);
}

// Récupérer les activités
$activities = $userActivityClass->getByUserId($userId);

// Récupérer les évaluations
$evaluations = $profile ? $userEvaluationClass->getByUserId($userId) : [];
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Profil de <?php echo escape($user['full_name']); ?></h1>
        <a href="../../users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
        <!-- Informations personnelles -->
        <div class="card">
            <h3 style="margin-bottom: 1rem;">Informations personnelles</h3>
            <div style="margin-bottom: 1rem;">
                <strong>Nom complet:</strong><br>
                <?php echo escape($user['full_name']); ?>
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Email:</strong><br>
                <?php echo escape($user['email']); ?>
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Type:</strong><br>
                <span class="badge badge-info"><?php echo escape($user['role']); ?></span>
            </div>
            <?php if ($user['competences']): ?>
            <div style="margin-bottom: 1rem;">
                <strong>Compétences:</strong><br>
                <?php echo nl2br(escape($user['competences'])); ?>
            </div>
            <?php endif; ?>
            <?php if ($user['domaines_interet']): ?>
            <div style="margin-bottom: 1rem;">
                <strong>Domaines d'intérêt:</strong><br>
                <?php echo nl2br(escape($user['domaines_interet'])); ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Informations spécifiques au profil -->
        <?php if ($profile): ?>
        <div class="card">
            <h3 style="margin-bottom: 1rem;">Informations <?php echo ucfirst($user['role']); ?></h3>
            <?php if ($user['role'] === 'volontaire'): ?>
                <div style="margin-bottom: 1rem;">
                    <strong>Durée engagement:</strong> <?php echo $profile['duree_engagement'] ?? '-'; ?> mois
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Heures effectuées:</strong> <?php echo $profile['heures_effectuees'] ?? 0; ?>h
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Charte signée:</strong> 
                    <?php echo $profile['charte_signee'] ? '<i class="fas fa-check"></i> Oui' : '<i class="fas fa-times"></i> Non'; ?>
                </div>
            <?php elseif ($user['role'] === 'stagiaire'): ?>
                <div style="margin-bottom: 1rem;">
                    <strong>École/Université:</strong> <?php echo escape($profile['ecole_universite'] ?? '-'); ?>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Niveau:</strong> <?php echo escape($profile['niveau_etudes'] ?? '-'); ?>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Convention signée:</strong> 
                    <?php echo $profile['convention_signee'] ? '<i class="fas fa-check"></i> Oui' : '<i class="fas fa-times"></i> Non'; ?>
                </div>
            <?php elseif ($user['role'] === 'benevole'): ?>
                <div style="margin-bottom: 1rem;">
                    <strong>Type engagement:</strong> 
                    <?php echo $profile['type_engagement'] === 'recurrent' ? 'Récurrent' : 'Ponctuel'; ?>
                </div>
                <div style="margin-bottom: 1rem;">
                    <strong>Badge reconnaissance:</strong> 
                    <?php echo $profile['badge_reconnaissance'] ? '<i class="fas fa-check"></i> Oui' : '<i class="fas fa-times"></i> Non'; ?>
                </div>
            <?php endif; ?>
            <div style="margin-bottom: 1rem;">
                <strong>Date début:</strong> <?php echo $profile['date_debut'] ? formatDate($profile['date_debut']) : '-'; ?>
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Date fin:</strong> <?php echo $profile['date_fin'] ? formatDate($profile['date_fin']) : '-'; ?>
            </div>
            <div style="margin-bottom: 1rem;">
                <strong>Statut validation:</strong> 
                <span class="badge <?php 
                    echo $profile['statut_validation'] === 'valide' ? 'badge-success' : 
                        ($profile['statut_validation'] === 'refuse' ? 'badge-danger' : 'badge-warning'); 
                ?>">
                    <?php echo $profile['statut_validation'] === 'valide' ? 'Validé' : 
                        ($profile['statut_validation'] === 'refuse' ? 'Refusé' : 'En attente'); ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Historique des activités -->
    <?php if (!empty($activities)): ?>
    <div class="card" style="margin-top: 1.5rem;">
        <h3 style="margin-bottom: 1rem;">Historique des activités</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activité</th>
                    <th>Projet</th>
                    <th>Heures</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activities as $activity): ?>
                    <tr>
                        <td><?php echo formatDate($activity['date_participation']); ?></td>
                        <td><?php echo escape($activity['activity_title'] ?? '-'); ?></td>
                        <td><?php echo escape($activity['project_title'] ?? '-'); ?></td>
                        <td><?php echo $activity['heures']; ?>h</td>
                        <td>
                            <span class="badge badge-info"><?php echo ucfirst($activity['statut']); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

