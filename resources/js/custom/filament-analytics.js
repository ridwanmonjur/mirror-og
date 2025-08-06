import { initializeApp } from "firebase/app";
import { initializeFirestore, persistentLocalCache, persistentMultipleTabManager, doc, getDoc, setDoc, serverTimestamp, query, collection, where, orderBy, limit, getDocs } from "firebase/firestore";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_MESSAGE_SENDER_ID,
    appId: import.meta.env.VITE_APP_ID,
};

const app = initializeApp(firebaseConfig);

const db = initializeFirestore(app, {
    localCache: persistentLocalCache({
        tabManager: persistentMultipleTabManager()
    })
});


class FilamentAnalyticsAPI {
    constructor() {
        this.baseUrl = window.location.origin;
        this.isInitialized = true;
        this.currentTimeFilter = 'all';
    }



    async fetchAnalyticsData(page = 1, limit = 10, timeFilter = null) {
        try {
            if (timeFilter !== null) {
                this.currentTimeFilter = timeFilter;
            }
            
            // Use time-filtered data if available, otherwise fall back to basic method
            let clickCounts, viewCounts, socialCounts, formJoins;
            
            if (window.getTimeFilteredAnalyticsCounts && this.currentTimeFilter !== 'all') {
                const filteredData = await window.getTimeFilteredAnalyticsCounts(this.currentTimeFilter);
                clickCounts = {
                    ...filteredData.clickCounts,
                    lastUpdated: new Date().toISOString()
                };
                viewCounts = {
                    ...filteredData.viewCounts,
                    lastUpdated: new Date().toISOString()
                };
                socialCounts = {
                    ...filteredData.socialCounts,
                    lastUpdated: new Date().toISOString()
                };
                formJoins = {
                    ...filteredData.formCounts,
                    lastUpdated: new Date().toISOString()
                };
            } else {
                [clickCounts, viewCounts, socialCounts, formJoins] = await Promise.all([
                    this.getStatsDocument('clickCounts'),
                    this.getStatsDocument('viewCounts'),
                    this.getStatsDocument('socialCounts'),
                    this.getStatsDocument('formJoins')
                ]);
            }
            
            const analyticsData = {
                pageViews: this.processPageViewsData(viewCounts),
                eventInteractions: this.processEventInteractionsData(clickCounts, page, limit),
                socialInteractions: this.processSocialInteractionsData(socialCounts),
                formSubmissions: this.processFormSubmissionsData(formJoins),
                topEvents: this.processTopEventsData(clickCounts, page, limit),
                userEngagement: this.processUserEngagementData(clickCounts, viewCounts),
                realTimeData: this.processRealTimeData(viewCounts),
                clickCounts: clickCounts,
                viewCounts: viewCounts
            };
            
            return analyticsData;
        } catch (error) {
            console.error('❌ Failed to process analytics data:', error);
            return this.getDefaultAnalyticsData();
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


    getDefaultEventInteractionsData() {
        return {
            total_clicks: 0,
            registrations: 0,
            conversion_rate: 0,
            top_events: []
        };
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


    getDefaultFormSubmissionsData() {
        return {
            total_submissions: 0,
            successful_submissions: 0,
            success_rate: 0,
            form_types: {}
        };
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


    getDefaultRealTimeData() {
        return {
            active_users_now: 0,
            current_page_views: 0,
            top_active_pages: [],
            recent_events: []
        };
    }

    async getStatsDocument(docName) {
        try {
            const docRef = doc(db, 'analytics', docName);
            const docSnap = await getDoc(docRef);
            
            if (docSnap.exists()) {
                return docSnap.data();
            } else {
                console.log(`No ${docName} document found, creating with default structure`);
                
                // Create document with default structure
                let defaultData = {};
                if (docName === 'clickCounts' || docName === 'viewCounts') {
                    defaultData = {
                        eventTiers: {},
                        eventTypes: {},
                        esportTitles: {},
                        locations: {},
                        eventNames: {},
                        userIds: {},
                        lastUpdated: serverTimestamp()
                    };
                } else if (docName === 'socialCounts') {
                    defaultData = {
                        actions: {},
                        targetTypes: {},
                        lastUpdated: serverTimestamp()
                    };
                } else if (docName === 'formJoins') {
                    defaultData = {
                        formNames: {},
                        lastUpdated: serverTimestamp()
                    };
                }
                
                await setDoc(docRef, defaultData);
                return defaultData;
            }
        } catch (error) {
            console.error(`Error fetching ${docName}:`, error);
            return {};
        }
    }
    
    processPageViewsData(globalCounts) {
        if (!globalCounts) return this.getDefaultPageViewsData();
        
        const eventNames = globalCounts.eventNames || {};
        const total = Object.values(eventNames).reduce((sum, count) => sum + count, 0);
        
        const topPages = Object.entries(eventNames)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 10)
            .map(([path, views]) => ({ path, views }));
        
        return {
            total,
            today: 0,
            yesterday: 0,
            weekly_change: 0,
            monthly_change: 0,
            top_pages: topPages
        };
    }
    
    processEventInteractionsData(globalCounts, page = 1, limit = 10) {
        if (!globalCounts) return this.getDefaultEventInteractionsData();
        
        const eventNames = globalCounts.eventNames || {};
        const totalClicks = Object.values(eventNames).reduce((sum, count) => sum + count, 0);
        
        const topEvents = Object.entries(eventNames)
            .sort(([,a], [,b]) => b - a)
            .slice((page - 1) * limit, page * limit)
            .map(([name, clicks]) => ({
                name,
                clicks,
                registrations: 0,
                tier: '',
                type: '',
                esport: ''
            }));
        
        return {
            total_clicks: totalClicks,
            registrations: 0,
            conversion_rate: 0,
            top_events: topEvents
        };
    }
    
    processSocialInteractionsData(socialCounts) {
        if (!socialCounts) return this.getDefaultSocialInteractionsData();
        
        const actions = socialCounts.actions || {};
        const totalFollows = actions.follow || 0;
        const totalLikes = actions.like || 0;
        const shares = actions.share || 0;
        
        const followRate = totalFollows > 0 ? (totalFollows / (totalFollows + totalLikes) * 100).toFixed(1) : 0;
        
        return {
            total_follows: totalFollows,
            total_likes: totalLikes,
            shares,
            follow_rate: followRate,
            engagement_rate: 0,
            top_followed_users: []
        };
    }
    
    processFormSubmissionsData(formJoins) {
        if (!formJoins) return this.getDefaultFormSubmissionsData();
        
        const formNames = formJoins.formNames || {};
        const totalSubmissions = Object.values(formNames).reduce((sum, count) => sum + count, 0);
        
        return {
            total_submissions: totalSubmissions,
            successful_submissions: 0,
            success_rate: 0,
            form_types: formNames
        };
    }
    
    processTopEventsData(globalCounts, page = 1, limit = 10) {
        if (!globalCounts) return [];
        
        const eventNames = globalCounts.eventNames || {};
        
        const topEvents = Object.entries(eventNames)
            .sort(([,a], [,b]) => b - a)
            .slice((page - 1) * limit, page * limit)
            .map(([name, participants]) => ({
                name,
                participants,
                revenue: 0,
                tier: '',
                type: '',
                esport: '',
                location: ''
            }));
        
        return topEvents;
    }
    
    processUserEngagementData(clickCounts, viewCounts) {
        if (!clickCounts && !viewCounts) return this.getDefaultUserEngagementData();
        
        const clickUserIds = clickCounts?.userIds || {};
        const viewUserIds = viewCounts?.userIds || {};
        const allUserIds = {...clickUserIds, ...viewUserIds};
        const activeUsers = Object.keys(allUserIds).length;
        
        return {
            active_users: activeUsers,
            session_duration: 0,
            bounce_rate: 0,
            pages_per_session: 0,
            new_users: 0,
            returning_users: 0
        };
    }
    
    processRealTimeData(globalCounts) {
        if (!globalCounts) return this.getDefaultRealTimeData();
        
        const eventNames = globalCounts.eventNames || {};
        const userIds = globalCounts.userIds || {};
        
        const activeUsersNow = Object.keys(userIds).length;
        const currentPageViews = Object.values(eventNames).reduce((sum, count) => sum + count, 0);
        
        const topActivePages = Object.entries(eventNames)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5)
            .map(([path, active_users]) => ({ path, active_users }));
        
        return {
            active_users_now: activeUsersNow,
            current_page_views: currentPageViews,
            top_active_pages: topActivePages,
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

    async refreshData(page = 1, limit = 10, timeFilter = null) {
        return await this.fetchAnalyticsData(page, limit, timeFilter);
    }
    
    setTimeFilter(timeFilter) {
        this.currentTimeFilter = timeFilter;
    }
    
    getTimeFilter() {
        return this.currentTimeFilter;
    }
    
    async getEventInteractionsPaginated(page = 1, limit = 10, timeFilter = null) {
        try {
            let clickCounts;
            const filterToUse = timeFilter || this.currentTimeFilter;
            
            if (window.getTimeFilteredAnalyticsCounts && filterToUse !== 'all') {
                const filteredData = await window.getTimeFilteredAnalyticsCounts(filterToUse);
                clickCounts = filteredData.clickCounts;
            } else {
                clickCounts = await this.getStatsDocument('clickCounts');
            }
            
            return this.processEventInteractionsData(clickCounts, page, limit);
        } catch (error) {
            console.error('Error fetching paginated event interactions:', error);
            return this.getDefaultEventInteractionsData();
        }
    }
    
    async getTopEventsPaginated(page = 1, limit = 10, timeFilter = null) {
        try {
            let clickCounts;
            const filterToUse = timeFilter || this.currentTimeFilter;
            
            if (window.getTimeFilteredAnalyticsCounts && filterToUse !== 'all') {
                const filteredData = await window.getTimeFilteredAnalyticsCounts(filterToUse);
                clickCounts = filteredData.clickCounts;
            } else {
                clickCounts = await this.getStatsDocument('clickCounts');
            }
            
            return this.processTopEventsData(clickCounts, page, limit);
        } catch (error) {
            console.error('Error fetching paginated top events:', error);
            return [];
        }
    }

    isAnalyticsAvailable() {
        return this.isInitialized;
    }

    getConnectionStatus() {
        return {
            analytics_service: this.isAnalyticsAvailable(),
            firestore_available: true
        };
    }
}



const filamentAnalytics = new FilamentAnalyticsAPI();

window.filamentAnalytics = filamentAnalytics;

window.initializeFilamentAnalytics = function() {
    if (window.filamentAnalytics) {
    }
};

// Time-based analytics data retrieval
function getDateKeysForFilter(timeFilter) {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    
    switch (timeFilter) {
        case '1d':
            return {
                collection: 'analytics-daily',
                docPattern: `${year}-${month}-${day}`
            };
        case '1m':
            return {
                collection: 'analytics-monthly', 
                docPattern: `${year}-${month}`
            };
        case '1y':
            return {
                collection: 'analytics-yearly',
                docPattern: `${year}`
            };
        default:
            return {
                collection: 'analytics-yearly',
                docPattern: `${year}`
            };
    }
}

async function getTimeBasedAnalytics(timeFilter = '1d') {
    try {
        const { collection: collectionName, docPattern } = getDateKeysForFilter(timeFilter);
        
        // For the analytics collection, use the exact document names as they exist
        let clickDocName, viewDocName, socialDocName, formDocName;
        
        if (collectionName === 'analytics') {
            // Yearly data is stored directly in analytics collection
            clickDocName = `clickCounts-${docPattern}`;
            viewDocName = `viewCounts-${docPattern}`;
            socialDocName = `socialCounts-${docPattern}`;
            formDocName = `formCounts-${docPattern}`;
        } else {
            // Daily and monthly data uses the full date pattern
            clickDocName = `clickCounts-${docPattern}`;
            viewDocName = `viewCounts-${docPattern}`;
            socialDocName = `socialCounts-${docPattern}`;
            formDocName = `formCounts-${docPattern}`;
        }
        
        const [clickCountsRef, viewCountsRef, socialCountsRef, formCountsRef] = [
            doc(db, collectionName, clickDocName),
            doc(db, collectionName, viewDocName),
            doc(db, collectionName, socialDocName),
            doc(db, collectionName, formDocName)
        ];
        
        console.log(`Querying analytics from ${collectionName}:`, {
            clickDoc: clickDocName,
            viewDoc: viewDocName,
            socialDoc: socialDocName,
            formDoc: formDocName
        });
        
        const [clickSnap, viewSnap, socialSnap, formSnap] = await Promise.all([
            getDoc(clickCountsRef),
            getDoc(viewCountsRef),
            getDoc(socialCountsRef),
            getDoc(formCountsRef)
        ]);
        
        const defaultStructure = {
            eventTiers: {},
            eventTypes: {},
            esportTitles: {},
            locations: {},
            eventNames: {},
            userIds: {},
            date: docPattern
        };
        
        const clickData = clickSnap.exists() ? clickSnap.data() : defaultStructure;
        const viewData = viewSnap.exists() ? viewSnap.data() : defaultStructure;
        const socialData = socialSnap.exists() ? socialSnap.data() : { 
            actions: {}, 
            targetTypes: {}, 
            date: docPattern 
        };
        const formData = formSnap.exists() ? formSnap.data() : { 
            formNames: {}, 
            date: docPattern 
        };
        
        console.log('Analytics data retrieved:', {
            clickExists: clickSnap.exists(),
            viewExists: viewSnap.exists(),
            socialExists: socialSnap.exists(),
            formExists: formSnap.exists()
        });
        
        return {
            clickCounts: clickData,
            viewCounts: viewData,
            socialCounts: socialData,
            formCounts: formData
        };
    } catch (error) {
        console.error('Error getting time-based analytics:', error);
        return {
            clickCounts: { eventTiers: {}, eventTypes: {}, esportTitles: {}, locations: {}, eventNames: {}, userIds: {} },
            viewCounts: { eventTiers: {}, eventTypes: {}, esportTitles: {}, locations: {}, eventNames: {}, userIds: {} },
            socialCounts: { actions: {}, targetTypes: {} },
            formCounts: { formNames: {} }
        };
    }
}

// Legacy function for backward compatibility - now uses yearly data
window.getAnalyticsCounts = async function() {
    return await getTimeBasedAnalytics('1y');
};

// New time-filtered function
window.getTimeFilteredAnalyticsCounts = async function(timeFilter = '1d') {
    return await getTimeBasedAnalytics(timeFilter);
};

export default filamentAnalytics;