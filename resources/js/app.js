 // Default Laravel bootstrapper, installs axios
 import './bootstrap';
 import Swal from 'sweetalert2'
 // Added: Actual Bootstrap JavaScript dependency
 import * as Popper from '@popperjs/core'
 window.Popper = Popper
 import * as bootstrap from 'bootstrap'
 window.bootstrap = bootstrap
 import cookie from 'cookiejs';
window.cookie = cookie;
// todo cookie package
// https://www.npmjs.com/package/cookiejs
 import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
window.storeToken = function(token) {
    localStorage.setItem('jwtToken', token);
};

window.loadBearerHeader = function() {
    return {
        'Authorization': `Bearer ${localStorage.getItem('jwtToken')}`,
    };
};

window.loadBearerCompleteHeader = function() {
    return {
        'Authorization': `Bearer ${localStorage.getItem('jwtToken')}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    };
};



window.Swal = Swal;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    width: 'fit-content',
    padding: '0.7rem',
    showConfirmButton: false,
    timer: 6000,
    timerProgressBar: true
})

window.Toast = Toast;

window.toastError = function (message, error = null) {
    console.error(error)
    Toast.fire({
        icon: 'error',
        text: message
    });
}

window.toastWarningAboutRole = function (button, message) {
    toastError(message);
    button.style.cursor = 'not-allowed';
}

window.dialogOpen = (title, resultConfirmedCb, resultDeniedCb) => { 
    console.log("fired")
    console.log("fired")
    console.log("fired")
    console.log("fired")
    Swal.fire({
        title,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        dangerButtonColor: "#8CCD39",
        confirmButtonColor: "#43A4D7",
        customClass: {
            actions: 'my-actions',
            cancelButton: 'order-1 right-gap',
            confirmButton: 'order-2',
            denyButton: 'order-3',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            resultConfirmedCb()
        } else if (result.isDenied) {
            resultDeniedCb()
        }
    })
}


