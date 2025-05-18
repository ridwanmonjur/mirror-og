import { getDoc, updateDoc, initializeFirestore, memoryLocalCache, doc, collection, setDoc, serverTimestamp } from "firebase/firestore";
import { createApp }  from "petite-vue";
import { initializeApp } from "firebase/app";
import Swal from "sweetalert2";



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


async function saveReport(tempState, report) {
    const allMatchStatusesCollectionRef = collection(db, `event/${report.event_details_id}/brackets`);
    const customDocId = `${report.team1_position}.${report.team2_position}`;
    const docRef = doc(allMatchStatusesCollectionRef, customDocId);

    try {
      let firestoreDoc = {
        score: [tempState?.score[0] ?? "0", tempState?.score[1] ?? "0"],
        matchStatus: [...tempState.matchStatus],
        realWinners: tempState.realWinners,
        organizerWinners: tempState.organizerWinners,
        team1Winners: tempState.team1Winners,
        team2Winners: tempState.team2Winners,
        team1Id: tempState.team1_id,
        team2Id: tempState.team2_id,
        position: tempState.position,
        completeMatchStatus: tempState.completeMatchStatus,
        randomWinners: [...tempState.randomWinners],
        defaultWinners: [...tempState.defaultWinners],
        disqualified: tempState.disqualified,
        disputeResolved: [...tempState.disputeResolved]
      };

      await setDoc(docRef, firestoreDoc, { merge: true });
    } catch (error) {
      console.error("Error adding document: ", error);
    }
}

