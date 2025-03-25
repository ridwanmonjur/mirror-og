

const querySelector = ".wrapper input";

window.onload = () => {
    document.querySelector('.wrapper').focus();
    document.querySelectorAll(querySelector).forEach(inpObj => {
        const id = inpObj?.id;
        if (id) {
            inpObj.addEventListener("click", () => {
                const spanMesage = document.querySelector(`#${id} ~ span.placeholder-moves-up`);
                spanMesage.style.top = "0px";
                spanMesage.style.fontSize = '12px';
            });

            inpObj.addEventListener("blur", () => {
                const spanMesage = document.querySelector(`#${id} ~ span.placeholder-moves-up`);
                if (inpObj.value == "") { 
                    spanMesage.style.top = "20px";
                    spanMesage.style.fontSize = '1rem';
                }

            });


            inpObj.addEventListener("input", () => {
                const label = inpObj.parentElement;
                const placeholder = label.querySelector('.placeholder-moves-up');
                if (inpObj.value !== '') {
                    placeholder.style.fontSize = '12px';
                    placeholder.style.top = '0px';
                } else {
                    placeholder.style.top = '';
                    placeholder.style.fontSize = '';
                }
            });
        } 
    });
}

// Toggle password
function togglePassword(id) {
    const passwordInput = document.getElementById(id);
    let parent = passwordInput.parentElement;
    const eyeIcon = parent.querySelector('.eye-icon');
    const eyeOffIcon = parent.querySelector('.eye-off-icon');
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      eyeIcon.classList.add('d-none');
      eyeOffIcon.classList.remove('d-none');
    } else {
      passwordInput.type = 'password';
      eyeIcon.classList.remove('d-none');
      eyeOffIcon.classList.add('d-none');
    }
}


function submitSignInUpForm(event) {
    event.preventDefault(); 

    document.querySelector('.flash-message').innerHTML = '';
    const formData = new FormData(event.target);

    fetch( event.target.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => {
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = data.route;
        } else {
            document.querySelectorAll('.field-error-message').forEach(element => {
                element.classList.add('d-none');
                element.innerHTML = '';
            });
            
            document.querySelector('.flash-message').innerHTML = `<div class="text-red">${data.message}</div>`;
            const validationErrors = data.errors?.validation ?? null;
            if (validationErrors) {
               
                Object.keys(validationErrors).forEach(field => {
                    const errorElement = document.getElementById(`${field}-error`);
                    if (errorElement) {
                        errorElement.innerHTML = 
                            `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>${validationErrors[field]}`;                        errorElement.classList.remove('d-none');
                    }
                });
            }

        }

        if (data.verify && data.validityLink) {
            let validityDiv = document.querySelector('.emailValidity');
            validityDiv.classList.remove('d-none');
            let hyperLink = validityDiv.querySelector('a.validityLink');
            hyperLink.href = data.validityLink;
        }
    })
    .catch(error => {
        document.querySelector('.flash-message').innerHTML = `<div class="text-red">An error occurred during form submission. Please try again later</div>`;
    });

    return false;
}

function redirectToGoogle() {
    const routes = document.getElementById('routeConfig');
    window.location.href = routes.dataset.googleLogin;
}

function redirectToSteam() {
    const routes = document.getElementById('routeConfig');
    window.location.href = routes.dataset.steamLogin;
}


