// Client validation
function checkValidity(inpObj, inputID) {
    const input = document.querySelector(`#${inputID}`);
    const spanMesage = document.querySelector(`#${inputID} ~ span.placeholder-moves-up`);
    const fieldErrorMesage = document.querySelector(`#${inputID} ~ .field-error-message`);
    const customErrorMessages = {
        email: "Email address is invalid.",
    };

    if (input.value.trim() == "") {
        spanMesage.style.top = "0px";
    } else {
        spanMesage.style.top = "0px";
    }

    if (!inpObj.checkValidity()) {
        const customErrorMessage = customErrorMessages[inputID] || inpObj.validationMessage;
        fieldErrorMesage.innerHTML = `<i
        class="fas fa-exclamation-circle form_icon__error"></i><span>${customErrorMessage}</span>`;
        inpObj.classList.add("input__error");
        fieldErrorMesage.classList.remove("d-none");
        fieldErrorMesage.style.opacity = "1";
        return true;
    } else {
        inpObj.classList.remove("input__error");
        fieldErrorMesage.innerHTML = "";
        fieldErrorMesage.classList.add("d-none");
        fieldErrorMesage.style.opacity = "0";
        return false;
    }
}

const querySelector = ".wrapper input";

window.onload = () => {
    document.querySelector('.wrapper').focus();
    // document.querySelectorAll(querySelector).forEach(inpObj => {
    //     const id = inpObj?.id;
    //     console.log({inpObj, id})
    //     if (id) {
    //         const spanMesage = document.querySelector(`#${id} ~ span.placeholder-moves-up`);

    //         if (inpObj.value.trim() === "") {
    //             console.log({inpObj, id})
    //             if (spanMesage) spanMesage.style.top = "20px";
    //         } else {
    //             if (spanMesage) spanMesage.style.top = "0px";
    //         }

    //         inpObj.addEventListener("blur", () => {
    //             checkValidity(inpObj, id);
    //         });
    //     } else {
    //         console.warn("Input element without ID found:", inpObj);
    //     }
    // });
}

function movePlaceholderUp(input) {
    const label = input.parentElement;
    const placeholder = label.querySelector('.placeholder-moves-up');
    if (input.value !== '') {
        placeholder.style.top = '0px';
        placeholder.style.fontSize = '12px';
    } else {
        placeholder.style.top = '';
        placeholder.style.fontSize = '';
    }
}

// Toggle password
function togglePassword(fieldId, buttonId) {
    var passwordField = document.getElementById(fieldId);
    var toggleButton = document.getElementById(buttonId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.className = 'fa fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleButton.className = 'fa fa-eye';
    }
}

function submitForm(event) {
    console.log("hit");
    console.log("hit");
    console.log("hit");
    console.log("hit");
    event.preventDefault(); 
    let isSucceededValidation = true;

    document.querySelector('.flash-message').innerHTML = '';
    const formData = new FormData(event.target);
    document.querySelectorAll(querySelector).forEach(inpObj => {
        const id = inpObj?.id;
        console.log({inpObj, id})
        if (id) {
            isSucceededValidation = isSucceededValidation && checkValidity(inpObj, id);
        } else {
            console.warn("Input element without ID found:", inpObj);
        }
    });

    if (!isSucceededValidation) {
        return true;
    }

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
            console.log({data})
            // TODO
            // COOKIE TASK
            window.location.href = data.route;
        } else {
            document.querySelector('.flash-message').innerHTML = `<div class="text-red">${data.message}</div>`;
            console.error('Error:', data.message);
        }
    })
    .catch(error => {
        document.querySelector('.flash-message').innerHTML = `<div class="text-red">An error occurred during form submission. Please try again later</div>`;
        console.error('Error during login:', error);
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


function togglePassword(fieldId, buttonId) {
    var passwordField = document.getElementById(fieldId);
    var toggleButton = document.getElementById(buttonId);

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleButton.className = 'fa fa-eye-slash';
    } else {
        passwordField.type = 'password';
        toggleButton.className = 'fa fa-eye';
    }
}