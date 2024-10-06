@include('Organizer.__Partials.CreateEventHeadTag')
<body style="margin-top: 0 !important;">
@include('__CommonPartials.NavbarGoToSearchPage')

    <main>
        <input type="hidden" id="manage_event_route" value="{{ route('event.index') }}">
        <input type="hidden" id="edit_event_route" value="{{ route('event.edit', ['id' => $id]) }}">

        <br><br><br><br>
        <div class="text-center" >
            <div >
                <u>
                    <h3 id="heading">Error occurred!</h3>
                </u>
            </div>
            <div class="box-width">
                <p id="notification">{{ $error }}</p>
            </div>
            <br><br><br><br>
            <button onclick="goToManageScreen();" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: black; border: 1px solid black;">
                Go to event page
            </button>
            @if (isset($id) && isset($edit) && $edit )
                <br><br>
                <button onclick="goToEditScreen();" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: black; border: 1px solid black;">
                    Edit event
                </button>
            @endif
        </div>
        <script src="{{ asset('/assets/js/organizer/EventNotFound.js') }}"></script>


    </main>
</body>
