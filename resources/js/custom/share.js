
function initSocialShareModals() {
    const baseUrl = window.location.origin;

    const shareButtons = document.querySelectorAll('.share-button');

    function handleShareClick() {
        const eventId = this.dataset.eventId;
    
        const shareUrl = `${baseUrl}/event/${eventId}`;
    
        navigator.clipboard.writeText(shareUrl)
            .then(() => {
                window.Swal.fire({
                    icon: 'success',
                    text: "Successfully copied to clipboard!",
                    html: `<div>Successfully copied to clipboard!</div>
                    <div class="bg-secondary text-light rounded-3 px-3 py-2 mt-2" style="word-break: break-all;">${shareUrl}</div>`,
                    confirmButtonColor: '#43A4D7'
                });
            })
            .catch(__ => {
                window.Swal.fire({
                    icon: 'error',
                    text: "Failed to copy to clipboard!",
                    confirmButtonColor: '#43A4D7'
                });
            });
    }
    
    shareButtons.forEach(button => {
        const eventId = button.dataset.eventId;

        console.log({eventId})

        button.removeEventListener('click', handleShareClick);
        button.addEventListener('click', handleShareClick);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSocialShareModals();
});

window.initSocialShareModals = initSocialShareModals;