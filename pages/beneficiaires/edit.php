<?php
/**
 * Modifier un bénéficiaire
 * RAMP-BENIN
 */

$pageTitle = 'Modifier un Bénéficiaire';
require_once __DIR__ . '/../../includes/header.php';

$beneficiaireClass = new Beneficiaire();
$error = '';

$id = $_GET['id'] ?? 0;
$beneficiary = $beneficiaireClass->getById($id);

if (!$beneficiary) {
    redirectWithMessage('index.php', 'Bénéficiaire non trouvé', 'error');
}

// Récupérer les projets
$projetClass = new Projet();
$projects = $projetClass->getAll();

// Récupérer les activités du projet actuel
$activiteClass = new Activite();
$currentActivities = [];
if ($beneficiary['project_id']) {
    $currentActivities = $activiteClass->getAll($beneficiary['project_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'project_id' => $_POST['project_id'],
        'activity_id' => !empty($_POST['activity_id']) ? $_POST['activity_id'] : null,
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'gender' => $_POST['gender'],
        'date_of_birth' => $_POST['date_of_birth'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'email' => $_POST['email'] ?? null,
        'address' => $_POST['address'] ?? null,
        'ville_de_provenance' => $_POST['ville_de_provenance'] ?? null,
        'category' => $_POST['category'],
        'registration_date' => $_POST['registration_date'],
        'status' => $_POST['status'],
        'notes' => $_POST['notes'] ?? null
    ];
    
    if ($beneficiaireClass->update($id, $data)) {
        redirectWithMessage('index.php', 'Bénéficiaire mis à jour avec succès', 'success');
    } else {
        $error = 'Une erreur est survenue lors de la mise à jour';
    }
}
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Modifier le Bénéficiaire</h1>
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
                <label class="form-label">Projet *</label>
                <select name="project_id" id="project_id" class="form-control" required>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>" <?php echo ($beneficiary['project_id'] == $project['id']) ? 'selected' : ''; ?>>
                            <?php echo escape($project['code'] . ' - ' . $project['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Activité</label>
                <select name="activity_id" id="activity_id" class="form-control">
                    <option value="">Sélectionner une activité (optionnel)</option>
                    <?php foreach ($currentActivities as $activity): ?>
                        <option value="<?php echo $activity['id']; ?>" <?php echo (isset($beneficiary['activity_id']) && $beneficiary['activity_id'] == $activity['id']) ? 'selected' : ''; ?>>
                            <?php echo escape($activity['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Les activités seront chargées automatiquement après la sélection d'un projet</small>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo escape($beneficiary['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo escape($beneficiary['last_name']); ?>" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Genre *</label>
                    <select name="gender" class="form-control" required>
                        <option value="M" <?php echo $beneficiary['gender'] === 'M' ? 'selected' : ''; ?>>Masculin</option>
                        <option value="F" <?php echo $beneficiary['gender'] === 'F' ? 'selected' : ''; ?>>Féminin</option>
                        <option value="Other" <?php echo $beneficiary['gender'] === 'Other' ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_of_birth" class="form-control" value="<?php echo $beneficiary['date_of_birth'] ?? ''; ?>">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo escape($beneficiary['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo escape($beneficiary['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Adresse</label>
                <textarea name="address" class="form-control" rows="2"><?php echo escape($beneficiary['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Ville de provenance</label>
                <input type="text" name="ville_de_provenance" class="form-control" 
                       value="<?php echo escape($beneficiary['ville_de_provenance'] ?? ''); ?>" 
                       placeholder="Ex: Cotonou, Porto-Novo, etc.">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Catégorie</label>
                    <select name="category" class="form-control">
                        <option value="individual" <?php echo $beneficiary['category'] === 'individual' ? 'selected' : ''; ?>>Individuel</option>
                        <option value="group" <?php echo $beneficiary['category'] === 'group' ? 'selected' : ''; ?>>Groupe</option>
                        <option value="community" <?php echo $beneficiary['category'] === 'community' ? 'selected' : ''; ?>>Communauté</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date d'inscription</label>
                    <input type="date" name="registration_date" class="form-control" value="<?php echo $beneficiary['registration_date']; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="status" class="form-control">
                    <option value="active" <?php echo $beneficiary['status'] === 'active' ? 'selected' : ''; ?>>Actif</option>
                    <option value="inactive" <?php echo $beneficiary['status'] === 'inactive' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="completed" <?php echo $beneficiary['status'] === 'completed' ? 'selected' : ''; ?>>Terminé</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo escape($beneficiary['notes'] ?? ''); ?></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const projectSelect = $('#project_id');
    const activitySelect = $('#activity_id');
    const currentActivityId = <?php echo isset($beneficiary['activity_id']) ? $beneficiary['activity_id'] : 'null'; ?>;
    
    projectSelect.on('change', function() {
        const projectId = $(this).val();
        
        // Réinitialiser le select des activités
        activitySelect.html('<option value="">Chargement...</option>');
        
        if (projectId) {
            // Charger les activités du projet sélectionné
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
                            const selected = (currentActivityId && activity.id == currentActivityId) ? 'selected' : '';
                            activitySelect.append(
                                $('<option></option>')
                                    .attr('value', activity.id)
                                    .attr('selected', selected)
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
    
    // Déclencher le changement si un projet est déjà sélectionné
    if (projectSelect.val()) {
        projectSelect.trigger('change');
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

