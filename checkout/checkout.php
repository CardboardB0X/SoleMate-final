<?php
$page_title = "Checkout - SoleMate";
$path_prefix = '../';
require '../config.php';

// User must be logged in to checkout
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to proceed to checkout.";
    $_SESSION['message_type'] = "warning";
    $_SESSION['redirect_to_checkout_after_login'] = true; // Optional: redirect back after login
    redirect($path_prefix . 'auth/login.php');
}

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty. Please add some products before checking out.";
    $_SESSION['message_type'] = "info";
    redirect($path_prefix . 'index.php');
}

require $path_prefix . 'templates/header.php';

// Fetch user details for pre-filling form (if available)
$user_details = null;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_user = $pdo->prepare("SELECT first_name, last_name, email, phone FROM users WHERE user_id = :user_id");
        $stmt_user->bindParam(':user_id', $_SESSION['user_id']);
        $stmt_user->execute();
        $user_details = $stmt_user->fetch();
    } catch (PDOException $e) {
        // Log error, but continue
        error_log("Checkout - Error fetching user details: " . $e->getMessage());
    }
}

// Fetch cart items for summary
$cart_items_summary = [];
$order_subtotal = 0;
$product_ids = array_keys($_SESSION['cart']);
if (!empty($product_ids)) {
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $stmt_cart = $pdo->prepare("SELECT id, name, price, sale_price, is_on_sale FROM products WHERE id IN ($placeholders)");
    $stmt_cart->execute($product_ids);
    $db_products_summary = $stmt_cart->fetchAll(PDO::FETCH_KEY_PAIR | PDO::FETCH_GROUP);

    foreach ($_SESSION['cart'] as $product_id => $item_data) {
        if (isset($db_products_summary[$product_id])) {
            $product = $db_products_summary[$product_id][0];
            $price = ($product['is_on_sale'] && $product['sale_price'] > 0) ? (float)$product['sale_price'] : (float)$product['price'];
            $cart_items_summary[] = [
                'name' => $product['name'],
                'quantity' => $item_data['quantity'],
                'price' => $price,
                'subtotal' => $price * $item_data['quantity']
            ];
            $order_subtotal += ($price * $item_data['quantity']);
        }
    }
}
$shipping_fee = 50.00; // Example fixed shipping fee
$order_total = $order_subtotal + $shipping_fee;

?>

