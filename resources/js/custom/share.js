function initSocialShareModals() {
    const baseUrl = window.location.origin;

    const shareButtons = document.querySelectorAll('.share-button');

    shareButtons.forEach(button => {
        button.addEventListener('click', () => {
            const eventId = button.dataset.eventId;

            const shareUrl = `${baseUrl}/event/${eventId}`;

            navigator.clipboard.writeText(shareUrl)
                .then(() => {
                    window.Swal.fire({
                        icon: 'success',
                        text: "Successfully copied to clipboard!",
                        html: `<div>Successfully copied to clipboard!</div>
                               <div style="background-color: black; color: white; padding: 10px; margin-top: 10px; border-radius: 4px; word-break: break-all;">${shareUrl}</div>`,
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

        })
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSocialShareModals();
});