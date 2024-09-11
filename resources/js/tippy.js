import tippy from 'tippy.js';

window.addPopover = function (parent, child, trigger="click") {
    if (parent._tippy) {
        console.log('Popover already exists for this element');
        return;
    }

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