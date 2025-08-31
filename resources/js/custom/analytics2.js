// Firebase will be dynamically imported only when needed
import firebaseService from '../services/firebase.js';



// Utility functions for date formatting
function getDateKeys() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    
    return {
        daily: `${year}-${month}-${day}`,
        monthly: `${year}-${month}`,
        yearly: `${year}`
    };
}


if (document.querySelector('meta[name="analytics"]') && import.meta.env.VITE_APP_ENV !== 'production') {

// Dynamically import only Firestore functions (not initialization)
let firestoreModules = null;

async function getFirebaseServices() {
    if (!firestoreModules) {
        const firestore = await import('firebase/firestore');
        
        firestoreModules = {
            setDoc: firestore.setDoc,
            doc: firestore.doc,
            updateDoc: firestore.updateDoc,
            increment: firestore.increment,
            serverTimestamp: firestore.serverTimestamp,
            getDoc: firestore.getDoc,
        };
    }

    // Get existing Firebase services (reuses existing Firestore instance)
    const { db } = firebaseService.getServices();
    
    return { firebaseModules: firestoreModules, db };
}

async function updateAnalyticsCounts(eventTier, eventType, esportTitle, location, eventName, userId, type = 'click') {
    try {
        const { firebaseModules, db: firestoreDb } = await getFirebaseServices();
        const dateKeys = getDateKeys();
        
        const collections = [
            { collection: 'analytics-daily', docId: dateKeys.daily },
            { collection: 'analytics-monthly', docId: dateKeys.monthly },
            { collection: 'analytics-yearly', docId: dateKeys.yearly }
        ];
        
        for (const { collection, docId } of collections) {
            const countsRef = firebaseModules.doc(firestoreDb, collection, `${type}Counts-${docId}`);
            
            const docSnap = await firebaseModules.getDoc(countsRef);
            
            const updateData = {
                lastUpdated: firebaseModules.serverTimestamp(),
                date: docId
            };

            if (eventTier && typeof eventTier === 'string' && eventTier.trim()) {
                const cleanTier = eventTier.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                updateData[`eventTiers.${cleanTier}`] = firebaseModules.increment(1);
            }
            
            if (eventType && typeof eventType === 'string' && eventType.trim()) {
                const cleanType = eventType.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                updateData[`eventTypes.${cleanType}`] = firebaseModules.increment(1);
            }
            
            if (esportTitle && typeof esportTitle === 'string' && esportTitle.trim()) {
                const cleanTitle = esportTitle.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                updateData[`esportTitles.${cleanTitle}`] = firebaseModules.increment(1);
            }
            
            if (location && typeof location === 'string' && location.trim()) {
                const cleanLocation = location.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                updateData[`locations.${cleanLocation}`] = firebaseModules.increment(1);
            }

            if (eventName && typeof eventName === 'string' && eventName.trim()) {
                const cleanName = eventName.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                updateData[`eventNames.${cleanName}`] = firebaseModules.increment(1);
            }

            if (userId && typeof userId === 'string' && userId.trim()) {
                const cleanUserId = userId.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 50);
                updateData[`userIds.${cleanUserId}`] = firebaseModules.increment(1);
            }

            if (!docSnap.exists()) {
                // Create document with initial structure
                const initialData = {
                    eventTiers: {},
                    eventTypes: {},
                    esportTitles: {},
                    locations: {},
                    eventNames: {},
                    userIds: {},
                    lastUpdated: firebaseModules.serverTimestamp(),
                    date: docId
                };
                
                if (eventTier && typeof eventTier === 'string' && eventTier.trim()) {
                    const cleanTier = eventTier.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                    initialData.eventTiers[cleanTier] = 1;
                }
                if (eventType && typeof eventType === 'string' && eventType.trim()) {
                    const cleanType = eventType.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                    initialData.eventTypes[cleanType] = 1;
                }
                if (esportTitle && typeof esportTitle === 'string' && esportTitle.trim()) {
                    const cleanTitle = esportTitle.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                    initialData.esportTitles[cleanTitle] = 1;
                }
                if (location && typeof location === 'string' && location.trim()) {
                    const cleanLocation = location.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                    initialData.locations[cleanLocation] = 1;
                }
                if (eventName && typeof eventName === 'string' && eventName.trim()) {
                    const cleanName = eventName.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 100);
                    initialData.eventNames[cleanName] = 1;
                }
                if (userId && typeof userId === 'string' && userId.trim()) {
                    const cleanUserId = userId.replace(/[^a-zA-Z0-9_-]/g, '_').substring(0, 50);
                    initialData.userIds[cleanUserId] = 1;
                }
                
                await firebaseModules.setDoc(countsRef, initialData);
            } else {
                await firebaseModules.updateDoc(countsRef, updateData);
            }
        }
        
        console.log('Analytics counts updated for all time periods');
    } catch (error) {
        console.error('Error updating analytics counts:', error);
    }
}

