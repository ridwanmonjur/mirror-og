/**
 * Shop Checkout Wallet and Coupon Functionality
 * Handles wallet payments and coupon application for shop checkout
 */

document.addEventListener('DOMContentLoaded', function() {
    // Coupon code functionality
    const couponCodeInput = document.getElementById('coupon_code');
    const amountInput = document.getElementById('amount');
    const walletCouponInput = document.getElementById('wallet_coupon_code');
    
    // Sync coupon code between main form and wallet modal
    if (couponCodeInput && walletCouponInput) {
        couponCodeInput.addEventListener('input', function() {
            walletCouponInput.value = this.value;
        });
    }

    // Real-time coupon validation
    if (couponCodeInput) {
        let couponTimeout;
        couponCodeInput.addEventListener('input', function() {
            clearTimeout(couponTimeout);
            const couponCode = this.value.trim();
            
            if (couponCode.length > 2) {
                couponTimeout = setTimeout(() => {
                    validateCoupon(couponCode);
                }, 500);
            }
        });
    }

    // Amount validation
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            validateAmount(parseFloat(this.value));
        });
    }

    // Wallet modal handling
    const walletModal = document.getElementById('walletModal');
    const walletForm = document.getElementById('walletPaymentForm');
    
    if (walletModal && walletForm) {
        walletForm.addEventListener('submit', function(e) {
            handleWalletSubmit(e);
        });
    }
});

/**
 * Validate coupon code via AJAX
 */
function validateCoupon(couponCode) {
    const cartTotal = parseFloat(document.getElementById('amount').value);
    
    // Create a temporary form data to validate coupon
    const formData = new FormData();
    formData.append('coupon_code', couponCode);
    formData.append('amount', cartTotal);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch('/shop/validate-coupon', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        displayCouponFeedback(data);
        updateAmountAfterCoupon(data);
    })
    .catch(error => {
        console.error('Coupon validation error:', error);
    });
}

/**
 * Display coupon validation feedback
 */
function displayCouponFeedback(data) {
    const couponInput = document.getElementById('coupon_code');
    let feedbackElement = document.getElementById('coupon-feedback');
    
    // Remove existing feedback
    if (feedbackElement) {
        feedbackElement.remove();
    }
    
    // Create new feedback element
    feedbackElement = document.createElement('div');
    feedbackElement.id = 'coupon-feedback';
    feedbackElement.className = 'mt-2';
    
    if (data.success) {
        feedbackElement.className += ' text-success';
        feedbackElement.innerHTML = `<small><i class="bi bi-check-circle"></i> ${data.message}</small>`;
        couponInput.classList.remove('is-invalid');
        couponInput.classList.add('is-valid');
    } else {
        feedbackElement.className += ' text-danger';
        feedbackElement.innerHTML = `<small><i class="bi bi-exclamation-circle"></i> ${data.message}</small>`;
        couponInput.classList.remove('is-valid');
        couponInput.classList.add('is-invalid');
    }
    
    couponInput.parentNode.parentNode.appendChild(feedbackElement);
}

/**
 * Update amount input after coupon application
 */
function updateAmountAfterCoupon(data) {
    if (data.success && data.finalFee !== undefined) {
        const amountInput = document.getElementById('amount');
        amountInput.value = data.finalFee.toFixed(2);
        
        // Update wallet modal amount if applicable
        const walletAmountInput = document.getElementById('wallet_amount');
        if (walletAmountInput) {
            walletAmountInput.value = data.finalFee.toFixed(2);
        }
        
        // Update order summary if needed
        updateOrderSummary(data);
    }
}

/**
 * Validate payment amount
 */
function validateAmount(amount) {
    const minAmount = parseFloat(document.querySelector('small[class*="text-muted"]').textContent.match(/RM (\d+\.?\d*)/)[1]);
    const amountInput = document.getElementById('amount');
    let feedbackElement = document.getElementById('amount-feedback');
    
    // Remove existing feedback
    if (feedbackElement) {
        feedbackElement.remove();
    }
    
    if (amount < minAmount) {
        feedbackElement = document.createElement('div');
        feedbackElement.id = 'amount-feedback';
        feedbackElement.className = 'mt-2 text-danger';
        feedbackElement.innerHTML = `<small>Minimum payment amount is RM ${minAmount.toFixed(2)}</small>`;
        amountInput.classList.add('is-invalid');
        amountInput.parentNode.parentNode.appendChild(feedbackElement);
        return false;
    } else {
        amountInput.classList.remove('is-invalid');
        amountInput.classList.add('is-valid');
        return true;
    }
}

/**
 * Handle wallet form submission
 */
function handleWalletSubmit(event) {
    const couponCode = document.getElementById('coupon_code').value;
    const walletCouponInput = document.getElementById('wallet_coupon_code');
    
    // Sync coupon code to wallet form
    if (walletCouponInput) {
        walletCouponInput.value = couponCode;
    }
    
    // Show loading state
    const submitButton = event.target.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    // Allow form to submit normally
    // The loading state will be reset by page redirect
}

/**
 * Update order summary display
 */
function updateOrderSummary(data) {
    // This function can be expanded to update the order summary
    // section with discount information
    if (data.discount && data.discount > 0) {
        const orderSummary = document.querySelector('.cart-calculator table');
        if (orderSummary) {
            // Add or update discount row
            let discountRow = document.getElementById('discount-row');
            if (!discountRow) {
                discountRow = document.createElement('tr');
                discountRow.id = 'discount-row';
                orderSummary.appendChild(discountRow);
            }
            
            discountRow.innerHTML = `
                <td>Coupon Discount</td>
                <td class="text-end text-success">- RM ${data.discount.toFixed(2)}</td>
            `;
        }
    }
}

/**
 * Show error message
 */
function showError(message) {
    // Create or update error alert
    let errorAlert = document.getElementById('checkout-error');
    if (!errorAlert) {
        errorAlert = document.createElement('div');
        errorAlert.id = 'checkout-error';
        errorAlert.className = 'alert alert-danger mt-3';
        document.querySelector('.px-3').prepend(errorAlert);
    }
    
    errorAlert.innerHTML = message;
    errorAlert.scrollIntoView({ behavior: 'smooth' });
}

/**
 * Show success message
 */
function showSuccess(message) {
    // Create or update success alert
    let successAlert = document.getElementById('checkout-success');
    if (!successAlert) {
        successAlert = document.createElement('div');
        successAlert.id = 'checkout-success';
        successAlert.className = 'alert alert-success mt-3';
        document.querySelector('.px-3').prepend(successAlert);
    }
    
    successAlert.innerHTML = message;
    successAlert.scrollIntoView({ behavior: 'smooth' });
}