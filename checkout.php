<?php
$page_title = "Checkout";
$path_prefix = '';
require_once __DIR__ . '/config.php';

// --- Security: User must be logged in ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['intended_url'] = SITE_URL . 'checkout.php';
    $_SESSION['checkout_error_message'] = "You need to be logged in to proceed to checkout.";
    header("Location: " . SITE_URL . "index.php"); // Redirect to homepage, JS will trigger login modal
    exit;
}
$user_id = $_SESSION['user_id'];

// --- Cart Check: Cart must not be empty ---
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    $_SESSION['message'] = ['type' => 'warning', 'text' => 'Your cart is empty. Please add some products before checking out.'];
    header("Location: " . SITE_URL . "shop.php");
    exit;
}

// --- Fetch user's saved addresses ---
$user_addresses = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM user_addresses WHERE user_id = :user_id ORDER BY is_default_shipping DESC, created_at DESC");
    $stmt->execute([':user_id' => $user_id]);
    $user_addresses = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching addresses for checkout: " . $e->getMessage());
    // Allow checkout with manual address entry
}

// --- Fetch shipping methods ---
$shipping_methods = [];
try {
    $stmt = $pdo->query("SELECT * FROM shipping_methods WHERE is_active = 1 ORDER BY cost ASC");
    $shipping_methods = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching shipping methods: " . $e->getMessage());
    // Potentially have a default or handle error
}

// --- Calculate cart totals (needed for display) ---
$cart_subtotal = 0;
$total_items_in_cart = 0;
foreach ($cart as $item) {
    $cart_subtotal += ($item['price'] * $item['quantity']);
    $total_items_in_cart += $item['quantity'];
}
// Placeholder for discounts, shipping cost will be dynamic
$discount_amount = 0.00; // Implement discount logic if needed
$shipping_cost = $shipping_methods[0]['cost'] ?? 50.00; // Default to first available or a flat rate
$grand_total = $cart_subtotal - $discount_amount + $shipping_cost;


// --- Form Data & Errors (if submission failed and redirected back) ---
$checkout_errors = $_SESSION['checkout_errors'] ?? [];
$checkout_form_data = $_SESSION['checkout_form_data'] ?? [];
unset($_SESSION['checkout_errors'], $_SESSION['checkout_form_data']);

require_once __DIR__ . '/templates/header.php';
?>

<h1 class="page-main-title">Checkout</h1>

