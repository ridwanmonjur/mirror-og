   <!-- Modals -->
   <div x-data="profileData" id="connectionModal" class="modal fade" tabindex="-1" aria-labelledby="connectionModalLabel"
       aria-hidden="true">
       <div class="modal-dialog modal-xl" style="top: 10vh; color: black;">
           <div class="modal-content ">

               <div class="modal-body mb-3">
                   <div>
                       <h5 class="ms-3 my-3"
                           x-text="currentTab.charAt(0).toUpperCase() + currentTab.slice(1).toLowerCase()"></h5>
                   </div>
                   <div class="d-flex justify-content-between flex-wrap mt-2 mb-3">
                       <button class="btn btn-link py-0" data-bs-dismiss="modal" aria-label="Close">
                           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                               class="bi bi-chevron-left" viewBox="0 0 16 16">
                               <path fill-rule="evenodd"
                                   d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                           </svg>
                       </button>
                       <template x-if="role === 'PARTICIPANT'">
                           <ul class="nav " id="connectionTabs" role="tablist">
                               <li :class="{ 'btn nav-item ms-0 px-4 py-2 ': true, ' text-primary ': currentTab == 'followers' }"
                                   role="presentation" id="followers-tab" data-bs-toggle="tab"
                                   data-bs-target="#followers" type="button" role="tab"
                                   x-on:click="resetSearch('followers');"
                                >
                                   Followers 
                               </li>
                               <li :class="{ 'btn nav-item px-4 py-2': true, ' text-primary ': currentTab == 'following' }"
                                   role="presentation" id="following-tab" data-bs-toggle="tab"
                                   data-bs-target="#following" type="button" role="tab"
                                   x-on:click="resetSearch('following');">
                                   Following
                               </li>
                               <li :class="{ 'btn nav-item px-4 py-2 ': true, ' text-primary ': currentTab == 'friends' }"
                                   role="presentation" id="friends-tab" data-bs-toggle="tab" data-bs-target="#friends"
                                   type="button" role="tab" x-on:click="resetSearch('friends');">
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
                       <template x-if="!connections || !connections[0]">
                           <div>
                               <br>
                               <div class="text-center my-4">No users in this list.</div>
                           </div>
                       </template>
                       <template x-if="connections || connections[0]">
                           <div>
                               <div class="mt-3 px-0 justify-content-center grid-2-columns mx-auto ">
                                   <template x-for="(user, index) in connections" :key="user.id">

                                       <div class=" border border-secondary mx-3 mb-3 pt-3 pb-2 px-3"
                                           style="border-radius: 20px; padding-top: 10px; padding-bottom: 10px;">
                                           <div class="position-relative">
                                               <div class="d-flex align-items-start">
                                                   <a :href="`/view/${user?.role?.toLowerCase()}/${user.id}`">
                                                       <img :src="'/storage/' + user.userBanner"
                                                           class="rounded-circle object-fit-cover border border-secondary me-2"
                                                           width="70" height="70"
                                                           onerror="this.src='/assets/images/404.png';">
                                                   </a>
                                                   <div class="text-start">
                                                       <h6 class="card-title mb-1  text-truncate" x-text="user.name">
                                                       </h6>
                                                       <p class="card-text mb-0 mt-2" style="color: gray;"
                                                           x-text="user.email"></p>
                                                       @if (!isset($user) || $user?->role !== 'ORGANIZER')
                                                           <div class="mt-2">
                                                                <template x-if="user.logged_friendship_status == 'accepted'">
                                                                    <div class="d-inline mx-0 px-0">
                                                                        <button x-cloak
                                                                            class="btn btn-success text-dark btn-sm px-3 rounded-pill mb-1"
                                                                            style="pointer-events: none;"
                                                                         >
                                                                            Friends
                                                                        </button>
                                                                        <svg 
                                                                            class="rounded me-2 cursor-pointer rounded-circle "
                                                                            style="border: 1px solid red;"
                                                                            data-action="unfriend"
                                                                            x-on:click="friendRequest(event)"
                                                                            x-bind:data-route="'/participant/friends'"
                                                                            x-bind:data-inputs='user.id'
                                                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" viewBox="0 0 16 16">
                                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template x-if="!user.logged_friendship_status">
                                                                    <div class="d-inline mx-0 px-0">
                                                                         <button x-cloak
                                                                            class="btn btn-primary btn-sm px-3 text-light rounded-pill me-2 mb-1"
                                                                            data-action="friend-request"
                                                                            x-on:click="friendRequest(event)"
                                                                            x-bind:data-route="'/participant/friends'"
                                                                            x-bind:data-inputs='user.id'>
                                                                            Add Friend
                                                                        </button>
                                                                    </div>
                                                                </template>

                                                                <template x-if="(user.logged_friendship_actor != loggedUserId) && (loggedUserId != null)">
