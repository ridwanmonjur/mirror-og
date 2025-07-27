class VanillaLightbox {
    constructor() {
        this.lightboxInstances = {};
        this.initializeLightbox();
    }

    initializeLightbox() {
        const elements = document.querySelectorAll('a[data-fslightbox]');
        
        elements.forEach((element) => {
            if (!element.hasAttribute('data-fslightbox')) return;

            let src = element.getAttribute('data-href');

            if (!src) {
                console.warn('The "data-fslightbox" attribute was set without the "href" attribute.');
                return;
            }

            const groupName = element.getAttribute('data-fslightbox');
            
            if (!this.lightboxInstances[groupName]) {
                this.lightboxInstances[groupName] = {
                    sources: [],
                    elements: []
                };
            }

            this.lightboxInstances[groupName].sources.push(src);
            this.lightboxInstances[groupName].elements.push(element);

            const sourceIndex = this.lightboxInstances[groupName].sources.length - 1;

            element.onclick = (e) => {
                e.preventDefault();
                this.open(groupName, sourceIndex);
            };
        });
    }

    createLightboxHTML(src) {
        
        const lightboxHTML = `
            <div class="modal fade" 
                 id="vanillaLightbox" 
                 tabindex="-1" 
                 aria-hidden="true"
                 style="
                    overflow: hidden; 
                    max-height: 100vh;
                ";

            >
                <div class="modal-dialog py-2 modal-dialog-centered modal-xl" aria-hidden="true"
                >
                    <div class="modal-content my-2 py-2" aria-hidden="true">
                        
                        <div class="modal-body text-center p-0 pt-3" aria-hidden="true">
                            <img src="${src}" 
                                 class="fslightbox-source" 
                                 style=" 
                                    max-width: 100%;
                                    object-fit: contain;
                                    max-height: 80vh;
                                 "
                                 aria-hidden="true"
                                 onerror="this.onerror=null;this.src='/assets/images/404q.png';"
                                 >
                        </div>
                        <div class="modal-footer border-0 text-center mt-1 mx-auto p-0" aria-hidden="true">
                            <button type="button" 
                            class="btn btn-primary text-white" 
                            data-bs-dismiss="modal" 
                            aria-hidden="true">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return lightboxHTML;
    }

    addCloseHandlers(modal) {
        document.getElementById('vanillaLightbox').addEventListener('click', (e) => {
            if (e.target.tagName !== 'IMG') {
                modal.hide();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                modal.hide();
            }
        });
    }

    open(groupName, sourceIndex) {
        const source = this.lightboxInstances[groupName].sources[sourceIndex];

        const existingLightbox = document.getElementById('vanillaLightbox');
        if (existingLightbox) {
            existingLightbox.remove();
        }

        const lightboxHTML = this.createLightboxHTML(source);
        document.body.insertAdjacentHTML('beforeend', lightboxHTML);

        const modal = new bootstrap.Modal(document.getElementById('vanillaLightbox'));
        modal.show();
        this.addCloseHandlers(modal);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.vanillaLightbox = new VanillaLightbox();
});