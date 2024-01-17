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
  var passwordField = document.getElementById('password');
  var toggleButton = document.getElementById('togglePassword');

  if (passwordField.type === 'password') {
      passwordField.type = 'text';
      toggleButton.className = 'fa fa-eye-slash';
  } else {
      passwordField.type = 'password';
      toggleButton.className = 'fa fa-eye';
  }
}