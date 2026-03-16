<?php
/**
 * Voir un partenaire
 * RAMP-BENIN
 */

$pageTitle = 'Détails du Partenaire';
require_once __DIR__ . '/../../includes/header.php';

$partenaireClass = new Partenaire();

$id = $_GET['id'] ?? 0;
$partner = $partenaireClass->getById($id);

if (!$partner) {
    redirectWithMessage('index.php', 'Partenaire non trouvé', 'error');
}

$types = [
    'government' => 'Gouvernement',
    'ngo' => 'ONG',
    'private' => 'Privé',
    'international' => 'International'
];
?>

<div class="container-fluid">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="color: var(--color-blue);"><?php echo escape($partner['name']); ?></h1>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary">Modifier</a>
            <a href="index.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Informations du Partenaire</h3>
        </div>
        <table class="table">
            <tr>
                <th>Nom</th>
                <td><?php echo escape($partner['name']); ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?php echo $types[$partner['type']] ?? $partner['type']; ?></td>
            </tr>
            <tr>
                <th>Personne de contact</th>
                <td><?php echo escape($partner['contact_person'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Téléphone</th>
                <td><?php echo escape($partner['phone'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo escape($partner['email'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Adresse</th>
                <td><?php echo escape($partner['address'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th>Site web</th>
                <td>
                    <?php if ($partner['website']): ?>
                        <a href="<?php echo escape($partner['website']); ?>" target="_blank" style="color: var(--color-blue);">
                            <?php echo escape($partner['website']); ?>
                        </a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo escape($partner['description'] ?? 'Aucune description'); ?></td>
            </tr>
            <tr>
                <th>Statut</th>
                <td><?php echo getStatusBadge($partner['status'], 'general'); ?></td>
            </tr>
            <tr>
                <th>Date de création</th>
                <td><?php echo formatDate($partner['created_at']); ?></td>
            </tr>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

