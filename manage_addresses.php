<?php
$page_title = "Manage Addresses";
$path_prefix = '';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_url'] = SITE_URL . 'manage_addresses.php';
    header("Location: " . SITE_URL . "login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$addresses = [];
$errors = [];
$success_message = '';

// --- Fetch existing addresses ---
try {
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default_shipping DESC, is_default_billing DESC, created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $addresses = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching user addresses: " . $e->getMessage());
    $errors[] = "Could not load your saved addresses.";
}

// --- Handle Form Submissions (Add/Edit/Delete/Set Default) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? null;

    // --- ADD NEW ADDRESS ---
    if ($action === 'add_address') {
        $add_first_name = trim($_POST['add_first_name'] ?? '');
        $add_last_name = trim($_POST['add_last_name'] ?? '');
        $add_phone = trim($_POST['add_phone'] ?? '');
        $add_address_line1 = trim($_POST['add_address_line1'] ?? '');
        $add_address_line2 = trim($_POST['add_address_line2'] ?? '');
        $add_city = trim($_POST['add_city'] ?? '');
        $add_zip_code = trim($_POST['add_zip_code'] ?? '');
        $add_country = trim($_POST['add_country'] ?? 'Philippines'); // Default
        $add_is_default_shipping = isset($_POST['add_is_default_shipping']) ? 1 : 0;
        $add_is_default_billing = isset($_POST['add_is_default_billing']) ? 1 : 0;

        if (empty($add_first_name)) $errors[] = "First name is required for new address.";
        if (empty($add_last_name)) $errors[] = "Last name is required for new address.";
        if (empty($add_address_line1)) $errors[] = "Address Line 1 is required.";
        if (empty($add_city)) $errors[] = "City is required.";
        if (empty($add_zip_code)) $errors[] = "Zip code is required.";
        if (empty($add_country)) $errors[] = "Country is required.";

        if (empty($errors)) {
            try {
                $pdo->beginTransaction();
                // If setting as new default, unset other defaults first
                if ($add_is_default_shipping) {
                    $pdo->exec("UPDATE user_addresses SET is_default_shipping = 0 WHERE user_id = $user_id");
                }
                if ($add_is_default_billing) {
                     $pdo->exec("UPDATE user_addresses SET is_default_billing = 0 WHERE user_id = $user_id");
                }

                $stmt = $pdo->prepare("INSERT INTO user_addresses (user_id, first_name, last_name, phone, address_line1, address_line2, city, zip_code, country, is_default_shipping, is_default_billing, address_type) 
                                       VALUES (:user_id, :first_name, :last_name, :phone, :address_line1, :address_line2, :city, :zip_code, :country, :is_default_shipping, :is_default_billing, 'shipping')"); // Default type or based on form
                $stmt->execute([
                    ':user_id' => $user_id,
                    ':first_name' => $add_first_name,
                    ':last_name' => $add_last_name,
                    ':phone' => !empty($add_phone) ? $add_phone : null,
                    ':address_line1' => $add_address_line1,
                    ':address_line2' => !empty($add_address_line2) ? $add_address_line2 : null,
                    ':city' => $add_city,
                    ':zip_code' => $add_zip_code,
                    ':country' => $add_country,
                    ':is_default_shipping' => $add_is_default_shipping,
                    ':is_default_billing' => $add_is_default_billing
                ]);
                $pdo->commit();
                $_SESSION['address_action_success'] = "New address added successfully!";
                header("Location: " . SITE_URL . "manage_addresses.php"); // Refresh
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("Error adding address: " . $e->getMessage());
                $errors[] = "Could not add address. Please try again.";
            }
        }
    }
    // --- DELETE ADDRESS ---
    elseif ($action === 'delete_address') {
        $address_id_to_delete = filter_input(INPUT_POST, 'address_id', FILTER_VALIDATE_INT);
        if ($address_id_to_delete) {
            try {
                // Ensure the address belongs to the user before deleting
                $stmt = $pdo->prepare("DELETE FROM user_addresses WHERE address_id = :address_id AND user_id = :user_id");
                $stmt->execute([':address_id' => $address_id_to_delete, ':user_id' => $user_id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['address_action_success'] = "Address deleted successfully.";
                } else {
                    $errors[] = "Could not delete address or address not found.";
                }
                header("Location: " . SITE_URL . "manage_addresses.php"); // Refresh
                exit;
            } catch (PDOException $e) {
                error_log("Error deleting address: " . $e->getMessage());
                $errors[] = "Could not delete address.";
            }
        } else {
            $errors[] = "Invalid address ID for deletion.";
        }
    }
    // --- SET DEFAULT ADDRESS ---
    elseif ($action === 'set_default') {
        $address_id_to_set = filter_input(INPUT_POST, 'address_id', FILTER_VALIDATE_INT);
        $default_type = $_POST['default_type'] ?? null; // 'shipping' or 'billing'

        if ($address_id_to_set && ($default_type === 'shipping' || $default_type === 'billing')) {
            try {
                $pdo->beginTransaction();
                $column_to_set = ($default_type === 'shipping') ? 'is_default_shipping' : 'is_default_billing';
                
                // Unset current default for this type
                $stmt_unset = $pdo->prepare("UPDATE user_addresses SET $column_to_set = 0 WHERE user_id = :user_id");
                $stmt_unset->execute([':user_id' => $user_id]);

                // Set new default
                $stmt_set = $pdo->prepare("UPDATE user_addresses SET $column_to_set = 1 WHERE address_id = :address_id AND user_id = :user_id");
                $stmt_set->execute([':address_id' => $address_id_to_set, ':user_id' => $user_id]);
                
                $pdo->commit();
                $_SESSION['address_action_success'] = "Default " . $default_type . " address updated.";
                header("Location: " . SITE_URL . "manage_addresses.php"); // Refresh
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("Error setting default address: " . $e->getMessage());
                $errors[] = "Could not update default address.";
            }
        } else {
            $errors[] = "Invalid request for setting default address.";
        }
    }
    // Store errors in session to display after redirect if form submission fails at validation stage
    if(!empty($errors) && $action === 'add_address'){ // Only for add form, others redirect immediately
        $_SESSION['address_action_error'] = implode("<br>", array_map('e', $errors));
        // Retain form data if add fails
        $_SESSION['add_address_form_data'] = $_POST;
        header("Location: " . SITE_URL . "manage_addresses.php#addAddressForm");
        exit;
    }
}

// Retrieve and clear session messages
if(isset($_SESSION['address_action_success'])){
    $success_message = $_SESSION['address_action_success'];
    unset($_SESSION['address_action_success']);
}
if(isset($_SESSION['address_action_error'])){
    $errors = array_merge($errors, explode("<br>", $_SESSION['address_action_error'])); // Merge with current errors
    unset($_SESSION['address_action_error']);
}
$add_address_form_data = $_SESSION['add_address_form_data'] ?? [];
unset($_SESSION['add_address_form_data']);


require_once __DIR__ . '/templates/header.php';
$active_account_page = basename($_SERVER['PHP_SELF']);
?>

<div class="account-page-container">
    <aside class="account-sidebar">
        <h3>Account Navigation</h3>
        <ul class="account-nav-menu">
            <li><a href="<?php echo SITE_URL; ?>account.php">Dashboard</a></li>
            <li><a href="<?php echo SITE_URL; ?>order_history.php">Order History</a></li>
            <li><a href="<?php echo SITE_URL; ?>edit_profile.php">Edit Profile & Password</a></li>
            <li><a href="<?php echo SITE_URL; ?>manage_addresses.php" class="active">Manage Addresses</a></li>
            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php">Admin Panel</a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_URL; ?>logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="account-content">
        <h2>Manage Your Addresses</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo e($success_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors) && !isset($_POST['action'])): // Display general fetch errors ?>
            <div class="alert alert-danger">
                <ul><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>


        <div class="address-list">
            <h3>Saved Addresses</h3>
            <?php if (empty($addresses)): ?>
                <p>You have no saved addresses. Add one below!</p>
            <?php else: ?>
                <?php foreach ($addresses as $address): ?>
                    <div class="address-card <?php echo ($address['is_default_shipping'] || $address['is_default_billing']) ? 'default-address-card' : ''; ?>">
                        <h4>
                            <?php echo e($address['first_name'] . ' ' . $address['last_name']); ?>
                            <?php if ($address['is_default_shipping']): ?> <span class="default-badge">Default Shipping</span><?php endif; ?>
                            <?php if ($address['is_default_billing']): ?> <span class="default-badge">Default Billing</span><?php endif; ?>
                        </h4>
                        <p>
                            <?php echo e($address['address_line1']); ?><br>
                            <?php if(!empty($address['address_line2'])): ?><?php echo e($address['address_line2']); ?><br><?php endif; ?>
                            <?php echo e($address['city'] . ', ' . $address['zip_code']); ?><br>
                            <?php echo e($address['country']); ?>
                        </p>
                        <?php if(!empty($address['phone'])): ?><p>Phone: <?php echo e($address['phone']); ?></p><?php endif; ?>
                        
                        <div class="address-actions">
                            <!-- Edit button (implement edit functionality later) -->
                            <!-- <a href="edit_address.php?id=<?php echo $address['address_id']; ?>" class="btn btn-sm">Edit</a> -->
                            
                            <form action="manage_addresses.php" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete_address">
                                <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this address?');">Delete</button>
                            </form>

                            <?php if (!$address['is_default_shipping']): ?>
                            <form action="manage_addresses.php" method="POST" style="display:inline; margin-left:5px;">
                                <input type="hidden" name="action" value="set_default">
                                <input type="hidden" name="default_type" value="shipping">
                                <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-secondary">Set as Default Shipping</button>
                            </form>
                            <?php endif; ?>
                            <?php if (!$address['is_default_billing']): ?>
                             <form action="manage_addresses.php" method="POST" style="display:inline; margin-left:5px;">
                                <input type="hidden" name="action" value="set_default">
                                <input type="hidden" name="default_type" value="billing">
                                <input type="hidden" name="address_id" value="<?php echo $address['address_id']; ?>">
                                <button type="submit" class="btn btn-sm btn-secondary">Set as Default Billing</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <hr style="margin: 30px 0;">

        <div class="edit-profile-section" id="addAddressForm">
            <h3>Add New Address</h3>
            <?php if (!empty($errors) && isset($_POST['action']) && $_POST['action'] === 'add_address'): // Display add form errors here ?>
                <div class="alert alert-danger">
                    <strong>Please correct the following errors:</strong><br>
                    <ul><?php foreach ($errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form action="manage_addresses.php" method="POST">
                <input type="hidden" name="action" value="add_address">
                <div style="display:flex; gap: 20px;">
                    <div class="form-group" style="flex:1;">
                        <label for="add_first_name">First Name:</label>
                        <input type="text" id="add_first_name" name="add_first_name" value="<?php echo e($add_address_form_data['add_first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label for="add_last_name">Last Name:</label>
                        <input type="text" id="add_last_name" name="add_last_name" value="<?php echo e($add_address_form_data['add_last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="add_phone">Phone Number (Optional):</label>
                    <input type="tel" id="add_phone" name="add_phone" value="<?php echo e($add_address_form_data['add_phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="add_address_line1">Address Line 1:</label>
                    <input type="text" id="add_address_line1" name="add_address_line1" value="<?php echo e($add_address_form_data['add_address_line1'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="add_address_line2">Address Line 2 (Optional):</label>
                    <input type="text" id="add_address_line2" name="add_address_line2" value="<?php echo e($add_address_form_data['add_address_line2'] ?? ''); ?>">
                </div>
                 <div style="display:flex; gap: 20px;">
                    <div class="form-group" style="flex:2;">
                        <label for="add_city">City:</label>
                        <input type="text" id="add_city" name="add_city" value="<?php echo e($add_address_form_data['add_city'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label for="add_zip_code">Zip Code:</label>
                        <input type="text" id="add_zip_code" name="add_zip_code" value="<?php echo e($add_address_form_data['add_zip_code'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="add_country">Country:</label>
                    <input type="text" id="add_country" name="add_country" value="<?php echo e($add_address_form_data['add_country'] ?? 'Philippines'); ?>" required>
                </div>
                <div class="form-group">
                    <input type="checkbox" id="add_is_default_shipping" name="add_is_default_shipping" value="1" <?php echo (isset($add_address_form_data['add_is_default_shipping'])) ? 'checked' : ''; ?>>
                    <label for="add_is_default_shipping" style="display:inline; font-weight:normal;">Set as default shipping address</label>
                </div>
                 <div class="form-group">
                    <input type="checkbox" id="add_is_default_billing" name="add_is_default_billing" value="1" <?php echo (isset($add_address_form_data['add_is_default_billing'])) ? 'checked' : ''; ?>>
                    <label for="add_is_default_billing" style="display:inline; font-weight:normal;">Set as default billing address</label>
                </div>
                <button type="submit" class="form-button">Add Address</button>
            </form>
        </div>
    </main>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>