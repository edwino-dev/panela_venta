# PanelaFinca — Tienda Online

Aplicación PHP para venta de panela artesanal. Construida con PHP 8.1+, PDO/MySQL y Vanilla JS.

## Estructura del proyecto

```
php/
├── assets/
│   ├── css/style.css          # Estilos responsivos
│   ├── js/app.js              # Lógica cliente (sin dependencias)
│   └── img/products/          # Imágenes de productos
├── sql/
│   └── schema.sql             # Crear BD, tablas e insertar datos de prueba
└── src/
    ├── php/                   # Capa de negocio (no accesible desde web)
    │   ├── bootstrap.php      # Inicialización única: sesión, CSRF, errores
    │   ├── config.php         # Constantes de configuración
    │   ├── db.php             # Singleton PDO
    │   ├── helpers.php        # e(), redirect(), csrfToken(), jsonResponse()
    │   ├── product_model.php  # CRUD de productos
    │   └── cart.php           # Lógica del carrito en sesión
    ├── public/                # Controladores accesibles desde web
    │   ├── index.php          # Página principal / catálogo
    │   ├── product.php        # Detalle de producto  (?id=N)
    │   ├── cart.php           # Página del carrito
    │   └── api.php            # Endpoints AJAX (JSON)
    └── templates/
        ├── header.php         # Cabecera HTML
        └── footer.php         # Pie de página HTML
```

## Instalación (XAMPP)

1. Copia la carpeta `php/` en `C:\xampp\htdocs\ventas_panela\` (o `/var/www/html/ventas_panela/`).
2. Importa la base de datos: `mysql -u root < sql/schema.sql`
3. Ajusta `src/php/config.php` si tu usuario/contraseña MySQL son distintos.
4. Agrega imágenes de productos en `assets/img/products/` o usa `default.jpg`.
5. Abre `http://localhost/ventas_panela/src/public/` en tu navegador.

## Buenas prácticas implementadas

- **Separación de responsabilidades**: bootstrap, config, db, helpers, modelos y vistas en capas independientes.
- **Prevención de XSS**: función `e()` / `htmlspecialchars` en PHP; `textContent` en JS (nunca `innerHTML` con datos externos).
- **Protección CSRF**: token en `<meta>` enviado en cada POST; validado con `hash_equals` en `api.php`.
- **Sesión segura**: `session_status()` antes de `session_start()`; cookies `httponly` y `samesite=Lax`.
- **PDO preparado**: `ATTR_EMULATE_PREPARES = false` para consultas reales parametrizadas.
- **Validación de stock**: `hasStock()` antes de agregar al carrito.
- **Manejo de errores**: `try/catch` en JS y PHP; respuestas HTTP con código apropiado.
- **Accesibilidad**: atributos `aria-*`, `role="alert"`, `aria-live`, `<label>` vinculados.
