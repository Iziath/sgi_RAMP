<?php
/**
 * Voir un bénéficiaire
 * RAMP-BENIN
 */

$pageTitle = 'Détails du Bénéficiaire';
require_once __DIR__ . '/../../includes/header.php';

$beneficiaireClass = new Beneficiaire();

$id = $_GET['id'] ?? 0;
$beneficiary = $beneficiaireClass->getById($id);

if (!$beneficiary) {
    redirectWithMessage('index.php', 'Bénéficiaire non trouvé', 'error');
}

$categories = ['individual' => 'Individuel', 'group' => 'Groupe', 'community' => 'Communauté'];
$genders = ['M' => 'Masculin', 'F' => 'Féminin', 'Other' => 'Autre'];
?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--color-blue);"><?php echo escape($beneficiary['first_name'] . ' ' . $beneficiary['last_name']); ?></h1>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary">Modifier</a>
            <a href="index.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations du Bénéficiaire</h3>
        </div>
        <table class="table">
            <tr>
                <th>Projet</th>
                <td>
                    <a href="../projets/view.php?id=<?php echo $beneficiary['project_id']; ?>" style="color: var(--color-blue);">
                        <?php echo escape($beneficiary['project_title'] ?? '-'); ?>
                    </a>
                </td>
            </tr>
            <tr>
                <th>Prénom</th>
                <td><?php echo escape($beneficiary['first_name']); ?></td>
            </tr>
            <tr>
                <th>Nom</th>
                <td><?php echo escape($beneficiary['last_name']); ?></td>
            </tr>
            <tr>
                <th>Genre</th>
                <td><?php echo $genders[$beneficiary['gender']] ?? $beneficiary['gender']; ?></td>
            </tr>
            <tr>
                <th>Date de naissance</th>
                <td><?php echo formatDate($beneficiary['date_of_birth']); ?></td>
            </tr>
            <tr>
                <th>Téléphone</th>
                <td><?php echo escape($beneficiary['phone'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo escape($beneficiary['email'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Adresse</th>
                <td><?php echo escape($beneficiary['address'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Catégorie</th>
                <td><?php echo $categories[$beneficiary['category']] ?? $beneficiary['category']; ?></td>
            </tr>
            <tr>
                <th>Date d'inscription</th>
                <td><?php echo formatDate($beneficiary['registration_date']); ?></td>
            </tr>
            <tr>
                <th>Statut</th>
                <td><?php echo getStatusBadge($beneficiary['status'], 'beneficiary'); ?></td>
            </tr>
            <?php if ($beneficiary['notes']): ?>
            <tr>
                <th>Notes</th>
                <td><?php echo escape($beneficiary['notes']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

