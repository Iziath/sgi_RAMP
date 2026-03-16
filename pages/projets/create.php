<?php
/**
 * Créer un projet
 * RAMP-BENIN
 */

$pageTitle = 'Créer un Projet';
require_once __DIR__ . '/../../includes/header.php';

$projetClass = new Projet();
$userClass = new User();
$partenaireClass = new Partenaire();
$error = '';

// Récupérer les organisations
$db = Database::getInstance()->getConnection();
$organizations = $db->query("SELECT id, name FROM organizations WHERE status = 'active'")->fetchAll();

// Récupérer les utilisateurs (pour responsable projet)
$users = $userClass->getAll();

// Récupérer les partenaires actifs
$partners = $partenaireClass->getAll();
$partners = array_filter($partners, function($p) { return $p['status'] === 'active'; });

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'organization_id' => $_POST['organization_id'] ?? 1,
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? null,
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'] ?? null,
        'budget' => $_POST['budget'] ?? 0,
        'status' => $_POST['status'] ?? 'planning',
        'created_by' => $_SESSION['user_id'],
        'responsable_projet_id' => !empty($_POST['responsable_projet_id']) ? $_POST['responsable_projet_id'] : null
    ];
    
    $id = $projetClass->create($data);
    if ($id) {
        // Assigner les partenaires
        if (!empty($_POST['partner_ids']) && is_array($_POST['partner_ids'])) {
            $projetClass->assignPartners($id, $_POST['partner_ids']);
        }
        
        redirectWithMessage('index.php', 'Projet créé avec succès', 'success');
    } else {
        $error = 'Une erreur est survenue lors de la création du projet';
    }
}
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Créer un Projet</h1>
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
                <label class="form-label">Organisation</label>
                <select name="organization_id" class="form-control" required>
                    <?php foreach ($organizations as $org): ?>
                        <option value="<?php echo $org['id']; ?>"><?php echo escape($org['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Titre *</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Date de début *</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Budget (FCFA)</label>
                    <input type="number" name="budget" class="form-control" step="0.01" min="0" value="0">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-control">
                        <option value="planning">En planification</option>
                        <option value="active">Actif</option>
                        <option value="completed">Terminé</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Responsable du projet</label>
                <select name="responsable_projet_id" class="form-control">
                    <option value="">-- Sélectionner un responsable --</option>
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['status'] === 'active'): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo escape($user['full_name']); ?> (<?php echo escape($user['role']); ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Partenaires</label>
                <div class="selection-box">
                    <?php if (empty($partners)): ?>
                        <p class="text-muted">Aucun partenaire disponible. <a href="../partenaires/create.php">Créer un partenaire</a></p>
                    <?php else: ?>
                        <?php foreach ($partners as $partner): ?>
                            <label class="selection-item">
                                <input type="checkbox" name="partner_ids[]" value="<?php echo $partner['id']; ?>" class="selection-checkbox">
                                <span class="selection-label">
                                    <strong><?php echo escape($partner['name']); ?></strong>
                                    <span class="selection-meta"><?php echo escape($partner['type']); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <small class="form-text text-muted">Sélectionnez un ou plusieurs partenaires pour ce projet</small>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

