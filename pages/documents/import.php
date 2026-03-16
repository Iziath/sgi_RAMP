<?php
/**
 * Importer/Exporter des documents
 * RAMP-BENIN
 */

$pageTitle = 'Importer/Exporter Documents';
require_once __DIR__ . '/../../includes/header.php';

$documentClass = new Document();
$error = '';
$success = '';

// Traiter l'exportation
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $documents = $documentClass->getAll();
    
    // En-têtes CSV
    $headers = ['ID', 'Titre', 'Catégorie', 'Niveau', 'Département', 'Fichier', 'Taille (KB)', 'Type', 'Téléchargé par', 'Date d\'ajout'];
    
    $filename = 'documents_export_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    
    foreach ($documents as $doc) {
        fputcsv($output, [
            $doc['id'],
            $doc['title'],
            $doc['category'] ?? '',
            $doc['level'],
            $doc['department'] ?? '',
            $doc['file_name'],
            round($doc['file_size'] / 1024, 2),
            $doc['file_type'],
            $doc['uploaded_by_name'] ?? '',
            $doc['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

// Traiter l'importation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erreur lors du téléchargement du fichier';
    } else {
        $file = $_FILES['csv_file'];
        
        if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
            $error = 'Veuillez télécharger un fichier CSV';
        } else {
            $handle = fopen($file['tmp_name'], 'r');
            $header = fgetcsv($handle);
            
            $imported = 0;
            $errors = [];
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 4) continue;
                
                try {
                    // Les documents importés auront besoin d'une source physique
                    // Pour l'instant, on enregistre juste les métadonnées
                    $data = [
                        'title' => $row[1],
                        'category' => $row[2] ?: null,
                        'level' => $row[3],
                        'department' => $row[4] ?? null,
                        'description' => 'Importé le ' . date('d/m/Y H:i'),
                        'file_path' => '/uploads/documents/imported/',
                        'file_name' => 'document_' . $imported . '.pdf',
                        'file_size' => 0,
                        'file_type' => 'application/pdf',
                        'uploaded_by' => $_SESSION['user_id']
                    ];
                    
                    if ($documentClass->create($data)) {
                        $imported++;
                    }
                } catch (Exception $e) {
                    $errors[] = 'Ligne ' . ($imported + 2) . ': ' . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            if ($imported > 0) {
                $success = $imported . ' document(s) importé(s) avec succès';
            }
            if (!empty($errors)) {
                $error = implode('<br>', $errors);
            }
        }
    }
}

$stats = $documentClass->getStats();
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Importer/Exporter Documents</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <!-- Exporter -->
        <div class="card">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-download"></i> Exporter les Documents</h3>
            <p style="color: #666; margin-bottom: 1.5rem;">Téléchargez la liste de tous les documents en format CSV.</p>
            
            <div style="background-color: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; text-align: center;">
                <div style="font-size: 2rem; color: var(--color-green); margin-bottom: 0.5rem;">
                    <i class="fas fa-database"></i>
                </div>
                <p style="margin: 0; color: #666;">
                    <strong><?php echo $stats['total']; ?></strong> documents disponibles
                </p>
            </div>
            
            <a href="import.php?action=export" class="btn btn-success" style="width: 100%; margin-top: 1rem;">
                <i class="fas fa-download"></i> Exporter en CSV
            </a>
        </div>
        
        <!-- Importer -->
        <div class="card">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-upload"></i> Importer des Documents</h3>
            <p style="color: #666; margin-bottom: 1.5rem;">Importez des documents à partir d'un fichier CSV.</p>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Fichier CSV *</label>
                    <div style="border: 2px dashed var(--color-blue); border-radius: 8px; padding: 1.5rem; text-align: center; cursor: pointer;"
                         onclick="document.getElementById('csvInput').click()">
                        <i class="fas fa-file-csv" style="font-size: 2rem; color: var(--color-blue); margin-bottom: 0.5rem;"></i>
                        <p style="margin: 0.5rem 0;">Cliquez pour sélectionner un fichier CSV</p>
                        <input type="file" id="csvInput" name="csv_file" class="form-control" required 
                               accept=".csv" style="display: none;" onchange="updateCSVName(this)">
                    </div>
                    <small id="csvFileName" style="color: #999; margin-top: 0.5rem;"></small>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <i class="fas fa-upload"></i> Importer
                </button>
            </form>
            
            <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0;">
                <h4 style="margin-bottom: 1rem;">Format du CSV</h4>
                <pre style="background-color: #f5f5f5; padding: 1rem; border-radius: 4px; font-size: 0.85rem; overflow-x: auto;">ID,Titre,Catégorie,Niveau,Département,Fichier,Taille,Type,Auteur,Date</pre>
            </div>
        </div>
    </div>
</div>

<script>
function updateCSVName(input) {
    const csvFileName = document.getElementById('csvFileName');
    if (input.files && input.files[0]) {
        csvFileName.textContent = 'Fichier sélectionné: ' + input.files[0].name;
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
