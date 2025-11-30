<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="analytics" content="enabled">
    @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common/game-sidebar.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <title>Driftwood</title>
    <meta name="description" content="Join Driftwood - the premier community esports platform. Play competitive games, meet like-minded players, and build your esports community. Join our closed beta today!">
    <meta name="keywords" content="community esports, esports platform, competitive gaming, esports community, amateur esports, esports tournament, gaming community, closed beta, play meet chill">

    <!-- Open Graph tags for social sharing -->
    <meta property="og:title" content="Driftwood - The Best Place for Community Esports">
    <meta property="og:description" content="Join Driftwood - the premier community esports platform. Play competitive games, meet like-minded players, and build your esports community.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://driftwood.gg/">
    <meta property="og:image" content="https://driftwood.gg/images/assets/images/dw_logo.webp">

    <!-- Twitter Card tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Driftwood - Community Esports Platform">
    <meta name="twitter:description" content="Play competitive games, meet players, and build your esports community. Join our closed beta!">
    <meta property="twitter:image" content="https://driftwood.gg/images/assets/images/dw_logo.webp">

    <link rel="alternate" type="application/atom+xml" title="Latest Esports Events" href="{{ route('feeds.events') }}" />

     <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Organization",
                "name": "Driftwood",
                "url": "https://driftwood.gg",
                "logo": "https://driftwood.gg/assets/images/dw_logo.webp",
                "description": "Community esports platform where players compete, meet, and build communities around competitive gaming.",
                "sameAs": [
                    "https://twitter.com/DriftwoodEsports",
                    "https://facebook.com/DriftwoodEsports",
                    "https://discord.gg/driftwood"
                ]
            }
        </script>

        <!-- Structured Data for WebApplication -->
        <script type="application/ld+json">
            {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "Driftwood",
            "url": "https://driftwood.gg",
            "applicationCategory": "GameApplication",
            "operatingSystem": "Web",
            "offers": {
                "@type": "Offer",
                "price": "600 RM - 6000 RM",
                "priceCurrency": "RM",
                "description": "Closed beta access"
                }
            }
        </script>
</head>

<body class="has-game-sidebar">
    @include('googletagmanager::body')
    @include('includes.GameSidebar')
    @include('includes.Navbar', ['search' => true ])
    <main>

        <div class="scroll-indicator"></div>

        <input type="hidden" id="endpoint_route" value="{{ route('public.landing.view') }}">
        
        <section class="hero user-select-none d-none d-lg-block">
            <img 
                loading="lazy"  alt="Driftwood Esports Event"
                onerror="this.onerror=null;this.src='/assets/images/404q.png';"
                src="{{ asset('/assets/images/homepage new header.png') }}" alt=""
            >
        </section>

        <div class="d-block text-center d-lg-none">
            <br><br>
        </div>

        <div 
            class=" d-none  right-shift d-lg-flex text-center mx-auto justify-content-between align-items-center px-2 flex-wrap align-items-center"
        >
            <h3 class="px-5 mb-0 text-dark">
            @if (empty(app('request')->input('search')))
             We've got events for you...
            @else
            Showing search results for '{{app('request')->input('search')}}'
            @endif
            </h3>
            <a href="{{ route('shop.index') }}" class="text-light rounded-3 btn-primary py-1 px-2 ">
                <h5 class="py-0 my-0">
                    Go to shop
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                    </svg>
                </h5>
                
            </a>
        </div>

        <section 
            class="featured-events right-shift scrolling-pagination mt-3  mx-auto"
        >
            @include('includes.Landing')
        </section>
        @if (isset($events[0]))
            <div class="no-more-data d-none"></div>
        @else
            <div class="container d-flex justify-content-center align-items-center">
                <div class="no-more-data text-center mx-auto">
                    <div class=" d-flex flex-column justify-content-center align-items-center rounded-box-dolphin event shadow-sm" style="max-width: 400px; background-color: rgba(255, 255, 255, 0.7);">
                            <div >
                                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="mx-auto d-block text-muted">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z" fill="currentColor"/>
                                </svg>
                            </div>
                            <h5 class="card-title text-muted mb-2">No Events Available</h5>
                            <p class="card-text text-muted small mb-0">We don't have any events to display</p>
                        </div>
                </div>
            </div>
        @endif
        <br><br>
        
        
        <script type="module" src="{{ asset('/assets/js/shared/Landing.js') }}"></script>
    @include('includes.Footer')

    </main>

</body>

</html>
