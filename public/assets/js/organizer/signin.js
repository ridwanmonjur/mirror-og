function submitForm(event) {
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