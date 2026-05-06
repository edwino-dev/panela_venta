<?php
// api.php - Endpoints AJAX (JSON).
// Requiere bootstrap para sesión, CSRF y helpers.

require_once __DIR__ . '/../php/bootstrap.php';
require_once __DIR__ . '/../php/cart.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Acciones que modifican estado → requieren CSRF y POST
$mutatingActions = ['add_cart', 'update_cart', 'remove_cart', 'clear_cart'];

try {
    if (in_array($action, $mutatingActions, true)) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['ok' => false, 'error' => 'Método no permitido.'], 405);
        }
        verifyCsrf();
    }

    switch ($action) {

        case 'list_products':
            $q    = trim($_GET['q'] ?? '');
            $data = $q ? searchProducts($q) : getAllProducts();
            jsonResponse(['ok' => true, 'data' => $data]);

        case 'add_cart':
            $id  = (int)($_POST['id']  ?? 0);
            $qty = (int)($_POST['qty'] ?? 1);
            $result = addToCart($id, $qty);
            if ($result !== true) {
                jsonResponse(['ok' => false, 'error' => $result], 422);
            }
            jsonResponse([
                'ok'    => true,
                'cart'  => getCartItems(),
                'total' => getCartTotal(),
                'count' => getCartCount(),
            ]);

        case 'update_cart':
            $id  = (int)($_POST['id']  ?? 0);
            $qty = (int)($_POST['qty'] ?? 0);
            $result = updateCart($id, $qty);
            if ($result !== true) {
                jsonResponse(['ok' => false, 'error' => $result], 422);
            }
            jsonResponse([
                'ok'    => true,
                'cart'  => getCartItems(),
                'total' => getCartTotal(),
                'count' => getCartCount(),
            ]);

        case 'remove_cart':
            $id = (int)($_POST['id'] ?? 0);
            removeFromCart($id);
            jsonResponse([
                'ok'    => true,
                'cart'  => getCartItems(),
                'total' => getCartTotal(),
                'count' => getCartCount(),
            ]);

        case 'clear_cart':
            clearCart();
            jsonResponse(['ok' => true, 'cart' => [], 'total' => 0, 'count' => 0]);

        case 'get_cart':
            jsonResponse([
                'ok'    => true,
                'cart'  => getCartItems(),
                'total' => getCartTotal(),
                'count' => getCartCount(),
            ]);

        default:
            jsonResponse(['ok' => false, 'error' => 'Acción no válida.'], 400);
    }
} catch (RuntimeException $e) {
    jsonResponse(['ok' => false, 'error' => $e->getMessage()], 500);
}
