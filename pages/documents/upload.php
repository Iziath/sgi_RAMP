<?php
/**
 * Ajouter un document
 * RAMP-BENIN
 */

$pageTitle = 'Ajouter un Document';
require_once __DIR__ . '/../../includes/header.php';

$documentClass = new Document();
$error = '';
$success = '';
$uploadsDir = 'uploads/documents/';
$maxFileSize = 50 * 1024 * 1024; // 50 MB

// Créer le répertoire s'il n'existe pas
if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $uploadsDir)) {
    mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $uploadsDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Veuillez sélectionner un fichier valide';
    } elseif ($_FILES['document']['size'] > $maxFileSize) {
        $error = 'Le fichier est trop volumineux (max 50 MB)';
    } else {
        // Valider les données du formulaire
        if (empty($_POST['title'])) {
            $error = 'Le titre est requis';
        } elseif (empty($_POST['level'])) {
            $error = 'Le niveau est requis';
        } else {
            $file = $_FILES['document'];
            $filename = time() . '_' . basename($file['name']);
            $filepath = $uploadsDir . $filename;
            $fullpath = $_SERVER['DOCUMENT_ROOT'] . '/' . $filepath;
            
            if (move_uploaded_file($file['tmp_name'], $fullpath)) {
                $data = [
                    'title' => $_POST['title'],
                    'description' => $_POST['description'] ?? null,
                    'category' => $_POST['category'] ?? null,
                    'level' => $_POST['level'],
                    'department' => $_POST['department'] ?? null,
                    'file_path' => '/' . $filepath,
                    'file_name' => $file['name'],
                    'file_size' => $file['size'],
                    'file_type' => $file['type'],
                    'uploaded_by' => $_SESSION['user_id']
                ];
                
                if ($documentClass->create($data)) {
                    redirectWithMessage('index.php', 'Document ajouté avec succès', 'success');
                } else {
                    unlink($fullpath);
                    $error = 'Une erreur est survenue lors de l\'ajout du document';
                }
            } else {
                $error = 'Erreur lors du téléchargement du fichier';
            }
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
        <h1 style="margin: 0; color: var(--color-blue);">Ajouter un Document</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo escape($error); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Titre du document *</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Catégorie</label>
                    <input type="text" name="category" class="form-control" placeholder="Ex: Rapport, Manuel, etc.">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Niveau *</label>
                    <select name="level" class="form-control" required>
                        <option value="">Sélectionner un niveau</option>
                        <?php foreach ($levels as $key => $label): ?>
                            <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Département</label>
                <input type="text" name="department" class="form-control" placeholder="Ex: RH, Finance, Opérations">
            </div>
            
            <div class="form-group">
                <label class="form-label">Fichier *</label>
                <div style="border: 2px dashed var(--color-blue); border-radius: 8px; padding: 2rem; text-align: center; cursor: pointer;"
                     onclick="document.getElementById('fileInput').click()">
                    <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: var(--color-blue); margin-bottom: 0.5rem;"></i>
                    <p style="margin: 0.5rem 0;">Cliquez pour sélectionner un fichier</p>
                    <small style="color: #999;">Max 50 MB</small>
                    <input type="file" id="fileInput" name="document" class="form-control" required 
                           style="display: none;" onchange="updateFileName(this)">
                </div>
                <small id="fileName" style="color: #999; margin-top: 0.5rem;"></small>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Ajouter Document</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        fileName.textContent = 'Fichier sélectionné: ' + input.files[0].name + ' (' + 
                              (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB)';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
