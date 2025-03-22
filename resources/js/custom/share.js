
function initSocialShareModals() {
    const baseUrl = window.location.origin;
    
    const icons = {
        facebook: `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="currentColor" >
            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>`,
        twitter: `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="currentColor" >
            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723 9.99 9.99 0 01-3.127 1.195 4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
        </svg>`,
        discord: `<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="currentColor" >
            <path d="M20.317 4.3698a19.7913 19.7913 0 00-4.8851-1.5152.0741.0741 0 00-.0785.0371c-.211.3753-.4447.8648-.6083 1.2495-1.8447-.2762-3.68-.2762-5.4868 0-.1636-.3933-.4058-.8742-.6177-1.2495a.077.077 0 00-.0785-.037 19.7363 19.7363 0 00-4.8852 1.515.0699.0699 0 00-.0321.0277C.5334 9.0458-.319 13.5799.0992 18.0578a.0824.0824 0 00.0312.0561c2.0528 1.5076 4.0413 2.4228 5.9929 3.0294a.0777.0777 0 00.0842-.0276c.4616-.6304.8731-1.2952 1.226-1.9942a.076.076 0 00-.0416-.1057c-.6528-.2476-1.2743-.5495-1.8722-.8923a.077.077 0 01-.0076-.1277c.1258-.0943.2517-.1923.3718-.2914a.0743.0743 0 01.0776-.0105c3.9278 1.7933 8.18 1.7933 12.0614 0a.0739.0739 0 01.0785.0095c.1202.099.246.1981.3728.2924a.077.077 0 01-.0066.1276 12.2986 12.2986 0 01-1.873.8914.0766.0766 0 00-.0407.1067c.3604.698.7719 1.3628 1.225 1.9932a.076.076 0 00.0842.0286c1.961-.6067 3.9495-1.5219 6.0023-3.0294a.077.077 0 00.0313-.0552c.5004-5.177-.8382-9.6739-3.5485-13.6604a.061.061 0 00-.0312-.0286zM8.02 15.3312c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9555-2.4189 2.157-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.9555 2.4189-2.1569 2.4189zm7.9748 0c-1.1825 0-2.1569-1.0857-2.1569-2.419 0-1.3332.9554-2.4189 2.1569-2.4189 1.2108 0 2.1757 1.0952 2.1568 2.419 0 1.3332-.946 2.4189-2.1568 2.4189Z"/>
        </svg>`
    };
    
    if (!document.getElementById('socialShareModal')) {
        const modalHtml = `
            <div class="modal fade" id="socialShareModal" tabindex="-1" aria-labelledby="socialShareModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content px-3 py-3">
                        <h5 class="" id="socialShareModalLabel">Share this event</h5>
                        <div class="modal-body py-3">
                            <div class="d-flex justify-content-center gap-3">
                                <button class="btn btn-outline-primary share-facebook d-flex text-center justify-content-center align-items-center">
                                    ${icons.facebook}
                                </button>
                                <button class="btn btn-outline-info share-twitter text-center d-flex justify-content-center align-items-center">
                                    ${icons.twitter}
                                </button>
                                <button class="btn btn-outline-secondary share-discord text-center d-flex justify-content-center align-items-center">
                                    ${icons.discord}
                                </button>
                            </div>
                        </div>
                        <div class="mx-auto text-center">
                            <button type="button" class="btn btn-primary rounded-pill text-white px-3 py-2 " data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const modalContainer = document.createElement('div');
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer.firstElementChild);
    }
    
    const modalElement = document.getElementById('socialShareModal');
    const modal = new bootstrap.Modal(modalElement);
    
    const copyToClipboard = (url) => {
        navigator.clipboard.writeText(url)
            .then(() => {
                const notification = modalElement.querySelector('.copy-notification');
                notification.style.display = 'block';
                
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 2000);
            })
            .catch(err => {
                console.error('Failed to copy URL: ', err);
            });
    };
    
    const shareButtons = document.querySelectorAll('.share-button');
    
    shareButtons.forEach(button => {
        button.addEventListener('click', () => {
            const eventId = button.dataset.eventId;
            
            const shareUrl = `${baseUrl}/event/${eventId}`;
            
            const facebookBtn = modalElement.querySelector('.share-facebook');
            const twitterBtn = modalElement.querySelector('.share-twitter');
            const discordBtn = modalElement.querySelector('.share-discord');
            
            facebookBtn.dataset.url = shareUrl;
            twitterBtn.dataset.url = shareUrl;
            discordBtn.dataset.url = shareUrl;
            
            modal.show();
        });
    });
    
    modalElement.querySelector('.share-facebook').addEventListener('click', (e) => {
        copyToClipboard(e.target.closest('.share-facebook').dataset.url);
    });
    
    modalElement.querySelector('.share-twitter').addEventListener('click', (e) => {
        copyToClipboard(e.target.closest('.share-twitter').dataset.url);
    });
    
    modalElement.querySelector('.share-discord').addEventListener('click', (e) => {
        copyToClipboard(e.target.closest('.share-discord').dataset.url);
    });
}

document.addEventListener('DOMContentLoaded', () => {
        initSocialShareModals();
});