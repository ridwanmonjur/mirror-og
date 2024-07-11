<html>

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/chat/fullpage.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/chat.js'])
</head>

<body>
    <div x-data="alpineDataComponent" class="app-container row"
        @fetchstart.window="console.log('Received event:', $event);initDB(event)">
        <input type="hidden" id="fetchFirebaseUsersInput" value="{{ route('user.firebase.readAll') }}">
        <input type="hidden" id="viewUserProfile" value='@json($userProfile?->only(['id', 'name', 'mobile_no']))'>
        <input type="hidden" id="loggedUserProfile" value='@json($user)'>
        <div class="sidebar col-12 col-lg-5 col-xl-4 m-0 p-0">
            <div class="sidebar-header">
                <h2 id="initDB">Chat List</h2>
                <button class="add-chat"><i class="fas fa-plus"></i></button>
            </div>
            <div class="chat-list">
                <template x-for="room in oldRooms" :key="room.id">
                    <div  x-on:click="currentRoom = room.id" class="chat-item">
                        <template x-if="room.otherRoomMember.userBanner != null">
                            <img x-bind:src="'/storage/' + room.otherRoomMember.userBanner" width="50" height="50"
                                class="object-fit-cover rounded-circle">
                        </template>
                        <template x-if="room.otherRoomMember.userBanner == null">
                            <div class="avatar"
                                x-text="room.otherRoomMember.name ? room.otherRoomMember.name[0]?.toUpperCase(): room.otherRoomMember.email[0]?.toUpperCase()">
                            </div>
                        </template>
                        <div class="chat-info ms-2">
                            <h3 x-text="room.otherRoomMember.name"></h3>
                            <p class="status my-0" x-text="room.otherRoomMember.name"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <div class="chat-container col-0 d-none d-lg-flex col-lg-7 col-xl-8 m-0 p-0">
            <div class="chat-header w-100">
                <h2 class="chat-user-name my-0">Alex</h2>
                <button class="menu-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                        class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                        <path
                            d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                    </svg>
                </button>
            </div>
            <div class="chat-messages" id="chat-messages">
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Type a message...">
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                        class="bi bi-telegram" viewBox="0 0 16 16">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('/assets/js/chat/chat.js') }}"></script>

</html>
