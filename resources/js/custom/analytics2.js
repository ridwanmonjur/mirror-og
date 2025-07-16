window.dataLayer = window.dataLayer || [];

const ga4MeasurementId = import.meta.env.VITE_GOOGLE_ANALYTICS_MEASUREMENT_ID;

function loadGA4() {
    if (ga4MeasurementId && !window.gtag) {
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${ga4MeasurementId}`;
        document.head.appendChild(script);
        
        window.dataLayer = window.dataLayer || [];
        window.gtag = function() {
            window.dataLayer.push(arguments);
        };
        
        window.gtag('js', new Date());
        window.gtag('config', ga4MeasurementId, {
            // Modern approach - no custom_map needed
            send_page_view: true,
            // Optional: Configure enhanced measurement
            enhanced_measurement: {
                scrolls: true,
                clicks: true,
                file_downloads: true,
                page_changes: true
            }
        });
    }
}

function gtag() {
    if (typeof window.gtag === 'function') {
        window.gtag.apply(window, arguments);
    } else {
        console.warn('gtag not loaded, queuing event:', arguments);
        window.dataLayer.push(arguments);
    }
}

loadGA4();

class GA4Analytics {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.initializeGA4();
    }

    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    initializeGA4() {
        window.dataLayer.push({
            'event': 'analytics_initialized',
            'analytics_version': '3.0',
            'session_id': this.sessionId,
            'initialization_time': new Date().toISOString()
        });
    }

    getSessionId() {
        return this.sessionId;
    }
}

const ga4Analytics = new GA4Analytics();

// Modern event tracking functions
window.trackEventCardClick = function(element) {
    console.log("Event card click tracked");
    try {
        const eventData = {
            eventId: element.dataset.eventId,
            eventName: element.dataset.eventName,
            eventTier: element.dataset.eventTier,
            eventType: element.dataset.eventType,
            esportTitle: element.dataset.esportTitle,
            location: element.dataset.location,
            tierId: element.dataset.tierId,
            typeId: element.dataset.typeId,
            gameId: element.dataset.gameId,
            userId: element.dataset.userId
        };

        

        // Modern GA4 event with direct parameter names
        gtag('event', 'event_card_click', {
            event_category: 'event_interaction',
            event_label: eventData.eventName,
            // Direct parameter mapping - matches PHP service expectations
            event_id: eventData.eventId,
            event_name: eventData.eventName,
            event_tier: eventData.eventTier,
            event_type: eventData.eventType,
            esport_title: eventData.esportTitle,
            location: eventData.location,
            tier_id: eventData.tierId,
            type_id: eventData.typeId,
            game_id: eventData.gameId,
            user_id: eventData.userId
        });

        // Send additional contextual events
        if (eventData.eventTier) {
            gtag('event', 'tier_selection', {
                event_category: 'tier_interaction',
                event_label: eventData.eventTier,
                event_tier: eventData.eventTier
            });
        }

        if (eventData.eventType) {
            gtag('event', 'type_selection', {
                event_category: 'type_interaction',
                event_label: eventData.eventType
            });
        }

        if (eventData.esportTitle) {
            gtag('event', 'esport_selection', {
                event_category: 'esport_interaction',
                event_label: eventData.esportTitle
            });
        }

        if (eventData.location) {
            gtag('event', 'location_selection', {
                event_category: 'location_interaction',
                event_label: eventData.location
            });
        }

        console.log('Event card click tracked:', eventData);

    } catch (error) {
        console.error('Error tracking event card click:', error);
        
        window.dataLayer.push({
            event: 'tracking_error',
            error_message: error.message,
            error_type: 'event_card_click',
            timestamp: new Date().toISOString()
        });
    }
};

window.trackEventViewFromDiv = function(element) {
    console.log("Event view tracked");
    try {
        const eventData = {
            eventId: element.dataset.eventId,
            eventName: element.dataset.eventName,
            eventTier: element.dataset.eventTier,
            eventType: element.dataset.eventType,
            esportTitle: element.dataset.esportTitle,
            location: element.dataset.location,
            tierId: element.dataset.tierId,
            typeId: element.dataset.typeId,
            gameId: element.dataset.gameId,
            userId: element.dataset.userId
        };

       

        // Modern GA4 event - matches your PHP service expectations exactly
        gtag('event', 'page_view', {
            event_category: 'event_interaction',
            event_label: eventData.eventName,
            // These parameter names match your PHP service
            event_id: eventData.eventId,
            event_name: eventData.eventName,
            event_tier: eventData.eventTier,
            event_type: eventData.eventType,
            esport_title: eventData.esportTitle,
            location: eventData.location,
            tier_id: eventData.tierId,
            type_id: eventData.typeId,
            game_id: eventData.gameId,
            user_id: eventData.userId
        });

        // if (eventData.eventTier) {
        //     gtag('event', 'tier_view', {
        //         event_category: 'tier_interaction',
        //         event_label: eventData.eventTier,
        //         event_tier: eventData.eventTier,
        //         esport_title: eventData.esportTitle,
        //         location: eventData.location
        //     });
        // }

        // if (eventData.eventType) {
        //     gtag('event', 'type_selection', {
        //         event_category: 'type_interaction',
        //         event_label: eventData.eventType
        //     });
        // }

        // if (eventData.esportTitle) {
        //     gtag('event', 'esport_selection', {
        //         event_category: 'esport_interaction',
        //         event_label: eventData.esportTitle
        //     });
        // }

        // if (eventData.location) {
        //     gtag('event', 'location_selection', {
        //         event_category: 'location_interaction',
        //         event_label: eventData.location
        //     });
        // }

        cosnsole.log({eventData});

        console.log('Event view tracked:', eventData);

    } catch (error) {
        console.error('Error tracking event view:', error);
        
        window.dataLayer.push({
            event: 'tracking_error',
            error_message: error.message,
            error_type: 'event_view',
            timestamp: new Date().toISOString()
        });
    }
};

window.trackSocialInteraction = function(action, target, targetType = 'user') {

    
    gtag('event', 'social_interaction', {
        event_category: 'social',
        event_label: `${action}_${targetType}`,
        social_action: action,
        social_target: target,
        social_type: targetType
    });
};

// Form submission tracking
window.trackFormSubmission = function(formName, additionalParams = {}) {
    
    
    gtag('event', 'form_submit', {
        event_category: 'form',
        event_label: formName,
        form_name: formName,
        ...additionalParams
    });
};

// Utility functions
window.getSessionId = () => ga4Analytics.getSessionId();

// Page visibility tracking
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        window.dataLayer.push({
            event: 'page_hidden',
            timestamp: new Date().toISOString(),
            session_id: ga4Analytics.sessionId
        });
    } else {
        window.dataLayer.push({
            event: 'page_visible',
            timestamp: new Date().toISOString(),
            session_id: ga4Analytics.sessionId
        });
    }
});

// Enhanced error tracking
window.addEventListener('error', function(error) {
    window.dataLayer.push({
        event: 'javascript_error',
        error_message: error.message,
        error_filename: error.filename,
        error_lineno: error.lineno,
        error_colno: error.colno,
        timestamp: new Date().toISOString()
    });
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ga4Analytics };
}