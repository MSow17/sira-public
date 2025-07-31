<?php
// Configuration des paramètres de session avant de démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    $sessionLifetime = 3600; // 1 heure

    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path' => '/',
        'domain' => '', // ou ton domaine exact en prod
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    ini_set('session.gc_maxlifetime', $sessionLifetime);

    session_start();

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $sessionLifetime) {
        session_unset();
        session_destroy();
        session_start(); // relancer session après destruction
    }

    $_SESSION['last_activity'] = time();
}

