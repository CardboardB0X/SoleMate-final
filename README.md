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
8.  [API Endpoints (if applicable)](#api-endpoints)
9.  [Troubleshooting](#troubleshooting)
10. [Future Enhancements / TODO](#future-enhancements--todo)
11. [Contributing (if open source)](#contributing)

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
