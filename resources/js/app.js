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

 // Added: Popper.js dependency for popover support in Bootstrap
import '@popperjs/core';