"                                                                   <div class="d-inline mx-0 px-0">
                                                                        <template x-if="user.logged_friendship_status == 'pending'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button x-cloak
                                                                                    class="btn bg-primary border-primary text-white btn-sm px-3  rounded-pill me-1 mb-1"
                                                                                    data-action="acceptt-pending-request"
                                                                                    x-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                                    </svg>
                                                                                    Accept request
                                                                                </button>
                                                                                <button x-cloak
                                                                                    class="btn bg-red text-white btn-sm px-3  rounded-pill me-2 mb-1"
                                                                                    data-action="reject-request"
                                                                                    x-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                >
                                                                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                                                    </svg>
                                                                                    Reject
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                        <button x-cloak
                                                                            x-show="user.logged_friendship_status == 'rejected'"
                                                                            class="btn border-primary  btn-sm px-3 text- rounded-pill me-2 mb-1"
                                                                            x-on:click="friendRequest(event)"
                                                                            type="button"
                                                                            data-action="accept-after-reject-request"
                                                                            x-bind:data-route="'/participant/friends'"
                                                                            x-bind:data-inputs='user.id'
                                                                        >
                                                                            Rejected User
                                                                        </button>
                                                                        <button x-cloak
                                                                            x-show="user.logged_friendship_status == 'left'"
                                                                            class="btn border-primary  btn-sm px-3 text- rounded-pill me-2 mb-1"
                                                                            x-on:click="friendRequest(event)"
                                                                            type="button"
                                                                            data-action="accept-after-left-request"
                                                                            x-bind:data-route="'/participant/friends'"
                                                                            x-bind:data-inputs='user.id'
                                                                        >
                                                                            Not Friends
                                                                        </button>
                                                                       
                                                                    </div>
                                                                </template>

                                                                <template x-if="user.logged_friendship_actor == loggedUserId">
