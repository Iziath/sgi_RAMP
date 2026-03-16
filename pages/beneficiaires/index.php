<?php
/**
 * Liste des bénéficiaires
 * RAMP-BENIN
 */

$pageTitle = 'Gestion des Bénéficiaires';
require_once __DIR__ . '/../../includes/header.php';

$beneficiaireClass = new Beneficiaire();
$projetClass = new Projet();
$activiteClass = new Activite();

// Récupérer les filtres
$filterProjectId = $_GET['project_id'] ?? null;
$filterActivityId = $_GET['activity_id'] ?? null;

// Récupérer les bénéficiaires avec filtres
$beneficiaries = $beneficiaireClass->getAll($filterProjectId, $filterActivityId);

// Récupérer tous les projets et activités pour les filtres
$projects = $projetClass->getAll();
$allActivities = $activiteClass->getAll();
?>

<div class="container-fluid">
    <div class="page-header-actions">
        <h1 class="page-title-block">Gestion des Bénéficiaires</h1>
        <div style="display: flex; gap: 1rem;">
            <a href="import.php" class="btn btn-info"><i class="fas fa-download"></i> Importer Excel</a>
            <a href="create.php" class="btn btn-success"><i class="fas fa-plus"></i> Nouveau Bénéficiaire</a>
        </div>
    </div>
    
    <!-- Filtres de recherche -->
    <div class="card" style="margin-bottom: 1.5rem;">
        <form method="GET" action="" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Filtrer par Projet</label>
                <select name="project_id" id="filter_project_id" class="form-control">
                    <option value="">Tous les projets</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>" <?php echo ($filterProjectId == $project['id']) ? 'selected' : ''; ?>>
                            <?php echo escape($project['code'] . ' - ' . $project['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Filtrer par Activité</label>
                <select name="activity_id" id="filter_activity_id" class="form-control">
                    <option value="">Toutes les activités</option>
                    <?php if ($filterProjectId): ?>
                        <?php 
                        $filteredActivities = $activiteClass->getAll($filterProjectId);
                        foreach ($filteredActivities as $activity): 
                        ?>
                            <option value="<?php echo $activity['id']; ?>" <?php echo ($filterActivityId == $activity['id']) ? 'selected' : ''; ?>>
                                <?php echo escape($activity['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                <a href="index.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Réinitialiser</a>
            </div>
        </form>
    </div>
    
    <div class="card">
        <table id="beneficiariesTable" class="table">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>Genre</th>
                    <th>Projet</th>
                    <th>Activité</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Ville de provenance</th>
                    <th>Catégorie</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($beneficiaries as $beneficiary): ?>
                    <tr>
                        <td><?php echo escape($beneficiary['first_name'] . ' ' . $beneficiary['last_name']); ?></td>
                        <td><?php echo $beneficiary['gender'] === 'M' ? 'Masculin' : ($beneficiary['gender'] === 'F' ? 'Féminin' : 'Autre'); ?></td>
                        <td><?php echo escape($beneficiary['project_title'] ?? '-'); ?></td>
                        <td><?php echo escape($beneficiary['activity_title'] ?? '-'); ?></td>
                        <td><?php echo escape($beneficiary['phone'] ?? '-'); ?></td>
                        <td><?php echo escape($beneficiary['email'] ?? '-'); ?></td>
                        <td><?php echo escape($beneficiary['ville_de_provenance'] ?? '-'); ?></td>
                        <td>
                            <?php 
                            $categories = ['individual' => 'Individuel', 'group' => 'Groupe', 'community' => 'Communauté'];
                            echo $categories[$beneficiary['category']] ?? $beneficiary['category'];
                            ?>
                        </td>
                        <td><?php echo formatDate($beneficiary['registration_date']); ?></td>
                        <td>
                            <div class="status-badge-wrapper">
                                <?php echo getStatusBadge($beneficiary['status'], 'beneficiary'); ?>
                            </div>
                        </td>
                        <td class="actions-cell">
                            <div class="actions-group">
                                <a href="view.php?id=<?php echo $beneficiary['id']; ?>" class="btn-icon btn-icon-primary" title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit.php?id=<?php echo $beneficiary['id']; ?>" class="btn-icon btn-icon-success" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $beneficiary['id']; ?>" class="btn-icon btn-icon-danger" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bénéficiaire ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gestion du chargement dynamique des activités pour le filtre
    $('#filter_project_id').on('change', function() {
        const projectId = $(this).val();
        const activitySelect = $('#filter_activity_id');
        
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
                    activitySelect.html('<option value="">Toutes les activités</option>');
                    if (response.success && response.data) {
                        response.data.forEach(function(activity) {
                            activitySelect.append(
                                $('<option></option>')
                                    .attr('value', activity.id)
                                    .text(activity.title)
                            );
                        });
                    }
                }
            });
        } else {
            activitySelect.html('<option value="">Toutes les activités</option>');
        }
    });
    
    $('#beneficiariesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
        },
        order: [[0, 'asc']],
        columnDefs: [
            { 
                targets: -1, 
                orderable: false,
                searchable: false,
                className: 'actions-cell'
            }
        ],
        responsive: true,
        scrollX: true
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

