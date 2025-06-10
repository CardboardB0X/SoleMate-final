<?php
require '../config.php'; // Go up one directory

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php'); // Only POST requests
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "You need to be logged in to place an order.";
    $_SESSION['message_type'] = "danger";
    redirect('../auth/login.php');
}
if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Your cart is empty.";
    $_SESSION['message_type'] = "warning";
    redirect('../index.php');
}

// --- Collect and Sanitize Data ---
// Billing
$billing_first_name = trim($_POST['billing_first_name'] ?? '');
$billing_last_name  = trim($_POST['billing_last_name'] ?? '');
$billing_email      = filter_var(trim($_POST['billing_email'] ?? ''), FILTER_SANITIZE_EMAIL);
$billing_phone      = trim($_POST['billing_phone'] ?? '');
$billing_address_line1 = trim($_POST['billing_address_line1'] ?? '');
$billing_address_line2 = trim($_POST['billing_address_line2'] ?? null);
$billing_city       = trim($_POST['billing_city'] ?? '');
$billing_zip_code   = trim($_POST['billing_zip_code'] ?? '');
$billing_country    = trim($_POST['billing_country'] ?? 'Philippines');

// Shipping (if different)
$ship_to_billing = isset($_POST['ship_to_billing_address']) && $_POST['ship_to_billing_address'] == '1';

$shipping_first_name = $ship_to_billing ? $billing_first_name : trim($_POST['shipping_first_name'] ?? '');
// ... (similarly for all shipping fields)
$shipping_last_name      = $ship_to_billing ? $billing_last_name : trim($_POST['shipping_last_name'] ?? '');
$shipping_email          = $ship_to_billing ? $billing_email : filter_var(trim($_POST['shipping_email'] ?? ''), FILTER_SANITIZE_EMAIL);
$shipping_phone          = $ship_to_billing ? $billing_phone : trim($_POST['shipping_phone'] ?? '');
$shipping_address_line1  = $ship_to_billing ? $billing_address_line1 : trim($_POST['shipping_address_line1'] ?? '');
$shipping_address_line2  = $ship_to_billing ? $billing_address_line2 : trim($_POST['shipping_address_line2'] ?? null);
$shipping_city           = $ship_to_billing ? $billing_city : trim($_POST['shipping_city'] ?? '');
$shipping_zip_code       = $ship_to_billing ? $billing_zip_code : trim($_POST['shipping_zip_code'] ?? '');
$shipping_country        = $ship_to_billing ? $billing_country : trim($_POST['shipping_country'] ?? 'Philippines');


// Order Meta
$payment_method   = trim($_POST['payment_method'] ?? 'cod');
$customer_notes   = trim($_POST['customer_notes'] ?? null);
$order_subtotal_form = (float)($_POST['order_subtotal'] ?? 0);
$shipping_fee_form   = (float)($_POST['shipping_fee'] ?? 0);
$order_total_form    = (float)($_POST['order_total'] ?? 0);

// --- Basic Validation (add more as needed) ---
if (empty($billing_first_name) || empty($billing_last_name) || empty($billing_email) || empty($billing_address_line1) || empty($billing_city) || empty($billing_zip_code)) {
    $_SESSION['message'] = "Please fill all required billing fields.";
    $_SESSION['message_type'] = "danger";
    redirect('checkout.php');
}
if (!$ship_to_billing && (empty($shipping_first_name) || empty($shipping_address_line1) /* ... more shipping fields */)) {
     $_SESSION['message'] = "Please fill all required shipping fields if shipping to a different address.";
     $_SESSION['message_type'] = "danger";
     redirect('checkout.php');
}


// --- Recalculate totals server-side for security ---
$calculated_subtotal = 0;
$product_ids_in_cart = array_keys($_SESSION['cart']);
$order_items_data = []; // To store item details for insertion

if (!empty($product_ids_in_cart)) {
    $placeholders = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
    $stmt_products = $pdo->prepare("SELECT id, name, sku, price, sale_price, is_on_sale, stock_quantity, manage_stock FROM products WHERE id IN ($placeholders)");
    $stmt_products->execute($product_ids_in_cart);
    $products_from_db = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
    $products_map = [];
    foreach ($products_from_db as $p) { $products_map[$p['id']] = $p; }

    foreach ($_SESSION['cart'] as $pid => $item) {
        if (!isset($products_map[$pid])) {
            $_SESSION['message'] = "One or more products in your cart are no longer available. Please review your cart.";
            $_SESSION['message_type'] = "danger";
            redirect('../index.php'); // Or cart page
        }
        $product = $products_map[$pid];
        $quantity_ordered = $item['quantity'];

        // Stock Check
        if ($product['manage_stock'] && $quantity_ordered > $product['stock_quantity']) {
            $_SESSION['message'] = "Not enough stock for " . htmlspecialchars($product['name']) . ". Available: " . $product['stock_quantity'] . ". Your cart had " . $quantity_ordered . ". Order not placed.";
            $_SESSION['message_type'] = "danger";
            redirect('checkout.php'); // Or back to cart page
        }

        $unit_price = ($product['is_on_sale'] && $product['sale_price'] > 0) ? (float)$product['sale_price'] : (float)$product['price'];
        $item_subtotal = $unit_price * $quantity_ordered;
        $calculated_subtotal += $item_subtotal;

        $order_items_data[] = [
            'product_id' => $pid,
            'product_sku' => $product['sku'],
            'product_name' => $product['name'],
            'quantity' => $quantity_ordered,
            'unit_price' => $unit_price,
            'item_subtotal' => $item_subtotal,
            'item_total' => $item_subtotal // Assuming no item-specific discount for now
        ];
    }
}
$calculated_shipping_fee = 50.00; // Should be dynamic or from settings
$calculated_order_total = $calculated_subtotal + $calculated_shipping_fee;

