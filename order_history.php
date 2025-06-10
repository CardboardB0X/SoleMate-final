<?php
$page_title = "My Order History";
$path_prefix = '';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_url'] = SITE_URL . 'order_history.php';
    header("Location: " . SITE_URL . "login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = [];

try {
    $stmt = $pdo->prepare("SELECT order_id, order_uid, order_total, order_status, created_at 
                           FROM orders 
                           WHERE user_id = :user_id 
                           ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching order history: " . $e->getMessage());
    // Set a message to display to the user, or handle gracefully
    $fetch_error = "Could not retrieve your order history at this time. Please try again later.";
}

require_once __DIR__ . '/templates/header.php';
$active_account_page = basename($_SERVER['PHP_SELF']);
?>

<div class="account-page-container">
    <aside class="account-sidebar">
        <h3>Account Navigation</h3>
        <ul class="account-nav-menu">
            <li><a href="<?php echo SITE_URL; ?>account.php" class="<?php echo ($active_account_page == 'account.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="<?php echo SITE_URL; ?>order_history.php" class="<?php echo ($active_account_page == 'order_history.php') ? 'active' : ''; ?>">Order History</a></li>
            <li><a href="<?php echo SITE_URL; ?>edit_profile.php" class="<?php echo ($active_account_page == 'edit_profile.php') ? 'active' : ''; ?>">Edit Profile & Password</a></li>
            <li><a href="<?php echo SITE_URL; ?>manage_addresses.php" class="<?php echo ($active_account_page == 'manage_addresses.php') ? 'active' : ''; ?>">Manage Addresses</a></li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_URL; ?>logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="account-content">
        <h2>My Order History</h2>

        <?php if (isset($fetch_error)): ?>
            <p class="alert alert-danger"><?php echo e($fetch_error); ?></p>
        <?php elseif (empty($orders)): ?>
            <p>You have not placed any orders yet.</p>
        <?php else: ?>
            <table class="order-history-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date Placed</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <a href="<?php echo SITE_URL . 'view_order.php?uid=' . e($order['order_uid']); ?>">
                                    #<?php echo e(strtoupper(substr($order['order_uid'], 0, 8))); ?>
                                </a>
                            </td>
                            <td><?php echo e(date("M j, Y, g:i a", strtotime($order['created_at']))); ?></td>
                            <td>₱<?php echo number_format((float)$order['order_total'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo e(strtolower(str_replace(' ', '_', $order['order_status']))); ?>">
                                    <?php echo e(ucwords(str_replace('_', ' ', $order['order_status']))); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo SITE_URL . 'view_order.php?uid=' . e($order['order_uid']); ?>" class="btn btn-sm view-details-btn" style="font-size:0.8em; padding: 4px 8px;">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>