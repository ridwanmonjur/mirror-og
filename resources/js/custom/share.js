
function initSocialShareModals() {
    const baseUrl = window.location.origin;

    const shareButtons = document.querySelectorAll('.share-button');

    function handleShareClick() {
        const eventId = this.dataset.eventId;
        const eventName = this.dataset.eventName;
        const eventTier = this.dataset.eventTier;
        const eventType = this.dataset.eventType;
        const esportTitle = this.dataset.esportTitle;
        const location = this.dataset.location;
        const tierId = this.dataset.tierId;
        const typeId = this.dataset.typeId;
        const gameId = this.dataset.gameId;
        const userId = this.dataset.userId;
    
        const shareUrl = `${baseUrl}/event/${eventId}`;
        
        // Track share event with analytics service
        if (window.analytics || window.trackEvent) {
            const shareData = {
                event_category: 'share_interaction',
                event_label: 'event_share',
            };
            
            if (eventId) shareData.event_id = eventId;
            if (eventName) shareData.event_name = eventName;
            if (tierId) shareData.tier_id = tierId;
            if (typeId) shareData.type_id = typeId;
            if (gameId) shareData.game_id = gameId;
            if (userId) shareData.user_id = userId;
            if (eventTier) shareData.event_tier = eventTier;
            if (eventType) shareData.event_type = eventType;
            if (esportTitle) shareData.esport_title = esportTitle;
            if (location) shareData.location = location;
            
            // Use analytics service if available, fallback to trackEvent
            if (window.analytics) {
                window.analytics.trackEvent('event_share', shareData);
            } else if (window.trackEvent) {
                window.trackEvent('event_share', shareData);
            }
        }
    
        navigator.clipboard.writeText(shareUrl)
            .then(() => {
                window.Swal.fire({
                    icon: 'success',
                    title: '<span style="font-size: 1.4rem; font-weight: 600; color: #2d3748;">Link Copied Successfully!</span>',
                    html: `
                        <div style="margin-top: 1.25rem;">
                            <p style="font-size: 0.95rem; color: #718096; margin-bottom: 1.25rem; font-weight: 400; line-height: 1.5;">
                                Share this link with others to invite them to the event
                            </p>
                            <div class="bg-primary text-white" style="
                                border-radius: 10px;
                                padding: 1rem 1.25rem;
                                word-break: break-all;
                                font-family: 'Courier New', Consolas, monospace;
                                font-size: 0.875rem;
                                box-shadow: 0 4px 6px rgba(67, 164, 215, 0.15);
                                transition: all 0.2s ease;
                                line-height: 1.6;
                            ">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="flex-shrink: 0; opacity: 0.9;">
                                        <path d="M13.5 1a1.5 1.5 0 0 1 1.5 1.5v11a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 13.5v-11A1.5 1.5 0 0 1 2.5 1h11zm-11-1A2.5 2.5 0 0 0 0 2.5v11A2.5 2.5 0 0 0 2.5 16h11a2.5 2.5 0 0 0 2.5-2.5v-11A2.5 2.5 0 0 0 13.5 0h-11z"/>
                                        <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
                                    </svg>
                                    <span style="flex: 1; font-weight: 500;">${shareUrl}</span>
                                </div>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#43A4D7',
                    confirmButtonText: 'Done',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-button'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    }
                });
            })
            .catch(__ => {
                window.Swal.fire({
                    icon: 'error',
                    title: '<span style="font-size: 1.4rem; font-weight: 600; color: #2d3748;">Unable to Copy Link</span>',
                    html: '<p style="font-size: 0.95rem; color: #718096; margin-top: 0.75rem; line-height: 1.6;">There was a problem copying the link to your clipboard. Please try again or copy it manually.</p>',
                    confirmButtonColor: '#43A4D7',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'swal-custom-popup',
                        confirmButton: 'swal-custom-button'
                    }
                });
            });
    }
    
    shareButtons.forEach(button => {
        const eventId = button.dataset.eventId;
        const eventName = button.dataset.eventName;
        const eventTier = button.dataset.eventTier;
        const eventType = button.dataset.eventType;
        const esportTitle = button.dataset.esportTitle;
        const location = button.dataset.location;
        const tierId = button.dataset.tierId;
        const typeId = button.dataset.typeId;
        const gameId = button.dataset.gameId;
        const userId = button.dataset.userId;

        console.log({
            eventId,
            eventName,
            eventTier,
            eventType,
            esportTitle,
            location,
            tierId,
            typeId,
            gameId,
            userId
        });

        button.removeEventListener('click', handleShareClick);
        button.addEventListener('click', handleShareClick);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initSocialShareModals();
});

window.initSocialShareModals = initSocialShareModals;