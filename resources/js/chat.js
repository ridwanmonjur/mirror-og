import { initializeApp } from "firebase/app";
import { getFirestore, addDoc, onSnapshot, limit, orderBy, doc, query, collection, collectionGroup, getDocs, getDoc, where, or } from "firebase/firestore";
import { initializeAppCheck, ReCaptchaEnterpriseProvider } from "firebase/app-check";
import dayjs from 'dayjs-ext'
import relativeTime from 'dayjs-ext/plugin/relativeTime'
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

const db = getFirestore(app);

console.log({ firebaseConfig, app, db })

dayjs.extend(relativeTime).locale('sg')

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
    const year = date.getFullYear();
    const month = monthNames[date.getMonth()];
    const day = date.getDate().toString().padStart(2, '0');
    
    const formattedDate = `${day} ${month} ${year}`;    
    return formattedDate;
}

function loadMessages(messages) {
    chatMessages.innerHTML = '';
    console.log({messagesxx: messages})
    messages.forEach(message => {
        if (message['newDate']) {
            addDate(message['createdAtDate']);
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
    console.log({messageY: message})
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
            sender.name[0]?.toUpperCase(): sender.email[0]?.toUpperCase();
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
        currentMessage: [],
        newRoom: null,
        oldRooms: [],
        messages: {},
        lastDocInBatch: {},
        chats: [],
        oldRoomUsers: [],
        userIdMap: {},
        async sendMessage() {
            let value = chatInput?.value;
            
            if (!this.currentRoom || String(value).trim() == "") {
                return;
            }
         
            const docRef = await addDoc(collection(db,  `room/${this.currentRoom}/message`), {
                senderId: loggedUserProfile.id,
                text: value,
                createdAt: new Date()
            });

            console.log({docRef})

            chatInput.value = "";
        },
        async initDB() {
            if (this.dataInited) return;
            let userIdMap = {};
            let currentUserId = loggedUserProfile?.id;
            let rooms = [], userIdList = [];
            const roomCollectionRef = collection(db, 'room');

            const roomQ = query(roomCollectionRef,
                or(
                    where("user1", "==", String(currentUserId)),
                    where("user2", "==", String(currentUserId))
                )
            );

            onSnapshot(roomQ, async (rommSnapshot) => {
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

                        if (this.isDataInited) {
                            room['otherRoomMember'] = this.userIdMap[room['otherRoomMemberId']];
                            await this.getMessages(data['id']);
                        }
                        console.log({ data, inited: this.isDataInited })

                        rooms.push(data);
                    }
                    if (change.type === "modified") {
                        console.log("Modified city: ", change.doc.data());
                    }
                });

                if (this.isDataInited) {
                    return;
                }

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
                    userIdMap[user.id] = user;
                }
                userIdMap[loggedUserProfile.id] = loggedUserProfile;

                for (let room of rooms) {
                    room['otherRoomMember'] = userIdMap[room['otherRoomMemberId']];
                }

                console.log({ rooms })

                this.oldRooms = rooms;
                this.userIdMap = userIdMap;
                this.isDataInited = true;
                for (let room of rooms) {
                    await this.getMessages(room['id']);
                }

                let currentMessageX = Alpine.raw(this.messages)[this.currentRoom];
                let isArray = Array.isArray(currentMessageX);
                console.log({currentMessageX, isArray})
                this.currentMessage= currentMessageX;
                console.log({
                    room: Alpine.raw(this.oldRooms),
                    currentRoom: Alpine.raw(this.currentRoom),
                    currentMessage: Alpine.raw(this.currentMessage),
                    messages: Alpine.raw(this.messages)
                })

            });

            // await this.getMessages();
        },
        async getMessages(id=null) {

            id??= this.currentRoom;
            let q = query(
                collection(db, `room/${id}/message`),
                orderBy("createdAt")
            );

            if (this.lastDocInBatch?.createdAt) {
                q = query(q, startAfter(this.lastDocInBatch.createdAt));
            }

            q = query(q, limit(25));

            let results = [];
            onSnapshot(collection(db, `room/${id}/message`), (snapshot) => {
                let prevCreatedAt = null;
                snapshot.docChanges().forEach(async (querySnapshot) => {
                    console.log("sth real happened");
                    console.log("sth real happened");
                    console.log("sth real happened");
                    console.log("sth real happened");
                    console.log("sth real happened");
                    console.log("sth real happened");
                    console.log("sth real happened");
                    let objectDoc = {
                        id: querySnapshot.doc.id,
                        ...querySnapshot.doc.data(),
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
                    console.log({ objectDoc, sender: Alpine.raw(this.userIdMap) });
                    objectDoc['sender'] = Alpine.raw(this.userIdMap)[objectDoc['senderId']];
                    objectDoc['createdAtDate'] = objectDoc['createdAt'].toDate();
                    if (objectDoc['createdAtDate']?.getDate() !== prevCreatedAt?.getDate() || objectDoc['createdAtDate'] ?.getMonth() !== prevCreatedAt?.getMonth()) {
                        objectDoc['newDate'] = true;
                    }

                    prevCreatedAt = objectDoc['createdAtDate'];

                    results.push(objectDoc);
                });
            });


            if (this.messages.hasOwnProperty(id)) {
                this.messages[id].concat(results);
            } else {
                this.messages[id] = results;
            }            
            
            this.lastDocInBatch[id] = results[5];
        },
        init() {
            this.$watch("currentRoom", ()=>{
                let currentRoomObject = Alpine.raw(this.oldRooms).filter((value)=>{
                    return value.id == this.currentRoom;
                });

                console.log({oldRooms: this.oldRooms, currentRoomObject});
                this.currentRoomObject = currentRoomObject[0];

                console.log({currentRoomzzz: Alpine.raw(this.messages)[this.currentRoom]})
                loadMessages(Alpine.raw(this.messages)[this.currentRoom]) ; 
            })
        }
    }
});


Alpine.start();
