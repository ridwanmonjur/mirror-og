import { createApp, reactive } from "petite-vue";
import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, setDoc, getDoc, addDoc, onSnapshot, updateDoc, orderBy, doc, query, collection, where, or, clearIndexedDbPersistence } from "firebase/firestore";
// import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";
import { DateTime } from "luxon";


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

let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const chatInput = document.querySelector(".chat-input input");
const sendButton = document.querySelector(".chat-input button");
const chatMessages = document.querySelector(".chat-messages");
const chatItems = document.querySelectorAll(".chat-item");
const chatUserName = document.querySelector(".chat-user-name");

const fetchFirebaseUsersInputRoute = document.querySelector("#fetchFirebaseUsersInput");
const viewUserProfileInput = document.querySelector("#viewUserProfile");
const loggedUserProfileInput = document.querySelector("#loggedUserProfile");
let loggedUserProfile = JSON.parse(loggedUserProfileInput?.value ?? "[]");
let viewUserProfile = JSON.parse(viewUserProfileInput?.value ?? "[]");
let fetchFirebaseUsersRoute = fetchFirebaseUsersInputRoute?.value;

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function humanReadableChatDateFormat(date) {
    if (!date) return "Error occurred";
    const year = date?.getFullYear();
    let monthFromDate = date?.getMonth();
    const month = monthNames[monthFromDate] ?? null;
    const day = date?.getDate()?.toString()?.padStart(2, '0');

    const formattedDate = `${day} ${month} ${year}`;
    return formattedDate;
}

function scrollIntoView() {

    const chatContainer = document.getElementById('chat-messages');
    chatContainer.scrollTop = chatContainer.scrollHeight;
    window.scrollTo(0, document.body.scrollHeight);

}

function addDate(date, prepend = false) {
    const dateDivContainer = document.createElement("div");
    const dateDiv = document.createElement("small");
    dateDivContainer.classList.add("d-flex", "justify-content-center", "my-3");
    dateDiv.classList.add("mx-auto", "px-3", 'py-1', "rounded-pill");
    dateDiv.style.backgroundColor = "white";
    dateDiv.style.boxShadow = "0 4px 20px rgba(0, 0, 0, 0.2)";
    dateDiv.innerText = humanReadableChatDateFormat(date);
    dateDivContainer.appendChild(dateDiv);
    chatMessages.appendChild(dateDivContainer);
}

