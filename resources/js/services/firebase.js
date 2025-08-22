import { initializeApp } from "firebase/app";
import { initializeFirestore, persistentLocalCache, persistentMultipleTabManager } from "firebase/firestore";
import { getAuth, signInWithCustomToken } from "firebase/auth";

class FirebaseService {
  constructor(hiddenUserId = null) {
    this.app = null;
    this.auth = null;
    this.db = null;
    this.isInitialized = false;
    this.hiddenUserId = hiddenUserId;
  }

  initialize() {
    if (this.isInitialized) {
      return {
        app: this.app,
        auth: this.auth,
        db: this.db
      };
    }

    const firebaseConfig = {
      apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
      authDomain: import.meta.env.VITE_AUTH_DOMAIN,
      projectId: import.meta.env.VITE_PROJECT_ID,
      storageBucket: import.meta.env.VITE_STORAGE_BUCKET,
      messagingSenderId: import.meta.env.VITE_MESSAGE_SENDER_ID,
      appId: import.meta.env.VITE_APP_ID,
    };

    this.app = initializeApp(firebaseConfig);
    
    if (this.hiddenUserId) {
      this.auth = getAuth(this.app);
    }

    const firestoreOptions = {
      localCache: persistentLocalCache({
        tabManager: persistentMultipleTabManager()
      })
    };
    
    

    this.db = initializeFirestore(this.app, firestoreOptions);

    this.isInitialized = true;

    return {
      app: this.app,
      auth: this.auth,
      db: this.db
    };
  }

  async initializeAuth(eventId) {
    if (!this.auth) {
      throw new Error('Auth not initialized. User not logged in.');
    }

    try {
      // Check for existing authentication in session storage (event-specific if eventId provided)
      const authStorageKey =  `firebase_auth_bracket-${eventId}`;
      const storedAuth = sessionStorage.getItem(authStorageKey);
      let cachedToken = null;

      if (storedAuth) {
        const authData = JSON.parse(storedAuth);
        if (authData.expires > Date.now()) {
          cachedToken = authData.token;
        } else {
          sessionStorage.removeItem(authStorageKey);
        }
      }

      let jwtToken = cachedToken;
      
      if (!jwtToken) {
        // Get user info
        const uid = this.hiddenUserId;
        const role = window.loggedUserProfile?.role || 'PARTICIPANT';
        const teamId = window.loggedUserProfile?.team?.id || window.loggedUserProfile?.teams?.[0]?.id || null;
        
        const domain = `${import.meta.env.VITE_API_URL}`;
        const jwtRoute = `${domain}/auth/token`;
        const response = await fetch(jwtRoute, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ 
            uid,
            role,
            teamId
          })
        });

        if (!response.ok) {
          throw new Error('Failed to get JWT token');
        }

        const { token } = await response.json();
        if (!token) {
          throw new Error('Failed to get JWT token');
        }
        
        jwtToken = token;
        
        // Store token in session storage with 1 hour expiry
        const authData = {
          token: jwtToken,
          expires: Date.now() + (60 * 60 * 1000) // 1 hour
        };
        sessionStorage.setItem(authStorageKey, JSON.stringify(authData));
      }

      const userCredential = await signInWithCustomToken(this.auth, jwtToken);
      const currentUser = userCredential.user;
      const idTokenResult = await currentUser.getIdTokenResult();
      const { role, teamId, userId } = idTokenResult.claims;

      console.log('Firebase authenticated:', currentUser.uid);

      return {
        user: currentUser,
        claims: { role, teamId, userId }
      };
    } catch (error) {
      console.error('Firebase authentication failed:', error);
      // Clean up on error
      const authStorageKey = eventId ? `firebase_auth_bracket-${eventId}` : 'firebase_auth_general';
      sessionStorage.removeItem(authStorageKey);
      throw error;
    }
  }

  getServices(hiddenUserId = null) {
    if (hiddenUserId) {
      this.hiddenUserId = hiddenUserId;
    }
    
    if (!this.isInitialized) {
      this.initialize();
    }
    
    return {
      app: this.app,
      auth: this.auth,
      db: this.db
    };
  }
}

export default new FirebaseService();