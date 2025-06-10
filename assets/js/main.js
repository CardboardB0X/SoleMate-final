// assets/js/main.js
document.addEventListener('DOMContentLoaded', function() {
    // --- Helper Functions ---
    function escapeHtml(unsafe) {
        if (unsafe === null || typeof unsafe === 'undefined') {
            return '';
        }
        return unsafe.toString()
            .replace(/&/g, "&")
            .replace(/</g, "<")
            .replace(/'/g, "'");
    }

    function nl2br(str) {
        if (typeof str === 'undefined' || str === null) {
            return '';
        }
        return str.replace(/(?:\r\n|\r|\n)/g, '<br>');
    }

    function closeModal(modalElement) {
        if (modalElement && modalElement.style.display === 'block') {
            modalElement.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore background scrolling
            // Specific resets for different modals
            if (modalElement.id === 'productModal') {
                const productModalBody = document.getElementById('modalBodyContent');
                if (productModalBody) {
                    productModalBody.innerHTML = '<div class="spinner" id="productModalSpinner"></div>';
                }
            }
            if (modalElement.id === 'loginModal') {
                const loginMessageDiv = document.getElementById('loginMessage');
                const loginModalForm = document.getElementById('loginModalForm');
                if (loginMessageDiv) loginMessageDiv.innerHTML = '';
                if (loginModalForm) loginModalForm.reset();
            }
        }
    }
    
    // --- Hamburger Menu Toggle ---
    const hamburger = document.querySelector('.hamburger-menu');
    const navMenu = document.querySelector('nav.main-navigation .nav-menu');

    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            this.classList.toggle('active');
            navMenu.classList.toggle('active');
            const isExpanded = this.getAttribute('aria-expanded') === 'true' || false;
            this.setAttribute('aria-expanded', !isExpanded);
        });
    }

    // --- Product Detail Modal ---
    const productModal = document.getElementById('productModal');
    const modalBodyContent = document.getElementById('modalBodyContent'); // For product modal
    let productModalSpinner;

    async function openProductDetailModal(productId) {
        if (!productModal || !modalBodyContent) {
            console.error("Product detail modal elements not found.");
            return;
        }
        productModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        modalBodyContent.innerHTML = '<div class="spinner" id="productModalSpinnerInstance" style="display: block;"></div>';
        productModalSpinner = document.getElementById('productModalSpinnerInstance');

        try {
            const response = await fetch(`get_product_details.php?id=${productId}`);
            if (!response.ok) {
                const errData = await response.json().catch(() => ({ error: `HTTP error! status: ${response.status}` }));
                throw new Error(errData.error || `HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (productModalSpinner) productModalSpinner.style.display = 'none';

            if (data.error) {
                modalBodyContent.innerHTML = `<p class="alert alert-danger">${escapeHtml(data.error)}</p>`;
                return;
            }

            let priceHTML = '';
            if (data.is_on_sale && data.sale_price > 0 && data.sale_price < data.price) {
                priceHTML = `<span class="sale-price">₱${parseFloat(data.sale_price).toFixed(2)}</span> <span class="original-price">₱${parseFloat(data.price).toFixed(2)}</span>`;
            } else {
                priceHTML = `<span class="price">₱${parseFloat(data.price).toFixed(2)}</span>`;
            }

            let stockText = 'In Stock';
            let stockClass = '';
            let canAddToCartModal = true;
            if (data.manage_stock) {
                if (data.stock_status === 'out_of_stock') { stockText = 'Out of Stock'; stockClass = 'out-of-stock'; canAddToCartModal = false; }
                else if (data.stock_status === 'on_backorder') { stockText = 'On Backorder'; stockClass = 'low-stock'; }
                else if (data.stock_quantity !== null && data.stock_quantity <= 5 && data.stock_quantity > 0) { stockText = `Low Stock (${data.stock_quantity} left)`; stockClass = 'low-stock';}
            } else {
                 if (data.stock_status === 'out_of_stock') { stockText = 'Out of Stock'; stockClass = 'out-of-stock'; canAddToCartModal = false;}
                 else { stockText = 'In Stock'; }
            }

            modalBodyContent.innerHTML = `
                <div class="product-modal-details">
                    <div class="product-modal-image-container"><img src="${escapeHtml(data.image_url || 'assets/images/placeholder.png')}" alt="${escapeHtml(data.name)}" class="modal-image"></div>
                    <div class="product-modal-info-container">
                        <h2 class="modal-product-name">${escapeHtml(data.name)}</h2>
                        ${data.brand_name ? `<p class="modal-product-brand">Brand: ${escapeHtml(data.brand_name)}</p>` : ''}
                        ${data.category_name ? `<p class="modal-product-category">Category: ${escapeHtml(data.category_name)}</p>` : ''}
                        ${data.sku ? `<p class="modal-product-sku">SKU: ${escapeHtml(data.sku)}</p>` : ''}
                        <div class="modal-price-section">${priceHTML}</div>
                        <span class="stock-info ${stockClass}" style="font-size:1em; padding: 5px 10px; margin-bottom:15px;">${escapeHtml(stockText)}</span>
                        <div class="modal-product-description">${data.description ? nl2br(escapeHtml(data.description)) : (data.short_description ? nl2br(escapeHtml(data.short_description)) : 'No detailed description available.')}</div>
                        <button class="btn add-to-cart-btn modal-add-to-cart" data-product-id="${escapeHtml(data.id)}" ${!canAddToCartModal ? 'disabled' : ''}>${canAddToCartModal ? 'Add to Cart' : (stockText === 'Out of Stock' ? 'Out of Stock' : 'Unavailable')}</button>
                    </div>
                </div>`;
            const modalAddToCartButton = modalBodyContent.querySelector('.modal-add-to-cart');
            if (modalAddToCartButton) { modalAddToCartButton.addEventListener('click', handleAddToCartClick); }
        } catch (error) {
            if (productModalSpinner) productModalSpinner.style.display = 'none';
            console.error('Error opening product detail modal:', error);
            modalBodyContent.innerHTML = `<p class="alert alert-danger">Sorry, an error occurred while loading product details: ${escapeHtml(error.message)}</p>`;
        }
    }

    document.querySelectorAll('.view-details-btn, .view-details-img-trigger, .view-details-name-trigger').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            if (productId) {
                openProductDetailModal(productId);
            }
        });
    });

    const productModalCloseButton = productModal ? productModal.querySelector('.close-button') : null;
    if (productModalCloseButton) {
        productModalCloseButton.addEventListener('click', () => closeModal(productModal));
    }
    if (productModal) {
        productModal.addEventListener('click', (event) => { if (event.target === productModal) closeModal(productModal); });
    }

    // --- Cart Functionality (Server-Side Interaction) ---
    const cartModal = document.getElementById('cartModal');
    const cartModalBody = document.getElementById('cartModalBody');
    const cartModalFooter = document.getElementById('cartModalFooter');
    const cartCountElement = document.getElementById('cart-count');
    const cartTotalAmountElement = document.getElementById('cartTotalAmount');

    function updateCartDisplay(cartData) {
        if (!cartCountElement || !cartTotalAmountElement || !cartModalBody || !cartModalFooter) {
            console.error("Cart display elements not all found for update.");
            return;
        }
        updateCartCount(cartData.total_items);
        cartTotalAmountElement.textContent = `₱${parseFloat(cartData.total_price || 0).toFixed(2)}`;

        if (!cartData.cart || cartData.cart.length === 0) {
            cartModalBody.innerHTML = '<p class="empty-cart-message">Your cart is currently empty.</p>';
            cartModalFooter.style.display = 'none';
        } else {
            cartModalBody.innerHTML = '';
            cartData.cart.forEach(item => {
                const itemSubtotal = (parseFloat(item.price) || 0) * (parseInt(item.quantity) || 0);
                const cartItemHTML = `
                    <div class="cart-item" data-product-id="${item.id}">
                        <img src="${escapeHtml(item.image_url || 'assets/images/placeholder.png')}" alt="${escapeHtml(item.name)}">
                        <div class="cart-item-details"><h4>${escapeHtml(item.name)}</h4><p>Price: ₱${parseFloat(item.price || 0).toFixed(2)}</p><p>Subtotal: ₱${itemSubtotal.toFixed(2)}</p></div>
                        <div class="cart-item-actions"><input type="number" class="cart-item-quantity-input" value="${item.quantity}" min="1" max="99" data-product-id="${item.id}" aria-label="Quantity for ${escapeHtml(item.name)}"><button class="remove-item-btn" data-product-id="${item.id}" aria-label="Remove ${escapeHtml(item.name)} from cart">×</button></div>
                    </div>`;
                cartModalBody.insertAdjacentHTML('beforeend', cartItemHTML);
            });
            cartModalFooter.style.display = 'block';
            cartModalBody.querySelectorAll('.cart-item-quantity-input').forEach(input => input.addEventListener('change', handleQuantityChange));
            cartModalBody.querySelectorAll('.remove-item-btn').forEach(button => button.addEventListener('click', handleRemoveItem));
        }
    }
    
    function updateCartCount(count) {
        if (cartCountElement) {
            const numCount = parseInt(count) || 0;
            cartCountElement.textContent = numCount;
            cartCountElement.style.display = numCount > 0 ? 'inline-block' : 'none';
        }
    }

    async function fetchCartDataAndUpdateDisplay() {
        try {
            const response = await fetch('get_cart_data.php');
            if (!response.ok) {
                throw new Error('Failed to fetch cart data, server responded with ' + response.status);
            }
            const cartData = await response.json();
            updateCartDisplay(cartData);
        } catch (error) {
            console.error("Error fetching cart data:", error);
            showCartNotification('Error: Could not load cart.', 'error', 5000);
        }
    }

    async function handleCartAction(action, productId, quantity = 1) {
        const formData = new FormData();
        formData.append('action', action);
        formData.append('product_id', productId);
        if (action === 'update' || (action === 'add' && quantity !== 1) ) {
            formData.append('quantity', quantity);
        }

        try {
            const response = await fetch('cart_actions.php', { method: 'POST', body: formData });
            if (!response.ok) {
                const errText = await response.text();
                throw new Error(`Server error: ${response.status} - ${errText}`);
            }
            const result = await response.json();
            if (result.success) {
                showCartNotification(result.message || 'Cart updated!', 'success');
                await fetchCartDataAndUpdateDisplay();
            } else {
                showCartNotification(result.message || 'Could not update cart.', 'error');
            }
        } catch (error) {
            console.error(`Error performing cart action (${action}):`, error);
            showCartNotification('An error occurred while updating cart. Please try again.', 'error');
        }
    }

    function handleAddToCartClick(event) {
        const button = event.target.closest('.add-to-cart-btn');
        if (!button || button.disabled) {
            return;
        }
        const productId = button.getAttribute('data-product-id');
        if (productId) {
            handleCartAction('add', productId, 1);
        }
    }

    document.querySelectorAll('.add-to-cart-btn').forEach(button => { // Attach to all initially present add-to-cart buttons
        button.addEventListener('click', handleAddToCartClick);
    });


    async function handleQuantityChange(event) {
        const productId = event.target.getAttribute('data-product-id');
        let newQuantity = parseInt(event.target.value);
        if (isNaN(newQuantity) || newQuantity < 0) {
            newQuantity = 0;
        }
        if (newQuantity > 99) {
            newQuantity = 99;
        }
        event.target.value = newQuantity;

        if (newQuantity === 0) {
            handleCartAction('remove', productId);
        } else {
            handleCartAction('update', productId, newQuantity);
        }
    }

    async function handleRemoveItem(event) {
        const button = event.target.closest('.remove-item-btn');
        const productId = button.getAttribute('data-product-id');
        if (productId) {
            handleCartAction('remove', productId);
        }
    }

    const cartIndicator = document.getElementById('cart-indicator');
    if (cartIndicator) {
        cartIndicator.addEventListener('click', async (e) => {
            e.preventDefault();
            if (cartModal) {
                await fetchCartDataAndUpdateDisplay();
                cartModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
    }
    const cartModalCloseButton = cartModal ? cartModal.querySelector('.cart-close-button') : null;
    if (cartModalCloseButton) {
        cartModalCloseButton.addEventListener('click', () => closeModal(cartModal));
    }
    if (cartModal) {
        cartModal.addEventListener('click', (event) => { if (event.target === cartModal) closeModal(cartModal); });
    }
    
    function showCartNotification(message, type = 'success', duration = 3000) {
        let n = document.getElementById('cart-notification');
        if (!n) {
            n = document.createElement('div');
            n.id = 'cart-notification';
            Object.assign(n.style, {
                position: 'fixed',
                bottom: '20px',
                right: '20px',
                padding: '12px 20px',
                borderRadius: '5px',
                boxShadow: '0 3px 8px rgba(0,0,0,0.15)',
                zIndex: '2000',
                opacity: '0',
                transform: 'translateY(20px)',
                transition: 'opacity 0.3s ease-in-out, transform 0.3s ease-in-out'
            });
            document.body.appendChild(n);
        }
        n.textContent = message;
        n.style.backgroundColor = type === 'error' ? '#e74c3c' : (type === 'warning' ? '#f39c12' : '#2ecc71');
        n.style.color = 'white';
        
        requestAnimationFrame(() => {
            n.style.opacity = '1';
            n.style.transform = 'translateY(0)';
        });
        
        if (n.notificationTimer) clearTimeout(n.notificationTimer);
        if (n.removeTimer) clearTimeout(n.removeTimer);

        n.notificationTimer = setTimeout(() => {
            n.style.opacity = '0';
            n.style.transform = 'translateY(20px)';
            n.removeTimer = setTimeout(() => {
                 if (document.body.contains(n)) {
                    // document.body.removeChild(n); // Optional: uncomment to remove element
                 }
            }, 500);
        }, duration);
    }

    // --- Login Modal Functionality ---
    const loginModal = document.getElementById('loginModal');
    const loginModalTrigger = document.getElementById('loginModalTrigger');
    const loginModalForm = document.getElementById('loginModalForm');
    const loginMessageDiv = document.getElementById('loginMessage');
    const loginModalCloseButton = loginModal ? loginModal.querySelector('.login-close-button') : null;

    function openLoginModal() {
        if (loginModal) {
            loginModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            if (loginMessageDiv) loginMessageDiv.innerHTML = '';
            if (loginModalForm) loginModalForm.reset();
        }
    }

    if (loginModalTrigger) {
        loginModalTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            openLoginModal();
        });
    }
    
    const loginModalTriggersFromOtherPages = document.querySelectorAll('#loginModalTriggerFromRegister, #loginModalTriggerFromRegister2, #loginModalTriggerFromProtectedPage');
    loginModalTriggersFromOtherPages.forEach(trigger => {
        if (trigger) {
            trigger.addEventListener('click', (e) => { e.preventDefault(); openLoginModal(); });
        }
    });


    if (loginModalCloseButton) {
        loginModalCloseButton.addEventListener('click', () => closeModal(loginModal));
    }
    if (loginModal) {
        loginModal.addEventListener('click', (event) => {
            if (event.target === loginModal) {
                closeModal(loginModal);
            }
        });
    }

    if (loginModalForm) {
        loginModalForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (loginMessageDiv) loginMessageDiv.innerHTML = '<div class="spinner" style="margin: 10px auto;"></div>';

            const formData = new FormData(this);
            try {
                const response = await fetch('process_login.php', { method: 'POST', body: formData });
                let result;
                if (!response.ok) {
                    // Try to parse error response, otherwise use status text
                    try {
                        result = await response.json();
                        throw new Error(result.message || `Server error: ${response.status}`);
                    } catch (jsonError) {
                        throw new Error(`Server error: ${response.status}`);
                    }
                }
                result = await response.json(); // If response.ok

                if (result.success) {
                    if (loginMessageDiv) {
                        loginMessageDiv.innerHTML = `<div class="alert alert-success">${escapeHtml(result.message)}</div>`;
                    }
                    setTimeout(() => {
                        window.location.href = result.redirect_url; // Use URL from PHP response
                    }, 1500);
                } else {
                    if (loginMessageDiv) {
                        loginMessageDiv.innerHTML = `<div class="alert alert-danger">${escapeHtml(result.message || 'Login failed.')}</div>`;
                    }
                }
            } catch (error) {
                console.error('Login form submission error:', error);
                if (loginMessageDiv) {
                    loginMessageDiv.innerHTML = `<div class="alert alert-danger">An error occurred during login: ${escapeHtml(error.message)}</div>`;
                }
            }
        });
    }

    // --- General Modal Closing (Escape Key) ---
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (productModal && productModal.style.display === 'block') closeModal(productModal);
            if (cartModal && cartModal.style.display === 'block') closeModal(cartModal);
            if (loginModal && loginModal.style.display === 'block') closeModal(loginModal);
        }
    });

    // Initial cart data load on page ready
    fetchCartDataAndUpdateDisplay();
});