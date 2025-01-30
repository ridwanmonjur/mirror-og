import tippy from 'tippy.js';
import { DateTime } from "luxon";

window.addPopover = function (parent, child, trigger="click", options = {}) {
    if (!parent) {
        return;
    }
    
    if (parent?._tippy) {
        parent._tippy.destroy();
    }

    if (child && child.innerHTML)  {
        tippy(parent, {
            content: child.innerHTML,
            allowHTML: true,
            placement: 'top',
            trigger,
            triggerTarget: parent,
            // hideOnClick: false,
            // trigger: 'click',
            interactive: true,
            hideOnClick: false,
            delay: [50, 0],
            theme: 'light',
            zIndex: 9999,
            appendTo: document.body,
            ...options,

            
        });
    }

    
   
}

window.formatDateLuxon = (date) => {
    if (!date) return 'Not available';
    return  DateTime
        .fromISO(date)
        .toRelative();
}
