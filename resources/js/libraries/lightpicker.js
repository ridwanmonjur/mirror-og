import flatpickr from "flatpickr";
import Litepicker from "litepicker"
window.createLitepicker = (options) => {
    return new Litepicker(options);
}

window.createFlatpickr = (selector, options) => {
    return new flatpickr(selector, options)
}