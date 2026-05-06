<?php
// product.php - Detalle de un producto.
// bootstrap se carga via header.php → bootstrap.php

require_once __DIR__ . '/../php/product_model.php';

$id      = (int)($_GET['id'] ?? 0);
$product = getProductById($id);

require_once __DIR__ . '/../templates/header.php';

if ($product === false):
?>
<main class="container">
  <div class="not-found">
    <p class="not-found-icon">📦</p>
    <h2>Producto no encontrado</h2>
    <p>El producto que buscas no existe o fue retirado del catálogo.</p>
    <a href="<?= BASE_URL ?>/" class="btn-primary">Ver todos los productos</a>
  </div>
</main>
<?php
    require_once __DIR__ . '/../templates/footer.php';
    exit;
endif;
?>

<main class="container">
  <nav class="breadcrumb" aria-label="Ruta de navegación">
    <a href="<?= BASE_URL ?>/">Inicio</a>
    <span aria-hidden="true">›</span>
    <span><?= e($product['name']) ?></span>
  </nav>

  <article class="product-detail">
    <div class="product-img-wrap">
      <img
        id="productImage"
        src="<?= e(IMG_URL . '/' . ($product['image'] ?? 'default.jpg')) ?>"
        alt="Fotografía de <?= e($product['name']) ?>"
        loading="lazy"
        onerror="this.src='<?= ASSETS_URL ?>/img/products/default.jpg'"
        style="width: 100%; height: auto; max-height: 600px; object-fit: cover; border-radius: 14px; cursor: zoom-in;"
        title="Haz clic para ampliar"
      >
    </div>

    <!-- Modal para imagen ampliada -->
    <div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 1000; cursor: zoom-out;">
      <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 90vw; max-height: 90vh;">
        <img id="modalImage" src="" alt="Imagen ampliada" style="width: 100%; height: auto; max-width: 100%; max-height: 90vh; object-fit: contain;">
        <button id="closeModal" style="position: absolute; top: -40px; right: 0; background: none; border: none; color: white; font-size: 24px; cursor: pointer;">✕</button>
      </div>
    </div>

    <div class="product-info">
      <p class="product-badge">
        <?= $product['stock'] > 0 ? '✅ En stock (' . (int)$product['stock'] . ' disponibles)' : '❌ Agotado' ?>
      </p>

      <h1 class="product-name"><?= e($product['name']) ?></h1>

      <?php if (!empty($product['short_description'])): ?>
        <p class="product-short"><?= e($product['short_description']) ?></p>
      <?php endif; ?>

      <p class="product-price"><?= formatPrice((float)$product['price']) ?></p>

      <?php if (!empty($product['description'])): ?>
        <div class="product-desc">
          <?= nl2br(e($product['description'])) ?>
        </div>
      <?php endif; ?>

      <?php if ($product['stock'] > 0): ?>
      <div class="product-actions">
        <label for="qty" class="sr-only">Cantidad</label>
        <div class="qty-control">
          <button type="button" id="qtyDec" aria-label="Reducir cantidad">−</button>
          <input id="qty" type="number" value="1" min="1" max="<?= (int)$product['stock'] ?>" aria-label="Cantidad">
          <button type="button" id="qtyInc" aria-label="Aumentar cantidad">+</button>
        </div>
        <button
          id="addToCart"
          class="btn-primary btn-lg"
          data-id="<?= (int)$product['id'] ?>"
        >
          Agregar al carrito
        </button>
      </div>
      <p id="cartMessage" class="cart-message" role="alert" aria-live="polite"></p>
      <?php endif; ?>
    </div>
  </article>
</main>

<script>
// Script específico de la página de detalle
(function () {
  const qtyInput = document.getElementById('qty');
  const max      = parseInt(qtyInput?.max || '999', 10);

  document.getElementById('qtyDec')?.addEventListener('click', () => {
    const v = parseInt(qtyInput.value, 10);
    if (v > 1) qtyInput.value = v - 1;
  });

  document.getElementById('qtyInc')?.addEventListener('click', () => {
    const v = parseInt(qtyInput.value, 10);
    if (v < max) qtyInput.value = v + 1;
  });

  document.getElementById('addToCart')?.addEventListener('click', async function () {
    const btn = this;
    const id  = btn.dataset.id;
    const qty = parseInt(qtyInput.value, 10);
    const msg = document.getElementById('cartMessage');

    btn.disabled    = true;
    btn.textContent = 'Agregando…';
    msg.className   = 'cart-message';
    msg.textContent = '';

    try {
      const res  = await window.App.addToCart(id, qty);
      if (res.ok) {
        msg.className   = 'cart-message success';
        msg.textContent = '✅ Producto agregado al carrito.';
        window.App.updateCartBadge(res.count);
      } else {
        msg.className   = 'cart-message error';
        msg.textContent = '⚠️ ' + (res.error || 'Error al agregar.');
      }
    } catch {
      msg.className   = 'cart-message error';
      msg.textContent = '⚠️ Error de conexión. Intenta de nuevo.';
    } finally {
      btn.disabled    = false;
      btn.textContent = 'Agregar al carrito';
    }
  });

  // Modal de imagen ampliada
  const productImage = document.getElementById('productImage');
  const imageModal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  const closeModal = document.getElementById('closeModal');

  if (productImage && imageModal) {
    // Abrir modal al hacer clic en la imagen
    productImage.addEventListener('click', () => {
      modalImage.src = productImage.src;
      imageModal.style.display = 'block';
      document.body.style.overflow = 'hidden';
    });

    // Cerrar modal al hacer clic en la X
    closeModal.addEventListener('click', () => {
      imageModal.style.display = 'none';
      document.body.style.overflow = '';
    });

    // Cerrar modal al hacer clic fuera de la imagen
    imageModal.addEventListener('click', (e) => {
      if (e.target === imageModal) {
        imageModal.style.display = 'none';
        document.body.style.overflow = '';
      }
    });

    // Cerrar modal con la tecla ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && imageModal.style.display === 'block') {
        imageModal.style.display = 'none';
        document.body.style.overflow = '';
      }
    });
  }
})();
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
