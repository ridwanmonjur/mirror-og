<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('__CommonPartials.HeadIcon')
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
    <link rel=" stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Splash</title>

</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage', ['search' => true ])

    <main>
        <section class="hero">
            <img 
                onerror="this.onerror=null;this.src='/assets/images/404.png';"
                src="{{ asset('/assets/images/ss.png') }}" alt="">
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
        
        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
        <script src="{{ asset('/assets/js/pagination/loadMore.js') }}"></script>
        <script>
            var ENDPOINT = "{{ route('landing.view') }}";
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
                        ENDPOINT = "{{ route('landing.view') }}";
                       
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
