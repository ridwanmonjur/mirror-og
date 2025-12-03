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
            $routeLogo = route('public.landing.view');
        } else if ($role == 'ORGANIZER') {
            $routeLogo = route('organizer.home.view');
        }
        else {
            $routeLogo = route('public.landing.view');
        }
    } else {
        $routeLogo = route('public.landing.view');
    }
@endphp
<nav class="navbar px-3 ">
    <a href="{{ route('public.closedBeta.view') }}">
        <img width="192" height="30" src="{{ asset('/assets/images/DW_LOGO.png') }}" alt="">
    </a>
    <svg style="margin-top: 10px; margin-right: 10px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" class="feather feather-menu menu-toggle d-block d-xl-none" onclick="toggleNavbar()">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
    
   @php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="nav-buttons">
    <li class="nav-item" style="list-style-type: none;">
        <a class="nav-link {{ $currentRoute === 'public.closedBeta.view' ? 'text-primary' : 'text-dark' }} me-5 px-4 fw-semibold text-center" 
           href="{{ route('public.closedBeta.view') }}">
            CLOSED BETA
        </a>
    </li>
    <li class="nav-item" style="list-style-type: none;">
        <a class="nav-link {{ $currentRoute === 'public.about.view' ? 'text-primary' : 'text-dark' }} me-5 px-4 text-center" 
           href="{{ route('public.about.view') }}">
            ABOUT
        </a>
    </li>
    <li class="nav-item" style="list-style-type: none;">
        <a class="nav-link {{ $currentRoute === 'public.contact.view' ? 'text-primary' : 'text-dark' }} me-5 ps-5 pe-0 text-center" 
           href="{{ route('public.contact.view') }}">
            CONTACT
        </a>
    </li>
</div>
</nav>
<nav class="mobile-navbar d-centered-at-mobile d-none py-3" id="mobile-navbar">
     <a href="{{ $routeLogo }}">
        <img width="192" height="30" src="{{ asset('/assets/images/DW_LOGO.png') }}" alt="">
    </a>
    <div class="nav-buttons search-bar-mobile d-centered-at-mobile py-2">
         <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-center  fw-semibold" href="{{ route('public.closedBeta.view') }}">CLOSED BETA</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-center text-dark" href="{{ route('public.about.view') }}">ABOUT</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-center text-dark" href="{{ route('public.contact.view') }}">CONTACT</a>
            </li>
            <li class="nav-item mt-2 mx-auto"
                onclick="
                    let element = document.getElementById('mobile-navbar');
                    if (element) element.classList.toggle('d-none');
                "
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#43A4D7" class="bi bi-x-circle d-inline" viewBox="0 0 16 16"
                >
                <path stroke-width="0.9" d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                <path stroke-width="0.9" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                </svg>
                <span class=" py-1 px-0 text-primary"><b>Close</b></span>
            </li>
        </ul>
    </div>
</nav>
<script src="{{ asset('/assets/js/shared/jsUtils.js') }}"></script>

{{-- <script src="{{ Vite::asset('public/assets/js/shared/jsUtils.js') }}"></script> --}}