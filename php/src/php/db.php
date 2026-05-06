<?php
// db.php - Singleton PDO con reconexión lazy.

require_once __DIR__ . '/config.php';

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        DB_HOST,
        DB_NAME
    );

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false, // consultas realmente preparadas
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        // Asegurar codificación UTF-8
        $pdo->exec("SET NAMES 'utf8mb4'");
        $pdo->exec("SET CHARACTER SET utf8mb4");
    } catch (PDOException $e) {
        // No exponemos detalles de conexión en producción
        $msg = APP_ENV === 'development'
            ? 'Error de base de datos: ' . $e->getMessage()
            : 'No se pudo conectar a la base de datos.';
        throw new RuntimeException($msg, 0, $e);
    }

    return $pdo;
}
