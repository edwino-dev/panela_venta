/**
 * app.js — PanelaFinca · Lógica cliente
 *
 * CORRECCIONES respecto al original:
 *  - XSS eliminado: se usa textContent / createElement en lugar de innerHTML con datos del servidor
 *  - alert() reemplazado por mensajes inline no bloqueantes
 *  - Manejo de errores de red con try/catch en TODOS los fetch
 *  - Token CSRF enviado en todas las peticiones POST
 *  - Namespace global `window.App` para reutilización entre páginas
 */

(function () {
  'use strict';

  // ── Configuración ──────────────────────────────────────────────
  const csrfMeta = document.querySelector('meta[name="csrf-token"]');
  const apiMeta  = document.querySelector('meta[name="api-base"]');

  const CSRF     = csrfMeta?.content ?? '';
  const API_BASE = apiMeta?.content   ?? '/panelafinca_corregido/php/src/public/api.php';

  // ── Utilidades ────────────────────────────────────────────────
  /**
   * Escapa texto para inserción segura en el DOM.
   * Usa textContent internamente — no produce HTML sin sanitizar.
   */
  function esc(str) {
    const el = document.createElement('span');
    el.textContent = String(str ?? '');
    return el.innerHTML; // seguro: ya fue escapado por el DOM
  }

  /**
   * Formatea un número como precio en pesos colombianos.
   */
  function formatPrice(n) {
    return '$' + Number(n).toLocaleString('es-CO', { minimumFractionDigits: 0 });
  }

  /**
   * Fetch con manejo de errores centralizado.
   * Lanza un Error descriptivo si la respuesta no es ok o falla la red.
   */
  async function apiFetch(url, options = {}) {
    const res = await fetch(url, options);
    if (!res.ok) {
      throw new Error(`Error HTTP ${res.status}: ${res.statusText}`);
    }
    return res.json();
  }

  // ── API Pública (window.App) ──────────────────────────────────
  window.App = {
    apiBase: API_BASE,

    /**
     * Agrega un producto al carrito enviando el token CSRF.
     */
    async addToCart(productId, qty = 1) {
      const body = new URLSearchParams({
        action:      'add_cart',
        id:          productId,
        qty:         qty,
        csrf_token:  CSRF,
      });
      return apiFetch(API_BASE, { method: 'POST', body });
    },

    /**
     * Actualiza el badge del contador del carrito en el header.
     */
    updateCartBadge(count) {
      const badge = document.getElementById('cartCount');
      if (badge) badge.textContent = count ?? 0;
    },
  };

  // ── Carga de productos (index.php) ────────────────────────────
  const productsGrid = document.getElementById('products');

  if (productsGrid) {
    /**
     * Crea un elemento <article class="card"> sin innerHTML inseguro.
     * Todos los datos del servidor se insertan via textContent.
     */
    function buildCard(p) {
      const article = document.createElement('article');
      article.className = 'card';

      // Imagen
      const img = document.createElement('img');
      img.className = 'card-img';
      img.src       = `/panelafinca_corregido/php/assets/img/products/${esc(p.image || 'default.jpg')}`;
      img.alt       = `Foto de ${p.name}`;
      img.loading   = 'lazy';
      img.onerror   = function () { this.src = '/panelafinca_corregido/php/assets/img/products/default.jpg'; };

      // Cuerpo
      const body = document.createElement('div');
      body.className = 'card-body';

      const name = document.createElement('h3');
      name.className = 'card-name';
      name.textContent = p.name;

      const short = document.createElement('p');
      short.className = 'card-short';
      short.textContent = p.short_description || '';

      const price = document.createElement('p');
      price.className = 'card-price';
      price.textContent = formatPrice(p.price);

      const actions = document.createElement('div');
      actions.className = 'card-actions';

      const link = document.createElement('a');
      link.className = 'card-link';
      link.href = `/panelafinca_corregido/php/src/public/product.php?id=${encodeURIComponent(p.id)}`;
      link.textContent = 'Ver';

      const addBtn = document.createElement('button');
      addBtn.className = 'btn-primary add-btn';
      addBtn.dataset.id = p.id;
      addBtn.textContent = 'Agregar';

      actions.appendChild(link);
      actions.appendChild(addBtn);
      body.appendChild(name);
      body.appendChild(short);
      body.appendChild(price);
      body.appendChild(actions);
      article.appendChild(img);
      article.appendChild(body);

      return article;
    }

    async function renderProducts(q = '') {
      // Estado de carga
      productsGrid.innerHTML = '';
      const loadEl = document.createElement('div');
      loadEl.className = 'loading-state';
      loadEl.innerHTML = '<div class="spinner"></div><p>Cargando productos…</p>';
      productsGrid.appendChild(loadEl);

      try {
        const url  = `${API_BASE}?action=list_products` + (q ? `&q=${encodeURIComponent(q)}` : '');
        const data = await apiFetch(url);

        productsGrid.innerHTML = '';

        if (!data.ok || !data.data.length) {
          const empty = document.createElement('div');
          empty.className = 'empty-state';
          empty.innerHTML = '<p class="empty-icon">🌿</p><p>No se encontraron productos.</p>';
          productsGrid.appendChild(empty);
          return;
        }

        const countEl = document.getElementById('productCount');
        if (countEl) countEl.textContent = `${data.data.length} productos disponibles`;

        data.data.forEach(p => productsGrid.appendChild(buildCard(p)));

        // Delegación de eventos para botones "Agregar"
        productsGrid.addEventListener('click', handleAddClick, { once: false });

      } catch (err) {
        productsGrid.innerHTML = '';
        const errEl = document.createElement('div');
        errEl.className = 'empty-state';
        errEl.innerHTML = '<p class="empty-icon">⚠️</p><p>Error al cargar productos. Intenta de nuevo.</p>';
        productsGrid.appendChild(errEl);
        console.error('[App] renderProducts:', err);
      }
    }

    // Manejo de clic en "Agregar" en el grid — feedback visual sin alert()
    async function handleAddClick(e) {
      const btn = e.target.closest('.add-btn');
      if (!btn) return;

      const id          = btn.dataset.id;
      const originalText = btn.textContent;
      btn.disabled    = true;
      btn.textContent = '…';

      try {
        const data = await window.App.addToCart(id, 1);
        if (data.ok) {
          btn.textContent = '✓ Agregado';
          btn.style.background = 'var(--success)';
          window.App.updateCartBadge(data.count);
          setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '';
            btn.disabled = false;
          }, 1800);
        } else {
          btn.textContent = data.error || 'Error';
          btn.style.background = 'var(--error)';
          setTimeout(() => {
            btn.textContent = originalText;
            btn.style.background = '';
            btn.disabled = false;
          }, 2200);
        }
      } catch {
        btn.textContent = 'Sin conexión';
        btn.style.background = 'var(--error)';
        setTimeout(() => {
          btn.textContent = originalText;
          btn.style.background = '';
          btn.disabled = false;
        }, 2200);
      }
    }

    // Inicializar
    renderProducts();

    const searchBtn   = document.getElementById('searchBtn');
    const searchInput = document.getElementById('searchInput');

    if (searchBtn) {
      searchBtn.addEventListener('click', () => renderProducts(searchInput?.value ?? ''));
    }

    if (searchInput) {
      searchInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') renderProducts(searchInput.value);
      });
    }
  }

  // ── Contador de carrito (todas las páginas) ───────────────────
  async function refreshCartBadge() {
    try {
      const data = await apiFetch(`${API_BASE}?action=get_cart`);
      if (data.ok) window.App.updateCartBadge(data.count);
    } catch {
      // Silencioso: el badge simplemente no se actualiza
    }
  }

  document.addEventListener('DOMContentLoaded', refreshCartBadge);

})();
