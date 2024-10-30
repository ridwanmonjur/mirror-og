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
          matchId: null,
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
          console.log({userLevel: this.report.userLevel, IS_ORGANIZER: this.userLevelEnums['IS_ORGANIZER']})
          if (this.report.userLevel === this.userLevelEnums['IS_ORGANIZER']) {
            this.report.organizerWinners[matchNumber] = selectedTeamIndex;
            this.report.realWinners[matchNumber] = selectedTeamIndex;
          }

          if (this.report.userLevel === this.userLevelEnums['IS_TEAM1'] || this.report.userLevel === this.userLevelEnums['IS_TEAM2']) {
            this.report.teams[teamNumber].winners[matchNumber] = selectedTeamIndex;
            let otherTeamIndex = this.report.teams[otherTeamNumber].winners[matchNumber];
            if (otherTeamIndex) {
              if (otherTeamIndex === selectedTeamIndex) {
                this.report.realWinners[matchNumber] = selectedTeamIndex;
              } 
            }
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
            this.report.teams[teamNumber].winners[matchNumber] = otherIndex;
            this.report.realWinners[matchNumber] = otherIndex;
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
              team2Winners: this.report.teams[0].winners,
            });
            console.log("Document written with ID: ", customDocId);
            console.log({array: Alpine.raw(this.report)});
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
          
              // const selectionMessage = document.querySelector('.selectionMessage');
              // selectionMessage.innerText = ""; 
          }); 
        },
        changeMatchNumber(increment) {
          this.clearSelection();
          this.reportUI.matchNumber = this.reportUI.matchNumber + increment; 
        },
        getDisabled() {
            return this.reportUI.disabled[this.reportUI.matchNumber] ;
        },
       
        init() {
            this.getAllMatchStatusesData();
            window.addEventListener('currentReportChange', (event) => {
                if (this.subscribeToMatchStatusesSnapshot) this.subscribeToMatchStatusesSnapshot();

                let dataset = event?.detail ?? null;
                let teamNumber = dataset.userLevel === userLevelEnums['IS_TEAM1'] ? 0 :
                  (dataset.userLevel === userLevelEnums['IS_TEAM2'] ? 1 : 0 );
                let otherTeamNumber =  !teamNumber;
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
                // for (let snap in this.chatSnapshots) {
                //     snap();
                // }
            },
            getAllMatchStatusesData() {
              const allMatchStatusesCollectionRef = collection(db, `event/${eventId}/match_status`);
              const allMatchStatusesQ = query(allMatchStatusesCollectionRef);
              let allDataList = {};
              let subscribeToMatchStatusesSnapshot = onSnapshot(allMatchStatusesQ, async (reportSnapshot) => {
                  reportSnapshot.docChanges().forEach( (change) => {
                      if (change.type === "added" || change.type === "modified") {
                        let data = change.doc.data();
                        allDataList[change.doc.id] = data;

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
                    console.log({dataHi:data, id: reportSnapshot.id});
                    let { score, 
                        status: matchStatus, 
                        winners: realWinners, 
                        organizerWinners,
                        team1Winners,
                        team2Winners
                      } = data;

                    this.report = {
                      ...this.report,
                      organizerWinners,

                      matchId: reportSnapshot.id,
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
                      let disabledList = [false, false, false];
                      for (let index = 0; index <= 2; index++) {
                        disabledList[index] = 
                          this.report.teams[this.reportUI.teamNumber].winners[(this.reportUI.matchNumber-1) % 3] === null;
                      }

                      this.reportUI.disabled = [...disabledList];
                    }

                // !== null || 

                } else {
                  console.log("No such document!");
                }
              }
            );

            this.subscribeToCurrentReportSnapshot = subscribeToCurrentReportSnapshot;
          }
        }
    });
    
        // changeUser(user) {
        //     user = Alpine.raw(user);  

        //     if (user?.id && user?.id == loggedUserProfile?.id) {
        //         window.toastError("You can't send messages to yourself");
        //         return; 
        //     }

        //     if (user?.id in this.roomUserIdMap) {
        //         let currentRoomObject = Alpine.raw(this.oldRooms)?.filter((value)=>{
        //             return value.otherRoomMemberId == user?.id;
        //         });
                
        //         if (currentRoomObject && currentRoomObject[0]) {
        //             this.currentRoom = currentRoomObject[0].id;
        //         } else {
        //             window.toastError("Current room is missing...")
        //         }
        //     } else {
        //         chatMessages.innerHTML = '';
        //         window.dialogOpen("Are you sure you want to start a new chat with this person ?", 
        //             async () => {
        //                 this.currentRoom = "newRoom";
        //                 let currentRoomObject = {
        //                     user1: Number(loggedUserProfile.id).toString(),
        //                     user2: Number(user?.id).toString()
        //                 }

        //                 this.currentRoomObject = {
        //                     ...currentRoomObject,
        //                     otherRoomMember: { ...user, name: "Loading new user..." },
        //                     otherRoomMemberId: user?.id,
        //                 }
                    
        //                 await addDoc(collection(db,  "room"), {
        //                     user1: currentRoomObject.user1,
        //                     user2: currentRoomObject.user2,
        //                     createdAt: new Date()
        //                 });
        //             }, null
        //         )  
        //     }
        // },
        // async sendMessage() {
           
        //         await addDoc(collection(db,  `room/${this.currentRoom}/message`), {
        //             senderId: loggedUserProfile.id,
        //             text: value,
        //             createdAt: new Date(),
        //             isRead: false,
        //         });

          
        // },
        
       
        // async handleScrollChat() {
        //     let id =  this.currentRoom;
            
        //     if (!this.messages[id] || !this.messages[id][0]) {
        //         return;
        //     }

        //     let q = query(
        //         collection(db, `room/${id}/message`),
        //         orderBy("createdAt", "desc")
        //     );

        //     let firstMsg = this.messages[id][0];
        //     q = query(q, startAfter(firstMsg));
        //     q = query(q, limit(5));
        //     const querySnapshot = await getDocs(q);
        //     let results = [];
        //     let prevCreatedAt = this.messages[id][0]['createdAtDate'];
        //     let length = 0;
        //     querySnapshot.forEach((doc) => {
        //         let objectDoc = {
        //             id: doc.id,
        //             ...doc.data(),
        //         };

        //         if (objectDoc['senderId'] == loggedUserProfile.id) {
        //             objectDoc['className'] = ['message', 'reply'];
        //             objectDoc['isMe'] = true;
        //         } else if (objectDoc['senderId'] != loggedUserProfile.id) {
        //             objectDoc['className'] = ['message'];
        //             objectDoc['isMe'] = false;
        //         } else {
        //             window.alert("Some error occurred");
        //         }

        //         objectDoc['sender'] = Alpine.raw(this.roomUserIdMap)[objectDoc['senderId']];
        //         objectDoc['createdAtDate'] = objectDoc['createdAt'].toDate();
        //         if (length) {
        //             if (objectDoc['createdAtDate']?.getDate() !== prevCreatedAt?.getDate() 
        //                 || objectDoc['createdAtDate'] ?.getMonth() !== prevCreatedAt?.getMonth()
        //                 || objectDoc['createdAtDate'] ?.getYear() !== prevCreatedAt?.getYear()
        //             ) {
        //                 objectDoc['isLastDateShow'] = true;
        //                 objectDoc['lastDate'] = prevCreatedAt;
        //             } 
        //         } 

        //         prevCreatedAt = objectDoc['createdAtDate'];
        //         length++;
        //         results.unshift(objectDoc);


        //     });
        //     this.messagesLength[id] = length;
        //     if (length) {
        //         results[0]['isLastDateShow'] = true;
        //         results[0]['lastDate'] = results[0]['createdAtDate'];
        //     }

        //     // send to outside
        //     if (id in this.messages) {
        //         this.messages[id] = this.messages[id].concat(results);
        //     } else {
        //         this.messages[id] = results;
        //     } 
            
        //     if (this.currentRoom == id) {
        //         appendMessages(results, length);
        //         scrollIntoView("bottom", length);
        //     }
        // },
        // async getMessages(id) {

        //     let q = query(
        //         collection(db, `room/${id}/message`),
        //         orderBy("createdAt", "desc")
        //     );

        //     let isInitialDataFetched = false;
        //     let prevCreatedAt = null;


        //     q = query(q, limit(10));

        //     let subscribeToChat = onSnapshot(q, {
        //         includeMetadata: true,
        //     }, async (snapshot) => {
        //         let results = [];
        //         let length = 0;
        //         snapshot.docChanges().forEach(async (change) => {
        //             if (change.type === "added") {
        //                 let objectDoc = {
        //                     id: change.doc.id,
        //                     ...change.doc.data(),
        //                 };

        //                 if (objectDoc['senderId'] == loggedUserProfile.id) {
        //                     objectDoc['className'] = ['message', 'reply'];
        //                     objectDoc['isMe'] = true;
        //                 } else if (objectDoc['senderId'] != loggedUserProfile.id) {
        //                     objectDoc['className'] = ['message'];
        //                     objectDoc['isMe'] = false;
        //                 } else {
        //                     window.alert("Some error occurred");
        //                 }

        //                 objectDoc['sender'] = Alpine.raw(this.roomUserIdMap)[objectDoc['senderId']];
        //                 let currentDate = objectDoc['createdAt'].toDate();
        //                 objectDoc['createdAtDate'] = currentDate;


        //                 if (length) {
        //                     if (currentDate?.getDate() != prevCreatedAt?.getDate() 
        //                         || currentDate ?.getMonth() != prevCreatedAt?.getMonth()
        //                         || currentDate?.getYear() != prevCreatedAt?.getYear()
        //                     ) {
        //                         objectDoc['isLastDateShow'] = true;
        //                         objectDoc['lastDate'] = prevCreatedAt;
        //                     } 
        //                 } 

        //                 prevCreatedAt = objectDoc['createdAtDate'];
        //                 length++;
                       
        //                 if (isInitialDataFetched) {
        //                     results.push(objectDoc);
        //                 } else {
        //                     results.unshift(objectDoc);
        //                 }
        //             }

        //         });

        //     });

        //     const messageRef = doc(db, `room/${this.currentRoom}/message`, lastMsgInBatch.id);
        //     await updateDoc(messageRef, {
        //         isRead: true
        //     });
                   
                  
        //     this.chatSnapshots.push(subscribeToChat);
        // },
       
        // init() {
        //     this.initDB();
        //     document.querySelector('#chat-messages').addEventListener("scroll", throttle2((e) => { 
        //         const element = e.target;
        //         const scrollTop = element.scrollTop;

        //         if (scrollTop < 100 && scrollTop < (element.oldScroll ?? Infinity)) {
        //             this.handleScrollChat()
        //         }

        //         element.oldScroll = scrollTop;
        //     }, 400));
            
        // },
        



Alpine.start();