// Compare form total with calculated total (simple check)
if (abs($order_total_form - $calculated_order_total) > 0.01) { // Allow for small float discrepancies
    $_SESSION['message'] = "There was an issue with the order total. Please try again.";
    $_SESSION['message_type'] = "danger";
    error_log("Order total mismatch: Form {$order_total_form}, Calculated {$calculated_order_total}");
    redirect('checkout.php');
}


// --- Start Transaction ---
try {
    $pdo->beginTransaction();

    $order_uid = uniqid('ORD-', true); // Generate a unique order ID
    $user_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    $stmt_order = $pdo->prepare("
        INSERT INTO orders (
            order_uid, user_id, 
            billing_first_name, billing_last_name, billing_email, billing_phone, 
            billing_address_line1, billing_address_line2, billing_city, billing_zip_code, billing_country,
            shipping_first_name, shipping_last_name, shipping_email, shipping_phone,
            shipping_address_line1, shipping_address_line2, shipping_city, shipping_zip_code, shipping_country,
            ship_to_billing_address,
            order_subtotal, shipping_fee, order_total, payment_method, 
            payment_status, order_status, customer_notes, ip_address, user_agent
        ) VALUES (
            :order_uid, :user_id,
            :b_fname, :b_lname, :b_email, :b_phone, :b_addr1, :b_addr2, :b_city, :b_zip, :b_country,
            :s_fname, :s_lname, :s_email, :s_phone, :s_addr1, :s_addr2, :s_city, :s_zip, :s_country,
            :ship_to_billing,
            :subtotal, :shipping, :total, :pmethod,
            'pending', 'pending_payment', :notes, :ip, :ua
        )
    ");

    $stmt_order->execute([
        ':order_uid' => $order_uid, ':user_id' => $user_id,
        ':b_fname' => $billing_first_name, ':b_lname' => $billing_last_name, ':b_email' => $billing_email, ':b_phone' => $billing_phone,
        ':b_addr1' => $billing_address_line1, ':b_addr2' => $billing_address_line2, ':b_city' => $billing_city, ':b_zip' => $billing_zip_code, ':b_country' => $billing_country,
        ':s_fname' => $shipping_first_name, ':s_lname' => $shipping_last_name, ':s_email' => $shipping_email, ':s_phone' => $shipping_phone,
        ':s_addr1' => $shipping_address_line1, ':s_addr2' => $shipping_address_line2, ':s_city' => $shipping_city, ':s_zip' => $shipping_zip_code, ':s_country' => $shipping_country,
        ':ship_to_billing' => $ship_to_billing ? 1 : 0,
        ':subtotal' => $calculated_subtotal, ':shipping' => $calculated_shipping_fee, ':total' => $calculated_order_total,
        ':pmethod' => $payment_method, ':notes' => $customer_notes, ':ip' => $ip_address, ':ua' => $user_agent
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert Order Items
    $stmt_item = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, product_sku, product_name, quantity, unit_price, item_subtotal, item_total)
        VALUES (:order_id, :product_id, :sku, :name, :qty, :price, :subtotal, :total)
    ");
    foreach ($order_items_data as $item) {
        $stmt_item->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':sku' => $item['product_sku'],
            ':name' => $item['product_name'],
            ':qty' => $item['quantity'],
            ':price' => $item['unit_price'],
            ':subtotal' => $item['item_subtotal'],
            ':total' => $item['item_total']
        ]);

        // Update stock (if manage_stock is enabled for the product)
        $product_info_for_stock = $products_map[$item['product_id']];
        if ($product_info_for_stock['manage_stock']) {
            $new_stock = $product_info_for_stock['stock_quantity'] - $item['quantity'];
            $stmt_stock = $pdo->prepare("UPDATE products SET stock_quantity = :new_stock, stock_status = IF(:new_stock > 0, 'in_stock', 'out_of_stock') WHERE id = :product_id");
            $stmt_stock->execute([':new_stock' => $new_stock, ':product_id' => $item['product_id']]);
        }
    }

    $pdo->commit();

    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['last_order_id'] = $order_id; // For confirmation page
    $_SESSION['last_order_uid'] = $order_uid;

    redirect('order_confirmation.php');

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Order placement error: " . $e->getMessage());
    $_SESSION['message'] = "An error occurred while placing your order. Please try again. " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    redirect('checkout.php');
}
?>