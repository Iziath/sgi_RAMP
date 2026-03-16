<?php
/**
 * Supprimer un projet
 * RAMP-BENIN
 */

require_once __DIR__ . '/../../includes/header.php';

$projetClass = new Projet();

$id = $_GET['id'] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    if ($projetClass->delete($id)) {
        redirectWithMessage('index.php', 'Projet supprimé avec succès', 'success');
    } else {
        redirectWithMessage('index.php', 'Erreur lors de la suppression', 'error');
    }
}

$project = $projetClass->getById($id);
if (!$project) {
    redirectWithMessage('index.php', 'Projet non trouvé', 'error');
}
?>

<div class="container-fluid">
    <div class="card">
        <h2 style="color: var(--color-blue); margin-bottom: 1rem;">Supprimer le Projet</h2>
        <p>Êtes-vous sûr de vouloir supprimer le projet <strong><?php echo escape($project['title']); ?></strong> ?</p>
        <p style="color: #dc3545;"><strong>Attention:</strong> Cette action est irréversible. Toutes les activités et bénéficiaires associés seront également supprimés.</p>
        
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

