{{-- <div class="navbar-placeholder"> </div> --}}
<nav class="navbar px-3">
    <div class="logo">
        <a href="{{ route('landing.view') }}"> <!-- Link to the home route -->
            <img width="160px" height="60px" src="{{ asset('/assets/images/logo-default.png') }}" alt="">
        </a>
    </div>
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
            @include('CommonLayout.Navbar.NavbarGuest')
        @endguest
        @auth
            @if (isset($user) && $user->role == 'PARTICIPANT')
                @include('CommonLayout.Navbar.WhereIsMoop')
            @endif
            <img style="position: relative; top: 0px;" width="50px" height="40px"
                src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            {{-- <img style="position: relative; top: 0px;" width="70px" height="40px"
                src="{{ asset('/assets/images/navbar-crown.png') }}" alt=""> --}}
            <a class="" style="text-decoration: none;position: relative; top: 10px;" href="{{ route('logout.action') }}">Logout</a>
        @endauth
    </div>
</nav>
<nav class="mobile-navbar d-centered-at-mobile d-none">
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
    <div class="nav-buttons search-bar-mobile d-centered-at-mobile">
        @guest
            <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            <a class="" href="{{ route('organizer.signin.view') }}">
             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                <path
                    d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0" />
            </svg>&nbsp;
            Sign in as organizer</a>
            <a class="" href="{{ route('participant.signin.view') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                class="bi bi-people-fill" viewBox="0 0 16 16">
                <path
                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
            </svg>&nbsp;
            Sign in as participant</a>
        @endguest
        @auth
            @if (isset($user) && $user->role == 'PARTICIPANT')
                @include('CommonLayout.Navbar.WhereIsMoop')
            @endif
            <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            {{-- <img width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt=""> --}}
            <a class="" style="text-decoration: none;" href="{{ route('logout.action') }}">Logout</a>
        @endauth
    </div>
</nav>
<script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
<script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
@if (isset($search))
    @include('CommonLayout.Navbar.NavbarShowSearchResultsScript')
@else
    @include('CommonLayout.Navbar.NavbarGoToSearchPageScript')
@endif
<br>
<br>
