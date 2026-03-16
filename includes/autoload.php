<?php
/**
 * Autoloader pour les classes
 * RAMP-BENIN
 */

spl_autoload_register(function ($class) {
    $file = CLASSES_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

