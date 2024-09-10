import tippy from 'tippy.js';

window.addPopover = function (parent, child, trigger="click") {
    tippy(parent, {
        content: child.innerHTML,
        allowHTML: true,
        placement: 'top',
        trigger,
        triggerTarget: parent,
        interactive: true,
        theme: 'light',
        zIndex: 9999,
    });
}