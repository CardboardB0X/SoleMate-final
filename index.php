<?php
$page_title = "SoleMate - Find Your Perfect Pair";
$path_prefix = '';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/templates/header.php';

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
    echo "<p class='alert alert-danger container'>Sorry, we couldn't load products at this time. Please try again later.</p>";
}
?>

<h1 class="page-main-title">Our Products</h1>

<?php if (empty($products)): ?>
    <p style="text-align:center; padding: 20px; font-size: 1.1em;">No products found at the moment. Please check back later!</p>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <div class="view-details-img-trigger" data-product-id="<?php echo e($product['id']); ?>">
                    <img src="<?php echo e($product['image_url'] ?: $path_prefix . 'assets/images/placeholder.png'); ?>" alt="<?php echo e($product['name']); ?>">
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
                            $stock_class = 'out-of-stock';
                            $stock_text = 'Out of Stock';
                            $can_add_to_cart = false;
                        } elseif ($product['stock_status'] == 'on_backorder') {
                            $stock_class = 'low-stock';
                            $stock_text = 'On Backorder';
                        } elseif (isset($product['stock_quantity']) && $product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0) {
                            $stock_class = 'low-stock';
                            $stock_text = 'Low Stock (' . e($product['stock_quantity']) . ')';
                        }
                    } else { // Not managing stock
                        if ($product['stock_status'] == 'out_of_stock') { // Still respect out_of_stock if set
                           $stock_class = 'out-of-stock';
                           $stock_text = 'Out of Stock';
                           $can_add_to_cart = false;
                        } else {
                           $stock_text = 'In Stock'; // Assume in stock if not managed and not explicitly out
                        }
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