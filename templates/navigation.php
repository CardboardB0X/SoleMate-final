<?php
$nav_path_prefix = $path_prefix ?? '';
$nav_current_page_basename = $current_page_basename ?? basename($_SERVER['PHP_SELF']);

$nav_links = [
    'index.php' => 'Home',
    'shop.php' => 'All Products',
];
?>
<nav class="main-navigation">
    <div class="container">
        <div class="logo">
            <a href="<?php echo e($nav_path_prefix); ?>index.php"><?php echo e(SITE_NAME); ?></a>
        </div>
        <button class="hamburger-menu" aria-label="Toggle menu" aria-expanded="false">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <div class="nav-menu">
            <ul class="nav-links">
                <?php foreach ($nav_links as $url => $title): ?>
                    <li>
                        <a href="<?php echo e($nav_path_prefix . $url); ?>" 
                           class="<?php echo ($nav_current_page_basename == $url) ? 'active' : ''; ?>">
                            <?php echo e($title); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="nav-right">
             <div class="nav-right">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_first_name'])): ?>
                    <span>Welcome, <?php echo e($_SESSION['user_first_name']); ?>!</span>
                    <a href="<?php echo e($nav_path_prefix); ?>account.php" class="<?php echo ($nav_current_page_basename == 'account.php') ? 'active' : ''; ?>">My Account</a>
                    <a href="<?php echo e($nav_path_prefix); ?>logout.php">Logout</a>
                <?php else: ?>
                    <a href="#" id="loginModalTrigger" class="<?php echo ($nav_current_page_basename == 'login.php') ? 'active' : ''; ?>">Login</a>
                    <a href="<?php echo e($nav_path_prefix); ?>register.php" class="<?php echo ($nav_current_page_basename == 'register.php') ? 'active' : ''; ?>">Register</a>
                <?php endif; ?>
                <a href="#" id="cart-indicator" aria-label="View shopping cart">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-cart3" viewBox="0 0 16 16" style="vertical-align: middle;">
                        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.701 13H13.5a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <span id="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>
</nav>