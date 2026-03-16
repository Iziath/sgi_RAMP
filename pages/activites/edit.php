<?php
/**
 * Modifier une activité
 * RAMP-BENIN
 */

$pageTitle = 'Modifier une Activité';
require_once __DIR__ . '/../../includes/header.php';

$activiteClass = new Activite();
$error = '';

$id = $_GET['id'] ?? 0;
$activity = $activiteClass->getById($id);

if (!$activity) {
    redirectWithMessage('index.php', 'Activité non trouvée', 'error');
}

// Récupérer les projets
$projetClass = new Projet();
$projects = $projetClass->getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'project_id' => $_POST['project_id'],
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? null,
        'planned_start_date' => $_POST['planned_start_date'] ?? null,
        'planned_end_date' => $_POST['planned_end_date'] ?? null,
        'actual_start_date' => $_POST['actual_start_date'] ?? null,
        'actual_end_date' => $_POST['actual_end_date'] ?? null,
        'status' => $_POST['status'],
        'progress' => $_POST['progress'] ?? 0,
        'budget' => $_POST['budget'] ?? 0
    ];
    
    if ($activiteClass->update($id, $data)) {
        redirectWithMessage('index.php', 'Activité mise à jour avec succès', 'success');
    } else {
        $error = 'Une erreur est survenue lors de la mise à jour';
    }
}
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Modifier l'Activité</h1>
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
                <select name="project_id" class="form-control" required>
                    <?php foreach ($projects as $project): ?>
                        <option value="<?php echo $project['id']; ?>" <?php echo ($activity['project_id'] == $project['id']) ? 'selected' : ''; ?>>
                            <?php echo escape($project['code'] . ' - ' . $project['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Titre *</label>
                <input type="text" name="title" class="form-control" value="<?php echo escape($activity['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo escape($activity['description'] ?? ''); ?></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Date prévue début</label>
                    <input type="date" name="planned_start_date" class="form-control" value="<?php echo $activity['planned_start_date'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date prévue fin</label>
                    <input type="date" name="planned_end_date" class="form-control" value="<?php echo $activity['planned_end_date'] ?? ''; ?>">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Date réelle début</label>
                    <input type="date" name="actual_start_date" class="form-control" value="<?php echo $activity['actual_start_date'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date réelle fin</label>
                    <input type="date" name="actual_end_date" class="form-control" value="<?php echo $activity['actual_end_date'] ?? ''; ?>">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Budget (FCFA)</label>
                    <input type="number" name="budget" class="form-control" step="0.01" min="0" value="<?php echo $activity['budget']; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Progression (%)</label>
                    <input type="number" name="progress" class="form-control" min="0" max="100" value="<?php echo $activity['progress']; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-control">
                        <option value="planned" <?php echo $activity['status'] === 'planned' ? 'selected' : ''; ?>>Planifié</option>
                        <option value="in_progress" <?php echo $activity['status'] === 'in_progress' ? 'selected' : ''; ?>>En cours</option>
                        <option value="completed" <?php echo $activity['status'] === 'completed' ? 'selected' : ''; ?>>Terminé</option>
                        <option value="cancelled" <?php echo $activity['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulé</option>
                    </select>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

