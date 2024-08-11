 import Swal from 'sweetalert2'
 import * as Popper from '@popperjs/core'
 window.Popper = Popper
 import * as bootstrap from 'bootstrap'
 window.bootstrap = bootstrap


window.loadBearerHeader = function() {
    return {
        credentials: 'include'
    };
};

window.loadBearerCompleteHeader = function() {
    return {
        credentials: 'include',
        'Accept': 'application/json',
        'Content-Type': 'application/json',

    };
};

const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    width: 'fit-content',
    padding: '0.7rem',
    showConfirmButton: false,
    timer: 6000,
    timerProgressBar: true
})


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
    Swal.fire({
        icon: "warning",
        title: title,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        confirmButtonColor: "#43A4D7",
    }).then((result) => {
        if (result.isConfirmed) {
            resultConfirmedCb()
        } else if (result.isDenied) {
            if (resultDeniedCb) { resultDeniedCb() }
        }
    })
}

window.loadMessage = () => {
    let success = localStorage.getItem('success');
    let error = localStorage.getItem('error');
    let tab = localStorage.getItem('tab');
    let message = localStorage.getItem('message');

    if (tab) {
        document.getElementById(tab)?.click();
    }

    if (success === 'true' && message) {
        Swal.fire({
            icon: "success",
            title: "Success...",
            confirmButtonColor: "#43A4D7",
            text: message,
            timer: 6000
          });
     
    } else if (error === 'true') {
            Swal.fire({
                confirmButtonColor: "#43A4D7",
                icon: "error",
                title: "Oops...",
                text: message ?? "Something went wrong...",
                footer: 'Please try again following our feedback.'
            });
    }

    localStorage.removeItem('success');
    localStorage.removeItem('error');
    localStorage.removeItem('message');
    localStorage.removeItem('tab');
}

window.Toast = Toast;
window.Swal = Swal;
