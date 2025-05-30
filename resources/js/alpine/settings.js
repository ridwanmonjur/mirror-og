import { createApp, reactive } from "petite-vue";
import Swal from "sweetalert2";
import { Tooltip } from "bootstrap";

document.querySelectorAll('.search-bar').forEach((element)=> {
    element.remove();
});

const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

function setErrorCurrentPassword (errorMessage) {
    const error = document.getElementById('current-password-error');
    error.classList.remove('d-none');
    error.textContent = errorMessage;
}

function TransactionComponent() {
    const transactionsDataInput = document.getElementById('transactions-data');
    const initialTransactions = JSON.parse(transactionsDataInput.value || '[]');

    return {
        init() {
            this.transactions = [...initialTransactions.data]
        },
        
        // Data
        transactions: [],
        loading: false,
        hasMore: initialTransactions.has_more,
        nextCursor: initialTransactions.next_cursor,

        // Methods
        async loadTransactions(reset = false) {
            if (this.loading) return;
            
            this.loading = true;
            
            try {
                const params = new URLSearchParams({
                    per_page: initialTransactions.per_page
                });


                if (!reset && this.nextCursor) {
                    params.append('cursor', this.nextCursor);
                }

                const response = await fetch(`/wallet?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (reset) {
                    this.transactions = data.data;
                } else {
                    this.transactions.push(...data.data);
                }

                this.hasMore = data.has_more;
                this.nextCursor = data.next_cursor;
                
            } catch (error) {
                console.error('Error loading transactions:', error);
            } finally {
                this.loading = false;
            }
        },

        loadMore() {
            this.loadTransactions(false);
        },

        resetAndLoad() {
            this.transactions = [];
            this.nextCursor = null;
            this.hasMore = true;
            this.loadTransactions(true);
        }
    };
}



function AccountComponent() {
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let userProfile = JSON.parse(document.getElementById('initialUserProfile').value);

    return {
        isPasswordNull: userProfile.isPasswordNull,
        emailAddress: userProfile.email,
        recoveryAddress: userProfile.recovery_email,
        async changeEmailAddress(event) {
            try {
                let button = event.currentTarget;
                let {eventType, route} = button.dataset;
                const { value: currentEmailInput } = await Swal.fire({
                    showCancelButton: true,
                    confirmButtonColor: '#43A4D7',
                    reverseButtons: true,
                   
                    html: `
                        <div class="pt-4 pb-3">
                            <h5 class="modal-heading text-center"> Enter a new email address </h5>
                            <div class="mx-3 text-start mt-4">
                                <input type="email" 
                                    id="swal-new-email" 
                                    class="form-control border-primary" 
                                    placeholder="Enter new email"
                                >
                                <div class="text-red text-center mt-3 d-none" id="current-email-error">
                                
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Next',
                    didOpen: () => Swal.getConfirmButton().focus(),

                    preConfirm: () => {
                        const input = document.getElementById('swal-new-email');
                        const error = document.getElementById('current-email-error');
                        const value = input.value.trim();
                        
                        error.textContent = '';
                        
                        if (!value) {
                            error.classList.remove('d-none');

                            error.textContent = 'Please enter your new email!';
                            return false;
                        }
                        if ( !isValidEmail(value) ) {
                            error.classList.remove('d-none');

                            error.textContent = 'New email does not match!';
                            return false;
                        }
                        
                        return value;
                    }
                });
                
                if (currentEmailInput) {
                    if (this.emailAddress == currentEmailInput) {
                        window.toastError("This email address is your current email address. Please enter a new one!");
                        return;
                    }
                
                    let data = await fetch(route, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            eventType,
                            newEmail: currentEmailInput
                        })
                    });

                    data = await data.json();

                    if (data.success) {
                        Swal.fire({
                            confirmButtonColor: '#43A4D7',
                            reverseButtons: true,
                            icon: 'info',
                            title: 'Email verification sent!',
                            text: `A verification email has been sent to your current email, ${this.emailAddress}. Please verify it first.` 
                        });

                    } else {
                        if (data.message != null) {
                            toastError(data.message);
                        } else {
                            toastError('Failed to update email');
                        }
                    }


                }
           
            } catch (error) {
                toastError('Failed to update email');
            }
        },
        async changeRecoveryEmailAddress(event) {
            try {
                let button = event.currentTarget;
                let {eventType, route} = button.dataset;
                const { value: recoveryEmailInput } = await Swal.fire({
                    showCancelButton: true,
                    confirmButtonColor: '#43A4D7',
                    reverseButtons: true,
                   
                    html: `
                        <div class="pt-4 pb-3">
                            <h5 class="modal-heading text-center"> Enter a new email address </h5>
                            <div class="mx-3 text-start mt-4">
                                <input type="email" 
                                    id="swal-new-email" 
                                    class="form-control border-primary" 
                                    placeholder="Enter new email"
                                >
                                <div class="text-red text-center mt-3 d-none" id="current-email-error">
                                
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Next',
                    didOpen: () => Swal.getConfirmButton().focus(),

                    preConfirm: () => {
                        const input = document.getElementById('swal-new-email');
                        const error = document.getElementById('current-email-error');
                        const value = input.value.trim();
                        
                        error.textContent = '';
                        
                        if (!value) {
                            error.classList.remove('d-none');

                            error.textContent = 'Please enter your recovery email!';
                            return false;
                        }
                        if ( !isValidEmail(value) ) {
                            error.classList.remove('d-none');

                            error.textContent = 'Recovery email is not valid!';
                            return false;
                        }
                        
                        return value;
                    }
                });
                
                if (recoveryEmailInput) {
                    let data = await fetch(route, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken
                        },
                        body: JSON.stringify({
                            eventType,
                            newRecoveryEmail: recoveryEmailInput
                        })
                    });

                    data = await data.json();

                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            text: `Email updated successfully to '${recoveryEmailInput}'!` 
                        });

                        this.recoveryAddress = recoveryEmailInput;
                    } else {
                        if (data.message != null) {
                            toastError(data.message);
                        } else {
                            toastError('Failed to update email');
                        }
                    }
                }
           
            } catch (error) {
                toastError('Failed to update email');
            }
        },
        async changePassword(event) {
            try {
                let button = event.currentTarget;
                let {eventOneType, eventTwoType, route} = button.dataset;

                if (!this.isPasswordNull) {
                    const { value: currentPasswordInput, isConfirmed, isDenied } = await Swal.fire({
                        focusConfirm: true,
                        showCancelButton: true,
                        confirmButtonColor: '#43A4D7',
                        reverseButtons: true,
                        
                        html: `
                            <div class="pt-4 pb-3">
                                <h5 class="modal-heading text-center">Verify Current Password</h5>
                                <div class="mx-3 text-start mt-4">
                                    <input type="password" 
                                        id="swal-current-password" 
                                        class="form-control border-primary" 
                                        placeholder="Enter current password"
                                        autoComplete="new-password"

                                    >
                                    <div class="text-center mt-2 text-red d-none" id="current-password-error"></div>
                                </div>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showCancelButton: true,
                        confirmButtonText: 'Next',
                        preConfirm: async () => {
                            const input = document.getElementById('swal-current-password');
                            const error = document.getElementById('current-password-error');
                            const value = input.value.trim();
                            
                            error.textContent = '';
                            
                            if (!value) {
                                error.classList.remove('d-none');
    
                                error.textContent = 'Please enter your new email!';
                                return false;
                            }

                            try {
                                let data = await fetch(route, {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": csrfToken
                                    },
                                    body: JSON.stringify({
                                        eventType: eventOneType,
                                        currentPassword: value
                                    })
                                });
        
                                data = await data.json();
                                if (!data || !data.success) {
                                    setErrorCurrentPassword("This password is incorrect!");
                                    return false;
                                }
                            } catch (error2) {
                                setErrorCurrentPassword("Your current password is incorrect!");
                                return false;
                            }
                            
                            return value;
                        }
                    });

                    if (!isConfirmed && !isDenied) {
                        return;
                    }
                }  
                const { value: newPasswordInput } = await Swal.fire({
                    showCancelButton: true,
                    confirmButtonColor: '#43A4D7',
                    reverseButtons: true,
                    allowInputAutoFocus: false,
                    html: `
                        <div class="pt-4 pb-3">
                            <h5 class="text-center">Enter New Password</h5>
                            <div class="mx-3 text-start mt-4">
                                <input type="password" 
                                    id="swal-new-password" 
                                    class="form-control border-primary" 
                                    placeholder="Enter new password"
                                    autoComplete="new-password"
    
                                >
                                
                                <input type="password" 
                                    id="swal-confirm-password" 
                                    class="form-control border-primary mt-3" 
                                    placeholder="Confirm new password"
                                    autoComplete="new-password"

                                >
                                <div class="d-none text-red mt-2 text-center" id="new-password-error"></div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Change Password',
                    preConfirm: () => {
                        const newPassword = document.getElementById('swal-new-password');
                        const confirmPassword = document.getElementById('swal-confirm-password');
                        const newError = document.getElementById('new-password-error');
                        
                        const newValue = newPassword.value.trim();
                        const confirmValue = confirmPassword.value.trim();
                        
                        newError.textContent = '';
                        
                        if (!newValue) {
                            newError.classList.remove('d-none');
                            newError.textContent = 'Please enter a new password';
                            return false;
                        }
                        
                        if (newValue.length < 6) {
                            newError.classList.remove('d-none');
                            newError.textContent = 'Password must be at least 8 characters long';
                            return false;
                        }
                        
                        if (!confirmValue) {
                            newError.classList.remove('d-none');
                            newError.textContent = 'Please confirm your new password';
                            return false;
                        }
                        
                        if (newValue !== confirmValue) {
                            newError.classList.remove('d-none');
                            newError.textContent = 'Passwords do not match';
                            return false;
                        }
                        
                        return newValue;
                    }
                });
        
                if (!newPasswordInput) return; 

                let newData = await fetch(route, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        eventType: eventTwoType,
                        newPassword: newPasswordInput
                    })
                });

                newData = await newData.json();

                if (!newData.success) {
                    toastError('Failed to update password');
                    return;
                }
        
        
                Toast.fire({
                    icon: 'success',
                    text: 'Password updated successfully!'
                });
                this.isPasswordNull = false;

        
            } catch (error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update password',
                    text: error.message
                });
            }
        },
        init () {
            
            const urlParams = new URLSearchParams(window.location.search);
            function scrollToElement(elementId) {
                console.log(`Attempting to scroll to ${elementId}`);
                
                setTimeout(() => {
                    const element = document.getElementById(elementId);
                    
                    if (element) {
                        const elementPosition = element.getBoundingClientRect();
                        
                        window.scrollTo({
                            top: elementPosition.bottom,
                            behavior: 'smooth'
                        });
                    } else {
                        console.error(`Element ${elementId} not found`);
                    }
                }, 500);
            }
            
            if (urlParams.has('methods_limit')) {
                console.log('methods_limit parameter found');
                scrollToElement('nestedCollapse2');
            }
            
            if (urlParams.has('history_limit')) {
                console.log('history_limit parameter found');
                scrollToElement('nestedCollapse3');
            }
        }

    }
   
}

function openTab (id) {
    document.querySelectorAll('.container-main')?.forEach((element) => {
        element.classList.add('d-none');
    });

    document.querySelector(`#${id}`)?.classList.remove('d-none');
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });

    let mainNav = document.getElementById('main-nav');
    if (id === 'wallet-main') {
        mainNav.classList.add('d-none');
    } else {
        mainNav.classList.remove('d-none');
    }
}

