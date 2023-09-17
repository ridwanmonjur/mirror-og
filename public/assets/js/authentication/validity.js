// Client validation

function checkValidity(inpObj, inputID) {
  const placeholder = document.querySelector(`#${inputID} + .placeholder-moves-up`);
  const fieldErrorMesage = document.querySelector(`#${inputID} ~ .field-error-message`);

  if (!inpObj.checkValidity()) {
    fieldErrorMesage.classList.remove("d-none")
    fieldErrorMesage.innerHTML = `<i class="fas fa-exclamation-circle form_icon__error"></i><span>${inpObj.validationMessage}</span>`;
    inpObj.classList.add("input__error");
    placeholder.classList.add("placeholder__error");
  }
  else{
    fieldErrorMesage.classList.add("d-none")
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