<html>

<head>
    @include('googletagmanager::head')
    <link rel="stylesheet" href="{{ asset('/assets/css/chat/fullpage.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/chat2.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body >
    @include('googletagmanager::body')
    <div id="app"  class="app-container row"
        >
        <input type="hidden" id="fetchFirebaseUsersInput" value="{{ route('user.firebase.readAll') }}">
        <input type="hidden" id="viewUserProfile" value="{{json_encode($userProfile?->only(['id', 'name', 'mobile_no']))}}">
        <input type="hidden" id="loggedUserProfile" value="{{json_encode($user)}}">
        <div class="sidebar col-12 col-lg-5 col-xl-4 m-0 p-0" >
            <div class="sidebar-header">
                <h2 id="initDB" class="my-0">Chat List</h2>
                {{-- TODO --}}
                <button v-on:click="fetchProspectiveChatters(null);" class="add-chat" data-bs-toggle="modal" data-bs-target="#other-users-component">
                {{-- Add chat icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                </button>
            </div>
            <div class="chat-list" @vue:mounted="mounted" id="room-component" v-scope="RoomComponent()">
                    <div v-for="room in oldRooms" :key="room.id" v-bind:data-identity-for-read="room.id" v-on:click="currentRoom = room?.id" 
                        v-bind:class="{'chat-item': true, 'bg-primary' : currentRoom == room?.id }"
                    >
                            <img v-if="room?.otherRoomMember?.userBanner != null" {!! trustedBladeHandleImageFailure() !!} v-bind:src="'/storage/' + room?.otherRoomMember?.userBanner" width="50" height="50"
                                class="object-fit-cover rounded-circle me-3">
                            <div v-else class="avatar me-3"
                                v-text="room.otherRoomMember?.name ? room.otherRoomMember?.name?.charAt(0)?.toUpperCase(): room?.otherRoomMember?.email[0]?.toUpperCase()">
                            </div>
                        <div class="chat-info">
                            <h3 v-text="room?.otherRoomMember?.name"></h3>
                            <p class="status my-0">
                                <span v-text="formatDate(room.otherRoomMember?.updated_at)"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill mt-1 ms-2 d-none" viewBox="0 0 16 16">
                                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                                </svg>
                            </p>
                        </div>
                       
                    </div>
                    
                 
            </div>
        </div>
       
    </div>
</body>
<script src="{{ asset('/assets/js/shared/BackgroundModal.js') }}"></script>

<script src="{{ asset('/assets/js/chat.js') }}"></script>
</html>
