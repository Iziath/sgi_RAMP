<?php
/**
 * Supprimer une activité
 * RAMP-BENIN
 */

require_once __DIR__ . '/../../includes/header.php';

$activiteClass = new Activite();

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    if ($activiteClass->delete($id)) {
        redirectWithMessage('index.php', 'Activité supprimée avec succès', 'success');
    } else {
        redirectWithMessage('index.php', 'Erreur lors de la suppression', 'error');
    }
}

$activity = $activiteClass->getById($id);
if (!$activity) {
    redirectWithMessage('index.php', 'Activité non trouvée', 'error');
}
?>

<div class="container-fluid">
    <div class="card">
        <h2 style="color: var(--color-blue); margin-bottom: 1rem;">Supprimer l'Activité</h2>
        <p>Êtes-vous sûr de vouloir supprimer l'activité <strong><?php echo escape($activity['title']); ?></strong> ?</p>
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

