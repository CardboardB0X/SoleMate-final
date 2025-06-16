<?php
require_once __DIR__ . '/config.php'; // For DB, SITE_URL, e()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security: User must be logged in
if (!isset($_SESSION['user_id'])) {
    // Should ideally not happen if checkout.php protects itself
    $_SESSION['checkout_error_message'] = "You must be logged in to place an order.";
    header("Location: " . SITE_URL . "index.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Cart Check: Cart must not be empty
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Your cart is empty. Cannot place order.'];
    header("Location: " . SITE_URL . "shop.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . SITE_URL . "checkout.php"); // Redirect if not POST
    exit;
}

$errors = [];
$form_data = $_POST; // Store for repopulating form if errors

// --- Validate Billing Details ---
$billing_first_name = trim($form_data['billing_first_name'] ?? '');
// ... (Add validation for ALL billing fields: last_name, email, phone, address_line1, city, zip_code, country)
if (empty($billing_first_name)) $errors[] = "Billing First Name is required.";
// Add more billing validations...

// --- Validate Shipping Details (if not same as billing) ---
$ship_to_billing = isset($form_data['ship_to_billing_address']) && $form_data['ship_to_billing_address'] == '1';
$shipping_first_name = ''; // Initialize

if (!$ship_to_billing) {
    $shipping_first_name = trim($form_data['shipping_first_name'] ?? '');
    // ... (Add validation for ALL shipping fields if different)
    if (empty($shipping_first_name)) $errors[] = "Shipping First Name is required.";
    // Add more shipping validations...
} else {
    // If shipping to billing, copy billing details to shipping variables for DB insertion
    $shipping_first_name = $billing_first_name;
    // ... (Copy all other billing details to corresponding shipping variables)
    $form_data['shipping_first_name'] = $billing_first_name;
    $form_data['shipping_last_name'] = trim($form_data['billing_last_name'] ?? '');
    $form_data['shipping_email'] = trim($form_data['billing_email'] ?? ''); // Optional for shipping
    $form_data['shipping_phone'] = trim($form_data['billing_phone'] ?? '');
    $form_data['shipping_address_line1'] = trim($form_data['billing_address_line1'] ?? '');
    $form_data['shipping_address_line2'] = trim($form_data['billing_address_line2'] ?? '');
    $form_data['shipping_city'] = trim($form_data['billing_city'] ?? '');
    $form_data['shipping_zip_code'] = trim($form_data['billing_zip_code'] ?? '');
    $form_data['shipping_country'] = trim($form_data['billing_country'] ?? 'Philippines');
}


// --- Validate Shipping Method ---
$shipping_method_id = filter_input(INPUT_POST, 'shipping_method_id', FILTER_VALIDATE_INT);
$shipping_method_details = null;
if ($shipping_method_id) {
    try {
        $stmt = $pdo->prepare("SELECT name, cost FROM shipping_methods WHERE method_id = :id AND is_active = 1");
        $stmt->execute([':id' => $shipping_method_id]);
        $shipping_method_details = $stmt->fetch();
        if (!$shipping_method_details) $errors[] = "Invalid shipping method selected.";
    } catch (PDOException $e) {
        $errors[] = "Error verifying shipping method.";
        error_log("Checkout shipping verify error: " . $e->getMessage());
    }
} else {
    $errors[] = "Shipping method is required.";
}

// --- Validate Payment Method ---
$payment_method = $form_data['payment_method'] ?? '';
$allowed_payment_methods = ['cod', 'bank_transfer', 'card_mock'];
if (!in_array($payment_method, $allowed_payment_methods)) {
    $errors[] = "Invalid payment method selected.";
}
// Add specific validation for card_mock fields if payment_method is 'card_mock'
// For a real payment gateway, this is where you'd make API calls.

// --- Customer Notes ---
$customer_notes = trim($form_data['customer_notes'] ?? '');


// --- If errors, redirect back to checkout with errors and form data ---
if (!empty($errors)) {
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_form_data'] = $form_data;
    header("Location: " . SITE_URL . "checkout.php");
    exit;
}

// --- All Validations Passed - Proceed to Create Order ---
try {
    $pdo->beginTransaction();

    // 1. Calculate totals again on server-side for security
    $order_subtotal = 0;
    $order_items_for_db = [];
    foreach ($cart as $productId => $cart_item) {
        // Fetch current product price from DB to prevent price tampering
        $prod_stmt = $pdo->prepare("SELECT price, sale_price, is_on_sale, stock_quantity, manage_stock, stock_status FROM products WHERE id = :id AND is_active = 1");
        $prod_stmt->execute([':id' => $productId]);
        $db_product = $prod_stmt->fetch();

        if (!$db_product) { throw new Exception("Product " . e($cart_item['name']) . " is no longer available."); }
        
        // Stock Check again right before order creation
        if ($db_product['manage_stock']) {
            if ($db_product['stock_status'] === 'out_of_stock' || ($db_product['stock_status'] === 'in_stock' && $db_product['stock_quantity'] < $cart_item['quantity'])) {
                throw new Exception("Sorry, " . e($cart_item['name']) . " is out of stock or has insufficient quantity.");
            }
        }

        $current_price = ($db_product['is_on_sale'] && $db_product['sale_price'] > 0 && $db_product['sale_price'] < $db_product['price'])
                         ? (float)$db_product['sale_price']
                         : (float)$db_product['price'];
        
        $item_subtotal = $current_price * $cart_item['quantity'];
        $order_subtotal += $item_subtotal;

        $order_items_for_db[] = [
            'product_id' => $productId,
            'product_sku' => $db_product['sku'] ?? null, // Assuming SKU is in products table
            'product_name' => $cart_item['name'],
            'quantity' => $cart_item['quantity'],
            'unit_price' => $current_price,
            'item_subtotal' => $item_subtotal,
            'item_discount' => 0.00, // Implement item-specific discount logic if needed
            'item_total' => $item_subtotal // Assuming no item-specific discount for now
        ];
    }

    $shipping_fee = (float)($shipping_method_details['cost'] ?? 0.00);
    $discount_amount_order = 0.00; // Implement order-level discount logic if needed
    $order_total = $order_subtotal - $discount_amount_order + $shipping_fee;
    $order_uid = uniqid('SOLEMATE_', true); // Generate a unique order ID

    // 2. Insert into `orders` table
    $order_stmt = $pdo->prepare("
        INSERT INTO orders (order_uid, user_id, billing_first_name, billing_last_name, billing_email, billing_phone, 
                            billing_address_line1, billing_address_line2, billing_city, billing_zip_code, billing_country,
                            shipping_first_name, shipping_last_name, shipping_email, shipping_phone,
                            shipping_address_line1, shipping_address_line2, shipping_city, shipping_zip_code, shipping_country,
                            ship_to_billing_address, order_subtotal, shipping_method_name, shipping_fee, 
                            discount_code, discount_amount, order_total, payment_method, payment_status, order_status, 
                            customer_notes, ip_address, user_agent)
        VALUES (:order_uid, :user_id, :bfn, :bln, :be, :bp, :bal1, :bal2, :bc, :bzc, :bcy,
                :sfn, :sln, :se, :sp, :sal1, :sal2, :sc, :szc, :scy,
                :stba, :osub, :smn, :sf, :dc, :da, :otot, :pm, :ps, :os, :cn, :ip, :ua)
    ");

    $order_status = 'pending_payment'; // Default status
    $payment_status = 'pending';
    if ($payment_method === 'cod') { // For COD, order status could be 'processing'
        $order_status = 'processing';
        // $payment_status = 'pending'; // Or 'paid_on_delivery' or similar later
    }
    // For 'card_mock', if successful, payment_status would be 'paid'
    if ($payment_method === 'card_mock') {
        // Simulate payment processing
        $payment_successful_mock = true; // Assume success for mock
        if ($payment_successful_mock) {
            $payment_status = 'paid';
            $order_status = 'processing'; // Or directly to processing
        } else {
            $payment_status = 'failed';
            $order_status = 'on_hold'; // Or failed_payment
            throw new Exception("Mock card payment failed."); // Or handle gracefully
        }
    }


    $order_stmt->execute([
        ':order_uid' => $order_uid, ':user_id' => $user_id,
        ':bfn' => $billing_first_name, ':bln' => trim($form_data['billing_last_name'] ?? ''), ':be' => trim($form_data['billing_email'] ?? ''), ':bp' => trim($form_data['billing_phone'] ?? ''),
        ':bal1' => trim($form_data['billing_address_line1'] ?? ''), ':bal2' => trim($form_data['billing_address_line2'] ?? null),
        ':bc' => trim($form_data['billing_city'] ?? ''), ':bzc' => trim($form_data['billing_zip_code'] ?? ''), ':bcy' => trim($form_data['billing_country'] ?? 'Philippines'),
        
        ':sfn' => $form_data['shipping_first_name'], ':sln' => $form_data['shipping_last_name'], ':se' => $form_data['shipping_email'] ?? null, ':sp' => $form_data['shipping_phone'],
        ':sal1' => $form_data['shipping_address_line1'], ':sal2' => $form_data['shipping_address_line2'] ?? null,
        ':sc' => $form_data['shipping_city'], ':szc' => $form_data['shipping_zip_code'], ':scy' => $form_data['shipping_country'],
        
        ':stba' => $ship_to_billing ? 1 : 0,
        ':osub' => $order_subtotal,
        ':smn' => $shipping_method_details['name'] ?? 'N/A', ':sf' => $shipping_fee,
        ':dc' => null, ':da' => $discount_amount_order, // Placeholder for discount code/amount
        ':otot' => $order_total,
        ':pm' => $payment_method, ':ps' => $payment_status, ':os' => $order_status,
        ':cn' => !empty($customer_notes) ? $customer_notes : null,
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    $order_db_id = $pdo->lastInsertId();

    // 3. Insert into `order_items` table
    $item_stmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, product_sku, product_name, quantity, unit_price, item_subtotal, item_discount, item_total)
        VALUES (:order_id, :pid, :psku, :pname, :qty, :uprice, :isub, :idis, :itot)
    ");
    foreach ($order_items_for_db as $item_data) {
        $item_stmt->execute([
            ':order_id' => $order_db_id,
            ':pid' => $item_data['product_id'],
            ':psku' => $item_data['product_sku'],
            ':pname' => $item_data['product_name'],
            ':qty' => $item_data['quantity'],
            ':uprice' => $item_data['unit_price'],
            ':isub' => $item_data['item_subtotal'],
            ':idis' => $item_data['item_discount'],
            ':itot' => $item_data['item_total']
        ]);

        // 4. Update product stock (if manage_stock is enabled)
        $prod_stock_stmt = $pdo->prepare("SELECT stock_quantity, manage_stock, stock_status FROM products WHERE id = :id");
        $prod_stock_stmt->execute([':id' => $item_data['product_id']]);
        $product_stock_info = $prod_stock_stmt->fetch();

        if ($product_stock_info && $product_stock_info['manage_stock']) {
            $new_stock = $product_stock_info['stock_quantity'] - $item_data['quantity'];
            $new_stock_status = ($new_stock <= 0) ? 'out_of_stock' : $product_stock_info['stock_status'];
            
            $update_stock_stmt = $pdo->prepare("UPDATE products SET stock_quantity = :new_stock, stock_status = :new_status WHERE id = :id");
            $update_stock_stmt->execute([':new_stock' => $new_stock, ':new_status' => $new_stock_status, ':id' => $item_data['product_id']]);
        }
    }

    $pdo->commit();

    // 5. Clear the cart from session
    unset($_SESSION['cart']);

    // 6. Send order confirmation email (TODO)
    // mail($billing_email, "Your Solemate Order #".$order_uid." Confirmed", "Thank you for your order! ...");

    // 7. Redirect to an order success / thank you page
    $_SESSION['order_confirmation_uid'] = $order_uid;
    header("Location: " . SITE_URL . "order_success.php");
    exit;

} catch (Exception $e) { // Catch both PDOException and general Exception
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Checkout Processing Error: " . $e->getMessage());
    $_SESSION['checkout_errors'] = ["An error occurred while processing your order: " . e($e->getMessage()) . " Please try again or contact support."];
    $_SESSION['checkout_form_data'] = $form_data; // Keep form data for repopulation
    header("Location: " . SITE_URL . "checkout.php");
    exit;
}
?>