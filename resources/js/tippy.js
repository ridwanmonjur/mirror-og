import tippy from 'tippy.js';
window.addPopover = function (parent, child) {
    tippy(parent, {
        content: child.innerHTML,
        allowHTML: true,
        placement: 'top',
        trigger: 'click',
        interactive: true,
        theme: 'light',
    });
}