function createBrackets () {
    const bracketsDataInput = document.getElementById('brackets-data');
    const teamsDataInput = document.getElementById('teams-data');

    const bracketsData = JSON.parse(bracketsDataInput.value);
    const teamsData = JSON.parse(teamsDataInput.value);
    return {
        init() {
            console.log("Brackets initiaed!");
        },
        brackets: bracketsData,
        teams: teamsData,
        selectedMatch: null,


        openModal: function (match, type) {
            this.selectedMatch = match;

            if (type === 'brackets') {
                const modalTitle = document.getElementById('detailsModalTitle');
                if (modalTitle) {
                    modalTitle.textContent = 'Match Brackets Details';
                }
            }

            const form = document.getElementById(type === 'brackets' ? 'bracketsForm' : 'scoresForm');
            if (form) {
                form.setAttribute('data-mode', 'edit');
            }
        },

        saveChanges: async function (type) {
            if (!this.selectedMatch) return;

            const formElement = document.getElementById(type === 'brackets' ? 'bracketsForm' : 'scoresForm');
            if (!formElement) return;

            const formData = new FormData(formElement);

            const updatedData = {};

            for (const [key, value] of formData.entries()) {
                console.log({ key, value })
                if (key.includes('[') && key.includes(']')) {
                    const mainKey = key.substring(0, key.indexOf('['));
                    const indexStr = key.substring(key.indexOf('[') + 1, key.indexOf(']'));
                    const index = parseInt(indexStr);

                    if (!updatedData[mainKey]) {
                        updatedData[mainKey] = Array.isArray(this.selectedMatch[mainKey]) ?
                            [...this.selectedMatch[mainKey]] : [];
                    }

                    updatedData[mainKey][index] = value;
                } else {
                    updatedData[key] = value;
                }
            }

            if (type === 'brackets') {
                const modalTitle = document.getElementById('detailsModalTitle');
                if (modalTitle) {
                    modalTitle.textContent = 'Match Brackets Details';
                }

                const updatedMatch = { ...this.selectedMatch, ...updatedData };
                console.log('Saving changes for match:', this.selectedMatch.id, 'Type:', type);

                const endpoint = formElement.action;

                let data = await fetch(endpoint, {
                    method: formElement.method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(updatedMatch)
                })

                data = await data.json();
                if (data.success) {
                    let {team1, match, team2} = data.data;
                    if (match.team2_position && team2 && team1) {
                        const reportRef = doc(db, `event/${this.selectedMatch.event_details_id}/brackets`, `${match.team1_position}.${match.team2_position}`);
                        const docSnap = await getDoc(reportRef);
                        if (docSnap.exists()) {
                          const updateData = {
                            team1Id: team1.id,
                            team2Id: team2.id,
                            updated_at: serverTimestamp(),
                          };
                      
                          await updateDoc(reportRef, updateData);
                        }
                    }
    
                

                    console.log({team1, match, team2});
                    this.brackets = this.brackets.map((value) => {
                        return (value.team1_position == match.team1_position && value.team2_position == match.team2_position) ?
                            {
                                ...this.selectedMatch,
                                id: match.id,
                                team1Name: team1.teamName,
                                team2Name: team2.teamName
                            } : value 
                    });
                }
            } else {
                
                const updatedMatch = { ...this.selectedMatch, ...updatedData };
                console.log({updatedMatch, selectedMatch: this.selectedMatch});
                await saveReport(updatedMatch, this.selectedMatch);
                this.brackets = this.brackets.map((value) => {
                    return (value.team1_position == updatedMatch['team1_position'] && value.team2_position == updatedMatch['team2_position']) ?
                        {
                            ...this.selectedMatch,
                           ...updatedMatch
                        } : value 
                });
            }

            const modalElement = document.getElementById(type === 'brackets' ? 'detailsModal' : 'scoresModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    };
}

async function saveDisputes(disputeDto) {
    const reportRef = doc(db, `event/${disputeDto.event_id}/brackets`, disputeDto.report_id);
    console.log(`event/${disputeDto.event_id}/reports/${disputeDto.report_id}`);
    
    try {
        const reportSnap = await getDoc(reportRef);
        
        if (!reportSnap.exists()) {
            Swal.fire({
                icon: 'error',
                'text' : "No such report exists!",
                confirmButtonColor: '#43A4D7'
            });
            return;
        }
        let newDisputeId = `${disputeDto.report_id}.`+`${disputeDto.match_number}`;
        const disputesRef = doc(db, `event/${disputeDto.event_id}/disputes`, newDisputeId);
        await setDoc(disputesRef, disputeDto, { merge: true });
        
        const reportData = reportSnap.data();
        let disputeResolved = Array.isArray(reportData.disputeResolved) ? 
            [...reportData.disputeResolved] : [null, null, null];
        disputeResolved[disputeDto.match_number] = true;
        
        await updateDoc(reportRef, {
            disputeResolved: disputeResolved
        });

        Swal.fire({
            icon: 'success',
            'text' : "Dispute saved successfully!",
            confirmButtonColor: '#43A4D7'

        });
        
    } catch (error) {
        console.error("Error saving dispute: ", error);
        toast.error("Error saving dispute: " + error.message);
    }
}


function createDisputes () {
    const teamsDataInput = document.getElementById('teams-data');
    const teamsData = JSON.parse(teamsDataInput.value);
    const disputesDataInput = document.getElementById('disputes-data');
    const disputesData = JSON.parse(disputesDataInput.value);
    const usersDataInput = document.getElementById('users-data');
    const usersData = JSON.parse(usersDataInput.value);
    const disputeRolesInput = document.getElementById('dispute-roles-data');
    const disputeRolesData = JSON.parse(disputeRolesInput.value);
    const setupDataInput = document.getElementById('setup-data');
    const setupData = JSON.parse(setupDataInput.value);
    const eventId = document.getElementById('event-id').value;

    return {
        init() {
            console.log("Disputes initiaed!");
        },

        teams: teamsData,
        disputes: disputesData,
        disputeRoles : disputeRolesData,
        setups: setupData,
        users: usersData,
        selectedDispute: null,
        resolutionData: {
            winner: "0",
            resolved_by: "",
            notes: ""
        },
        
        createNewDispute: function() {
            this.selectedDispute = {
                id: '',
                dispute_userId: '',
                resolution_resolved_by: null,
                created_at: new Date().toISOString(),
                response_explanation: null,
                dispute_description: '',
                response_teamNumber: null,
                report_id: '',
                match_number: '',
                response_userId: null,
                dispute_reason: '',
                dispute_image_videos: [],
                resolution_winner: "0",
                updated_at: new Date().toISOString(),
                response_teamId: null,
                dispute_teamNumber: '',
                event_id: eventId,
                dispute_teamId: ''
            };
            
            const modalTitle = document.getElementById('disputeModalTitle');
            if (modalTitle) {
                modalTitle.textContent = 'Create New Dispute';
            }
            
            const form = document.getElementById('disputeForm');
            if (form) {
                form.setAttribute('data-mode', 'create');
            }
        },
        
        openModal: function(dispute) {
            this.selectedDispute = JSON.parse(JSON.stringify(dispute));
            
            const modalTitle = document.getElementById('disputeModalTitle');
            if (modalTitle) {
                modalTitle.textContent = 'Dispute Details';
            }
            
            const form = document.getElementById('disputeForm');
            if (form) {
                form.setAttribute('data-mode', 'edit');
            }
        },
        
        openResolutionModal: function(dispute) {
            this.selectedDispute = JSON.parse(JSON.stringify(dispute));
            this.resolutionData = {
                winner: dispute.resolution_winner || "0",
                resolved_by: dispute.resolution_resolved_by || "",
                notes: ""
            };
        },
        
        saveChanges: async function() {
            if (!this.selectedDispute) return;
            
            const formElement = document.getElementById('disputeForm');
            if (!formElement) return;
            
            const formData = new FormData(formElement);
            const formValues = {};
            for (const [key, value] of formData.entries()) {
                formValues[key] = value;
            }
            
            for (const key in formValues) {
                if (key in this.selectedDispute) {
                    this.selectedDispute[key] = formValues[key];
                }
            }
            
            if (formValues.resolution_winner) {
                this.selectedDispute.resolution_winner = formValues.resolution_winner;
            }
            if (formValues.resolution_resolved_by) {
                this.selectedDispute.resolution_resolved_by = formValues.resolution_resolved_by;
            }
            
            const isCreating = formElement.getAttribute('data-mode') === 'create';
            
            if (isCreating && !this.selectedDispute.id) {
                if (this.selectedDispute.report_id && this.selectedDispute.match_number) {
                    this.selectedDispute.id = this.selectedDispute.report_id + '.' + this.selectedDispute.match_number;
                } else {
                    this.selectedDispute.id = 'D' + Date.now();
                }
            }
            
            this.selectedDispute.updated_at = new serverTimestamp();
            if (isCreating) {
                this.selectedDispute.created_at = new serverTimestamp();
            }
            
            const updatedDispute = { ...this.selectedDispute, ...formValues };
            console.log({updatedDispute, selectedDispute: this.selectedDispute});
            await saveDisputes(updatedDispute, this.selectedDispute);
            this.disputes = this.disputes.map((value) => {
                return (value.report_id == this.selectedDispute.report_id && value.match_number == this.selectedDispute.match_number) ?
                    {
                        ...this.selectedDispute,
                        ...updatedDispute
                    } : value 
            });
            
            const modalElement = document.getElementById('disputeModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
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
    }
   
           
}

document.addEventListener('DOMContentLoaded', async () => {
    createApp({
        createBrackets
    }).mount('#brackets');

    createApp({
        createDisputes
    }).mount('#disputes');
});