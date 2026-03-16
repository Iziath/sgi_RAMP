<?php
/**
 * Fonctions utilitaires
 * RAMP-BENIN
 */

require_once __DIR__ . '/../config/constants.php';

/**
 * Échapper les données pour l'affichage HTML
 * @param string $data
 * @return string
 */
function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Formater une date au format français
 * @param string $date
 * @param bool $includeTime
 * @return string
 */
function formatDate($date, $includeTime = false) {
    if (empty($date)) {
        return '-';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    
    $format = $includeTime ? 'd/m/Y à H:i' : 'd/m/Y';
    return date($format, $timestamp);
}

/**
 * Formater un montant en devise
 * @param float $amount
 * @param string $currency
 * @return string
 */
function formatCurrency($amount, $currency = 'FCFA') {
    return number_format($amount, 0, ',', ' ') . ' ' . $currency;
}

/**
 * Obtenir le pourcentage de progression
 * @param int $current
 * @param int $total
 * @return int
 */
function getProgressPercentage($current, $total) {
    if ($total == 0) {
        return 0;
    }
    return min(100, round(($current / $total) * 100));
}

/**
 * Obtenir le statut avec badge coloré
 * @param string $status
 * @param string $type
 * @return string
 */
function getStatusBadge($status, $type = 'project') {
    $badges = [
        'project' => [
            'planning' => '<span class="badge badge-warning">En planification</span>',
            'active' => '<span class="badge badge-success">Actif</span>',
            'completed' => '<span class="badge badge-info">Terminé</span>',
            'cancelled' => '<span class="badge badge-danger">Annulé</span>'
        ],
        'activity' => [
            'planned' => '<span class="badge badge-secondary">Planifié</span>',
            'in_progress' => '<span class="badge badge-success">En cours</span>',
            'completed' => '<span class="badge badge-info">Terminé</span>',
            'cancelled' => '<span class="badge badge-danger">Annulé</span>'
        ],
        'beneficiary' => [
            'active' => '<span class="badge badge-success">Actif</span>',
            'inactive' => '<span class="badge badge-secondary">Inactif</span>',
            'completed' => '<span class="badge badge-info">Terminé</span>'
        ],
        'general' => [
            'active' => '<span class="badge badge-success">Actif</span>',
            'inactive' => '<span class="badge badge-secondary">Inactif</span>'
        ]
    ];
    
    $statusKey = strtolower($status);
    if (isset($badges[$type][$statusKey])) {
        return $badges[$type][$statusKey];
    }
    
    return '<span class="badge badge-secondary">' . escape($status) . '</span>';
}

/**
 * Valider une adresse email
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Générer un code unique
 * @param string $prefix
 * @return string
 */
function generateUniqueCode($prefix = '') {
    return $prefix . strtoupper(uniqid());
}

/**
 * Rediriger avec un message
 * @param string $url
 * @param string $message
 * @param string $type
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
    header('Location: ' . $url);
    exit();
}

/**
 * Afficher le message flash
 * @return string|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        $alertClass = $type === 'success' ? 'alert-success' : ($type === 'error' ? 'alert-danger' : 'alert-info');
        return '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">
                    ' . escape($message) . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    }
    return null;
}

