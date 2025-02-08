   <!-- Modals -->
   <div v-scope="ProfileData(
    {{$propsTeamOrUserId}},
    {{$propsUserId}},
    {{$propsIsUserSame}},
    '{{$propsRole}}',
    '{{$propsUserRole}}'
   )" id="connectionModal" class="modal w-100 fade" tabindex="-1" aria-labelledby="connectionModalLabel"
       aria-hidden="true" @vue:mounted="init"
    >
       <div class="modal-dialog modal-xl" style="top: 5vh; color: black;">
           <div class="modal-content ">

               <div class="modal-body mb-3">
                   <div>
                       <h5 class="ms-3 my-3"
                           v-text="currentTab.charAt(0).toUpperCase() + currentTab.slice(1).toLowerCase()"></h5>
                   </div>
                   <div class="d-flex user-select-none justify-content-between flex-wrap mt-2 mb-3">
                       <button class="btn btn-link py-0" data-bs-dismiss="modal" aria-label="Close">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                               class="bi bi-chevron-left" viewBox="0 0 16 16">
                               <path fill-rule="evenodd"
                                   d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                           </svg>
                       </button>
                       <template v-if="role === 'PARTICIPANT'">
                           <ul class="nav " id="connectionTabs" role="tablist">
                               <li :class="{ 'btn nav-item user-select-none ms-0 px-3 py-2 ': true, ' text-primary ': currentTab == 'followers' }"
                                   role="presentation" id="followers-tab" data-bs-toggle="tab"
                                   data-bs-target="#followers" type="button" role="tab"
                                   v-on:click="resetSearch('followers');"
                                >
                                   Followers 
                               </li>
                               <li :class="{ 'btn nav-item user-select-none px-3 py-2': true, ' text-primary ': currentTab == 'following' }"
                                   role="presentation" id="following-tab" data-bs-toggle="tab"
                                   data-bs-target="#following" type="button" role="tab"
                                   v-on:click="resetSearch('following');">
                                   Following
                               </li>
                               <li :class="{ 'btn nav-item user-select-none px-3 py-2 ': true, ' text-primary ': currentTab == 'friends' }"
                                   role="presentation" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends"
                                   type="button" role="tab" v-on:click="resetSearch('friends');">
                                   Friends
                               </li>
                           </ul>
                       </template>
                       <div>
                           <div class="input-group position-relative border border-primary">
                               <span class="input-group-text border border-end-0 bg-white">
                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                       fill="currentColor" class="bi bi-search text-primary" viewBox="0 0 16 16">
                                       <path
                                           d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                   </svg>
                               </span>
                               <input id="search-connections" @input.debounce="loadSearch" type="search"
                                   class=" form-control ps-2 border-start-0 ps-0 pe-4" placeholder="Search..."
                                   aria-label="Search">
                           </div>
                       </div>
                   </div>
                   <!-- Profile Info -->
                   <div>
                       <template v-if="!connections || !connections[0]">
                           <div>
                               <br>
                               <div class="text-center my-4">No users in this list.</div>
                           </div>
                       </template>
                       <template v-if="connections || connections[0]">
                           <div>
                               <div class="mt-3 px-0 justify-content-center grid-2-columns mx-auto user-select-none ">
                                   <template v-for="(user, index) in connections" :key="user.id">

                                       <div class=" border border-secondary mx-1 mb-3 py-3 px-2"
                                           style="border-radius: 20px; padding-top: 10px; padding-bottom: 10px;">
                                           <div class="position-relative">
                                               <div class="d-flex align-items-center">
                                                   <a v-bind:href="`/view/${user?.role?.toLowerCase()}/${user.id}`">
                                                       <img v-bind:src="'/storage/' + user?.userBanner"
                                                           class="rounded-circle object-fit-cover border border-secondary me-2"
                                                           width="70" height="70"
                                                           onerror="this.src='/assets/images/404.png';">
                                                   </a>
                                                   <div class="text-start">
                                                        <template v-if="user.role == 'PARTICIPANT' && user.nickname">
                                                            <u><h5 style="width: 25ch;" class="card-title mb-1  text-truncate" v-text="user.nickname">
                                                            </h5></u>
                                                            <p style="width: 25ch;" class="card-text mb-0 mt-2" style="color: gray;"
                                                                v-text="user.name">
                                                            </p>
                                                        </template>
                                                        <template v-else>
                                                            <h6 style="width: 25ch;" class="card-title mb-3  text-truncate" v-text="user.name">
                                                            </h6>
                                                        </template>
                                                        <div class="mt-2"> 
                                                            <div v-cloak class="p-0 ms-0 my-0 me-2 d-inline-block" v-if="loggedUserRole != 'ORGANIZER' && user.role != 'ORGANIZER'">
                                                                <template v-if="user.logged_friendship_status == 'accepted'">
                                                                    <div class="d-inline mx-0 px-0">
                                                                        <button v-cloak
                                                                            class="btn btn-success text-dark btn-sm px-3 rounded-pill mb-1 dropdown-toggle dropdown-show"
                                                                            role="button" id="dropdownMenuLink"
                                                                            v-bind:data-role="user.role"
                                                                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                                        >
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-check-fill me-2" viewBox="0 0 16 16">
                                                                                <path fill-rule="evenodd" d="M15.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L12.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0"></path>
                                                                                <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"></path>
                                                                            </svg>
                                                                            <span>Friends</span>
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="ms-2 bi bi-chevron-down" viewBox="0 0 16 16">
                                                                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                                                                            </svg>
                                                                        </button>
                                                                        <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuLink">
                                                                            <button class="dropdown-item cursor-pointer  px-3 py-2" 
                                                                                data-action="unfriend"
                                                                                type="button"
                                                                                v-on:click="friendRequest(event)"
                                                                                v-bind:data-route="'/participant/friends'"
                                                                                v-bind:data-inputs="user.id"
                                                                                v-bind:data-role="user.role"
                                                                            >
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                                                    class="bi bi-person-x-fill me-2" viewBox="0 0 16 16">
                                                                                    <path fill-rule="evenodd"
                                                                                        d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708" />
                                                                                </svg>
                                                                                <span> Remove friendship</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                                <template v-if="!user.logged_friendship_status">
                                                                    <div class="d-inline mx-0 px-0">
                                                                        <button v-cloak
                                                                            class="btn btn-primary btn-sm px-3 text-light rounded-pill  mb-1"
                                                                            data-action="friend-request"
                                                                            v-on:click="friendRequest(event)"
                                                                            v-bind:data-route="'/participant/friends'"
                                                                            v-bind:data-role="user.role"
                                                                            v-bind:data-inputs="user.id">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle me-1" viewBox="0 0 16 16">
                                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                                                            </svg>
                                                                            Add Friend
                                                                        </button>
                                                                    </div>
                                                                </template>

                                                                <template v-if="(user.logged_friendship_actor != loggedUserId) && (loggedUserId != null)">
                                                                   <div class="d-inline mx-0 px-0">
                                                                        <template v-if="user.logged_friendship_status == 'pending'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button v-cloak
                                                                                    class="btn bg-primary border-primary text-white btn-sm px-3  rounded-pill me-2 mb-1"
                                                                                    data-action="acceptt-pending-request"
                                                                                    v-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    v-bind:data-route="'/participant/friends'"
                                                                                    v-bind:data-inputs="user.id"
                                                                                    v-bind:data-role="user.role"
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                                    </svg>
                                                                                    Accept request
                                                                                </button>
                                                                                <button v-cloak
                                                                                    class="btn bg-red text-white btn-sm px-3  rounded-pill  mb-1"
                                                                                    data-action="reject-request"
                                                                                    v-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    v-bind:data-route="'/participant/friends'"
                                                                                    v-bind:data-inputs="user.id"
                                                                                    v-bind:data-role="user.role"
                                                                                >
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                                                    </svg>
                                                                                    Reject
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                        <button v-cloak
                                                                            v-show="user.logged_friendship_status == 'rejected'"
                                                                            class="btn border-danger  btn-sm px-3 text-red rounded-pill  mb-1"
                                                                            style="pointer-events: none;"
                                                                            type="button"
                                                                        >
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-emoji-frown" viewBox="0 0 16 16">
                                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                            <path d="M4.285 12.433a.5.5 0 0 0 .683-.183A3.5 3.5 0 0 1 8 10.5c1.295 0 2.426.703 3.032 1.75a.5.5 0 0 0 .866-.5A4.5 4.5 0 0 0 8 9.5a4.5 4.5 0 0 0-3.898 2.25.5.5 0 0 0 .183.683M7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5m4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5"/>
                                                                            </svg>
                                                                            Rejected User
                                                                        </button>
                                                                        <button v-cloak
                                                                            v-show="user.logged_friendship_status == 'left'"
                                                                            class="btn border-primary  btn-sm px-3 text- rounded-pill  mb-1"
                                                                            style="pointer-events: none;"
                                                                            type="button"
                                                                        >
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-x-fill me-2" viewBox="0 0 16 16">
                                                                                <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708"></path>
                                                                            </svg>
                                                                            Not Friends
                                                                        </button>
                                                                    
                                                                    </div>
                                                                </template>

                                                                <template v-if="user.logged_friendship_actor == loggedUserId">
                                                                   <div class="d-inline mx-0 px-0">
                                                                        <template v-if="user.logged_friendship_status == 'pending'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button v-cloak
                                                                                    v-show="user.logged_friendship_status == 'pending'"
                                                                                    class="btn btn-sm px-3 text-red rounded-pill  mb-1"
                                                                                    type="button"
                                                                                    v-bind:data-route="'/participant/friends'"
                                                                                    v-bind:data-inputs="user.id"
                                                                                    data-action="cancel-request"
                                                                                    v-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    style="border: 1px solid red;"
                                                                                    v-bind:data-route="'/participant/friends'"
                                                                                    v-bind:data-inputs="user.id"
                                                                                >
                                                                                    <svg 
                                                                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash me-1" viewBox="0 0 16 16">
                                                                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                                                    </svg>
                                                                                    Cancel request
                                                                                </button>
                                                                                
                                                                            </div>
                                                                        </template>
                                                                        <template v-if="user.logged_friendship_status == 'left'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button v-cloak
                                                                                    class="btn border-primary  btn-sm px-3 text- rounded-pill  mb-1"
                                                                                    type="button"
                                                                                    style="pointer-events: none;"
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-x-fill me-2" viewBox="0 0 16 16">
                                                                                        <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m6.146-2.854a.5.5 0 0 1 .708 0L14 6.293l1.146-1.147a.5.5 0 0 1 .708.708L14.707 7l1.147 1.146a.5.5 0 0 1-.708.708L14 7.707l-1.146 1.147a.5.5 0 0 1-.708-.708L13.293 7l-1.147-1.146a.5.5 0 0 1 0-.708"></path>
                                                                                    </svg>
                                                                                    Not Friends
                                                                                </button>
                                                                                <button v-cloak
                                                                                    class="btn bg-primary border-primary text-white btn-sm px-3  rounded-pill me-1 mb-1"
                                                                                    data-action="acceptt-left-request"
                                                                                    v-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    v-bind:data-route="'/participant/friends'"
                                                                                    v-bind:data-inputs="user.id"
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                                    </svg>
                                                                                    Re-friend
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                        <template v-if="user.logged_friendship_status == 'rejected'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button v-cloak
                                                                                    class="btn border-danger  btn-sm px-3 text-red rounded-pill me-2 mb-1"
                                                                                    style="pointer-events: none;"
                                                                                    type="button"
                                                                                >
                                                                                    Rejected User
                                                                                </button>
                                                                                <button v-cloak
                                                                                    class="btn bg-primary border-primary text-white btn-sm px-3  rounded-pill  mb-1"
                                                                                    data-action="acceptt-left-request"
                                                                                    v-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    v-bind:data-route="'/participant/friends'"
                                                                                    v-bind:data-inputs="user.id"
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                                    </svg>
                                                                                    Befriend
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            <div class="d-inline-block p-0 m-0 me-2">
                                                                <button v-cloak v-if="!user.logged_follow_status"
                                                                    type="button"
                                                                    class="btn btn-primary btn-sm px-3 text-light rounded-pill mb-1"
                                                                    data-action="follow"
                                                                    v-on:click="followRequest(event)"
                                                                    v-bind:data-role="user.role"
                                                                    v-bind:data-route="user.role === 'PARTICIPANT' ?
                                                                        '/participant/follow' :
                                                                        '/api/participant/organizer/follow'"
                                                                    v-bind:data-inputs="user.id"
                                                                >
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi me-1 bi-eyeglasses" viewBox="0 0 16 16">
                                                                        <path d="M4 6a2 2 0 1 1 0 4 2 2 0 0 1 0-4m2.625.547a3 3 0 0 0-5.584.953H.5a.5.5 0 0 0 0 1h.541A3 3 0 0 0 7 8a1 1 0 0 1 2 0 3 3 0 0 0 5.959.5h.541a.5.5 0 0 0 0-1h-.541a3 3 0 0 0-5.584-.953A2 2 0 0 0 8 6c-.532 0-1.016.208-1.375.547M14 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/>
                                                                        </svg>
                                                                    Follow
                                                                </button>

                                                                <button v-cloak type="button"
                                                                    v-if="user.logged_follow_status"
                                                                    class="btn btn-success btn-sm px-3 text-dark rounded-pill mb-1"
                                                                    data-action="unfollow" 
                                                                    v-bind:data-role="user.role"
                                                                    v-on:click="followRequest(event)"
                                                                    v-bind:data-route="user.role === 'PARTICIPANT' ?
                                                                        '/participant/follow' :
                                                                        '/api/participant/organizer/follow'"
                                                                    v-bind:data-inputs="user.id">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi me-1 bi-sunglasses" viewBox="0 0 16 16">
                                                                        <path d="M3 5a2 2 0 0 0-2 2v.5H.5a.5.5 0 0 0 0 1H1V9a2 2 0 0 0 2 2h1a3 3 0 0 0 3-3 1 1 0 1 1 2 0 3 3 0 0 0 3 3h1a2 2 0 0 0 2-2v-.5h.5a.5.5 0 0 0 0-1H15V7a2 2 0 0 0-2-2h-2a2 2 0 0 0-1.888 1.338A2 2 0 0 0 8 6a2 2 0 0 0-1.112.338A2 2 0 0 0 5 5zm0 1h.941c.264 0 .348.356.112.474l-.457.228a2 2 0 0 0-.894.894l-.228.457C2.356 8.289 2 8.205 2 7.94V7a1 1 0 0 1 1-1"/>
                                                                        </svg>Following
                                                                </button>
                                                            </div>
                                                            <div v-cloak v-if="user.logged_block_status" class="d-inline m-0 p-0">
                                                                 <a v-bind:href="`/view/${user?.role?.toLowerCase()}/${user.id}`">
                                                                    <button  type="button"
                                                                        class="btn border-danger btn-sm px-3 text-red me-2 rounded-pill mb-1"
                                                                    >
                                                                        <svg class="me-1 mt-1"
                                                                            xmlns="http://www.w3.org/2000/svg" width="13"
                                                                            height="13" viewBox="0 0 24 24" fill="none"
                                                                            stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round" stroke-linejoin="round">
                                                                            <circle cx="12" cy="12" r="10">
                                                                            </circle>
                                                                            <line x1="4.93" y1="4.93" x2="19.07"
                                                                                y2="19.07"></line>
                                                                        </svg>
                                                                        Blocked Chat
                                                                    </button>
                                                                </a>
                                                            </div>

                                                        </div>
                                                   </div>

                                               </div>

                                               <div class="dropdown position-absolute top-0" style="left: 90%;">
                                                   <button class="btn btn-link px-0 border-0 shadow-none"
                                                       type="button" data-bs-toggle="dropdown"
                                                       aria-expanded="false">
                                                       <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                           height="24" viewBox="0 0 24 24" fill="none"
                                                           stroke="currentColor" stroke-width="2"
                                                           stroke-linecap="round" stroke-linejoin="round">
                                                           <circle cx="12" cy="12" r="1"></circle>
                                                           <circle cx="19" cy="12" r="1"></circle>
                                                           <circle cx="5" cy="12" r="1"></circle>
                                                       </svg>
                                                   </button>
                                                   <ul class="dropdown-menu  dropdown-menu-end"
                                                       style="padding: 2px ;">
                                                       <li>

                                                           <a class="dropdown-item ms-0 py-1" v-bind:href="'/profile/message/?userId=' + user.id">
                                                               <svg class="me-1 mt-1"
                                                                   xmlns="http://www.w3.org/2000/svg" width="13"
                                                                   height="13" viewBox="0 0 24 24" fill="none"
                                                                   stroke="currentColor" stroke-width="2"
                                                                   stroke-linecap="round" stroke-linejoin="round">
                                                                   <path
                                                                       d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z">
                                                                   </path>
                                                               </svg>
                                                               Send Messages
                                                           </a>
                                                       </li>
                                                        <li>
                                                           <a class="dropdown-item ms-0 py-1 " href="#"
                                                                v-bind:data-route="'/api/user/' + user.id + '/block'"
                                                                v-on:click="blockRequest(event)"
                                                                v-bind:data-status="user.logged_block_status"
                                                                v-bind:data-inputs="user.id"

                                                           >
                                                               <svg class="me-1 mt-1"
                                                                   xmlns="http://www.w3.org/2000/svg" width="13"
                                                                   height="13" viewBox="0 0 24 24" fill="none"
                                                                   stroke="currentColor" stroke-width="2"
                                                                   stroke-linecap="round" stroke-linejoin="round">
                                                                   <circle cx="12" cy="12" r="10">
                                                                   </circle>
                                                                   <line x1="4.93" y1="4.93" x2="19.07"
                                                                       y2="19.07"></line>
                                                               </svg>
                                                               <span v-text=" user.logged_block_status ? 'Unblock Chat': 'Block Chat'">  </span>
                                                           </a>
                                                       </li> 
                                                       <li v-on:click="triggerReportSelection(event)" 
                                                            v-bind:data-user-id="user.id" 
                                                            v-bind:data-user-name="user.name" 
                                                            v-bind:data-user-banner="user?.userBanner"  
                                                            data-bs-dismiss="modal" aria-haspopup="true"
                                                       >
                                                           <a class="dropdown-item ms-0 text-red report-item py-1 "
                                                               href="#">
                                                               <svg class="text-red me-1 mt-1"
                                                                   xmlns="http://www.w3.org/2000/svg" width="13"
                                                                   height="13" viewBox="0 0 24 24" fill="none"
                                                                   stroke="currentColor" stroke-width="2"
                                                                   stroke-linecap="round" stroke-linejoin="round">
                                                                   <path
                                                                       d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z">
                                                                   </path>
                                                                   <line x1="4" y1="22" x2="4"
                                                                       y2="15"></line>
                                                               </svg>
                                                               <span class="text-red py-0 my-0"> Report User </span>
                                                           </a>
                                                       </li> 
                                                   </ul>
                                               </div>
                                           </div>
                                       </div>
                                   </template>
                               </div>
                           </div>
                       </template>

                       <!-- Pagination -->
                       <template v-if="next_page">
                           <div aria-label="Page navigation" class="mt-3 d-flex justify-content-center">
                               <button class="btn  btn-link " v-on:click="loadNextPage">Next page</button>
                           </div>
                       </template>
                   </div>
               </div>
           </div>
       </div>
   </div>
@auth
    <div v-scope="ReportFormData()" id="reportUserModal" @vue:mounted="init"  class="modal" >
            <div  tabindex="-1">
                <div class="modal-dialog modal-lg mb-0"  style="color: black !important; text-align: start !important;">
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
                                        width="35" height="35" onerror="this.src='/assets/images/404.png';">
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
                                    <div class="card mt-3 p-5 mb-5">
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
                                                                onerror="this.src='{{ asset('assets/images/404.png') }}'">
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
                                        width="35" height="35" onerror="this.src='/assets/images/404.png';">

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
                                        <button type="button"
                                            class="btn btn-secondary px-3 text-white rounded-pill me-4"
                                            v-on:click="reset()" data-bs-target="#connectionModal"
                                            data-bs-dismiss="modal" data-bs-toggle="modal">Cancel</button>
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
@endauth