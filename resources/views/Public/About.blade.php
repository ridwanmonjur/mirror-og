@extends('layout.app')

@section('title', 'Driftwood - About Us')
@section('body-class', 'about')
@push('styles')
    <link 
        rel="preload" 
        href="https://fonts.gstatic.com/s/inter/v13/UcC73FwrK3iLTeHuS_fvQtMwCp50KnMa1ZL7.woff2" 
        as="font" 
        type="font/woff2" 
        crossorigin
    >
@endpush
@section('content')
    <header>
        @auth
            @include('__CommonPartials.__Navbar.NavbarGoToSearchPage')
        @endauth
        @guest
            @include('__CommonPartials.__Navbar.NavbarBeta')
        @endguest
    </header>
    <main style="padding: 5vh 10vw ;">
        {{-- Para 1 --}}
        <section class="mb-5">
            <h2 class="display-4 fw-light text-white mb-4">What is Driftwood about?</h2>
            <p class="text-white mb-3">Driftwood is about bringing people who love esports closer together.</p>
            <p class="text-white mb-3 text-justify">To us, esports is something that can form bonds between friends, family, and
                community. We want to share our passion for esports with those around us, and we want to enable you to share
                your passion with those around you.</p>
        </section>
        {{-- Para 2 --}}
        <section>
            <h2 class="display-4 px-0 fw-light text-white mb-4">How did Driftwood begin?</h2>
            <p class="text-white px-0 mb-3 text-justify">Our team grew up playing and loving competitive games. Late nights at LAN cafes after
                school, laughing and shouting with friends, getting excited at small victories, and feeling defeated after
                clean sweeps.</p>
            <p class="text-white px-0 mb-3 text-justify">It's been years since we've been in the competitive sphere. Yet, the struggles faced
                by new and amateur players today are the same as what we faced over a decade ago. Passionate players start
                clubs and run tournaments, creating vibrant spaces and communities, but many of these grassroots efforts
                aren't able to connect with the people and businesses outside of their spaces. One by one as founders grow
                older, people move on, and efforts borne from passion are left to fall into disrepair.</p>
            <p class="text-white px-0 mb-3">So we thought, if there hasn't been change, then let's create change.</p>
            <p class="text-white px-0 mb-3 text-justify">That is how Driftwood came to be. We want a place for esports fans to come together
                and connect with people both inside and outside of the gaming space, and to create a lasting ecosystem
                around the games we love.</p>
        </section>
    </main>
@endsection

