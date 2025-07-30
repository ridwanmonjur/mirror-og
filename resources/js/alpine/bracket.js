import { initializeApp } from "firebase/app";
import {
  initializeFirestore, memoryLocalCache, setDoc, serverTimestamp,
  addDoc, onSnapshot, updateDoc,  doc, query, collection, collectionGroup, getDocs, getDoc, where, or
} from "firebase/firestore";
// import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";
import { getAuth, signInWithCustomToken, onAuthStateChanged } from "firebase/auth";
import { createApp, reactive } from "petite-vue";
import { generateInitialBracket, resetDotsToContainer, clearSelection, calcScores, updateReportFromFirestore, showSwal, createReportTemp, createDisputeDto, generateWarningHtml, updateAllCountdowns, diffDateWithNow, updateCurrentReportDots } from "../custom/brackets";
import { addAllTippy, addDotsToContainer, addTippyToClass } from "../custom/tippy";
import BracketData from "../custom/BracketData";
import UploadData from "../custom/UploadData";


const eventId = document.getElementById('eventId')?.value;

window.updateReportDispute = async (reportId, team1Id, team2Id) => {
  const reportRef = doc(db, `event/${eventId}/brackets`, reportId);
  const docSnap = await getDoc(reportRef);
  if (docSnap.exists()) {
    const updateData = {
      team1Id,
      team2Id,
      updated_at: serverTimestamp(),
    };

    await updateDoc(reportRef, updateData);
  }

  const disputesRef = collection(db, `events/${eventId}/disputes`);
  const q = query(disputesRef, where('report_id', '==', reportId));
  
  const querySnapshot = await getDocs(q);
  if (querySnapshot.empty) return ;

  const updatePromises = querySnapshot.docs.map(doc => {
    const data = doc.data();
    const updates = {};
    
    if (data.dispute_teamNumber == 0) {
      updates.dispute_teamId = team1Id;
    } else if (data.dispute_teamNumber == 1) {
      updates.dispute_teamId = team2Id;
    }
    
    if (data.response_teamNumber == 0) {
      updates.response_teamId = team1Id;
    } else if (data.response_teamNumber == 1) {
      updates.response_teamId = team2Id;
    }
    
    return updateDoc(doc.ref, updates);
  });
  
  await Promise.all(updatePromises);
  return querySnapshot.size;
}

