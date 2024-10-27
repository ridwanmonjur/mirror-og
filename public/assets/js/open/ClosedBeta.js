
const emailForm = document.getElementById('emailForm');
localStorage.setItem('disabled', false);

if (emailForm) {
    emailForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const emailInput = document.getElementById('emailInput').value;
        const submitButton = document.getElementById('submitButton');
        const url = submitButton.getAttribute('data-url');

        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': emailForm.querySelector('input[name="_token"]').getAttribute('content'),
            },
            body: JSON.stringify({ email: emailInput })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Confirmation popup for new email submission
                Swal.fire({
                    title: "Almost done!",
                    html: `
                        <div class="p-2 mt-2">
                            <p>Just check your email to confirm your email address and you're good to go.</p>
                            <p style="margin-top: 20px;">If you need any support, ping us at 
                                <a class="text-primary" href="mailto:supportmain@driftwood.gg">supportmain@driftwood.gg</a>
                                and we'll come to your aid.
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'Resend Confirmation Email',
                    confirmButtonColor: "#43A4D7",
                    showConfirmButton: true,
                    showDenyButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCloseButton: false,
                    denyButtonText: 'Back to Driftwood',
                    denyButtonColor: "red",
                    preConfirm: () => {
                        resendVerificationEmail(emailInput, url);
                        return false; 
                    }
                });
            } else if (data.error === 'duplicate_verified') {
                Swal.fire({
                    title: "Wait a minute…",
                    html: `
                        <div class="p-2 mt-2">
                            <p>This email address <strong>${emailInput}</strong> is already submitted and confirmed!</p>
                            <p>Submit another email address, or just wait for an invitation email for ${emailInput}.</p>
                            <p style="margin-top: 20px;">If you need any support, ping us at 
                                <a class="text-primary" href="mailto:supportmain@driftwood.gg">supportmain@driftwood.gg</a> 
                                and we'll come to your aid.
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'Back to Driftwood',
                    confirmButtonColor: "#43A4D7",
                    showConfirmButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCloseButton: false,
                });
            }
            
            else if (data.error === 'duplicate_unverified') {
                // Popup for duplicate unverified email
                Swal.fire({
                    title: "Wait a minute…",
                    html: `
                        <div class="p-2 mt-2">
                            <p>Looks like this email address <strong>${emailInput}</strong> has been submitted before, but hasn't been confirmed yet.</p>
                            <p>Just check your email to confirm your email address and you're good to go.</p>
                            <p style="margin-top: 20px;">If you need any support, ping us at 
                                <a class="text-primary" href="mailto:supportmain@driftwood.gg">supportmain@driftwood.gg</a> 
                                and we'll come to your aid.
                            </p>
                        </div>
                    `,
                    
                    confirmButtonText: 'Resend Confirmation Email',
                    confirmButtonColor: "#43A4D7",
                    stopKeydownPropagation: false,
                    showConfirmButton: true,
                    showDenyButton: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCloseButton: false,
                    denyButtonText: 'Back to Driftwood',
                    denyButtonColor: "red",
                    preConfirm: () => {
                        let isButtonDisabled = localStorage.getItem('disabled') === "true";
                        if (isButtonDisabled) return false;
                        else {
                            resendVerificationEmail(emailInput, url);
                            return false; 
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.message || 'An error occurred. Please try again.',
                    confirmButtonText: 'Back to Driftwood',
                    confirmButtonColor: "#43A4D7",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showCloseButton: false
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: 'An unexpected error occurred. Please try again.',
                confirmButtonText: 'Back to Driftwood',
                confirmButtonColor: "#43A4D7",
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false
            });
            console.error('Error:', error);
        });
        
        document.getElementById('emailInput').value = '';
    });
}

let countdownInterval = null;

function resendVerificationEmail(email, resendUrl) {
    
    toggleResetButtonToUnavailable();

    let secondsLeft = 90;

    if (countdownInterval) clearInterval(countdownInterval);
    
    countdownInterval = setInterval(() => {
        secondsLeft--;
        Swal.update({
            confirmButtonText: `Hold on for ${secondsLeft}s`
        });
        
        if (secondsLeft <= 0) {
            clearInterval(countdownInterval);
            toggleResetButtonToUnavailable(false);
        }
    }, 1000);
    
    fetch(resendUrl, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({ email: email, isResend: true })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.update({
                footer: "<span class='text-success'>We've sent you a confirmation email. Please take a look!</span>",
            });
        } else {
            if (countdownInterval) clearInterval(countdownInterval);
            toggleResetButtonToUnavailable(false);
            Swal.update({
                footer: `<span class='text-red'>${data.message || 'Failed to resend verification email. Please try again.'}</span>`,
            })
        }
    })
    .catch(error => {
        Swal.update({
            footer: `<span class='text-red'>${data.message || 'Failed to resend verification email. Please try again.'}</span>`,
        })
        
        clearInterval(countdownInterval);
      

        
    });
}

function toggleResetButtonToUnavailable (willDisable = true) {
    const confirmButton = Swal.getConfirmButton();

    if (willDisable) {
        
        localStorage.setItem('disabled', true);
        Swal.update({
            confirmButtonText: 'Hold on for 90s',
            confirmButtonColor: '#666666',
            footer: ''
        });

        Swal.disableButtons();
        confirmButton.pointerEvents = 'none';
    }

    else {
       
        localStorage.setItem('disabled', false);
        Swal.update({
            confirmButtonText: 'Resend Confirmation Email',
            confirmButtonColor: '#43A4D7',
            footer: ''
        });

        Swal.enableButtons();
        confirmButton.pointerEvents = 'auto';
    }
}