const chatStore = reactive({
    chatMessageList: [],
    chatSnapshots: null,
    messagesLength: 0,
    resetMessages() {
        this.messagesLength = 0;
        this.chatMessageList = [];
    },
    async getMessages(id) {

        let q = query(
            collection(db, `room/${id}/message`),
            orderBy("createdAt", "desc")
        );

        let isInitialDataFetched = false;
        let prevCreatedAt = null;
        let length = 0;
        let results = [];
        q = query(q);

        let subscribeToChat = onSnapshot(q, {
            includeMetadata: true,
        }, async (snapshot) => {
         

            snapshot.docChanges().forEach(async (change) => {

                if (change.type === "added") {
                    let objectDoc = {
                        id: change.doc.id,
                        ...change.doc.data(),
                    };

                    console.log({results});
                    if (objectDoc['senderId'] == loggedUserProfile.id) {
                        objectDoc['className'] = ['message', 'reply'];
                        objectDoc['isMe'] = true;
                    } else if (objectDoc['senderId'] != loggedUserProfile.id) {
                        objectDoc['className'] = ['message'];
                        objectDoc['isMe'] = false;
                    } else {
                        window.alert("Some error occurred");
                    }

                    objectDoc['sender'] = roomStore.roomUserIdMap[objectDoc['senderId']];
                    let currentDate = objectDoc['createdAt'].toDate();
                    objectDoc['createdAtDate'] = currentDate;


                    if (length) {
                        if (currentDate?.getDate() != prevCreatedAt?.getDate() 
                            || currentDate ?.getMonth() != prevCreatedAt?.getMonth()
                            || currentDate?.getYear() != prevCreatedAt?.getYear()
                        ) {
                            objectDoc['isLastDateShow'] = true;
                            objectDoc['lastDate'] = prevCreatedAt;
                        } 
                    } 

                    prevCreatedAt = objectDoc['createdAtDate'];
                    length++;
                   
                    if (isInitialDataFetched) {
                        results.push(objectDoc);
                    } else {
                        results.unshift(objectDoc);
                    }
                }

            });

            
            this.messagesLength = this.messagesLength ? this.messagesLength + length: length;
            
            this.chatMessageList = [...(this.chatMessageList || []), ...results];
            
                // if (this.currentRoom == id) {
                //     // this.appendMessages(results, length);
                //     scrollIntoView();
                //     let lastMsgInBatch = results[length-1];
                //     if (lastMsgInBatch && lastMsgInBatch?.senderId != loggedUserProfile?.id && !lastMsgInBatch.isRead) {
                //         const messageRef = doc(db, `room/${this.currentRoom}/message`, lastMsgInBatch.id);
                //         await updateDoc(messageRef, {
                //             isRead: true
                //         });
                //     } 
                // } else {
                //     let lastMsgInBatch = results[length-1];
                //     if (lastMsgInBatch && (lastMsgInBatch.senderId != loggedUserProfile.id && !lastMsgInBatch.isRead)) {
                //         const element = document.querySelector(`[data-identity-for-read="${id}"]`);
                //         element.style.backgroundColor = 'black';
                //         element.querySelector('.bi-bell-fill').classList.remove('d-none');
                //     }
                // }

            isInitialDataFetched = true;
        }, (error)=>{
            console.error({error})
            toastError("Failed to fetch data");
        });

        this.chatSnapshots = subscribeToChat;
    },
});

// chatMessageList put in roomStore
const roomStore = reactive({
    roomUserIdMap: {},
    oldRooms: [],
    currentRoom: null,
    roomSnapshot: null,
   
    initDB() {
        let isInitialDataFetched = false;
        let roomUserIdMap = {};
        let currentUserId = loggedUserProfile?.id;
        let rooms = [], userIdList = [];
        const roomCollectionRef = collection(db, 'room');

        const roomQ = query(roomCollectionRef,
            or(
                where("user1", "==", String(currentUserId)),
                where("user2", "==", String(currentUserId))
            )
        );

        let subscribeToRoomSnapshot = onSnapshot(roomQ, async (rommSnapshot) => {
        let length = 0;

            rommSnapshot.docChanges().forEach((change) => {
                if (change.type === "added") {
                    let data = change.doc.data();
                    data['id'] = change.doc.id;
                    if (data.user1 != currentUserId) {
                        userIdList.push(data.user1);
                        data.otherRoomMemberId = data.user1;
                    } else {
                        userIdList.push(data.user2);
                        data.otherRoomMemberId = data.user2;
                    }

                    if (isInitialDataFetched) {
                        rooms.unshift(data);
                    } else {
                        rooms.push(data);
                    }

                    length++;
                }
                if (change.type === "modified") {
                    console.log("Modified city: ", change.doc.data());
                }
            });

            let route = fetchFirebaseUsersInputRoute.value;

            let users = await fetch(route, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    userIdList
                })
            });

            users = await users.json();
            for (let user of users?.data) {
                roomUserIdMap[user?.id] = user;
            }

            roomUserIdMap[loggedUserProfile.id] = loggedUserProfile;

            for (let room of rooms) {
                room['otherRoomMember'] = roomUserIdMap[room['otherRoomMemberId']];
            }

            let currentRoomIndex = 0;
            // params 
            if ( viewUserProfile?.id ) {

                if (viewUserProfile.id in roomUserIdMap) {
                    currentRoomIndex = rooms.findIndex(room => 
                        room.otherRoomMemberId == viewUserProfile.id
                    );
                } else {
                    roomUserIdMap[viewUserProfile.id] = viewUserProfile;
                    let newRoomData = {
                        user1: Number(loggedUserProfile.id).toString(),
                        user2: Number(viewUserProfile.id).toString(),
                        otherRoomMember: { ...viewUserProfile },
                        otherRoomMemberId: viewUserProfile.id,
                    }

                    let roomId = newRoomData.user1 + '.' + newRoomData.user2;
                    await setDoc(doc(db, "room", roomId), {
                        user1: newRoomData.user1,
                        user2: newRoomData.user2,
                        createdAt: new Date(),
                        id: newRoomData.user1 + '.' + newRoomData.user2
                    });

                    currentRoomIndex = length;
                }
              
            } else {
                currentRoomIndex = 0
            }

            this.roomUserIdMap = { ...roomUserIdMap };
            this.oldRooms = [ ...rooms ];
            this.currentRoom = currentRoomIndex;
            console.log({oldRooms: rooms, currentRoomIndex});
            chatStore.getMessages(rooms[currentRoomIndex].id);

            scrollIntoView();
        });

        this.roomSnapshot = subscribeToRoomSnapshot;
    },
    async setCurrentRoom(index) {
        this.currentRoom = index;
        if (index != null) {
            chatStore.chatSnapshots();
            chatStore.resetMessages();
            await chatStore.getMessages( this.oldRooms[index].id );
        }

    },
    get currentRoomObj() {
        if (this.currentRoom != null) return this.oldRooms[this.currentRoom];
        else return null;
    }
});


