<?php
require_once __DIR__ . '/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : (isset($_GET['product_id']) ? (int)$_GET['product_id'] : null);
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!$action || !$productId || $productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request. Action or Product ID missing.']);
    exit;
}
if ($quantity <= 0 && $action !== 'remove') {
    $quantity = 1;
}
if ($quantity > 99) {
    $quantity = 99;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, price, sale_price, is_on_sale, image_url, stock_quantity, stock_status, manage_stock, slug FROM products WHERE id = :id AND is_active = 1");
    $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found or not available.']);
        exit;
    }

    $actualPrice = ($product['is_on_sale'] && $product['sale_price'] > 0 && $product['sale_price'] < $product['price'])
                   ? (float)$product['sale_price']
                   : (float)$product['price'];

    if ($action === 'add' || $action === 'update') {
        $requested_total_quantity = $quantity;
        if ($action === 'add' && isset($_SESSION['cart'][$productId])) {
            $requested_total_quantity = $_SESSION['cart'][$productId]['quantity'] + $quantity;
        }

        if ($product['manage_stock']) {
            if ($product['stock_status'] === 'out_of_stock') {
                echo json_encode(['success' => false, 'message' => e($product['name']) . ' is out of stock.']);
                exit;
            }
            if ($product['stock_status'] === 'in_stock' && $product['stock_quantity'] < $requested_total_quantity) {
                echo json_encode(['success' => false, 'message' => 'Not enough stock for ' . e($product['name']) . '. Available: ' . e($product['stock_quantity'])]);
                exit;
            }
        }
    }

    switch ($action) {
        case 'add':
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$productId] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $actualPrice,
                    'quantity' => $quantity,
                    'image_url' => $product['image_url'] ?: ($path_prefix ?? '') . 'assets/images/placeholder.png',
                    'slug' => $product['slug'] ?? 'product-slug'
                ];
            }
            if ($product['manage_stock'] && $product['stock_status'] === 'in_stock' && $_SESSION['cart'][$productId]['quantity'] > $product['stock_quantity']) {
                $_SESSION['cart'][$productId]['quantity'] = $product['stock_quantity'];
                 echo json_encode(['success' => true, 'message' => e($product['name']) . ' quantity adjusted to available stock: ' . e($product['stock_quantity']), 'cart' => $_SESSION['cart']]);
                 exit;
            }
            echo json_encode(['success' => true, 'message' => e($product['name']) . ' added to cart.', 'cart' => $_SESSION['cart']]);
            break;
        case 'update':
            if (isset($_SESSION['cart'][$productId])) {
                if ($quantity > 0) {
                    if ($product['manage_stock'] && $product['stock_status'] === 'in_stock' && $quantity > $product['stock_quantity']) {
                        $_SESSION['cart'][$productId]['quantity'] = $product['stock_quantity'];
                        echo json_encode(['success' => true, 'message' => 'Quantity for ' . e($product['name']) . ' adjusted to available stock: ' . e($product['stock_quantity']), 'cart' => $_SESSION['cart']]);
                    } else {
                        $_SESSION['cart'][$productId]['quantity'] = $quantity;
                        echo json_encode(['success' => true, 'message' => 'Cart updated.', 'cart' => $_SESSION['cart']]);
                    }
                } else {
                    unset($_SESSION['cart'][$productId]);
                    echo json_encode(['success' => true, 'message' => e($product['name']) . ' removed from cart.', 'cart' => $_SESSION['cart']]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not in cart to update.']);
            }
            break;
        case 'remove':
            if (isset($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
                echo json_encode(['success' => true, 'message' => e($product['name']) . ' removed from cart.', 'cart' => $_SESSION['cart']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not in cart to remove.']);
            }
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid cart action.']);
            break;
    }
} catch (PDOException $e) {
    error_log("Cart Action PDO Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error during cart operation.']);
} catch (Exception $e) {
    error_log("Cart Action General Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}
?>