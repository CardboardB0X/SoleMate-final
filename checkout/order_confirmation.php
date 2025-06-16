<?php
$page_title = "Order Confirmation - SoleMate";
$path_prefix = '../';
require '../config.php';
require $path_prefix . 'templates/header.php';

if (!isset($_SESSION['last_order_id'])) {
    // No recent order, redirect to home or orders page
    redirect($path_prefix . 'index.php');
}

$order_id = $_SESSION['last_order_id'];
$order_uid = $_SESSION['last_order_uid'] ?? 'N/A';

// Unset them so this page can't be refreshed with the same data easily
unset($_SESSION['last_order_id']);
unset($_SESSION['last_order_uid']);
?>

<div class="form-container" style="text-align: center;">
    <h2>Thank You For Your Order!</h2>
    <p>Your order has been placed successfully.</p>
    <p>Your Order ID is: <strong><?php echo htmlspecialchars($order_uid); ?></strong></p>
    <p>We have received your order and will begin processing it shortly. You will receive an email confirmation soon (feature not implemented).</p>
    <p>
        <a href="<?php echo $path_prefix . 'index.php'; ?>" class="form-button" style="display:inline-block; width:auto; margin-right:10px;">Continue Shopping</a>
        <a href="<?php echo $path_prefix . 'account/order_history.php'; ?>" class="form-button" style="display:inline-block; width:auto; background-color:#6c757d;">View Order History</a>
    </p>
</div>

<?php
require $path_prefix . 'templates/footer.php';
?>