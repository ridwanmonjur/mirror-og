<div  @vue:mounted="init"
    id="notif-dropdown" 
    v-scope="PageNotificationComponent()"
    class="dropdown me-3" data-reference="parent" data-bs-auto-close="outside" >
    <a href="#" role="button" class="btn d-flex justify-content-start align-items-center m-0 p-0" id="dropNotification" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true"
    >
        
        <img width="42px" height="38px" src="{{ asset('/assets/images/navbar-bell.png') }}" alt=""
            class="me-2"    style="object-position: center;"
        >
        <span>
                <svg width="6" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 16">
                    <circle v-if="true" cx="2" cy="2" r="2" v-bind:fill="notificationColors.social"></circle>
                    <circle v-if="true" cx="2" cy="8" r="2" v-bind:fill="notificationColors.teams"></circle>
                    <circle v-if="true" cx="2" cy="14" r="2" v-bind:fill="notificationColors.event"></circle>
                </svg>
            </span>
    </a>
    <div class="dropdown-menu border shadow-lg mx-2 py-2 py-0" style="position: absolute; left: -500px; border-radius: 10px; top: 70px; font-size: 14px; width: 600px;"
        aria-labelledby="dropNotification"
            onclick="event.stopPropagation()"
    >
        <div class="d-flex mx-4 mt-2 justify-content-between py-1">
            <h5 class="py-0 my-0">Notifications</h5>
            <a href="{{ route('user.notif.view') }}" role="button" class="px-2 py-1 btn btn-small bg-secondary text-white fs-7 me-3" 
                aria-haspopup="true" aria-expanded="true"
            >
                See all
            </a>
            
        </div>
        <div     
        >
            <div class="tabs w-100 d-block row py-1 mx-2 px-0 " >
                <button id="SocialBtn" class="tab-button d-inline col-12 col-lg-3 py-1  outer-tab"
                    v-bind:class="{ 'tab-button-active': currentTab == 'social' }" 
                    style="width: auto;"
                    v-on:click="changeNotificationTab('social')"    
                >Social
                <span v-if="true" class="me-2">
                    <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                        <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors['social']"></circle>
                    </svg>
                </span>
                </button>
                <button id="TeamsBtn" class="tab-button py-1 col-12 col-lg-3  d-inline  outer-tab"
                    onclick="showTab(event, 'Teams', 'outer-tab')"
                    v-on:click="changeNotificationTab('teams')"
                    v-bind:class="{ 'tab-button-active': currentTab == 'teams' }" 
                    style="width: auto;"
                >
                    Teams
                    <span v-if="true" class="me-2">
                        <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                            <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors['teams']"></circle>
                        </svg>
                    </span>
                </button>
                <button id="EventBtn" class="tab-button py-1  col-12 col-lg-3 d-inline  outer-tab"
                    v-on:click="changeNotificationTab('event')"   
                    v-bind:class="{ 'tab-button-active': currentTab == 'event' }" 
                    style="width: auto;"
                >Event
                <span v-if="true" class="me-2">
                    <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                        <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors['event']"></circle>
                    </svg>
                </span>
                </button>
            </div>
            <div class="mx-2">
                <div class="notification-list">
                    <template v-for="notification in notificationList" :key="notification.id">
                        <div 
                            class="notification-item cursor-pointer d-flex align-items-center p-3 border-0 "
                            v-on:click="markNotificationRead(notification.id, notification.link)"
                        >
                           
                            <div class="notification-icon " style="white-space: nowrap;">
                                <span v-if="!notification.isRead" class="me-2">
                                    <svg width="5" height="5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 4">
                                        <circle cx="2" cy="2" r="2" v-bind:fill="notificationColors[currentTab]"></circle>
                                    </svg>
                                </span>
                                <template v-if="notification.iconType" >
                                    <span class="me-2" v-html="getIconSvg(notification.iconType)"></span>
                                </template>
                                <template v-else-if="notification.imageSrc">
                                    <img v-bind:src="notification.imageSrc" class="rounded-circle object-fit-cover me-2" width="30"
                                        height="30" alt="Profile">
                                </template>
                            </div>
                            <div class="notification-content ">
                                <div  v-html="notification.html"></div>
                                <small class="text-muted" v-text="formatTime(notification.createdAt)"></small>
                            </div>
                        </div>
                    </template> 
                </div>
            </div>
        </div>

    </div>