<div class="checkout-container">
    <div class="checkout-form-section">
        <form id="checkoutForm" action="process_checkout.php" method="POST">
            <h3>Billing Details</h3>
            <?php if (!empty($checkout_errors)): ?>
                <div class="alert alert-danger">
                    <strong>Please correct the following errors:</strong><br>
                    <ul><?php foreach ($checkout_errors as $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="billing_first_name">First Name:</label>
                <input type="text" id="billing_first_name" name="billing_first_name" value="<?php echo e($checkout_form_data['billing_first_name'] ?? $_SESSION['user_first_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_last_name">Last Name:</label>
                <input type="text" id="billing_last_name" name="billing_last_name" value="<?php echo e($checkout_form_data['billing_last_name'] ?? $_SESSION['user_last_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_email">Email:</label>
                <input type="email" id="billing_email" name="billing_email" value="<?php echo e($checkout_form_data['billing_email'] ?? $_SESSION['user_email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_phone">Phone:</label>
                <input type="tel" id="billing_phone" name="billing_phone" value="<?php echo e($checkout_form_data['billing_phone'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_address_line1">Address Line 1:</label>
                <input type="text" id="billing_address_line1" name="billing_address_line1" value="<?php echo e($checkout_form_data['billing_address_line1'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_address_line2">Address Line 2 (Optional):</label>
                <input type="text" id="billing_address_line2" name="billing_address_line2" value="<?php echo e($checkout_form_data['billing_address_line2'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="billing_city">City:</label>
                <input type="text" id="billing_city" name="billing_city" value="<?php echo e($checkout_form_data['billing_city'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_zip_code">Zip Code:</label>
                <input type="text" id="billing_zip_code" name="billing_zip_code" value="<?php echo e($checkout_form_data['billing_zip_code'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="billing_country">Country:</label>
                <input type="text" id="billing_country" name="billing_country" value="Philippines" readonly> <!-- Default, or make selectable -->
            </div>

            <hr style="margin: 20px 0;">
            <h3>Shipping Details</h3>
            <div class="form-group">
                <input type="checkbox" id="ship_to_billing_address" name="ship_to_billing_address" value="1" 
                       <?php echo (!isset($checkout_form_data['ship_to_billing_address']) || !empty($checkout_form_data['ship_to_billing_address'])) ? 'checked' : ''; ?>>
                <label for="ship_to_billing_address" style="display:inline; font-weight:normal;">Ship to same address</label>
            </div>

            <div id="shipping_address_fields" style="<?php echo (!isset($checkout_form_data['ship_to_billing_address']) || !empty($checkout_form_data['ship_to_billing_address'])) ? 'display:none;' : ''; ?>">
                 <div class="form-group">
                    <label for="shipping_first_name">First Name:</label>
                    <input type="text" id="shipping_first_name" name="shipping_first_name" value="<?php echo e($checkout_form_data['shipping_first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="shipping_last_name">Last Name:</label>
                    <input type="text" id="shipping_last_name" name="shipping_last_name" value="<?php echo e($checkout_form_data['shipping_last_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="shipping_phone">Phone:</label>
                    <input type="tel" id="shipping_phone" name="shipping_phone" value="<?php echo e($checkout_form_data['shipping_phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="shipping_address_line1">Address Line 1:</label>
                    <input type="text" id="shipping_address_line1" name="shipping_address_line1" value="<?php echo e($checkout_form_data['shipping_address_line1'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="shipping_address_line2">Address Line 2 (Optional):</label>
                    <input type="text" id="shipping_address_line2" name="shipping_address_line2" value="<?php echo e($checkout_form_data['shipping_address_line2'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="shipping_city">City:</label>
                    <input type="text" id="shipping_city" name="shipping_city" value="<?php echo e($checkout_form_data['shipping_city'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="shipping_zip_code">Zip Code:</label>
                    <input type="text" id="shipping_zip_code" name="shipping_zip_code" value="<?php echo e($checkout_form_data['shipping_zip_code'] ?? ''); ?>">
                </div>
                 <div class="form-group">
                    <label for="shipping_country">Country:</label>
                    <input type="text" id="shipping_country" name="shipping_country" value="Philippines" readonly>
                </div>
            </div>
            
            <?php if(!empty($user_addresses)): ?>
            <div class="form-group">
                <label for="saved_address_billing">Use Saved Billing Address:</label>
                <select id="saved_address_billing" name="saved_address_billing">
                    <option value="">-- Enter New Billing Address --</option>
                    <?php foreach($user_addresses as $addr): ?>
                        <option value="<?php echo e($addr['address_id']); ?>" 
                                data-details='<?php echo e(json_encode($addr)); ?>'
                                <?php echo ($addr['is_default_billing'] && empty($checkout_form_data)) ? 'selected' : ''; ?> >
                            <?php echo e($addr['address_line1'] . ', ' . $addr['city'] . ($addr['is_default_billing'] ? ' (Default Billing)' : '') . ($addr['is_default_shipping'] ? ' (Default Shipping)' : '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" id="saved_address_shipping_group" style="<?php echo (!isset($checkout_form_data['ship_to_billing_address']) || !empty($checkout_form_data['ship_to_billing_address'])) ? 'display:none;' : ''; ?>">
                <label for="saved_address_shipping">Use Saved Shipping Address:</label>
                 <select id="saved_address_shipping" name="saved_address_shipping">
                    <option value="">-- Enter New Shipping Address --</option>
                     <?php foreach($user_addresses as $addr): ?>
                        <option value="<?php echo e($addr['address_id']); ?>" data-details='<?php echo e(json_encode($addr)); ?>' <?php echo ($addr['is_default_shipping'] && empty($checkout_form_data)) ? 'selected' : ''; ?>>
                            <?php echo e($addr['address_line1'] . ', ' . $addr['city'] . ($addr['is_default_shipping'] ? ' (Default Shipping)' : '') . ($addr['is_default_billing'] ? ' (Default Billing)' : '')); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>


            <hr style="margin: 20px 0;">
            <h3>Shipping Method</h3>
            <div class="form-group">
                <?php if(!empty($shipping_methods)): ?>
                    <?php foreach($shipping_methods as $index => $method): ?>
                    <div class="shipping-method-option">
                        <input type="radio" id="shipping_method_<?php echo e($method['method_id']); ?>" 
                               name="shipping_method_id" value="<?php echo e($method['method_id']); ?>"
                               data-cost="<?php echo e($method['cost']); ?>"
                               <?php echo ($index === 0 || (isset($checkout_form_data['shipping_method_id']) && $checkout_form_data['shipping_method_id'] == $method['method_id'])) ? 'checked' : ''; ?> required>
                        <label for="shipping_method_<?php echo e($method['method_id']); ?>">
                            <?php echo e($method['name']); ?> (₱<?php echo number_format((float)$method['cost'], 2); ?>)
                            <?php if($method['description']): ?> <small>- <?php echo e($method['description']); ?></small> <?php endif; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No shipping methods available. Please contact support.</p>
                    <input type="hidden" name="shipping_method_id" value="0"> <!-- Fallback -->
                <?php endif; ?>
            </div>

            <hr style="margin: 20px 0;">
            <h3>Payment Method</h3>
            <div class="form-group">
                <select name="payment_method" id="payment_method" required>
                    <option value="cod" <?php echo (isset($checkout_form_data['payment_method']) && $checkout_form_data['payment_method'] == 'cod') ? 'selected' : ''; ?>>Cash on Delivery (COD)</option>
                    <option value="bank_transfer" <?php echo (isset($checkout_form_data['payment_method']) && $checkout_form_data['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="card_mock" <?php echo (isset($checkout_form_data['payment_method']) && $checkout_form_data['payment_method'] == 'card_mock') ? 'selected' : ''; ?>>Credit/Debit Card (Mock)</option>
                </select>
            </div>
            <div id="card_mock_fields" style="display:none; border: 1px solid #eee; padding: 15px; margin-bottom:15px; border-radius:5px;">
                <p style="margin-top:0; font-weight:bold; color:#e67e22;">Mock Credit Card Details (For Demo Only)</p>
                <div class="form-group">
                    <label for="mock_card_number">Card Number:</label>
                    <input type="text" id="mock_card_number" name="mock_card_number" placeholder="xxxx xxxx xxxx xxxx">
                </div>
                <div style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1">
                        <label for="mock_card_expiry">Expiry (MM/YY):</label>
                        <input type="text" id="mock_card_expiry" name="mock_card_expiry" placeholder="MM/YY">
                    </div>
                    <div class="form-group" style="flex:1">
                        <label for="mock_card_cvc">CVC:</label>
                        <input type="text" id="mock_card_cvc" name="mock_card_cvc" placeholder="123">
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="customer_notes">Order Notes (Optional):</label>
                <textarea id="customer_notes" name="customer_notes" rows="3" placeholder="Notes about your order, e.g. special delivery instructions."><?php echo e($checkout_form_data['customer_notes'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="form-button btn-checkout" id="placeOrderBtn">Place Order</button>
        </form>
    </div>

    <div class="order-summary-section">
        <h3>Your Order</h3>
        <div id="checkoutOrderSummary">
            <?php foreach($cart as $productId => $item): ?>
            <div class="order-summary-item">
                <span><?php echo e($item['name']); ?> × <?php echo e($item['quantity']); ?></span>
                <strong>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
            </div>
            <?php endforeach; ?>
            <hr>
            <div class="order-summary-item">
                <span>Subtotal</span>
                <strong id="summary_subtotal">₱<?php echo number_format($cart_subtotal, 2); ?></strong>
            </div>
            <!-- Placeholder for discount display
            <div class="order-summary-item" id="summary_discount_row" style="display:none;">
                <span>Discount</span>
                <strong id="summary_discount_amount">-₱0.00</strong>
            </div>
            -->
            <div class="order-summary-item">
                <span>Shipping</span>
                <strong id="summary_shipping_cost">₱<?php echo number_format($shipping_cost, 2); ?></strong>
            </div>
            <hr>
            <div class="order-summary-item order-summary-total">
                <span>Total</span>
                <strong id="summary_grand_total">₱<?php echo number_format($grand_total, 2); ?></strong>
            </div>
        </div>
         <p style="font-size:0.8em; margin-top:15px; text-align:center;">By placing your order, you agree to our <a href="terms_of_service.php" target="_blank">Terms of Service</a> and <a href="privacy_policy.php" target="_blank">Privacy Policy</a>.</p>
    </div>
</div>

<?php
require_once __DIR__ . '/templates/footer.php';
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shipToBillingCheckbox = document.getElementById('ship_to_billing_address');
    const shippingFieldsDiv = document.getElementById('shipping_address_fields');
    const savedAddressShippingGroup = document.getElementById('saved_address_shipping_group');

    function toggleShippingFields() {
        if (shipToBillingCheckbox.checked) {
            shippingFieldsDiv.style.display = 'none';
            if(savedAddressShippingGroup) savedAddressShippingGroup.style.display = 'none';
            // Clear or disable shipping fields if needed
            shippingFieldsDiv.querySelectorAll('input').forEach(input => input.required = false);
        } else {
            shippingFieldsDiv.style.display = 'block';
            if(savedAddressShippingGroup) savedAddressShippingGroup.style.display = 'block';
            // Set shipping fields as required if they were
            document.getElementById('shipping_first_name').required = true;
            document.getElementById('shipping_last_name').required = true;
            document.getElementById('shipping_address_line1').required = true;
            document.getElementById('shipping_city').required = true;
            document.getElementById('shipping_zip_code').required = true;
        }
    }
    if (shipToBillingCheckbox) {
        shipToBillingCheckbox.addEventListener('change', toggleShippingFields);
        toggleShippingFields(); // Initial call
    }

    // Populate address fields from saved address dropdown
    function populateAddressFields(prefix, details) {
        document.getElementById(prefix + '_first_name').value = details.first_name || '';
        document.getElementById(prefix + '_last_name').value = details.last_name || '';
        document.getElementById(prefix + '_phone').value = details.phone || '';
        document.getElementById(prefix + '_address_line1').value = details.address_line1 || '';
        document.getElementById(prefix + '_address_line2').value = details.address_line2 || '';
        document.getElementById(prefix + '_city').value = details.city || '';
        document.getElementById(prefix + '_zip_code').value = details.zip_code || '';
        // Country is likely readonly, but if not:
        // document.getElementById(prefix + '_country').value = details.country || 'Philippines';
    }

    const savedBillingSelect = document.getElementById('saved_address_billing');
    if (savedBillingSelect) {
        savedBillingSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const details = JSON.parse(selectedOption.dataset.details);
                populateAddressFields('billing', details);
            }
        });
        // Pre-populate if a default billing is selected on load
        if (savedBillingSelect.value && savedBillingSelect.options[savedBillingSelect.selectedIndex].dataset.details){
             const details = JSON.parse(savedBillingSelect.options[savedBillingSelect.selectedIndex].dataset.details);
             populateAddressFields('billing', details);
        }
    }
    const savedShippingSelect = document.getElementById('saved_address_shipping');
    if (savedShippingSelect) {
        savedShippingSelect.addEventListener('change', function() {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const details = JSON.parse(selectedOption.dataset.details);
                populateAddressFields('shipping', details);
            }
        });
         if (savedShippingSelect.value && savedShippingSelect.options[savedShippingSelect.selectedIndex].dataset.details){
             const details = JSON.parse(savedShippingSelect.options[savedShippingSelect.selectedIndex].dataset.details);
             populateAddressFields('shipping', details);
        }
    }

    // Update order summary based on shipping method
    const shippingMethodRadios = document.querySelectorAll('input[name="shipping_method_id"]');
    const summaryShippingCostEl = document.getElementById('summary_shipping_cost');
    const summaryGrandTotalEl = document.getElementById('summary_grand_total');
    const summarySubtotal = parseFloat(document.getElementById('summary_subtotal').textContent.replace(/[^0-9.-]+/g,""));
    // const summaryDiscount = 0.00; // Add logic for discount if implemented

    function updateOrderSummaryTotals() {
        let selectedShippingCost = 0;
        const checkedShippingRadio = document.querySelector('input[name="shipping_method_id"]:checked');
        if (checkedShippingRadio) {
            selectedShippingCost = parseFloat(checkedShippingRadio.dataset.cost);
        }
        summaryShippingCostEl.textContent = `₱${selectedShippingCost.toFixed(2)}`;
        const grandTotal = summarySubtotal /* - summaryDiscount */ + selectedShippingCost;
        summaryGrandTotalEl.textContent = `₱${grandTotal.toFixed(2)}`;
    }

    shippingMethodRadios.forEach(radio => {
        radio.addEventListener('change', updateOrderSummaryTotals);
    });
    updateOrderSummaryTotals(); // Initial calculation


    // Toggle mock card fields
    const paymentMethodSelect = document.getElementById('payment_method');
    const cardFieldsDiv = document.getElementById('card_mock_fields');
    if(paymentMethodSelect && cardFieldsDiv){
        paymentMethodSelect.addEventListener('change', function(){
            cardFieldsDiv.style.display = this.value === 'card_mock' ? 'block' : 'none';
        });
        // Initial check
        cardFieldsDiv.style.display = paymentMethodSelect.value === 'card_mock' ? 'block' : 'none';
    }

    // Prevent multiple submissions
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    if(checkoutForm && placeOrderBtn){
        checkoutForm.addEventListener('submit', function(){
            placeOrderBtn.disabled = true;
            placeOrderBtn.textContent = 'Processing...';
        });
    }
});
</script>