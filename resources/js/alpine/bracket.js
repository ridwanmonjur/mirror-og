import { initializeApp } from "firebase/app";
import {
  initializeFirestore, memoryLocalCache, setDoc, serverTimestamp,
  addDoc, onSnapshot, updateDoc,  doc, query, collection, collectionGroup, getDocs, getDoc, where, or
} from "firebase/firestore";
// import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";
import { getAuth, signInWithCustomToken, onAuthStateChanged } from "firebase/auth";
import { createApp, reactive } from "petite-vue";
import tippy from 'tippy.js';
import { initialBracketData, calcScores, updateReportFromFirestore, showSwal, createReportTemp, createDisputeDto, generateWarningHtml, updateAllCountdowns, diffDateWithNow } from "../custom/brackets";

window.specialTippy = [];
window.popoverIdToPopover = window.activePopovers || {};
window.ourIdToPopoverId = window.ourIdToPopoverId || {};

function getPopover(element) {
  let popverId = window.ourIdToPopoverId[element];
  if (popverId) {
    let popover = window.popoverIdToPopover[popverId];
    return popover;
  }

  return null;
}

window.hideAll = () => {
  for (let element of window.specialTippy) {
    let popover = getPopover(element);
    popover.hide();
  }
}

window.showAll = () => {
  for (let element of window.specialTippy) {
    let popover = getPopover(element);
    popover?.show();
  }
}

function createTippy(parent, html, trigger, options) {
    return tippy(parent, {
        content: html,
        allowHTML: true,
        placement: 'top',
        trigger,
        triggerTarget: parent,
        // hideOnClick: false,
        // trigger: 'click',
        interactive: true,
        hideOnClick: false,
        delay: [50, 0],
        theme: 'light',
        zIndex: 9999,
        appendTo: document.body,
        ...options,        
    });
}

window.addPopoverWithIdAndHtml = function (parent, html, trigger="click", options = {}, ourId = null) {
    if (!parent || !html) return null;

    if (ourId) {
        
        let popoverId = window.ourIdToPopoverId[ourId];
        if (popoverId) {
          if (popoverId in window.popoverIdToPopover) {
                window.popoverIdToPopover[popoverId].destroy();
                const { [popoverId]: removed, ...rest } = window.popoverIdToPopover;
                window.popoverIdToPopover = rest;
            }
        }
    }
    
    const tippyInstance = createTippy(
        parent, 
        html, 
        trigger, 
        { ...options }
    );

    window.ourIdToPopoverId[ourId] = tippyInstance.id;
    window.popoverIdToPopover[tippyInstance.id] = tippyInstance;
        
    return tippyInstance;
}

window.addPopoverWithIdAndChild = function (parent, child, trigger="click", options = {}, ourId) {
    if (!parent || !child || !child.innerHTML) return null;
    
    return window.addPopoverWithIdAndHtml(parent, child.innerHTML, trigger, options, ourId);
}

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


const parentElements = document.querySelectorAll(".first-item .popover-parent");
parentElements?.forEach(parent => {
  const contentElement = parent.querySelector(".popover-content");
  const parentElement = parent.querySelector(".popover-button");
  if (contentElement) {
    window.addPopover(parentElement, contentElement, 'mouseenter', {
      interactive: false
    });
  }
});


