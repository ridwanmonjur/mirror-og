// Google Analytics/Google Tag Manager tracking service
class AnalyticsService {
    constructor() {
        this.isInitialized = false;
        this.trackingId = null;
        this.useGTM = false;
        this.init();
    }

    init() {
        // Check if Google Tag Manager is already loaded (via Laravel GTM package)
        if (typeof window.dataLayer !== 'undefined' && window.dataLayer.length > 0) {
            this.useGTM = true;
            this.isInitialized = true;
            console.log('Google Tag Manager detected - using GTM for analytics');
            return;
        }

        // Fallback to direct Google Analytics if GTM is not available
        const trackingMeta = document.querySelector('meta[name="google-analytics-tracking-id"]');
        this.trackingId = trackingMeta?.getAttribute('content') || window.GA_TRACKING_ID;
        
        if (!this.trackingId) {
            // Initialize dataLayer for potential future use
            window.dataLayer = window.dataLayer || [];
            console.warn('Google Analytics tracking ID not found. Analytics service initialized without tracking.');
            this.isInitialized = true;
            return;
        }

        // Load Google Analytics script
        this.loadGoogleAnalytics();
    }

    loadGoogleAnalytics() {
        // Create and load the gtag script
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${this.trackingId}`;
        
        script.onload = () => {
            // Initialize gtag
            window.dataLayer = window.dataLayer || [];
            window.gtag = function() {
                dataLayer.push(arguments);
            };
            
            // Configure Google Analytics
            gtag('js', new Date());
            gtag('config', this.trackingId, {
                page_title: document.title,
                page_location: window.location.href,
                custom_map: {
                    'custom_dimension_1': 'user_id',
                    'custom_dimension_2': 'event_id',
                    'custom_dimension_3': 'tier_id',
                    'custom_dimension_4': 'type_id',
                    'custom_dimension_5': 'game_id'
                }
            });
            
            this.isInitialized = true;
            console.log('Google Analytics initialized successfully');
        };

        script.onerror = () => {
            console.error('Failed to load Google Analytics script');
            // Still initialize dataLayer for potential future use
            window.dataLayer = window.dataLayer || [];
            this.isInitialized = true;
        };

        document.head.appendChild(script);
    }

    // Track custom events
    trackEvent(eventName, parameters = {}) {
        if (!this.isInitialized) {
            console.warn('Analytics not initialized. Event not tracked:', eventName);
            return;
        }

        // Ensure dataLayer is available
        if (typeof window.dataLayer === 'undefined') {
            console.warn('dataLayer not available. Event not tracked:', eventName);
            return;
        }

        // Push event to dataLayer (works for both GTM and direct gtag)
        window.dataLayer.push({
            'event': eventName,
            ...parameters
        });

        // Also use gtag if available for direct GA tracking
        if (typeof gtag === 'function') {
            gtag('event', eventName, parameters);
        }
    }

    // Track social interactions
    trackSocialInteraction(action, target, targetType = 'user') {
        this.trackEvent('social_interaction', {
            event_category: 'social',
            event_label: `${action}_${targetType}`,
            social_action: action,
            social_target: target,
            social_type: targetType
        });
    }

    // Track follow interactions specifically
    trackFollowInteraction(action, targetUserId, targetUsername, followerUserId = null) {
        if (!this.isInitialized) {
            console.warn('Analytics not initialized. Follow interaction not tracked');
            return;
        }

        const eventData = {
            event_category: 'social',
            event_label: `${action}_user`,
            social_action: action,
            social_target: targetUserId,
            social_type: 'user',
            target_user_id: targetUserId,
            target_username: targetUsername
        };

        if (followerUserId) {
            eventData.follower_user_id = followerUserId;
        }

        this.trackEvent('follow_interaction', eventData);

        // Also track using the generic social interaction method
        this.trackSocialInteraction(action, targetUserId, 'user');
    }

    // Track form submissions
    trackFormSubmission(formName, additionalParams = {}) {
        this.trackEvent('form_submit', {
            event_category: 'form',
            event_label: formName,
            form_name: formName,
            ...additionalParams
        });
    }

    // Track page views (useful for SPA navigation)
    trackPageView(pageTitle = null, pagePath = null) {
        if (!this.isInitialized) {
            console.warn('Analytics not initialized. Page view not tracked');
            return;
        }

        const config = {};
        if (pageTitle) config.page_title = pageTitle;
        if (pagePath) config.page_location = pagePath;

        if (typeof gtag === 'function') {
            gtag('config', this.trackingId, config);
        }
    }

    // Track page views with event data
    trackPageViewWithEventData(pageTitle, pagePath, eventData) {
        if (!this.isInitialized) {
            console.warn('Analytics not initialized. Page view with event data not tracked');
            return;
        }

        // Extract event attributes from dataset
        const eventAttributes = this.extractEventAttributes(eventData);
        
        // Track the page view
        this.trackEvent('page_view', {
            event_category: 'page_view',
            event_label: pageTitle,
            page_title: pageTitle,
            page_location: pagePath,
            ...eventAttributes
        });

        // Also track as event view if we have event data
        if (eventAttributes.event_id) {
            this.trackEvent('event_view', {
                event_category: 'event_interaction',
                event_label: eventAttributes.event_name || 'Unknown Event',
                ...eventAttributes
            });
        }
    }

    // Track user engagement
    trackUserEngagement(engagementType, value = 1) {
        this.trackEvent('user_engagement', {
            event_category: 'engagement',
            event_label: engagementType,
            value: value
        });
    }

    // Track conversion events
    trackConversion(conversionType, value = null, currency = 'USD') {
        const eventData = {
            event_category: 'conversion',
            event_label: conversionType
        };

        if (value !== null) {
            eventData.value = value;
            eventData.currency = currency;
        }

        this.trackEvent('conversion', eventData);
    }

    // Track event registration
    trackEventRegistration(eventId, eventName, entryFee, eventData) {
        if (!this.isInitialized) {
            console.warn('Analytics not initialized. Event registration not tracked');
            return;
        }

        // Extract event attributes from dataset
        const eventAttributes = this.extractEventAttributes(eventData);
        
        // Track the registration event
        this.trackEvent('event_registration', {
            event_category: 'event_interaction',
            event_label: eventName,
            event_id: eventId,
            event_name: eventName,
            entry_fee: entryFee,
            value: entryFee,
            currency: 'USD',
            ...eventAttributes
        });

        // Also track as a conversion
        this.trackConversion('event_registration', entryFee, 'USD');
    }

    // Track user identification (for logged-in users)
    setUserId(userId) {
        if (!this.isInitialized || typeof gtag !== 'function') {
            console.warn('Analytics not initialized. User ID not set');
            return;
        }

        gtag('config', this.trackingId, {
            user_id: userId
        });
    }

    // Track custom dimensions
    setCustomDimension(index, value) {
        if (!this.isInitialized || typeof gtag !== 'function') {
            console.warn('Analytics not initialized. Custom dimension not set');
            return;
        }

        gtag('config', this.trackingId, {
            [`custom_dimension_${index}`]: value
        });
    }

    // Check if analytics is ready
    isReady() {
        return this.isInitialized && typeof gtag === 'function';
    }

    // Helper method to extract event attributes from dataset
    extractEventAttributes(eventData) {
        if (!eventData) return {};

        const attributes = {};
        
        // Map dataset attributes to analytics parameters
        const attributeMap = {
            eventId: 'event_id',
            eventName: 'event_name',
            entryFee: 'entry_fee',
            eventTier: 'event_tier',
            eventType: 'event_type',
            esportTitle: 'esport_title',
            location: 'location',
            tierId: 'tier_id',
            typeId: 'type_id',
            gameId: 'game_id',
            userId: 'user_id'
        };

        // Extract attributes from dataset
        Object.keys(attributeMap).forEach(key => {
            if (eventData[key] !== undefined && eventData[key] !== null && eventData[key] !== '') {
                attributes[attributeMap[key]] = eventData[key];
            }
        });

        // Convert numeric values
        const numericFields = ['event_id', 'entry_fee', 'tier_id', 'type_id', 'game_id', 'user_id'];
        numericFields.forEach(field => {
            if (attributes[field] !== undefined) {
                const numValue = parseInt(attributes[field]);
                if (!isNaN(numValue)) {
                    attributes[field] = numValue;
                }
            }
        });

        return attributes;
    }
}

// Create and initialize the analytics service
const analyticsService = new AnalyticsService();

// Attach to window for global access
window.analytics = analyticsService;

// Initialize analytics function to be called from templates
window.initializeAnalytics = function() {
    if (window.analytics) {
        const analyticsData = document.getElementById('analytics-data');
        
        if (analyticsData) {
            const eventId = analyticsData.dataset.eventId;
            const eventName = analyticsData.dataset.eventName;
            const entryFee = analyticsData.dataset.entryFee;
            
            // Track page view with all available event data
            window.analytics.trackPageViewWithEventData(
                'Event View - ' + eventName,
                window.location.href,
                analyticsData.dataset
            );
            
            // Track event registration when form is submitted
            const joinForm = document.querySelector('form[name="joinForm"]');
            if (joinForm) {
                joinForm.addEventListener('submit', function(e) {
                    window.analytics.trackEventRegistration(
                        parseInt(eventId),
                        eventName,
                        parseInt(entryFee || 0),
                        analyticsData.dataset
                    );
                });
            }
        }
    }
};

// Enhanced social interaction tracking with event context
window.trackSocialInteractionWithContext = function(action, target, targetType = 'user', eventContext = null) {
    if (window.analytics) {
        // Track the basic social interaction
        window.analytics.trackSocialInteraction(action, target, targetType);
        
        // If event context is provided, track with additional context
        if (eventContext) {
            const eventData = {
                event_category: 'social_with_context',
                event_label: `${action}_${targetType}_with_context`,
                social_action: action,
                social_target: target,
                social_type: targetType,
                ...window.analytics.extractEventAttributes(eventContext)
            };
            
            window.analytics.trackEvent('social_interaction_with_context', eventData);
        }
    }
};

// Enhanced follow interaction tracking
window.trackFollowInteractionWithContext = function(action, targetUserId, targetUsername, followerUserId = null, eventContext = null) {
    if (window.analytics) {
        // Track the basic follow interaction
        window.analytics.trackFollowInteraction(action, targetUserId, targetUsername, followerUserId);
        
        // If event context is provided, track with additional context
        if (eventContext) {
            const eventData = {
                event_category: 'social_follow_with_context',
                event_label: `${action}_user_with_context`,
                social_action: action,
                social_target: targetUserId,
                social_type: 'user',
                target_user_id: targetUserId,
                target_username: targetUsername,
                ...window.analytics.extractEventAttributes(eventContext)
            };
            
            if (followerUserId) {
                eventData.follower_user_id = followerUserId;
            }
            
            window.analytics.trackEvent('follow_interaction_with_context', eventData);
        }
    }
};

// Export for module usage
export default analyticsService;