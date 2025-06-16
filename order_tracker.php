<?php
$page_title = "Track Your Order";
$path_prefix = '';
require_once __DIR__ . '/config.php';

$order_details = null;
$tracker_error = null;
$search_order_uid = '';
$search_email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_order_uid = trim($_POST['order_uid'] ?? '');
    $search_email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));

    if (empty($search_order_uid) || empty($search_email)) {
        $tracker_error = "Please enter both Order ID and Email Address.";
    } elseif (!filter_var($search_email, FILTER_VALIDATE_EMAIL)) {
        $tracker_error = "Invalid email format provided.";
    } else {
        try {
            // For guest tracking, we check order_uid and billing_email
            // If only for logged-in users, you'd add AND user_id = :user_id
            $stmt = $pdo->prepare("SELECT order_id, order_uid, billing_email, order_status, created_at, shipping_first_name, shipping_last_name, shipping_city, shipping_country 
                                   FROM orders 
                                   WHERE order_uid = :order_uid AND billing_email = :billing_email");
            $stmt->execute([':order_uid' => $search_order_uid, ':billing_email' => $search_email]);
            $order_details = $stmt->fetch();

            if (!$order_details) {
                $tracker_error = "No order found matching that Order ID and Email Address. Please check your details and try again.";
            }
        } catch (PDOException $e) {
            error_log("Order Tracker Error: " . $e->getMessage());
            $tracker_error = "An error occurred while trying to track your order. Please try again later.";
        }
    }
}

require_once __DIR__ . '/templates/header.php';
?>

<h1 class="page-main-title">Track Your Order</h1>

<div class="form-container" style="max-width: 600px;">
    <p>Enter your Order ID and the email address used during checkout to see your order status.</p>
    
    <?php if ($tracker_error): ?>
        <div class="alert alert-danger"><?php echo e($tracker_error); ?></div>
    <?php endif; ?>

    <form action="order_tracker.php" method="POST">
        <div class="form-group">
            <label for="order_uid">Order ID:</label>
            <input type="text" id="order_uid" name="order_uid" value="<?php echo e($search_order_uid); ?>" placeholder="e.g., SOLEMATE_xxxxxxxxxxxxxx" required>
        </div>
        <div class="form-group">
            <label for="email">Billing Email Address:</label>
            <input type="email" id="email" name="email" value="<?php echo e($search_email); ?>" placeholder="youremail@example.com" required>
        </div>
        <button type="submit" class="form-button">Track Order</button>
    </form>
</div>

<?php if ($order_details): ?>
<div class="container" style="margin-top: 30px;">
    <div class="order-summary-section" style="max-width: 700px; margin:auto; background-color:#fff; padding:25px; border-radius:8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3>Order Status for #<?php echo e(strtoupper(substr($order_details['order_uid'], 0, 8))); ?></h3>
        <p><strong>Order Date:</strong> <?php echo e(date("F j, Y, g:i a", strtotime($order_details['created_at']))); ?></p>
        <p><strong>Current Status:</strong> 
            <span class="status-badge status-<?php echo e(strtolower(str_replace(' ', '_', $order_details['order_status']))); ?>">
                <?php echo e(ucwords(str_replace('_', ' ', $order_details['order_status']))); ?>
            </span>
        </p>
        <p><strong>Shipping To:</strong> <?php echo e($order_details['shipping_first_name'] . ' ' . $order_details['shipping_last_name']); ?>, <?php echo e($order_details['shipping_city'] . ', ' . $order_details['shipping_country']); ?></p>
        
        <div class="order-progress-bar" style="margin-top:20px;">
            <?php
            $statuses = ['pending_payment', 'processing', 'shipped', 'delivered', 'completed'];
            // 'cancelled', 'refunded', 'on_hold' are terminal or exceptional, handle display differently if needed
            $current_status_index = array_search(strtolower($order_details['order_status']), $statuses);
            if ($current_status_index === false && in_array(strtolower($order_details['order_status']), ['cancelled', 'refunded', 'on_hold'])) {
                 echo '<p style="font-weight:bold; color: #e74c3c;">Order Status: ' . e(ucwords(str_replace('_', ' ', $order_details['order_status']))) . '</p>';
            } elseif ($current_status_index !== false) {
                foreach ($statuses as $index => $status_name) {
                    $is_completed = $index <= $current_status_index;
                    $is_active = $index === $current_status_index;
                    echo '<div class="progress-step ' . ($is_completed ? 'completed' : '') . ' ' . ($is_active ? 'active' : '') . '">';
                    echo '<div class="step-dot"></div>';
                    echo '<div class="step-label">' . e(ucwords(str_replace('_', ' ', $status_name))) . '</div>';
                    echo '</div>';
                    if ($index < count($statuses) -1 ) { // Don't add line after last step
                         // Line connector - simple visual, can be improved with CSS
                        echo '<div class="progress-line ' . ($is_completed && $index < $current_status_index ? 'completed' : '') . '"></div>';
                    }
                }
            } else {
                 echo '<p style="font-weight:bold;">Current Status: ' . e(ucwords(str_replace('_', ' ', $order_details['order_status']))) . '</p>';
            }
            ?>
        </div>
        <p style="margin-top:20px; font-size:0.9em;">Please note: Status updates may take some time to reflect. For detailed information, please <a href="<?php echo SITE_URL ?>contact.php">contact support</a>.</p>
    </div>
</div>
<?php endif; ?>


<?php
require_once __DIR__ . '/templates/footer.php';
?>