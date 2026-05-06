<?php
// cart.php - Gestión del carrito en sesión.
// Depende de product_model para validar stock real.

require_once __DIR__ . '/product_model.php';

/**
 * Devuelve los ítems actuales del carrito.
 * @return array<int, array{id:int, name:string, price:float, qty:int}>
 */
function getCartItems(): array
{
    return $_SESSION['cart'] ?? [];
}

/**
 * Agrega un producto al carrito validando stock.
 * Devuelve true si se agregó, o un string de error.
 */
function addToCart(int $productId, int $qty = 1): true|string
{
    if ($qty <= 0) {
        return 'La cantidad debe ser mayor a cero.';
    }

    $product = getProductById($productId);
    if ($product === false) {
        return 'Producto no encontrado.';
    }

    $currentQty = (int)($_SESSION['cart'][$productId]['qty'] ?? 0);
    $newQty = $currentQty + $qty;

    if (!hasStock($productId, $newQty)) {
        return 'Stock insuficiente. Disponible: ' . $product['stock'];
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['qty'] = $newQty;
    } else {
        $_SESSION['cart'][$productId] = [
            'id'    => $product['id'],
            'name'  => $product['name'],
            'price' => (float)$product['price'],
            'qty'   => $qty,
        ];
    }

    return true;
}

/**
 * Actualiza la cantidad de un ítem. Cantidad 0 elimina el ítem.
 * Devuelve true o string de error.
 */
function updateCart(int $productId, int $qty): true|string
{
    if (!isset($_SESSION['cart'][$productId])) {
        return 'Ítem no encontrado en el carrito.';
    }

    if ($qty <= 0) {
        unset($_SESSION['cart'][$productId]);
        return true;
    }

    if (!hasStock($productId, $qty)) {
        return 'Stock insuficiente.';
    }

    $_SESSION['cart'][$productId]['qty'] = $qty;
    return true;
}

/**
 * Elimina un ítem del carrito.
 */
function removeFromCart(int $productId): void
{
    unset($_SESSION['cart'][$productId]);
}

/**
 * Vacía el carrito completo.
 */
function clearCart(): void
{
    $_SESSION['cart'] = [];
}

/**
 * Devuelve el total del carrito.
 */
function getCartTotal(): float
{
    $total = 0.0;
    foreach (getCartItems() as $item) {
        $total += (float)$item['price'] * (int)$item['qty'];
    }
    return $total;
}

/**
 * Devuelve la cantidad total de unidades en el carrito.
 */
function getCartCount(): int
{
    return array_sum(array_column(getCartItems(), 'qty'));
}
