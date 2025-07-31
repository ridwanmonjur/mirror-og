export default function UploadData (type, fileStore) {
  return {
    get inputFiles() {
      return fileStore.getFiles(type)
    },

    handleFiles(event) {
      if (!event.target?.files) return;

      const newFiles = Array.from(event.target?.files);
      const validFiles = [];
      
      for (const file of newFiles) {
        if (!(file.type.startsWith('image/') || file.type.startsWith('video/'))) {
          window.toastError("Only images and videos are supported");
          continue;
        }
        validFiles.push(file);
      }

      if (validFiles.length === 0) return;

      const maxSizeInMB = 20;
      const maxSizeInBytes = maxSizeInMB * 1024 * 1024;
      const oversizedFiles = [];
      
      for (const file of validFiles) {
        if (file.size > maxSizeInBytes) {
          oversizedFiles.push({
            name: file.name,
            size: (file.size / (1024 * 1024)).toFixed(2) + ' MB'
          });
        }
      }
      
      if (oversizedFiles.length > 0) {
        const fileList = oversizedFiles.map(f => `${f.name} (${f.size})`).join(', ');
        window.toastError(`The following files are too large (max ${maxSizeInMB}MB): ${fileList}`);
        return;
      }

      fileStore.addFiles(validFiles, type);
      
      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);

      uploadArea.innerHTML = "";

      this.inputFiles.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
          this.createImgPreview(file, index);
        } else {
          this.createVideoPreview(file, index);
        }
      });
      event.target.value = '';
    },
    
    clickInput() {
      const fileInput = document.querySelector(`#${type}Id .file-input`);
      fileInput?.click()
    },
    
    createVideoPreview(file, index) {
      const preview = document.createElement('div');
      preview.className = 'preview-item me-2';

      // Create video icon SVG
      const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      svg.setAttribute("viewBox", "0 0 24 24");
      svg.setAttribute("width", "64");
      svg.setAttribute("height", "64");

      const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
      path.setAttribute("d", "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z");
      path.setAttribute("fill", "#666666");
      svg.appendChild(path);

      const deleteBtn = document.createElement('button');
      deleteBtn.innerHTML = '×';
      deleteBtn.className = 'delete-btn';
      deleteBtn.addEventListener('click', () => {
        preview.remove();
        fileStore.clearFilesByIndex(type, index);
      });

      const fileName = document.createElement('small');
      fileName.textContent = file.name;

      preview.appendChild(svg);
      preview.appendChild(deleteBtn);
      preview.appendChild(fileName);

      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);
      const plusButton = uploadArea.querySelector('.plus-button');
      uploadArea.insertBefore(preview, plusButton);
    },
    createImgPreview(file, index) {
      const preview = document.createElement('div');
      preview.className = 'preview-item loading me-2';

      const img = document.createElement('img');
      img.addEventListener('load', () => {
        preview.classList.remove('loading');
      });

      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'delete-btn';
      deleteBtn.innerHTML = '×';
      deleteBtn.addEventListener('click', () => {
        preview.remove();
        fileStore.clearFilesByIndex(type, index);
      });

      const reader = new FileReader();
      reader.onload = (e) => {
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);

      const fileName = document.createElement('small');
      fileName.textContent = file.name;

      preview.appendChild(img);
      preview.appendChild(deleteBtn);
      preview.appendChild(fileName);

      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);
      const plusButton = uploadArea.querySelector('.plus-button');
      uploadArea.insertBefore(preview, plusButton);
    },

    getImages() {
      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);

      return Array.from(uploadArea.querySelectorAll('.preview-item img'))
        .map(img => img.src);
    },

    clearAllValues() {
      fileStore.clearAllFiles(type);
      
      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);
      if (uploadArea) {
        const previewItems = uploadArea.querySelectorAll('.preview-item');
        previewItems.forEach(item => item.remove());
      }
      
      const fileInput = document.querySelector(`#${type}Id .file-input`);
      if (fileInput) {
        fileInput.value = '';
      }
    },

    init2() {
      window.addEventListener('clearUploadData', () => {
        this.clearAllValues();
      });
    }
  };
}