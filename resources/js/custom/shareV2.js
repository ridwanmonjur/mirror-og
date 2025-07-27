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
    
        const shareUrl = `${baseUrl}/eventv2/${eventId}`;
        
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