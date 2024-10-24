import { Events, FileUploadWithPreview } from 'file-upload-with-preview';

const fileUploadPreviewById = function(id) {
    return new FileUploadWithPreview(id);
}
window.fileUploadPreviewById = fileUploadPreviewById;
window.Events = Events;