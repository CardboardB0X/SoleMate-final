<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No product ID provided.']);
    exit;
}
$productId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($productId === false || $productId === null || $productId <= 0) {
    echo json_encode(['error' => 'Invalid product ID.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.id, p.name, p.slug, p.description, p.short_description,
            p.price, p.sale_price, p.is_on_sale, p.image_url,
            p.stock_quantity, p.stock_status, p.sku, p.manage_stock,
            b.name AS brand_name,
            c.name AS category_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.id = :id AND p.is_active = 1
    ");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();

    if ($product) {
        $product['price'] = (float)$product['price'];
        if ($product['sale_price'] !== null) {
            $product['sale_price'] = (float)$product['sale_price'];
        }
        $product['is_on_sale'] = (bool)$product['is_on_sale'];
        if ($product['stock_quantity'] !== null) {
            $product['stock_quantity'] = (int)$product['stock_quantity'];
        }
        $product['manage_stock'] = (bool)$product['manage_stock'];
        echo json_encode($product);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found or not available.']);
    }
} catch (PDOException $e) {
    error_log("Error in get_product_details.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed. Please try again later.']);
}
?>