import Cropper from 'cropperjs';

const imagePreview = document.getElementById('imagePreview');
const cropButton = document.getElementById('cropButton');
const modalElement = document.getElementById('cropperModal');

let listenerAdded = false;

let cropper = null;

function setupFileInputEditor(selector, cb) {
  const fileInput = document.querySelector(selector);

  if (!fileInput) {
    console.error(`No element found for selector: ${selector}`);
    return;
  }

  fileInput.addEventListener('input', () => {
    if (!fileInput.files.length) return;
    let file = fileInput.files[0];
    const reader = new FileReader();
    reader.onload = function (e) {

      if (modalElement) {
        bootstrap.Modal.getOrCreateInstance(modalElement)?.show();
      }

      imagePreview.src = e.target.result;

      if (cropper) {
        cropper.destroy();
      }

      cropper = new Cropper(imagePreview, {
        aspectRatio: 4 / 1,
        viewMode: 1,
        dragMode: 'move',
        restore: false,
        guides: true,
        center: true,
        highlight: true,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: true,
        minContainerWidth: 800,
        minContainerHeight: 500,
 
      });
    };
    reader.readAsDataURL(file);

    if (listenerAdded) return;

    cropButton.addEventListener('click', function () {
      if (!cropper) return;

      const canvas = cropper.getCroppedCanvas();
   

      canvas.toBlob(function (blob) {
        const croppedFile = new File([blob], file.name, {
          type: 'image/png',
          lastModified: new Date().getTime()
        });
  
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(croppedFile);
        
        fileInput.files = dataTransfer.files;
        cb(blob);

       

        bootstrap.Modal.getOrCreateInstance(modalElement)?.hide();
      }, 'image/png');

    });
    listenerAdded = true;
  });
}


window.setupFileInputEditor = setupFileInputEditor;