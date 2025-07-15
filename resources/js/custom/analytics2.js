// Enhanced GTM Click Tracking - Simplified without localStorage counts
// This implementation uses GTM's data layer and gtag properly

// Initialize data layer if it doesn't exist
window.dataLayer = window.dataLayer || [];

// Get GTM ID from environment
const gtmId = import.meta.env.VITE_GOOGLE_TAG_MANAGER_ID;

// Load Google Tag Manager script
function loadGTM() {
    if (gtmId && !window.gtag) {
        // Load GTM script
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${gtmId}`;
        document.head.appendChild(script);
        
        // Initialize gtag
        window.dataLayer = window.dataLayer || [];
        window.gtag = function() {
            window.dataLayer.push(arguments);
        };
        
        // Configure GTM
        window.gtag('js', new Date());
        window.gtag('config', gtmId);
    }
}

// Enhanced gtag function with error handling
function gtag() {
    if (typeof window.gtag === 'function') {
        window.gtag.apply(window, arguments);
    } else {
        console.warn('gtag not loaded, queuing event:', arguments);
        window.dataLayer.push(arguments);
    }
}

// Load GTM on initialization
loadGTM();

// Simplified GTM Analytics class
class GTMAnalytics {
    constructor() {
        this.sessionId = this.generateSessionId();
        this.initializeGTM();
    }

    // Generate unique session ID
    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    // Initialize GTM configuration
    initializeGTM() {
        // Push initial configuration to data layer
        window.dataLayer.push({
            'event': 'analytics_initialized',
            'analytics_version': '2.0',
            'session_id': this.sessionId,
            'initialization_time': new Date().toISOString()
        });

        // Get measurement ID from environment
        const measurementId = import.meta.env.VITE_GOOGLE_ANALYTICS_MEASUREMENT_ID;
        
        // Set up custom dimensions if needed
        gtag('config', measurementId, {
            'custom_map': {
                'custom_dimension_1': 'event_tier',
                'custom_dimension_2': 'event_type',
                'custom_dimension_3': 'esport_title',
                'custom_dimension_4': 'location'
            }
        });
    }

    // Get session ID
    getSessionId() {
        return this.sessionId;
    }
}

// Initialize analytics
const gtmAnalytics = new GTMAnalytics();

// Enhanced event tracking function
window.trackEventCardClick = function(element) {
    try {
        // Extract event data from element
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

        // Prepare enhanced event data for GTM
        const enhancedEventData = {
            event: 'event_card_click',
            event_category: 'event_interaction',
            event_action: 'click',
            event_label: eventData.eventName || 'unknown_event',
            
            // Custom event parameters
            event_id: eventData.eventId,
            event_name: eventData.eventName,
            event_tier: eventData.eventTier,
            event_type: eventData.eventType,
            esport_title: eventData.esportTitle,
            location: eventData.location,
            tier_id: eventData.tierId,
            type_id: eventData.typeId,
            game_id: eventData.gameId,
            user_id: eventData.userId,
            
            // Session data
            session_id: gtmAnalytics.sessionId,
            timestamp: new Date().toISOString(),
            
            // Additional context
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer
        };

        // Push to data layer for GTM
        window.dataLayer.push(enhancedEventData);

        // Also send via gtag for immediate tracking
        gtag('event', 'event_card_click', {
            event_category: 'event_interaction',
            event_label: eventData.eventName,
            custom_parameter_1: eventData.eventTier,
            custom_parameter_2: eventData.eventType,
            custom_parameter_3: eventData.esportTitle,
            custom_parameter_4: eventData.location
        });

        // Track specific metrics
        if (eventData.eventTier) {
            gtag('event', 'tier_selection', {
                event_category: 'tier_interaction',
                event_label: eventData.eventTier
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

        // Log for debugging
        console.log('Event tracked:', enhancedEventData);

    } catch (error) {
        console.error('Error tracking event:', error);
        
        // Send error event to GTM
        window.dataLayer.push({
            event: 'tracking_error',
            error_message: error.message,
            error_stack: error.stack,
            timestamp: new Date().toISOString()
        });
    }
};

// Additional tracking functions
window.trackSocialInteraction = function(action, target, targetType = 'user') {
    const eventData = {
        event: 'social_interaction',
        event_category: 'social',
        event_action: action,
        event_label: `${action}_${targetType}`,
        social_action: action,
        social_target: target,
        social_type: targetType,
        timestamp: new Date().toISOString()
    };

    window.dataLayer.push(eventData);
    
    gtag('event', 'social_interaction', {
        event_category: 'social',
        event_label: `${action}_${targetType}`,
        social_action: action,
        social_target: target,
        social_type: targetType
    });
};

window.trackFormSubmission = function(formName, additionalParams = {}) {
    const eventData = {
        event: 'form_submit',
        event_category: 'form',
        event_action: 'submit',
        event_label: formName,
        form_name: formName,
        timestamp: new Date().toISOString(),
        ...additionalParams
    };

    window.dataLayer.push(eventData);
    
    gtag('event', 'form_submit', {
        event_category: 'form',
        event_label: formName,
        form_name: formName,
        ...additionalParams
    });
};

// New utility functions for GA4 data access
window.getEventCounts = () => {
    console.log('Event counts are stored in GA4. Use GA4 reporting API or interface to access aggregated data.');
    return null;
};

window.resetEventCounts = () => {
    console.log('Event counts are managed by GA4. Cannot reset from client-side.');
    return null;
};

window.getTierCount = (tier) => {
    console.log(`Tier counts for '${tier}' are stored in GA4. Use GA4 reporting to access this data.`);
    return null;
};

window.getTypeCount = (type) => {
    console.log(`Type counts for '${type}' are stored in GA4. Use GA4 reporting to access this data.`);
    return null;
};

window.getEsportCount = (esport) => {
    console.log(`Esport counts for '${esport}' are stored in GA4. Use GA4 reporting to access this data.`);
    return null;
};

window.getLocationCount = (location) => {
    console.log(`Location counts for '${location}' are stored in GA4. Use GA4 reporting to access this data.`);
    return null;
};

// New function to get session ID
window.getSessionId = () => gtmAnalytics.getSessionId();

// Page visibility tracking for better analytics
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        window.dataLayer.push({
            event: 'page_hidden',
            timestamp: new Date().toISOString(),
            session_id: gtmAnalytics.sessionId
        });
    } else {
        window.dataLayer.push({
            event: 'page_visible',
            timestamp: new Date().toISOString(),
            session_id: gtmAnalytics.sessionId
        });
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { gtmAnalytics };
}