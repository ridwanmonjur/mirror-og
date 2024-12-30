import { createApp, reactive } from "petite-vue";
import Swal from "sweetalert2";

const FormModal = Swal.mixin({
    showCancelButton: true,
    confirmButtonColor: '#43A4D7',
    reverseButtons: true,
    allowInputAutoFocus: false
    
});

document.querySelectorAll('.search-bar').forEach((element)=> {
    element.remove();
});

const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

let userProfile = JSON.parse(document.getElementById('initialUserProfile').value);
console.log(userProfile);
console.log(userProfile);
console.log(userProfile);
function AccountComponent() {
    return {
        emailAddress: userProfile.email,
        recoveryAddress: userProfile.recoveryAddress,
        async changeEmailAddress(event) {
            try {
                let button = event.currentTarget;
                let {eventType, route} = button.dataset;
                console.log({eventType});
                console.log({eventType});
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
                                <div class="invalid-feedback" id="current-email-error"></div>
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
                        
                        input.classList.remove('text-red');
                        error.textContent = '';
                        
                        if (!value) {
                            input.classList.add('text-red');
                            error.textContent = 'Please enter your new email';
                            return false;
                        }
                        if ( !isValidEmail(value) ) {
                            input.classList.add('text-red');
                            error.textContent = 'New email does not match';
                            return false;
                        }
                        
                        return value;
                    }
                });
                
                if (value) {
                    await fetch(route, {
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

                    Toast.fire({
                        icon: 'success',
                        text: `Email updated successfully to '${currentEmailInput}'!` 
                    });
                }
           
               
                
             
        
            } catch (error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update email',
                    text: error.message
                });
            }
        },
        async changePassword() {
            try {
                const { value: currentPasswordInput } = await Swal.fire({
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
                                <div class="invalid-feedback" id="current-password-error"></div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Next',
                });
        
                if (!currentPasswordInput) return; 
        
                const { value: newPasswordInput } = await FormModal.fire({
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
                                <div class="invalid-feedback" id="new-password-error"></div>
                                
                                <input type="password" 
                                    id="swal-confirm-password" 
                                    class="form-control border-primary mt-3" 
                                    placeholder="Confirm new password"
                                    autoComplete="new-password"

                                >
                                <div class="invalid-feedback" id="confirm-password-error"></div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Change Password',
                    preConfirm: () => {
                        const newPassword = document.getElementById('swal-new-password');
                        const confirmPassword = document.getElementById('swal-confirm-password');
                        const newError = document.getElementById('new-password-error');
                        const confirmError = document.getElementById('confirm-password-error');
                        
                        const newValue = newPassword.value.trim();
                        const confirmValue = confirmPassword.value.trim();
                        
                        newPassword.classList.remove('text-red');
                        confirmPassword.classList.remove('text-red');
                        newError.textContent = '';
                        confirmError.textContent = '';
                        
                        if (!newValue) {
                            newPassword.classList.add('text-red');
                            newError.textContent = 'Please enter a new password';
                            return false;
                        }
                        
                        if (newValue.length < 8) {
                            newPassword.classList.add('text-red');
                            newError.textContent = 'Password must be at least 8 characters long';
                            return false;
                        }
                        
                        if (!confirmValue) {
                            confirmPassword.classList.add('text-red');
                            confirmError.textContent = 'Please confirm your new password';
                            return false;
                        }
                        
                        if (newValue !== confirmValue) {
                            confirmPassword.classList.add('text-red');
                            confirmError.textContent = 'Passwords do not match';
                            return false;
                        }
                        
                        return newValue;
                    }
                });
        
                if (!newPasswordInput) return; 
        
                
                Toast.fire({
                    icon: 'success',
                    text: 'Password updated successfully!'
                });
        
            } catch (error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update password',
                    text: error.message
                });
            }
        }

    }
}

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        AccountComponent,
       
    }).mount('#app');
});


document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('methods_limit')) {
        document.querySelector('[data-bs-target="#collapseTwo"]')?.click();
        
        document.querySelector('[data-bs-target="#nestedCollapse2"]')?.click();
        
        const element = document.getElementById('nestedHeading2');
        if (element) {
            const elementPosition = element.getBoundingClientRect().top;
            
            window.scrollTo({
                bottom: elementPosition,
                behavior: 'smooth'
            });
        }
    }

    if (urlParams.has('page_next')) {
        document.querySelector('[data-bs-target="#collapseTwo"]')?.click();
        
        document.querySelector('[data-bs-target="#nestedCollapse3"]')?.click();
        
        const element = document.getElementById('nestedHeading3');
        if (element) {
            const elementPosition = element.getBoundingClientRect().top;
            
            window.scrollTo({
                bottom: elementPosition,
                behavior: 'smooth'
            });
        }
    }
});