{{-- <div class="navbar-placeholder"> </div> --}}
<nav class="navbar px-3">
    <div class="logo">
        <img width="160px" height="60px" src="{{ asset('/assets/images/logo-default.png') }}" alt="">
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
            {{-- <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            <div class="dropdown" data-reference="parent" data-bs-offset="-80,-80">
                <a href="#" role="button" class="btn dropdown-toggle" id="dropdownMenuLink" data-bs-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">Sign In</a>
                <div id="" class="dropdown-menu" style="position: absolute; left: -90px;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="{{ route('organizer.signin.view') }}">Organizer</a>
                    <a class="dropdown-item" href="{{ route('participant.signin.view') }}">Participant</a>
                </div>
            </div> --}}

<div class="dropdown" data-reference="parent" data-bs-offset="-80,-80">

    <a href="#" role="button" class="btn dropdown-toggle" id="dropdownMenuLink" data-bs-toggle="dropdown"
        aria-haspopup="true" aria-expanded="true">
        <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
        Sign In
    </a>
    <div id="" class="dropdown-menu" style="position: absolute; left: -10px;" aria-labelledby="dropdownMenuLink">
        <div style="background-color: #98ddeb; width: auto; padding: 10px; text-align: center;">
            <div style="display: flex; align-items: center;">
                <p style="margin: 0;">Hi there!</p>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smile" style="margin-left: 5px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                    <line x1="9" y1="9" x2="9.01" y2="9"></line>
                    <line x1="15" y1="9" x2="15.01" y2="9"></line>
                </svg>
            </div>
        </div>
        <a class="dropdown-item" href="{{ route('organizer.signin.view') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill-gear" viewBox="0 0 16 16">
                <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0"/>
            </svg>&nbsp;
            Organizer
        </a>
        <a class="dropdown-item" href="{{ route('participant.signin.view') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
            </svg>&nbsp;
            Participant
        </a>
    </div>
</div>
        @endguest
        @auth
            <button class="oceans-gaming-default-button oceans-gaming-gray-button"> Where is moop? </button>
            <img style="position: relative; top: 0px;" width="50px" height="40px"
                src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            <img style="position: relative; top: 0px;" width="70px" height="40px"
                src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
            <a class="" style="text-decoration: none;position: relative; top: 10px;" href="{{ route('logout.action') }}">Logout</a>
        @endauth
    </div>
</nav>
<nav class="mobile-navbar d-centered-at-mobile d-none">
    <div class="search-bar search-bar-mobile ">
        <input type="text" name="search" id="search-bar-mobile"
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
            <a class="" href="{{ route('organizer.signin.view') }}">Sign in as organizer</a>
            <a class="" href="{{ route('participant.signin.view') }}">Sign in as participant</a>
        @endguest
        @auth
            <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            <img width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
            <a class="" style="text-decoration: none;" href="{{ route('logout.action') }}">Logout</a>
        @endauth
    </div>
</nav>
<script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
<script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
<script>
    function toggleDropdown() {
        document.querySelector("#myDropdown").classList.toggle("d-none")
    }

    var ENDPOINT = "{{ route('landing.view') }}";
    var page = 1;
    var search = null;
    window.addEventListener(
        "scroll",
        throttle((e) => {
            var windowHeight = window.innerHeight;
            var documentHeight = document.documentElement.scrollHeight;
            var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop + windowHeight >= documentHeight - 200) {
                page++;
                ENDPOINT = "{{ route('landing.view') }}";

                if (!search || String(search).trim() == "") {
                    search = null;
                    ENDPOINT += "?page=" + page;
                } else {
                    ENDPOINT += "?search=" + search + "&page=" + page;
                }
                infinteLoadMore(null, ENDPOINT);
            }
        }, 300)
    );
    document.getElementById('search-bar').addEventListener(
        "keydown",
        debounce((e) => {
            searchPart(e);
        }, 1000)
    );

      document.getElementById('search-bar-mobile').addEventListener(
        "keydown",
        debounce((e) => {
            searchPart(e);
        }, 1000)
    );

    function searchPart(e) {
        page = 1;
        let noMoreDataElement = document.querySelector('.no-more-data');
        noMoreDataElement.classList.add('d-none');
        document.querySelector('.scrolling-pagination').innerHTML = '';
        search = e.target.value;
        ENDPOINT = "{{ route('landing.view') }}";
        if (!search || String(search).trim() == "") {
            search = null;
            ENDPOINT += "?page=" + page;
            infinteLoadMore(null, ENDPOINT);
        } else {
            ENDPOINT = "{{ route('landing.view') }}";
            ENDPOINT += "?search=" + e.target.value + "&page=" + page;
            window.location.href = ENDPOINT;
        }
    }
</script>
