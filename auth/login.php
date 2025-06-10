<?php
$page_title = "Login - SoleMate";
$path_prefix = '../';
require '../config.php';

if (isset($_SESSION['user_id'])) {
    redirect($path_prefix . 'index.php'); // Already logged in
}

require $path_prefix . 'templates/header.php';
?>

<div class="form-container">
    <h2>Login to Your Account</h2>
    <form action="auth_handler.php" method="POST">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="form-button">Login</button>
    </form>
    <p style="text-align:center; margin-top:15px;">
        Don't have an account? <a href="register.php">Register here</a>
    </p>
</div>

<?php
require $path_prefix . 'templates/footer.php';
?>