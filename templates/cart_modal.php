<?php $path_prefix_modal = $path_prefix ?? ''; // Use existing path_prefix if available?>


<div id="cartModal" class="modal cart-modal">

    <div class="cart-modal-content">

        <span class="close-button cart-close-button">×</span>

        <h2>Your Shopping Cart</h2>


        <div id="cartModalBody">

            <div class="empty-cart-message" style="display: none;">

                <p>Your cart is currently empty.</p>

                <p><a href="<?php echo $path_prefix_modal; ?>shop.php" class="btn btn-checkout">Continue Shopping</a></p>

            </div>



            <!--

            <div class="cart-item" data-cart-item-id="[PRODUCT_ID_OR_UNIQUE_KEY]">

                <img src="[IMAGE_URL]" alt="[PRODUCT_NAME]">

                <div class="cart-item-details">

                    <h4>[PRODUCT_NAME]</h4>

                    <p>Price: ₱[PRICE]</p>

                    <p>Size: [SIZE] / Color: [COLOR] (If applicable)</p>

                </div>

                <div class="cart-item-actions">

                    <input type="number" class="cart-item-quantity-input" value="[QUANTITY]" min="1" data-id="[PRODUCT_ID_OR_UNIQUE_KEY]">

                    <button class="remove-item-btn" data-id="[PRODUCT_ID_OR_UNIQUE_KEY]">×</button>

                </div>

                <div class="cart-item-subtotal" style="margin-left: 15px; font-weight: bold;">

                    ₱[SUBTOTAL]

                </div>

            </div>

            -->


            <div id="cartItemsContainer">


            </div>


            <div id="cartSpinner" class="spinner" style="display: none;"></div> <!-- Spinner for loading cart -->


        </div>


        <div id="cartModalFooter" style="display: none;">

            <div class="cart-total">

                Total: <span id="cartTotalAmount">₱0.00</span>

            </div>


            <div class="cart-actions">

                <button class="btn btn-secondary cart-close-button-alt">Continue Shopping</button>

                <a href="<?php echo $path_prefix_modal; ?>checkout.php" class="btn btn-checkout" id="checkoutButton">Proceed to Checkout</a>

            </div>

        </div>


    </div>

</div>