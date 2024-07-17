import { initializeApp } from "firebase/app";
import { initializeFirestore, memoryLocalCache, addDoc, onSnapshot, getDocsFromCache, startAfter, limit, orderBy, doc, query, collection, collectionGroup, getDocs, getDoc, where, or } from "firebase/firestore";
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



const chatInput = document.querySelector(".chat-input input");
const sendButton = document.querySelector(".chat-input button");
const chatMessages = document.querySelector(".chat-messages");
const chatItems = document.querySelectorAll(".chat-item");
const chatUserName = document.querySelector(".chat-user-name");

const fetchFirebaseUsersInputRoute = document.querySelector("#fetchFirebaseUsersInput");
const viewUserProfileInput = document.querySelector("#viewUserProfile");
const loggedUserProfileInput = document.querySelector("#loggedUserProfile");
let loggedUserProfile = JSON.parse(loggedUserProfileInput?.value);
let viewUserProfile = JSON.parse(viewUserProfileInput?.value);
let fetchFirebaseUsersRoute = fetchFirebaseUsersInputRoute?.value;

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function humanReadableChatTimeFormat(date) {
    const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    return formattedTime
}

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
    let numberOfChildren = chatMessages.children?.length;
    
    if (numberOfChildren) {
        chatMessages.children[numberOfChildren-1]?.scrollIntoView();
    }
}

function loadMessages(messages) {
    messages?.forEach((message, index) => {
        if (message['isLastDateShow'] ) {
            addDate(message['lastDate']);
        }

        addMessage(message);

    });
}

