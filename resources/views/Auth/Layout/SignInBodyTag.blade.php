<body style="overflow-y: auto !important;">
    @include('googletagmanager::body')

    <!-- Center waves -->
    <div class="wave-center-container">
        <svg class="wave wave-center" viewBox="0 0 1200 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,50 C150,70 250,20 400,50 C550,80 700,15 850,50 C1000,75 1100,25 1200,50" fill="none" stroke="white" stroke-width="8" opacity="0.4"/>
        </svg>
        <svg class="wave wave-center-2" viewBox="0 0 1200 250" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
            <path d="M0,125 C150,200 300,15 450,125 C600,220 750,10 900,125 C1050,210 1150,40 1200,125" fill="none" stroke="white" stroke-width="8" opacity="0.6"/>
        </svg>
    </div>

    <main>
        <div class="wrapper py-4">

            @yield('signInbody')

        </div>


        <script src="{{ asset('/assets/js/shared/authValidity.js') }}"></script>
    </main>

</body>
