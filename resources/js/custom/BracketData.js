import { 
  setDoc, serverTimestamp,
  onSnapshot, updateDoc, doc, query, collection,  getDocs,  where
} from "firebase/firestore";
import { onAuthStateChanged } from "firebase/auth";
import { generateInitialBracket, resetDotsToContainer, clearSelection, calcScores, updateReportFromFirestore, showSwal, createReportTemp, createDisputeDto, generateWarningHtml, updateAllCountdowns, diffDateWithNow, updateCurrentReportDots } from "./brackets";
import firebaseService from "../services/firebase.js";

export default function BracketData({ fileStore, bracketData, auth, db }) {
  const { hiddenUserId, eventId, userLevelEnums,  disputeLevelEnums, userTeamId } = bracketData;
  
  let totalMatches = 3;
  const {
    reportStore,
    disputeStore,
    _initialBracket
  } = generateInitialBracket(userTeamId, disputeLevelEnums, userLevelEnums, totalMatches);
  
  let _reportStore = {...reportStore};
  
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

  return {
    userLevelEnums,
    disputeLevelEnums,
    ..._initialBracket,
    async init() {
      console.log(">>>init>>>");
      this.clearUploadData();
      if (hiddenUserId) {
        try {
          const { user, claims } = await firebaseService.initializeAuth();
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
        } catch (error) {
          console.error('Firebase initialization failed:', error);
        }
      }

      window.addEventListener('changeReport', (event) => {
        window.showLoading();
        let newReport = {}, newReportUI = {};
        let eventUpdate = event?.detail ?? null;
        clearSelection();
        this.reportUI.statusText = '';
        if (this.subscribeToMatchStatusesSnapshot)
          this.subscribeToMatchStatusesSnapshot();
        if (this.subscribeToCurrentReportDisputesSnapshot)
          this.subscribeToCurrentReportDisputesSnapshot();

        let teamNumber = eventUpdate?.user_level == this.userLevelEnums['IS_TEAM1'] ? 0 :
          (eventUpdate.user_level == this.userLevelEnums['IS_TEAM2'] ? 1 : 0);

        let otherTeamNumber = teamNumber === 0 ? 1 : 0;
        if (!eventUpdate) {
          newReport = {
            ..._initialBracket.report,
          };

          newReportUI = {
            ..._initialBracket.reportUI,
          }

        } else {
          resetDotsToContainer();

          newReportUI = {
            ..._initialBracket.reportUI,
            teamNumber,
            otherTeamNumber,
          }

          newReport = {
            ..._initialBracket.report,
            position: eventUpdate.position ?? _initialBracket.report.position,
            userLevel: eventUpdate.user_level ?? _initialBracket.report.userLevel,
            deadline: eventUpdate.deadline ?? null,
            stageName: eventUpdate.stage_name ?? null,
            teams: [
              {
                ..._initialBracket.report.teams[0],
                winners: _initialBracket.report.teams[0].winners,
                id: eventUpdate.team1_id,
                position: eventUpdate.team1_position,
                banner: eventUpdate.team1_teamBanner,
                name: eventUpdate.team1_teamName ?? '-'
              },
              {
                ..._initialBracket.report.teams[1],
                winners: _initialBracket.report.teams[1].winners,
                id: eventUpdate.team2_id,
                position: eventUpdate.team2_position,
                banner: eventUpdate.team2_teamBanner,
                name: eventUpdate.team2_teamName ?? '-'
              }
            ],
          };
        }

        this.reportUI = { ...newReportUI };

        this.setReportSnapshot(
          eventUpdate.classNamesWithoutPrecedingDot, 
          newReport, 
          newReportUI
        );
      });

      // Swal.close();
    },

    destroy() {
      if (this.subscribeToMatchStatusesSnapshot) this.subscribeToMatchStatusesSnapshot();
    },

    async onSubmitSelectTeamToWin() {
      if (this.reportUI.disabled) {
        window.toastError("Cannot report scores on this match yet.");
        return;
      }

      let teamNumber = this.reportUI.teamNumber;
      let otherTeamNumber = this.reportUI.otherTeamNumber;
      let matchNumber = this.reportUI.matchNumber;
      let selectedTeamIndex = document.getElementById('selectedTeamIndex').value;
      let update = createReportTemp(this.report, reportStore.list);

      const result = await showSwal({
        title: 'Choosing the winner',
        html: `Are you sure you want to choose the winner?`,
        confirmButtonText: 'Make Choice',
      });

      if (result.isConfirmed) {
          if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
            update.organizerWinners[matchNumber] = selectedTeamIndex;
            update.realWinners[matchNumber] = selectedTeamIndex;
            update.score = calcScores(update);     
          }

          if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
            update.teams[teamNumber].winners[matchNumber] = selectedTeamIndex;
            let otherTeamWinner = this.report.teams[otherTeamNumber].winners;
            if (otherTeamWinner) {
              if (otherTeamWinner === selectedTeamIndex) {
                update.realWinners[matchNumber] = selectedTeamIndex;
              }
            }

            update.score = calcScores(update);        
          }

          await this.writeReportDB(update);
      }
    },

    async onChangeTeamToWin() {
      if (this.reportUI.disabled) {
        window.toastError("Cannot report scores on this match yet.");
        return;
      }

      const result = await showSwal({
        title: 'Changing the winner',
        html: `Are you sure you want to change the winner?`,
        confirmButtonText: 'Change Declaration',
      });
     
      if (result.isConfirmed) {
        let matchNumber = this.reportUI.matchNumber;
        let update = createReportTemp(this.report, reportStore.list);
        if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
          let otherIndex = this.report.realWinners === "1" ? "0" : "1";
          update.organizerWinners[matchNumber] = otherIndex;
          update.realWinners[matchNumber] = otherIndex;
          update.score = calcScores(update);
        }

        if (
          this.report.userLevel === this.userLevelEnums['IS_TEAM1'] 
          || this.report.userLevel === this.userLevelEnums['IS_TEAM2']
        ) {
          let teamNumber = this.reportUI.teamNumber;
          let otherTeamNumber = this.reportUI.otherTeamNumber;
          let otherIndex = this.report.teams[otherTeamNumber].winners;
          if (otherIndex) {
            update.teams[teamNumber].winners[matchNumber] = otherIndex;
            update.realWinners[matchNumber] = otherIndex;
          }
          update.score = calcScores(update);
        }

        await this.writeReportDB(update);
      }
    },
    
    async onRemoveTeamToWin() {
      if (this.reportUI.disabled) {
        window.toastError("Cannot report scores on this match yet.");
        return;
      }

      const result = await showSwal({
        title: 'Remove the winner',
        html: `Are you sure you want to remove the winner?`,
        confirmButtonText: 'Yes, Remove Winner',
      });


      if (result.isConfirmed) {
        let matchNumber = this.reportUI.matchNumber;
        let update = createReportTemp(this.report, reportStore.list);
        update.organizerWinners[matchNumber] = null;
        update.realWinners[matchNumber] = null;
        update.score = calcScores(update);

        await this.writeReportDB(update);
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

      try {
        let tempReport = createReportTemp(this.report, reportStore.list);
        tempReport['disputeResolved'][this.reportUI.matchNumber] = true;
        if (match_number != 2) {
          tempReport['matchStatus'][Number(this.reportUI.matchNumber)+1] = "ONGOING";
        }

        tempReport['realWinners'][this.reportUI.matchNumber] = resolution_winner;
        
        tempReport['completeMatchStatus']  = match_number == 2 ? "ENDED": "ONGOING",
        tempReport['score'] = calcScores(tempReport);
        this.writeReportDB(tempReport, false);       
      
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

    async writeReportDB(tempState, willCheckDeadline=true) {
      let validateData = {
        'team1_id' : this.report.teams[0].id,
        'team1_position': this.report.teams[0].position,
        'team2_id' : this.report.teams[1].id,
        'team2_position': this.report.teams[1].position,
        'my_team_id': this.report.teams[this.reportUI.teamNumber].id,
         willCheckDeadline
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
      } catch (error) {
        window.toastError("Problem updating data");
        return;
      }

      const matchesCRef = collection(db, `event/${eventId}/brackets`);
      const customDocId = `${this.report.teams[0].position}.${this.report.teams[1].position}`;
      const docRef = doc(matchesCRef, customDocId);

      try {
        let firestoreDoc = {
          score: [tempState?.score[0] ?? "0", tempState?.score[1] ?? "0"],
          stageName: tempState.stageName,
          realWinners: [...tempState.realWinners],
          organizerWinners: [...tempState.organizerWinners],
          team1Id: tempState.teams[0].id,
          team2Id: tempState.teams[1].id,
          position: tempState.position,
          completeMatchStatus: tempState.completeMatchStatus,
          randomWinners: [...tempState.randomWinners],
          defaultWinners: [...tempState.defaultWinners],
          disqualified: tempState.disqualified,
          disputeResolved: [...tempState.disputeResolved],
          team1Winners: [...tempState.teams[0]?.winners],
          team2Winners: [...tempState.teams[1]?.winners],
          matchStatus: [...tempState.matchStatus],
        };

        await setDoc(docRef, firestoreDoc);
        

      } catch (error) {
        console.error("Error adding document: ", error);
      }
    },

    toggleResponseForm(classToShow, classToHide) {
      document.querySelector(`.${classToShow}`).classList.toggle("d-none");
      document.querySelector(`.${classToHide}`).classList.toggle("d-none");
    },
    
    showImageModal(imgPath, mediaType) {
      const modal = window.bootstrap.Modal.getOrCreateInstance(document.getElementById('imageModal'));
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
      if (this.reportUI.disabled) {
        window.toastError("Cannot report scores on this match yet.");
        return;
      }

      clearSelection();
      this.reportUI.statusText = '';
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

    changeMatchNumber(increment) {
      clearSelection();
      let newNo = Number(this.reportUI.matchNumber) + Number(increment);
      this.clearUploadData();
      let isDisabled = newNo != 0; 
      if (isDisabled) {
        isDisabled = isDisabled &&
          this.report.userLevel != this.userLevelEnums['IS_ORGANIZER'] &&
          reportStore.list.realWinners[newNo-1] == null &&
          reportStore.list.teams[this.reportUI.teamNumber]?.winners[newNo-1] == null;
      }

      let demoNo = newNo + 1;
      this.reportUI = {
        ...this.reportUI,
        matchNumber: newNo,
        disabled: isDisabled,
        statusText: isDisabled ?
          'Selection is not yet available.' :
          `Select a winner for Game ${demoNo}`
      };

      this.report = reportStore.makeCurrentReport(this.report, newNo) ;
      this.dispute = disputeStore.makeCurrentDispute(newNo)
      
    },
  
    
    setDisabled(reportUI = {}) {
      let currentNumber = Number(this.reportUI.matchNumber);
      console.log({currentNumber});
      console.log({currentNumber});
      console.log({currentNumber});

      let isDisabled = currentNumber != 0;
      if (isDisabled) {
        isDisabled = isDisabled && this.report.userLevel !== this.userLevelEnums['IS_ORGANIZER'] &&
          (
            reportStore.list.realWinners[currentNumber-1] == null &&
            reportStore.list.teams[this.reportUI.teamNumber]?.winners[currentNumber-1] == null
          );
      }

      console.log({isDisabled});
      console.log({isDisabled});
      console.log({isDisabled});
        

      this.reportUI = {
        ...reportUI,
        statusText: isDisabled ?
          'Selection is not yet available.' :
          `Select a winner for Game ${currentNumber+1}`,
        disabled: isDisabled
      };
    },
    
    makeCurrentReportDisputeSnapshot(classNamesWithoutPrecedingDot) {
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

          disputeStore.setList(allDisputes);

          this.dispute = allDisputes[this.reportUI.matchNumber];
        }
      );
      

      this.subscribeToCurrentReportDisputesSnapshot = subscribeToCurrentReportDisputesSnapshot;
      window.closeLoading()
    },

    setReportSnapshot(classNamesWithoutPrecedingDot, newReport, newReportUI) {
    
      const currentReportRef = doc(db, `event/${eventId}/brackets`, classNamesWithoutPrecedingDot);

      let subscribeToCurrentReportSnapshot = onSnapshot(
        currentReportRef,
        async (reportSnapshot) => {
          
          if (reportSnapshot.exists()) {
            let data = reportSnapshot.data();
            data['id'] = reportSnapshot.id;

            reportStore.updateListFromFirestore(data);           
            newReportUI.matchNumber = this.reportUI.matchNumber;
            
            let matchNumber = newReportUI.matchNumber;
            this.report = updateReportFromFirestore(newReport, data, matchNumber);
            this.setDisabled(newReportUI);
            updateCurrentReportDots(reportStore.list);
            this.makeCurrentReportDisputeSnapshot(classNamesWithoutPrecedingDot);
           
          } else {
             
            newReportUI.matchNumber = 0;
             

            reportStore.setList(_reportStore.list);
            this.report = { ...newReport };
            this.setDisabled(newReportUI);
            this.dispute = null;
          }

          window.closeLoading();
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
            const uploadResult = await fileStore.uploadToServer('claim');
            if (!uploadResult) {
              console.error('File upload failed, cannot proceed with dispute');
              return;
            }
            let { files } = uploadResult;
      
            let disputeDto = createDisputeDto(newFormObject, files);
            validateDisputeCreation(disputeDto);
            let newDisputeId = `${this.report.teams[0].position}.`+`${this.report.teams[1].position}.${this.reportUI.matchNumber}`;
            const disputesRef = doc(db, `event/${eventId}/disputes`, newDisputeId);
            await setDoc(disputesRef, disputeDto);
            let tempReport = createReportTemp(this.report, reportStore.list);
            tempReport['disputeResolved'][this.reportUI.matchNumber] = false;           
            tempReport['score'] = calcScores(tempReport);
            this.writeReportDB(tempReport, false);        

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
          const uploadResult = await fileStore.uploadToServer('response');
          if (!uploadResult) {
            console.error('File upload failed, cannot proceed with dispute response');
            return;
          }
          let { files } = uploadResult;

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

          let tempReport = createReportTemp(this.report, reportStore.list);
          tempReport['disputeResolved'][this.reportUI.matchNumber] = false;
          tempReport['score'] = calcScores(tempReport);
          this.writeReportDB(tempReport, false);       
          
        } 
      },

    clearUploadData() {
      window.dispatchEvent(new CustomEvent('clearUploadData'));
    }
    
  };

}