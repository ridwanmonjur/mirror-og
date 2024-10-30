import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, setDoc, addDoc, onSnapshot, updateDoc, getDocsFromCache, startAfter, limit, orderBy, doc, query, collection, collectionGroup, getDocs, getDoc, where, or } from "firebase/firestore";
// import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";

import Alpine from 'alpinejs';
window.Alpine = Alpine;


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



function scrollIntoView(length, type="top") {
    if (type="top") {
        if (length) chatMessages.children[length-1]?.scrollIntoView();
       
    } else if (type="bottom") {
       if (length) chatMessages.children[0]?.scrollIntoView();

    }
}



Alpine.data('alpineDataComponent', function () {

    const userLevelEnums = JSON.parse(document.getElementById('userLevelEnums').value);
    const userTeamId = document.getElementById('joinEventTeamId').value[0] ?? null;
    const eventId = document.getElementById('eventId').value;
    let initialData = {
        reportUI: {
            matchNumber: 0,
            userTeamId,
            teamNumber: null,
            otherTeamNumber: null,
            disabled: [false, false, false]
        },
        report: {
          id: null,
          organizerWinners: [null, null, null],
          realWinners: [null, null, null],
          userLevel: null,
          matchStatus: ['ONGOING', null, null],
          teams: [
            {
              winners:  [null, null, null],
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
          // {
          //   id: null,
          //   information: {
          //     teamName: "TeamName",
          //     teamBanner: "",
          //     reason: "The reason",
          //     desc: "The desc",
          //     evidence: [],
          //     userId: 1,
          //   },
          //   counter: {
          //     teamName: "TeamName",
          //     teamBanner: "",
          //     reason: "The reason",
          //     desc: "The desc",
          //     evidence: [],
          //     userId: 1,
          //   },
          //   resolution: {

          //   },

          // }, 
          null
        ],
    };
    return {
      ...initialData,
      subscribeToMatchStatusesSnapshot: null,
      subscribeToCurrentReportSnapshot: null,
      userLevelEnums,
        async onSubmitSelectTeamToWin() {
          let teamNumber = this.reportUI.teamNumber;
          let otherTeamNumber = this.reportUI.otherTeamNumber;
          let matchNumber = this.reportUI.matchNumber;
          let selectedTeamIndex = document.getElementById('selectedTeamIndex').value;
          console.log({teamNumber, otherTeamNumber, userLevel: this.report.userLevel, IS_ORGANIZER: this.userLevelEnums['IS_ORGANIZER']})
          if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
            this.report.organizerWinners[matchNumber] = selectedTeamIndex;
            this.report.realWinners[matchNumber] = selectedTeamIndex;
          }

          if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
            this.report.teams[teamNumber].winners[matchNumber] = selectedTeamIndex;
            let otherTeamWinner = this.report.teams[otherTeamNumber].winners[matchNumber];
            console.log({otherTeamWinner, teamNumber, otherTeamNumber, report: Alpine.raw(this.report)});
            if (otherTeamWinner) {
              if (otherTeamWinner === selectedTeamIndex) {
                this.report.realWinners[matchNumber] = selectedTeamIndex;
              } 
            }

            console.log({otherTeamWinner, teamNumber, otherTeamNumber, report: Alpine.raw(this.report)});
          }

          await this.saveReport();
        },
        async onChangeTeamToWin() {
          let matchNumber = this.reportUI.matchNumber;

          if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
            let otherIndex = this.report.realWinners[matchNumber] === "1" ? "0" : "1"; 
            this.report.organizerWinners[matchNumber] = otherIndex;
            this.report.realWinners[matchNumber] = otherIndex;
          }

          if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
            let teamNumber = this.reportUI.teamNumber;
            let otherTeamNumber = this.reportUI.otherTeamNumber;
            let otherIndex = this.report.teams[otherTeamNumber].winners[matchNumber];
            if (otherIndex) {
              this.report.teams[teamNumber].winners[matchNumber] = otherIndex;
              this.report.realWinners[matchNumber] = otherIndex;
            }
          }

          await this.saveReport();
        },
        async saveReport() {
          const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);

          const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`; 
          const docRef = doc(allMatchStatusesCollectionRef, customDocId);
          
          try {
            await setDoc(docRef, {
              organizerWinners: this.report.organizerWinners,
              status: this.report.matchStatus,
              winners: this.report.realWinners,
              score: [this.report.teams[0].score, this.report.teams[1].score],
              team1Winners: this.report.teams[0].winners,
              team2Winners: this.report.teams[1].winners,
            });
            console.log("Document written with ID: ", customDocId);
            console.log({array: Alpine.raw(this.report)});
            if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) 
            {
              this.setDisabled();
            }
          } catch (error) {
            console.error("Error adding document: ", error);
          }
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
            const selectionMessage = document.querySelector('.selectionMessage');
            if (selectionMessage) {
              selectionMessage.innerText = `Currently selecting ${this.report.teams[index].name} as the winner of Game ${this.reportUI.matchNumber+1}`;
            }
          },
      
        clearSelection() {
          let selectionButtons  = document.querySelectorAll('.selectedButton');
          selectionButtons.forEach((selectedButton)=> {
              if (selectedButton) {
                selectedButton.style.backgroundColor = 'white'; 
                selectedButton.style.color = 'black'; 
                selectedButton = null;
              }

              const selectTeamSubmitButton = document.querySelector('.selectTeamSubmitButton');
              selectTeamSubmitButton.style.backgroundColor = '#white'; 
              selectTeamSubmitButton.style.color = 'black';

              document.getElementById('selectedTeamIndex').value = null;
          
              const selectionMessage = document.querySelector('.selectionMessage');
              if (selectionMessage) {
                selectionMessage.innerText = ""; 
              }
          }); 
        },
        changeMatchNumber(increment) {
          this.reportUI.matchNumber = this.reportUI.matchNumber + increment; 
        },
        getDisabled() {
          return this.reportUI.disabled[this.reportUI.matchNumber] ;
        },
        setDisabled() {
          let disabledList = [false, false, false];
          for (let index = 0; index <= 2; index++) {
            disabledList[index] = 
              this.report.teams[this.reportUI.teamNumber].winners[(this.reportUI.matchNumber-1) % 2] === null;
          }

          this.reportUI.disabled = [...disabledList];
        },
        
        init() {
            this.getAllMatchStatusesData();
            window.addEventListener('currentReportChange', (event) => {
             
                let dataset = event?.detail ?? null;

                if (dataset.position === this.report.position 
                  && dataset.team1_position == this.report.teams[0].position 
                  && dataset.team2_position == this.report.teams[1].position 
                ) {
                return;
              }

              this.clearSelection();
              if (this.subscribeToMatchStatusesSnapshot) this.subscribeToMatchStatusesSnapshot();


                let teamNumber = dataset.userLevel === userLevelEnums['IS_TEAM1'] ? 0 :
                  (dataset.userLevel === userLevelEnums['IS_TEAM2'] ? 1 : 0 );

                let otherTeamNumber =  teamNumber === 0 ? 1 : 0;
              
                this.reportUI = {
                  ...initialData.reportUI,
                  teamNumber, 
                  otherTeamNumber
                }

                this.report = {
                  ...initialData.report,
                  position: dataset.position,
                  userLevel: dataset.user_level,
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

                this.getCurrentReportSnapshot(dataset.classNamesWithoutPrecedingDot);
              });
            },
            destroy() {
                if (this.subscribeToMatchStatusesSnapshot) this.subscribeToMatchStatusesSnapshot();
            },
            getAllMatchStatusesData() {
              const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);
              const allMatchStatusesQ = query(allMatchStatusesCollectionRef);
              let allDataList = {};
              let subscribeToMatchStatusesSnapshot = onSnapshot(allMatchStatusesQ, async (reportSnapshot) => {
                  reportSnapshot.docChanges().forEach( (change) => {
                      if (change.type === "added" || change.type === "modified") {
                        allDataList[change.doc.id] = change.doc.data();
                      }
                  });
                  
                  console.log(allDataList);
              });
  
              this.subscribeToMatchStatusesSnapshot = subscribeToMatchStatusesSnapshot;
          },

          getCurrentReportSnapshot(classNamesWithoutPrecedingDot) {
            const currentReportRef = doc(db, `event/${eventId}/match_status`, classNamesWithoutPrecedingDot);
            let subscribeToCurrentReportSnapshot = onSnapshot(
              currentReportRef, 
              async (reportSnapshot) => {
                  if (reportSnapshot.exists()) {
                    let data = reportSnapshot.data();
                    let { 
                        score, 
                        status: matchStatus, 
                        winners: realWinners, 
                        organizerWinners,
                        team1Winners,
                        team2Winners,
                      } = data;

                    this.report = {
                      ...this.report,
                      organizerWinners,
                      id: reportSnapshot.id,
                      matchStatus,
                      realWinners,
                      teams: [
                        {
                          ...this.report.teams[0],
                          score: score[0],
                          winners: team1Winners

                        },
                        {
                          ...this.report.teams[1],
                          score: score[1],
                          winners: team2Winners
                        }
                      ]
                    }

                    console.log({userLevelEnums: this.userLevelEnums, userLevel: this.report.userLevel})
                    if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) 
                    {
                      this.setDisabled();
                    }

                    const allNull = this.dispute.every(item => item === null);
                    if (allNull) this.dispute = [
                      null, null, null
                    ];

                    try {
                      let response = await fetch('/api/disputes', {
                          method: 'POST',
                          headers: {
                            "Content-Type": "application/json",
                              'Accept': 'application/json',
                              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                          },
                            body: JSON.stringify({
                              report_id: this.report.id,
                              action: 'retrieve'
                            })
                        });
      
                        response = await response.json();
      
                        if (response.success) {
                          this.dispute = response.data;
                        } else {
                          window.toastError(response.message);
                        }
                    } catch (error) {
                      window.toastError("Failed");
                    }

                } else {
                  console.log("No such document!");
                }
              }
            );

            this.subscribeToCurrentReportSnapshot = subscribeToCurrentReportSnapshot;
          },
          async submitDisputeForm(event) {
            event.preventDefault();
            console.log("hi");
            const form = event.target;
            const formData = new FormData(form);
            let jsonObject = {}
            for (let [key, value] of formData.entries()) {
                jsonObject[key] = value;
            }

            if (jsonObject['action'] === 'create') {
              const selectedRadio = jsonObject['reportReason'];
              const otherReasonInput = jsonObject['otherReasonText'];
              let dispute_reason = '';
              if (selectedRadio) {
                dispute_reason = selectedRadio;
              } 
              else if (otherReasonInput) {
                dispute_reason = otherReasonInput.trim();
              }
              
              delete jsonObject['reportReason'];
              delete jsonObject['otherReasonText'];
              jsonObject['dispute_reason'] = dispute_reason;

              try {
                  let response = await fetch('/api/disputes', {
                      method: 'POST',
                      headers: {
                        "Content-Type": "application/json",
                          'Accept': 'application/json',
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                      },
                      body: JSON.stringify(jsonObject)
                  });

                  response = await response.json();

                  if (response.success) {
                    window.Toast.fire({
                        icon: 'success',
                        text: response.message
                    });

                  
                  } else {
                    window.toastError("Failed");
                  }
              } catch (error) {
                window.toastError("Failed");
              }
            }
            
          },
        }
    });
    
        



Alpine.start();

