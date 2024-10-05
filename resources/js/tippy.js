import tippy from 'tippy.js';

window.addPopover = function (parent, child, trigger="click") {
    if (!parent) {
        return;
    }
    
    if (parent?._tippy) {
        parent._tippy.destroy();
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