</div>
<div class="dropdown" data-reference="parent" data-bs-auto-close="outside" >
    <a href="#" role="button" class="btn m-0 p-0" id="dropUser" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true">
        @if($user->userBanner)
            <img
                class="object-fit-cover rounded-circle me-2 border border-primary" 
                src="{{ bladeImageNull($user->userBanner)}}" width="38" height="38">
        @else 
            <span style="display: inline-block; height: 38px; width: 38px;"
                class="bg-dark d-flex justify-content-center align-items-center text-light rounded-circle">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </span>
        @endif
    </a>
    {{-- <a href="#" role="button" class="btn" id="dropUser" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true">
        <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
    </a> --}}
    <div class="dropdown-menu border shadow-lg  py-0" style="position: absolute; left: -200px; border-radius: 10px; top: 70px; font-size: 14px; width: 250px;"
        aria-labelledby="dropUser">
        <div class="border-secondary border-1 border-bottom text-center mb-0 px-2">
            <a class="py-0" href="{{ route(strtolower($user->role) . '.profile.view') }}">
                <div class="py-navbar d-flex justify-content-start align-items-center py-2 ps-2">
                    @if ($user->userBanner)
                        <img class="object-fit-cover rounded-circle me-2 border border-primary"
                            src="{{bladeImageNull($user->userBanner)}}" width="45" height="45">
                    @else
                        <span style="display: inline-block; height: 45px; min-width: 45px; max-width: 45px;"
                            class="bg-dark d-flex justify-content-center align-items-center text-light rounded-circle">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    @endif
                    <span class="text-start hover-bigger align-middle ms-2">
                        <small class="d-inline-block text-truncate"> {{ $user->name }}</small> <br>
                        <small class="d-inline-block text-truncate"> {{ $user->email }}</small>
                        {{-- <small> N__Edit put the profile link </small> --}}
                    </span>
                </div>
            </a>
        </div>
        @if ($user->role == 'ORGANIZER')
            <a class="dropdown-item py-navbar  ps-4 align-middle " href="{{ route('organizer.profile.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-person-circle me-3" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
                Profile
            </a>
            <a class="dropdown-item py-navbar my-0  ps-4 align-middle " href="{{ route('event.create') }}">
               <svg version="1.1" class="me-3" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" enable-background="new 0 0 32 32" xml:space="preserve" 
                    width="22" height="22" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <polygon points="22,16.8 22,26 6,26 6,10 15.2,10 17.2,8 4,8 4,28 24,28 24,14.8 "></polygon> <path fill="none" stroke="#000000" stroke-width="2" stroke-miterlimit="10" d="M16.5,18.3L13,19l0.7-3.5l9.9-9.9 c0.8-0.8,2-0.8,2.8,0l0,0c0.8,0.8,0.8,2,0,2.8L16.5,18.3z"></path> </g></svg>
               <span>Create</span>
            </a>
            <a class="dropdown-item py-navbar my-0   ps-4 align-middle " href="{{ route('event.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-kanban me-3" viewBox="0 0 16 16">
                    <path
                        d="M13.5 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-11a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zm-11-1a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                    <path
                        d="M6.5 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm-4 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm8 0a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1z" />
                </svg>
                Manage
            </a>
            
            <a class="dropdown-item  py-navbar my-0 border-secondary border-1 border-bottom  ps-4 align-middle " href="{{ route('user.settings.view') }}">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(18,18,18)" class="bi bi-gear-fill me-3" viewBox="0 0 16 16">
                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"></path>
                </svg>
                <span>Settings</span>
            </a>
           
        @endif
        @if ($user->role == 'PARTICIPANT' )
            <a class="dropdown-item py-navbar my-0  ps-4 align-middle " href="{{ route('participant.profile.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-person-circle me-3" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                </svg>
                Profile
            </a>
            <a class="dropdown-item  py-navbar my-0  ps-4 align-middle " href="{{ url('/participant/team/list/') }}">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trophy me-3" viewBox="0 0 16 16">
                <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z"/>
                </svg>
                <span>Team List</span>
            </a>
            <a class="dropdown-item  py-navbar my-0  ps-4 align-middle " href="{{ route('user.settings.view') }}">
               <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="rgb(18,18,18)" class="bi bi-gear-fill me-3" viewBox="0 0 16 16">
                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"></path>
                </svg>
                <span>Settings</span>
            </a>
             <a class=" dropdown-item border-secondary border-1 border-bottom py-navbar my-0  ps-4 align-middle  " href="{{ route('public.contact.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-rolodex me-3" viewBox="0 0 16 16">
                    <path d="M8 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                    <path d="M1 1a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h.5a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h.5a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H6.707L6 1.293A1 1 0 0 0 5.293 1zm0 1h4.293L6 2.707A1 1 0 0 0 6.707 3H15v10h-.085a1.5 1.5 0 0 0-2.4-.63C11.885 11.223 10.554 10 8 10c-2.555 0-3.886 1.224-4.514 2.37a1.5 1.5 0 0 0-2.4.63H1z"/>
                </svg>
                <span>Contact</span>
            </a>
            
        @endif
        <a class="dropdown-item py-navbar my-0  ps-4 align-middle " href="{{ route('logout.action') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="inherit"
                class="bi bi-box-arrow-right ms-1 me-3" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                <path fill-rule="evenodd"
                    d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
            </svg>
            <span>Logout</span>
        </a>
    </div>
</div>
