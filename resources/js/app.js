
import Swal from 'sweetalert2'
import * as Popper from '@popperjs/core'
window.Popper = Popper
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap;
import { createApp } from 'petite-vue';
import { PageNotificationComponent } from './custom/notifications';

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

window.loadMessage = () => {
    let success = localStorage.getItem('success');
    let error = localStorage.getItem('error');
    let message = localStorage.getItem('message');

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
}


window.Toast = Toast;
window.Swal = Swal;

import './libraries/lightgallery';
import './libraries/lightpicker';
import './libraries/motion';
import './libraries/tagify';
import './libraries/tippy';
import './libraries/file-edit';
import './libraries/colorpicker';
const pageMeta = document.querySelector('meta[name="page-component"]');
const pageName = pageMeta?.getAttribute('content');

if (pageName ) {
    try {
        import (`./alpine/${pageName}.js`);
    } catch (error) {
        console.error(`Failed to load component for ${pageName}:`, error);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    
    createApp({
        PageNotificationComponent,
    }).mount('#notif-dropdown');

    const colors = [
        '#234B5C',  // Rich navy blue
        '#8B4513',  // Saddle brown
        '#2E5D2E',  // Deep forest green
        '#4B2E84',  // Royal purple
        '#324165',  // Deep slate blue
        '#8B3A3A'   // Deep red
    ];

    (function applyRandomColorsAndShapes() {
        const circles = document.querySelectorAll('.random-color-circle');
    
        circles.forEach(circle => {
            const randomColor = getRandomColor();
            circle.style.borderColor = randomColor;
            circle.style.borderWidth = '2px';
            circle.style.borderStyle = 'solid';
            circle.style.borderRadius = '50%';
        });
    })();
    
    function getRandomColor() {
        const randomIndex = Math.floor(Math.random() * 6);
        return colors[randomIndex];
    }


    function applyRandomColorsAndShapes() {
        const circles = document.querySelectorAll('.random-bg-circle');
        circles.forEach(circle => {
        if (!circle.style.backgroundColor) {
            const randomColor = getRandomColorBg();
            circle.style.backgroundColor = randomColor;
        }
        });
    }
    
    function getRandomColorBg() {
        const randomIndex = Math.floor(Math.random() * 6);
        return colors[randomIndex];
    }
    
    applyRandomColorsAndShapes();
    
    function applyRandomBorderColor() {
        const circles = document.querySelectorAll('.random-border-circle');
    
        circles.forEach(circle => {
            const randomColor = getRandomColor();
            circle.style.borderColor = randomColor;
            circle.style.borderWidth = '2px';
            circle.style.borderStyle = 'solid';
            circle.style.borderRadius = '50%';
        });
    };
    
    applyRandomBorderColor();

    // Start Alpine
    // Alpine.start();
});