function populateCoupons (event) {
    openTab('wallet-redeem-coupons');
    let target = event.currentTarget;
    let { couponCode } = target.dataset;
    let couponElement = document.querySelector("input#coupon_code");

    if (couponElement) {
        couponElement.value = couponCode;
        couponElement.focus();
    }
}

function emptyCoupons () {
    openTab('wallet-redeem-coupons');
    let couponElement = document.querySelector("input#coupon_code");

    if (couponElement) {
        couponElement.value = '';
        couponElement.focus();
    }
}

function fillInput (inputId, value) {
   
    let couponElement = document.querySelector(`input#${inputId}`);
    if (couponElement) {
        couponElement.value = value;
    }
    
}

function CouponStatusComponent() {
    return {
        status: 'default', // 'success', 'error', 'default'
        isSubmitting: false,
        message: null,
        
        get statusIcon() {
            switch(this.status) {
                case 'success':
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>`;
                case 'error':
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill text-red" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z"/>
                    </svg>`;
                default:
                    return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket-perforated" viewBox="0 0 16 16">
                        <path d="M4 4.85v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9z"/>
                        <path d="M1.5 3A1.5 1.5 0 0 0 0 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 1.5 13h13a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 16 6V4.5A1.5 1.5 0 0 0 14.5 3zM1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v1.05a2.5 2.5 0 0 0 0 4.9v1.05a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-1.05a2.5 2.5 0 0 0 0-4.9z"/>
                    </svg>`;
            }
        },
        
        get statusLabel() {
            switch(this.status) {
                case 'success':
                    return 'Coupon Applied Successfully';
                case 'error':
                    return 'Invalid Coupon Code';
                default:
                    return 'Enter Coupon Code';
            }
        },
        
        get statusClass() {
            switch(this.status) {
                case 'success':
                    return 'text-success';
                case 'error':
                    return 'text-red';
                default:
                    return 'text-muted';
            }
        },
        
        async submitCoupon(event) {
            event.preventDefault();

            let couponElement = document.querySelector("input#coupon_code");
            let couponCode = couponElement.value;
            if (!couponCode.trim()) {
                this.status = 'error';
                this.message = "You have not entered any code yet."
                return;
            }
            
            this.isSubmitting = true;
            this.status = 'default';
            this.message = null;
            
            try {
                const formData = new FormData();
                formData.append('coupon_code', couponCode);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
                
                const response = await fetch('/wallet/redeem-coupon', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    this.status = 'success';
                    this.message = data.message;

                } else {
                    this.status = 'error';
                    this.message = data.message;
                }
            } catch (error) {
                console.error('Error:', error);
                this.status = 'error';
                this.message = 'An unknown error has occured!';
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        CouponStatusComponent,
    }).mount('#coupon-form'); // Mount to your coupon form container
});



let originalBtnText = null;        
async function withdrawMoney(e) {
    e.preventDefault();
    let withdrawalForm = e.currentTarget;
    const submitBtn = withdrawalForm.querySelector('button.withdraw-button');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...';
    
    
    try {
        const formData = new FormData(withdrawalForm);
        
        const response = await fetch(withdrawalForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data && data.success) {
            Swal.fire({
                icon: 'success',
                'title': 'Successfully made your request!',
                text: data.message || 'Withdrawal request submitted successfully!',
                confirmButtonColor: '#43A4D7',
                willClose: () => {
                    window.location.reload();
                }
            });
            
        } else {
            console.log({data});
            console.log({data});
            console.log({data});
            if (data.action_required) {
                Swal.fire({
                    icon: 'info',
                    title: 'Bank Account Required',
                    text: 'You must link a bank account before making a withdrawal.',
                    showCancelButton: true,
                    confirmButtonText: 'Link Bank Account',
                    confirmButtonColor: '#43A4D7',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = data.link;
                    }
                });

                return;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Your request has failed!',
                text: data?.message || 'Withdrawal request failed!',
                confirmButtonColor: '#43A4D7'
            });
            
        }
        
    } catch (error) {
        console.error('Withdrawal submission error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Your request has failed!',
            text: 'Withdrawal request failed!',
            confirmButtonColor: '#43A4D7'
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
}

let settings = document.querySelector('.settings');
let wallet =  document.querySelector('.wallet');
let banks =  document.querySelector('.banks-tom');

if (settings) {
    document.addEventListener('DOMContentLoaded', () => {
        createApp({
            AccountComponent,
        }).mount(settings);
    });
}

else if (wallet) {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList]?.map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl));
    let firstElement = null;
    let list = document.querySelectorAll('#wallet-view-coupons .coupon')
    if (list && '0' in list) firstElement = list[0];

    if (firstElement) {
        firstElement.classList.add('coupon-active');
    }

    let withdrawalForm2 = document.getElementById('withdrawal-form')
    const submitBtn = withdrawalForm2.querySelector('button.withdraw-button');
    originalBtnText = submitBtn.textContent;

    withdrawalForm2.addEventListener('submit', withdrawMoney);
    window.openTab = openTab;
    window.fillInput = fillInput;
    window.populateCoupons = populateCoupons;
    window.emptyCoupons = emptyCoupons;


    document.addEventListener('DOMContentLoaded', () => {
        createApp({
            TransactionComponent,
            CouponStatusComponent
        }).mount(wallet);
    });
} 