function addDate(date) {
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

function addMessage(message) {
    let {text, createdAtDate, className, sender} = message;
    const messageDiv = document.createElement("div");
    messageDiv.classList.add(...className);
    let avatarDiv;
    if (sender?.userBanner) {
        avatarDiv = document.createElement("img");
        avatarDiv.src = `/storage/${sender?.userBanner}`;
        avatarDiv.height = "50";
        avatarDiv.onerror= ()=> {
            avatarDiv.src = '/assets/images/404.png';
        }

        avatarDiv.width = "50";
        avatarDiv.classList.add('object-fit-cover', 'rounded-circle')
    } else {
        avatarDiv = document.createElement("div");
        avatarDiv.classList.add("avatar");
        avatarDiv.textContent = sender.name ? 
            sender.name?.charAt(0)?.toUpperCase(): sender.email[0]?.toUpperCase();
    }

    avatarDiv.classList.add('me-2')

    const messageContentDiv = document.createElement("div");
    messageContentDiv.classList.add("message-content", 'w-75');
    messageContentDiv.textContent = text;

    const timestampSpan = document.createElement("span");
    timestampSpan.classList.add("timestamp");
    timestampSpan.textContent = humanReadableChatTimeFormat(createdAtDate);

    messageContentDiv.appendChild(timestampSpan);
    messageDiv.appendChild(avatarDiv);
    messageDiv.appendChild(messageContentDiv);
    chatMessages.appendChild(messageDiv);
}

Alpine.data('alpineDataComponent', function () {
    return {
        isDataInited: false,
        currentRoom: null,
        currentRoomObject: null,
        newRoom: null,
        oldRooms: [],
        messages: {},
        currentId: null,
        lastMsgInBatch: {},
        chats: [],
        prospectiveChats: {
            data: [],
            links: []
        },
        roomUserIdMap: {},
        roomSnapshot: null,
        chatSnapshots: [],
        async changeUser(user) {
            user = Alpine.raw(user);  

            if (user?.id && user?.id == loggedUserProfile?.id) {
                window.toastError("You can't send messages to yourself");
                return; 
            }

            if (user?.id in this.roomUserIdMap) {
                let currentRoomObject = Alpine.raw(this.oldRooms)?.filter((value)=>{
                    return value.otherRoomMemberId == user?.id;
                });
                
                if (currentRoomObject && currentRoomObject[0]) {
                    this.currentRoom = currentRoomObject[0].id;
                } else {
                    window.toastError("Current room is missing...")
                }
            } else {
                chatMessages.innerHTML = '';
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

                        await addDoc(collection(db,  "room"), {
                            user1: currentRoomObject.user1,
                            user2: currentRoomObject.user2
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
                    createdAt: new Date()
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
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    searchQ: event?.target?.value ?? null,
                })
            });

            users = await users.json();
            this.prospectiveChats = users?.data;
        },
        async initDB() {
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
                rommSnapshot.docChanges().forEach(async (change) => {
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
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
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
                this.roomUserIdMap = roomUserIdMap;
                console.log({ogRooms: rooms, rooms: Alpine.raw(this.oldRooms)});
                if (isInitialDataFetched) {
                    this.oldRooms.unshift(rooms[0]);
                    await this.getMessages(rooms[0]['id']);
                    this.currentRoom = rooms[0].id;
                    return;
                } else {
                    for (let room of rooms) {
                        await this.getMessages(room['id']);
                        this.oldRooms = rooms;
                    }
                }
                
                let url = new URL(window?.location?.href);
                let searchParams = new URLSearchParams(url?.search);
                const userId = searchParams?.get('userId');
                if (userId && userId != loggedUserProfile?.id) {
                    this.changeUser(viewUserProfile);
                } else {
                    let chats = document.querySelectorAll('.chat-item');
                    chats[0]?.click();
                }
                isInitialDataFetched = true;
            });

            this.roomSnapshot = subscribeToRoomSnapshot;
        },
        async handleScrollChat(event) {
            let element = event.target; 
        },
        async getMessages(id) {

            let q = query(
                collection(db, `room/${id}/message`),
                orderBy("createdAt", "desc")
            );

            let isInitialDataFetched = false;

            if (this.lastMsgInBatch &&  id in this.lastMsgInBatch && this.lastMsgInBatch[id].createdAt) {
                q = query(q, startAfter(this.lastMsgInBatch));
            }

            q = query(q, limit(10));

            let subscribeToChat = onSnapshot(q, {
                includeMetadata: true,
            },  (snapshot) => {
                let results = [];
                let prevCreatedAt = null;
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
                        console.log({prevCreatedAt, length, inited: this.isDataInited, isInitialDataFetched});

                        objectDoc['sender'] = Alpine.raw(this.roomUserIdMap)[objectDoc['senderId']];
                        objectDoc['createdAtDate'] = objectDoc['createdAt'].toDate();
                        if (length) {
                            if (objectDoc['createdAtDate']?.getDate() !== prevCreatedAt?.getDate() 
                                || objectDoc['createdAtDate'] ?.getMonth() !== prevCreatedAt?.getMonth()
                                || objectDoc['createdAtDate'] ?.getYear() !== prevCreatedAt?.getYear()
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

                if (length && !isInitialDataFetched) {
                    results[0]['isLastDateShow'] = true;
                    results[0]['lastDate'] = results[0]['createdAtDate'];
                }

                if (id in this.messages) {
                    this.messages[id] = this.messages[id].concat(results);
                } else {
                    this.messages[id] = results;
                } 
                
                if (this.currentRoom == id) {
                    loadMessages(results);
                    scrollIntoView();
                }
                
                if (length > 0) this.lastMsgInBatch[id] = results[length-1];
                isInitialDataFetched = true;
            }, (error)=>{
                console.log({error})
                toastError("Failed to fetch data");
            });

            this.chatSnapshots.push(subscribeToChat);
        },
        init() {
            this.initDB();

            this.$watch("currentRoom", async () => {
                if (this.currentRoom == "newRoom") {
                    return;
                }

                let currentRoomObject = Alpine.raw(this.oldRooms).filter((value)=>{
                    return value.id == this.currentRoom;
                });

                this.currentRoomObject = currentRoomObject[0];
                chatMessages.innerHTML = '';
                loadMessages(Alpine.raw(this.messages)[this.currentRoom]);
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


/*
    -> keep track of rooms' unsent messages
    -> keep track of online users
    1) if you send a message, you can't be unread. all read.
        check if the same for others. 
        1) check if online from locally fetched list. if not make unread other guy
        2) if other guy online:
            1) on the same room: unread = false; 
            2) in different rooms: no action


*/