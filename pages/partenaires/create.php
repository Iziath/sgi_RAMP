<?php
/**
 * Créer un partenaire
 * RAMP-BENIN
 */

$pageTitle = 'Créer un Partenaire';
require_once __DIR__ . '/../../includes/header.php';

$partenaireClass = new Partenaire();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'],
        'type' => $_POST['type'],
        'contact_person' => $_POST['contact_person'] ?? null,
        'phone' => $_POST['phone'] ?? null,
        'email' => $_POST['email'] ?? null,
        'address' => $_POST['address'] ?? null,
        'website' => $_POST['website'] ?? null,
        'description' => $_POST['description'] ?? null,
        'status' => $_POST['status'] ?? 'active'
    ];
    
    $id = $partenaireClass->create($data);
    if ($id) {
        redirectWithMessage('index.php', 'Partenaire créé avec succès', 'success');
    } else {
        $error = 'Une erreur est survenue lors de la création du partenaire';
    }
}
?>

<div class="container-fluid">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <h1 style="margin: 0; color: var(--color-blue);">Créer un Partenaire</h1>
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
                <label class="form-label">Nom *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Type *</label>
                <select name="type" class="form-control" required>
                    <option value="government">Gouvernement</option>
                    <option value="ngo">ONG</option>
                    <option value="private">Privé</option>
                    <option value="international">International</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Personne de contact</label>
                <input type="text" name="contact_person" class="form-control">
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
                <label class="form-label">Site web</label>
                <input type="url" name="website" class="form-control" placeholder="https://...">
            </div>
            
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="status" class="form-control">
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="index.php" class="btn btn-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

