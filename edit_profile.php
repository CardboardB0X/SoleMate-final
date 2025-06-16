<?php
$page_title = "Edit Profile & Password";
$path_prefix = '';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_url'] = SITE_URL . 'edit_profile.php';
    header("Location: " . SITE_URL . "login.php"); // Redirect to login if not logged in
    exit;
}

// Fetch current user data
$user_id = $_SESSION['user_id'];
try {
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone FROM users WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $current_user = $stmt->fetch();
    if (!$current_user) {
        // Should not happen if session is valid, but good to check
        session_destroy();
        header("Location: " . SITE_URL . "login.php?error=user_not_found");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching user data for edit: " . $e->getMessage());
    // Display an error or redirect
    die("Error loading your profile. Please try again later.");
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
        <h2>Edit Your Profile</h2>

        <div id="profileUpdateMessage">
            <?php if(isset($_SESSION['profile_update_success'])): ?>
                <div class="alert alert-success"><?php echo e($_SESSION['profile_update_success']); ?></div>
                <?php unset($_SESSION['profile_update_success']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['profile_update_error'])): ?>
                <div class="alert alert-danger"><?php echo e($_SESSION['profile_update_error']); ?></div>
                <?php unset($_SESSION['profile_update_error']); ?>
            <?php endif; ?>
        </div>

        <div class="edit-profile-section">
            <h3>Personal Information</h3>
            <form id="editProfileForm" action="process_edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo e($current_user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo e($current_user['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" id="email" name="email" value="<?php echo e($current_user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number (Optional):</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo e($current_user['phone']); ?>">
                </div>
                <button type="submit" class="form-button">Save Changes</button>
            </form>
        </div>

        <div id="passwordUpdateMessage">
             <?php if(isset($_SESSION['password_update_success'])): ?>
                <div class="alert alert-success"><?php echo e($_SESSION['password_update_success']); ?></div>
                <?php unset($_SESSION['password_update_success']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['password_update_error'])): ?>
                <div class="alert alert-danger"><?php echo e($_SESSION['password_update_error']); ?></div>
                <?php unset($_SESSION['password_update_error']); ?>
            <?php endif; ?>
        </div>

        <div class="edit-profile-section">
            <h3>Change Password</h3>
            <form id="changePasswordForm" action="process_change_password.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                </div>
                <button type="submit" class="form-button">Change Password</button>
            </form>
        </div>
    </main>
</div>
<?php
require_once __DIR__ . '/templates/footer.php';
?>