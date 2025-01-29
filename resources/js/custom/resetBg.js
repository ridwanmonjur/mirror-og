
function resetBg(initialData) {
    console.log({initialData});
    if (!initialData) initialData = {
        fontColor: null,
        frameColor: null,
        backgroundBanner: null,
        backgroundColor: '#fffdfb',
        backgroundGradient: null,
    };

    let newFontColor = initialData.fontColor ?? 'black';
    let newFrameColor = initialData.frameColor ?? 'green';

    localStorage.removeItem('colorOrGradient');

    document.querySelector("input[name='backgroundColor']").value = initialData.backgroundColor || '';
    document.querySelector("input[name='backgroundGradient']").value = initialData.backgroundGradient || '';
    document.querySelector("input[name='fontColor']").value = newFontColor;
    document.querySelector("input[name='frameColor']").value = newFrameColor;
    document.getElementById('changeBackgroundBanner').value = '';

    const backgroundBanner = document.getElementById('backgroundBanner');

    if (initialData.backgroundBanner) {
        backgroundBanner.style.backgroundImage = `url('/storage/${initialData.backgroundBanner}')`;
        backgroundBanner.style.background = 'auto';
    } else if (initialData.backgroundColor) {
        backgroundBanner.style.backgroundImage = 'none';
        backgroundBanner.style.background = initialData.backgroundColor;
    } else if (initialData.backgroundGradient) {
        backgroundBanner.style.backgroundImage = initialData.backgroundGradient;
        backgroundBanner.style.background = 'auto';
    } else {
        backgroundBanner.style.backgroundImage = 'none';
        backgroundBanner.style.background = '#fffdfb';
    }

    document.querySelectorAll(".cursive-font").forEach((cursiveElement) => {
        if (initialData.backgroundGradient) {
            cursiveElement.style.backgroundImage = initialData.backgroundGradient;
            cursiveElement.style.background = 'auto';
        } else if (initialData.backgroundColor) {
            cursiveElement.style.backgroundImage = 'none';
            cursiveElement.style.background = initialData.backgroundColor;
        } else {
            cursiveElement.style.backgroundImage = 'none';
            cursiveElement.style.background = 'transparent';
        }
    });
    
    

    // newFontColor
    document.querySelectorAll("[data-font-color]").forEach((element) => {
        element.style.color = newFontColor;
    });

    document.querySelectorAll(".form-color").forEach((element) => {
        element.style.color = newFontColor;
    });

    backgroundBanner.style.color = newFontColor;

    backgroundBanner.querySelectorAll('.form-control').forEach((element) => {
        element.style.color = newFontColor;
    });

    backgroundBanner.querySelectorAll('.followCounts').forEach((element) => {
        element.style.color = newFontColor;
    })

    // newFrameColor
    document.querySelectorAll("[data-frame-color]").forEach((element) => {
        element.style.borderColor = newFrameColor;
    });

    document.querySelectorAll('.uploaded-image').forEach((element)=> {
        element.style.borderColor = newFrameColor;
    });
       
}

function initOffCanvasListeners() {

    const myOffcanvas = document.getElementById('profileDrawer');
    
    myOffcanvas.addEventListener('hide.bs.offcanvas', () => {
        let modalBackdrop =  document.querySelector('.modal-backdrop');
        modalBackdrop.style.opacity = '0.5';
    });
    myOffcanvas.addEventListener('shown.bs.offcanvas', () => {
        let modalBackdrop =  document.querySelector('.modal-backdrop');
        modalBackdrop.style.opacity = '0';
    });

    const cropperModal = document.getElementById('cropperModal');
    cropperModal.addEventListener('hide.bs.modal', () => {
        let modalBackdrop =  document.querySelector('.modal-backdrop');
        modalBackdrop.style.opacity = '0';
    });
    cropperModal.addEventListener('shown.bs.modal', () => {
        let modalBackdrop =  document.querySelector('.modal-backdrop');
        modalBackdrop.style.opacity = '0.5';
    });
    
}


export {
    resetBg,
    initOffCanvasListeners
};