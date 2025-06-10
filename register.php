<?php
$page_title = "Register Account";
$path_prefix = '';
require_once __DIR__ . '/config.php';
if (isset($_SESSION['user_id'])) {
    header("Location: " . SITE_URL . "account.php");
    exit;
}

$errors = [];
$success_message = '';
$form_data = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'tos_agree' => false
];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form_data['first_name'] = trim($_POST['first_name'] ?? '');
    $form_data['last_name'] = trim($_POST['last_name'] ?? '');
    $form_data['email'] = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $form_data['phone'] = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $form_data['tos_agree'] = isset($_POST['tos_agree']); // Check if checkbox was ticked

    if (empty($form_data['first_name'])) $errors[] = "First name is required.";
    if (empty($form_data['last_name'])) $errors[] = "Last name is required.";
    if (empty($form_data['email'])) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
        $stmt->execute(['email' => $form_data['email']]);
        if ($stmt->fetch()) {
            $errors[] = "This email address is already registered.";
        }
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Passwords do not match.";
    }
    if (!empty($form_data['phone']) && !preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $form_data['phone'])) {
        $errors[] = "Invalid phone number format.";
    }
    if (!$form_data['tos_agree']) { // Check TOS agreement
        $errors[] = "You must agree to the Terms of Service to register.";
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $is_admin = 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, is_admin, is_verified, tos_agreed_at) 
                                   VALUES (:first_name, :last_name, :email, :phone, :password_hash, :is_admin, 0, NOW())"); // Add tos_agreed_at
            $stmt->execute([
                ':first_name' => $form_data['first_name'],
                ':last_name' => $form_data['last_name'],
                ':email' => $form_data['email'],
                ':phone' => !empty($form_data['phone']) ? $form_data['phone'] : null,
                ':password_hash' => $password_hash,
                ':is_admin' => $is_admin
            ]);
            // $user_id = $pdo->lastInsertId(); // If needed
            $success_message = "Registration successful! You can now log in.";
            // Clear form fields by resetting the array
            $form_data = array_fill_keys(array_keys($form_data), '');
            $form_data['tos_agree'] = false;


        } catch (PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            $errors[] = "An error occurred during registration. Please try again.";
        }
    }
}

require_once __DIR__ . '/templates/header.php';
?>

<div class="form-container">
    <h2>Create Your Account</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo e($success_message); ?> 
            <a href="#" id="loginModalTriggerFromRegister">Login here</a>.
        </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
        <div class="form-group">
            <label for="reg_first_name">First Name:</label>
            <input type="text" id="reg_first_name" name="first_name" value="<?php echo e($form_data['first_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_last_name">Last Name:</label>
            <input type="text" id="reg_last_name" name="last_name" value="<?php echo e($form_data['last_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_email">Email Address:</label>
            <input type="email" id="reg_email" name="email" value="<?php echo e($form_data['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_phone">Phone Number (Optional):</label>
            <input type="tel" id="reg_phone" name="phone" value="<?php echo e($form_data['phone']); ?>">
        </div>
        <div class="form-group">
            <label for="reg_password">Password:</label>
            <input type="password" id="reg_password" name="password" required>
        </div>
        <div class="form-group">
            <label for="reg_password_confirm">Confirm Password:</label>
            <input type="password" id="reg_password_confirm" name="password_confirm" required>
        </div>
        <div class="form-group">
            <input type="checkbox" id="tos_agree" name="tos_agree" value="1" <?php echo $form_data['tos_agree'] ? 'checked' : ''; ?> required>
            <label for="tos_agree" style="display:inline; font-weight:normal;">I agree to the <a href="terms_of_service.php" target="_blank">Terms of Service</a>.</label>
        </div>
        <button type="submit" class="form-button">Register</button>
    </form>
    <p class="form-link-text">Already have an account? <a href="#" id="loginModalTriggerFromRegister2">Login here</a>.</p>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>