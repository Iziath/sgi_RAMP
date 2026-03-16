<?php
/**
 * Page d'accueil - Redirection vers le dashboard ou login
 * RAMP-BENIN
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/auth/login.php');
}
exit();

