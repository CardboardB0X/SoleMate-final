<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

$cart_data = $_SESSION['cart'] ?? [];
$total_items = 0;
$total_price = 0.00;

foreach ($cart_data as $item) {
    if (isset($item['quantity']) && isset($item['price'])) {
        $total_items += $item['quantity'];
        $total_price += $item['price'] * $item['quantity'];
    }
}

echo json_encode([
    'cart' => array_values($cart_data),
    'total_items' => $total_items,
    'total_price' => $total_price
]);
?>