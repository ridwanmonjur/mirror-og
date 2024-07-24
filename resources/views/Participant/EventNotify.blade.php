<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_event_reg.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <div class="flexbox box-width back-next">
            <button onclick="goToNextScreen('step-1', 'none')" type="button"
                class="oceans-gaming-default-button oceans-gaming-transparent-button back-button"> Back </button>
            <button onclick="goToNextScreen('step-3', 'timeline-1')" type="button" class="oceans-gaming-default-button"
                onclick=""> Next > </button>
        </div>
        <div class="time-line-box mx-auto" id="timeline-box">
            <div class="swiper-container ps-5 text-center">
                <div class="swiper-wrapper ps-5">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                        <div class="timestamp" onclick="goToNextScreen('step-1', 'timeline-1')"><span
                                class="cat">Select Team</span></div>
                        <div class="status__left" onclick="goToNextScreen('step-1', 'timeline-1')">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2">
                        <div class="timestamp" onclick="goToNextScreen('step-5', 'timeline-2')"><span>Receive
                                Notification</span></div>
                        <div class="status" onclick="goToNextScreen('step-5', 'timeline-2')">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-launch">
                        <div class="timestamp" onclick="goToNextScreen('step-launch-1', 'timeline-launch')"><span
                                class="date">Manage Members</span></div>
                        <div class="status" onclick="goToNextScreen('step-launch-1', 'timeline-launch')">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                        <div class="timestamp"
                            onclick="goToNextScreen('step-payment', 'timeline-payment'); fillStepPaymentValues();">
                            <span>Manage Roster</span></div>
                        <div class="status__right"
                            onclick="goToNextScreen('step-payment', 'timeline-payment'); fillStepPaymentValues();">
                            <span><small></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb-top">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a onclick="goToNextScreen('step-1', 'timeline-1')">Categories</a></li>
                    <li class="breadcrumb-item"><a onclick="goToNextScreen('step-5', 'timeline-2')">Details</a></li>
                    <li class="breadcrumb-item"><a
                            onclick="goToNextScreen('step-payment', 'timeline-payment'); fillStepPaymentValues();">Payment</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            onclick="goToNextScreen('step-launch-1', 'timeline-launch')">Launch</a></li>
                </ol>
            </nav>
        </div>
        <div class="wrapper">
            <div class="first_notify">
                <header><u>All members of "{{$selectTeam->teamName}}" have been notified</u></header>
            </div>
            <br><br>
            <div class="midbar">
                <p>Registration has NOT been confirmed. Registration can only be confirmed after enough team members
                    have accepted and the entry fee has been paid.
                </p>

                <p>You can check the registration status on your <a href="{{ route('participant.register.manage', ['id' => $selectTeam->id]) }}"> <u> team </u> </a>'s page.</p>
                <a href="{{ route('participant.memberManage.action', ['teamId' => $selectTeam->id, 'id' => $id]) }}">
                    <div class="text-center">
                        <input type="submit" class="choose-payment-method" value="Done">
                    </div>
                </a>
                <div class="text-center">
                    <button onclick="goToRegistrationScreen()" class="oceans-gaming-default-button oceans-gaming-transparent-button"> See Registration Status </button>
                </div>
                <br><br>
            </div>
        </div>
        
    </main>
    <script>
        function goToRegistrationScreen() {
            window.location.href = "{{ route('participant.register.manage', ['id'=> $selectTeam->id]) }}";
        }
    </script>
    <script src="{{ asset('/assets/js/event_creation/timeline.js') }}"></script>

</body>

</html>