function addAllTippy() {
  const parentSecondElements = document.querySelectorAll(".middle-item");

  parentSecondElements?.forEach(parent => {
    let dataset = parent.dataset;
    let {stage_name: stageName, inner_stage_name: innerStageName} = dataset;
    const triggers = parent.querySelectorAll(".popover-button");
    triggers.forEach((trigger) => {
      let triggerPositionId = trigger.dataset.position;
      let triggerParentsPositionIds = previousValues[triggerPositionId];
      if (triggerParentsPositionIds && Array.isArray(triggerParentsPositionIds)) {
        let classNamesJoined = triggerParentsPositionIds.join(".");
        let triggerClassName = '.popover-middle-content.' + classNamesJoined;
        let contentElement = document.querySelector(triggerClassName);
        if (contentElement) {
       
          if (contentElement.classList.contains('warning') && !(
            stageName == 'L' && ['e1', 'e3', 'e5'].includes(innerStageName)
          )) {
            let {
              diffDate, position
            } = contentElement.dataset;

            if (triggerParentsPositionIds.includes(position)) {
              let tippyId = position + '+' + triggerPositionId;
              let popover = window.addPopoverWithIdAndHtml(trigger, generateWarningHtml(diffDate, triggerPositionId), 'manual', {
                  onShow(instance) {
                    const tippyBox = instance.popper;
                    tippyBox.addEventListener('click', () => {
                      instance.hide();
                  });
                  }
                }, tippyId);
              window.specialTippy = [...window.specialTippy, tippyId] 
            }
          }

          window.addPopoverWithIdAndChild(trigger, contentElement, 'mouseenter', {
            interactive: false
          }, classNamesJoined + '/' + triggerPositionId);
        }
      }
    })
  });
}


function addTippyToClass(classAndPositionList) {
  for (let classX of classAndPositionList) {
    let [triggerClass, prevClass] = classX;
    const triggers = document.querySelectorAll(`.popover-button.data-position-${triggerClass}`);
    triggers.forEach((trigger) => {
      let triggerClassName = '.popover-middle-content.' + prevClass;
      let contentElement = document.querySelector(triggerClassName);
      window.addPopoverWithIdAndHtml(trigger, contentElement, 'mouseenter', {
        interactive: false
      }, prevClass + '/' + triggerClassName);
    });
  }
}
window.addTippyToClass = addTippyToClass;

function addDotsToContainer(key, value) {

  let parent = document.querySelector(`.${key}.popover-middle-content`);
  let table = document.querySelector(`.${key}.tournament-bracket__table`);
  let dottedScoreContainer = parent?.querySelectorAll('.dotted-score-container');
  let dottedScoreBox = parent?.querySelectorAll('.dotted-score-box');
  let statusBox = parent?.querySelectorAll('.status-box');
  let dottedScoreTable = table?.querySelectorAll('.dotted-score-box');
  dottedScoreContainer?.forEach((element, index) => {
    element.querySelectorAll('.dotted-score')?.forEach((dottedElement, dottedElementIndex) => {
      if (value.realWinners[dottedElementIndex]) {
        if (value.realWinners[dottedElementIndex] == index) {
          dottedElement.classList.remove('bg-secondary', 'bg-red', 'd-none');
          dottedElement.classList.add("bg-success");
        } else {
          dottedElement.classList.remove('bg-secondary', 'bg-success', 'd-none');
          dottedElement.classList.add("bg-red");
        }
      } else {
        dottedElement.classList.remove('bg-success', 'bg-red', 'd-none');
        dottedElement.classList.add('bg-secondary');
      }
    })
  });

  dottedScoreBox?.forEach((element, index) => {
    element.innerHTML = value['score'][index];
  });

  dottedScoreTable?.forEach((element, index) => {
    element.innerHTML = value['score'][index];
  });

  statusBox?.forEach((element, index) => {
    if ('completeMatchStatus' in value) {
      element.innerHTML = value['completeMatchStatus'];
    }
  });

}

