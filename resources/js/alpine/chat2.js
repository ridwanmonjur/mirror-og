import { createApp, reactive } from "petite-vue";
import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, setDoc, addDoc, onSnapshot, updateDoc, orderBy, doc, query, collection,  where, or, clearIndexedDbPersistence } from "firebase/firestore";
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
    const month = monthNames [monthFromDate] ?? null;
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


// chatMessageList put in roomStore
const roomStore = reactive({
    chatMessageList: [],
    roomUserIdMap: {}, 
    oldRooms: [],
    currentRoom: null,
    roomSnapshot: null,
    initDB() {
        let isInitialDataFetched = false;
        let roomUserIdMap = {};
        let currentUserId = loggedUserProfile?.id;
        let rooms = [], userIdList = [];
        let length = 0;
        const roomCollectionRef = collection(db, 'room');

        const roomQ = query(roomCollectionRef,
            or(
                where("user1", "==", String(currentUserId)),
                where("user2", "==", String(currentUserId))
            )
        );

        let subscribeToRoomSnapshot = onSnapshot(roomQ, async (rommSnapshot) => {
            rommSnapshot.docChanges().forEach( (change) => {
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
            this.roomUserIdMap = {...roomUserIdMap };
           
               
            this.oldRooms = [...rooms];
            isInitialDataFetched = true;
        });

        

        this.roomSnapshot = subscribeToRoomSnapshot;
    },
    get currentRoomObj() {
        return this.roomStore.oldRooms[this.roomStore.currentRoom]
      }
});


function ChatContainer() {
    return {
        $template: '#chat-parent-component',
        fetchData: () => roomStore.initDB(),
    
        mounted() {
            this.fetchData();
        }
    }
}


function ChatList() {
    // chatMessageList put in component
    return {
        $template: '#chat-component',
        chatMessageList: roomStore.chatMessageList,
        currentRoomObj: roomStore.currentRoomObj,
        humanReadableChatTimeFormat(date) {
            const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
            return formattedTime
        },
    }
}


function OtherUsers() {
    return {
      $template: '#other-users-component',
      
    }
}

function RoomComponent() {
    return {
      $template: '#room-component',
      oldRooms: roomStore.oldRooms,
    }
}

createApp({
    ChatContainer,
    ChatList,
    OtherUsers,
    RoomComponent
}).mount('#app')