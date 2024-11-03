import { openDefaultEditor } from '@pqina/pintura/pintura.js';
import Cropper from 'cropperjs';

// Create necessary HTML elements
const container = document.createElement('div');
container.className = 'cropper-container';
container.style.maxWidth = '500px';
container.style.margin = '20px auto';

const fileInput = document.createElement('input');
fileInput.type = 'file';
fileInput.accept = 'image/*';
fileInput.className = 'file-input';

const previewContainer = document.createElement('div');
previewContainer.style.marginTop = '20px';
previewContainer.style.height = '400px';
previewContainer.style.display = 'none';

const imagePreview = document.createElement('img');
imagePreview.style.maxWidth = '100%';
imagePreview.style.display = 'block';

const cropButton = document.createElement('button');
cropButton.textContent = 'Crop Image';
cropButton.style.marginTop = '10px';
cropButton.style.display = 'none';

// Add elements to container
container.appendChild(fileInput);
previewContainer.appendChild(imagePreview);
container.appendChild(previewContainer);
container.appendChild(cropButton);
document.body.appendChild(container);

let cropper = null;

function setupFileInputEditor(selector, cb) {
  const fileInput = document.querySelector(selector);

  if (!fileInput) {
    console.error(`No element found for selector: ${selector}`);
    return;
  }

  fileInput.addEventListener('change', () => {
    if (!fileInput.files.length) return;
    let file = fileInput.files[0];
    console.log({file})
    console.log({file})
    console.log({file})
    const reader = new FileReader();
    reader.onload = function(e) {
      console.log({file})
      console.log({file})
      console.log({file})
  
      imagePreview.src = e.target.result;
        previewContainer.style.display = 'block';
        cropButton.style.display = 'block';
        
        // Destroy existing cropper if it exists
        if (cropper) {
            cropper.destroy();
        }
        
        // Initialize Cropper.js
        cropper = new Cropper(imagePreview, {
            aspectRatio: 4/1, // 1:1 ratio
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.8,
            restore: false,
            guides: true,
            center: true,
            highlight: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            toggleDragModeOnDblclick: true,
        });
    };
    reader.readAsDataURL(file);
  });
}

cropButton.addEventListener('click', function() {
  if (!cropper) return;
  
  // Get cropped canvas
  const canvas = cropper.getCroppedCanvas({
      width: 300,  // Output width
      height: 300, // Output height
  });
  
  // Convert to blob
  canvas.toBlob(function(blob) {
      // Create download link
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'cropped-image.png';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
  }, 'image/png');
});



window.setupFileInputEditor = setupFileInputEditor;