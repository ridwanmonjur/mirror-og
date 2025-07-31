import {
  setDoc, serverTimestamp,
  addDoc, onSnapshot, updateDoc,  doc, query, collection, collectionGroup, getDocs, getDoc, where, or
} from "firebase/firestore";
import { createApp, reactive } from "petite-vue";
import { updateAllCountdowns, diffDateWithNow } from "../custom/brackets";
import { addAllTippy, addDotsToContainer, addTippyToClass } from "../custom/tippy";
import BracketData from "../custom/BracketData";
import UploadData from "../custom/UploadData";
import firebaseService from "../services/firebase.js";


const eventId = document.getElementById('eventId')?.value;
const { db } = firebaseService.initialize();

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
    BracketData: () => BracketData(fileStore),
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

