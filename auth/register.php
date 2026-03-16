<?php
/**
 * Page d'inscription (optionnelle)
 * RAMP-BENIN
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Rediriger si déjà connecté
redirectIfLoggedIn();

// Par défaut, l'inscription peut être désactivée (réservée aux admins)
// Vous pouvez activer cette fonctionnalité si nécessaire

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Veuillez remplir tous les champs obligatoires';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères';
    } elseif (!isValidEmail($email)) {
        $error = 'Adresse email invalide';
    } else {
        $userClass = new User();
        
        // Vérifier si le username existe déjà
        if ($userClass->usernameExists($username)) {
            $error = 'Ce nom d\'utilisateur est déjà utilisé';
        } elseif ($userClass->emailExists($email)) {
            $error = 'Cette adresse email est déjà utilisée';
        } else {
            // Créer l'utilisateur
            $userId = $userClass->create([
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'full_name' => $full_name,
                'role' => 'user',
                'status' => 'active'
            ]);
            
            if ($userId) {
                $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            } else {
                $error = 'Une erreur est survenue lors de l\'inscription';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1 class="login-title">Inscription</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo escape($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo escape($success); ?>
                    <br><br>
                    <a href="<?php echo BASE_URL; ?>/auth/login.php" class="btn btn-primary">Se connecter</a>
                </div>
            <?php else: ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full_name" class="form-label">Nom complet *</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Nom d'utilisateur *</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe *</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="<?php echo PASSWORD_MIN_LENGTH; ?>">
                        <small style="color: var(--color-gray-dark);">Minimum <?php echo PASSWORD_MIN_LENGTH; ?> caractères</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        S'inscrire
                    </button>
                </form>
                
                <div class="text-center mt-2">
                    <a href="<?php echo BASE_URL; ?>/auth/login.php" style="color: var(--color-blue);">
                        Déjà un compte ? Se connecter
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

