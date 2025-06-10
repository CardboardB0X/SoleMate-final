<?php
$page_title = "SoleMate - Find Your Perfect Pair";
$path_prefix = '';
require_once __DIR__ . '/config.php'; // Defines SITE_URL, starts session, defines e()
require_once __DIR__ . '/templates/header.php';

// Fetch products from the database
try {
    $stmt = $pdo->query("
        SELECT 
            p.id, p.name, p.slug, p.short_description, p.price, p.sale_price, p.is_on_sale, p.image_url,
            p.stock_quantity, p.stock_status, p.manage_stock,
            b.name AS brand_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        WHERE p.is_active = 1 
          AND (p.stock_status = 'in_stock' OR (p.stock_status = 'on_backorder' AND p.manage_stock = 1))
        ORDER BY p.is_featured DESC, p.created_at DESC
        LIMIT 20 
    ");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    error_log("PDO Error in index.php: " . $e->getMessage());
    // Display error within the main content area for better visibility
    echo "<div class='container' style='padding-top:20px;'><p class='alert alert-danger'>Sorry, we couldn't load products at this time. Please try again later.</p></div>";
}

// Display a "logged out" message if redirected from logout.php
if (isset($_GET['logged_out']) && $_GET['logged_out'] === 'true') {
    // This message will be displayed above the main product listing
    // We'll use JavaScript to show it via the notification system for better UX
    // Or you can have a dedicated div here. For now, rely on JS notification.
    // Note: For this to work with JS, main.js would need to check for this query param.
    // Let's add a simple direct message for non-JS or before JS runs.
    echo "<div class='container' style='padding-top:10px;'><div class='alert alert-success'>You have been successfully logged out.</div></div>";
}
if (isset($_SESSION['checkout_error_message'])) {
    // Display error if redirected from checkout.php before login
    echo "<div class='container' style='padding-top:10px;'><div class='alert alert-warning'>" . e($_SESSION['checkout_error_message']) . " Please <a href='#' id='loginModalTriggerFromMessage'>login</a> to continue.</div></div>";
    unset($_SESSION['checkout_error_message']);
    // Add JS to trigger login modal from this specific link
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const trigger = document.getElementById('loginModalTriggerFromMessage');
            const loginModal = document.getElementById('loginModal');
            const loginMessageDiv = document.getElementById('loginMessage');
            const loginModalForm = document.getElementById('loginModalForm');
            if (trigger && loginModal) {
                trigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    loginModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    if (loginMessageDiv) loginMessageDiv.innerHTML = '';
                    if (loginModalForm) loginModalForm.reset();
                });
            }
        });
    </script>";
}


?>

<h1 class="page-main-title">Our Products</h1>

<?php if (empty($products) && !(isset($_GET['logged_out']) || isset($_SESSION['checkout_error_message'])) ): // Only show if no other messages ?>
    <p style="text-align:center; padding: 20px; font-size: 1.1em;">No products found at the moment. Please check back later!</p>
<?php elseif (!empty($products)): ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="view-details-img-trigger" data-product-id="<?php echo e($product['id']); ?>">
                    <img src="<?php echo e($product['image_url'] ? SITE_URL . $product['image_url'] : $path_prefix . 'assets/images/placeholder.png'); ?>" alt="<?php echo e($product['name']); ?>">
                </div>
                
                <h3 class="view-details-name-trigger" data-product-id="<?php echo e($product['id']); ?>"><?php echo e($product['name']); ?></h3>
                
                <?php if (!empty($product['brand_name'])): ?>
                    <p class="brand"><?php echo e($product['brand_name']); ?></p>
                <?php endif; ?>
                
                <div class="price-section">
                    <?php if ($product['is_on_sale'] && isset($product['sale_price']) && $product['sale_price'] > 0 && $product['sale_price'] < $product['price']): ?>
                        <span class="sale-price">₱<?php echo number_format((float)$product['sale_price'], 2); ?></span>
                        <span class="original-price">₱<?php echo number_format((float)$product['price'], 2); ?></span>
                    <?php else: ?>
                        <span class="price">₱<?php echo number_format((float)$product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>

                <p class="short-desc">
                    <?php 
                        $short_desc = $product['short_description'] ?? '';
                        echo e(strlen($short_desc) > 70 ? substr($short_desc, 0, 70) . '...' : $short_desc);
                    ?>
                </p>

                <?php
                    $stock_text = 'In Stock';
                    $stock_class = '';
                    $can_add_to_cart = true;

                    if ($product['manage_stock']) {
                        if ($product['stock_status'] == 'out_of_stock') {
                            $stock_class = 'out-of-stock'; $stock_text = 'Out of Stock'; $can_add_to_cart = false;
                        } elseif ($product['stock_status'] == 'on_backorder') {
                            $stock_class = 'low-stock'; $stock_text = 'On Backorder';
                        } elseif (isset($product['stock_quantity']) && $product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0) {
                            $stock_class = 'low-stock'; $stock_text = 'Low Stock (' . e($product['stock_quantity']) . ')';
                        }
                    } else {
                        if ($product['stock_status'] == 'out_of_stock') {
                           $stock_class = 'out-of-stock'; $stock_text = 'Out of Stock'; $can_add_to_cart = false;
                        } else { $stock_text = 'In Stock'; }
                    }
                ?>
                <span class="stock-info <?php echo $stock_class; ?>"><?php echo $stock_text; ?></span>
                
                <div style="margin-top: auto;"> 
                    <button class="btn view-details-btn" data-product-id="<?php echo e($product['id']); ?>">
                        View Details
                    </button>
                    <button class="btn add-to-cart-btn" data-product-id="<?php echo e($product['id']); ?>" <?php echo !$can_add_to_cart ? 'disabled' : ''; ?>>
                        <?php echo $can_add_to_cart ? 'Add to Cart' : ($stock_text === 'Out of Stock' ? 'Out of Stock' : 'Unavailable'); ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/templates/footer.php';
?>