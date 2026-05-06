<?php
// product_model.php - Operaciones de lectura/escritura sobre productos.

require_once __DIR__ . '/db.php';

function getAllProducts(): array
{
    $stmt = getPDO()->query(
        'SELECT id, name, short_description, price, stock, image
           FROM products
          WHERE stock > 0
          ORDER BY created_at DESC'
    );
    return $stmt->fetchAll();
}

function getProductById(int $id): array|false
{
    if ($id <= 0) {
        return false;
    }
    $stmt = getPDO()->prepare(
        'SELECT * FROM products WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function searchProducts(string $q): array
{
    if (trim($q) === '') {
        return getAllProducts();
    }
    $like = '%' . $q . '%';
    $stmt = getPDO()->prepare(
        'SELECT id, name, short_description, price, stock, image
           FROM products
          WHERE (name LIKE ? OR description LIKE ?)
            AND stock > 0
          LIMIT 50'
    );
    $stmt->execute([$like, $like]);
    return $stmt->fetchAll();
}

/**
 * Verifica si el producto tiene stock suficiente para la cantidad solicitada.
 */
function hasStock(int $productId, int $qty): bool
{
    $stmt = getPDO()->prepare(
        'SELECT stock FROM products WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$productId]);
    $row = $stmt->fetch();
    return $row !== false && (int)$row['stock'] >= $qty;
}
