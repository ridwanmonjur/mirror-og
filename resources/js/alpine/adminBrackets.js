import { getDoc, updateDoc, initializeFirestore, memoryLocalCache, doc } from "firebase/firestore";
import { createApp }  from "petite-vue";
import { initializeApp } from "firebase/app";



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
            }

            const modalElement = document.getElementById(type === 'brackets' ? 'detailsModal' : 'scoresModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    };
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
                event_id: '',
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
        
        saveChanges: function(type) {
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
            
            this.selectedDispute.updated_at = new Date().toISOString();
            if (isCreating) {
                this.selectedDispute.created_at = new Date().toISOString();
            }
            
            if (isCreating) {
                console.log('Creating new dispute:', this.selectedDispute);
                this.disputes.push(JSON.parse(JSON.stringify(this.selectedDispute)));
            } else {
                console.log('Saving changes for dispute:', this.selectedDispute.id);
                const index = this.disputes.findIndex(d => d.id === this.selectedDispute.id);
                if (index !== -1) {
                    this.disputes.splice(index, 1, JSON.parse(JSON.stringify(this.selectedDispute)));
                    console.log('Updated dispute in array');
                }
            }
            
            // Here you would add API call logic to save to server
            // For example:
            // const endpoint = isCreating ? '/api/disputes' : '/api/disputes/' + this.selectedDispute.id;
            // const method = isCreating ? 'POST' : 'PUT';
            // fetch(endpoint, {
            //     method: method,
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify(this.selectedDispute)
            // })
            
            // Close the modal after saving
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