window.dataLayer = window.dataLayer || [];

const measurementId = import.meta.env.VITE_GOOGLE_ANALYTICS_MEASUREMENT_ID;

function loadGA4() {
    if (measurementId && !window.gtag) {
        const script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`;
        document.head.appendChild(script);
        
        window.dataLayer = window.dataLayer || [];
        window.gtag = function() {
            window.dataLayer.push(arguments);
        };
        
        window.gtag('js', new Date());
        window.gtag('config', measurementId, {
            'custom_map': {
                'custom_dimension_1': 'event_tier',
                'custom_dimension_2': 'event_type',
                'custom_dimension_3': 'esport_title',
                'custom_dimension_4': 'location'
            }
        });
    }
}

function gtag() {
    if (typeof window.gtag === 'function') {
        window.gtag.apply(window, arguments);
    } else {
        window.dataLayer.push(arguments);
    }
}

loadGA4();

class FilamentAnalyticsAPI {
    constructor() {
        this.baseUrl = window.location.origin;
        this.isInitialized = false;
        this.measurementId = measurementId;
        this.init();
    }

    init() {
        const hasDataLayer = typeof window.dataLayer !== 'undefined';
        const hasGtag = typeof window.gtag !== 'undefined';
        
        if (hasDataLayer && hasGtag) {
            this.isInitialized = true;
        } else if (hasDataLayer) {
            this.isInitialized = true;
        } else {
            this.isInitialized = false;
        }
    }

    getAnalyticsFromDataLayer() {
        if (typeof window.dataLayer === 'undefined') {
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

    getPageViewsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultPageViewsData();

            const eventClicks = events.filter(event => event.event === 'event_card_click');
            const total = eventClicks.length;
            
            const today = 0;
            const yesterday = 0;

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

    getEventInteractionsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultEventInteractionsData();

            const eventClickEvents = events.filter(event => event.event === 'event_card_click');
            const totalClicks = eventClickEvents.length;

            const registrationEvents = events.filter(event => event.event === 'form_submit');
            const registrations = registrationEvents.length;

            const conversionRate = totalClicks > 0 ? (registrations / totalClicks * 100).toFixed(1) : 0;

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
                    registrations: 0,
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

    getDefaultEventInteractionsData() {
        return {
            total_clicks: 0,
            registrations: 0,
            conversion_rate: 0,
            top_events: []
        };
    }

    getSocialInteractionsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultSocialInteractionsData();

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
            const engagementRate = 0;

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

    getFormSubmissionsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultFormSubmissionsData();

            const formEvents = events.filter(event => event.event === 'form_submit');
            const totalSubmissions = formEvents.length;
            const successfulSubmissions = 0;

            const formTypes = {};
            formEvents.forEach(event => {
                const formName = event.form_name || event.event_label || 'unknown';
                formTypes[formName] = (formTypes[formName] || 0) + 1;
            });

            const successRate = 0;

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

    getDefaultFormSubmissionsData() {
        return {
            total_submissions: 0,
            successful_submissions: 0,
            success_rate: 0,
            form_types: {}
        };
    }

    getTopEventsDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return [];

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
                        revenue: 0,
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

    getUserEngagementDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultUserEngagementData();

            const userIds = new Set();
            const eventClickEvents = events.filter(event => event.event === 'event_card_click');
            
            eventClickEvents.forEach(event => {
                if (event.user_id) {
                    userIds.add(event.user_id);
                }
            });

            const totalEvents = events.length;
            const activeUsers = userIds.size || 0;
            const sessionDuration = 0;
            const bounceRate = 0;
            const pagesPerSession = 0;
            const newUsers = 0;
            const returningUsers = 0;

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

    getRealTimeDataFromDataLayer() {
        try {
            const events = this.getAnalyticsFromDataLayer();
            if (!events) return this.getDefaultRealTimeData();

            const recentEvents = events.slice(-10);
            
            const recentUserIds = new Set();
            recentEvents.forEach(event => {
                if (event.user_id) {
                    recentUserIds.add(event.user_id);
                }
            });
            
            const activeUsersNow = recentUserIds.size || 0;
            const currentPageViews = recentEvents.filter(event => event.event === 'event_card_click').length;

            const pageCounts = {};
            recentEvents.forEach(event => {
                const path = event.page_url || 'unknown';
                pageCounts[path] = (pageCounts[path] || 0) + 1;
            });

            const topActivePages = Object.entries(pageCounts)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 5)
                .map(([path, active_users]) => ({ path, active_users }));

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

    getDefaultRealTimeData() {
        return {
            active_users_now: 0,
            current_page_views: 0,
            top_active_pages: [],
            recent_events: []
        };
    }

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

    async refreshData() {
        return await this.fetchAnalyticsData();
    }

    isAnalyticsAvailable() {
        return this.isInitialized && (
            typeof window.dataLayer !== 'undefined' || 
            typeof window.analytics !== 'undefined'
        );
    }

    getConnectionStatus() {
        return {
            analytics_service: this.isAnalyticsAvailable(),
            dataLayer_available: typeof window.dataLayer !== 'undefined',
            gtag_available: typeof window.gtag !== 'undefined',
            measurement_id: this.measurementId
        };
    }
}

const filamentAnalytics = new FilamentAnalyticsAPI();

window.filamentAnalytics = filamentAnalytics;

window.initializeFilamentAnalytics = function() {
    if (window.filamentAnalytics) {
    }
};

export default filamentAnalytics;