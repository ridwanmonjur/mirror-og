import {
  initializeFirestore, memoryLocalCache, setDoc, serverTimestamp,
  addDoc, onSnapshot, updateDoc,  doc, query, collection, collectionGroup, getDocs, getDoc, where, or
} from "firebase/firestore";

const initialBracketData = (userTeamId) => ({
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
    randomWinners: [null, null, null],
    defaultWinners: [null, null, null],
    disqualified: false,
    disputeResolved: [null, null, null],
    realWinners: [null, null, null],
    userLevel: userLevelEnums['IS_PUBLIC'],
    completeMatchStatus: 'UPCOMING',
    matchStatus: ['UPCOMING', 'UPCOMING', 'UPCOMING'],
    deadline: null,
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
});

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
    if (minutes > 0) countdownText += `${minutes}m `;
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



export {
  initialBracketData,
  calcScores,
  createReportTemp,
  updateReportFromFirestore,
  updateAllCountdowns,
  diffDateWithNow,
  generateWarningHtml,
  createDisputeDto,
  showSwal
};
