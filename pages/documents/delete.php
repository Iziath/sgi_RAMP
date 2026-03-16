<?php
/**
 * Supprimer un document
 * RAMP-BENIN
 */

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$documentClass = new Document();
$id = $_GET['id'] ?? null;

if (!$id) {
    redirectWithMessage('../documents/index.php', 'Document non trouvé', 'error');
}

$document = $documentClass->getById($id);
if (!$document) {
    redirectWithMessage('../documents/index.php', 'Document non trouvé', 'error');
}

// Confirmer la suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    if ($documentClass->delete($id)) {
        redirectWithMessage('index.php', 'Document supprimé avec succès', 'success');
    } else {
        redirectWithMessage('index.php', 'Erreur lors de la suppression du document', 'error');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si utilisateur clique sur Annuler
    redirectWithMessage('index.php', 'Suppression annulée', 'info');
}

// Redirection directe si GET avec confirmation
if (isset($_GET['confirm']) && $_GET['confirm'] === '1') {
    if ($documentClass->delete($id)) {
        redirectWithMessage('index.php', 'Document supprimé avec succès', 'success');
    } else {
        redirectWithMessage('index.php', 'Erreur lors de la suppression du document', 'error');
    }
}

// Sinon afficher une page de confirmation
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Supprimer le Document</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <div class="card" style="max-width: 600px;">
        <div style="padding: 1.5rem; text-align: center;">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
            <h3 style="margin: 1rem 0; color: #333;">Confirmer la suppression</h3>
            <p style="color: #666; margin: 1rem 0;">Êtes-vous sûr de vouloir supprimer le document <strong><?php echo escape($document['title']); ?></strong> ?</p>
            <p style="color: #999; font-size: 0.9rem;">Cette action est irréversible et supprimera également le fichier associé.</p>
        </div>
        
        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2rem;">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
            <a href="delete.php?id=<?php echo $id; ?>&confirm=1" class="btn btn-danger">
                <i class="fas fa-trash"></i> Supprimer
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
