// Filament Analytics API - GA4 Integration for admin panel

// Initialize data layer if it doesn't exist
window.dataLayer = window.dataLayer || [];

// Get GTM ID from environment
const gtmId = import.meta.env.VITE_GOOGLE_TAG_MANAGER_ID;
const measurementId = import.meta.env.VITE_GOOGLE_ANALYTICS_MEASUREMENT_ID;

// Load Google Tag Manager script
function loadGTM() {
    console.log('🚀 Loading GTM with ID:', gtmId);
    console.log('🎯 GA4 Measurement ID:', measurementId);
    
    if (gtmId && !window.gtag) {
        // Load GTM script
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${gtmId}`;
        document.head.appendChild(script);
        
        console.log('📡 GTM script loaded from:', script.src);
        
        // Initialize gtag
        window.dataLayer = window.dataLayer || [];
        window.gtag = function() {
            window.dataLayer.push(arguments);
        };
        
        // Configure GTM
        window.gtag('js', new Date());
        window.gtag('config', gtmId);
        
        // Also configure GA4 measurement ID
        window.gtag('config', measurementId, {
            'custom_map': {
                'custom_dimension_1': 'event_tier',
                'custom_dimension_2': 'event_type',
                'custom_dimension_3': 'esport_title',
                'custom_dimension_4': 'location'
            }
        });
        
        console.log('✅ GTM and GA4 configured successfully');
    } else {
        console.warn('⚠️ GTM already loaded or GTM ID missing');
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

class FilamentAnalyticsAPI {
    constructor() {
        this.baseUrl = window.location.origin;
        this.isInitialized = false;
        this.propertyId = measurementId.replace('G-', ''); // Extract property ID from measurement ID
        this.gtmId = gtmId;
        this.measurementId = measurementId;
        this.init();
    }

    init() {
        // console.log('🔧 Initializing Filament Analytics API...');
        // console.log('🆔 Property ID:', this.propertyId);
        // console.log('🏷️ GTM ID:', this.gtmId);
        // console.log('📊 Measurement ID:', this.measurementId);
        
        // Check if we have GTM dataLayer and gtag available
        const hasDataLayer = typeof window.dataLayer !== 'undefined';
        const hasGtag = typeof window.gtag !== 'undefined';
        
        if (hasDataLayer && hasGtag) {
            this.isInitialized = true;
            console.log('✅ Filament Analytics API initialized');
        } else if (hasDataLayer) {
            this.isInitialized = true;
            console.log('✅ Filament Analytics API initialized');
        } else {
            this.isInitialized = false;
        }
    }


    getAnalyticsFromDataLayer() {
        
        if (typeof window.dataLayer === 'undefined') {
            console.warn('⚠️ DataLayer not available');
            return null;
        }

        const analyticsEvents = window.dataLayer.filter(item => 
            item.event && 
            !item.event.startsWith('gtm.') && 
            item.event !== 'analytics_initialized' &&
            item.event !== 'page_hidden' &&
            item.event !== 'page_visible'
        );

        return analyticsEvents;
    }

    processGA4Response(data) {
        if (!data || !data.rows) {
            return [];
        }

        return data.rows.map(row => {
            const result = {};
            
            if (data.dimensionHeaders && row.dimensionValues) {
                data.dimensionHeaders.forEach((header, index) => {
                    result[header.name] = row.dimensionValues[index]?.value || '';
                });
            }

            if (data.metricHeaders && row.metricValues) {
                data.metricHeaders.forEach((header, index) => {
                    result[header.name] = parseInt(row.metricValues[index]?.value || '0');
                });
            }

            return result;
        });
    }

    async fetchAnalyticsData() {
        
        try {
            const analyticsData = {
                pageViews: this.getPageViewsDataFromDataLayer(),
                eventInteractions: this.getEventInteractionsDataFromDataLayer(),
                socialInteractions: this.getSocialInteractionsDataFromDataLayer(),
                formSubmissions: this.getFormSubmissionsDataFromDataLayer(),
                topEvents: this.getTopEventsDataFromDataLayer(),
                userEngagement: this.getUserEngagementDataFromDataLayer(),
                realTimeData: this.getRealTimeDataFromDataLayer()
            };
            
            return analyticsData;
        } catch (error) {
            console.error('❌ Failed to process analytics data:', error);
            return this.getDefaultAnalyticsData();
        }
    }

    // Get page views data from dataLayer
    getPageViewsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            console.log({events});
            console.log({events});
            console.log({events});
            console.log({events});
            console.log({events});
            if (!events) return this.getDefaultPageViewsData();

            // Count actual event_card_click events (real user interactions)
            const eventClicks = events.filter(event => event.event === 'event_card_click');
            const total = eventClicks.length;
            
            // Simple counts - no fake estimates
            const today = 0; // Would need timestamp analysis
            const yesterday = 0; // Would need timestamp analysis

            // Get actual pages where events occurred
            const pageCounts = {};
            eventClicks.forEach(event => {
                const path = event.page_url || 'unknown';
                pageCounts[path] = (pageCounts[path] || 0) + 1;
            });

            const topPages = Object.entries(pageCounts)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 10)
                .map(([path, views]) => ({ path, views }));

            return {
                total,
                today,
                yesterday,
                weekly_change: 0,
                monthly_change: 0,
                top_pages: topPages
            };
        } catch (error) {
            console.error('Failed to get page views data from dataLayer:', error);
            return this.getDefaultPageViewsData();
        }
    }

    // Default page views data
    getDefaultPageViewsData() {
        return {
            total: 0,
            today: 0,
            yesterday: 0,
            weekly_change: 0,
            monthly_change: 0,
            top_pages: []
        };
    }

    // Get event interactions data from dataLayer
    getEventInteractionsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultEventInteractionsData();

            // Count actual event card clicks
            const eventClickEvents = events.filter(event => event.event === 'event_card_click');
            const totalClicks = eventClickEvents.length;

            // Count actual form submissions
            const registrationEvents = events.filter(event => event.event === 'form_submit');
            const registrations = registrationEvents.length;

            // Calculate actual conversion rate
            const conversionRate = totalClicks > 0 ? (registrations / totalClicks * 100).toFixed(1) : 0;

            // Get actual event counts by name
            const eventCounts = {};
            eventClickEvents.forEach(event => {
                const eventName = event.event_name || 'Unknown Event';
                eventCounts[eventName] = (eventCounts[eventName] || 0) + 1;
            });

            const topEvents = Object.entries(eventCounts)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 10)
                .map(([name, clicks]) => ({
                    name,
                    clicks,
                    registrations: 0, // Would need to track actual registrations per event
                    tier: eventClickEvents.find(e => e.event_name === name)?.event_tier || '',
                    type: eventClickEvents.find(e => e.event_name === name)?.event_type || '',
                    esport: eventClickEvents.find(e => e.event_name === name)?.esport_title || ''
                }));

            return {
                total_clicks: totalClicks,
                registrations,
                conversion_rate: parseFloat(conversionRate),
                top_events: topEvents
            };
        } catch (error) {
            console.error('Failed to get event interactions data from dataLayer:', error);
            return this.getDefaultEventInteractionsData();
        }
    }

    // Default event interactions data
    getDefaultEventInteractionsData() {
        return {
            total_clicks: 0,
            registrations: 0,
            conversion_rate: 0,
            top_events: []
        };
    }

    // Get social interactions data from dataLayer
    getSocialInteractionsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultSocialInteractionsData();

            // Count actual social interactions
            const socialEvents = events.filter(event => event.event === 'social_interaction');
            
            let totalFollows = 0;
            let totalLikes = 0;
            let shares = 0;

            socialEvents.forEach(event => {
                const action = event.social_action || event.event_label;
                if (action === 'follow') totalFollows++;
                if (action === 'like') totalLikes++;
                if (action === 'share') shares++;
            });

            const followRate = totalFollows > 0 ? (totalFollows / (totalFollows + totalLikes) * 100).toFixed(1) : 0;
            const engagementRate = 0; // Would need proper engagement metrics

            return {
                total_follows: totalFollows,
                total_likes: totalLikes,
                shares,
                follow_rate: followRate,
                engagement_rate: engagementRate,
                top_followed_users: []
            };
        } catch (error) {
            console.error('Failed to get social interactions data from dataLayer:', error);
            return this.getDefaultSocialInteractionsData();
        }
    }

    // Default social interactions data
    getDefaultSocialInteractionsData() {
        return {
            total_follows: 0,
            total_likes: 0,
            shares: 0,
            follow_rate: 0,
            engagement_rate: 0,
            top_followed_users: []
        };
    }

    // Get form submissions data from dataLayer
    getFormSubmissionsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultFormSubmissionsData();

            // Count actual form submissions
            const formEvents = events.filter(event => event.event === 'form_submit');
            const totalSubmissions = formEvents.length;
            const successfulSubmissions = 0; // Would need success/failure tracking

            // Count actual form types
            const formTypes = {};
            formEvents.forEach(event => {
                const formName = event.form_name || event.event_label || 'unknown';
                formTypes[formName] = (formTypes[formName] || 0) + 1;
            });

            const successRate = 0; // Would need proper success tracking

            return {
                total_submissions: totalSubmissions,
                successful_submissions: successfulSubmissions,
                success_rate: successRate,
                form_types: formTypes
            };
        } catch (error) {
            console.error('Failed to get form submissions data from dataLayer:', error);
            return this.getDefaultFormSubmissionsData();
        }
    }

    // Default form submissions data
    getDefaultFormSubmissionsData() {
        return {
            total_submissions: 0,
            successful_submissions: 0,
            success_rate: 0,
            form_types: {}
        };
    }

    // Get top events data from dataLayer
    getTopEventsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return [];

            // Count actual event_card_click events by their event_name
            const eventCounts = {};
            const eventClickEvents = events.filter(event => event.event === 'event_card_click');
            
            eventClickEvents.forEach(event => {
                const eventName = event.event_name || 'Unknown Event';
                if (eventName !== 'unknown_event') {
                    eventCounts[eventName] = (eventCounts[eventName] || 0) + 1;
                }
            });

            const topEvents = Object.entries(eventCounts)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 10)
                .map(([name, participants]) => {
                    const sampleEvent = eventClickEvents.find(e => e.event_name === name);
                    return {
                        name,
                        participants,
                        revenue: 0, // Would need actual revenue tracking
                        tier: sampleEvent?.event_tier || '',
                        type: sampleEvent?.event_type || '',
                        esport: sampleEvent?.esport_title || '',
                        location: sampleEvent?.location || ''
                    };
                });

            return topEvents;
        } catch (error) {
            console.error('Failed to get top events data from dataLayer:', error);
            return [];
        }
    }

    // Get user engagement data from dataLayer
    getUserEngagementDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultUserEngagementData();

            // Count actual unique users from user_id in events
            const userIds = new Set();
            const eventClickEvents = events.filter(event => event.event === 'event_card_click');
            
            eventClickEvents.forEach(event => {
                if (event.user_id) {
                    userIds.add(event.user_id);
                }
            });

            const totalEvents = events.length;
            const activeUsers = userIds.size || 0;
            const sessionDuration = 0; // Would need actual session tracking
            const bounceRate = 0; // Would need actual bounce tracking
            const pagesPerSession = 0; // Would need actual page tracking
            const newUsers = 0; // Would need new/returning user tracking
            const returningUsers = 0; // Would need new/returning user tracking

            return {
                active_users: activeUsers,
                session_duration: sessionDuration,
                bounce_rate: bounceRate,
                pages_per_session: pagesPerSession,
                new_users: newUsers,
                returning_users: returningUsers
            };
        } catch (error) {
            console.error('Failed to get user engagement data from dataLayer:', error);
            return this.getDefaultUserEngagementData();
        }
    }

    // Default user engagement data
    getDefaultUserEngagementData() {
        return {
            active_users: 0,
            session_duration: 0,
            bounce_rate: 0,
            pages_per_session: 0,
            new_users: 0,
            returning_users: 0
        };
    }

    // Get real-time data from dataLayer
    getRealTimeDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultRealTimeData();

            // Get recent events (last 10)
            const recentEvents = events.slice(-10);
            
            // Count actual unique users from recent events
            const recentUserIds = new Set();
            recentEvents.forEach(event => {
                if (event.user_id) {
                    recentUserIds.add(event.user_id);
                }
            });
            
            const activeUsersNow = recentUserIds.size || 0;
            const currentPageViews = recentEvents.filter(event => event.event === 'event_card_click').length;

            // Get actual pages from page_url in events
            const pageCounts = {};
            recentEvents.forEach(event => {
                const path = event.page_url || 'unknown';
                pageCounts[path] = (pageCounts[path] || 0) + 1;
            });

            const topActivePages = Object.entries(pageCounts)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 5)
                .map(([path, active_users]) => ({ path, active_users }));

            // Format recent events
            const formattedRecentEvents = recentEvents.map(event => ({
                event: event.event,
                timestamp: event.timestamp || new Date().toISOString(),
                category: event.event_category || 'event_interaction',
                label: event.event_label || event.event_name || '',
                event_name: event.event_name || '',
                tier: event.event_tier || '',
                type: event.event_type || '',
                esport: event.esport_title || ''
            }));

            return {
                active_users_now: activeUsersNow,
                current_page_views: currentPageViews,
                top_active_pages: topActivePages,
                recent_events: formattedRecentEvents
            };
        } catch (error) {
            console.error('Failed to get real-time data from dataLayer:', error);
            return this.getDefaultRealTimeData();
        }
    }

    // Default real-time data
    getDefaultRealTimeData() {
        return {
            active_users_now: 0,
            current_page_views: 0,
            top_active_pages: [],
            recent_events: []
        };
    }

    // Get default analytics data structure
    getDefaultAnalyticsData() {
        return {
            pageViews: this.getDefaultPageViewsData(),
            eventInteractions: this.getDefaultEventInteractionsData(),
            socialInteractions: this.getDefaultSocialInteractionsData(),
            formSubmissions: this.getDefaultFormSubmissionsData(),
            topEvents: [],
            userEngagement: this.getDefaultUserEngagementData(),
            realTimeData: this.getDefaultRealTimeData()
        };
    }

    

    // Refresh analytics data
    async refreshData() {
        return await this.fetchAnalyticsData();
    }

    // Check if analytics service is available
    isAnalyticsAvailable() {
        return this.isInitialized && (
            typeof window.dataLayer !== 'undefined' || 
            typeof window.analytics !== 'undefined'
        );
    }

    // Get connection status
    getConnectionStatus() {
        return {
            analytics_service: this.isAnalyticsAvailable(),
            gtm_available: typeof window.dataLayer !== 'undefined',
            gtag_available: typeof window.gtag !== 'undefined',
            property_id: this.propertyId,
            gtm_id: this.gtmId,
            measurement_id: this.measurementId
        };
    }
}

// Create and initialize the Filament Analytics API
const filamentAnalytics = new FilamentAnalyticsAPI();

window.filamentAnalytics = filamentAnalytics;

window.initializeFilamentAnalytics = function() {
    if (window.filamentAnalytics) {
    }
};

export default filamentAnalytics;