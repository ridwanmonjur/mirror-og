import tippy from 'tippy.js';

window.addPopover = function (parent, child, trigger="click") {
    if (!parent) {
        return;
    }
    console.log({parent: parent?._tippy});
    if (parent?._tippy) {
        console.log('Popover already exists for this element');
        parent._tippy.destroy();
    }
    console.log({parent: parent?._tippy});
    console.log("New Popover");
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