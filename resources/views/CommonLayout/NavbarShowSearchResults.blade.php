{{-- <div class="navbar-placeholder"> </div> --}}
<nav class="navbar">
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
            <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
            <div class="dropdown" data-reference="parent" data-offset="-20,-20">
                <a href="#" role="button" class="btn dropdown-toggle" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="true">Sign In</a>
                <div id="" class="dropdown-menu" style="position: absolute; left: -60px;" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="{{ route('organizer.signin.view') }}">Organizer</a>
                    <a class="dropdown-item" href="{{ route('participant.signin.view') }}">Participant</a>
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
            <a class="" href="{{ route('organizer.signin.view') }}">Sign in as Organizer</a>
            <a class="" href="{{ route('participant.signin.view') }}">Sign in as Participant</a>
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
            if (scrollTop + windowHeight >= documentHeight - 20) {
                page++;
                ENDPOINT = "{{ route('landing.view') }}";
                console.log({
                    search
                })
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