import { initializeApp } from "firebase/app";
import {
  initializeFirestore, memoryLocalCache, setDoc, serverTimestamp,
  addDoc, onSnapshot, updateDoc,  doc, query, collection, collectionGroup, getDocs, getDoc, where, or
} from "firebase/firestore";
// import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";
import { getAuth, signInWithCustomToken, onAuthStateChanged } from "firebase/auth";
import { createApp, reactive } from "petite-vue";


const eventId = document.getElementById('eventId')?.value;

window.updateReportDispute = async (reportId, team1Id, team2Id) => {
  const reportRef = doc(db, `event/${eventId}/match_status`, reportId);
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
    const triggers = parent.querySelectorAll(".popover-button");
    triggers.forEach((trigger) => {
      let triggerPositionId = trigger.dataset.position;
      let triggerParentsPositionIds = previousValues[triggerPositionId];
      if (triggerParentsPositionIds && Array.isArray(triggerParentsPositionIds)) {
        let triggerClassName = '.popover-middle-content.' + triggerParentsPositionIds.join(".");
        let contentElement = document.querySelector(triggerClassName);
        window.addPopover(trigger, contentElement, 'mouseenter', {
          interactive: false
        });
      }
    })
  });
}

function addTippyToClass(classAndPositionList) {
  for (let classX of classAndPositionList) {
    const triggers = document.querySelectorAll(`.popover-button.data-position-${classX[1]}`);
    triggers.forEach((trigger) => {
      let triggerClassName = '.popover-middle-content.' + classX[0];
      let contentElement = document.querySelector(triggerClassName);
      window.addPopover(trigger, contentElement, 'mouseenter', {
        interactive: false
      });
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

function getAllMatchStatusesData() {
  const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);
  const allMatchStatusesQ = query(allMatchStatusesCollectionRef);
  let allDataList = {}, modifiedDataList = {}, newDataList = {};
  let newClassList = [], modifiedClassList = [];
  let isAddedActionType = true, isLoadedActionType = false;
  window.showLoading();

  onSnapshot(allMatchStatusesQ, async (reportSnapshot) => {
    reportSnapshot.docChanges().forEach((change) => {
      if (change.type === "added") {
        isAddedActionType = true;
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
  
    await window.closeLoading();
  });
}

getAllMatchStatusesData();


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


function BracketData() {

  const userLevelEnums = JSON.parse(document.getElementById('userLevelEnums' ?? '[]').value);
  const userTeamId = document.getElementById('joinEventTeamId').value[0] ?? null;
  let initialData = {
    firebaseUser: null,
    disputeLevelEnums: {
      'ORGANIZER': 1,
      'DISPUTEE': 2,
      'RESPONDER': 3
    },
    subscribeToMatchStatusesSnapshot: null,
    subscribeToCurrentReportSnapshot: null,
    subscribeToCurrentReportDisputesSnapshot: null,
    reportUI: {
      matchNumber: 0,
      userTeamId,
      teamNumber: 0,
      otherTeamNumber: 1,
      disabled: [false, false, false],
      statusText: 'Select a winner for Game 1'
    },
    report: {
      id: null,
      organizerWinners: [null, null, null],
      realWinners: [null, null, null],
      userLevel: userLevelEnums['IS_PUBLIC'],
      completeMatchStatus: 'UPCOMING',
      matchStatus: ['UPCOMING', 'UPCOMING', 'UPCOMING'],
      teams: [
        {
          winners: [null, null, null],
          id: null,
          position: "",
          banner: "",
          name: "No team chosen yet",
          score: 0,
        },
        {
          id: null,
          position: "",
          banner: "",
          name: "No team chosen yet",
          winners: [null, null, null],
          score: 0,
        }
      ],
      position: ""
    },
    dispute: [
      null,
      null,
      null
    ],
  };
  
  return {
    ...initialData,
    userLevelEnums,
    calcScores(update) {
      let score1 = update.realWinners?.reduce((acc, value) => acc + (value == "0" ? 1 : 0), 0);
      let score2 = update.realWinners?.reduce((acc, value) => acc + (value == "1" ? 1 : 0), 0);
      return [score1, score2];
    },
    async onSubmitSelectTeamToWin() {
      let teamNumber = this.reportUI.teamNumber;
      let otherTeamNumber = this.reportUI.otherTeamNumber;
      let matchNumber = this.reportUI.matchNumber;
      let selectedTeamIndex = document.getElementById('selectedTeamIndex').value;
      let update = {
        organizerWinners: [...this.report.organizerWinners],
        matchStatus: [...this.report.matchStatus],
        realWinners: [...this.report.realWinners],
        teams: [...this.report.teams],
        position: this.report.position,
        completeMatchStatus: matchNumber == 2 ? "ENDED": "ONGOING"
      };

      update.matchStatus[matchNumber] = "ENDED";
      if (matchNumber != 2) {
        update.matchStatus[matchNumber+1] = "ONGOING";
      }

      if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
        update.organizerWinners[matchNumber] = selectedTeamIndex;
        update.realWinners[matchNumber] = selectedTeamIndex;
        update.score = this.calcScores(update);
      }

      if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
        update.teams[teamNumber].winners[matchNumber] = selectedTeamIndex;
        let otherTeamWinner = this.report.teams[otherTeamNumber].winners[matchNumber];
        if (otherTeamWinner) {
          if (otherTeamWinner === selectedTeamIndex) {
            update.realWinners[matchNumber] = selectedTeamIndex;
          }
        }
        update.score = this.calcScores(update);

      }

      try {
        await this.saveReport(update);

      } catch (error) {
        window, toastError("Problem updating data");
      }

    },
    async onChangeTeamToWin() {
      window.Swal.fire({
        title: 'Changing the winner',
        html: `
            Are you sure you want to change the winner?
        `,
        showCancelButton: true,
        confirmButtonColor: "#43A4D7",
        confirmButtonText: 'Change Declaration',
        cancelButtonText: 'Back',
        padding: '2em',
        confirmButtonColor: '#43A4D7',
        reverseButtons: true,
      }).then(async (result) => {
        if (result.isConfirmed) {
          let matchNumber = this.reportUI.matchNumber;

          let update = {
            organizerWinners: [...this.report.organizerWinners],
            matchStatus: [...this.report.matchStatus],
            realWinners: [...this.report.realWinners],
            teams: [...this.report.teams],
            position: this.report.position,
            completeMatchStatus: matchNumber == 2 ? "ENDED": "ONGOING"
          };
    
          update.matchStatus[matchNumber] = "ENDED";
          if (matchNumber != 2) {
            update.matchStatus[matchNumber+1] = "ONGOING";
          }

          if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
            let otherIndex = this.report.realWinners[matchNumber] === "1" ? "0" : "1";
            update.organizerWinners[matchNumber] = otherIndex;
            update.realWinners[matchNumber] = otherIndex;
            update.score = this.calcScores(update);

          }

          if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
            let teamNumber = this.reportUI.teamNumber;
            let otherTeamNumber = this.reportUI.otherTeamNumber;
            let otherIndex = this.report.teams[otherTeamNumber].winners[matchNumber];
            if (otherIndex) {
              update.teams[teamNumber].winners[matchNumber] = otherIndex;
              update.realWinners[matchNumber] = otherIndex;
            }
            update.score = this.calcScores(update);
          }

          await this.saveReport(update);
        }
      });
    },
    
    async onRemoveTeamToWin() {
      window.Swal.fire({
        title: 'Remove the winner',
        html: `
            Are you sure you want to remove the winner?
        `,
        confirmButtonColor: "#43A4D7",
        showCancelButton: true,
        confirmButtonText: 'Remove Declaration',
        cancelButtonText: 'Back',
        padding: '2em',
        confirmButtonColor: '#43A4D7',
        reverseButtons: true,
      }).then(async (result) => {
        if (result.isConfirmed) {
          let matchNumber = this.reportUI.matchNumber;

          let update = {
            organizerWinners: [...this.report.organizerWinners],
            matchStatus: [...this.report.matchStatus],
            realWinners: [...this.report.realWinners],
            teams: [...this.report.teams],
            position: this.report.position,
            completeMatchStatus: matchNumber == 2 ? "ENDED": "ONGOING"
          };
    
          update.matchStatus[matchNumber] = "ENDED";
          if (matchNumber != 2) {
            update.matchStatus[matchNumber+1] = "ONGOING";
          }

          update.organizerWinners[matchNumber] = null;
          update.realWinners[matchNumber] = null;
          update.score = this.calcScores(update);

          await this.saveReport(update);
        }
      });
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

      const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);
      const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`;
      const docRef = doc(allMatchStatusesCollectionRef, customDocId);

      try {
        let winnerNew = [...this.report.realWinners];
        let updatedRemaining = {
          matchStatus: [...this.report.matchStatus],
          completeMatchStatus: match_number == 2 ? "ENDED": "ONGOING"
        };
  
        updatedRemaining.matchStatus[match_number] = "ENDED";
        if (match_number != 2) {
          updatedRemaining.matchStatus[Number(match_number)+1] = "ONGOING";
        }
        
        winnerNew[this.reportUI.matchNumber] = resolution_winner;
        await updateDoc(docRef, {
          winners: winnerNew,
          ...updatedRemaining
        });

        const docSnap = await getDoc(docRef);
        if (docSnap.exists()) {
          let id = docRef.id;
          let data = docSnap.data();

          this.report = {
            ...this.report,
            realWinners : [...winnerNew]
          };

          this.dispute = this.dispute.map((item, index) => 
            index == updateData[this.reportUI.matchNumber] ? { ...data, id } : item
          );
        
          if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) {
            this.setDisabled(this.reportUI);
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
    async saveReport(report) {
      const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);
      const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`;
      const docRef = doc(allMatchStatusesCollectionRef, customDocId);

      try {
        let _report = {};
        _report['score'] = [report?.score[0] ?? "0", report?.score[1] ?? "0"];
        _report['matchStatus'] = report.matchStatus;
        _report['realWinners'] = report.realWinners;
        _report['organizerWinners'] = report.organizerWinners;
        _report['team1Winners'] = report.teams[0]?.winners;
        _report['team2Winners'] = report.teams[1]?.winners;
        _report['team1Id'] = report.teams[0].id;
        _report['team2Id'] = report.teams[1].id;
        _report['position'] = report.position;
        _report['completeMatchStatus'] = report.completeMatchStatus;
        await setDoc(docRef, _report);

        this.report = {
          ...this.report,
          organizerWinners: _report.organizerWinners,
          id: _report['id'],
          matchStatus: _report.matchStatus,
          completeMatchStatus: _report.completeMatchStatus,
          realWinners: _report.realWinners,
          teams: [
            {
              ...this.report.teams[0],
              score: _report.score[0],
              winners: _report.team1Winners
            },
            {
              ...this.report.teams[1],
              score: _report.score[1],
              winners: _report.team2Winners
            }
          ]
        };

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
      this.reportUI = {
        ...this.reportUI,
        matchNumber: this.reportUI.matchNumber + increment,
        statusText: this.reportUI.disabled[this.reportUI.matchNumber + increment] ?
          'Selection is not yet available.' :
          `Select a winner for Game ${this.reportUI.matchNumber + 1 + increment}`
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

      window.addEventListener('currentReportChange', (event) => {
        window.showLoading();

        let newReport = null, newReportUI = null;

        let dataset = event?.detail ?? null;


        this.clearSelection();
        if (this.subscribeToMatchStatusesSnapshot)
          this.subscribeToMatchStatusesSnapshot();
        if (this.subscribeToCurrentReportDisputesSnapshot)
          this.subscribeToCurrentReportDisputesSnapshot();

        let teamNumber = dataset?.user_level == this.userLevelEnums['IS_TEAM1'] ? 0 :
          (dataset.user_level == this.userLevelEnums['IS_TEAM2'] ? 1 : 0);

        let otherTeamNumber = teamNumber === 0 ? 1 : 0;


        if (!dataset) {
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
            position: dataset.position ?? initialData.report.position,
            userLevel: dataset.user_level ?? initialData.report.userLevel,
            teams: [
              {
                ...initialData.report.teams[0],
                winners: initialData.report.teams[0].winners,
                id: dataset.team1_id,
                position: dataset.team1_position,
                banner: dataset.team1_teamBanner,
                name: dataset.team1_teamName
              },
              {
                ...initialData.report.teams[0],
                winners: initialData.report.teams[1].winners,
                id: dataset.team2_id,
                position: dataset.team2_position,
                banner: dataset.team2_teamBanner,
                name: dataset.team2_teamName
              }
            ],
          };
        }

        this.getCurrentReportSnapshot(dataset.classNamesWithoutPrecedingDot, newReport, newReportUI);
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

            if (change.type === "added" || change.type === "modified") {
              allDisputes[data['match_number']] = {
                ...data, id
              };
            }
          });


          this.dispute = [...allDisputes];
        }
      );

      this.subscribeToCurrentReportDisputesSnapshot = subscribeToCurrentReportDisputesSnapshot;
      window.closeLoading()
    },

    getCurrentReportSnapshot(classNamesWithoutPrecedingDot, newReport, newReportUI) {
      const currentReportRef = doc(db, `event/${eventId}/match_status`, classNamesWithoutPrecedingDot);
      let subscribeToCurrentReportSnapshot = onSnapshot(
        currentReportRef,
        async (reportSnapshot) => {
          if (reportSnapshot.exists()) {
            let data = reportSnapshot.data();
            let {
              score,
              matchStatus,
              realWinners,
              organizerWinners,
              team1Winners,
              team2Winners,
              completeMatchStatus
            } = data;

            if (!score) {
              score = [0, 0];
            }

            this.report = {
              ...newReport,
              organizerWinners,
              id: reportSnapshot.id,
              matchStatus,
              completeMatchStatus,
              realWinners,
              teams: [
                {
                  ...newReport.teams[0],
                  winners: team1Winners,
                  score: score[0],
                },
                {
                  ...newReport.teams[1],
                  winners: team2Winners,
                  score: score[1],
                }
              ]
            }


            if (this.report.userLevel != this.userLevelEnums['IS_ORGANIZER']) {
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
          } else {
            this.report = {
              ...newReport
            };

            this.reportUI = {
              ...newReportUI,
            }

            this.dispute = [null, null, null];
            window.closeLoading()
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
      window.Swal.fire({
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
        confirmButtonColor: "#43A4D7",
        showCancelButton: true,
        confirmButtonText: 'Submit Dispute',
        cancelButtonText: 'Back',
        padding: '2em',
        reverseButtons: true,
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            let { files } = await fileStore.uploadToServer('claim');
      
            const disputeDto = {
              report_id: newFormObject.report_id,
              match_number: newFormObject.match_number,
              event_id: newFormObject.event_id,
              dispute_userId: newFormObject.dispute_userId,
              dispute_teamId: newFormObject.dispute_teamId,
              dispute_teamNumber: newFormObject.dispute_teamNumber,
              dispute_reason: newFormObject.dispute_reason,
              dispute_description: newFormObject.dispute_description || null,
              dispute_image_videos: files,
              // Initialize optional fields as null
              response_userId: null,
              response_teamId: null,
              response_teamNumber: null,
              response_explanation: null,
              resolution_winner: null,
              resolution_resolved_by: null,

              created_at: serverTimestamp(),
              updated_at: serverTimestamp()
            };

            validateDisputeCreation(disputeDto);

            const disputesRef = collection(db, `event/${eventId}/disputes`);
            const docRef = await addDoc(disputesRef, disputeDto);
            const docSnap = await getDoc(docRef);
            if (docSnap.exists()) {
              let id = docRef.id;
              let data = docSnap.data();
              this.dispute = this.dispute.map((item, index) => 
                index == disputeDto['match_number'] ? { ...data, id } : item
              );
            }

            window.Toast.fire({
              icon: 'success',
              text: "Successfully created!"
            });
          } catch (error) {
            console.error("Error adding document: ", error);
          }
        }
      });

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

      window.Swal.fire({
        title: 'Responding to a Dispute',
        html: `
            <p class="mt-2 mb-2">An opponent team has raised a dispute and requires your response.</p>
            <p class="mt-2">Responding to a dispute does not consume any dispute submissions.</p>
            <p class="mt-2">A dispute submission is only consumed when you raise a dispute and submit it.</p>
            <p class="mt-2">A dispute submission will be returned if a dispute resolves in the disputing team's favour.</p>
        `,
        confirmButtonColor: "#43A4D7",
        confirmButtonText: 'Continue',
        width: 500,
        padding: '2em',
      }).then(async (result) => {
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
          const docSnap = await getDoc(disputeRef);
          if (docSnap.exists()) {
            let id = disputeRef.id;
            let data = docSnap.data();
            
            this.dispute = this.dispute.map((item, index) => 
              index == match_number ? { ...data, id } : item
            );
          }
        } else {
          handleCancelResponse();
        }
      });
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

window.onload = () => {

  createApp({
    BracketData,
    UploadData,
  }).mount('#Bracket');
}
// Alpine.start();

