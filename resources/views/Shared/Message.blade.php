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
        <div  class="sidebar col-12 col-lg-5 col-xl-4 m-0 p-0" @vue:mounted="mounted" id="room-component" v-scope="RoomComponent()">
            <div class="sidebar-header" >
                <h2 id="initDB" class="my-0">Chat List</h2>
                {{-- TODO --}}
                <button v-on:click="fetchProspectiveChatters(null);" class="add-chat" data-bs-toggle="modal" data-bs-target="#other-users-component">
                {{-- Add chat icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                </button>
            </div>
            <div  class="room-list">
                <div v-for="(room, key) in oldRooms" :key="room.id" v-bind:data-identity-for-read="room.id" v-on:click="setCurrentRoom(key)" 
                    v-bind:class="{'chat-item': true, 'bg-primary' : currentRoomObj?.id == room?.id }"
                >
                        <img v-if="room?.otherRoomMember?.userBanner != null" {!! trustedBladeHandleImageFailure() !!} v-bind:src="'/storage/' + room?.otherRoomMember?.userBanner" width="50" height="50"
                            class="object-fit-cover rounded-circle me-3">
                        <div v-else class="avatar me-3"
                            v-text="room?.otherRoomMember?.name ? room.otherRoomMember.name?.charAt(0)?.toUpperCase(): room?.otherRoomMember?.email[0]?.toUpperCase()">
                        </div>
                    <div class="chat-info">
                        <h3 v-text="room?.otherRoomMember?.name"></h3>
                        <p class="status my-0">
                            <span v-text="formatDate(room?.otherRoomMember?.updated_at)"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bell-fill mt-1 ms-2 d-none" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901"/>
                            </svg>
                        </p>
                    </div>
                    
                </div>
            </div>
        </div>
       <div id="chat-component" v-scope="ChatListComponent()" class="chat-container position-relative col-12 d-flex col-lg-7 col-xl-8 m-0 p-0" style="overflow: hidden;">
            <div class="chat-header w-100">
                <h2 class="chat-user-name my-0">
                    <span v-show="currentRoomObj?.otherRoomMember?.name != null">
                        <img v-if="currentRoomObj?.otherRoomMember?.userBanner != null" {!! trustedBladeHandleImageFailure() !!} v-bind:src="'/storage/' + currentRoomObj?.otherRoomMember?.userBanner" width="50" height="50"
                                    class="object-fit-cover rounded-circle me-3">
                        <span v-else class="avatar d-inline-flex justify-content-center align-items-center rounded-circle me-3" style="width: 50px; height: 50px;"
                            v-text="currentRoomObj?.otherRoomMember?.name ? currentRoomObj.otherRoomMember?.name?.charAt(0)?.toUpperCase(): '-'">
                        </span>
                    </span>
                    <span 
                        v-text="currentRoomObj?.otherRoomMember?.name ?? 'Start a chat'"
                    ></span>
                </h2>
                <button class="menu-btn dropdown">
                {{-- Settions icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" fill="currentColor"
                        class="bi bi-three-dots-vertical" viewBox="0 0 16 16"
                        data-bs-toggle="dropdown" aria-expanded="false"
                    >
                        <path
                            d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                    </svg>
                    <ul class="dropdown-menu py-0">
                        <li><a class="dropdown-item py-2" href="#">Block</a></li>
                        <li><a class="dropdown-item py-2" href="#">Report</a></li>
                    </ul>
                </button>
               
            </div>
            <div class="chat-messages" id="chat-messages" style="overflow-y: auto;">
                <template :key="message.id" v-for="(message, index) in chatMessageList" >
                    <div v-scope="DateDividerComponent({ 
                        date: message['createdAtDate'],
                        index: index,
                        shouldShowDate: message['isLastDateShow'] 
                    })">
                    </div>
                    <div :class="message.className">
                    <img 
                        v-if="message.sender?.userBanner"
                        :src="`/storage/${message.sender.userBanner}`"
                        v-on:error="$el.src = '/assets/images/404.png'"
                        height="50"
                        width="50"
                        class="object-fit-cover rounded-circle me-2"
                    >
                
                    <div 
                        v-else
                        class="avatar me-2"
                        v-text="message.sender.name ? 
                            message.sender.name.charAt(0).toUpperCase() : 
                            message.sender.email[0].toUpperCase()"
                        >
                    </div>

                    <div class="message-content w-75">
                        <span v-text="message.text"></span>
                        <span class="timestamp" v-text="humanReadableChatTimeFormat(message.createdAtDate)"></span>
                    </div>
                    </div>
                </template>
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Type a message...">
                <button id="sendMessageBtn" v-on:click="sendMessage">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                        class="bi bi-telegram" viewBox="0 0 16 16">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09" />
                    </svg>
                </button>
            </div>
        </div>
        <div v-scope="OtherUsersComponent()" class="modal fade" id="other-users-component" tabindex="-1" aria-labelledby="other-users-componentLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Start a new chat</h5>
                    </div>
                    <div class="modal-body py-4 pv-3">
                        <div class="row">
                            <div class="col-12 col-lg-9">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <input @input.debounce.500m="fetchProspectiveChatters($event)" type="text" class="form-control cursor-pointer" placeholder="Search...">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="tab-size"> 
                            <table class="table responsive ">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">First</th>
                                        <th scope="col">Last</th>
                                        <th scope="col">Message</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <tr v-for="chat in users?? []" :key="chat.name + chat.id" class="border-none" style="vertical-align: center !important;">
                                            <th scope="row"></th>
                                            <td>
                                                <img class="object-fit-cover rounded-circle" height="40" width="40" {!!trustedBladeHandleImageFailure()!!} v-bind:src="'/storage/' + chat?.userBanner" >
                                                <span class="ms-3" v-text="chat?.name"> </span>
                                            </td>
                                            <td class="pt-3 pb-2" v-text="chat?.role.toLowerCase()"></td>
                                            <td class="text-center pt-3 pb-2 cursor-pointer"  data-bs-dismiss="modal" v-on:click="changeUser(chat)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="blue" class="bi bi-send-fill cursor-pointer" viewBox="0 0 16 16">
                                                <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
                                                </svg>
                                            </td>
                                        </tr>
                                </tbody>
                            </table>
                            <ul class="pagination cursor-pointer py-3">
                                    <li v-for="link in pagination ?? []" :key="link.label" v-on:click="if (link.url) { fetchProspectiveChatters(event); }" v-bind:data-url="link.url" :class="{'page-item': true, 'active': link.active, 'disabled': link.url}" > 
                                        <a 
                                            onclick="event.preventDefault()"
                                            :class="{
                                                'page-link' : true,
                                                'text-light': link.active,
                                            }" v-html="link.label"
                                        > 
                                        </a>
                                    </li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" data-bs-dismiss="modal" class="rounded-pill btn btn-primary text-light">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('/assets/js/shared/BackgroundModal.js') }}"></script>
{{-- <script src="{{ asset('/assets/js/shared/chat.js') }}"></script> --}}

</html>
