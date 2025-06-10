<?php
require '../config.php'; // Adjust path

$cart_items_details = [];
$cart_total_price = 0.00;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        try {
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.price, p.sale_price, p.is_on_sale, p.image_url, p.slug 
                FROM products p
                WHERE p.id IN ($placeholders)
            ");
            $stmt->execute($product_ids);
            $db_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($db_products as $product) {
                if (isset($_SESSION['cart'][$product['id']])) {
                    $quantity = $_SESSION['cart'][$product['id']]['quantity'];
                    $current_price = ($product['is_on_sale'] && $product['sale_price'] > 0) ? (float)$product['sale_price'] : (float)$product['price'];
                    $item_total = $current_price * $quantity;
                    $cart_total_price += $item_total;

                    $cart_items_details[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'quantity' => $quantity,
                        'price_each' => $current_price,
                        'item_total' => $item_total,
                        'image_url' => $product['image_url'] ?: '../assets/placeholder.png', // Adjust path
                        'slug' => $product['slug']
                    ];
                }
            }
        } catch (PDOException $e) {
            echo "<p class='alert alert-danger'>Error fetching cart details: " . $e->getMessage() . "</p>";
            error_log("Get Cart Content DB Error: " . $e->getMessage());
            // Optionally clear cart or handle error
            // exit(); // Or return an error message
        }
    }
}

$current_cart_item_count = 0;
foreach ($cart_items_details as $item) {
    $current_cart_item_count += $item['quantity'];
}

?>

<?php if (empty($cart_items_details)): ?>
    <p class="empty-cart-message">Your cart is currently empty.</p>
    <input type="hidden" id="current-cart-item-count" value="0">
<?php else: ?>
    <input type="hidden" id="current-cart-item-count" value="<?php echo $current_cart_item_count; ?>">
    <?php foreach ($cart_items_details as $item): ?>
        <div class="cart-item">
            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
            <div class="cart-item-details">
                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                <p>Price: ₱<?php echo number_format($item['price_each'], 2); ?></p>
                <p>Subtotal: ₱<?php echo number_format($item['item_total'], 2); ?></p>
            </div>
            <div class="cart-item-actions">
                Qty: <input type="number" class="cart-item-quantity-input" data-product-id="<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="0" style="width:60px;">
                <button class="remove-item-btn" data-product-id="<?php echo $item['id']; ?>" title="Remove item">×</button>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="cart-total">
        Total: ₱<?php echo number_format($cart_total_price, 2); ?>
    </div>
    <div class="cart-actions">
        <button class="btn btn-secondary cart-modal-close">Continue Shopping</button>
        <a href="<?php echo '../checkout/checkout.php'; ?>" class="btn btn-checkout">Proceed to Checkout</a>
    </div>
<?php endif; ?>