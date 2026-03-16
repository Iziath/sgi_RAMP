<?php
/**
 * Supprimer un bénéficiaire
 * RAMP-BENIN
 */

require_once __DIR__ . '/../../includes/header.php';

$beneficiaireClass = new Beneficiaire();

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    if ($beneficiaireClass->delete($id)) {
        redirectWithMessage('index.php', 'Bénéficiaire supprimé avec succès', 'success');
    } else {
        redirectWithMessage('index.php', 'Erreur lors de la suppression', 'error');
    }
}

$beneficiary = $beneficiaireClass->getById($id);
if (!$beneficiary) {
    redirectWithMessage('index.php', 'Bénéficiaire non trouvé', 'error');
}
?>

<div class="container-fluid">
    <div class="card">
        <h2 style="color: var(--color-blue); margin-bottom: 1rem;">Supprimer le Bénéficiaire</h2>
        <p>Êtes-vous sûr de vouloir supprimer le bénéficiaire <strong><?php echo escape($beneficiary['first_name'] . ' ' . $beneficiary['last_name']); ?></strong> ?</p>
        <p style="color: #dc3545;"><strong>Attention:</strong> Cette action est irréversible.</p>
        
        <form method="POST" action="" style="margin-top: 1.5rem;">
            <input type="hidden" name="confirm" value="1">
            <div style="display: flex; gap: 1rem;">
                <a href="index.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-danger">Confirmer la suppression</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

