import { initializeApp } from "firebase/app";
import { initializeFirestore, persistentLocalCache, persistentMultipleTabManager } from "firebase/firestore";
import { getAuth, signInWithCustomToken } from "firebase/auth";
import { initializeAppCheck, ReCaptchaEnterpriseProvider, ReCaptchaV3Provider, getToken } from "firebase/app-check";

class FirebaseService {
  constructor(hiddenUserId = null) {
    this.app = null;
    this.auth = null;
    this.db = null;
    this.appCheck = null;
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
    
    // Initialize App Check with reCAPTCHA Enterprise
    try {
      const recaptchaSiteKey = import.meta.env.VITE_RECAPTCHA;
      console.log('Attempting App Check initialization with reCAPTCHA key:', recaptchaSiteKey ? 'Present' : 'Missing');
      if (recaptchaSiteKey) {
        this.appCheck = initializeAppCheck(this.app, {
          provider: new ReCaptchaEnterpriseProvider(recaptchaSiteKey),
          isTokenAutoRefreshEnabled: true
        });
        console.log('Firebase App Check initialized successfully with Enterprise reCAPTCHA');
        console.log('App Check auto-refresh enabled:', true);
      } else {
        console.warn('VITE_RECAPTCHA not found - App Check not initialized');
        console.warn('This will cause "Missing or insufficient permissions" errors');
      }
    } catch (error) {
      console.error('Failed to initialize App Check:', error);
      console.error('App Check error details:', {
        code: error.code,
        message: error.message,
        name: error.name
      });
      // Continue without App Check in case of errors
    }
    
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
      db: this.db,
      appCheck: this.appCheck
    };
  }

  async initializeAuth(eventId, hiddenUserId, userRole, joinEventTeamId) {
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
        const uid = hiddenUserId;
        const role = userRole;
        const teamId = joinEventTeamId;
        const domain = `${import.meta.env.VITE_CLOUD_FRONTEND_URL}`;
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
      db: this.db,
      appCheck: this.appCheck
    };
  }

  async getAppCheckToken() {
    if (!this.appCheck) {
      console.warn('App Check not initialized - cannot get token');
      return null;
    }

    try {
      const appCheckTokenResponse = await getToken(this.appCheck, false);
      return appCheckTokenResponse.token;
    } catch (error) {
      console.error('Failed to get App Check token:', error);
      return null;
    }
  }
}

export default new FirebaseService();