"                                                                   <div class="d-inline mx-0 px-0">
                                                                        <template x-if="user.logged_friendship_status == 'pending'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button x-cloak
                                                                                    x-show="user.logged_friendship_status == 'pending'"
                                                                                    class="btn border-primary  btn-sm px-3 text-primary rounded-pill me-1 mb-1"
                                                                                    style="pointer-events: none;"
                                                                                    type="button"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                >
                                                                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                                    </svg>
                                                                                    Request Sent
                                                                                </button>
                                                                                <svg 
                                                                                   data-action="cancel-request"
                                                                                    x-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash me-2" viewBox="0 0 16 16">
                                                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                                                </svg>
                                                                            </div>
                                                                        </template>
                                                                        <template x-if="user.logged_friendship_status == 'left'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button x-cloak
                                                                                    class="btn border-primary  btn-sm px-3 text- rounded-pill me-2 mb-1"
                                                                                    x-on:click="friendRequest(event)"
                                                                                    type="button"
                                                                                    data-action="accept-after-left-request"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                >
                                                                                    Not Friends
                                                                                </button>
                                                                                <button x-cloak
                                                                                    class="btn bg-primary border-primary text-white btn-sm px-3  rounded-pill me-1 mb-1"
                                                                                    data-action="acceptt-left-request"
                                                                                    x-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                >
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                                                    <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                                                                                    </svg>
                                                                                    Re-friend
                                                                                </button>
                                                                            </div>
                                                                        </template>
                                                                        <template x-if="user.logged_friendship_status == 'rejected'">
                                                                            <div class="d-inline mx-0 px-0">
                                                                                <button x-cloak
                                                                                    class="btn border-primary  btn-sm px-3 text- rounded-pill me-2 mb-1"
                                                                                    x-on:click="friendRequest(event)"
                                                                                    type="button"
                                                                                    data-action="accept-after-reject-request"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
                                                                                >
                                                                                    Rejected User
                                                                                </button>
                                                                                 <button x-cloak
                                                                                    class="btn bg-primary border-primary text-white btn-sm px-3  rounded-pill me-1 mb-1"
                                                                                    data-action="acceptt-left-request"
                                                                                    x-on:click="friendRequest(event)" 
                                                                                    type="button"
                                                                                    x-bind:data-route="'/participant/friends'"
                                                                                    x-bind:data-inputs='user.id'
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

                                                               <button x-cloak x-show="!user.logged_follow_status"
                                                                   type="button"
                                                                   class="btn btn-primary btn-sm px-3 text-light rounded-pill mb-1"
                                                                   data-action="follow"
                                                                   x-on:click="followRequest(event)"
                                                                   x-bind:data-role='user.role'
                                                                   x-bind:data-route="user.role === 'PARTICIPANT' ?
                                                                       '/participant/follow' :
                                                                       '/api/participant/organizer/follow'"
                                                                   x-bind:data-inputs="user.id">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi me-1 bi-eyeglasses" viewBox="0 0 16 16">
                                                                    <path d="M4 6a2 2 0 1 1 0 4 2 2 0 0 1 0-4m2.625.547a3 3 0 0 0-5.584.953H.5a.5.5 0 0 0 0 1h.541A3 3 0 0 0 7 8a1 1 0 0 1 2 0 3 3 0 0 0 5.959.5h.541a.5.5 0 0 0 0-1h-.541a3 3 0 0 0-5.584-.953A2 2 0 0 0 8 6c-.532 0-1.016.208-1.375.547M14 8a2 2 0 1 1-4 0 2 2 0 0 1 4 0"/>
                                                                    </svg>
                                                                   Follow
                                                               </button>

                                                               <button x-cloak type="button"
                                                                   x-show="user.logged_follow_status"
                                                                   class="btn btn-success btn-sm px-3 text-dark rounded-pill mb-1"
                                                                   data-action="unfollow" 
                                                                   x-bind:data-role='user.role'
                                                                   x-on:click="followRequest(event)"
                                                                   x-bind:data-route="user.role === 'PARTICIPANT' ?
                                                                       '/participant/follow' :
                                                                       '/api/participant/organizer/follow'"
                                                                   x-bind:data-inputs="user.id">
                                                                   <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi me-1 bi-sunglasses" viewBox="0 0 16 16">
                                                                    <path d="M3 5a2 2 0 0 0-2 2v.5H.5a.5.5 0 0 0 0 1H1V9a2 2 0 0 0 2 2h1a3 3 0 0 0 3-3 1 1 0 1 1 2 0 3 3 0 0 0 3 3h1a2 2 0 0 0 2-2v-.5h.5a.5.5 0 0 0 0-1H15V7a2 2 0 0 0-2-2h-2a2 2 0 0 0-1.888 1.338A2 2 0 0 0 8 6a2 2 0 0 0-1.112.338A2 2 0 0 0 5 5zm0 1h.941c.264 0 .348.356.112.474l-.457.228a2 2 0 0 0-.894.894l-.228.457C2.356 8.289 2 8.205 2 7.94V7a1 1 0 0 1 1-1"/>
                                                                    </svg>Following
                                                               </button>

                                                           </div>
                                                       @endif
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

                                                           <a class="dropdown-item ms-0 py-1" x-bind:href="'/profile/message/?userId=' + user.id">
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
                                                                x-bind:data-route="'/api/user/' + user.id + '/block'"
                                                                x-on:click="blockRequest(event)"

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
                                                               Block Chat
                                                           </a>
                                                       </li> 
                                                       <li>
                                                           <a class="dropdown-item ms-0 report-item py-1 "
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
                       <template x-if="next_page">
                           <div aria-label="Page navigation" class="mt-3 d-flex justify-content-center">
                               <button class="btn  btn-link " x-on:click="loadNextPage">Next page</button>
                           </div>
                       </template>
                       <br><br>
                       <br>
                   </div>
               </div>
           </div>
       </div>
   </div>
