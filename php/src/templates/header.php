<?php
// header.php - Plantilla de cabecera HTML.
// bootstrap.php ya fue incluido por el controlador público que lo llama.
require_once __DIR__ . '/../php/bootstrap.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME) ?> — Panela artesanal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
  <!-- Token CSRF disponible para JS -->
  <meta name="csrf-token" content="<?= e(csrfToken()) ?>">
  <meta name="api-base"   content="<?= e(BASE_URL) ?>/api.php">
</head>
<body>

<header class="site-header">
  <div class="wrap header-inner">
    <a class="brand" href="<?= BASE_URL ?>/">
      <span class="brand-icon">🌿</span>
      <?= e(APP_NAME) ?>
    </a>
    <nav class="site-nav">
      <a href="<?= BASE_URL ?>/">Inicio</a>
      <a href="<?= BASE_URL ?>/cart.php" class="nav-cart">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span>Carrito</span>
        <span class="cart-badge" id="cartCount">0</span>
      </a>
    </nav>
  </div>
</header>
