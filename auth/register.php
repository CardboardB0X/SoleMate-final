<?php
$page_title = "Register - SoleMate";
$path_prefix = '../'; // To access CSS/JS from auth/ directory
require '../config.php'; // Go up one directory for config

if (isset($_SESSION['user_id'])) {
    redirect($path_prefix . 'index.php'); // Already logged in
}

require $path_prefix . 'templates/header.php';
?>

<div class="form-container">
    <h2>Create Account</h2>
    <form action="auth_handler.php" method="POST">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="form-button">Register</button>
    </form>
    <p style="text-align:center; margin-top:15px;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<?php
require $path_prefix . 'templates/footer.php';
?>