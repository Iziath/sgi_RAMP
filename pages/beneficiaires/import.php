<?php
/**
 * Importer des bénéficiaires depuis un fichier Excel
 * RAMP-BENIN
 */

$pageTitle = 'Importer des Bénéficiaires';
require_once __DIR__ . '/../../includes/header.php';

$beneficiaireClass = new Beneficiaire();
$projetClass = new Projet();
$activiteClass = new Activite();
$error = '';
$success = '';
$importResults = null;

// Récupérer les projets et activités
$projects = $projetClass->getAll();
$allActivities = $activiteClass->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];
    
    // Vérifier que le fichier a été uploadé
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Vérifier l'extension
        if (!in_array($fileExtension, ['xlsx', 'xls', 'csv'])) {
            $error = 'Format de fichier non supporté. Veuillez utiliser un fichier Excel (.xlsx, .xls) ou CSV.';
        } else {
            // Vérifier si PhpSpreadsheet est disponible
            if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                $error = 'La bibliothèque PhpSpreadsheet n\'est pas installée. Veuillez installer via Composer: composer require phpoffice/phpspreadsheet';
            } else {
                try {
                    require_once __DIR__ . '/../../vendor/autoload.php';
                    
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                    
                    // Ignorer la première ligne (en-têtes)
                    array_shift($rows);
                    
                    $imported = 0;
                    $errors = [];
                    $projectId = $_POST['project_id'] ?? null;
                    $activityId = !empty($_POST['activity_id']) ? $_POST['activity_id'] : null;
                    
                    foreach ($rows as $index => $row) {
                        // Format attendu: Prénom, Nom, Genre, Date de naissance, Téléphone, Email, Adresse, Ville de provenance, Catégorie, Date d'inscription, Statut, Notes
                        if (empty($row[0]) || empty($row[1])) {
                            continue; // Ignorer les lignes vides
                        }
                        
                        $data = [
                            'project_id' => $projectId,
                            'activity_id' => $activityId,
                            'first_name' => trim($row[0] ?? ''),
                            'last_name' => trim($row[1] ?? ''),
                            'gender' => strtoupper(substr(trim($row[2] ?? 'M'), 0, 1)) ?: 'M',
                            'date_of_birth' => !empty($row[3]) ? date('Y-m-d', strtotime($row[3])) : null,
                            'phone' => !empty($row[4]) ? trim($row[4]) : null,
                            'email' => !empty($row[5]) ? filter_var(trim($row[5]), FILTER_VALIDATE_EMAIL) : null,
                            'address' => !empty($row[6]) ? trim($row[6]) : null,
                            'ville_de_provenance' => !empty($row[7]) ? trim($row[7]) : null,
                            'category' => !empty($row[8]) ? strtolower(trim($row[8])) : 'individual',
                            'registration_date' => !empty($row[9]) ? date('Y-m-d', strtotime($row[9])) : date('Y-m-d'),
                            'status' => !empty($row[10]) ? strtolower(trim($row[10])) : 'active',
                            'notes' => !empty($row[11]) ? trim($row[11]) : null
                        ];
                        
                        // Valider les données
                        if (empty($data['first_name']) || empty($data['last_name'])) {
                            $errors[] = "Ligne " . ($index + 2) . ": Prénom et Nom requis";
                            continue;
                        }
                        
                        if (!$projectId) {
                            $errors[] = "Ligne " . ($index + 2) . ": Projet requis";
                            continue;
                        }
                        
                        // Créer le bénéficiaire
                        if ($beneficiaireClass->create($data)) {
                            $imported++;
                        } else {
                            $errors[] = "Ligne " . ($index + 2) . ": Erreur lors de l'importation";
                        }
                    }
                    
                    $importResults = [
                        'imported' => $imported,
                        'errors' => $errors
                    ];
                    
                    if ($imported > 0) {
                        $success = "$imported bénéficiaire(s) importé(s) avec succès.";
                        if (!empty($errors)) {
                            $error = count($errors) . " erreur(s) lors de l'importation.";
                        }
                    } else {
                        $error = "Aucun bénéficiaire n'a été importé. " . implode(' ', $errors);
                    }
                    
                } catch (Exception $e) {
                    $error = 'Erreur lors de la lecture du fichier: ' . $e->getMessage();
                }
            }
        }
    } else {
        $error = 'Erreur lors du téléversement du fichier.';
    }
}
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Importer des Bénéficiaires</h1>
        <a href="index.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo escape($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo escape($success); ?></div>
    <?php endif; ?>
    
    <?php if ($importResults && !empty($importResults['errors'])): ?>
        <div class="alert alert-warning">
            <strong>Erreurs détectées:</strong>
            <ul>
                <?php foreach ($importResults['errors'] as $err): ?>
                    <li><?php echo escape($err); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h3 style="margin-bottom: 1rem;">Instructions</h3>
        <p>Le fichier Excel doit contenir les colonnes suivantes (dans l'ordre) :</p>
        <ol>
            <li><strong>Prénom</strong> (requis)</li>
            <li><strong>Nom</strong> (requis)</li>
            <li><strong>Genre</strong> (M, F ou Other)</li>
            <li><strong>Date de naissance</strong> (format: AAAA-MM-JJ)</li>
            <li><strong>Téléphone</strong></li>
            <li><strong>Email</strong></li>
            <li><strong>Adresse</strong></li>
            <li><strong>Ville de provenance</strong></li>
            <li><strong>Catégorie</strong> (individual, group, community)</li>
            <li><strong>Date d'inscription</strong> (format: AAAA-MM-JJ)</li>
            <li><strong>Statut</strong> (active, inactive, completed)</li>
            <li><strong>Notes</strong></li>
        </ol>
        <p><strong>Note:</strong> La première ligne sera considérée comme en-tête et sera ignorée.</p>
    </div>
    
    <div class="card">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Projet *</label>
                <select name="project_id" id="project_id" class="form-control" required>
                    <option value="">Sélectionner un projet</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>">
                            <?php echo escape($project['code'] . ' - ' . $project['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Activité</label>
                <select name="activity_id" id="activity_id" class="form-control">
                    <option value="">Sélectionner une activité (optionnel)</option>
                </select>
                <small class="form-text text-muted">Les activités seront chargées automatiquement après la sélection d'un projet</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Fichier Excel *</label>
                <input type="file" name="excel_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                <small class="form-text text-muted">Formats acceptés: .xlsx, .xls, .csv</small>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Importer</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const projectSelect = $('#project_id');
    const activitySelect = $('#activity_id');
    
    projectSelect.on('change', function() {
        const projectId = $(this).val();
        
        activitySelect.html('<option value="">Chargement...</option>');
        
        if (projectId) {
            $.ajax({
                url: '<?php echo BASE_URL; ?>/api/ajax_handler.php',
                method: 'GET',
                data: {
                    action: 'get_activities_by_project',
                    project_id: projectId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        activitySelect.html('<option value="">Sélectionner une activité (optionnel)</option>');
                        response.data.forEach(function(activity) {
                            activitySelect.append(
                                $('<option></option>')
                                    .attr('value', activity.id)
                                    .text(activity.title)
                            );
                        });
                    } else {
                        activitySelect.html('<option value="">Aucune activité disponible pour ce projet</option>');
                    }
                },
                error: function() {
                    activitySelect.html('<option value="">Erreur lors du chargement des activités</option>');
                }
            });
        } else {
            activitySelect.html('<option value="">Sélectionner d\'abord un projet</option>');
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