function ChatListComponent() {
    // chatMessageList put in component
    return {
        get chatMessageList() {
            return chatStore.chatMessageList;
        },
        get currentRoomObj() {
            return roomStore.currentRoomObj;
        },
        get currentRoom () {
            return roomStore.currentRoom;
        },
        humanReadableChatTimeFormat(date) {
            const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
            return formattedTime
        },
        async sendMessage() {
            let value = chatInput?.value;
            
            if (this.currentRoom === null ) {
                window.toastError("Choose a conversation first!");
                return;
            }

            if ( String(value).trim() == "") {
                window.toastError("Empty messages!")
                return;
            }

            try{
                if (this.currentRoom == null) {
                   window.toastError("New chat still being updated...")
                }

                await addDoc(collection(db,  `room/${this.currentRoomObj.id}/message`), {
                    senderId: loggedUserProfile.id,
                    text: value,
                    createdAt: new Date(),
                    isRead: false,
                });

            } catch(err){
                console.error(err);
            }

            chatInput.value = "";
        },
    }
}

function SendMessage() {
    
}


function OtherUsersComponent() {
    return {

    }
}

function RoomComponent() {
    return {
        get oldRooms() {
            return roomStore.oldRooms;
        },
        get currentRoom() {
            return roomStore.currentRoom;
        },
        get currentRoomObj() {
            return roomStore.currentRoomObj;
        },
        setCurrentRoom(roomIndex) {
            roomStore.setCurrentRoom(roomIndex);
        },
        formatDate(date) {
            if (!date) return "N/A"
            let newDate = DateTime
                .fromFormat(date, "yyyy-MM-dd HH:mm:ss")
                .toRelative();

            console.log({ newDate });
            console.log({ newDate });
            console.log({ newDate });

            return newDate;
        },
        mounted() {
            roomStore.initDB();
        }
    }
}


document.addEventListener('DOMContentLoaded', () => {
    createApp({
        RoomComponent,
        ChatListComponent,
        OtherUsersComponent,
        
        // Add other components here
    }).mount('#app');
});

// window.loadPetiteView = () => {
//     createApp({
//         // ChatContainerComponent,
//         // ChatListComponent,
//         // OtherUsersComponent,
//         RoomComponent
//     }).mount("#app");
// }