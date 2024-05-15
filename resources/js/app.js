 // Default Laravel bootstrapper, installs axios
 import Swal from 'sweetalert2'
 // Added: Actual Bootstrap JavaScript dependency
 import * as Popper from '@popperjs/core'
 window.Popper = Popper
 import * as bootstrap from 'bootstrap'
 window.bootstrap = bootstrap
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
        credentials: 'include'
    };
};

window.loadBearerCompleteHeader = function() {
    return {
        // credentials: 'include',
        credentials: 'include',
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

window.loadMessage = () => {
    let success = localStorage.getItem('success');
    let tab = localStorage.getItem('tab');
    let message = localStorage.getItem('message');

    if (tab) {
        document.getElementById(tab).click();
    }

    if (success === 'true') {
        Toast.fire({
            icon: 'success',
            text: message || (tab ? `Successfully switched to ${tab} tab.` : 'Operation successful.')
        });
    }

    localStorage.removeItem('success');
    localStorage.removeItem('message');
    localStorage.removeItem('tab');
}

