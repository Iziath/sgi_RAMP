<?php
/**
 * Gestion des documents
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Documents';
require_once __DIR__ . '/../../includes/header.php';
require "../../classes/Document.php";

$documentClass = new Document();

// Récupérer les filtres
$filterCategory = $_GET['category'] ?? null;
$filterLevel = $_GET['level'] ?? null;

// Récupérer les documents avec filtres
$documents = $documentClass->getAll($filterCategory, $filterLevel);

// Récupérer les catégories et départements pour les filtres
// $categories = $documentClass->getCategories();
// $departments = $documentClass->getDepartments();
// $stats = $documentClass->getStats();

$levels = [
    'departement' => 'Département',
    'regional' => 'Régional',
    'sous_regional' => 'Sous-Régional'
];
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block" style="font-size:35px">Gestion des Documents</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="import.php" class="btn btn-info" style="background-color:#5A6AB2"><i class="fas fa-upload"></i> Importer</a>
            <a href="upload.php" class="btn btn-success"><i class="fas fa-plus"></i> Ajouter Document</a>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-grid" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Documents Total</div>
        </div>
        <?php foreach ($levels as $key => $label): ?>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['by_level'][$key] ?? 0; ?></div>
                <div class="stat-label"><?php echo $label; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Filtres de recherche -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Filtrer par Catégorie</label>
                <select name="category" id="filter_category" class="form-control">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo escape($cat); ?>" <?php echo ($filterCategory === $cat) ? 'selected' : ''; ?>>
                            <?php echo escape($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Filtrer par Niveau</label>
                <select name="level" id="filter_level" class="form-control">
                    <option value="">Tous les niveaux</option>
                    <?php foreach ($levels as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo ($filterLevel === $key) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Rechercher</label>
                <input type="text" name="search" class="form-control" placeholder="Titre du document...">
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Réinitialiser</a>
            </div>
        </form>
    </div>
    
    <div class="card">
        <table id="documentsTable" class="table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Niveau</th>
                    <th>Département</th>
                    <th>Fichier</th>
                    <th>Téléchargé par</th>
                    <th>Date d'ajout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?php echo escape($doc['title']); ?></td>
                        <td><span class="badge badge-info"><?php echo escape($doc['category'] ?? '-'); ?></span></td>
                        <td><span class="badge badge-success"><?php echo isset($levels[$doc['level']]) ? $levels[$doc['level']] : escape($doc['level']); ?></span></td>
                        <td><?php echo escape($doc['department'] ?? '-'); ?></td>
                        <td>
                            <small><?php echo escape($doc['file_name']); ?></small><br>
                            <small style="color: #999;"><?php echo number_format($doc['file_size'] / 1024, 2); ?> KB</small>
                        </td>
                        <td><?php echo escape($doc['uploaded_by_name'] ?? '-'); ?></td>
                        <td><?php echo formatDate($doc['created_at']); ?></td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="<?php echo $doc['file_path']; ?>" class="btn-icon btn-icon-primary" title="Télécharger" download>
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $doc['id']; ?>" class="btn-icon btn-icon-success" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $doc['id']; ?>" class="btn-icon btn-icon-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($documents)): ?>
            <div style="padding: 2rem; text-align: center; color: #999;">
                <p>Aucun document trouvé</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#documentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