async function updateSocialCounts(action, targetType) {
    try {
        const { firebaseModules, db: firestoreDb } = await getFirebaseServices();
        const dateKeys = getDateKeys();
        
        const collections = [
            { collection: 'analytics-daily', docId: dateKeys.daily },
            { collection: 'analytics-monthly', docId: dateKeys.monthly },
            { collection: 'analytics-yearly', docId: dateKeys.yearly }
        ];
        
        for (const { collection, docId } of collections) {
            const socialRef = firebaseModules.doc(firestoreDb, collection, `socialCounts-${docId}`);
            
            const docSnap = await firebaseModules.getDoc(socialRef);
            
            const updateData = {
                lastUpdated: firebaseModules.serverTimestamp(),
                date: docId
            };

            if (action) {
                updateData[`actions.${action}`] = firebaseModules.increment(1);
            }
            
            if (targetType) {
                updateData[`targetTypes.${targetType}`] = firebaseModules.increment(1);
            }

            if (!docSnap.exists()) {
                const initialData = {
                    actions: {},
                    targetTypes: {},
                    lastUpdated: firebaseModules.serverTimestamp(),
                    date: docId
                };
                
                if (action) initialData.actions[action] = 1;
                if (targetType) initialData.targetTypes[targetType] = 1;
                
                await firebaseModules.setDoc(socialRef, initialData);
            } else {
                await firebaseModules.updateDoc(socialRef, updateData);
            }
        }
        
        console.log('Social counts updated for all time periods');
    } catch (error) {
        console.error('Error updating social counts:', error);
    }
}

