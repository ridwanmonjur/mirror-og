import {
  initializeFirestore, memoryLocalCache, setDoc, serverTimestamp,
  addDoc, onSnapshot, updateDoc,  doc, query, collection, collectionGroup, getDocs, getDoc, where, or
} from "firebase/firestore";


let fireAuthStore = {
  firebaseUser: null,
};


const generateInitialBracket = (userTeamId, disputeLevelEnums, userLevelEnums, totalMatches) => {
  
  let _authStore = {
    firebaseUser: null,
  };

let _reportStore = {
  report: {
    id: null,
    organizerWinners: Array(totalMatches).fill(null),
    randomWinners: Array(totalMatches).fill(null),
    defaultWinners: Array(totalMatches).fill(null),
    disqualified: false,
    disputeResolved: Array(totalMatches).fill(null),
    realWinners: Array(totalMatches).fill(null),
    userLevel: userLevelEnums['IS_PUBLIC'],
    completeMatchStatus: 'UPCOMING',
    matchStatus: Array(totalMatches).fill('UPCOMING'),
    deadline: null,
    teams: [
      {
        winners: Array(totalMatches).fill(null),
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
        winners: Array(totalMatches).fill(null),
        score: 0,
      }
    ],
    position: ""
  },

  setReport(report) {
    this.report = report
  },

  makeCurrentReport(matchNumber) {
    return {
      id: this.report.id,
      organizerWinners: this.report.organizerWinners[matchNumber],
      randomWinners: this.report.randomWinners[matchNumber],
      defaultWinners: this.report.defaultWinners[matchNumber],
      disqualified: this.report.disqualified,
      disputeResolved: this.report.disputeResolved[matchNumber],
      realWinners: this.report.realWinners[matchNumber],
      userLevel: this.report.userLevel,
      completeMatchStatus: this.report.completeMatchStatus,
      matchStatus: this.report.matchStatus[matchNumber],
      deadline: this.report.deadline,
      teams: [
        {
          winners: this.report.teams[0].winners[matchNumber],
          id: this.report.teams[0].id,
          position: this.report.teams[0].position,
          banner: this.report.teams[0].banner,
          name: this.report.teams[0].name,
          score: this.report.teams[0].score,
        },
        {
          winners: this.report.teams[1].winners[matchNumber],
          id: this.report.teams[1].id,
          position: this.report.teams[1].position,
          banner: this.report.teams[1].banner,
          name: this.report.teams[1].name,
          score: this.report.teams[1].score,
        }
      ],
      position: this.report.position
    }
  }
};

let _disputeStore = {
  dispute: Array(totalMatches).fill(null),
  makeCurrentReport(matchNumber) {
    return this.dispute[matchNumber]
  }
};

  let _initialBracket = {
      firebaseUser: null,
      disputeLevelEnums,
      subscribeToMatchStatusesSnapshot: null,
      subscribeToCurrentReportDisputesSnapshot: null,
      reportUI: {
        matchNumber: 0,
        userTeamId,
        teamNumber: 0,
        otherTeamNumber: 1,
        disabled: false
        // disabled: [false, false, false],
        // statusText: 'Select a winner for Game 1'
      },
      report: {
        id: null,
        organizerWinners: null,
        randomWinners: null,
        defaultWinners: null,
        disqualified: false,
        disputeResolved: null,
        realWinners: null,
        userLevel: userLevelEnums['IS_PUBLIC'],
        completeMatchStatus: 'UPCOMING',
        matchStatus: 'UPCOMING',
        deadline: null,
        teams: [
          {
            winners: null,
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
            winners:  null,
            score: 0,
          }
        ],
        position: ""
      },
      dispute: {

      },
    };
  return {
     _initialBracket,
     _reportStore,
    _authStore,
     _disputeStore,
  };
};

function resetDotsToContainer() {
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
}

function calcScores(update) {
  let score1 = update.realWinners?.reduce((acc, value) => acc + (value == "0" ? 1 : 0), 0);
  let score2 = update.realWinners?.reduce((acc, value) => acc + (value == "1" ? 1 : 0), 0);
  return [score1, score2];
}

