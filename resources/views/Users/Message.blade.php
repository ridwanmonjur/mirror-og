<html>

<head>
    @include('includes.HeadIcon')
    @include('googletagmanager::head')
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/chat.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/common/fullpage.css') }}">
</head>

<body>
    @include('includes.__Navbar.NavbarGoToSearchPage')
    @include('googletagmanager::body')
    <main>
    <div id="app" class="app-container row">
        <input type="hidden" id="fetchFirebaseUsersInput" value="{{ route('user.firebase.readAll') }}">
        <input type="hidden" id="viewUserProfile"
            value="{{ json_encode($userProfile?->only(['id', 'name', 'mobile_no'])) }}">
        <input type="hidden" id="loggedUserProfile" value="{{ json_encode($user) }}">
        <div class="sidebar col-12  col-lg-4 m-0 p-0" @vue:mounted="mounted" id="room-component"
            v-scope="RoomComponent()" v-cloak>
            <div class="sidebar-header align-middle">
                <h5 id="initDB" class="my-0">Chat List</h5>
                {{-- TODO --}}
                <button v-on:click="fetchProspectiveChatters(null);" class="add-chat" data-bs-toggle="modal"
                    data-bs-target="#other-users-component">
                    {{-- Add chat icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor"
                        class="bi bi-plus" viewBox="0 0 16 16">
                        <path
                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                    </svg>
                </button>
            </div>
            <div v-cloak class="room-list user-select-none custom-scrollbar">
                <div v-for="(room, key) in oldRooms" v-bind:key="room.id" v-bind:data-identity-for-read="room.id"
                    v-on:click="setCurrentRoom(key)"
                    v-bind:class="{'chat-item': true, 'bg-primary' : currentRoomObj?.id == room?.id }">
                    <img v-if="room?.otherRoomMember?.userBanner != null" {!! trustedBladeHandleImageFailure() !!}
                        v-bind:src="'/storage/' + room?.otherRoomMember?.userBanner" width="50" height="50"
                        class="object-fit-cover rounded-circle me-3">
                    <div v-else class="avatar me-3"
                        v-text="room?.otherRoomMember?.name ? room.otherRoomMember.name?.charAt(0)?.toUpperCase(): room?.otherRoomMember?.email[0]?.toUpperCase()">
                    </div>
                    <div class="chat-info w-75">
                        <div v-if="room?.otherRoomMember && room?.otherRoomMember?.name">
                            <h3 class="d-inline user-select-none" v-text="room?.otherRoomMember?.name"></h3>
                            <small v-bind:class="{'text-white fw-bold' : currentRoomObj?.id == room?.id }"
                                class="text-red" v-if="room?.i_blocked">
                                <svg class="me-1 ms-2" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10">
                                    </circle>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                </svg>
                                Blocked
                            </small>
                        </div>
                        <p class="status my-0 user-select-none">
                            <span v-bind:class="{'text-white fw-bold' : currentRoomObj?.id == room?.id }"
                                v-text="formatDate(room?.otherRoomMember?.updated_at)"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-bell-fill mt-1 ms-2 d-none" viewBox="0 0 16 16">
                                <path
                                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2m.995-14.901a1 1 0 1 0-1.99 0A5 5 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901" />
                            </svg>
                            
                        </p>

                    </div>

                </div>
            </div>
        </div>
        <div v-cloak id="chat-component" v-scope="ChatListComponent()"
            class="chat-container position-relative col-12 d-flex  col-lg-8 m-0 p-0" style="overflow: hidden;">
            <div class="chat-header 75">
                <h2 class="chat-user-name py-0 my-0">
                    <span v-show="currentRoomObj?.otherRoomMember?.name != null">
                        <img v-if="currentRoomObj?.otherRoomMember?.userBanner != null" {!! trustedBladeHandleImageFailure() !!}
                            v-bind:src="'/storage/' + currentRoomObj?.otherRoomMember?.userBanner" width="40"
                            height="40" class="object-fit-cover rounded-circle me-3">
                        <span v-else
                            class="avatar d-inline-flex justify-content-center align-items-center rounded-circle me-3"
                            style="width: 40px; height: 40px;"
                            v-text="currentRoomObj?.otherRoomMember?.name ? currentRoomObj.otherRoomMember?.name?.charAt(0)?.toUpperCase(): '-'">
                        </span>
                    </span>
                    <span v-text="currentRoomObj?.otherRoomMember?.name ?? 'Start a chat'"></span>
                </h2>
                <button class="menu-btn dropdown">
                    {{-- Settions icon --}}
                    <button class="btn menu-btn text-white" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-three-dots-vertical" viewBox="0 0 16 16" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <path
                                d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                        </svg>
                    </button>
                    <ul class="dropdown-menu py-0  ">
                        <li v-bind:data-route="'/api/user/' + currentRoomObj?.otherRoomMember?.id + '/block'"
                            v-on:click="blockRequest(event)"
                            v-bind:data-status="currentRoomObj?.i_blocked"
                            v-bind:data-inputs="currentRoomObj?.otherRoomMember?.id"><a class="dropdown-item py-2"
                                href="javascript:void(0)" role="button">
                                <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10">
                                    </circle>
                                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                </svg>
                                <span v-text="currentRoomObj?.i_blocked ? 'Unblock Chat': 'Block Chat'">  </span>
                            </a></li>
                        <li v-on:click="triggerReportSelection(event);"
                            v-bind:data-user-id="currentRoomObj?.otherRoomMember?.id"
                            v-bind:data-user-name="currentRoomObj?.otherRoomMember?.name"
                            v-bind:data-user-banner="currentRoomObj?.otherRoomMember?.userBanner"><a
                                class="dropdown-item text-red py-2" href="#" role="button">
                                <svg class=" me-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z">
                                    </path>
                                    <line x1="4" y1="22" x2="4" y2="15"></line>
                                </svg>
                                <span class="text-red">Report User </span>
                            </a></li>
                    </ul>
                </button>

            </div>
            <div class="chat-messages" id="chat-messages" style="overflow-y: auto;">
                <template :key="message.id" v-for="(message, index) in chatMessageList">
                    <div
                        v-scope="DateDividerComponent({ 
                        date: message['createdAtDate'],
                        index: index,
                        shouldShowDate: message['isLastDateShow'] 
                    })">
                    </div>
                    <div :class="message.className">
                        <img v-if="message.sender?.userBanner" v-bind:src="`/storage/${message.sender.userBanner}`"
                            v-on:error="$el.src = '/assets/images/404q.png'" height="40" width="40"
                            class="object-fit-cover rounded-circle me-2">

                        <div v-else class="avatar me-2"
                            v-text="message.sender.name ? 
                            message.sender.name.charAt(0).toUpperCase() : 
                            message.sender.email[0].toUpperCase()">
                        </div>

                        <div class="message-content w-75">
                            <span style="white-space: pre-wrap !important;" v-text="message.text"></span>
                            <span class="timestamp"
                                v-text="humanReadableChatTimeFormat(message.createdAtDate)"></span>
                        </div>
                    </div>
                </template>
            </div>
            <div class="chat-input">
                <textarea placeholder="Enter your message here..."></textarea>
                <button id="sendMessageBtn" v-on:click="sendMessage">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                        class="bi bi-telegram" viewBox="0 0 16 16">
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.287 5.906q-1.168.486-4.666 2.01-.567.225-.595.442c-.03.243.275.339.69.47l.175.055c.408.133.958.288 1.243.294q.39.01.868-.32 3.269-2.206 3.374-2.23c.05-.012.12-.026.166.016s.042.12.037.141c-.03.129-1.227 1.241-1.846 1.817-.193.18-.33.307-.358.336a8 8 0 0 1-.188.186c-.38.366-.664.64.015 1.088.327.216.589.393.85.571.284.194.568.387.936.629q.14.092.27.187c.331.236.63.448.997.414.214-.02.435-.22.547-.82.265-1.417.786-4.486.906-5.751a1.4 1.4 0 0 0-.013-.315.34.34 0 0 0-.114-.217.53.53 0 0 0-.31-.093c-.3.005-.763.166-2.984 1.09" />
                    </svg>
                </button>
            </div>
        </div>
        <div v-scope="OtherUsersComponent()" class="modal fade" id="other-users-component" tabindex="-1"
            aria-labelledby="other-users-componentLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content  px-4 py-2">
                    <div class="px-4 pt-4   border-0">
                        <h5 class="modal-title py-0 my-0" id="staticBackdropLabel">Start a new chat</h5>
                    </div>
                    <div class="modal-body pt-2 px-3 pb-2 container-fluid">
                        <div class="row">
                            <div class="col-11 ">
                                <div class="input-group mx-3 my-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                                <path
                                                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                            </svg>
                                        </span>
                                    </div>
                                    <input @input.debounce.500m="fetchProspectiveChatters($event)" type="text"
                                        class="form-control cursor-pointer px-3" placeholder="Search...">
                                </div>
                            </div>
                        </div>
                       <div class="container-fluid mt-2 px-3">
                                <div class="w-100 g-3 mt-3">
                                    <div v-for="chat in users ?? []" :key="chat.name + chat.id" >
                                        <div class="card mb-2 rounded-lg shadow-sm" 
                                            onmouseover="this.style.transform='translateY(-2px)'" 
                                            onmouseout="this.style.transform='translateY(0)'"
                                            style="transition: transform 0.2s ease"
                                            v-on:click="changeUser(chat)"
                                            data-bs-dismiss="modal"
                                            role="button">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-2 col-xl-1">
                                                        <img class="rounded-circle border border-secondary"
                                                        onerror="this.src='/assets/images/404q.png';"
                                                            height="40" width="40"
                                                            {!! trustedBladeHandleImageFailure() !!}
                                                            v-bind:src="'/storage/' + chat?.userBanner">
                                                    </div>
                                                    <div class="col-9 col-xl-10 col-xl-">
                                                        <span class="text-wrap d-inline-block align-middle me-2 "  v-text="chat?.name"></span>
                                                        <small class="text-muted d-inline-block align-middle" v-text="chat?.role.toLowerCase()"></small>
                                                    </div>
                                                    <div class="col-1 text-end gear-icon-btn">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" class="bi bi-chevron-right text-secondary"
                                                            viewBox="0 0 16 16">
                                                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="pagination cursor-pointer pt-2">
                                <li v-for="link in pagination ?? []" :key="link.label"
                                    v-on:click="if (link.url) { fetchProspectiveChatters(event); }"
                                    v-bind:data-url="link.url"
                                    :class="{ 'page-item': true, 'active': link.active, 'disabled': link.url }">
                                    <a onclick="event.preventDefault()"
                                        :class="{
                                            'page-link': true,
                                            'text-light': link.active,
                                        }"
                                        v-html="link.label">
                                    </a>
                                </li>
                            </ul>
                        </div>
                          <button type="button" data-bs-dismiss="modal"
                            class="rounded-pill btn d-inline-block mb-2 mx-auto px-4 btn-primary text-light ">Close</button>
                </div>
                    </div>
                   
                      
            </div>
      

        <div v-scope="ReportBlockComponent()" id="reportUserModal" @vue:mounted="init()"  class="modal" style="font-size: 0.9rem;">
            <div id="reportUserModal" tabindex="-1">
                <div class="modal-dialog  mb-0">
                    <div class="modal-content  pt-3 pb-0 px-3">
                        <div class="modal-body px-3 mx-3 pt-0 mt-0">
                            <div v-on:click="toggleWillShowReports" class="mt-2 text-red ms-0 cursor-pointer"
                                v-if="!willShowReports">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                                </svg> Back to reports
                            </div>

                            <div v-else>
                                <h5 class="mt-4 mb-0 d-block pt-0 pb-3 text-primary text-start">
                                    <img v-bind:src="'/storage/' + user?.userBanner"
                                        class="rounded-circle object-fit-cover border border-primary me-2"
                                        width="35" height="35" onerror="this.src='/assets/images/404q.png';">
                                    <span>Past Reports against </span>
                                    <span v-text="user?.userName"> </span>
                                </h5>

                                <div class="d-flex justify-content-between mb-2">
                                    <button class="btn btn-primary text-light px-3 mb-2 rounded-pill"
                                        v-on:click="toggleWillShowReports">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 16 16" class="bi bi-plus-lg me-1">
                                            <path
                                                d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1" />
                                        </svg>
                                        Make a new report
                                    </button>
                                    <button class="me-2 btn ps-0 text-dark rounded-pill "
                                        data-bs-target="#connectionModal" data-bs-dismiss="modal"
                                        data-bs-toggle="modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-chevron-left d-inline"
                                            viewBox="0 0 16 16">
                                            <path fill-rule="evenodd"
                                                d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                                        </svg>
                                        <span> Go Back </span>
                                    </button>

                                </div>
                                <div v-if="!reports?.[0]">
                                    <div class="mt-3 p-5 mb-5">
                                        No reports available.
                                    </div>
                                </div>
                                <template v-for="(report, key) in reports" :key="report.id">
                                    <div class="card mb-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0" v-text="'Report #' + (Number(key)+1)"></h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <small class="text-muted">Reported by</small>
                                                        <p class="mb-0 fw-medium">
                                                            <img src="{{ asset('storage/' . ($user?->userBanner ?? '')) }}"
                                                                class="rounded-circle object-fit-cover border border-secondary me-1"
                                                                width="25" height="25"
                                                                onerror="this.src='{{ asset('assets/images/404q.png') }}'">
                                                            <span>{{ $user?->name }} </span>
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted">Date</small>
                                                        <p class="mb-0" v-text="formatDate(report.created_at)"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-0">
                                                <small class="text-muted">Reason</small>
                                                <p class="mb-2 fw-medium" v-text="report.reason"></p>

                                                <small class="text-muted">Description</small>
                                                <p class="mb-0" v-text="report.description"></p>
                                            </div>

                                            {{-- <div class="mt-3 border-top pt-3">
                                            <small class="text-muted d-block">Admin Notes</small>
                                            <p 
                                                class="text-muted fst-italic mb-0" 
                                                v-text="report.admin_notes || 'No admin notes available'"
                                            ></p>
                                        </div> --}}
                                        </div>
                                        {{-- <div class="card-footer text-muted">
                                        <small v-text="'Last updated: ' + formatDate(report.updated_at)"></small>
                                    </div> --}}
                                    </div>
                                </template>
                            </div>

                            <div v-show="!willShowReports">
                                <div class="border-0  mt-2 mb-0">
                                    <h5 class="d-inline-block py-2 text-start text-primary">
                                        Writing a report</h5>
                                </div>

                                <h5 class="my-0 d-inline-block pt-0 pb-3 text-primary text-start">
                                    <img v-bind:src="'/storage/' + user?.userBanner"
                                        class="rounded-circle object-fit-cover border border-primary me-2"
                                        width="35" height="35" onerror="this.src='/assets/images/404q.png';">

                                    <span v-text="user?.userName"> </span>
                                </h5>
                                <p style="color: gray;"><i> We are so sorry for your experience. Please let us know
                                        what we
                                        can do to help.</i> </p>

                                <form v-on:submit.prevent="submitReport(event)">
                                    <div class="mb-3">
                                        <p class="form-label  d-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="inherit" class="bi bi-exclamation-triangle ms-1 me-2"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z" />
                                                <path
                                                    d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                            </svg>
                                            Please mention your reason for reporting this user.
                                        </p>
                                        <div v-for="reason in reasons" :key="reason">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input "
                                                    v-bind:id="'reason_' + reason" style="color: gray !important;"
                                                    name="reason" v-bind:value="reason" v-model="formData.reason"
                                                    v-bind:class="{ 'is-invalid': errors.reason }" required>
                                                <label class="form-check-label" v-bind:for="'reason_' + reason"
                                                    v-text="reason"></label>
                                            </div>
                                        </div>

                                        <!-- Other Reason Input (shows only when 'Other' is selected) -->
                                        <div v-show="formData.reason === 'Other'" class="mt-2">
                                            <input type="text" class="form-control"
                                                placeholder="Please specify the reason" v-model="formData.otherReason"
                                                :required="formData.reason === 'Other'">
                                        </div>
                                        <div class="invalid-feedback" v-text="errors.reason"></div>
                                    </div>

                                    <div class="mb-3">
                                        <p class="form-label text-dark d-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                fill="inherit" class="ms-1 me-2 bi bi-exclamation-octagon"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z" />
                                                <path
                                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                            </svg>
                                            Please explain your report in more detail.
                                        </p>
                                        <textarea class="form-control" v-bind:class="{ 'is-invalid': errors.description }" id="description"
                                            v-model="formData.description" rows="4"></textarea>
                                        <div class="invalid-feedback" v-text="errors.description"></div>
                                    </div>

                                    <div class="d-flex justify-content-center  my-4">
                                        
                                        <button type="submit" class="btn btn-primary text-light px-3 rounded-pill"
                                            v-bind:disabled="loading">
                                            <span v-show="loading"
                                                class="spinner-border spinner-border-sm me-1"></span>
                                            Submit Report
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      </div>
    </main>
</body>
{{-- <script src="{{ asset('/assets/js/shared/chat.js') }}"></script> --}}

</html>
