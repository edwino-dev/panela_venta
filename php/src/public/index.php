<?php require_once __DIR__ . '/../templates/header.php'; ?>

<main class="container">

  <section class="hero">
    <div class="hero-text">
      <p class="hero-eyebrow">Directo del productor</p>
      <h1 class="hero-title">Panela artesanal<br>de la finca</h1>
      <p class="hero-sub">Elaborada con métodos ancestrales en fogón de leña, sin conservantes ni aditivos.</p>
    </div>
    <div class="hero-search">
      <div class="search-box">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input id="searchInput" type="search" placeholder="Buscar por nombre, presentación u origen…" autocomplete="off">
        <button id="searchBtn" class="btn-primary">Buscar</button>
      </div>
    </div>
  </section>

  <section class="section-header">
    <h2 class="section-title">Nuestros productos</h2>
    <p id="productCount" class="section-sub"></p>
  </section>

  <section id="products" class="products-grid" aria-live="polite">
    <div class="loading-state">
      <div class="spinner"></div>
      <p>Cargando productos…</p>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
