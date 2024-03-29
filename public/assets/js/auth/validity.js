// Client validation

function checkValidity(inpObj, inputID) {
    const fieldErrorMesage = document.querySelector(`#${inputID} ~ .field-error-message`);
    const customErrorMessages = {
        email: "Email address is invalid.",
        // Add more custom error messages for different fields if needed
    };

    if (!inpObj.checkValidity()) {
        const customErrorMessage = customErrorMessages[inputID] || inpObj.validationMessage;
        fieldErrorMesage.innerHTML = `<i class="fas fa-exclamation-circle form_icon__error"></i><span>${customErrorMessage}</span>`;
        inpObj.classList.add("input__error");
        fieldErrorMesage.classList.remove("d-none");
    } else {
        inpObj.classList.remove("input__error");
        fieldErrorMesage.innerHTML = "";
        fieldErrorMesage.classList.add("d-none");
    }
}

const idList = ["email", "password"];

idList.forEach(id => {
    const inpObj = document.getElementById(id);
    inpObj.addEventListener("blur", () => {
        checkValidity(inpObj, id);
    });
});

// Add an event listener to handle input changes
idList.forEach(id => {
    const inpObj = document.getElementById(id);
    const placeholder = document.querySelector(`#${id} + .placeholder-moves-up`);

    inpObj.addEventListener("input", () => {
        if (inpObj.value.trim() !== "") {
            placeholder.classList.add("has-content");
        } else {
            placeholder.classList.remove("has-content");
        }
    });
});

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