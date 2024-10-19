<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('__CommonPartials.HeadIcon')
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common/event-status.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    <title>Driftwood</title>
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage', ['search' => true ])

    <main>
        <section class="hero">
            <img 
                style="max-width: 100%; height: 45vh;"
                onerror="this.onerror=null;this.src='/assets/images/404.png';"
                src="{{ asset('/assets/images/homepage new header.png') }}" alt="">
        </section>

        <div class="text__middle">
            <p class="head">
            @if (empty(app('request')->input('search')))  
            What's happening?
            @else
            Showing search results for '{{app('request')->input('search')}}'
            @endif
            </p>
        </div>

        <section class="featured-events scrolling-pagination">
            @include('__CommonPartials.LandingPageScroll')
        </section>
        <div class="no-more-data d-none"></div>
        <br><br>
        
        <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
        <script>
            var ENDPOINT = "{{ route('public.landing.view') }}";
            var page = 1;
            let fetchedPage = 1;
            var search = null;
            
            window.addEventListener(
                "scroll",
                (e) => {
                    var windowHeight = window.innerHeight;
                    var documentHeight = document.documentElement.scrollHeight;
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    
                    if (scrollTop + windowHeight >= documentHeight - 200) {
                        page++;
                        ENDPOINT = "{{ route('public.landing.view') }}";
                       
                        if (!search || String(search).trim() == "") {
                            search = null;
                            ENDPOINT += "?page=" + page;
                        } else {
                            ENDPOINT += "?search=" + search + "&page=" + page;
                        }
                        
                        infinteLoadMore(null, ENDPOINT);
                    }
                });
        </script>
        <script>
            function myFunction() {
                document.getElementById("myDropdown").classList.toggle("show");
            }

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
    </main>
</body>

</html>
