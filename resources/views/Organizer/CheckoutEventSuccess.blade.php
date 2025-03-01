@include('Organizer.__Partials.CreateEventHeadTag')

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.__Navbar.NavbarGoToSearchPage')
    <main>
        <div>
            <div class="pt-5">
                @include('Organizer.__CheckoutPartials.CheckoutEventSuccess', ['event' => $event])
            </div>
        </div>
        <br><br>
    </main>
</body>
