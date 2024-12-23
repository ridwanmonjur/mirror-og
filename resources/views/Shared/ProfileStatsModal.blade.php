   <!-- Modals -->
    <div x-data="profileData" id="connectionModal" class="modal fade" tabindex="-1" aria-labelledby="connectionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" style="top: 10vh; color: black;">
            <div class="modal-content mx-3">
              
                <div class="modal-body mb-3">
                    <div>
                        <h5 class="ms-3 my-3" x-text="currentTab.charAt(0).toUpperCase() + currentTab.slice(1).toLowerCase()"></h5>
                    </div>
                    <div class="d-flex justify-content-between flex-wrap mt-2 mb-3">
                        <button class="btn btn-link py-0" data-bs-dismiss="modal" aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                            </svg>
                        </button>
                        <template x-if="role === 'PARTICIPANT'"> 
                        <ul class="nav " id="connectionTabs" role="tablist">
                            <li :class="{'btn nav-item ms-0 px-4 py-2 ' : true, ' text-primary ' : currentTab == 'followers' }" role="presentation"
                                id="followers-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#followers" 
                                type="button" 
                                role="tab" 
                                x-on:click="resetSearch('followers');"
                                >
                                 Followers
                            </li>
                            <li :class=" {'btn nav-item px-4 py-2' : true, ' text-primary ' : currentTab == 'following' }" role="presentation"
                                id="following-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#following" 
                                type="button" 
                                role="tab" 
                                x-on:click="resetSearch('following');"
                            >
                                 Following
                            </li>
                            <li :class=" { 'btn nav-item px-4 py-2 ' : true, ' text-primary ' : currentTab == 'friends'  }" role="presentation"
                                id="friends-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#friends" 
                                type="button" 
                                role="tab" 
                                x-on:click="resetSearch('friends');"
                            >
                                 Friends
                            </li>
                        </ul>
                        </template>
                        <template x-if="role === 'ORGANIZER' || role === 'TEAM'"> 
                        <ul class="nav " id="connectionTabs" role="tablist">
                            {{-- <li :class="{'btn nav-item ms-0 px-4 py-2 ' : true, ' text-primary ' : currentTab == 'followers' }" role="presentation"
                                id="followers-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#followers" 
                                type="button" 
                                role="tab" 
                                x-on:click="resetSearch('followers');"
                                >
                                 Followers
                            </li> --}}
                            
                        </ul>
                        </template>
                        <div >
                            <div class="input-group position-relative border border-primary">
                                <span class="input-group-text border border-end-0 bg-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search text-primary" viewBox="0 0 16 16">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                    </svg>
                                </span>
                                <input id="search-connections" @input.debounce="loadSearch" type="search" class=" form-control ps-3 border-start-0 ps-0 pe-4" placeholder="Search..." aria-label="Search">
                            </div>
                        </div>
                    </div>
                    <!-- Profile Info -->
                    <div class="table-responsive">
                        <template x-if="!connections || !connections[0]">
                            <div>
                                <br>
                                <div class="text-center my-4">No users in this list.</div>
                            </div>
                        </template>
                        <template x-if="connections || connections[0]">
                            <div>
                                <table class="member-table table responsive member-table mb-0 fs-7" >
                                    <tbody>
                                        <template x-for="user in connections" :key="user.id">
                                            <tr>
                                                <td class="text-center px-3 py-2" style="width: 50px;">
                                                    <a :href="`/view/${user?.role?.toLowerCase()}/${user.id}` ">
                                                        <svg class="cursor-pointer"
                                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                            fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                            <path
                                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                        </svg>
                                                    </a>
                                                </td>
                                                <td class="py-2 px-3">
                                                    <div class="d-flex align-items-center">
                                                        <img :src="'/storage/' + user.userBanner"
                                                            class="rounded-circle object-fit-cover border border-primary me-3" width="25" height="25"
                                                            onerror="this.src='/assets/images/404.png';"
                                                        >
                                                        <span x-text="user.name"></span>
                                                    </div>
                                                </td>
                                                <td class="py-2 px-3" x-text="user.email"></td>
                                                <td class="py-2 px-3" x-text="formatDate(user.created_at)"></td>
                                                <td>
                                                    <div class="dropdown  dropdown-stats">
                                                        <button class="btn btn-light border-0 shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <circle cx="12" cy="12" r="1"></circle>
                                                                <circle cx="19" cy="12" r="1"></circle>
                                                                <circle cx="5" cy="12" r="1"></circle>
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown-menu  dropdown-menu-end" style="padding: 2px ;">
                                                            <li>
                                                                <a class="dropdown-item ms-0 py-1" href="#">
                                                                    <svg class="me-1 mt-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                                                    </svg>
                                                                    Send Messages
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item ms-0 py-1 " href="#">
                                                                    <svg class="me-1 mt-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <circle cx="12" cy="12" r="10"></circle>
                                                                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                                                                    </svg>
                                                                    Block Chat
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item ms-0 report-item py-1 " href="#">
                                                                    <svg class="text-red me-1 mt-1" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                                                        <line x1="4" y1="22" x2="4" y2="15"></line>
                                                                    </svg>
                                                                    <span class="text-red py-0 my-0"> Report User </span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                              
                            </div>
                        </template>

                        <!-- Pagination -->
                        <template x-if="next_page">
                            <div aria-label="Page navigation"  class="mt-3 d-flex justify-content-center">
                                <button class="btn  btn-link " x-on:click="loadNextPage">Next page</button>
                            </div>
                        </template>
                        <br><br>
                        <br><br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