<div class="checkout-container" style="display: flex; gap: 30px; margin-top: 20px;">
    <div class="checkout-form-section" style="flex: 2;">
        <h2>Checkout</h2>
        <form action="place_order.php" method="POST" id="checkoutForm">
            <h3>Billing Details</h3>
            <div class="form-group">
                <label for="billing_first_name">First Name</label>
                <input type="text" id="billing_first_name" name="billing_first_name" value="<?php echo htmlspecialchars($user_details['first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_last_name">Last Name</label>
                <input type="text" id="billing_last_name" name="billing_last_name" value="<?php echo htmlspecialchars($user_details['last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_email">Email</label>
                <input type="email" id="billing_email" name="billing_email" value="<?php echo htmlspecialchars($user_details['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_phone">Phone</label>
                <input type="text" id="billing_phone" name="billing_phone" value="<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="billing_address_line1">Address Line 1</label>
                <input type="text" id="billing_address_line1" name="billing_address_line1" required>
            </div>
            <div class="form-group">
                <label for="billing_address_line2">Address Line 2 (Optional)</label>
                <input type="text" id="billing_address_line2" name="billing_address_line2">
            </div>
            <div class="form-group">
                <label for="billing_city">City</label>
                <input type="text" id="billing_city" name="billing_city" required>
            </div>
            <div class="form-group">
                <label for="billing_zip_code">Zip Code</label>
                <input type="text" id="billing_zip_code" name="billing_zip_code" required>
            </div>
            <input type="hidden" name="billing_country" value="Philippines"> <!-- Default -->

            <div class="form-group">
                <input type="checkbox" id="ship_to_billing_address" name="ship_to_billing_address" value="1" checked>
                <label for="ship_to_billing_address" style="display:inline;">Ship to billing address</label>
            </div>

            <div id="shipping_details_section" style="display:none;">
                <h3>Shipping Details</h3>
                <div class="form-group"><label for="shipping_first_name">First Name</label><input type="text" id="shipping_first_name" name="shipping_first_name"></div>
                <div class="form-group"><label for="shipping_last_name">Last Name</label><input type="text" id="shipping_last_name" name="shipping_last_name"></div>
                <div class="form-group"><label for="shipping_email">Email</label><input type="email" id="shipping_email" name="shipping_email"></div>
                <div class="form-group"><label for="shipping_phone">Phone</label><input type="text" id="shipping_phone" name="shipping_phone"></div>
                <div class="form-group"><label for="shipping_address_line1">Address Line 1</label><input type="text" id="shipping_address_line1" name="shipping_address_line1"></div>
                <div class="form-group"><label for="shipping_address_line2">Address Line 2</label><input type="text" id="shipping_address_line2" name="shipping_address_line2"></div>
                <div class="form-group"><label for="shipping_city">City</label><input type="text" id="shipping_city" name="shipping_city"></div>
                <div class="form-group"><label for="shipping_zip_code">Zip Code</label><input type="text" id="shipping_zip_code" name="shipping_zip_code"></div>
                <input type="hidden" name="shipping_country" value="Philippines">
            </div>

            <h3>Payment Method</h3>
            <div class="form-group">
                <select name="payment_method" id="payment_method" required>
                    <option value="cod">Cash on Delivery (COD)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="customer_notes">Order Notes (Optional)</label>
                <textarea id="customer_notes" name="customer_notes"></textarea>
            </div>
            
            <input type="hidden" name="order_subtotal" value="<?php echo $order_subtotal; ?>">
            <input type="hidden" name="shipping_fee" value="<?php echo $shipping_fee; ?>">
            <input type="hidden" name="order_total" value="<?php echo $order_total; ?>">

            <button type="submit" class="form-button">Place Order</button>
        </form>
    </div>

    <div class="order-summary-section" style="flex: 1; background: #f9f9f9; padding: 20px; border-radius: 5px;">
        <h3>Your Order</h3>
        <?php foreach ($cart_items_summary as $item): ?>
        <div style="display:flex; justify-content: space-between; padding-bottom: 5px; margin-bottom: 5px; border-bottom: 1px solid #eee;">
            <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
            <span>₱<?php echo number_format($item['subtotal'], 2); ?></span>
        </div>
        <?php endforeach; ?>
        <hr>
        <div style="display:flex; justify-content: space-between;"><strong>Subtotal:</strong> <strong>₱<?php echo number_format($order_subtotal, 2); ?></strong></div>
        <div style="display:flex; justify-content: space-between;"><strong>Shipping:</strong> <strong>₱<?php echo number_format($shipping_fee, 2); ?></strong></div>
        <hr>
        <div style="display:flex; justify-content: space-between; font-size: 1.2em;"><strong>Total:</strong> <strong>₱<?php echo number_format($order_total, 2); ?></strong></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shipToBillingCheckbox = document.getElementById('ship_to_billing_address');
    const shippingDetailsSection = document.getElementById('shipping_details_section');
    const shippingInputs = shippingDetailsSection.querySelectorAll('input, select, textarea');

    function toggleShippingFieldsRequired(required) {
        shippingInputs.forEach(input => {
            if (['shipping_first_name', 'shipping_last_name', 'shipping_address_line1', 'shipping_city', 'shipping_zip_code'].includes(input.name)) {
                 input.required = required;
            }
        });
    }

    shipToBillingCheckbox.addEventListener('change', function() {
        if (this.checked) {
            shippingDetailsSection.style.display = 'none';
            toggleShippingFieldsRequired(false);
        } else {
            shippingDetailsSection.style.display = 'block';
            toggleShippingFieldsRequired(true);
        }
    });
    // Initial state
    if (shipToBillingCheckbox.checked) {
        shippingDetailsSection.style.display = 'none';
        toggleShippingFieldsRequired(false);
    } else {
        shippingDetailsSection.style.display = 'block';
        toggleShippingFieldsRequired(true);
    }
});
</script>


<?php
require $path_prefix . 'templates/footer.php';
?>