async function updateFormCounts(formName) {
    try {
        const { firebaseModules, db: firestoreDb } = await getFirebaseServices();
        const dateKeys = getDateKeys();
        
        const collections = [
            { collection: 'analytics-daily', docId: dateKeys.daily },
            { collection: 'analytics-monthly', docId: dateKeys.monthly },
            { collection: 'analytics-yearly', docId: dateKeys.yearly }
        ];
        
        for (const { collection, docId } of collections) {
            const formRef = firebaseModules.doc(firestoreDb, collection, `formCounts-${docId}`);
            
            const docSnap = await firebaseModules.getDoc(formRef);
            
            const updateData = {
                lastUpdated: firebaseModules.serverTimestamp(),
                date: docId
            };

            if (formName) {
                updateData[`formNames.${formName}`] = firebaseModules.increment(1);
            }

            if (!docSnap.exists()) {
                const initialData = {
                    formNames: {},
                    lastUpdated: firebaseModules.serverTimestamp(),
                    date: docId
                };
                
                if (formName) initialData.formNames[formName] = 1;
                
                await firebaseModules.setDoc(formRef, initialData);
            } else {
                await firebaseModules.updateDoc(formRef, updateData);
            }
        }
        
        console.log('Form counts updated for all time periods');
    } catch (error) {
        console.error('Error updating form counts:', error);
    }
}

    window.trackEventCardClick = async function(element, event) {
        console.log("Event card click tracked");
        
        if (event) {
            event.preventDefault();
        }
        
        if (element.disabled || element.dataset.clicking === 'true') {
            console.log("Element already disabled or being processed");
            return;
        }
        
        const storedHref = element.href;
        element.disabled = true;
        element.dataset.clicking = 'true';
        
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

            console.log('Tracking event data:', eventData);

            // Validate eventData to prevent empty or undefined values
            const cleanEventData = {
                eventTier: eventData.eventTier || null,
                eventType: eventData.eventType || null,
                esportTitle: eventData.esportTitle || null,
                location: eventData.location || null,
                eventName: eventData.eventName || null,
                userId: eventData.userId || null
            };
            
            console.log('Clean event data:', cleanEventData);

            const analyticsPromise = updateAnalyticsCounts(
                cleanEventData.eventTier,
                cleanEventData.eventType,
                cleanEventData.esportTitle,
                cleanEventData.location,
                cleanEventData.eventName,
                cleanEventData.userId,
                'click'
            );

            const timeoutPromise = new Promise(resolve => {
                setTimeout(resolve, 2000);
            });

            Promise.race([analyticsPromise, timeoutPromise]).finally(() => {
                if (storedHref) {
                    window.location.href = storedHref;
                }
            });

            analyticsPromise.then(() => {
                console.log('Event card click tracked successfully:', eventData);
            }).catch((error) => {
                console.error('Error tracking event card click analytics:', error);
            });

        } catch (error) {
            console.error('Error tracking event card click:', error);
            
            if (storedHref) {
                window.location.href = storedHref;
            }
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

            updateAnalyticsCounts(
                eventData.eventTier,
                eventData.eventType,
                eventData.esportTitle,
                eventData.location,
                eventData.eventName,
                eventData.userId,
                'view'
            );

            console.log('Event view tracked:', eventData);

        } catch (error) {
            console.error('Error tracking event view:', error);
            
        }
    };

    window.trackSocialInteraction = function(action, target, targetType = 'user') {
        try {
            const eventData = {
                event_category: 'social',
                event_label: `${action}_${targetType}`,
                social_action: action,
                social_target: target,
                social_type: targetType
            };
            
            const socialInteractionTypes = {
                'like': 'UserLikes',
                'unlike': 'UserLikes', 
                'follow': targetType === 'user' ? 'UserFollows' : targetType === 'team' ? 'TeamFollows' : targetType === 'event' ? 'EventFollows' : 'UserFollows',
                'unfollow': targetType === 'user' ? 'UserFollows' : targetType === 'team' ? 'TeamFollows' : targetType === 'event' ? 'EventFollows' : 'UserFollows',
                'friend_request': 'UserFollows',
                'unfriend': 'UserFollows'
            };

            const interactionCategory = socialInteractionTypes[action] || 'UserFollows';
            
            console.log('Social interaction tracked:', {
                action,
                target,
                targetType,
                category: interactionCategory,
                eventData
            });
            
            updateSocialCounts(action, targetType);
            
        } catch (error) {
            console.error('Error tracking social interaction:', error);
        }
    };

    window.trackFormSubmission = function(formName, additionalParams = {}) {
        const eventData = {
            event_category: 'form',
            event_label: formName,
            form_name: formName,
            ...additionalParams
        };
        
        updateFormCounts(formName);
    };
} else {
    // Non-production environment - provide no-op functions
    window.trackEventCardClick = function(element, event) {
        console.log("Analytics disabled in non-production environment");
        if (event) {
            event.preventDefault();
        }
        // Still handle navigation for clicked links
        if (element.href) {
            setTimeout(() => {
                window.location.href = element.href;
            }, 100);
        }
    };
    
    window.trackEventViewFromDiv = function(element) {
        console.log("Analytics disabled in non-production environment");
    };
    
    window.trackSocialInteraction = function(action, target, targetType) {
        console.log("Analytics disabled in non-production environment");
    };
    
    window.trackFormSubmission = function(formName, additionalParams) {
        console.log("Analytics disabled in non-production environment");
    };
}


if (typeof module !== 'undefined' && module.exports) {
    module.exports = {};
}