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

    this.db = initializeFirestore(this.app, {
      localCache: persistentLocalCache({
        tabManager: persistentMultipleTabManager()
      })
    });

    this.isInitialized = true;

    return {
      app: this.app,
      auth: this.auth,
      db: this.db
    };
  }

  async initializeAuth() {
    if (!this.auth) {
      throw new Error('Auth not initialized. User not logged in.');
    }

    try {
      const response = await fetch('/api/user/firebase-token');
      if (!response.ok) {
        throw new Error('Failed to get Firebase token');
      }

      const { token } = await response.json();
      const userCredential = await signInWithCustomToken(this.auth, token);
      const currentUser = userCredential.user;
      const idTokenResult = await currentUser.getIdTokenResult();
      const { role, teamId, userId } = idTokenResult.claims;

      return {
        user: currentUser,
        claims: { role, teamId, userId }
      };
    } catch (error) {
      console.error('Firebase authentication failed:', error);
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