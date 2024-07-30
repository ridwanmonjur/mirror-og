import ColorPickerUI from '@easylogic/colorpicker'

const createColorPicker = function (container, onLastUpdateCb = null, onHideCb = null) {
    var colorpicker = ColorPickerUI.create({
        color: 'blue',
        position: 'inline',   
        container,
        mode: 'edit',
        swatches: [],
        type : 'macos', 
        onHide: () => {
            console.log('hide');
            if (onHideCb) onHideCb();
        },
        onChange: (color) => {
            console.log('changed', color)
        },
        onLastUpdate: color => {
            console.log(color);
            if (onLastUpdateCb && localStorage.getItem('isInited')) { onLastUpdateCb(color); }
            localStorage.setItem('isInited', "true");
        }
    }) 

    return colorpicker;
}


const createGradientPicker = function (container, onLastUpdateCb, onHideCb = null) {
    var colorpicker = ColorPickerUI.createGradientPicker({
        position: 'inline',   
        mode: 'edit',
        container,
        type : 'macos',
        gradient: 'linear-gradient(90deg, red  0%,yellow  100%)',
        onHide: () => {
            console.log('hide');
            if (onHideCb) onHideCb();
        },
        onChange: (color) => {
            console.log('changed', color)
        },
        onLastUpdate: color => {
            if (onLastUpdateCb && localStorage.getItem('isInited')=== "true") { onLastUpdateCb(color); }
            localStorage.setItem('isInited', "true");
        }
    }) 

    return colorpicker;
}

window.createColorPicker = createColorPicker;
window.createGradientPicker = createGradientPicker;

// 1. revert
// 3. lightbox  
