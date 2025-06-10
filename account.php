<?php
$page_title = "My Account Dashboard";
$path_prefix = '';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_url'] = SITE_URL . 'account.php';
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
            <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php" class="<?php echo (strpos($active_account_page, 'admin/') === 0) ? 'active' : ''; ?>">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_URL; ?>logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="account-content">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class='container' style="padding-top: 20px;">
                <h2 class="page-main-title">Access Denied</h2>
                <p class='alert alert-warning'>You need to be logged in to view your account dashboard. Please 
                    <a href='#' id='loginModalTriggerFromProtectedPage' style="font-weight:bold; text-decoration:underline;">login</a> or 
                    <a href='<?php echo SITE_URL; ?>register.php' style="font-weight:bold; text-decoration:underline;">register</a>.
                </p>
            </div>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const trigger = document.getElementById('loginModalTriggerFromProtectedPage');
                const loginModal = document.getElementById('loginModal');
                const loginMessageDiv = document.getElementById('loginMessage');
                const loginModalForm = document.getElementById('loginModalForm');
                function openLoginModalFromPage() {
                    if (loginModal) {
                        loginModal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                        if (loginMessageDiv) loginMessageDiv.innerHTML = '';
                        if (loginModalForm) loginModalForm.reset();
                    }
                }
                if (trigger) {
                    trigger.addEventListener('click', (e) => {
                        e.preventDefault();
                        openLoginModalFromPage();
                    });
                }
            });
            </script>
        <?php else: ?>
            <div class="account-dashboard">
                <h2>Account Dashboard</h2>
                <p>Hello, <strong><?php echo e($_SESSION['user_first_name'] ?? 'Valued Customer'); ?></strong>!</p>
                <p>Welcome to your account dashboard. From here, you can quickly access key areas to manage your information, view your order history, and update your preferences.</p>
                
                <div class="dashboard-quick-links">
                    <div class="quick-link-card">
                        <h4><a href="<?php echo SITE_URL; ?>order_history.php">Your Orders</a></h4>
                        <p>Track your recent orders, view details, and manage returns if applicable.</p>
                    </div>
                    <div class="quick-link-card">
                        <h4><a href="<?php echo SITE_URL; ?>edit_profile.php">Profile & Security</a></h4>
                        <p>Update your name, email, phone number, and change your password to keep your account secure.</p>
                    </div>
                    <div class="quick-link-card">
                        <h4><a href="<?php echo SITE_URL; ?>manage_addresses.php">Addresses</a></h4>
                        <p>Manage your saved shipping and billing addresses for a faster and smoother checkout experience.</p>
                    </div>
                </div>

                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <div class="admin-notice quick-link-card" style="background-color: #e9f5ff; border-left: 4px solid #3498db; margin-top: 25px;">
                        <h4>Admin Access</h4>
                        <p>You have administrative privileges. <a href="<?php echo SITE_URL; ?>admin/dashboard.php" style="font-weight:bold;">Access the Admin Panel</a> to manage the site.</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>