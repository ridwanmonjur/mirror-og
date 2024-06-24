import { openDefaultEditor } from '@pqina/pintura/pintura.js';

function setupFileInputEditor(selector, cb) {
    const fileInput = document.querySelector(selector);
  
    if (!fileInput) {
      console.error(`No element found for selector: ${selector}`);
      return;
    }
  
    fileInput.addEventListener('change', () => {
      if (!fileInput.files.length) return;
  
      const editor = openDefaultEditor({
        imageCropAspectRatio: 4 / 1,
        src: fileInput.files[0],
        cropEnableInfoIndicator: true,
      });
  
      editor.on('process', (imageState) => {
        console.log({ imageState });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(imageState.dest);
  
        fileInput.files = dataTransfer.files;
        cb(fileInput.files[0]);
      });
    });
}

window.setupFileInputEditor = setupFileInputEditor;
