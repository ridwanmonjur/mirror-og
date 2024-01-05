// Client validation

function checkValidity(inpObj, inputID) {
    const fieldErrorMesage = document.querySelector(`#${inputID} ~ .field-error-message`);

    if (!inpObj.checkValidity()) {
        fieldErrorMesage.classList.remove("d-none");
        fieldErrorMesage.innerHTML = `<i class="fas fa-exclamation-circle form_icon__error"></i><span>${inpObj.validationMessage}</span>`;
        inpObj.classList.add("input__error");
    } else {
        fieldErrorMesage.classList.add("d-none");
        inpObj.classList.remove("input__error");
        fieldErrorMesage.innerHTML = "";
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
function togglePassword() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}