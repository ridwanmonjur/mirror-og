@include('Organizer.includes.CreateEventHeadTag')

<body>
    @include('googletagmanager::body')
    @include('includes.__Navbar.NavbarGoToSearchPage')
    <main>
        <div>
            <div class="pt-5">
                @include('Organizer.__CheckoutPartials.CheckoutEventSuccess', ['event' => $event])
            </div>
        </div>
        <br><br>
    </main>
</body>
