 // Default Laravel bootstrapper, installs axios
 import './bootstrap';

 // Added: Actual Bootstrap JavaScript dependency
 import * as Popper from '@popperjs/core'
 window.Popper = Popper
 
 import * as bootstrap from 'bootstrap'
 window.bootstrap = Popper
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

 // Added: Popper.js dependency for popover support in Bootstrap
import '@popperjs/core';
