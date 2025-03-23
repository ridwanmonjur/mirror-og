@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
@php
    $notificationsCountArray = [
        'social_count' => 0,
        'teams_count' => 0,
        'event_count' => 0
    ];
    if (isset($user)) {
        $role = $user->role;
        if ($role == 'PARTICIPANT') {
            $routeLogo = route('public.landing.view');
        } else if ($role == 'ORGANIZER') {
            $routeLogo = route('organizer.home.view');
        }
        else {
            $routeLogo = route('public.landing.view');
        }

        $notificationsCount = $user->load('notificationCount')->notificationCount;    
        if ($notificationsCount) {
            $notificationsCountArray = $notificationsCount->toArray();
        }
    } else {
        $routeLogo = route('public.landing.view');
    }
@endphp
<div 
    class="d-none"
    id="importantUrls"
    data-search-endpoint="{{ route('public.search.view') }}"
    data-landing-endpoint="{{ route('public.landing.view') }}"
    data-social-count="{{ $notificationsCountArray['social_count'] }}"
    data-teams-count="{{ $notificationsCountArray['teams_count'] }}"
    data-event-count="{{ $notificationsCountArray['event_count'] }}"
>
</div>
<nav class="navbar justify-content-between align-items-center user-select-none px-3 ">
    <a href="{{ $routeLogo }}">
        <img width="192" height="30" src="{{ asset('/assets/images/driftwood logo.png') }}" alt="">
    </a>
    <svg style="margin-top: 10px; margin-right: 10px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" class="feather feather-menu menu-toggle" onclick="toggleNavbar()">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
    <div class="search-bar d-none-at-mobile">
        <input type="text" name="search" id="search-bar"
            value="{{app('request')->input('search')}}"
            placeholder="Search for events..."
            color="#808080"
        >
        <svg xmlns="http://www.w3.org/2000/svg" stroke="#808080" width="20" height="20"
            viewBox="0 0 24 24" fill="none"  stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-search">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
    </div>
    <div class="nav-buttons me-3">
        @guest
           <div class="dropdown d-inline-block position-relative" data-reference="parent" data-bs-auto-close="outside" >
                <a href="#" role="button" class="btn dropdown-toggle px-0  me-2 " id="dropdownMenuGuest" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true" >
                    <img width="30px" height="24px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
                    <span class="pt-2 ms-1">Sign In</span>
                </a>
                <div class="dropdown-menu shadow-lg dropdown-menu-end text-start border rounded-lg py-0 " 
                    aria-labelledby="dropdownMenuGuest">
                    <a class="dropdown-item py-navbar px-1 ps-3  special-font-signinin align-middle " href="{{ route('organizer.signin.view') }}">
                        <svg class="me-1" xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24" fill="#2e4b59"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M0 0h24v24H0z" fill="none"></path><path d="M16.53 11.06L15.47 10l-4.88 4.88-2.12-2.12-1.06 1.06L10.59 17l5.94-5.94zM19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"></path></g></svg>
                        Organizer
                    </a>
                    <a class="dropdown-item py-navbar special-font-signinin  px-1 ps-3 align-middle " href="{{ route('participant.signin.view') }}">
                    <svg class="me-1" version="1.1" id="Icons" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 32 32" xml:space="preserve" width="20px" height="20px" fill="#2e4b59"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:none;stroke:#2e4b59;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;} </style> <path d="M11.5,28H2.2c0.6-2,2.2-3.5,4.2-4.1c2.7-0.7,4.5-3.1,4.5-5.8c0-0.4-0.2-0.7-0.5-0.9C9,16.4,8.1,14.7,8,12.9 c0-0.7,0.5-1.4,1.2-1.6l1.1-0.4c0.4-0.1,0.7-0.5,0.7-0.9V6.8c1.8,2.5,4.4,4.2,7.3,4.9C18.1,12.1,18,12.5,18,13c0,1.8-1,3.4-2.5,4.3 c-0.3,0.2-0.5,0.5-0.5,0.9c0,1.7,0.8,3.3,2,4.4c1.5-0.2,2.9-0.3,4.3-0.1c-0.5-0.2-0.9-0.4-1.4-0.5c-1.6-0.4-2.7-1.7-2.9-3.2 c1.8-1.3,3-3.4,3-5.7c0-0.6,0.4-1,1-1c0.6,0,1-0.4,1-1V8.7c0-3.4-2.5-6.3-5.8-6.7c-2-0.2-4,0.5-5.4,2C9.8,3.9,8.9,4.2,8,4.9 C6.7,5.8,6,7.3,6,9v3.8c0,0,0,0.1,0,0.1c0,0,0,0.1,0,0.1c0,0,0,0.1,0,0.1c0.1,2.2,1.2,4.3,2.9,5.6c-0.2,1.6-1.4,2.9-3,3.3 c-3,0.8-5.2,3.1-5.8,6.1c-0.1,0.5,0,0.9,0.3,1.3C0.8,29.8,1.2,30,1.7,30h10.2C11.3,29.5,11.2,28.6,11.5,28z"></path> <path d="M26,20c-1.2,0-2.3,0.3-3.3,1h-4.4c-1-0.7-2.1-1-3.3-1c-3.3,0-6,2.7-6,6s2.7,6,6,6c1.2,0,2.3-0.3,3.3-1h4.4 c1,0.7,2.1,1,3.3,1c3.3,0,6-2.7,6-6S29.3,20,26,20z M17,27h-1v1c0,0.6-0.4,1-1,1s-1-0.4-1-1v-1h-1c-0.6,0-1-0.4-1-1s0.4-1,1-1h1v-1 c0-0.6,0.4-1,1-1s1,0.4,1,1v1h1c0.6,0,1,0.4,1,1S17.6,27,17,27z M25.7,27.7c-0.1,0.1-0.2,0.2-0.3,0.2C25.3,28,25.1,28,25,28 c-0.1,0-0.1,0-0.2,0c-0.1,0-0.1,0-0.2-0.1c-0.1,0-0.1-0.1-0.2-0.1c0,0-0.1-0.1-0.1-0.1c-0.1-0.1-0.2-0.2-0.2-0.3S24,27.1,24,27 c0-0.3,0.1-0.5,0.3-0.7c0.3-0.3,0.7-0.4,1.1-0.2c0.1,0.1,0.2,0.1,0.3,0.2c0.2,0.2,0.3,0.4,0.3,0.7C26,27.3,25.9,27.5,25.7,27.7z M28,25.2c0,0.1,0,0.1-0.1,0.2c0,0.1,0,0.1-0.1,0.2c0,0-0.1,0.1-0.1,0.1C27.5,25.9,27.3,26,27,26c-0.1,0-0.1,0-0.2,0 c-0.1,0-0.1,0-0.2-0.1c-0.1,0-0.1-0.1-0.2-0.1c0,0-0.1-0.1-0.1-0.1C26.1,25.5,26,25.3,26,25c0-0.3,0.1-0.5,0.3-0.7 c0.4-0.4,1-0.4,1.4,0c0,0,0.1,0.1,0.1,0.1c0,0.1,0.1,0.1,0.1,0.2c0,0.1,0,0.1,0.1,0.2c0,0.1,0,0.1,0,0.2S28,25.1,28,25.2z"></path> </g></svg>
                        Participant
                    </a>
                </div>
            </div>
        @endguest
        @auth
            @include('includes.__Navbar.NavbarAuth')
        @endauth
    </div>
