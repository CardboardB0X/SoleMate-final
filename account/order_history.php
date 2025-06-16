<?php
$page_title = "My Order History - SoleMate";
$path_prefix = '../';
require '../config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Please login to view your order history.";
    $_SESSION['message_type'] = "warning";
    redirect($path_prefix . 'auth/login.php');
}

require $path_prefix . 'templates/header.php';

try {
    $stmt = $pdo->prepare("
        SELECT order_id, order_uid, order_total, order_status, created_at 
        FROM orders 
        WHERE user_id = :user_id 
        ORDER BY created_at DESC
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
    echo "<p class='alert alert-danger'>Error fetching orders: " . $e->getMessage() . "</p>";
}

?>
<h2>My Order History</h2>

<?php if (empty($orders)): ?>
    <p>You have not placed any orders yet.</p>
<?php else: ?>
    <table class="order-history-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['order_uid']); ?></td>
                    <td><?php echo date("M d, Y H:i", strtotime($order['created_at'])); ?></td>
                    <td>₱<?php echo number_format((float)$order['order_total'], 2); ?></td>
                    <td>
                        <span class="status-<?php echo strtolower(str_replace(' ', '_', $order['order_status'])); ?>">
                            <?php echo ucwords(str_replace('_', ' ', $order['order_status'])); ?>
                        </span>
                    </td>
                    <td>
                        <a href="view_order.php?id=<?php echo $order['order_id']; ?>" class="view-details-btn" style="font-size:0.9em; padding: 5px 10px;">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php
require $path_prefix . 'templates/footer.php';
?>