async function getAllMatchStatusesData() {
  const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/brackets`);
  const allMatchStatusesQ = query(allMatchStatusesCollectionRef);
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

const userTeamId = document.getElementById('joinEventTeamId').value[0] ?? null;
let initialData = initialBracketData(userTeamId);

function BracketData() {

  const userLevelEnums = JSON.parse(document.getElementById('userLevelEnums' ?? '[]').value);
  const disputeLevelEnums = JSON.parse(document.getElementById('disputeLevelEnums' ?? '[]').value);
  
  return {
    ...initialData,
    userLevelEnums,
    disputeLevelEnums,
   
    async onSubmitSelectTeamToWin() {
      let teamNumber = this.reportUI.teamNumber;
      let otherTeamNumber = this.reportUI.otherTeamNumber;
      let matchNumber = this.reportUI.matchNumber;
      let selectedTeamIndex = document.getElementById('selectedTeamIndex').value;
      let update = createReportTemp(this.report);

      const result = await showSwal({
        title: 'Choosing the winner',
        html: `Are you sure you want to choose the winner?`,
        confirmButtonText: 'Make Choice',
      });

      if (result.isConfirmed) {
        let validateData = {
          'team1_id' : this.report.teams[0].id,
          'team1_position': this.report.teams[0].position,
          'team2_id' : this.report.teams[1].id,
          'team2_position': this.report.teams[1].position,
          'willCheckDeadline': this.report.userLevel != this.userLevelEnums['IS_ORGANIZER']        
        };

        try {
          const csrfToken5 = document.querySelector('meta[name="csrf-token"]').content;
          let response = await fetch(`/api/event/${eventId}/brackets`, {
            method: 'POST',
            body: JSON.stringify(validateData),
            headers: {
              'X-CSRF-TOKEN': csrfToken5,
              'Content-Type': 'application/json',
            }
          });

          response = await response.json(); 
          if (!response.success) {
            window.toastError(response.message || 'An error has occurred!');
            return;
          }

          if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
            update.organizerWinners[matchNumber] = selectedTeamIndex;
            update.realWinners[matchNumber] = selectedTeamIndex;
            update.score = calcScores(update);     
          }
  
          if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
            validateData['my_team_id'] = this.report.teams[teamNumber].id;
            update.teams[teamNumber].winners[matchNumber] = selectedTeamIndex;
            let otherTeamWinner = this.report.teams[otherTeamNumber].winners[matchNumber];
            if (otherTeamWinner) {
              if (otherTeamWinner === selectedTeamIndex) {
                update.realWinners[matchNumber] = selectedTeamIndex;
              }
            }
            update.score = calcScores(update);        
          }
  
          await this.saveReport(update);

        } catch (error) {
          window.toastError("Problem updating data");
        }
      }
    },
    async onChangeTeamToWin() {
      const result = await showSwal({
        title: 'Changing the winner',
        html: `Are you sure you want to change the winner?`,
        confirmButtonText: 'Change Declaration',
      });
     
      if (result.isConfirmed) {
        let matchNumber = this.reportUI.matchNumber;
        let update = createReportTemp(this.report);
        if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
          let otherIndex = this.report.realWinners[matchNumber] === "1" ? "0" : "1";
          update.organizerWinners[matchNumber] = otherIndex;
          update.realWinners[matchNumber] = otherIndex;
          update.score = calcScores(update);
        }

        if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
          let teamNumber = this.reportUI.teamNumber;
          let otherTeamNumber = this.reportUI.otherTeamNumber;
          let otherIndex = this.report.teams[otherTeamNumber].winners[matchNumber];
          if (otherIndex) {
            update.teams[teamNumber].winners[matchNumber] = otherIndex;
            update.realWinners[matchNumber] = otherIndex;
          }
          update.score = calcScores(update);
        }

        await this.saveReport(update);
      }
    },
    
    async onRemoveTeamToWin() {
      const result = await showSwal({
        title: 'Remove the winner',
        html: `Are you sure you want to remove the winner?`,
        confirmButtonText: 'Change Declaration',
      });


      if (result.isConfirmed) {
        let matchNumber = this.reportUI.matchNumber;
        let update = createReportTemp(this.report);
        update.organizerWinners[matchNumber] = null;
        update.realWinners[matchNumber] = null;
        update.score = calcScores(update);

        await this.saveReport(update);
      }
    },

    async resolveDisputeForm(event) {
      event.preventDefault();
      const form = event.currentTarget;
      const formData = Object.fromEntries(new FormData(form));
      let {
        id,
        match_number,
        resolution_winner,
        resolution_resolved_by,
        already_winner
      } = formData;

        const missingFields = [];
        if (!id) missingFields.push('ID');
        if (!match_number) missingFields.push('Match Number');
        if (!resolution_resolved_by) missingFields.push('Resolver');

        if (missingFields.length > 0) {
          window.toastError(`Missing required fields: ${missingFields.join(', ')}`);
          return;
        }
        
      if (!resolution_winner) {
        resolution_winner = already_winner == '0' ? '1' : '0';
      }

      const disputeRef = doc(db, `event/${eventId}/disputes`, id);
      const updateData = {
        resolution_winner,
        resolution_resolved_by,
        updated_at: serverTimestamp(),
      };

      await updateDoc(disputeRef, updateData);
      const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/brackets`);
      const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`;
      const reportRef = doc(allMatchStatusesCollectionRef, customDocId);

      try {
        let newRealWinners = [...this.report.realWinners];
        let matchStatusNew = [...this.report.matchStatus];
        let disputeResolved = [...this.report.disputeResolved];
        disputeResolved[this.reportUI.matchNumber] = true;
        if (match_number != 2) {
          matchStatusNew[Number(this.reportUI.matchNumber)+1] = "ONGOING";
        }
        newRealWinners[this.reportUI.matchNumber] = resolution_winner;
        
        let updatedRemaining = {
          matchStatus: matchStatusNew,
          completeMatchStatus: match_number == 2 ? "ENDED": "ONGOING",
          realWinners: newRealWinners,
          disputeResolved,
        };

        updatedRemaining['score'] = calcScores(updatedRemaining);
        await updateDoc(reportRef, updatedRemaining);        
        if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) {
          this.setDisabled({...this.reportUI, matchNumber: Number(match_number)});
        }  else {
          this.reportUI = {
            ...this.reportUI, matchNumber: Number(match_number)
          }
        }
      } catch (error) {
        console.error("Error adding document: ", error);
      }
    },
    async decideResolution(event, teamNumber) {
      let button = event.currentTarget;
      document.querySelectorAll(".selectedDisputeResolveButton").forEach((value) => { value.classList.remove('bg-primary', 'text-light'); }
      );

      button.classList.add('bg-primary', 'text-light');
      document.getElementById('resolution_winner_input').value = teamNumber;
    },
    async saveReport(tempState) {
      const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/brackets`);
      const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`;
      const docRef = doc(allMatchStatusesCollectionRef, customDocId);

      try {
        let firestoreDoc = {
          score: [tempState?.score[0] ?? "0", tempState?.score[1] ?? "0"],
          matchStatus: [...tempState.matchStatus],
          realWinners: tempState.realWinners,
          organizerWinners: tempState.organizerWinners,
          team1Winners: tempState.teams[0]?.winners,
          team2Winners: tempState.teams[1]?.winners,
          team1Id: tempState.teams[0].id,
          team2Id: tempState.teams[1].id,
          position: tempState.position,
          completeMatchStatus: tempState.completeMatchStatus,
          randomWinners: [...tempState.randomWinners],
          defaultWinners: [...tempState.defaultWinners],
          disqualified: tempState.disqualified,
          disputeResolved: [...tempState.disputeResolved]
        };

        await setDoc(docRef, firestoreDoc);
        this.report = updateReportFromFirestore(this.report, firestoreDoc);

        if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) {
          this.setDisabled(this.reportUI);
        } 
      } catch (error) {
        console.error("Error adding document: ", error);
      }
    },
    toggleResponseForm(classToShow, classToHide) {
      document.querySelector(`.${classToShow}`).classList.toggle("d-none");
      document.querySelector(`.${classToHide}`).classList.toggle("d-none");
    },
    showImageModal(imgPath, mediaType) {
      const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('imageModal'));
      const imagePreview = document.getElementById('imagePreview');
      const videoPreview = document.getElementById('videoPreview');
      const videoSource = document.getElementById('videoSource');
      imagePreview.style.display = 'none';
      videoPreview.style.display = 'none';
      
      if (mediaType === 'image') {
          imagePreview.src = '/storage/' + imgPath;
          imagePreview.style.display = 'block';
      } else {
          videoSource.src = '/storage/' + imgPath;
          videoPreview.load(); 
          videoPreview.style.display = 'block';
      }
      
      if (modal) modal.show();
    },

    selectTeamToWin(event, index) {
      this.clearSelection();
      let selectedButton = event.currentTarget;
      if (selectedButton) {
        selectedButton.style.backgroundColor = '#43A4D7';
        selectedButton.style.color = 'white';
      }

      const selectTeamSubmitButton = document.querySelector('.selectTeamSubmitButton');
      selectTeamSubmitButton.style.backgroundColor = '#43A4D7';
      selectTeamSubmitButton.style.color = 'white';

      document.getElementById('selectedTeamIndex').value = index;
      this.reportUI.statusText = `Currently selecting ${this.report.teams[index].name} as the winner of Game ${this.reportUI.matchNumber + 1}`;

    },

    clearSelection() {
      let selectionButtons = document.querySelectorAll('.selectedButton');
      selectionButtons.forEach((selectedButton) => {
        if (selectedButton) {
          selectedButton.style.backgroundColor = 'white';
          selectedButton.style.color = 'black';
          selectedButton = null;
        }

        const selectTeamSubmitButton = document.querySelector('.selectTeamSubmitButton');
        selectTeamSubmitButton.style.backgroundColor = '#white';
        selectTeamSubmitButton.style.color = 'black';

        document.getElementById('selectedTeamIndex').value = null;

        this.reportUI.statusText = '';

      });
    },

    changeMatchNumber(increment) {
      let newNo = Number(this.reportUI.matchNumber) + Number(increment);
      let demoNo = newNo + 1;
      this.reportUI = {
        ...this.reportUI,
        matchNumber: newNo,
        statusText: this.reportUI.disabled[newNo] ?
          'Selection is not yet available.' :
          `Select a winner for Game ${demoNo}`
      };

    },
    
    getDisabled() {
      return this.reportUI.disabled[this.reportUI.matchNumber];
    },
    
    setDisabled(reportUI = {}) {
      let disabledList = [false, false, false];

      disabledList[1] =
        this.report.teams[this.reportUI.teamNumber]?.winners[0] === null;
      disabledList[2] =
        this.report.teams[this.reportUI.teamNumber]?.winners[1] === null;

      this.reportUI = {
        ...reportUI,
        disabled: [...disabledList]
      };
    },
    
    resetDotsToContainer() {
      let parent = document.getElementById('reportModal');
      let dottedScoreContainer = parent.querySelectorAll('.dotted-score-container');
      dottedScoreContainer.forEach((element) => {
        element.querySelectorAll('.dotted-score')?.forEach((dottedElement, dottedElementIndex) => {
          dottedElement.classList.remove('bg-success', 'bg-red');
          dottedElement.classList.add('bg-secondary');
          if (dottedElementIndex == 2) {
            dottedElement.classList.add('d-none');
          }
        });
      });
    },
    
    async init() {
      console.log(">>>init>>>");
      if (hiddenUserId) {
        const { user, claims } = await initializeFirebaseAuth();
        this.firebaseUser = user;
        this.userClaims = claims;
        this.isInitialized = true;

        onAuthStateChanged(auth, (user) => {
          if (user) {
            user.getIdTokenResult().then(idTokenResult => {
              this.firebaseUser = { ...user, ...idTokenResult.claims };
            });
          } else {
            this.firebaseUser = null;
            this.userClaims = null;
          }
        });
      }

      window.addEventListener('changeReport', (event) => {
        window.showLoading();
        let newReport = {}, newReportUI = {};
        let eventUpdate = event?.detail ?? null;
        this.clearSelection();
        if (this.subscribeToMatchStatusesSnapshot)
          this.subscribeToMatchStatusesSnapshot();
        if (this.subscribeToCurrentReportDisputesSnapshot)
          this.subscribeToCurrentReportDisputesSnapshot();

        let teamNumber = eventUpdate?.user_level == this.userLevelEnums['IS_TEAM1'] ? 0 :
          (eventUpdate.user_level == this.userLevelEnums['IS_TEAM2'] ? 1 : 0);

        let otherTeamNumber = teamNumber === 0 ? 1 : 0;
        if (!eventUpdate) {
          newReport = {
            ...initialData.report,
          };

          newReportUI = {
            ...initialData.reportUI,
          }
        } else {
          this.resetDotsToContainer();

          newReportUI = {
            ...initialData.reportUI,
            teamNumber,
            otherTeamNumber,
          }

          newReport = {
            ...initialData.report,
            position: eventUpdate.position ?? initialData.report.position,
            userLevel: eventUpdate.user_level ?? initialData.report.userLevel,
            deadline: eventUpdate.deadline ?? null,
            teams: [
              {
                ...initialData.report.teams[0],
                winners: initialData.report.teams[0].winners,
                id: eventUpdate.team1_id,
                position: eventUpdate.team1_position,
                banner: eventUpdate.team1_teamBanner,
                name: eventUpdate.team1_teamName ?? '-'
              },
              {
                ...initialData.report.teams[0],
                winners: initialData.report.teams[1].winners,
                id: eventUpdate.team2_id,
                position: eventUpdate.team2_position,
                banner: eventUpdate.team2_teamBanner,
                name: eventUpdate.team2_teamName ?? '-'
              }
            ],
          };
        }


        this.getCurrentReportSnapshot(eventUpdate.classNamesWithoutPrecedingDot, newReport, newReportUI);
      });

      // Swal.close();
    },
    destroy() {
      if (this.subscribeToMatchStatusesSnapshot) this.subscribeToMatchStatusesSnapshot();
    },

    getCurrentReportDisputeSnapshot(classNamesWithoutPrecedingDot) {
      const disputesRef = collection(db, `/event/${eventId}/disputes`);
      const disputeQuery = query(
        disputesRef,
        where('report_id', '==', classNamesWithoutPrecedingDot),
        where('event_id', '==', eventId)
      );

      let subscribeToCurrentReportDisputesSnapshot = onSnapshot(
        disputeQuery,
        async (disputeSnapshot) => {
          let allDisputes = [null, null, null];
          disputeSnapshot.docChanges().forEach((change) => {
            let data = change.doc.data();
            let id = change.doc.id;

            if (change.type === "added"|| change.type == "modified") {
              allDisputes[data['match_number']] = {
                ...data, id
              };
            }
          });

          console.log({allDisputes});

          this.dispute = [...allDisputes];
        }
      );

      this.subscribeToCurrentReportDisputesSnapshot = subscribeToCurrentReportDisputesSnapshot;
      window.closeLoading()
    },

    getCurrentReportSnapshot(classNamesWithoutPrecedingDot, newReport, newReportUI) {
      const currentReportRef = doc(db, `event/${eventId}/brackets`, classNamesWithoutPrecedingDot);
      let initialLoad = true;

      let subscribeToCurrentReportSnapshot = onSnapshot(
        currentReportRef,
        async (reportSnapshot) => {
          if (reportSnapshot.exists()) {
            let data = reportSnapshot.data();
            data['id'] = reportSnapshot.id;
            this.report = updateReportFromFirestore(newReport, data)
            if (this.report.userLevel != this.userLevelEnums['IS_ORGANIZER']) {
              if (!initialLoad) {
                let matchNumber = this.reportUI.matchNumber;
                newReportUI['matchNumber'] = matchNumber;
              }

              this.setDisabled(newReportUI);
            }

            let dottedScoreContainer = document.querySelectorAll('#reportModal .dotted-score-container');
            dottedScoreContainer.forEach((element, index) => {
              element.querySelectorAll('.dotted-score')?.forEach((dottedElement, dottedElementIndex) => {
                if (this.report.realWinners[dottedElementIndex]) {
                  if (this.report.realWinners[dottedElementIndex] == index) {
                    dottedElement.classList.remove('bg-secondary', 'bg-red', 'd-none');
                    dottedElement.classList.add("bg-success");
                  } else {
                    dottedElement.classList.remove('bg-secondary', 'bg-success', 'd-none');
                    dottedElement.classList.add("bg-red");
                  }
                } else {
                  dottedElement.classList.remove('bg-success', 'bg-red', 'd-none');
                  dottedElement.classList.add('bg-secondary');
                }
              })
            })

            this.getCurrentReportDisputeSnapshot(classNamesWithoutPrecedingDot);
            initialLoad = false;
          } else {
            this.report = { ...newReport };
            this.reportUI = { ...newReportUI }
            this.dispute = [null, null, null];
            window.closeLoading();
            initialLoad = false;
          }
        }
      );

      this.subscribeToCurrentReportSnapshot = subscribeToCurrentReportSnapshot;
    },
    async submitDisputeForm(event) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      let formObject = {}
      for (let [key, value] of formData.entries()) {
        formObject[key] = value;
      }

      const selectedRadio = formObject['reportReason'];
      const otherReasonInput = formObject['otherReasonText'];
      let dispute_reason = '';
      if (selectedRadio) {
        dispute_reason = selectedRadio;
      }
      else if (otherReasonInput) {
        dispute_reason = otherReasonInput.trim();
      }

      const { reportReason, otherReasonText, ...newFormObject } = formObject;
      formObject = null;
      newFormObject['dispute_reason'] = dispute_reason;
      const result = await showSwal({
        title: 'Submitting a Dispute',
        html: `
          <div class="text-left">
              <p class="mt-2 mb-2">A total of TWO (2) dispute submissions are allocated to each team per event. 
              A dispute submission will only be consumed when a dispute is submitted.</p>
              
              <p class="mb-2">You have TWO (2) dispute submissions remaining. Submitting this dispute will consume
              one dispute submission and you will have ONE (1) dispute submission remaining.</p>
              
              <p class="mb-2">If this dispute is resolved in your favour, a dispute submission will be returned to you.</p>
              
          </div>
        `,
        confirmButtonText: 'Submit Dispute',
      });
      
        if (result.isConfirmed) {
          try {
            let { files } = await fileStore.uploadToServer('claim');
      
            let disputeDto = createDisputeDto(newFormObject, files);
            validateDisputeCreation(disputeDto);
            let newDisputeId = `${this.report.teams[0].position}.`+`${this.report.teams[1].position}.${this.reportUI.matchNumber}`;
            const disputesRef = doc(db, `event/${eventId}/disputes`, newDisputeId);
            await setDoc(disputesRef, disputeDto);
            const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/brackets`);
            const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`;
            const reportRef = doc(allMatchStatusesCollectionRef, customDocId);
            let disputeResolved = [...this.report.disputeResolved];
            disputeResolved[this.reportUI.matchNumber] = false;
            let updatedRemaining = {
              disputeResolved
            };

            await updateDoc(reportRef, updatedRemaining);
            this.setDisabled({...this.reportUI});

            window.Toast.fire({
              icon: 'success',
              text: "Successfully created!"
            });
          } catch (error) {
            console.error("Error adding document: ", error);
          }
        }
    },

    async respondDisputeForm(event) {
      event.preventDefault();
      const form = event.target;
      const formData = Object.fromEntries(new FormData(form));
      const {
        id,
        dispute_teamId,
        response_teamNumber,
        dispute_description,
        match_number,
        response_userId
      } = formData;

      let missingFields = [];
      if (!id || !dispute_teamId || !response_teamNumber) {
        if (!id) missingFields.push("ID");
        if (!dispute_teamId) missingFields.push('Disputer Team ID');
        if (!response_teamNumber) missingFields.push('Response Team ID');
        window.toastError(`Missing required fields: ${missingFields.join(', ')}`);
        return;
      }

      const result = await showSwal({
        title: 'Responding to a Dispute',
        html: `
            <p class="mt-2 mb-2">An opponent team has raised a dispute and requires your response.</p>
            <p class="mt-2">Responding to a dispute does not consume any dispute submissions.</p>
            <p class="mt-2">A dispute submission is only consumed when you raise a dispute and submit it.</p>
            <p class="mt-2">A dispute submission will be returned if a dispute resolves in the disputing team's favour.</p>
        `,
        confirmButtonText: 'Continue',
        width: 500,

      });
    
        if (result.isConfirmed) {
          let { files } = await fileStore.uploadToServer('response');

          const disputeRef = doc(db, `event/${eventId}/disputes`, id);

          const updateData = {
            response_teamId: dispute_teamId,
            response_teamNumber: response_teamNumber,
            response_explanation: dispute_description || null,
            response_userId,
            response_image_videos: files,
            updated_at: serverTimestamp(),
            status: 'responded'
          };

          await updateDoc(disputeRef, updateData);
          let disputeResolved = [...this.report.disputeResolved];
          disputeResolved[this.reportUI.matchNumber] = false;
          let updatedRemaining = {
            disputeResolved
          };

          await updateDoc(reportRef, updatedRemaining);
          
        } else {
          handleCancelResponse();
        }
      }
    
  };

}

function UploadData (type) {
  return {
    get inputFiles() {
      return fileStore.getFiles(type)
    },

    handleFiles(event) {
      if (!event.target?.files) return;

      const newFiles = Array.from(event.target?.files);
      newFiles?.forEach(file => {
        if (!(file.type.startsWith('image/') || file.type.startsWith('video/'))) {
          window.toastError("Only images and videos are supported");
          return;
        }
      });

      
      fileStore.addFiles(newFiles, type);
      
      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);

      uploadArea.innerHTML = "";

      this.inputFiles.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
          this.createImgPreview(file, index);
        } else {
          this.createVideoPreview(file, index);
        }
      });
      event.target.value = '';
    },
    
    clickInput() {
      const fileInput = document.querySelector(`#${type}Id .file-input`);
      fileInput?.click()
    },
    
    createVideoPreview(file, index) {
      const preview = document.createElement('div');
      preview.className = 'preview-item me-2';

      // Create video icon SVG
      const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      svg.setAttribute("viewBox", "0 0 24 24");
      svg.setAttribute("width", "64");
      svg.setAttribute("height", "64");

      const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
      path.setAttribute("d", "M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z");
      path.setAttribute("fill", "#666666");
      svg.appendChild(path);

      const deleteBtn = document.createElement('button');
      deleteBtn.innerHTML = '×';
      deleteBtn.className = 'delete-btn';
      deleteBtn.addEventListener('click', () => {
        preview.remove();
        fileStore.clearFilesByIndex(type, index);
      });

      const fileName = document.createElement('small');
      fileName.textContent = file.name;

      preview.appendChild(svg);
      preview.appendChild(deleteBtn);
      preview.appendChild(fileName);

      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);
      const plusButton = uploadArea.querySelector('.plus-button');
      uploadArea.insertBefore(preview, plusButton);
    },
    createImgPreview(file, index) {
      const preview = document.createElement('div');
      preview.className = 'preview-item loading me-2';

      const img = document.createElement('img');
      img.addEventListener('load', () => {
        preview.classList.remove('loading');
      });

      const deleteBtn = document.createElement('button');
      deleteBtn.className = 'delete-btn';
      deleteBtn.innerHTML = '×';
      deleteBtn.addEventListener('click', () => {
        preview.remove();
        fileStore.clearFilesByIndex(type, index);
      });

      const reader = new FileReader();
      reader.onload = (e) => {
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);

      const fileName = document.createElement('small');
      fileName.textContent = file.name;

      preview.appendChild(img);
      preview.appendChild(deleteBtn);
      preview.appendChild(fileName);

      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);
      const plusButton = uploadArea.querySelector('.plus-button');
      uploadArea.insertBefore(preview, plusButton);
    },

    getImages() {
      const uploadArea = document.querySelector(`#${type}Id #uploadArea`);

      return Array.from(uploadArea.querySelectorAll('.preview-item img'))
        .map(img => img.src);
    }
  };
}



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
  console.log("zzzzz");
  if (window.analytics) {
      const analyticsData = document.getElementById('analytics-data');
      window.window.trackEventViewFromDiv(analyticsData);
  }
}


window.onload = () => {
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
        // Remove existing tooltip if it exists
        const existingTooltip = window.bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (existingTooltip) {
            existingTooltip.dispose();
        }
        // Create new tooltip
        return new window.bootstrap.Tooltip(tooltipTriggerEl);
    });
    
  createApp({
    BracketData,
    UploadData,
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

