import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, setDoc,   serverTimestamp,
  addDoc, onSnapshot, updateDoc, getDocsFromCache, startAfter, limit, orderBy, doc, query, collection, collectionGroup, getDocs, getDoc, where, or } from "firebase/firestore";
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



Alpine.data('alpineDataComponent', function () {

    const userLevelEnums = JSON.parse(document.getElementById('userLevelEnums').value);
    const userTeamId = document.getElementById('joinEventTeamId').value[0] ?? null;
    const eventId = document.getElementById('eventId').value;
    let initialData = {
        disputeLevelEnums: {
          'ORGANIZER' : 1,
          'DISPUTEE': 2,
          'RESPONDER': 3
        },
        subscribeToMatchStatusesSnapshot: null,
        subscribeToCurrentReportSnapshot: null,
        subscribeToCurrentReportDisputesSnapshot: null,
        reportUI: {
            matchNumber: 0,
            userTeamId,
            teamNumber: null,
            otherTeamNumber: null,
            disabled: [false, false, false],
            statusText: 'Select a winner for Game 1'
        },
        report: {
          id: null,
          organizerWinners: [null, null, null],
          realWinners: [null, null, null],
          userLevel: null,
          matchStatus: ['ONGOING', null, null],
          dispute: [null, null, null],
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
          null
        ],
    };
    return {
      ...initialData,
    
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
          window.Swal.fire({
            title: 'Submitting a Dispute',
            html: `
                Are you sure you want to change the winner>
            `,
            showCancelButton: true,
            confirmButtonText: 'Submit Dispute',
            cancelButtonText: 'Back',
            padding: '2em',
            confirmButtonColor: '#286281',
            reverseButtons: true,
          }).then(async (result) => {
              if (result.isConfirmed) {
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
              }
          });
        
         
        },
        async resolveDisputeForm(event) {
          event.preventDefault();
          const form = event.currentTarget;
          const formData = Object.fromEntries(new FormData(form));
          let {
            id,
            dispute_matchNumber,
            resolution_winner,
            resolution_resolved_by,
            already_winner
          } = formData;

          console.log({formData});
      
          if (!id || !dispute_matchNumber || !resolution_resolved_by) {
            window.toastError('Missing required fields');
            console.error(id, dispute_matchNumber, resolution_resolved_by);
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

          if (already_winner) {
            this.dispute[this.reportUI.matchNumber] = {
              ...this.dispute[this.reportUI.matchNumber],
              ...updateData
            };

            if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) 
            {
              this.setDisabled();
            }

            return;
          }


          const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);
          const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`; 
          const docRef = doc(allMatchStatusesCollectionRef, customDocId);
          
          try {
            let winnerNew = [...this.report.realWinners];
            winnerNew[this.reportUI.matchNumber] = resolution_winner;
            await updateDoc(docRef, {
              winners: winnerNew,
            });

            console.log({
              demo: {...this.dispute[dispute_matchNumber] }, winnerNew
            })

            this.report.realWinners = [...winnerNew];
           
            this.dispute[this.reportUI.matchNumber] = {
              ...this.dispute[this.reportUI.matchNumber],
              ...updateData
            };

            if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) 
            {
              this.setDisabled();
            }
          } catch (error) {
            console.error("Error adding document: ", error);
          }
        },
        async decideResolution(event, teamNumber) {
          let button = event.currentTarget;
          document.querySelectorAll(".selectedDisputeResolveButton").forEach((value)=>
           {value.classList.remove('bg-primary', 'text-light'); }
          );
          
          button.classList.add('bg-primary', 'text-light');
          document.getElementById('resolution_winner_input').value = teamNumber;
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
        toggleResponseForm(classToShow, classToHide) {
          document.querySelector(`.${classToShow}`).classList.toggle("d-none");
          document.querySelector(`.${classToHide}`).classList.toggle("d-none");
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
            this.reportUI.statusText = `Currently selecting ${this.report.teams[index].name} as the winner of Game ${this.reportUI.matchNumber+1}`;
            
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
          
              this.reportUI.statusText = '';
              
          }); 
        },
        changeMatchNumber(increment) {
          this.reportUI = {
            ...this.reportUI ,
            matchNumber : this.reportUI.matchNumber + increment,
            statusText: this.reportUI.disabled[this.reportUI.matchNumber + increment] ?
              'Selection is not yet available.': 
              `Select a winner for Game ${this.reportUI.matchNumber+1+increment}`
          };
          
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
              if (this.subscribeToMatchStatusesSnapshot) 
                this.subscribeToMatchStatusesSnapshot();
              if (this.subscribeToCurrentReportDisputesSnapshot) 
                this.subscribeToCurrentReportDisputesSnapshot();

                let teamNumber = dataset.user_level == this.userLevelEnums['IS_TEAM1'] ? 0 :
                  (dataset.user_level == this.userLevelEnums['IS_TEAM2'] ? 1 : 0 );

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
                let allDisputes = [];
                disputeSnapshot.docChanges().forEach( (change) => {
                  let data = change.doc.data();
                  let id = change.doc.id;
                  if (change.type === "added") {
                    allDisputes[data['match_number']] = {
                      ...data, id
                    };
                  }

                  if (change.type === "modified") {
                    this.dispute[data['match_number']] = {
                      ...data, id
                    }
                  }
                });

                this.dispute = [...allDisputes];
              }
            );

            this.subscribeToCurrentReportDisputesSnapshot = subscribeToCurrentReportDisputesSnapshot;
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

                    if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) 
                    {
                      this.setDisabled();
                    }

                    this.getCurrentReportDisputeSnapshot(classNamesWithoutPrecedingDot);
                   

                } else {
                  let teamNumber = this.report.userLevel == this.userLevelEnums['IS_TEAM1'] ? 0 :
                    (this.report.userLevel == this.userLevelEnums['IS_TEAM2'] ? 1 : 0 );

                  let otherTeamNumber =  teamNumber === 0 ? 1 : 0;
                
                  this.reportUI = {
                    ...initialData.reportUI,
                    teamNumber, 
                    otherTeamNumber,
                    statusText: "Select a winner for Game 1"
                  }
                  console.log("No such document!");
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
            
            delete formObject['reportReason'];
            delete formObject['otherReasonText'];
            formObject['dispute_reason'] = dispute_reason;
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
                showCancelButton: true,
                confirmButtonText: 'Submit Dispute',
                cancelButtonText: 'Back',
                padding: '2em',
                reverseButtons: true,
            }).then(async (result) => {
                if (result.isConfirmed) {
                  try {
                    const disputeDto = {
                      report_id: formObject.report_id,
                      match_number: formObject.match_number,
                      event_id: formObject.event_id,
                      dispute_userId: formObject.dispute_userId,
                      dispute_teamId: formObject.dispute_teamId,
                      dispute_teamNumber: formObject.dispute_teamNumber,
                      dispute_reason: formObject.dispute_reason,
                      dispute_description: formObject.dispute_description || null,
                      
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
                      this.dispute[disputeDto['match_number']] = {
                        ...data,
                        id
                      }
                    }
                    
                    if (this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER']) 
                    {
                      this.setDisabled();
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
              dispute_matchNumber,
              response_userId
            } = formData;
        
            if (!id || !dispute_teamId || !response_teamNumber) {
              throw new Error('Missing required fields');
            }

            window.Swal.fire({
              title: 'Responding to a Dispute',
              html: `
                  <p class="mt-2 mb-2">An opponent team has raised a dispute and requires your response.</p>
                  <p class="mt-2">Responding to a dispute does not consume any dispute submissions.</p>
                  <p class="mt-2">A dispute submission is only consumed when you raise a dispute and submit it.</p>
                  <p class="mt-2">A dispute submission will be returned if a dispute resolves in the disputing team's favour.</p>
              `,
              confirmButtonText: 'Continue',
              width: 500,
              padding: '2em',
            }).then(async (result) => {
              if (result.isConfirmed) {
                const disputeRef = doc(db, `event/${eventId}/disputes`, id);
        
                const updateData = {
                  response_teamId: dispute_teamId,
                  response_teamNumber: response_teamNumber,
                  response_explanation: dispute_description || null,
                  response_userId,
                  updated_at: serverTimestamp(),
                  status: 'responded' 
                };
            
                await updateDoc(disputeRef, updateData);
                const docSnap = await getDoc(disputeRef);
                  if (docSnap.exists()) {
                    let id = disputeRef.id;
                    let data = docSnap.data();
                    this.dispute[dispute_matchNumber] = {
                      ...data,
                      id
                    }
                  }
              } else {
                  handleCancelResponse();
              }
          });
        
            
            
          },
          handleFiles(event, id) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                if (file.type.startsWith('image/')) {
                    this.createPreview(file, id);
                }
            });
            event.target.value = '';
          },

          createPreview(file, id) {
              const preview = document.createElement('div');
              preview.className = 'preview-item loading';

              const img = document.createElement('img');
              img.addEventListener('load', () => {
                  preview.classList.remove('loading');
              });

              const deleteBtn = document.createElement('button');
              deleteBtn.className = 'delete-btn';
              deleteBtn.innerHTML = '×';
              deleteBtn.addEventListener('click', () => preview.remove());

              const reader = new FileReader();
              reader.onload = (e) => {
                  img.src = e.target.result;
              };
              reader.readAsDataURL(file);

              preview.appendChild(img);
              preview.appendChild(deleteBtn);
              
              const uploadArea = this.$refs[`uploadArea${id}`];
              const plusButton = uploadArea.querySelector('.plus-button');
              uploadArea.insertBefore(preview, plusButton);
          },

          handleDrop(event, id) {
              event.preventDefault();
              this.$refs[`uploadArea${id}`].classList.remove('drag-over');
              
              const files = Array.from(event.dataTransfer.files);
              files.forEach(file => {
                  if (file.type.startsWith('image/')) {
                      this.createPreview(file, id);
                  }
              });
          },

          getAllImages() {
              const images1 = Array.from(this.$refs.uploadArea1.querySelectorAll('.preview-item img'))
                  .map(img => img.src);
              const images2 = Array.from(this.$refs.uploadArea2.querySelectorAll('.preview-item img'))
                  .map(img => img.src);
              
              console.log('Images from uploader 1:', images1);
              console.log('Images from uploader 2:', images2);
              return { uploader1: images1, uploader2: images2 };
          },
        }
    });
    
        
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


Alpine.start();

