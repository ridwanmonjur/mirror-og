<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_event_reg.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <input type="hidden" id="register_route"
        value="{{ route('participant.register.manage', ['id' => $selectTeam->id]) }}">
    <input type="hidden" id="event_view_route" value="{{ route('public.event.view', ['id' => $id]) }}">

    {{-- Hidden urls --}}
    
    <a class="d-none" id="manageRegistrationUrl"
        href="{{ route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $id]) }}"> </a>
    <main class="d-flex justify-content-center flex-row">
        <div class="wrapper-height ">
            <div class="wrapper">
                <div class="first_notify">
                    <header><u>All members of "{{ $selectTeam->teamName }}" have been notified</u></header>
                </div>
                <br><br>
                <div class="midbar">
                    <p>Registration has NOT been confirmed. Registration can only be confirmed after enough team members
                        have accepted and the entry fee has been paid.
                    </p>

                    <p>You can check the registration status on your <a
                            href="{{ route('participant.register.manage', ['id' => $selectTeam->id]) }}"> <u> team </u>
                        </a>'s page.</p>
                    <form id="eventNotify" method="POST" action="{{ route('participant.memberManage.action') }}">
                        @csrf
                        <input type="hidden" value="{{ $id }}" name="eventId">
                        <input type="hidden" value="{{ $selectTeam->id }}" name="teamId">
                        <div class="text-center d-none">
                            <input type="submit" class="choose-payment-method" value="Done">
                        </div>
                    </form>
                    <div class="text-center">
                        <input onclick="goToViewEvent()" type="submit" class="choose-payment-method" value="Done">
                    </div>
                    <div class="text-center">
                        <button onclick="goToRegistrationScreen()"
                            class="oceans-gaming-default-button oceans-gaming-transparent-button"> See Registration
                            Status </button>
                    </div>
                    <br><br>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('/assets/js/participant/EventNotify.js') }}"></script>


</body>

</html>
