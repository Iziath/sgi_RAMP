<?php
/**
 * Menu de navigation
 * RAMP-BENIN
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentPath = $_SERVER['REQUEST_URI'];
$currentUser = getCurrentUser();
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" class="logo-container">
            <img src="<?php echo BASE_URL; ?>/assets/images/logo-ramp.jpg" alt="RAMP Logo" class="logo-img">
        </a>
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    
    <nav>
        <ul class="sidebar-nav">
            <li>
                <a href="<?php echo BASE_URL; ?>/pages/dashboard.php" 
                   class="<?php echo (strpos($currentPath, 'dashboard') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i>
                    <span>Tableau de bord</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/pages/projets/index.php" 
                   class="<?php echo (strpos($currentPath, 'projets') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Projets</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/pages/activites/index.php" 
                   class="<?php echo (strpos($currentPath, 'activites') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i>
                    <span>Activités</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/pages/beneficiaires/index.php" 
                   class="<?php echo (strpos($currentPath, 'beneficiaires') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Bénéficiaires</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/pages/partenaires/index.php" 
                   class="<?php echo (strpos($currentPath, 'partenaires') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i>
                    <span>Partenaires</span>
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/pages/documents/index.php" 
                   class="<?php echo (strpos($currentPath, 'documents') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                </a>
            </li>
            <?php if (isAdmin()): ?>
            <li>
                <a href="<?php echo BASE_URL; ?>/users.php" 
                   class="<?php echo (strpos($currentPath, 'users') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-user-cog"></i>
                    <span>Utilisateurs</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="sidebar-user">
        <a href="<?php echo BASE_URL; ?>/auth/logout.php" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">
            Déconnexion
        </a>
    </div>
</aside>

