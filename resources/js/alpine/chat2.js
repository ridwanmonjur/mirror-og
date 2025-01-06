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

const scrollIntoView = () => {
    const chatContainer = document.querySelector('.chat-messages');
    if (!chatContainer) return;
    
    const lastMessage = chatContainer.lastElementChild;
    if (lastMessage) {
        // maybe
        // { behavior: 'smooth', 'bottom': true }
        lastMessage.scrollIntoView({ behavior: 'smooth' });
    } 
}


const userStore = reactive({
    pagination: [],
    users: [],
    async fetchProspectiveChatters(event = null) {
        if (event == null && this.users[0]) {
            return;
        }

        let route;
        if (event?.target?.dataset?.url) {
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
        let { data: usersData, links: pagination } = users?.data;
        this.users = usersData;
        this.pagination = pagination;
    },
});

const chatStore = reactive({
    chatMessageList: [],
    chatSnapshots: null,
    currentRoomObj: null,
    messagesLength: 0,
   
     async getMessages(id) {
        let q = query(
            collection(db, `room/${id}/message`),
            orderBy("createdAt", "asc")
        );

        let isInitialDataFetched = false;
        let prevCreatedAt = null;
       
        q = query(q);

        let subscribeToChat = onSnapshot(q, {
            includeMetadata: true,
        }, async (snapshot) => {
            let length = 0;
            let results = [];

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
                        window.toastError("Some error occurred");
                    }

                    objectDoc['sender'] = roomStore.roomUserIdMap[objectDoc['senderId']];
                    let currentDate = objectDoc['createdAt'].toDate();
                    objectDoc['createdAtDate'] = currentDate;


                    if (length) {
                        if (currentDate?.getDate() != prevCreatedAt?.getDate()
                            || currentDate?.getMonth() != prevCreatedAt?.getMonth()
                            || currentDate?.getYear() != prevCreatedAt?.getYear()
                        ) {
                            objectDoc['isLastDateShow'] = true;
                            objectDoc['lastDate'] = prevCreatedAt;
                        }
                    }

                    prevCreatedAt = objectDoc['createdAtDate'];
                    length++;
                    results.push(objectDoc);
                }

            });

            if (isInitialDataFetched) {
                this.messagesLength = this.messagesLength ? this.messagesLength + length : length;
                let newArray = [ ...this.chatMessageList , ...results ];
                this.chatMessageList = newArray;
            } else {
                this.messagesLength = length;
                this.chatMessageList = [ ...results ];
    
            }
           
         
            isInitialDataFetched = true;
        }, (error) => {
            console.error({ error })
            toastError("Failed to fetch data");
        });
        this.chatSnapshots = subscribeToChat;
    },

    resetChatSnapshot() {
        if (this.chatSnapshots) {
            this.chatSnapshots();
        }
    }
});

// chatMessageList put in roomStore
const roomStore = reactive({
    roomUserIdMap: {},
    oldRooms: [],
    currentRoom: null,
    roomSnapshot: null,
    currentRoomObj: null,
    async initDB() {
        let roomUserIdMap = {};
        let currentUserId = loggedUserProfile?.id;
        let length = 0;
        let rooms = [], userIdList = [];
        const roomCollectionRef = collection(db, 'room');

        const roomQ = query(roomCollectionRef,
            or(
                where("user1", "==", String(currentUserId)),
                where("user2", "==", String(currentUserId))
            )
        );

        let subscribeToRoomSnapshot = onSnapshot(roomQ, async (rommSnapshot) => {
            
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

                    
                    rooms.push(data);

                    length++;
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


            // params 
            if (viewUserProfile && 'id' in viewUserProfile) {

                if (!(viewUserProfile.id in roomUserIdMap)) {
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

                    return;
                }

            } 

            let currentRoomIndex = (rooms && viewUserProfile) ? rooms.findIndex(room =>
                room.otherRoomMemberId == viewUserProfile.id
            ): 0;

            if (currentRoomIndex !=-1) {
                this.roomUserIdMap = { ...roomUserIdMap };
                this.oldRooms = [...rooms];
                this.currentRoomObj = rooms[currentRoomIndex];
                this.currentRoom = currentRoomIndex;
                if (rooms && rooms[currentRoomIndex]) {
                    chatStore.getMessages(rooms[currentRoomIndex].id);
                }
                setTimeout(() => { scrollIntoView(); }, 500);
            } 
        });

        this.roomSnapshot = subscribeToRoomSnapshot;
    },
    resetRoomSnapshot() {
        if (this.roomSnapshot) this.roomSnapshot();
    },
     async changeUser(user) {
        window.location.href = `/profile/message?userId=${user.id}`;
    },
    async setCurrentRoom(index) {
        chatStore.resetChatSnapshot();
        this.currentRoom = index;
        this.currentRoomObj = this.oldRooms[index];
        if (index != null) {
            // chatStore.chatSnapshots();
            await chatStore.getMessages(this.insideCurrentRoom(index).id);
        }

        setTimeout(() => { scrollIntoView(); }, 500);
    },
    insideCurrentRoom(index) {
        if (index != null) return this.oldRooms[index];
        else return null;
    },
    
});

function DateDividerComponent(props) {
    return {
        $template: `
            <div v-if="shouldShowDate()" class="d-flex justify-content-center my-3">
                <small 
                class="mx-auto px-3 py-1 rounded-pill"
                style="background-color: white; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);"
                v-text="formattedDate()"
                ></small>
            </div>
        `,

        formattedDate() {
            return humanReadableChatDateFormat(props.date)
        },

        shouldShowDate() {
            return props.index === 0 || props.isLastDateShow
        }
    }
}

