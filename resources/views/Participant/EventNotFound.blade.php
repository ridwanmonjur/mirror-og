@include('Organizer.__Partials.CreateEventHeadTag')
<body style="margin-top: 0 !important;">
@include('__CommonPartials.NavbarGoToSearchPage')

    <main>
        <input type="hidden" id="home_view_route" value="{{ route('participant.home.view') }}">
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
            <button onclick="goToHomeScreen();" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: black; border: 1px solid black;">
                Go to home page
            </button>

        </div>
        <script src="{{ asset('/assets/js/participant/EventNotFound.js') }}"></script>
        

    </main>
</body>
