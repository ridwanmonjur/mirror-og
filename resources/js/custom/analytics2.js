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

async function updateAnalyticsCounts(eventTier, eventType, esportTitle, location, eventName, userId) {
    try {
        const countsRef = doc(db, 'stats', 'globalCounts');
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

        await updateDoc(countsRef, updateData);
        console.log('Analytics counts updated');
    } catch (error) {
        console.error('Error updating analytics counts:', error);
    }
}

async function updateSocialCounts(action, targetType) {
    try {
        const socialRef = doc(db, 'stats', 'socialCounts');
        const updateData = {
            lastUpdated: serverTimestamp()
        };

        if (action) {
            updateData[`actions.${action}`] = increment(1);
        }
        
        if (targetType) {
            updateData[`targetTypes.${targetType}`] = increment(1);
        }

        await updateDoc(socialRef, updateData);
        console.log('Social counts updated');
    } catch (error) {
        console.error('Error updating social counts:', error);
    }
}

async function updateFormCounts(formName) {
    try {
        const formRef = doc(db, 'stats', 'formJoins');
        const updateData = {
            lastUpdated: serverTimestamp()
        };

        if (formName) {
            updateData[`formNames.${formName}`] = increment(1);
        }

        await updateDoc(formRef, updateData);
        console.log('Form counts updated');
    } catch (error) {
        console.error('Error updating form counts:', error);
    }
}

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

        updateAnalyticsCounts(
            eventData.eventTier,
            eventData.eventType,
            eventData.esportTitle,
            eventData.location,
            eventData.eventName,
            eventData.userId
        );

        console.log('Event card click tracked:', eventData);

    } catch (error) {
        console.error('Error tracking event card click:', error);
        
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
            eventData.userId
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


if (typeof module !== 'undefined' && module.exports) {
    module.exports = {};
}