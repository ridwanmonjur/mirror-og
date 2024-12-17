import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, setDoc, addDoc, onSnapshot, updateDoc, orderBy, doc, query, collection,  where, or, clearIndexedDbPersistence } from "firebase/firestore";
// import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";
import { DateTime } from "luxon";

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

function firstMessageDatAppend (message) {
    addDate(message['createdAtDate']);
}



function prependMessages(messages, length) {
    for (let i = 0; i < length; i++) {
        const message = messages[length-i];
        if (message['isLastDateShow']) {
            addDate(message['lastDate']);
        }
    
        // appendOrPrependMessageItem(message, true);
    }
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


Alpine.data('alpineDataComponent', function () {
    return {
        isDataInited: false,
        currentRoom: null,
        currentRoomObject: null,
        newRoom: null,
        oldRooms: [],
        messages: {},
        chatMessageList: [],
        currentId: null,
        chats: [],
        prospectiveChats: {
            data: [],
            links: []
        },
        roomUserIdMap: {},
        roomSnapshot: null,
        chatSnapshots: [],
        messagesLength: {},
        humanReadableChatTimeFormat(date) {
            const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
            return formattedTime
        },
        appendMessages(messages, length) {
            for (let i = 0; i < length; i++) {
                const message = messages[i];
                if (message['isLastDateShow'] ) {
                    // addDate(message['lastDate']);
                }
        
        
                // appendOrPrependMessageItem(message);
            }
            console.log({messages});
            if (length) this.chatMessageList = messages;
        
        },
        changeUser(user) {

            if (user?.id && user?.id == loggedUserProfile?.id) {
                window.toastError("You can't send messages to yourself");
                return; 
            }

            if (user?.id in this.roomUserIdMap) {
                let currentRoomObject = this.oldRooms?.filter((value)=>{
                    return value.otherRoomMemberId == user?.id;
                });
                
                if (currentRoomObject && currentRoomObject[0]) {
                    this.currentRoom = currentRoomObject[0].id;

                } else {
                    window.toastError("Current room is missing...")
                }
            } else {
                // chatMessages.innerHTML = '';
                window.dialogOpen("Are you sure you want to start a new chat with this person ?", 
                    async () => {
                        this.currentRoom = "newRoom";
                        let currentRoomObject = {
                            user1: Number(loggedUserProfile.id).toString(),
                            user2: Number(user?.id).toString()
                        }

                        this.currentRoomObject = {
                            ...currentRoomObject,
                            otherRoomMember: { ...user, name: "Loading new user..." },
                            otherRoomMemberId: user?.id,
                        }
                    
                        await setDoc(doc(db, "room", currentRoomObject.user1 + '.' + currentRoomObject.user2), {
                            user1: currentRoomObject.user1,
                            user2: currentRoomObject.user2,
                            createdAt: new Date(),
                            id: currentRoomObject.user1 + '.' + currentRoomObject.user2
                        });
                    }, null
                )  
            }
        },
        async sendMessage() {
            let value = chatInput?.value;
            
            if (!this.currentRoom ) {
                window.toastError("Choose a conversation first!");
                return;
            }

            if ( String(value).trim() == "") {
                window.toastError("Empty messages!")
                return;
            }

            try{
                if (this.currentRoom == "newRoom") {
                   window.toastError("New chat still being updated...")
                }

                await addDoc(collection(db,  `room/${this.currentRoom}/message`), {
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
        async fetchProspectiveChatters(event = null) {
            if (event == null && this.prospectiveChats?.data[0]) {
                return;
            }

            let route;
            if (event?.target?.dataset?.url){
                route = event.target.dataset.url;
            } else {
                route = fetchFirebaseUsersInputRoute.value;
            }
            
            let users = await fetch(route, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify({
                    searchQ: event?.target?.value ?? null,
                })
            });

            users = await users.json();
            this.prospectiveChats = users?.data;
        },
        initDB() {
            if (this.isDataInited) return;
            // todo 1: fix new message
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
                await this.startChat();
            });

            this.roomSnapshot = subscribeToRoomSnapshot;
        },

        formatDate(date) {
            return DateTime
                .fromJSDate(date)
                .toRelative();
        },
        
        async getMessages(id) {

            let q = query(
                collection(db, `room/${id}/message`),
                orderBy("createdAt", "desc")
            );

            let isInitialDataFetched = false;
            let prevCreatedAt = null;


            q = query(q);

            let subscribeToChat = onSnapshot(q, {
                includeMetadata: true,
            }, async (snapshot) => {
                let results = [];
                let length = 0;
                snapshot.docChanges().forEach(async (change) => {
                    if (change.type === "added") {
                        let objectDoc = {
                            id: change.doc.id,
                            ...change.doc.data(),
                        };

                        if (objectDoc['senderId'] == loggedUserProfile.id) {
                            objectDoc['className'] = ['message', 'reply'];
                            objectDoc['isMe'] = true;
                        } else if (objectDoc['senderId'] != loggedUserProfile.id) {
                            objectDoc['className'] = ['message'];
                            objectDoc['isMe'] = false;
                        } else {
                            window.alert("Some error occurred");
                        }

                        objectDoc['sender'] = this.roomUserIdMap[objectDoc['senderId']];
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

                

                

                this.messagesLength[id] = this.messagesLength[id] ? this.messagesLength[id] + length: length;
                
                this.messages[id] = [...(this.messages[id] || []), ...results];
                
                if (this.currentRoom == id) {
                    this.appendMessages(results, length);
                    scrollIntoView();
                    let lastMsgInBatch = results[length-1];
                    if (lastMsgInBatch && lastMsgInBatch?.senderId != loggedUserProfile?.id && !lastMsgInBatch.isRead) {
                        const messageRef = doc(db, `room/${this.currentRoom}/message`, lastMsgInBatch.id);
                        await updateDoc(messageRef, {
                            isRead: true
                        });
                    } 
                } else {
                    let lastMsgInBatch = results[length-1];
                    if (lastMsgInBatch && (lastMsgInBatch.senderId != loggedUserProfile.id && !lastMsgInBatch.isRead)) {
                        const element = document.querySelector(`[data-identity-for-read="${id}"]`);
                        element.style.backgroundColor = 'black';
                        element.querySelector('.bi-bell-fill').classList.remove('d-none');
                    }
                }

                isInitialDataFetched = true;
            }, (error)=>{
                console.error({error})
                toastError("Failed to fetch data");
            });

            this.chatSnapshots.push(subscribeToChat);
        },
       startChat(){
            let url = new URL(window?.location?.href);
            let searchParams = new URLSearchParams(url?.search);
            const userId = searchParams?.get('userId');
            
            if (userId && userId != loggedUserProfile?.id) {
                this.changeUser(viewUserProfile);
            } 
        },
        init() {
            this.initDB();

            
            this.$watch("currentRoom", async () => {
                if (this.currentRoom == "newRoom") {
                    return;
                }

                let currentRoomObject = this.oldRooms?.filter((value)=>{
                    return value.id == this.currentRoom;
                });

                this.currentRoomObject = currentRoomObject[0];
                // chatMessages.innerHTML = '';
                let length =  this.messagesLength[this.currentRoom];
                let messages = this.messages[this.currentRoom]; 
                if (isNaN(length))  {
                    return;
                } 

                const element = document.querySelector(`[data-identity-for-read="${this.currentRoom}"]`);
                let lastMsgInBatch = messages[length-1];
                if (lastMsgInBatch && lastMsgInBatch?.senderId != loggedUserProfile?.id && !lastMsgInBatch.isRead) {
                    element.style.backgroundColor = 'transparent';
                    element.querySelector('.bi-bell-fill').classList.add('d-none');
                    const messageRef = doc(db, `room/${this.currentRoom}/message`, lastMsgInBatch.id);
                    await updateDoc(messageRef, {
                        isRead: true
                    });
                } 

                document.querySelectorAll('.chat-item').forEach((item)=>{
                    if (item.style.backgroundColor != 'black') {
                        item.style.backgroundColor = 'transparent';
                    }
                });
                
                element.style.backgroundColor = '#72777F';
                console.log({messages, 0: messages[0]});
                // firstMessageDatAppend(messages[0]);


                this.appendMessages(
                    messages,
                    length
                );
                
                scrollIntoView();
            })

        },
        destroy() {
            if (this.roomSnapshot) this.roomSnapshot();
            for (let snap in this.chatSnapshots) {
                snap();
            }
        }
    }
});



Alpine.start();

document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        document.getElementById('sendMessageBtn').click();
    }
});
