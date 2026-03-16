<?php
/**
 * Vérification de l'authentification
 * RAMP-BENIN
 * 
 * À inclure au début des pages protégées
 */

require_once __DIR__ . '/../includes/auth.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

