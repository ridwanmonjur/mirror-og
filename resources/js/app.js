
import Swal from 'sweetalert2'
import * as Popper from '@popperjs/core'
window.Popper = Popper
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap;
import { createApp } from 'petite-vue';
import { PageNotificationComponent } from './custom/notifications';
import './custom/analytics2.js';
import './custom/emoji.js';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    width: 'fit-content',
    padding: '0.7rem',
    showConfirmButton: false,
    timer: 6000,
    timerProgressBar: true
})

window.showLoading = ({ title = 'Loading...', html = `
  <div class="d-flex justify-content-center">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
  <div class="mt-2">Hang on, please</div>
`, backdrop = true } = {}) => {
  return window.Swal.fire({
    title,
    html,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    backdrop: backdrop ? 'rgba(0,0,0,0.7)' : 'transparent',
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

window.dialogOpen = (title, resultConfirmedCb, resultDeniedCb, options = null) => {
    const config = {
        icon: "warning",
        title: title,
        showDenyButton: true,
        showCancelButton: false,
        confirmButtonText: 'Yes',
        denyButtonText: 'No',
        confirmButtonColor: "#43A4D7",
    };
    
    if (options && typeof options === 'object') {
        if (options.image) {
            config.imageUrl = options.image;
            delete config.icon;
        }
        
        if (options.svgIcon && !options.image) {
            config.iconHtml = options.svgIcon;
            delete config.icon;
        }
        
        if (options.innerHTML) {
            config.html = options.innerHTML;
        }
        
        if (options.icon && !options.image && !options.svgIcon) {
            config.icon = options.icon;
        }
        
        if (options.footer) {
            config.footer = options.footer;
        }
        
        Object.keys(options).forEach(key => {
            if (!['image', 'innerHTML', 'icon', 'footer', 'svgIcon'].includes(key)) {
                config[key] = options[key];
            }
        });
    }
    
    Swal.fire(config).then((result) => {
        if (result.isConfirmed) {
            resultConfirmedCb()
        } else if (result.isDenied) {
            if (resultDeniedCb) { resultDeniedCb() }
        }
    })
}

// Usage examples:

// 1. Original usage (unchanged - maintains backward compatibility)
// dialogOpen("Are you sure?", confirmCallback, denyCallback);

// 2. With custom image
// dialogOpen("Delete item?", confirmCallback, denyCallback, {
//     image: "https://example.com/warning.png"
// });

// 3. With custom SVG icon
// dialogOpen("Custom warning", confirmCallback, denyCallback, {
//     svgIcon: `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#ff6b6b" viewBox="0 0 16 16">
//                 <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
//               </svg>`
// });

// 4. With custom HTML content
// dialogOpen("Confirm action", confirmCallback, denyCallback, {
//     innerHTML: "<strong>This action cannot be undone!</strong><br><em>Please confirm.</em>"
// });

// 5. With custom icon
// dialogOpen("Success!", confirmCallback, denyCallback, {
//     icon: "success"
// });

// 6. With footer
// dialogOpen("Are you sure?", confirmCallback, denyCallback, {
//     footer: "<small>This action will affect all users</small>"
// });

// 7. Combined options with SVG
// dialogOpen("Delete user account?", confirmCallback, denyCallback, {
//     svgIcon: `<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#dc3545" viewBox="0 0 16 16">
//                 <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5Z"/>
//               </svg>`,
//     innerHTML: "<strong>Warning!</strong><br>This will permanently delete the user account and all associated data.",
//     footer: "<small>This action cannot be undone</small>"
// });

// 8. With additional SweetAlert2 options
// dialogOpen("Confirm?", confirmCallback, denyCallback, {
//     confirmButtonText: "Delete",
//     denyButtonText: "Keep",
//     confirmButtonColor: "#d33",
//     width: 600
// });

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

// Google Analytics initialized via import above
// Maintain existing event tracking functionality using new analytics service

// Analytics functions are now imported from analytics2.js
// No need to redefine - they're already available as global functions



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

    const scrollIndicatorEl = document.querySelector('.scroll-indicator');
    if (scrollIndicatorEl) {
        try {
            const { default: ScrollProgressIndicator } = await import('scroll-progress-indicator');
            ScrollProgressIndicator.init({
                color: '#1fa5ed',
                height: '4px',
                position: 'top'
            });
        } catch (error) {
            console.error('Failed to load scroll-progress-indicator:', error);
        }
    }

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