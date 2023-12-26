<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}"">
    <link rel=" stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Splash</title>
    <script src="{{ asset('script.js') }}"></script>
</head>

<body>
    {{-- @include('CommonLayout.Navbar2') --}}
    <nav>
        <div class="nav__sect1">
                <img class="logo" src="{{ asset('/assets/images/logo2.png') }}" alt="">
                <div class="search_box">
                    <i id="search-icon" class="fa fa-search"></i>
                    <input id="search-bar" class="nav__input" type="text" placeholder="Search for events">
                </div>
            </div>

        <div class="hamburger-menu">
            <i class="fa fa-bars"></i>
        </div>
        @guest
            <div class="nav__items">
                <ul>
                    <li><a href="#" id='nav-but' class="moop">Where's Moop?</a></li>
                    <div class="dropdown">
                        <li><a onclick="myFunction()" style="color: white !important;" id='nav-but' class="dropbtn sign"
                                data-bs-toggle="dropdown" aria-expanded="false">Sign In</a></li>
                    </div>
                    <div id="myDropdown" class="dropdown-content">
                        <a class="dropdown-item" href="{{ route('organizer.signin.view') }}">Organizer</a>
                        <a class="dropdown-item" href="{{ route('participant.signin.view') }}">Participant</a>
                    </div>

                </ul>
            </div>
        @endguest
        @auth
            <div class="nav__items">
                <ul>
                    <li><a href="#" id='nav-but' class="moop">Where's Moop?</a></li>
                    <li>
                        <img style="position: relative; top: 0px; cursor: pointer;" width="50px" height="40px"
                            src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
                    </li>
                    <li>
                        <img style="position: relative; top: 0px; left: -20px; cursor: pointer;" width="70px"
                            height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
                    </li>
                    <li>
                        <a style="position: relative; top: 0px; left: -30px; cursor: pointer;"
                            href="{{ route('logout.action') }}" class="moop">Sign Out</a>
                    </li>
                </ul>
            </div>
        @endauth
    </nav> 

    <section class="hero">
        <img src="{{ asset('/assets/images/ss.png') }}" alt="">
    </section>

    <div class="text__middle">
        <p class="head">What's happening?</p>
    </div>

    <section class="featured-events scrolling-pagination">
        @include('LandingPageScroll')
    </section>

    <div class="no-more-data d-none"></div>
    <br><br>
    @include('CommonLayout.BootstrapJs')
    <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
    <script>
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
            "change",
            throttle((e) => {
                searchPart(e);
            }, 300)
        );

        function eventRespond(e) {
            e.preventDefault();
            searchPart(e);
        }

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
            } else {
                ENDPOINT += "?search=" + e.target.value + "&page=" + page;
            }
            infinteLoadMore(null, ENDPOINT);
        }
    </script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    <script>
        function myFunction() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>

</html>
