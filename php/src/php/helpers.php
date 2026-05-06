<?php
// helpers.php - Funciones utilitarias transversales.

/**
 * Escapa una cadena para salida HTML segura (previene XSS).
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirige a una URL y termina la ejecución.
 */
function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/**
 * Devuelve el token CSRF de la sesión actual.
 */
function csrfToken(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

/**
 * Valida que el token CSRF del POST coincida con el de la sesión.
 * Lanza una excepción si no coincide.
 */
function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        throw new RuntimeException('Token CSRF inválido.');
    }
}

/**
 * Formatea un precio en pesos colombianos.
 */
function formatPrice(float|int $price): string
{
    return '$' . number_format($price, 0, ',', '.');
}

/**
 * Responde con JSON y termina la ejecución (uso en api.php).
 */
function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
