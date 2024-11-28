import tippy from 'tippy.js';
import { DateTime } from "luxon";

window.addPopover = function (parent, child, trigger="click") {
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
            theme: 'light',
            zIndex: 9999,
            appendTo: document.body
        });
    }

    
   
}

window.formatDateLuxon = (date) => {
    if (!date) return 'N/A';
    return  DateTime
        .fromISO(date)
        .toRelative();
}
