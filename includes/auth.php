<?php
/**
 * Fonctions d'authentification
 * RAMP-BENIN
 */

require_once __DIR__ . '/../config/constants.php';

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Vérifier si l'utilisateur est administrateur
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Rediriger vers la page de connexion si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit();
    }
}

/**
 * Rediriger vers le dashboard si déjà connecté
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/dashboard.php');
        exit();
    }
}

/**
 * Authentifier un utilisateur
 * @param string $username
 * @param string $password
 * @return array|false Retourne les données de l'utilisateur ou false
 */
function authenticateUser($username, $password) {
    $userClass = new User();
    $user = $userClass->authenticate($username, $password);
    
    if ($user) {
        // Mettre à jour la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        
        return $user;
    }
    
    return false;
}

/**
 * Déconnecter l'utilisateur
 */
function logout() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit();
}

/**
 * Obtenir les informations de l'utilisateur connecté
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'full_name' => $_SESSION['full_name'],
        'role' => $_SESSION['role']
    ];
}

