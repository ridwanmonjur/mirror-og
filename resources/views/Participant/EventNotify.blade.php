<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_event_reg.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
</head>

<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    <main>
        <div class="wrapper_notify">
            @if (isset($redirect) && $redirect)
                <div class="mb-2 text-success mx-auto text-center">
                    You have joined this event successfully!
                    @if ($selectTeam->creator_id == $user->id)
                        <form class="d-inline" method="GET"
                            action="{{ route('participant.roster.manage', ['id' => $id, 'teamId' => $selectTeam->id]) }}">
                            <button class="oceans-gaming-default-button oceans-gaming-default-button-link ms-2 me-2" type="submit"
                                style="display: inline !important;">
                                <u> Manage Roster </u>
                            </button>
                        </form>
                    @endif
                </div>
            @endif
            <div class="first_notify">
                <header><u>All members of {{$selectTeam->teamName}} have been notified</u></header>
            </div>
            <br>
            <br>

            <div class="midbar">
                <p>Registration has NOT been confirmed. Registration can only be confirmed after enough team members
                    have accepted and the entry fee has been paid.
                </p>

                <p>You can check the registration status on your <a href=""> <u> team's </u> </a> page.</p>

                <form action="{{ route('participant.notify.action', ['id' => $id, 'teamId' => $selectTeam->id]) }}" method="POST">
                <div class="text-center">
                    <input type="submit" class="choose-payment-method" value="Done">
                </div>
                </form>

                <div class="text-center">
                    <button onclick="goToRegistrationScreen()" class="oceans-gaming-another-button"> See Registration Status </button>
                </div>

            </div>
        </div>
        @include('CommonLayout.BootstrapV5Js')
    </main>
    <script>
        function goToRegistrationScreen() {
            window.location.href = "{{ route('participant.register.manage', ['id'=> $selectTeam->id]) }}";
        }
    </script>
</body>

</html>
