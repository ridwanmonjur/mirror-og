import { createApp, reactive } from "petite-vue";
import Swal from "sweetalert2";


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
            console.log(initialTransactions);
            console.log("Transaction History initiated!");
            this.demoTransactions = [...this.transactions.slice(0, 3)];
            console.log(this.transactions);
        },
        
        // Data
        transactions: [],
        loading: false,
        hasMore: initialTransactions.has_more,
        nextCursor: initialTransactions.next_cursor,
        demoTransactions: [],

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
}

function populateCoupons (event) {
    openTab('wallet-redeem-coupons');
    let target = event.currentTarget;
    let { couponCode } = target.dataset;
    console.log({ couponCode });
    let couponElement = document.querySelector("input#coupon_code");
    console.log({ couponElement });

    if (couponElement) {
        couponElement.value = couponCode;
    }
}

function fillInput (inputId, value) {
   
    let couponElement = document.querySelector(`input#${inputId}`);
    if (couponElement) {
        couponElement.value = value;
    }
}

window.openTab = openTab;
window.fillInput = fillInput;
window.populateCoupons = populateCoupons;

document.addEventListener('DOMContentLoaded', () => {

    let settings = document.querySelector('.settings');
    let wallet =  document.querySelector('.wallet');

    if (settings) {
        createApp({
            AccountComponent,
        }).mount(settings);
    }
  
    if (wallet) {
        let firstElement = null;
            let list = document.querySelectorAll('#wallet-view-coupons .coupon')
            if (list && '0' in list) firstElement = list[0];

            if (firstElement) {
                firstElement.classList.add('coupon-active');
            }

            
        createApp({
            TransactionComponent,
        }).mount(wallet);
    }
    
});


