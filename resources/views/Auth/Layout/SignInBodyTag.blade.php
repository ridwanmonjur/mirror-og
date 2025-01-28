<body>
    @include('googletagmanager::body')
    <main>
        <div class="wrapper py-4">

            @yield('signInbody')

        </div>


        <script src="{{ asset('/assets/js/shared/authValidity.js') }}"></script>
    </main>

</body>
