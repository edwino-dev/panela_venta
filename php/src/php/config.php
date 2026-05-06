<?php
// config.php - SOLO constantes. Sin session_start() ni efectos secundarios.
// Carga bootstrap.php para inicializar la aplicación.

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'ventas_panela');
define('DB_USER', 'root');
define('DB_PASS', '');                        // Ajustar en producción
define('BASE_URL', '/panelafinca_corregido/php/src/public');
define('ASSETS_URL', '/panelafinca_corregido/php/assets');
define('IMG_URL',    ASSETS_URL . '/img/products');
define('APP_NAME',   'PanelaFinca');
define('APP_ENV',    'development');          // 'production' en prod
