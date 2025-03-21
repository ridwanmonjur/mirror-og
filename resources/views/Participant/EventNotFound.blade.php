@include('Organizer.includes.CreateEventHeadTag')
<body style="margin-top: 0 !important;">
@include('__CommonPartials.__Navbar.NavbarGoToSearchPage')

    <main>
        <br><br><br><br>
        <div class="text-center" >
            <div >
                <u>
                    <h3 id="heading">Error occurred!</h3>
                </u>
            </div>
            <div class="box-width">
                @if (isset($error))
                <p id="notification">{{ $error }}</p>
                @endif
            </div>
            <br><br><br><br>
            <a href="{{ route('public.landing.view') }}" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: #2e4b59; border: 1px solid black; text-decoration: none; display: inline-block;">
                Go to home page
            </a>

        </div>
        

    </main>
</body>
