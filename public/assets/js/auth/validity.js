// Client validation
function checkValidity(inpObj, inputID) {
    const input = document.querySelector(`#${inputID}`);
    const spanMesage = document.querySelector(`#${inputID} ~ span.placeholder-moves-up`);
    const fieldErrorMesage = document.querySelector(`#${inputID} ~ .field-error-message`);
    const customErrorMessages = {
        email: "Email address is invalid.",
    };

    if (input.value.trim() == "") {
        spanMesage.style.top = "20px";
    } else {
        spanMesage.style.top = "0px";
    }

    if (!inpObj.checkValidity()) {
        const customErrorMessage = customErrorMessages[inputID] || inpObj.validationMessage;
        fieldErrorMesage.innerHTML = `<i
    class="fas fa-exclamation-circle form_icon__error"></i><span>${customErrorMessage}</span>`;
        inpObj.classList.add("input__error");
        fieldErrorMesage.classList.remove("d-none");
    } else {
        inpObj.classList.remove("input__error");
        fieldErrorMesage.innerHTML = "";
        fieldErrorMesage.classList.add("d-none");
    }
}

const querySelector = ".wrapper input";

window.onload = () => {
    document.querySelector('.wrapper').focus();
    document.querySelectorAll(querySelector).forEach(inpObj => {
        const id = inpObj?.id;
        console.log({inpObj, id})
        if (id) {
            const spanMesage = document.querySelector(`#${id} ~ span.placeholder-moves-up`);

            if (inpObj.value.trim() === "") {
                console.log({inpObj, id})
                if (spanMesage) spanMesage.style.top = "20px";
            } else {
                if (spanMesage) spanMesage.style.top = "0px";
            }

            inpObj.addEventListener("blur", () => {
                checkValidity(inpObj, id);
            });
        } else {
            console.warn("Input element without ID found:", inpObj);
        }
    });
}

// Flash message
function showFlashMessage(message, type) {
    const flashMessage = document.querySelector(".flash-message");
    flashMessage.classList.toggle("d-none");
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
