<?php
/**
 * Modifier un document
 * RAMP-BENIN
 */

$pageTitle = 'Modifier un Document';
require_once __DIR__ . '/../../includes/header.php';

$documentClass = new Document();
$id = $_GET['id'] ?? null;
$error = '';

if (!$id) {
    redirectWithMessage('index.php', 'Document non trouvé', 'error');
}

$document = $documentClass->getById($id);
if (!$document) {
    redirectWithMessage('index.php', 'Document non trouvé', 'error');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['title'])) {
        $error = 'Le titre est requis';
    } elseif (empty($_POST['level'])) {
        $error = 'Le niveau est requis';
    } else {
        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? null,
            'category' => $_POST['category'] ?? null,
            'level' => $_POST['level'],
            'department' => $_POST['department'] ?? null
        ];
        
        if ($documentClass->update($id, $data)) {
            redirectWithMessage('index.php', 'Document modifié avec succès', 'success');
        } else {
            $error = 'Une erreur est survenue lors de la modification du document';
        }
    }
}

$levels = [
    'departement' => 'Département',
    'regional' => 'Régional',
    'sous_regional' => 'Sous-Régional'
];
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Modifier le Document</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo escape($error); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Titre du document *</label>
                <input type="text" name="title" class="form-control" value="<?php echo escape($document['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo escape($document['description'] ?? ''); ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Catégorie</label>
                    <input type="text" name="category" class="form-control" value="<?php echo escape($document['category'] ?? ''); ?>" placeholder="Ex: Rapport, Manuel, etc.">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Niveau *</label>
                    <select name="level" class="form-control" required>
                        <option value="">Sélectionner un niveau</option>
                        <?php foreach ($levels as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($document['level'] === $key) ? 'selected' : ''; ?>>
                                <?php echo $label; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Département</label>
                <input type="text" name="department" class="form-control" value="<?php echo escape($document['department'] ?? ''); ?>" placeholder="Ex: RH, Finance, Opérations">
            </div>
            
            <div class="card" style="background-color: #f8f9fa; margin-bottom: 1.5rem;">
                <h4>Informations du fichier</h4>
                <p><strong>Nom:</strong> <?php echo escape($document['file_name']); ?></p>
                <p><strong>Taille:</strong> <?php echo number_format($document['file_size'] / 1024, 2); ?> KB</p>
                <p><strong>Type:</strong> <?php echo escape($document['file_type']); ?></p>
                <a href="<?php echo $document['file_path']; ?>" class="btn btn-sm btn-primary" download>
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