let hiddenUserId = document.getElementById('hidden_user_id')?.value;
async function initializeFirebaseAuth() {
  try {
    const response = await fetch('/api/user/firebase-token');
    if (!response.ok) {
      throw new Error('Failed to get Firebase token');
    }

    let currentUser = null;
    const { token } = await response.json();

    const userCredential = await signInWithCustomToken(auth, token);
    currentUser = userCredential.user;

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

const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
  authDomain: import.meta.env.VITE_AUTH_DOMAIN,
  projectId: import.meta.env.VITE_PROJECT_ID,
  storageBucket: import.meta.env.VITE_STORAGE_BUCKET,
  messagingSenderId: import.meta.env.VITE_MESSAGE_SENDER_ID,
  appId: import.meta.env.VITE_APP_ID,
};

const app = initializeApp(firebaseConfig);
let auth = null;
if (hiddenUserId) {
  auth = getAuth(app);
}

const db = initializeFirestore(app, {
  localCache: memoryLocalCache(),
});

async function getAllMatchStatusesData() {
  const matchesCRef = collection(db, `event/${eventId}/brackets`);
  const allMatchStatusesQ = query(matchesCRef);
  let allDataList = {}, modifiedDataList = {}, newDataList = {};
  let newClassList = [], modifiedClassList = [];
  let isAddedActionType = true, isLoadedActionType = false;
  isAddedActionType = true;

  onSnapshot(allMatchStatusesQ, async (reportSnapshot) => {
    reportSnapshot.docChanges().forEach((change) => {
      if (change.type === "added") {
        if (!isLoadedActionType) {
          allDataList[change.doc.id] = change.doc.data();
        } else {
          newDataList[change.doc.id] = change.doc.data();
        }
      }

      if (change.type === "modified") {
        isAddedActionType = false;
        modifiedDataList[change.doc.id] = change.doc.data();
      }
    });


    if (!isLoadedActionType) {

      Object.entries(allDataList).forEach(([key, value]) => {
        addDotsToContainer(key, value)
      });

      addAllTippy();
      isLoadedActionType = true;
    } else {

      Object.entries(newDataList).forEach(([key, value]) => {
        addDotsToContainer(key, value);
        newClassList.push([key, value.position])
      });

      addTippyToClass(newClassList);

      Object.entries(modifiedDataList).forEach(([key, value]) => {
        addDotsToContainer(key, value);
        modifiedClassList.push([key, value.position])
      });

      addTippyToClass(modifiedClassList);
    }

    newDataList = {}, modifiedDataList = {};
    newClassList = [], modifiedClassList = [];
    let tabLoading = document.querySelector('button#tabLoading');
    if (tabLoading) {
      
      tabLoading.classList.remove('loading');
    } else {
      window.showAll();
    }

    await window.closeLoading();
    let isLoading = localStorage.getItem('isLoading');
      if (isLoading) {
        tabLoading.click();
      };

    localStorage.removeItem('isLoading');
  });
}

getAllMatchStatusesData();
updateAllCountdowns();
setInterval(updateAllCountdowns, 60000);

const fileStore = reactive({
  disputeClaimFiles: [],
  disputeResponseFiles: [],
  
  activeFileType: null, 
  
  addFiles(newFiles, fileType) {
    if (fileType === 'claim') {
      this.disputeClaimFiles = [...this.disputeClaimFiles, ...newFiles];
    } else if (fileType === 'response') {
      this.disputeResponseFiles = [...this.disputeResponseFiles, ...newFiles];
    }
  },
  
  getFiles(fileType) {
    return fileType === 'claim' 
      ? this.disputeClaimFiles 
      : this.disputeResponseFiles;
  },
  
  clearFilesByIndex(fileType, index) {
    if (fileType === 'claim') {
      this.disputeClaimFiles = this.disputeClaimFiles.filter((_, arrayIndex) => arrayIndex !== index);
    } else if (fileType === 'response') {
      this.disputeResponseFiles = this.disputeResponseFiles.filter((_, arrayIndex) => arrayIndex !== index);
    }
  },

  async uploadToServer(fileType) {
    try {
      const createFormData = new FormData();
      let files = fileType === 'claim' 
        ? this.disputeClaimFiles 
        : this.disputeResponseFiles;
      files.forEach((file) => {
        createFormData.append('media2[]', file);
      });

      const csrfToken4 = document.querySelector('meta[name="csrf-token"]').content;

      let uploadResponse = await fetch('/api/media', {
        method: 'POST',
        body: createFormData,
        headers: {
          'X-CSRF-TOKEN': csrfToken4
        }
      });

      uploadResponse = await uploadResponse.json();

      if (uploadResponse.files) {
        return uploadResponse;
      } else {
        window.toastError("Uploading to server failed");
      }
    } catch (error) {
      window.toastError("Uploading to server failed");
    }
  },
});

function CountDown (options) {
  return {
    targetDate: null,
    dateText: null,
    intervalId: null,
    
    init() {
      if (this.$refs.foo) {
        const el = this.$refs.foo;
        this.targetDate = el.dataset.diffDate;
      } else {
        this.targetDate = options.targetDate;
      }

      this.dateText = diffDateWithNow(this.targetDate);
      this.startTimer();
    },
    
    startTimer() {
      this.intervalId = setInterval(() => {
        this.dateText = diffDateWithNow(this.targetDate);
      }, 60000);
    },
    
    stopTimer() {
      if (this.intervalId) {
        clearInterval(this.intervalId);
        this.intervalId = null;
      }
    },
  };
}

let totalMatches = 3;
const userLevelEnums = JSON.parse(document.getElementById('userLevelEnums').value ?? '[]');
const disputeLevelEnums = JSON.parse(document.getElementById('disputeLevelEnums' ).value ?? '[]');

const userTeamId = document.getElementById('joinEventTeamId').value[0] ?? null;
let {
  reportStore,
  disputeStore,
  _initialBracket
} = generateInitialBracket(userTeamId, disputeLevelEnums, userLevelEnums, totalMatches);

let _reportStore = {...reportStore};
let _disputeStore = {...disputeStore};






const validateDisputeCreation = async (data) => {
  const errors = [];

  const requiredFields = [
    'report_id',
    'match_number',
    'event_id',
    'dispute_userId',
    'dispute_teamId',
    'dispute_teamNumber',
    'dispute_reason'
  ];

  for (const field of requiredFields) {
    if (!data[field]) {
      errors.push(`${field} is required`);
    }
  }

  const uniqueQuery = query(
    collection(db, 'disputes'),
    where('event_id', '==', data.event_id),
    where('match_number', '==', data.match_number),
    where('report_id', '==', data.report_id)
  );

  const existingDisputes = await getDocs(uniqueQuery);
  if (!existingDisputes.empty) {
    errors.push('A dispute with this event, match number, and report already exists');
  }

  return errors;
};

function initializeAnalytics() {
  if (window.trackEventViewFromDiv) {
      const analyticsData = document.getElementById('analytics-data');
      console.log(analyticsData);
      if (analyticsData) window.trackEventViewFromDiv(analyticsData);
  }
}


window.onload = () => {
  let eventBannerImg = document.getElementById('eventBannerImg');
  if (eventBannerImg) {
    eventBannerImg.removeAttribute('width');
    eventBannerImg.removeAttribute('height');
  }

  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
        const existingTooltip = window.bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (existingTooltip) {
            existingTooltip.dispose();
        }

        return new window.bootstrap.Tooltip(tooltipTriggerEl);
    });
    
  createApp({
    BracketData: () => BracketData(userLevelEnums, disputeLevelEnums, _initialBracket, reportStore, disputeStore, _reportStore, hiddenUserId, initializeFirebaseAuth, auth, eventId, db, fileStore, validateDisputeCreation),
    UploadData: (type) => UploadData(type, fileStore),
    CountDown
  }).mount('#Bracket');

  const modalIds = ['updateModal', 'reportModal', 'disputeModal'];
  let isModalOpening = false;

  modalIds.forEach(modalId => {
    const modalElement = document.getElementById(modalId);
    
    if (modalElement) {
      modalElement.addEventListener('show.bs.modal', function() {
        isModalOpening = true;
        window.closeAllTippy();
      });

      modalElement.addEventListener('shown.bs.modal', function() {
        isModalOpening = false;
      });

      modalElement.addEventListener('hide.bs.modal', function() {
        setTimeout(() => {
          if (!isModalOpening) {
            window.openAllTippy();
          }
        }, 200);
      });
    }
  });

  initializeAnalytics();

}