</nav>
<nav class="mobile-navbar d-centered-at-mobile align-items-center d-none py-3" id="mobile-navbar">
    <div class="search-bar search-bar-mobile ">
        <input
            type="text" name="search" id="search-bar-mobile"
            value="{{app('request')->input('search')}}"
            placeholder="Search for events...">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-search" style="left: 40px;">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
    </div>
    <div class="nav-buttons search-bar-mobile d-centered-at-mobile py-2" style="font-size: 14px;">
        @guest
            {{-- <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt=""> --}}
            <a class="py-1" href="{{ route('organizer.signin.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                    <path
                        d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0" />
                </svg>&nbsp;
                Sign in as organizer
            </a>
            <a class="py-1" href="{{ route('participant.signin.view') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                    class="bi bi-people-fill" viewBox="0 0 16 16">
                    <path
                        d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                </svg>&nbsp;
                Sign in as participant
            </a>
        @endguest
        @auth
            <div class="text-center px-2 py-1">
                <div class="d-flex align-items-center">
                    @if($user->userBanner) 
                        <img
                            class="object-fit-cover rounded-circle me-2 border border-primary" 
                            src="{{'/storage' . '/' . $user->userBanner}}" width="45" height="45">
                    @else 
                        <div
                            class="px-3 bg-dark text-light rounded-circle py-2">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <p style="text-overflow: ellipsis; overflow: hidden; font-size: larger;" class="text-start ms-3 my-0">
                        <b>{{ $user->name }}</b>
                    </p>
                </div>
            </div>
            @if ($user->role == 'PARTICIPANT' || $user->role == 'ADMIN')
                <a href="{{ url('/participant/team/create/' ) }}" class="py-1">Create a Team</a>
                <a href="{{ url('/participant/team/list/' ) }}" class="py-1">Team List</a>
                <a href="{{ route('user.notif.view' ) }}" class="py-1">Team Requests</a>
            @endif
            @if ($user->role == 'ORGANIZER' || $user->role == 'ADMIN')
                <a class="py-1" href="{{ route('event.create') }}" style="text-decoration: none;" href="{{ route('logout.action') }}">Create an event</a>
                <a class="py-1" href="{{ route('event.index') }}" style="text-decoration: none;" href="{{ route('logout.action') }}">Manage event</a>
            @endif
            <a class="py-1" style="text-decoration: none;" href="{{ route('logout.action') }}">Logout</a>
        @endauth
    </div>
    <div class="text-center cursor-pointer mb-2"
        onclick="
            let element = document.getElementById('mobile-navbar');
            console.log({element})
            if (element) element.classList.toggle('d-none');
        "
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#43A4D7" class="bi bi-x-circle d-inline" viewBox="0 0 16 16"
        >
        <path stroke-width="0.9" d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
        <path stroke-width="0.9" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
        </svg>
        <span class="ms-1 py-1 text-primary"><b>Close</b></span>
    </div>
</nav>
<script src="{{ asset('/assets/js/shared/jsUtils.js') }}"></script>

{{-- <script src="{{ Vite::asset('public/assets/js/shared/jsUtils.js') }}"></script> --}}
