@include('Organizer.Layout.CreateEventHeadTag')
<body style="margin-top: 0 !important;">
@include('CommonLayout.Navbar')

    <main>
        <br><br>
        <div class="text-center" >
            <div >
                <u>
                    <h3 id="heading">Error occurred!</h3>
                </u>
            </div>
            <div class="box-width">
                @if (isset($error))
                <p id="notification">{{ $error }}</p>
                @else
                @if (isset($request->id))
                <p id="notification">Event can't be found by id= {{ $request->id }}.</p>
                @else
                <p id="notification">Event can't be found.</p>
                @endif
                @endif
            </div>

            <br><br><br><br>

            <button onclick="goToManageScreen();" class="oceans-gaming-default-button" style="padding: 10px 50px; background-color: white; color: black; border: 1px solid black;">
                Go to event page
            </button>

        </div>
        <script>
            const goToManageScreen = () => {
                window.location.href = "{{route('event.index') }}";
            }
        </script>
        @include('CommonLayout.BootstrapJs')

    </main>
</body>
