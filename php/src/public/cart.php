<?php
// cart.php - Página pública del carrito de compras.
require_once __DIR__ . '/../php/cart.php';
require_once __DIR__ . '/../templates/header.php';

$items = getCartItems();
$total = getCartTotal();
?>

<main class="container">
  <h1 class="page-title">Tu carrito</h1>

  <?php if (empty($items)): ?>
    <div class="empty-cart">
      <p class="empty-icon">🛒</p>
      <h2>El carrito está vacío</h2>
      <p>Aún no has agregado productos. Explora nuestro catálogo.</p>
      <a href="<?= BASE_URL ?>/" class="btn-primary">Ver productos</a>
    </div>

  <?php else: ?>
    <div class="cart-layout">
      <section class="cart-items" aria-label="Ítems en el carrito">
        <table class="cart-table">
          <thead>
            <tr>
              <th scope="col">Producto</th>
              <th scope="col">Precio</th>
              <th scope="col">Cantidad</th>
              <th scope="col">Subtotal</th>
              <th scope="col"><span class="sr-only">Acciones</span></th>
            </tr>
          </thead>
          <tbody id="cartBody">
            <?php foreach ($items as $item): ?>
            <tr data-id="<?= (int)$item['id'] ?>">
              <td class="cart-name"><?= e($item['name']) ?></td>
              <td class="cart-price"><?= formatPrice((float)$item['price']) ?></td>
              <td>
                <div class="qty-control qty-sm">
                  <button type="button" class="qty-dec" aria-label="Reducir">−</button>
                  <input type="number" class="qty-input" value="<?= (int)$item['qty'] ?>" min="1" max="99">
                  <button type="button" class="qty-inc" aria-label="Aumentar">+</button>
                </div>
              </td>
              <td class="cart-subtotal"><?= formatPrice((float)$item['price'] * (int)$item['qty']) ?></td>
              <td>
                <button type="button" class="btn-remove" aria-label="Eliminar <?= e($item['name']) ?>">✕</button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="cart-actions-row">
          <button id="clearCart" class="btn-ghost btn-danger">Vaciar carrito</button>
        </div>
      </section>

      <aside class="cart-summary">
        <h2 class="summary-title">Resumen</h2>
        <dl class="summary-list">
          <dt>Subtotal</dt>
          <dd id="summaryTotal"><?= formatPrice($total) ?></dd>
          <dt>Envío</dt>
          <dd>Por calcular</dd>
        </dl>
        <div class="summary-total">
          <span>Total estimado</span>
          <strong id="summaryGrand"><?= formatPrice($total) ?></strong>
        </div>
        <button class="btn-primary btn-block">Continuar al pago</button>
        <a href="<?= BASE_URL ?>/" class="btn-ghost btn-block">Seguir comprando</a>
      </aside>
    </div>

    <div id="cartAlert" class="cart-message" role="alert" aria-live="polite"></div>
  <?php endif; ?>
</main>

<script>
// Lógica de la página carrito (opera sobre DOM existente)
(function () {
  const alert  = document.getElementById('cartAlert');
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const csrf   = csrfMeta ? csrfMeta.content : '';

  function showMsg(text, type = 'success') {
    if (!alert) return;
    alert.textContent = text;
    alert.className   = 'cart-message ' + type;
    setTimeout(() => { alert.textContent = ''; alert.className = 'cart-message'; }, 3000);
  }

  async function postCart(action, id, qty = null) {
    const body = new URLSearchParams({ action, id, csrf_token: csrf });
    if (qty !== null) body.set('qty', qty);
    const res  = await fetch(window.App.apiBase, { method: 'POST', body });
    return res.json();
  }

  // Actualizar cantidad
  document.getElementById('cartBody')?.addEventListener('change', async function (e) {
    if (!e.target.classList.contains('qty-input')) return;
    const row = e.target.closest('tr');
    const id  = row.dataset.id;
    const qty = parseInt(e.target.value, 10);
    if (isNaN(qty) || qty < 1) { e.target.value = 1; return; }
    const data = await postCart('update_cart', id, qty);
    if (data.ok) {
      const subtotalCell = row.querySelector('.cart-subtotal');
      const price = parseFloat(data.cart[id]?.price ?? 0);
      subtotalCell.textContent = '$' + (price * qty).toLocaleString('es-CO');
      document.getElementById('summaryTotal').textContent = '$' + data.total.toLocaleString('es-CO');
      document.getElementById('summaryGrand').textContent = '$' + data.total.toLocaleString('es-CO');
      window.App.updateCartBadge(data.count);
    } else {
      showMsg('⚠️ ' + (data.error || 'No se pudo actualizar.'), 'error');
    }
  });

  // Botones ±
  document.getElementById('cartBody')?.addEventListener('click', function (e) {
    const btn = e.target.closest('.qty-dec, .qty-inc');
    if (!btn) return;
    const input = btn.closest('.qty-control').querySelector('.qty-input');
    let v = parseInt(input.value, 10);
    if (btn.classList.contains('qty-dec') && v > 1) input.value = --v;
    if (btn.classList.contains('qty-inc') && v < 99) input.value = ++v;
    input.dispatchEvent(new Event('change', { bubbles: true }));
  });

  // Eliminar ítem
  document.getElementById('cartBody')?.addEventListener('click', async function (e) {
    const btn = e.target.closest('.btn-remove');
    if (!btn) return;
    const row = btn.closest('tr');
    const id  = row.dataset.id;
    const data = await postCart('remove_cart', id);
    if (data.ok) {
      row.remove();
      document.getElementById('summaryTotal').textContent = '$' + data.total.toLocaleString('es-CO');
      document.getElementById('summaryGrand').textContent = '$' + data.total.toLocaleString('es-CO');
      window.App.updateCartBadge(data.count);
      if (data.count === 0) location.reload();
    }
  });

  // Vaciar carrito
  document.getElementById('clearCart')?.addEventListener('click', async function () {
    if (!confirm('¿Estás seguro de vaciar el carrito?')) return;
    const data = await postCart('clear_cart', 0);
    if (data.ok) location.reload();
  });
})();
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
