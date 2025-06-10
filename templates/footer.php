<?php
$footer_path_prefix = $path_prefix ?? '';
?>
            </div> <!-- from header, to close some containers-->
        </div>
    </div>
    <div id="productModal" class="modal">
        <div class="modal-content">
            <button class="close-button" aria-label="Close product details modal">×</button>
            <div id="modalBodyContent">
                <div class="spinner" id="modalSpinner"></div>
            </div>
        </div>
    </div>
    <div id="cartModal" class="modal">
        <div class="cart-modal-content">
            <button class="close-button cart-close-button" aria-label="Close cart modal">×</button>
            <h2>Your Shopping Cart</h2>
            <div id="cartModalBody">
                <p class="empty-cart-message">Your cart is currently empty.</p>
            </div>
            <div id="cartModalFooter" style="display:none;">
                <div class="cart-total">
                    Total: <span id="cartTotalAmount">₱0.00</span>
                </div>
                <div class="cart-actions">
                    <a href="<?php echo e($footer_path_prefix); ?>shop.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="<?php echo e($footer_path_prefix); ?>checkout.php" class="btn btn-checkout" id="checkoutButton">Proceed to Checkout</a>
                </div>
            </div>
        </div>
        
    </div>

    <div id="loginModal" class="modal auth-modal">
        <div class="modal-content">
            <button class="close-button login-close-button" aria-label="Close login modal">×</button>
            <div class="form-container" style="box-shadow: none; padding:0; margin:0;">
                <h2>Login to Your Account</h2>
                <div id="loginMessage"></div> <!-- For success/error messages -->
                <form id="loginModalForm" action="process_login.php" method="POST">
                    <div class="form-group">
                        <label for="login_email">Email Address:</label>
                        <input type="email" id="login_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="login_password">Password:</label>
                        <input type="password" id="login_password" name="password" required>
                    </div>
                    <button type="submit" class="form-button">Login</button>
                </form>
                <p class="form-link-text">Don't have an account? <a href="register.php">Register here</a>.</p>
            </div>
        </div>
    </div>


    <footer class="main-footer">
        <div class="container">
            <div class="footer-nav">
                <a href="<?php echo e($footer_path_prefix); ?>index.php">Home</a>
                <a href="<?php echo e($footer_path_prefix); ?>shop.php">Shop</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo e($footer_path_prefix); ?>account.php">My Account</a>
                    <a href="<?php echo e($footer_path_prefix); ?>logout.php">Logout</a>
                <?php else: ?>
                    <a href="<?php echo e($footer_path_prefix); ?>login.php">Login</a>
                    <a href="<?php echo e($footer_path_prefix); ?>register.php">Register</a>
                <?php endif; ?>
            </div>
            <p>© <?php echo date("Y"); ?> <?php echo e(SITE_NAME); ?>. All Rights Reserved.</p>
            <p>Built by The ClickSenvee Team</p>
        </div>
    </footer>
    <!-- <footer class="easter-egg-footer">
        <a href="<?php echo e($footer_path_prefix); ?>secret_video.php" target="_blank" title="What could this be?">
            <img src="<?php echo e($footer_path_prefix); ?>emman.jpg" alt="A mysterious figure" class="footer-image">
            <p class="footer-caption">hot daddy in your area</p>
        </a> --> 
    </footer>
<script src="<?php echo e($footer_path_prefix); ?>assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>