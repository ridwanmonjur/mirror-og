<div class="modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="vertical-align: middle !important;">
        <div class="modal-content" style="background-color: transparent;">
           
            <div class="modal-body mx-auto">
                <div class="cropper-container">
                    <div class="mb-3">
                        <label for="imageInput" class="form-label">Choose an image</label>
                    </div>
                    
                    <div class="preview-container" >
                        <img id="imagePreview" class="img-fluid mx-auto">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-secondary me-3 rounded-pill px-3 py-2" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary rounded-pill px-3 py-2" id="cropButton" >Crop Image</button>
            </div>
        </div>
    </div>
</div>