@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
@php
    if (isset($user)) {
        $role = $user->role;
        if ($role == 'PARTICIPANT') {
            $routeLogo = route('landing.view');
        } else if ($role == 'ORGANIZER') {
            $routeLogo = route('organizer.home.view');
        }
        else {
            $routeLogo = route('landing.view');
        }
    } else {
        $routeLogo = route('landing.view');
    }
@endphp
<nav class="navbar px-3">
    <a href="{{ $routeLogo }}">
        <div class="logo">
            <img width="160px" height="60px" src="{{ asset('/assets/images/logo-default.png') }}" alt="">
        </div>
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
            placeholder="Search for events">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-search">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
    </div>
    <div class="nav-buttons">
        @guest
            @include('__CommonPartials.__Navbar.NavbarGuest')
        @endguest
        @auth
            @if (isset($user) && $user->role == 'PARTICIPANT')
                @include('__CommonPartials.__Navbar.WhereIsMoop')
            @endif
            @include('__CommonPartials.__Navbar.NavbarAuth')
        @endauth
    </div>
</nav>
<nav class="mobile-navbar d-centered-at-mobile d-none" id="mobile-navbar">
    <div class="search-bar search-bar-mobile ">
        <input
            type="text" name="search" id="search-bar-mobile"
            value="{{app('request')->input('search')}}"
            placeholder="Search for events">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
            stroke-linejoin="round" class="feather feather-search" style="left: 40px;">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
    </div>
    <div class="nav-buttons search-bar-mobile d-centered-at-mobile py-2">
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
                <a href="{{ url('/participant/request/' ) }}" class="py-1">Team Requests</a>
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
<script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
@if (isset($search))
    @include('__CommonPartials.__Navbar.NavbarShowSearchResultsScript')
@else
    @include('__CommonPartials.__Navbar.NavbarGoToSearchPageScript')
@endif
@auth
<script>
    function setAllNotificationsRead() {
        event.preventDefault();
        event.stopPropagation();
        var notificationId = event.target.dataset.notificationId;
        console.log(notificationId);
        var divElements = document.querySelectorAll('div.notification-container');
        let url = "{{route('user.notifications.readAll')}}";
        console.log({url})
         fetch(url, {
            method: 'put',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                ...window.loadBearerCompleteHeader()
            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response.success) {
                    divElements.forEach(function(div) {
                        div.classList.remove('notification-container-not-read');
                    });
                }
            })
            .catch(function (error) {
                console.log('Server error occured');
                throw new Error('Error occurred');
            });
    }

    function setNotificationReadById(event, notificationId, loopCount) {
        event.preventDefault();
        event.stopPropagation();

        var divElement = document.querySelector('div.notification-container[data-loop-count="' + loopCount + '"]');
        var element = divElement.querySelector('a.mark-read');
        console.log({element, notificationId, loopCount})
        let url = "{{ route('user.notifications.read', ['id' => ':id']) }}"
            .replace(':id', notificationId);
         fetch(url, {
            method: 'put',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-type': 'application/json',            },
        })
            .then((response) => response.json())
            .then((response) => {
                if (response.success) {
                    const notificationCountElement = document.getElementById('countUnread');
                    const notificationCount = notificationCountElement.dataset.notificationCount;
                    const decreasedCount = parseInt(notificationCount, 0) - 1;
                    if (decreasedCount >= 0) {
                        notificationCountElement.dataset.notificationCount = decreasedCount;
                        notificationCountElement.textContent = decreasedCount;
                    }
                    divElement.classList.remove('notification-container-not-read');
                    
                    element.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425z"/>
                        </svg>
                        <span> Read </span>
                    `;
                }
            })
            .catch(function (error) {

                console.error(error);
                throw new Error('Error occurred');
            });
    }
</script>
@endauth