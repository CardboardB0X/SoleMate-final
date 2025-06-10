<?php
$page_title = "View Order Details";
$path_prefix = '';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
    header("Location: " . SITE_URL . "login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_uid = $_GET['uid'] ?? null;
$order = null;
$order_items = [];

if (!$order_uid) {
    header("Location: " . SITE_URL . "order_history.php?error=no_order_id");
    exit;
}

try {
    // Fetch order details, ensuring it belongs to the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_uid = :order_uid AND user_id = :user_id");
    $stmt->execute([':order_uid' => $order_uid, ':user_id' => $user_id]);
    $order = $stmt->fetch();

    if ($order) {
        // Fetch order items
        $items_stmt = $pdo->prepare("SELECT oi.*, p.image_url, p.slug as product_slug 
                                     FROM order_items oi 
                                     LEFT JOIN products p ON oi.product_id = p.id
                                     WHERE oi.order_id = :order_id");
        $items_stmt->execute([':order_id' => $order['order_id']]);
        $order_items = $items_stmt->fetchAll();
    } else {
        // Order not found or doesn't belong to user
        header("Location: " . SITE_URL . "order_history.php?error=order_not_found");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching order details: " . $e->getMessage());
    die("Could not load order details. Please try again later.");
}

require_once __DIR__ . '/templates/header.php';
$active_account_page = 'order_history.php'; // Keep order history active in nav
?>

<div class="account-page-container">
    <aside class="account-sidebar">
        <h3>Account Navigation</h3>
         <ul class="account-nav-menu">
            <li><a href="<?php echo SITE_URL; ?>account.php">Dashboard</a></li>
            <li><a href="<?php echo SITE_URL; ?>order_history.php" class="active">Order History</a></li>
            <li><a href="<?php echo SITE_URL; ?>edit_profile.php">Edit Profile & Password</a></li>
            <li><a href="<?php echo SITE_URL; ?>manage_addresses.php">Manage Addresses</a></li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_URL; ?>logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="account-content view-order-container">
        <h2>Order Details #<?php echo e(strtoupper(substr($order['order_uid'], 0, 8))); ?></h2>
        <p><a href="<?php echo SITE_URL; ?>order_history.php">« Back to Order History</a></p>

        <div class="order-summary-section" style="margin-bottom: 20px; background: #fff; border: 1px solid #ddd;">
            <h3>Order Summary</h3>
            <p><strong>Order Date:</strong> <?php echo e(date("F j, Y, g:i a", strtotime($order['created_at']))); ?></p>
            <p><strong>Order Status:</strong> <span class="status-badge status-<?php echo e(strtolower(str_replace(' ', '_', $order['order_status']))); ?>"><?php echo e(ucwords(str_replace('_', ' ', $order['order_status']))); ?></span></p>
            <p><strong>Payment Method:</strong> <?php echo e(ucwords(str_replace('_', ' ', $order['payment_method']))); ?></p>
            <p><strong>Payment Status:</strong> <span class="status-badge status-<?php echo e(strtolower($order['payment_status'])); ?>"><?php echo e(ucwords($order['payment_status'])); ?></span></p>
            <hr>
            <p><strong>Subtotal:</strong> ₱<?php echo number_format((float)$order['order_subtotal'], 2); ?></p>
            <?php if (isset($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                <p><strong>Discount (<?php echo e($order['discount_code'] ?? 'Applied'); ?>):</strong> -₱<?php echo number_format((float)$order['discount_amount'], 2); ?></p>
            <?php endif; ?>
            <p><strong>Shipping Fee:</strong> ₱<?php echo number_format((float)$order['shipping_fee'], 2); ?></p>
            <p class="order-summary-total"><strong>Order Total:</strong> ₱<?php echo number_format((float)$order['order_total'], 2); ?></p>
        </div>

        <div class="view-order-details-grid">
            <div class="order-info-card">
                <h4>Billing Address</h4>
                <p><?php echo e($order['billing_first_name'] . ' ' . $order['billing_last_name']); ?></p>
                <p><?php echo e($order['billing_address_line1']); ?></p>
                <?php if(!empty($order['billing_address_line2'])): ?><p><?php echo e($order['billing_address_line2']); ?></p><?php endif; ?>
                <p><?php echo e($order['billing_city'] . ', ' . $order['billing_zip_code']); ?></p>
                <p><?php echo e($order['billing_country']); ?></p>
                <p>Email: <?php echo e($order['billing_email']); ?></p>
                <?php if(!empty($order['billing_phone'])): ?><p>Phone: <?php echo e($order['billing_phone']); ?></p><?php endif; ?>
            </div>

            <div class="order-info-card">
                <h4>Shipping Address</h4>
                <?php if($order['ship_to_billing_address']): ?>
                    <p>Same as billing address.</p>
                <?php else: ?>
                    <p><?php echo e($order['shipping_first_name'] . ' ' . $order['shipping_last_name']); ?></p>
                    <p><?php echo e($order['shipping_address_line1']); ?></p>
                    <?php if(!empty($order['shipping_address_line2'])): ?><p><?php echo e($order['shipping_address_line2']); ?></p><?php endif; ?>
                    <p><?php echo e($order['shipping_city'] . ', ' . $order['shipping_zip_code']); ?></p>
                    <p><?php echo e($order['shipping_country']); ?></p>
                    <?php if(!empty($order['shipping_email'])): ?><p>Email: <?php echo e($order['shipping_email']); ?></p><?php endif; ?>
                    <?php if(!empty($order['shipping_phone'])): ?><p>Phone: <?php echo e($order['shipping_phone']); ?></p><?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <h3>Items Ordered</h3>
        <table class="order-items-table">
            <thead>
                <tr>
                    <th colspan="2">Product</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td>
                        <a href="<?php echo SITE_URL . 'product.php?slug=' . e($item['product_slug'] ?? '#'); ?>">
                            <img src="<?php echo e($item['image_url'] ?: $path_prefix . 'assets/images/placeholder.png'); ?>" alt="<?php echo e($item['product_name']); ?>" style="width:50px; height:auto;">
                        </a>
                    </td>
                    <td>
                        <a href="<?php echo SITE_URL . 'product.php?slug=' . e($item['product_slug'] ?? '#'); ?>">
                            <?php echo e($item['product_name']); ?>
                        </a>
                    </td>
                    <td><?php echo e($item['product_sku'] ?? 'N/A'); ?></td>
                    <td><?php echo e($item['quantity']); ?></td>
                    <td>₱<?php echo number_format((float)$item['unit_price'], 2); ?></td>
                    <td>₱<?php echo number_format((float)$item['item_total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if(!empty($order['customer_notes'])): ?>
        <div class="order-info-card" style="margin-top:20px;">
            <h4>Customer Notes</h4>
            <p><?php echo nl2br(e($order['customer_notes'])); ?></p>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>