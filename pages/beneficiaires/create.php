<?php
/**
 * Créer un bénéficiaire
 * RAMP-BENIN
 */

$pageTitle = 'Créer un Bénéficiaire';
require_once __DIR__ . '/../../includes/header.php';

$beneficiaireClass = new Beneficiaire();
$error = '';

// Récupérer les projets
$projetClass = new Projet();
$projects = $projetClass->getAll();

$projectId = $_GET['project_id'] ?? null;

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
        'category' => $_POST['category'] ?? 'individual',
        'registration_date' => $_POST['registration_date'] ?? date('Y-m-d'),
        'status' => $_POST['status'] ?? 'active',
        'notes' => $_POST['notes'] ?? null
    ];
    
    $id = $beneficiaireClass->create($data);
    if ($id) {
        redirectWithMessage('index.php', 'Bénéficiaire créé avec succès', 'success');
    } else {
        $error = 'Une erreur est survenue lors de la création du bénéficiaire';
    }
}
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Créer un Bénéficiaire</h1>
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
                    <option value="">Sélectionner un projet</option>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>" <?php echo ($projectId == $project['id']) ? 'selected' : ''; ?>>
                            <?php echo escape($project['code'] . ' - ' . $project['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Activité</label>
                <select name="activity_id" id="activity_id" class="form-control">
                    <option value="">Sélectionner d'abord un projet</option>
                </select>
                <small class="form-text text-muted">Les activités seront chargées automatiquement après la sélection d'un projet</small>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Prénom *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nom *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Genre *</label>
                    <select name="gender" class="form-control" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                        <option value="Other">Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date de naissance</label>
                    <input type="date" name="date_of_birth" class="form-control">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Adresse</label>
                <textarea name="address" class="form-control" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Ville de provenance</label>
                <input type="text" name="ville_de_provenance" class="form-control" placeholder="Ex: Cotonou, Porto-Novo, etc.">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Catégorie</label>
                    <select name="category" class="form-control">
                        <option value="individual">Individuel</option>
                        <option value="group">Groupe</option>
                        <option value="community">Communauté</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date d'inscription</label>
                    <input type="date" name="registration_date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="status" class="form-control">
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                    <option value="completed">Terminé</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
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
    
    // Déclencher le changement si un projet est déjà sélectionné
    if (projectSelect.val()) {
        projectSelect.trigger('change');
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

