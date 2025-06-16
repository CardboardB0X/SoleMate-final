<?php
require '../config.php';
header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? null; // Use $_REQUEST to catch GET for get_total_quantity
$response = ['success' => false, 'message' => 'Invalid action.', 'total_quantity' => 0, 'cart_total_price' => 0.00];

// Calculate total quantity and price helper
function calculateCartTotals($pdo) {
    $total_quantity = 0;
    $cart_total_price = 0.00;
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        if (!empty($product_ids)) {
            $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
            $stmt_products = $pdo->prepare("SELECT id, price, sale_price, is_on_sale FROM products WHERE id IN ($placeholders)");
            $stmt_products->execute($product_ids);
            $products_data = $stmt_products->fetchAll(PDO::FETCH_KEY_PAIR | PDO::FETCH_GROUP); // id => [product_data]

            foreach ($_SESSION['cart'] as $product_id => $item) {
                if (isset($products_data[$product_id])) {
                    $product = $products_data[$product_id][0]; // fetchAll with FETCH_GROUP returns an array of arrays
                    $price = ($product['is_on_sale'] && $product['sale_price'] > 0) ? $product['sale_price'] : $product['price'];
                    $cart_total_price += $price * $item['quantity'];
                }
                $total_quantity += $item['quantity'];
            }
        }
    }
    return ['total_quantity' => $total_quantity, 'cart_total_price' => (float)$cart_total_price];
}


if ($action === 'add') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

        if ($product_id && $quantity > 0) {
            try {
                $stmt = $pdo->prepare("SELECT name, stock_quantity, manage_stock, stock_status FROM products WHERE id = :id AND is_active = 1");
                $stmt->bindParam(':id', $product_id);
                $stmt->execute();
                $product = $stmt->fetch();

                if ($product) {
                    if ($product['stock_status'] !== 'in_stock') {
                        $response['message'] = "Sorry, {$product['name']} is out of stock.";
                    } elseif ($product['manage_stock'] && isset($_SESSION['cart'][$product_id]['quantity']) && ($_SESSION['cart'][$product_id]['quantity'] + $quantity) > $product['stock_quantity']) {
                        $response['message'] = "Cannot add more. Only {$product['stock_quantity']} of {$product['name']} available.";
                    } elseif ($product['manage_stock'] && !isset($_SESSION['cart'][$product_id]) && $quantity > $product['stock_quantity']) {
                         $response['message'] = "Cannot add {$quantity}. Only {$product['stock_quantity']} of {$product['name']} available.";
                    } else {
                        if (isset($_SESSION['cart'][$product_id])) {
                            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                        } else {
                            $_SESSION['cart'][$product_id] = ['quantity' => $quantity];
                        }
                        $response['success'] = true;
                        $response['message'] = htmlspecialchars($product['name']) . " added to cart.";
                    }
                } else {
                    $response['message'] = "Product not found or not available.";
                }
            } catch (PDOException $e) {
                $response['message'] = "Database error: " . $e->getMessage();
                 error_log("Cart Add DB Error: " . $e->getMessage());
            }
        } else {
            $response['message'] = "Invalid product ID or quantity.";
        }
    } else {
        $response['message'] = "Missing product ID or quantity.";
    }
} elseif ($action === 'update') {
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
        $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

        if ($product_id && $quantity >= 0 && isset($_SESSION['cart'][$product_id])) { // quantity can be 0 to remove
             try {
                $stmt = $pdo->prepare("SELECT name, stock_quantity, manage_stock FROM products WHERE id = :id");
                $stmt->bindParam(':id', $product_id);
                $stmt->execute();
                $product = $stmt->fetch();

                if ($product && $product['manage_stock'] && $quantity > $product['stock_quantity']) {
                    $response['message'] = "Cannot update. Only {$product['stock_quantity']} of {$product['name']} available. Cart not updated.";
                    // Don't change success to true, let JS handle reverting or re-fetching
                } else {
                    if ($quantity == 0) {
                        unset($_SESSION['cart'][$product_id]);
                        $response['message'] = "Item removed from cart.";
                    } else {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                        $response['message'] = "Cart updated.";
                    }
                    $response['success'] = true;
                }
            } catch (PDOException $e) {
                 $response['message'] = "Database error updating cart. " . $e->getMessage();
                 error_log("Cart Update DB Error: " . $e->getMessage());
            }
        } else {
            $response['message'] = "Invalid product ID, quantity, or item not in cart.";
        }
    } else {
        $response['message'] = "Missing product ID or quantity for update.";
    }
} elseif ($action === 'remove') {
    if (isset($_POST['product_id'])) {
        $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
        if ($product_id && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $response['success'] = true;
            $response['message'] = "Item removed from cart.";
        } else {
            $response['message'] = "Invalid product ID or item not in cart.";
        }
    } else {
        $response['message'] = "Missing product ID for removal.";
    }
} elseif ($action === 'get_total_quantity') {
    $response['success'] = true; // Always successful for this read operation
    $response['message'] = "Total quantity fetched.";
} else {
    // message already set to 'Invalid action'
}

$cartTotals = calculateCartTotals($pdo);
$response['total_quantity'] = $cartTotals['total_quantity'];
$response['cart_total_price'] = $cartTotals['cart_total_price'];

echo json_encode($response);
?>