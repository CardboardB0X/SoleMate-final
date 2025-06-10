<?php
$page_title = "View Order - SoleMate";
$path_prefix = '../';
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to view your order details.";
    $_SESSION['message_type'] = "warning";
    redirect($path_prefix . 'auth/login.php');
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $_SESSION['message'] = "Invalid order ID.";
    $_SESSION['message_type'] = "danger";
    redirect('order_history.php');
}
$order_id = (int)$_GET['id'];

require $path_prefix . 'templates/header.php';

try {
    // Fetch Order Details
    $stmt_order = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id");
    $stmt_order->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt_order->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt_order->execute();
    $order = $stmt_order->fetch();

    if (!$order) {
        $_SESSION['message'] = "Order not found or you do not have permission to view it.";
        $_SESSION['message_type'] = "danger";
        redirect('order_history.php');
    }

    // Fetch Order Items
    $stmt_items = $pdo->prepare("
        SELECT oi.*, p.image_url 
        FROM order_items oi 
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :order_id
    ");
    $stmt_items->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt_items->execute();
    $order_items = $stmt_items->fetchAll();

} catch (PDOException $e) {
    echo "<p class='alert alert-danger'>Error fetching order details: " . $e->getMessage() . "</p>";
    $order = null; // Prevent further processing
}

?>

<?php if ($order): ?>
    <h2>Order Details (ID: <?php echo htmlspecialchars($order['order_uid']); ?>)</h2>
    <div style="display: flex; gap: 30px; margin-bottom:20px;">
        <div style="flex:1;">
            <h4>Order Information</h4>
            <p><strong>Date:</strong> <?php echo date("F j, Y, g:i a", strtotime($order['created_at'])); ?></p>
            <p><strong>Status:</strong> <span class="status-<?php echo strtolower(str_replace(' ', '_', $order['order_status'])); ?>"><?php echo ucwords(str_replace('_', ' ', $order['order_status'])); ?></span></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(strtoupper($order['payment_method'])); ?></p>
            <p><strong>Payment Status:</strong> <?php echo ucwords($order['payment_status']); ?></p>
        </div>
        <div style="flex:1;">
            <h4>Billing Address</h4>
            <p><?php echo htmlspecialchars($order['billing_first_name'] . ' ' . $order['billing_last_name']); ?></p>
            <p><?php echo htmlspecialchars($order['billing_address_line1']); ?></p>
            <?php if ($order['billing_address_line2']): ?><p><?php echo htmlspecialchars($order['billing_address_line2']); ?></p><?php endif; ?>
            <p><?php echo htmlspecialchars($order['billing_city'] . ', ' . $order['billing_zip_code']); ?></p>
            <p><?php echo htmlspecialchars($order['billing_country']); ?></p>
            <p>Email: <?php echo htmlspecialchars($order['billing_email']); ?></p>
            <?php if ($order['billing_phone']): ?><p>Phone: <?php echo htmlspecialchars($order['billing_phone']); ?></p><?php endif; ?>
        </div>
        <div style="flex:1;">
            <h4>Shipping Address</h4>
            <?php if ($order['ship_to_billing_address']): ?>
                <p>Same as billing address.</p>
            <?php else: ?>
                <p><?php echo htmlspecialchars($order['shipping_first_name'] . ' ' . $order['shipping_last_name']); ?></p>
                <p><?php echo htmlspecialchars($order['shipping_address_line1']); ?></p>
                <?php if ($order['shipping_address_line2']): ?><p><?php echo htmlspecialchars($order['shipping_address_line2']); ?></p><?php endif; ?>
                <p><?php echo htmlspecialchars($order['shipping_city'] . ', ' . $order['shipping_zip_code']); ?></p>
                <p><?php echo htmlspecialchars($order['shipping_country']); ?></p>
                <?php if ($order['shipping_email']): ?><p>Email: <?php echo htmlspecialchars($order['shipping_email']); ?></p><?php endif; ?>
                <?php if ($order['shipping_phone']): ?><p>Phone: <?php echo htmlspecialchars($order['shipping_phone']); ?></p><?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <h4>Items Ordered</h4>
    <table class="order-history-table" style="margin-bottom: 20px;">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
            <tr>
                <td>
                    <img src="<?php echo htmlspecialchars($item['image_url'] ?: $path_prefix . 'assets/placeholder.png'); ?>" alt="" style="width:50px; height:50px; object-fit:cover; margin-right:10px; vertical-align:middle;">
                    <?php echo htmlspecialchars($item['product_name']); ?>
                </td>
                <td><?php echo htmlspecialchars($item['product_sku'] ?: 'N/A'); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₱<?php echo number_format((float)$item['unit_price'], 2); ?></td>
                <td>₱<?php echo number_format((float)$item['item_total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align: right; width: 300px; margin-left: auto;">
        <p><strong>Subtotal:</strong> ₱<?php echo number_format((float)$order['order_subtotal'], 2); ?></p>
        <p><strong>Shipping Fee:</strong> ₱<?php echo number_format((float)$order['shipping_fee'], 2); ?></p>
        <p><strong>Discount:</strong> ₱<?php echo number_format((float)$order['discount_amount'], 2); ?></p>
        <hr>
        <p><strong>Total:</strong> ₱<?php echo number_format((float)$order['order_total'], 2); ?></p>
    </div>

    <?php if($order['customer_notes']): ?>
        <h4>Your Notes:</h4>
        <p><?php echo nl2br(htmlspecialchars($order['customer_notes'])); ?></p>
    <?php endif; ?>

    <p style="margin-top: 20px;"><a href="order_history.php" class="btn btn-secondary">Back to Order History</a></p>

<?php else: ?>
    <?php if(!isset($_SESSION['message'])): // Only show if no other message is set ?>
    <p class="alert alert-warning">Could not load order details.</p>
    <?php endif; ?>
<?php endif; ?>


<?php
require $path_prefix . 'templates/footer.php';
?>