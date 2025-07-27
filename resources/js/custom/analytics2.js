import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, setDoc, onSnapshot, orderBy, doc, query, collection, where, or, clearIndexedDbPersistence, updateDoc, increment, serverTimestamp, getDoc } from "firebase/firestore";

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
    localCache: memoryLocalCache(),
});

async function updateAnalyticsCounts(eventTier, eventType, esportTitle, location, eventName, userId, collectionName = 'globalCounts') {
    try {
        const countsRef = doc(db, 'analytics', collectionName);
        
        // Check if document exists first
        const docSnap = await getDoc(countsRef);
        
        const updateData = {
            lastUpdated: serverTimestamp()
        };

        if (eventTier) {
            updateData[`eventTiers.${eventTier}`] = increment(1);
        }
        
        if (eventType) {
            updateData[`eventTypes.${eventType}`] = increment(1);
        }
        
        if (esportTitle) {
            updateData[`esportTitles.${esportTitle}`] = increment(1);
        }
        
        if (location) {
            updateData[`locations.${location}`] = increment(1);
        }

        if (eventName) {
            updateData[`eventNames.${eventName}`] = increment(1);
        }

        if (userId) {
            updateData[`userIds.${userId}`] = increment(1);
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
                lastUpdated: serverTimestamp()
            };
            
            // Add initial counts
            if (eventTier) initialData.eventTiers[eventTier] = 1;
            if (eventType) initialData.eventTypes[eventType] = 1;
            if (esportTitle) initialData.esportTitles[esportTitle] = 1;
            if (location) initialData.locations[location] = 1;
            if (eventName) initialData.eventNames[eventName] = 1;
            if (userId) initialData.userIds[userId] = 1;
            
            await setDoc(countsRef, initialData);
        } else {
            await updateDoc(countsRef, updateData);
        }
        
        console.log('Analytics counts updated');
    } catch (error) {
        console.error('Error updating analytics counts:', error);
    }
}

async function updateSocialCounts(action, targetType) {
    try {
        const socialRef = doc(db, 'analytics', 'socialCounts');
        
        // Check if document exists first
        const docSnap = await getDoc(socialRef);
        
        const updateData = {
            lastUpdated: serverTimestamp()
        };

        if (action) {
            updateData[`actions.${action}`] = increment(1);
        }
        
        if (targetType) {
            updateData[`targetTypes.${targetType}`] = increment(1);
        }

        if (!docSnap.exists()) {
            // Create document with initial structure
            const initialData = {
                actions: {},
                targetTypes: {},
                lastUpdated: serverTimestamp()
            };
            
            // Add initial counts
            if (action) initialData.actions[action] = 1;
            if (targetType) initialData.targetTypes[targetType] = 1;
            
            await setDoc(socialRef, initialData);
        } else {
            await updateDoc(socialRef, updateData);
        }
        
        console.log('Social counts updated');
    } catch (error) {
        console.error('Error updating social counts:', error);
    }
}

async function updateFormCounts(formName) {
    try {
        const formRef = doc(db, 'analytics', 'formJoins');
        
        // Check if document exists first
        const docSnap = await getDoc(formRef);
        
        const updateData = {
            lastUpdated: serverTimestamp()
        };

        if (formName) {
            updateData[`formNames.${formName}`] = increment(1);
        }

        if (!docSnap.exists()) {
            // Create document with initial structure
            const initialData = {
                formNames: {},
                lastUpdated: serverTimestamp()
            };
            
            // Add initial counts
            if (formName) initialData.formNames[formName] = 1;
            
            await setDoc(formRef, initialData);
        } else {
            await updateDoc(formRef, updateData);
        }
        
        console.log('Form counts updated');
    } catch (error) {
        console.error('Error updating form counts:', error);
    }
}

// Only add functions to window if analytics meta tag is present
if (document.querySelector('meta[name="analytics"]')) {
    window.trackEventCardClick = async function(element, event) {
        console.log("Event card click tracked");
        
        // Prevent default navigation if this is a link
        if (event) {
            event.preventDefault();
        }
        
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

            // Wait for analytics to be saved before allowing redirect
            await Promise.all([
                updateAnalyticsCounts(
                    eventData.eventTier,
                    eventData.eventType,
                    eventData.esportTitle,
                    eventData.location,
                    eventData.eventName,
                    eventData.userId,
                    'clickCounts'
                ),
               
            ]);

            console.log('Event card click tracked successfully:', eventData);

            // Now navigate to the link if it exists
            if (element.href) {
                window.location.href = element.href;
            }

        } catch (error) {
            console.error('Error tracking event card click:', error);
            
            // Even if tracking fails, still navigate if it's a link
            if (element.href) {
                window.location.href = element.href;
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
                'viewCounts'
            );

            console.log('Event view tracked:', eventData);

        } catch (error) {
            console.error('Error tracking event view:', error);
            
        }
    };

    window.trackSocialInteraction = function(action, target, targetType = 'user') {
        const eventData = {
            event_category: 'social',
            event_label: `${action}_${targetType}`,
            social_action: action,
            social_target: target,
            social_type: targetType
        };
        
        updateSocialCounts(action, targetType);
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
}


if (typeof module !== 'undefined' && module.exports) {
    module.exports = {};
}