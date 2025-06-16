    <?php
$page_title = "Shop by Brand - SoleMate";
$path_prefix = '';
require 'config.php';
require 'templates/header.php';

// Fetch all active brands
try {
    $stmt_brand_list = $pdo->query("SELECT brand_id, name, slug FROM brands WHERE is_active = 1 ORDER BY name"); // Removed logo_url as not used in filter
    $brands = $stmt_brand_list->fetchAll();
} catch (PDOException $e) {
    $brands = [];
    echo "<p class='alert alert-danger'>Error fetching brands: " . $e->getMessage() . "</p>";
}

$selected_brand_id = null;
if (isset($_GET['brand_id']) && filter_var($_GET['brand_id'], FILTER_VALIDATE_INT) && $_GET['brand_id'] > 0) {
    $selected_brand_id = (int)$_GET['brand_id'];
}

$products = [];
$current_brand_name = "All Products";

$sql_products = "
    SELECT 
        p.id, p.name, p.slug, p.short_description, p.price, p.sale_price, p.is_on_sale, p.image_url,
        p.stock_quantity, p.stock_status,
        b.name AS brand_name /* This is important to show brand name in the card if not filtering by it */
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.is_active = 1 
      AND (p.stock_status = 'in_stock' OR p.stock_status = 'on_backorder')
";
$params = [];

if ($selected_brand_id) {
    $sql_products .= " AND p.brand_id = :brand_id_filter ";
    $params[':brand_id_filter'] = $selected_brand_id;
    
    // Get selected brand name
    try {
        $stmt_curr_brand = $pdo->prepare("SELECT name FROM brands WHERE brand_id = :b_id_name");
        $stmt_curr_brand->execute([':b_id_name' => $selected_brand_id]);
        $brand_name_fetch = $stmt_curr_brand->fetchColumn();
        if ($brand_name_fetch) $current_brand_name = $brand_name_fetch;
        else $current_brand_name = "Unknown Brand";
    } catch (PDOException $e) {
        error_log("Error fetching brand name: " . $e->getMessage());
        $current_brand_name = "Error loading brand";
    }
}
$sql_products .= " ORDER BY p.is_featured DESC, p.name ASC";

try {
    $stmt_prod = $pdo->prepare($sql_products);
    $stmt_prod->execute($params);
    $products = $stmt_prod->fetchAll();
} catch (PDOException $e) {
    echo "<p class='alert alert-danger'>Error fetching products: " . $e->getMessage() . "</p>";
    error_log("Error fetching products (brand page): SQL: " . $sql_products . " Params: " . print_r($params, true) . " Error: " . $e->getMessage());
    $products = [];
}
?>

<div class="shop-header">
    <h1>Shop by Brand: <?php echo htmlspecialchars($current_brand_name); ?></h1>
</div>

<div class="filter-section">
    <h3>Brands</h3>
    <div class="filter-options">
        <a href="shop_by_brand.php" class="<?php if(!$selected_brand_id && basename($_SERVER['PHP_SELF']) == 'shop_by_brand.php') echo 'active';?>">All Products</a>
        <?php foreach ($brands as $brand): ?>
            <a href="shop_by_brand.php?brand_id=<?php echo $brand['brand_id']; ?>"
               class="<?php if($selected_brand_id == $brand['brand_id']) echo 'active';?>">
                <?php echo htmlspecialchars($brand['name']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<?php if (empty($products)): ?>
    <p style="text-align:center; padding: 20px; font-size: 1.1em;">
        No products found <?php echo ($selected_brand_id && $current_brand_name !== "Unknown Brand" && $current_brand_name !== "Error loading brand") ? "for the brand '" . htmlspecialchars($current_brand_name) . "'" : ""; ?>. 
        Please check back later or try another brand!
    </p>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <?php?>
            <div class="product-card">
                <div style="cursor: pointer;" data-product-id="<?php echo $product['id']; ?>" class="view-details-img-trigger">
                    <img src="<?php echo htmlspecialchars($product['image_url'] ?: ($path_prefix ?? '') . 'assets/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <h3 style="cursor: pointer;" data-product-id="<?php echo $product['id']; ?>" class="view-details-name-trigger"><?php echo htmlspecialchars($product['name']); ?></h3>
                
                <?php if (!empty($product['brand_name'])): ?>
                    <p class="brand"><?php echo htmlspecialchars($product['brand_name']); ?></p>
                <?php endif; ?>
                
                <div class="price-section">
                    <?php if ($product['is_on_sale'] && isset($product['sale_price']) && $product['sale_price'] > 0): ?>
                        <span class="sale-price">₱<?php echo number_format((float)$product['sale_price'], 2); ?></span>
                        <span class="original-price">₱<?php echo number_format((float)$product['price'], 2); ?></span>
                    <?php else: ?>
                        <span class="price">₱<?php echo number_format((float)$product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>

                <p class="short-desc"><?php echo htmlspecialchars(substr($product['short_description'] ?? '', 0, 70)) . (strlen($product['short_description'] ?? '') > 70 ? '...' : ''); ?></p>

                <?php
                    $stock_class = '';
                    $stock_text = 'In Stock'; 
                    if (isset($product['stock_status'])) {
                        if ($product['stock_status'] == 'out_of_stock') {
                            $stock_class = 'out-of-stock';
                            $stock_text = 'Out of Stock';
                        } elseif ($product['stock_status'] == 'on_backorder') {
                            $stock_class = 'low-stock'; 
                            $stock_text = 'On Backorder';
                        } elseif (isset($product['stock_quantity']) && $product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0) {
                            $stock_class = 'low-stock';
                            $stock_text = 'Low Stock (' . $product['stock_quantity'] . ' left)';
                        }
                    } else {
                        $stock_text = 'Stock Info N/A'; $stock_class = 'low-stock'; 
                    }
                ?>
                <span class="stock-info <?php echo $stock_class; ?>"><?php echo $stock_text; ?></span>
                
                <div style="margin-top: auto;"> 
                    <button class="view-details-btn" data-product-id="<?php echo $product['id']; ?>">
                        View Details
                    </button>
                    <?php if (isset($product['stock_status']) && $product['stock_status'] != 'out_of_stock'): ?>
                    <button class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>" style="margin-top:5px;">
                        Add to Cart
                    </button>
                    <?php else: ?>
                    <button class="add-to-cart-btn" disabled style="margin-top:5px; background-color:#ccc; cursor:not-allowed;">
                        <?php echo (isset($product['stock_status']) && $product['stock_status'] == 'out_of_stock') ? 'Out of Stock' : 'Unavailable'; ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
require 'templates/footer.php';
?>