function ChatListComponent() {
    // chatMessageList put in component
    return {
        get chatMessageList() {
            return chatStore.chatMessageList;
        },
        get currentRoomObj() {
            return roomStore.currentRoomObj;
        },
        get currentRoom() {
            return roomStore.currentRoom;
        },
        triggerReportSelection(event) {
         
            let button = event.currentTarget;
            const {userId, userName, userBanner} = button.dataset;
            window.dispatchEvent(new CustomEvent('report-selected', {
                detail: { id: userId, userName, userBanner }
            }));
        },
        async blockRequest(e) {
            const button = e.currentTarget;
            if (!button) return;

            const status = button.dataset.status;
            const route = button.dataset.route;
            const inputId = button.dataset.inputs ;
        
            try {
                let data = await makeRequest(route, 'POST', JSON.stringify({}));
     
                console.log({data});

                if (!('is_blocked' in data)) {
                    return;
                }
                     
            } catch (error) {
                // Handle errors (you might want to show a notification to the user)
                console.error('Operation failed:', error);
                window.toastError('Failed to process your request. Please try again later.');
            }
        },
  
        humanReadableChatTimeFormat(date) {
            const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
            return formattedTime
        },
        async sendMessage() {
            let value = chatInput?.value;

            if (this.currentRoom === null) {
                window.toastError("Choose a conversation first!");
                return;
            }

            if (String(value).trim() == "") {
                window.toastError("Empty messages!")
                return;
            }

            try {
                if (this.currentRoom == null) {
                    window.toastError("New chat still being updated...")
                }

                if (this.currentRoomObj?.otherRoomMember?.i_blocked_them) {
                    window.toastError('You have blocked this user and can\'t send message this room.');
                    chatInput.value = "";
                    return;
                } 
                
                if (this.currentRoomObj?.otherRoomMember?.they_blocked_me) {
                    window.toastError('This user has blocked you and you can\'t send message this room.');
                    chatInput.value = "";
                    return;
                }

                await addDoc(collection(db, `room/${roomStore.currentRoomObj.id}/message`), {
                    senderId: loggedUserProfile.id,
                    text: value,
                    createdAt: new Date(),
                });

               scrollIntoView();

            } catch (err) {
                console.error(err);
            }

            chatInput.value = "";
        },
    }
}


function OtherUsersComponent() {
    return {
        async fetchProspectiveChatters(event) {
            await userStore.fetchProspectiveChatters(event);
        },
        async changeUser(user) {
            await roomStore.changeUser(user);
        },
        get users() {
            return userStore.users;
        },

        get pagination() {
            return userStore.pagination;
        }
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
        async fetchProspectiveChatters(event) {
            await userStore.fetchProspectiveChatters(event);
        },
        formatDate(date) {
            if (!date) return "Not active recently"
            let newDate = DateTime
                .fromFormat(date, "yyyy-MM-dd HH:mm:ss")
                .toRelative();


            return newDate;
        },
        async mounted() {
            await roomStore.initDB();
          
        }
    }
}


function ReportBlockComponent () {
    return {
        willShowReports: true,
        reports: [],
        errors: {},
        loading: false,
        user: null,
        reasons: [
            'Inappropriate Content',
            'Harassment',
            'Fake Account',
            'Hate Speech',
            'Other'
        ],
        formData: {
            reason: '',
            description: ''
        },
        errors: {},
        loading: false,

        async fetchReports() {
            try {
              const response = await fetch('/api/user/' + this.user.id + '/reports');
              if (!response.ok) throw new Error('Failed to fetch reports');
              let { reports } = await response.json();
              this.reports = reports;
            } catch (error) {
              console.error('Error:', error);
              this.reports = [];
            }
        },

        async submitReport(event) {
            event.preventDefault();
            this.loading = true;
            this.errors = {};

            try {
                const response = await fetch(`/api/user/${this.user.id}/report`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                        return;
                    }
                    throw new Error(data.message || 'An error occurred');
                }

                // Success
                this.reset();
                this.fetchReports();
                window.Toast.fire({
                    'icon': 'success',
                    'text': 'Report submitted successfully'
                });
            } catch (error) {
                console.error('Error submitting report:', error);
                window.toastError('Failed to submit report. Please try again.');
            } finally {
                this.loading = false;
            }
        },

        toggleWillShowReports() {
            console.log("zzzzz");
            console.log("zzzzz");
            console.log("zzzzz");
            this.willShowReports = !this.willShowReports;
        },

        reset() {
            this.showForm = false;
            this.formData = {
                reason: '',
                description: ''
            };
            this.errors = {};
        },

        formatDate(date) {
            return  DateTime
                .fromISO(date)
                .toRelative();
        },

        mounted() {
            console.log("zzzz");
            console.log("zzzz");
            console.log("zzzz");
            window.addEventListener('report-selected', async (event) => {
                console.log("zzzz");
                console.log("zzzz");
                console.log("tttt");
                console.log("tttt");
                let element = document.getElementById('reportUserModal')
                let modal = new window.bootstrap.Modal(element);
                modal.show();
                this.user = event.detail;
                await this.fetchReports();
                console.log({user: this.user});
                console.log({user: this.user});
                console.log({user: this.user});
                console.log({user: this.user});
            });
        }
    }
}


async function makeRequest(url, method, data) {
    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error making request:', error);
        throw error;
    }
}

  

document.addEventListener('DOMContentLoaded', () => {
    createApp({
        RoomComponent,
        ChatListComponent,
        OtherUsersComponent,
        DateDividerComponent,
        ReportBlockComponent
    }).mount('#app');
});


document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        document.getElementById('sendMessageBtn').click();
    }
});
