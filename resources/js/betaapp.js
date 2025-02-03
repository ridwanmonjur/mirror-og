
import Swal from 'sweetalert2'
import * as Popper from '@popperjs/core'
window.Popper = Popper
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap;

const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    width: 'fit-content',
    padding: '0.7rem',
    showConfirmButton: false,
    timer: 6000,
    timerProgressBar: true
})

window.showLoading = ({ title = '', html = '', backdrop = true } = {}) => {
    return window.Swal.fire({
      title,
      html,
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      backdrop: backdrop ? 'rgba(0,0,0,0.7)' : 'transparent',
      didOpen: () => {
        Swal.showLoading();
      }
    });
};

window.closeLoading = () => {
    window.Swal.close();
}


window.toastError = function (message, error = null) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#43a4d7',
        confirmButtonText: 'OK'
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


window.Toast = Toast;
window.Swal = Swal;