function createReportTemp(report) {
  let update = {
    organizerWinners: [...report.organizerWinners],
    matchStatus: [...report.matchStatus],
    realWinners: [...report.realWinners],
    teams: [...report.teams],
    position: report.position,
    completeMatchStatus: report.completeMatchStatus,
    randomWinners: [...report.randomWinners],
    defaultWinners: [...report.defaultWinners],
    disqualified: report.disqualified,
    disputeResolved: [...report.disputeResolved],
  }

  return update;
}

function updateAllCountdowns() {
  const diffDateElements = document.querySelectorAll(`.diffDate1`);

  diffDateElements.forEach(element => {
    const targetDateStr = element.getAttribute('data-diff-date');
    let countdownText = diffDateWithNow(targetDateStr);
    element.innerHTML = countdownText;
  });
}

function diffDateWithNow(targetDate) {
  targetDate = new Date(targetDate);
  const now = new Date();
  let countdownText = '';

  if (targetDate > now) {
    const diffMs = targetDate - now;
    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

    if (days > 0) countdownText += `${days}d `;
    if (hours > 0) countdownText += `${hours}h `;
    if (minutes > 0 && days <=0) countdownText += `${minutes}m `;
  } else {
    countdownText = 'Time over';
  }
  return countdownText;
}

function generateWarningHtml(diffDate, newPositionId) {
  let diffDateFormat = diffDateWithNow(diffDate);
  return `
      <div class="reportBox row z-99 justify-content-start bg-light border border-dark border rounded px-0 py-1" 
        style="width: 260px;"
      >
        <p class="text-center my-0 py-0 fs-6"> 
          <u>${newPositionId}</u>
        </p>
        <p class="text-primary text-center my-0 mb-1 py-0"> 
          You have pending results to report.
        </p>
        <small class="text-red small text-center my-0 py-0"> 
          Time left to report: 
          <span class="diffDate1" data-diff-date="${diffDate}">${diffDateFormat}</span>
        </small>
      </div>
  
    `;
}

function updateReportFromFirestore(baseReport, sourceData) {
  const score = sourceData.score || [0, 0];

  return {
    ...baseReport,
    organizerWinners: sourceData.organizerWinners,
    id: sourceData.id || sourceData.reportSnapshot?.id,
    matchStatus: sourceData.matchStatus,
    completeMatchStatus: sourceData.completeMatchStatus,
    realWinners: sourceData.realWinners,
    randomWinners: sourceData.randomWinners,
    defaultWinners: sourceData.defaultWinners,
    disqualified: sourceData.disqualified,
    disputeResolved: sourceData.disputeResolved,
    teams: [
      {
        ...baseReport.teams[0],
        score: score[0],
        winners: sourceData.team1Winners
      },
      {
        ...baseReport.teams[1],
        score: score[1],
        winners: sourceData.team2Winners
      }
    ]
  };
}

function createDisputeDto (newFormObject, files) {
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

  return disputeDto ;
}

async function showSwal(options = {}) {
  const defaults = {
    title: 'Submitting a Dispute',
    html: `Hi`,
    confirmButtonText: 'Submit Dispute',
    cancelButtonText: 'Back',
    confirmButtonColor: "#43A4D7",
    padding: '2em',
    reverseButtons: true,
    showCancelButton: true,
  };

  const settings = { ...defaults, ...options };

  try {
    const result = await window.Swal.fire({
      title: settings.title,
      html: settings.html,
      confirmButtonColor: settings.confirmButtonColor,
      showCancelButton: true,
      confirmButtonText: settings.confirmButtonText,
      cancelButtonText: settings.cancelButtonText,
      padding: '2em',
      reverseButtons: true,
    });
    
    return result;
  } catch (error) {
    console.error('Error in showSwal:', error);
    throw error;
  }
}

function clearSelection() {
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
}



export {
  generateInitialBracket,
  calcScores,
  createReportTemp,
  updateReportFromFirestore,
  updateAllCountdowns,
  diffDateWithNow,
  generateWarningHtml,
  createDisputeDto,
  showSwal,
  resetDotsToContainer,
  clearSelection
};
