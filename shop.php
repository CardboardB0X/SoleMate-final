<?php
$page_title = "Shop All Products";
$path_prefix = '';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/templates/header.php';

// Fetch all active categories for the filter menu
try {
    $cat_stmt = $pdo->query("SELECT category_id, name, slug FROM categories WHERE is_active = 1 ORDER BY name ASC");
    $all_categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $all_categories = [];
    error_log("Error fetching categories for shop: " . $e->getMessage());
}

// Fetch all active brands for the filter menu
try {
    $brand_stmt = $pdo->query("SELECT brand_id, name, slug FROM brands WHERE is_active = 1 ORDER BY name ASC");
    $all_brands = $brand_stmt->fetchAll();
} catch (PDOException $e) {
    $all_brands = [];
    error_log("Error fetching brands for shop: " . $e->getMessage());
}


// --- Filtering Logic ---
$where_clauses = ["p.is_active = 1", "(p.stock_status = 'in_stock' OR (p.stock_status = 'on_backorder' AND p.manage_stock = 1))"];
$params = [];
$filter_title_parts = [];

$current_filter_category = $_GET['category'] ?? null;
$current_filter_brand = $_GET['brand'] ?? null;
$current_filter_discounted = isset($_GET['filter']) && $_GET['filter'] === 'discounted';
$current_search_term = $_GET['search'] ?? null; // For a potential search bar

if ($current_filter_category) {
    // Fetch category name for title
    $cat_name_stmt = $pdo->prepare("SELECT name FROM categories WHERE slug = :slug AND is_active = 1");
    $cat_name_stmt->execute(['slug' => $current_filter_category]);
    $category_details = $cat_name_stmt->fetch();
    if ($category_details) {
        $where_clauses[] = "c.slug = :category_slug";
        $params[':category_slug'] = $current_filter_category;
        $filter_title_parts[] = "Category: " . e($category_details['name']);
    } else {
        $current_filter_category = null; // Invalid category slug
    }
}

if ($current_filter_brand) {
    // Fetch brand name for title
    $brand_name_stmt = $pdo->prepare("SELECT name FROM brands WHERE slug = :slug AND is_active = 1");
    $brand_name_stmt->execute(['slug' => $current_filter_brand]);
    $brand_details = $brand_name_stmt->fetch();
    if ($brand_details) {
        $where_clauses[] = "b.slug = :brand_slug";
        $params[':brand_slug'] = $current_filter_brand;
        $filter_title_parts[] = "Brand: " . e($brand_details['name']);
    } else {
        $current_filter_brand = null; // Invalid brand slug
    }
}

if ($current_filter_discounted) {
    $where_clauses[] = "p.is_on_sale = 1 AND p.sale_price IS NOT NULL AND p.sale_price > 0 AND p.sale_price < p.price";
    $filter_title_parts[] = "Discounted Items";
}

if ($current_search_term) {
    $where_clauses[] = "(p.name LIKE :search_term OR p.short_description LIKE :search_term OR p.description LIKE :search_term OR p.sku LIKE :search_term)";
    $params[':search_term'] = '%' . $current_search_term . '%';
    $filter_title_parts[] = "Search: \"" . e($current_search_term) . "\"";
}


$page_heading = "Our Products";
if (!empty($filter_title_parts)) {
    $page_heading = implode(' | ', $filter_title_parts);
} elseif (empty($products) && !$current_filter_category && !$current_filter_brand && !$current_filter_discounted && !$current_search_term) {
     $page_heading = "Shop All Products";
}


$sql = "SELECT 
            p.id, p.name, p.slug, p.short_description, p.price, p.sale_price, p.is_on_sale, p.image_url,
            p.stock_quantity, p.stock_status, p.manage_stock,
            b.name AS brand_name,
            c.name AS category_name_join 
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id";

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
    error_log("Error fetching products for shop page: " . $e->getMessage());
    echo "<p class='alert alert-danger container'>Could not load products due to a database error.</p>";
}

?>

<div class="shop-header">
    <h1 class="page-main-title"><?php echo e($page_heading); ?></h1>
</div>

<div class="filter-section">
    <h3>Filter Products</h3>
    <div class="filter-options-group">
        <h4>Categories:</h4>
        <div class="filter-options">
            <a href="<?php echo e($path_prefix); ?>shop.php" class="<?php echo (!$current_filter_category && !$current_filter_brand && !$current_filter_discounted && !$current_search_term) ? 'active' : ''; ?>">All Products</a>
            <?php foreach ($all_categories as $category): ?>
                <a href="<?php echo e($path_prefix); ?>shop.php?category=<?php echo e($category['slug']); ?>" 
                   class="<?php echo ($current_filter_category === $category['slug']) ? 'active' : ''; ?>">
                    <?php echo e($category['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="filter-options-group">
        <h4>Brands:</h4>
        <div class="filter-options">
             <a href="<?php echo e($path_prefix); ?>shop.php" class="<?php echo (!$current_filter_category && !$current_filter_brand && !$current_filter_discounted && !$current_search_term) ? 'active' : ''; ?>">All Brands</a> <!-- Link to show all if no brand filter -->
            <?php foreach ($all_brands as $brand): ?>
                <a href="<?php echo e($path_prefix); ?>shop.php?brand=<?php echo e($brand['slug']); ?>" 
                   class="<?php echo ($current_filter_brand === $brand['slug']) ? 'active' : ''; ?>">
                    <?php echo e($brand['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    
    <div class="filter-options-group">
        <h4>Offers:</h4>
        <div class="filter-options">
            <a href="<?php echo e($path_prefix); ?>shop.php?filter=discounted" 
               class="<?php echo ($current_filter_discounted) ? 'active' : ''; ?>">
                Discounted Items
            </a>
        </div>
    </div>
    

    <div class="filter-options-group">
        <h4>Search:</h4>
        <form action="shop.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo e($current_search_term); ?>">
            <?php if ($current_filter_category): ?><input type="hidden" name="category" value="<?php echo e($current_filter_category); ?>"><?php endif; ?>
            <?php if ($current_filter_brand): ?><input type="hidden" name="brand" value="<?php echo e($current_filter_brand); ?>"><?php endif; ?>
            <?php if ($current_filter_discounted): ?><input type="hidden" name="filter" value="discounted"><?php endif; ?>
            <button type="submit" class="btn">Search</button>
        </form>
    </div>

</div>


<?php if (empty($products)): ?>
    <p style="text-align:center; padding: 20px; font-size: 1.1em;">
        No products found matching your criteria. <?php if($current_filter_category || $current_filter_brand || $current_filter_discounted || $current_search_term): ?>
        <a href="<?php echo e($path_prefix); ?>shop.php">View all products</a>
        <?php endif; ?>
    </p>
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