# Solemate E-Commerce Website

**Author:** ClickSenVee
**Repository:** https://github.com/CardboardB0X/SoleMate-final

## Table of Contents

1.  [Project Overview](#project-overview)
2.  [Features](#features)
3.  [Technology Stack](#technology-stack)
4.  [Setup and Installation](#setup-and-installation)
    *   [Prerequisites](#prerequisites)
    *   [Database Setup](#database-setup)
    *   [Application Setup](#application-setup)
    *   [Running the Application](#running-the-application)
5.  [Project Structure](#project-structure)
6.  [Key Functionalities & How They Work](#key-functionalities--how-they-work)
    *   [User Authentication (Login/Register)](#user-authentication)
    *   [Product Listing & Filtering](#product-listing--filtering)
    *   [Product Detail Modal](#product-detail-modal)
    *   [Shopping Cart](#shopping-cart)
    *   [Account Management](#account-management)
7.  [Database Schema](#database-schema)
8.  [API Endpoints](#api-endpoints)
9.  [Troubleshooting](#troubleshooting)
10. [Future Enhancements / TODO](#future-enhancements--todo)
11. [Contributing](#contributing)

---

## 1. Project Overview

Solemate is a web-based e-commerce platform designed for selling footwear. It allows users to browse products, view details, add items to a cart, register for an account, log in, manage their profile, view order history, and manage addresses. The project is built using PHP for server-side logic, SQL for data persistence, and plain HTML, CSS, and JavaScript for the front-end.

---

## 2. Features

*   **User Management:**
    *   User Registration with TOS agreement.
    *   User Login via modal.
    *   User Logout.
    *   Account Dashboard.
    *   Profile & Password Editing.
    *   Address Management (Add, Delete, Set Default).
*   **Product Catalog:**
    *   Display all products on a shop page.
    *   Filter products by Category, Brand, and Discounted status.
    *   Product cards with image, name, price, and stock status.
    *   Modal view for detailed product information.
*   **Shopping Cart:**
    *   Add products to cart (from product cards and detail modal).
    *   View cart contents in a modal.
    *   Update item quantities in the cart.
    *   Remove items from the cart.
    *   Server-side cart management using PHP Sessions.
*   **Order Management (Basic):**
    *   Order History page for logged-in users.
    *   Placeholder for viewing individual order details.
*   **Design:**
    *   Responsive design for various screen sizes.
    *   Modal-based interactions for product details and login.
*   **Easter Egg:**
    *   A humorous footer link.

---

## 3. Technology Stack

*   **Backend:** PHP
*   **Database:** MySQL
*   **Frontend:**
    *   HTML5
    *   CSS3 (Plain CSS, no extensions/frameworks)
    *   JavaScript
*   **Web Server:** Apache(typically run via XAMPP)
*   **Version Control:** Git & GitHub

---

## 4. Setup and Installation

### Prerequisites

*   A web server environment (XAMPP)
*   PHP (version 8.0+ recommended).
*   MySQL database server.
*   A database management tool like phpMyAdmin
*   Git (for cloning the repository).
*   A web browser (Microsoft Edge).

### Database Setup

1.  **Create Database:**
    *   Using phpMyAdmin or your SQL client, create a new database named `solemate_db`.
    *   Ensure the character set is `utf8mb4` and collation is `utf8mb4_unicode_ci`.
2.  **Import SQL Schema:**
    *   Import the provided SQL schema file (`database_dump.sql` or similar - *you'll need to provide this file from your phpMyAdmin export*) into the `solemate_db` database. This will create the necessary tables (`users`, `products`, `categories`, `brands`, `orders`, `order_items`, `user_addresses`).
    *   The SQL dump provided in the initial prompt can be used here.

### Application Setup

1.  **Clone the Repository (or download files):**
    ```bash
    git clone [Link to your GitHub Repository] solemate_project
    cd solemate_project
    ```
    If downloading a ZIP, extract it to your web server's document root (e.g., `htdocs/` for XAMPP).

2.  **Configure Database Connection:**
    *   Open the `config.php` file in the project root.
    *   Update the following database credentials to match your local environment:
        ```php
        define('DB_HOST', '127.0.0.1');
        define('DB_NAME', 'solemate_db');
        define('DB_USER', 'your_db_username'); // e.g., 'root'
        define('DB_PASS', 'your_db_password'); // e.g., '' or your password
        ```

3.  **Configure `SITE_URL`:**
    *   In `config.php`, ensure `SITE_URL` is correctly set to the base URL of your project.
    *   For local development, this might be:
        ```php
        // define('SITE_URL', 'http://localhost/solemate_project/'); // Example
        // The auto-detection logic should try to set this, but hardcoding for local dev is often easier:
        $project_folder_name = 'solemate_project'; // CHANGE if your folder is different, or '' if in web root
        // ... (rest of SITE_URL logic)
        ```
        **Verify this value carefully.**

4.  **File Permissions (if on Linux/macOS):**
    *   Ensure your web server has read access to all project files.
    *   If you implement features like image uploads by users (not covered yet), the upload directory would need write permissions for the web server user.

### Running the Application

1.  **Start Your Web Server:** Ensure Apache and MySQL (MariaDB) services are running from your XAMPP/WAMP/MAMP control panel or your system services.
2.  **Access in Browser:** Open your web browser and navigate to the `SITE_URL` you configured (e.g., `http://localhost/solemate_project/`).

---

## 5. Project Structure

*solemate_project/
*   assets/ # Frontend assets
*      css/ # (Currently style.css is in root, can be moved here)
*      images/ # Site images like logo, favicon, placeholder
*         placeholder.png
*         favicon.ico
*      js/
*         main.js # Main JavaScript file
*   templates/ # Reusable PHP template parts
*      header.php
*      footer.php
*      navigation.php
*      promotion_bar.php
*   uploads/ # User uploaded content or dynamic product images
*      products/ # Product images
*         example-shoe1.jpg
*   .gitkeep # To ensure the empty folder is tracked by Git
*   config.php # Database and site configuration
*   index.php # Homepage / Main product listing
*   shop.php # All products page with filters
*   get_product_details.php # AJAX endpoint for product modal
*   cart_actions.php # AJAX endpoint for cart operations
*   get_cart_data.php # AJAX endpoint to fetch cart data
*   register.php # User registration page
*   process_login.php # Handles login form submission (for modal)
*   logout.php # Handles user logout
*   account.php # User account dashboard
*   edit_profile.php # Page for editing user profile and password
*   process_edit_profile.php # Handles profile update form
*   process_change_password.php # Handles password change form
*   order_history.php # Page to display user's order history
*   view_order.php # Page to display details of a single order
*   manage_addresses.php # Page to manage user addresses
*   terms_of_service.php # Terms of Service page
*   secret_video.php # Easter egg video page
*   style.css # Main stylesheet
*   emman.jpg # Easter egg image
*   secret.ia.mp4 # Easter egg video
*   .gitignore # Specifies intentionally untracked files by Git
*   README.md # This documentation file


---

## 6. Key Functionalities & How They Work

### User Authentication

*   **Registration (`register.php`):**
    *   Collects user details (name, email, password, optional phone).
    *   Requires agreement to Terms of Service.
    *   Server-side validation for required fields, email format, email uniqueness, password length, and password confirmation.
    *   New user data is inserted into the `users` table.
*   **Login (Modal via `main.js` and `process_login.php`):**
    *   Login form is presented in a modal triggered from navigation or other links.
    *   Form submitted via AJAX (`fetch` API in `main.js`) to `process_login.php`.
    *   `process_login.php` validates credentials:
        *   Fetches user by email from `users` table.
        *   Verifies password using `password_verify()`.
    *   On success, user information (`user_id`, `user_first_name`, etc.) is stored in `$_SESSION`.
    *   `last_login_at` is updated.
    *   `process_login.php` returns a JSON response indicating success/failure and a redirect URL.
    *   JavaScript handles the redirect or displays error messages in the modal.
*   **Logout (`logout.php`):**
    *   Destroys the PHP session.
    *   Clears session cookies.
    *   Redirects to the homepage.

### Product Listing & Filtering (`index.php`, `shop.php`)

*   **`index.php`:** Displays a curated list of products (e.g., featured, recent).
*   **`shop.php`:**
    *   Displays all active and in-stock/backorderable products.
    *   Provides filter menus for:
        *   **Categories:** Fetches categories from the `categories` table. Filters products by `category.slug`.
        *   **Brands:** Fetches brands from the `brands` table. Filters products by `brand.slug`.
        *   **Discounted:** Filters products where `is_on_sale = 1` and `sale_price` is valid.
    *   PHP logic constructs SQL `WHERE` clauses based on GET parameters (`?category=...`, `?brand=...`, `?filter=discounted`).
    *   Products are displayed in a responsive grid using CSS.

### Product Detail Modal (`main.js`, `get_product_details.php`)

*   "View Details" buttons/triggers on product cards contain `data-product-id`.
*   Clicking a trigger calls `openProductDetailModal()` in `main.js`.
*   JavaScript makes an AJAX `fetch` request to `get_product_details.php?id=<product_id>`.
*   `get_product_details.php` fetches product data (including brand and category names via JOINs) from the database and returns it as JSON.
*   JavaScript dynamically populates the modal content with the fetched product details.

### Shopping Cart (PHP Sessions, `main.js`, `cart_actions.php`, `get_cart_data.php`)

*   **Storage:** The shopping cart is stored in the PHP `$_SESSION['cart']` array. Each item typically stores `id`, `name`, `price`, `quantity`, `image_url`.
*   **`cart_actions.php`:**
    *   Handles AJAX POST requests for 'add', 'update' (quantity), and 'remove' actions.
    *   Takes `product_id` and (optional) `quantity` as parameters.
    *   Validates product existence and stock before modifying the cart.
    *   Updates `$_SESSION['cart']`.
    *   Returns a JSON response indicating success/failure and a message.
*   **`get_cart_data.php`:**
    *   Handles AJAX GET requests.
    *   Reads `$_SESSION['cart']` and calculates total items and total price.
    *   Returns the cart contents and totals as JSON.
*   **`main.js`:**
    *   **Adding:** "Add to Cart" buttons trigger `handleCartAction('add', ...)`.
    *   **Viewing (Cart Modal):** Cart icon click triggers `fetchCartDataAndUpdateDisplay()`, which calls `get_cart_data.php` and then `updateCartDisplay()` to render the cart modal.
    *   **Updating/Removing:** Input fields and remove buttons in the cart modal trigger `handleCartAction('update', ...)` or `handleCartAction('remove', ...)`.
    *   **UI Updates:** Cart count in the navigation and cart modal contents are updated dynamically via JavaScript after successful cart actions.

### Account Management (`account.php`, `edit_profile.php`, etc.)

*   **`account.php` (Dashboard):**
    *   Protected page (requires login).
    *   Displays a welcome message and navigation sidebar/quick links to other account sections.
*   **`edit_profile.php`:**
    *   Allows users to update their `first_name`, `last_name`, `email`, and `phone`.
    *   Separate form for changing password (requires current password).
    *   Submits to `process_edit_profile.php` and `process_change_password.php` respectively.
    *   Uses session flash messages for success/error feedback.
*   **`order_history.php`:**
    *   Protected page.
    *   Fetches and displays a list of the logged-in user's past orders from the `orders` table.
    *   Links to `view_order.php` for detailed views.
*   **`manage_addresses.php`:**
    *   Protected page.
    *   Lists user's saved addresses from `user_addresses`.
    *   Allows adding new addresses and deleting existing ones.
    *   Allows setting default shipping/billing addresses.

---

## 7. Database Schema

*   **`users`:** Stores user account information (id, name, email, hashed password, admin status, etc.).
*   **`products`:** Stores product details (id, name, description, price, stock, image, category_id, brand_id, etc.).
*   **`categories`:** Stores product categories (id, name, slug).
*   **`brands`:** Stores product brands (id, name, slug, logo).
*   **`orders`:** Stores overall order information (id, user_id, total, status, addresses, timestamps, etc.).
*   **`order_items`:** Stores individual items within an order (id, order_id, product_id, quantity, price_at_purchase).
*   **`user_addresses`:** Stores user's shipping and billing addresses (id, user_id, address details, default flags).

*(Refer to the initial SQL dump or a schema diagram for detailed column information and relationships.)*

---

## 8. API Endpoints (Internal AJAX)

The following PHP scripts act as internal API endpoints for JavaScript AJAX calls:

*   **`get_product_details.php`**
    *   Method: `GET`
    *   Parameter: `id` (product ID)
    *   Returns: JSON object of product details or error.
*   **`cart_actions.php`**
    *   Method: `POST`
    *   Parameters: `action` ('add', 'update', 'remove'), `product_id`, `quantity` (for add/update).
    *   Returns: JSON object indicating success/failure and a message.
*   **`get_cart_data.php`**
    *   Method: `GET`
    *   Returns: JSON object with current cart items, total items, and total price.
*   **`process_login.php`**
    *   Method: `POST`
    *   Parameters: `email`, `password`.
    *   Returns: JSON object indicating login success/failure, message, and redirect URL.

---

## 9. Troubleshooting

*   **Blank Page / HTTP 500 Errors:** Check your web server's PHP error logs. Enable PHP error display in `config.php` for development (`ini_set('display_errors', 1); error_reporting(E_ALL);`).
*   **Features Not Working (Modals, Cart):**
    *   Open browser developer tools (F12).
    *   **Console Tab:** Look for JavaScript errors.
    *   **Network Tab:** Check AJAX requests (`get_product_details.php`, `cart_actions.php`, etc.). Verify their status codes (200 is OK, 404 Not Found, 500 Server Error) and the "Response" content.
*   **Incorrect Paths / 404 Errors for Pages or Assets:**
    *   Verify `SITE_URL` in `config.php` is correct.
    *   Verify `$path_prefix` logic in `templates/header.php` correctly resolves paths for CSS/JS from different page depths.
    *   Ensure linked PHP files exist in the correct locations.
*   **Database Connection Issues:** Double-check `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` in `config.php`. Ensure your MySQL/MariaDB server is running.
*   **Session Issues (Cart/Login not persisting):**
    *   Ensure `session_start()` is called at the very beginning of `config.php` or `templates/header.php` before any output.
    *   Check browser cookie settings.

---

## 10. Future Enhancements / TODO

*   [ ] Implement full checkout process with payment gateway integration.
*   [ ] Admin panel for managing products, categories, brands, orders, and users.
*   [ ] Product search functionality.
*   [ ] Product reviews and ratings.
*   [ ] User email verification.
*   [ ] "Forgot Password" functionality.
*   [ ] Pagination for product listings and order history.
*   [ ] More advanced filtering/sorting options for products.
*   [ ] Full implementation of "Edit Address" functionality.
*   [ ] Wishlist feature.
*   [ ] Improve UI/UX design.
*   [ ] Implement more robust security measures (CSRF protection, input validation, XSS prevention)(https://cheatsheetseries.owasp.org/index.html).
*   [ ] Unit and integration testing.
*   [ ] SEO optimization.

---

## 11. Contributing


*   **ClickSenVee Team**

*  **Acknowledgement**

*   **AJAX Form Submission:** Inspiration for the AJAX login form submission pattern was drawn from concepts discussed on StackOverflow, similar to https://stackoverflow.com/questions/1960240/jquery-ajax-submit-form.
*   **CSS Modal Design:** The basic modal overlay and content styling principles are common, but ideas were reinforced by examples like https://github.com/drublic/css-modal and https://www.w3schools.com/w3css/w3css_modal.asp.
*   **PHP Session Cart:** The general approach to managing a server-side cart using PHP sessions is a standard technique, widely documented in PHP tutorials and community forums like https://stackoverflow.com/questions/62906258/shopping-cart-using-php-session , https://www.bing.com/search?pglt=297&q=php+session+cart&cvid=892850320be84ec2baf5c6914cacee16&gs_lcrp=EgRlZGdlKgYIABBFGDkyBggAEEUYOTIGCAEQABhAMgYIAhAAGEAyBggDEAAYQDIGCAQQABhAMgYIBRAAGEAyBggGEAAYQDIGCAcQABhAMgYICBAAGEDSAQgyNjkxajBqMagCALACAA&FORM=ANNTA1&PC=LCTS , https://www.w3schools.com/php/php_sessions.asp , and https://www.php.net/manual/en/book.session.php.













SQL code(since commit/push does not work)

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `logo_url` varchar(512) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `name`, `slug`, `logo_url`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nike', 'nike', 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Logo_NIKE.svg/1200px-Logo_NIKE.svg.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(2, 'Adidas', 'adidas', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/20/Adidas_Logo.svg/1200px-Adidas_Logo.svg.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(3, 'Timberland', 'timberland', 'https://cdn.worldvectorlogo.com/logos/timberland-2.svg', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(4, 'Crocs', 'crocs', 'https://cdn.iconscout.com/icon/free/png-256/free-crocs-3421600-2854042.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(5, 'Vans', 'vans', 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Vans_logo.svg/1024px-Vans_logo.svg.png', NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(512) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `slug`, `description`, `image_url`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Sneakers', 'sneakers', 'Comfortable and stylish sneakers for everyday wear and sports.', NULL, NULL, 0, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(2, 'Boots', 'boots', 'Durable and fashionable boots for all weather conditions.', NULL, NULL, 0, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(3, 'Sandals', 'sandals', 'Open-toed footwear perfect for warm weather and casual outings.', NULL, NULL, 0, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_uid` varchar(36) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `billing_first_name` varchar(100) NOT NULL,
  `billing_last_name` varchar(100) NOT NULL,
  `billing_email` varchar(255) NOT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `billing_address_line1` varchar(255) NOT NULL,
  `billing_address_line2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(100) NOT NULL,
  `billing_zip_code` varchar(20) NOT NULL,
  `billing_country` varchar(100) NOT NULL DEFAULT 'Philippines',
  `shipping_first_name` varchar(100) DEFAULT NULL,
  `shipping_last_name` varchar(100) DEFAULT NULL,
  `shipping_email` varchar(255) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_address_line1` varchar(255) DEFAULT NULL,
  `shipping_address_line2` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_zip_code` varchar(20) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `ship_to_billing_address` tinyint(1) NOT NULL DEFAULT 1,
  `order_subtotal` decimal(12,2) NOT NULL,
  `shipping_method_name` varchar(100) DEFAULT NULL,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `order_total` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `payment_gateway_txn_id` varchar(100) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
  `order_status` enum('pending_payment','processing','shipped','delivered','completed','cancelled','refunded','on_hold') NOT NULL DEFAULT 'pending_payment',
  `customer_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paid_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_sku` varchar(100) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `item_subtotal` decimal(12,2) NOT NULL,
  `item_discount` decimal(10,2) DEFAULT 0.00,
  `item_total` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `is_on_sale` tinyint(1) NOT NULL DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `manage_stock` tinyint(1) NOT NULL DEFAULT 1,
  `stock_status` enum('in_stock','out_of_stock','on_backorder') NOT NULL DEFAULT 'in_stock',
  `image_url` varchar(512) DEFAULT NULL,
  `hover_image_url` varchar(512) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `rating_count` int(11) DEFAULT 0,
  `weight_kg` decimal(8,3) DEFAULT NULL,
  `dimensions_cm` varchar(100) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `short_description`, `category_id`, `brand_id`, `sku`, `price`, `sale_price`, `is_on_sale`, `stock_quantity`, `manage_stock`, `stock_status`, `image_url`, `hover_image_url`, `gallery_images`, `is_new_arrival`, `is_featured`, `average_rating`, `rating_count`, `weight_kg`, `dimensions_cm`, `meta_title`, `meta_description`, `meta_keywords`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nike Air Force 1 \'07', 'nike-air-force-1-07', 'The radiance lives on in the Nike Air Force 1 \'07, the b-ball OG that puts a fresh spin on what you know best: durably stitched overlays, clean finishes and the perfect amount of flash to make you shine.', 'Iconic white sneaker with timeless appeal.', 1, 1, 'CW2288-111', 5495.00, NULL, 0, 25, 1, 'in_stock', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/g1unhf3p620eirejzmzq/air-force-1-07-mens-shoes-jBrhbr.png', NULL, NULL, 1, 1, 4.90, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(2, 'Adidas Stan Smith', 'adidas-stan-smith', 'Timeless appeal. Effortless style. Everyday versatility. For over 50 years and counting, adidas Stan Smith Shoes have continued to hold their place as an icon.', 'Classic court style, reinvented for today.', 1, 2, 'FX5502', 4800.00, 4200.00, 1, 18, 1, 'in_stock', 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/7072580525644101a307ac270099804f_9366/Stan_Smith_Shoes_White_FX5502_01_standard.jpg', NULL, NULL, 0, 1, 4.80, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(3, 'Timberland 6-Inch Premium Boot', 'timberland-6-inch-boot', 'These waterproof boots are an icon. Crafted in premium, full-grain leather and seam-sealed for complete waterproof protection.', 'The original yellow waterproof boot.', 2, 3, 'TB010061713', 10500.00, NULL, 0, 12, 1, 'in_stock', 'https://images.timberland.com/is/image/TimberlandEU/10061713-hero?wid=720&hei=720', NULL, NULL, 0, 0, 4.90, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(4, 'Crocs Classic Clog - Black', 'crocs-classic-clog-black', 'The irreverent go-to comfort shoe that you’re sure to fall deeper in love with day after day. Crocs Classic Clogs offer lightweight Iconic Crocs Comfort™.', 'Lightweight, comfortable, and versatile clog.', 3, 4, '10001-001', 2750.00, NULL, 0, 30, 1, 'in_stock', 'https://media.crocs.com/images/t_pdphero/f_auto%2Cq_auto/products/10001_001_ALT150/crocs', NULL, NULL, 1, 0, 4.60, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(5, 'Vans Old Skool - Black/White', 'vans-old-skool-black-white', 'The Old Skool, Vans classic skate shoe and first to bare the iconic sidestripe, is a low top lace-up featuring sturdy canvas and suede uppers, re-enforced toecaps to withstand repeated wear, padded collars for support and flexibility, and signature rubber waffle outsoles.', 'The iconic sidestripe skate shoe.', 1, 5, 'VN000D3HY28', 3800.00, NULL, 0, 22, 1, 'in_stock', 'https://images.vans.com/is/image/Vans/VN000D3HY28-HERO?$583x583$', NULL, NULL, 1, 1, 4.70, 0, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-01 10:07:03', '2025-06-01 10:07:03'),
(6, 'Nike Zoom Freak 5', 'nike-zoom-freak-5', 'Designed for the explosive game of Giannis Antetokounmpo, the Zoom Freak 5 offers springy cushioning, secure support, and multidirectional traction.', 'High-performance basketball sneakers made for speed.', 1, 1, 'DX4985-100', 6395.00, 5795.00, 1, 22, 1, 'in_stock', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/014dabcf-3fc0-4023-a227-42a2890e08f0/zoom-freak-5-basketball-shoes-7sL0Jz.png', NULL, NULL, 1, 1, 4.85, 91, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:05:00', '2025-06-02 00:05:00'),
(7, 'Adidas Forum Low', 'adidas-forum-low', 'The Adidas Forum Low shoes bring basketball heritage into everyday streetwear with a low-cut profile and retro style.', 'Retro basketball-inspired sneakers with bold flair.', 1, 2, 'FY7756', 4995.00, 4495.00, 1, 20, 1, 'in_stock', 'https://assets.adidas.com/images/w_600,f_auto,q_auto/bf3a3e0b62164c09a6efac4b012dd507_9366/Forum_Low_Shoes_White_FY7756_01_standard.jpg', NULL, NULL, 1, 0, 4.70, 98, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:00:00', '2025-06-02 00:00:00'),
(8, 'Vans ComfyCush Era', 'vans-comfycush-era', 'Vans ComfyCush Era combines classic style with upgraded comfort through softer cushioning and a moisture-wicking lining.', 'Classic skater shoe with modern comfort upgrade.', 1, 5, 'VN0A3WM9VNE', 3895.00, NULL, 0, 15, 1, 'in_stock', 'https://images.vans.com/is/image/Vans/VN0A3WM9VNE-HERO?$PDP-FULL-IMAGE$', NULL, NULL, 1, 1, 4.60, 47, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:01:00', '2025-06-02 00:01:00'),
(9, 'Nike Air Max 90', 'nike-air-max-90', 'The Nike Air Max 90 stays true to its OG running roots with the iconic Waffle sole, stitched overlays and classic TPU accents.', 'Timeless runner with visible Max Air cushioning.', 1, 1, 'CN8490-100', 6495.00, NULL, 0, 18, 1, 'in_stock', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/181119f5-f197-4ae9-bfbc-29543071a99d/air-max-90-mens-shoes-6n3vKB.png', NULL, NULL, 0, 0, 4.80, 122, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:02:00', '2025-06-02 00:02:00'),
(10, 'Adidas NMD_R1', 'adidas-nmd-r1', 'The Adidas NMD_R1 blends running-inspired design with streetwear style, featuring a snug sock-like fit and Boost cushioning.', 'Urban-style sneakers with responsive Boost comfort.', 1, 2, 'FX4351', 6995.00, 6495.00, 1, 10, 1, 'in_stock', 'https://assets.adidas.com/images/w_600,f_auto,q_auto/1c9d50483ae34c02b82aabb201076b88_9366/NMD_R1_Shoes_White_FX4351_01_standard.jpg', NULL, NULL, 1, 1, 4.75, 84, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:03:00', '2025-06-02 00:03:00'),
(11, 'Nike Canyon Sandal', 'nike-canyon-sandal', 'The Nike Canyon Sandal brings rugged outdoor style and comfort together with a lightweight design and adjustable straps.', 'Outdoor-ready sandals with cushioned comfort.', 1, 3, 'CW9704-300', 4295.00, 3795.00, 1, 25, 1, 'in_stock', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/201c30c3-556e-4b95-a0cb-6f4410b52959/canyon-sandal-shoes-Jsd9Sd.png', NULL, NULL, 1, 1, 4.70, 65, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:05:00', '2025-06-02 00:05:00'),
(12, 'Adidas Adilette Comfort', 'adidas-adilette-comfort', 'Slide into relaxation with the Adidas Adilette Comfort, featuring a contoured footbed and soft cushioning.', 'Classic comfort slides for everyday wear.', 1, 2, 'FZ2863', 2795.00, 2395.00, 1, 30, 1, 'in_stock', 'https://assets.adidas.com/images/w_600,f_auto,q_auto/71cde4a3691c47f09ef5af5a00ac2ea5_9366/Adilette_Comfort_Slides_Black_FZ2863_01_standard.jpg', NULL, NULL, 1, 0, 4.65, 88, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:00:00', '2025-06-02 00:00:00'),
(13, 'Vans Trek Slip-On Sandal', 'vans-trek-slip-on-sandal', 'The Vans Trek Slip-On is a water-friendly sandal with a durable design and easy-on construction, perfect for summer days.', 'Durable slip-on sandals for water and sun.', 1, 5, 'VN0A5HF5Y28', 2495.00, NULL, 0, 18, 1, 'in_stock', 'https://images.vans.com/is/image/Vans/VN0A5HF5Y28-HERO?$PDP-FULL-IMAGE$', NULL, NULL, 1, 1, 4.55, 41, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:01:00', '2025-06-02 00:01:00'),
(14, 'Nike Victori One Slide', 'nike-victori-one-slide', 'The Nike Victori One Slide offers a versatile design with responsive foam and a contoured grip pattern for all-day wear.', 'Cushioned slides built for comfort and ease.', 1, 3, 'CN9675-003', 3195.00, NULL, 0, 20, 1, 'in_stock', 'https://static.nike.com/a/images/t_PDP_1280_v1/f_auto,q_auto:eco/54b1c110-2c4c-41b4-a469-2b1e24e391d5/victori-one-shower-slides-2H4kKd.png', NULL, NULL, 0, 0, 4.60, 77, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:02:00', '2025-06-02 00:02:00'),
(15, 'Adidas Comfort Flip-Flops', 'adidas-comfort-flip-flops', 'The Adidas Comfort Flip-Flops combine laid-back style with soft cushioning and a flexible footbed for everyday use.', 'Casual flip-flops with plush foot support.', 1, 2, 'GZ5893', 1895.00, 1595.00, 1, 35, 1, 'in_stock', 'https://assets.adidas.com/images/w_600,f_auto,q_auto/eb65ae71fd2f4a66b52eaf8b016020c2_9366/Comfort_Flip-Flops_Black_GZ5893_01_standard.jpg', NULL, NULL, 1, 1, 4.50, 53, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-02 00:03:00', '2025-06-02 00:03:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `is_admin`, `is_verified`, `verification_token`, `password_reset_token`, `password_reset_expires`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'juandelacruz@hotmail.com', '$2y$10$iBm06jlCLH.tAprzA1g3zO3RWXTQaFR696sPbm4MG6PUwQ7Peixe2', 'Juan', 'Dela Cruz', NULL, 0, 0, NULL, NULL, NULL, '2025-06-06 14:58:40', '2025-06-02 15:14:24', '2025-06-06 06:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_type` enum('shipping','billing') NOT NULL DEFAULT 'shipping',
  `is_default_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `is_default_billing` tinyint(1) NOT NULL DEFAULT 0,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Philippines',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug_brands` (`slug`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug_categories` (`slug`),
  ADD KEY `idx_parent_id_categories` (`parent_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_uid` (`order_uid`),
  ADD KEY `idx_order_uid_orders` (`order_uid`),
  ADD KEY `idx_user_id_orders` (`user_id`),
  ADD KEY `idx_billing_email_orders` (`billing_email`),
  ADD KEY `idx_payment_status_orders` (`payment_status`),
  ADD KEY `idx_order_status_orders` (`order_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_order_id_order_items` (`order_id`),
  ADD KEY `idx_product_id_order_items` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_slug_products` (`slug`),
  ADD KEY `idx_category_id_products` (`category_id`),
  ADD KEY `idx_brand_id_products` (`brand_id`),
  ADD KEY `idx_is_new_arrival_products` (`is_new_arrival`),
  ADD KEY `idx_is_featured_products` (`is_featured`),
  ADD KEY `idx_is_active_products` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email_users` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_user_id_addresses` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `fk_address_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
