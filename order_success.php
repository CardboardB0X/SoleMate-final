<?php
$page_title = "Order Successful!";
$path_prefix = '';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['order_confirmation_uid'])) {
    // If no order UID in session, maybe they refreshed or came directly
    // Redirect to homepage or order history
    header("Location: " . SITE_URL . "index.php");
    exit;
}

$order_uid_display = $_SESSION['order_confirmation_uid'];
// unset($_SESSION['order_confirmation_uid']); // Clear it after displaying once, or keep for refresh

require_once __DIR__ . '/templates/header.php';
?>

<div class="container text-center" style="padding-top: 40px; padding-bottom: 40px;">
    <div style="max-width: 600px; margin:auto; background-color:#fff; padding:30px; border-radius:8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="#2ecc71" class="bi bi-check-circle-fill" viewBox="0 0 16 16" style="margin-bottom:20px;">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </svg>
        <h1 style="color: #2ecc71; font-size:2.5em; margin-bottom:15px;">Thank You For Your Order!</h1>
        <p style="font-size:1.2em;">Your order has been placed successfully.</p>
        <p>Your Order ID is: <strong>#<?php echo e(strtoupper(substr($order_uid_display, 0, 8))); ?></strong> (Full ID: <?php echo e($order_uid_display); ?>)</p>
        <p>We've sent a confirmation email to your address (feature to be implemented). You can track your order status in your account's order history.</p>
        
        <div style="margin-top:30px;">
            <a href="<?php echo SITE_URL; ?>shop.php" class="btn btn-primary" style="background-color:#3498db; margin-right:10px;">Continue Shopping</a>
            <a href="<?php echo SITE_URL; ?>order_history.php" class="btn btn-secondary">View Order History</a>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>