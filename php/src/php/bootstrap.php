<?php
// bootstrap.php - Inicialización centralizada de la aplicación.
// Debe incluirse UNA sola vez al inicio de cada request (antes de cualquier salida).

require_once __DIR__ . '/config.php';

// ── Manejo de errores ────────────────────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ── Sesión segura ────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false, // true en HTTPS/producción
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ── Token CSRF ───────────────────────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Autoinclusión de dependencias ────────────────────────────────────────────
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';
