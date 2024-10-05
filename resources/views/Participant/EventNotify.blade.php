<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="{{ asset('assets/css/participant/player_event_reg.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
    <input type="hidden" id="register_route" value="{{ route('participant.register.manage', ['id' => ':id']) }}">

    <input type="hidden" id="event_view_route" value="{{ route('public.event.view', ['id' => ':id']) }}">

        {{-- Hidden urls --}}
        <a class="d-none" id="manageRosterUrl" href="{{route('participant.roster.manage', ['id' => $id, 'teamId' => $selectTeam->id, 'redirect' => 'true' ] ) }}"> </a>
        <a class="d-none" id="manageRegistrationUrl" href="{{route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $id ] ) }}"> </a>
        <div class="time-line-box mx-auto" id="timeline-box">
            <div class="swiper-container text-center">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                        <div class="timestamp" onclick="window.toastError('Cannot go back to team selection again!');"><span
                                class="cat text-primary">Select Team</span></div>
                        <div class="status__left" onclick="window.toastError('Cannot go back to team selection again!');">
                            <span><small class="bg-primary"></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2">
                        <div class="timestamp" onclick="document.getElementById('proxySubmit')?.click();">
                            <span>Manage Members</span>
                        </div>
                        <div class="status" onclick="document.getElementById('proxySubmit')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-launch">
                        <div class="timestamp" onclick="document.getElementById('manageRosterUrl')?.click();"><span
                                class="date">Manage Roster</span></div>
                        <div class="status" onclick="document.getElementById('manageRosterUrl')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                        <div class="timestamp"
                            onclick="document.getElementById('manageRegistrationUrl')?.click();">
                            <span>Manage Registration</span></div>
                        <div class="status__right"
                            onclick="document.getElementById('manageRegistrationUrl')?.click();">
                            <span><small></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb-top">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a class="text-primary" onclick="window.toastError('Cannot go back to team selection again!');">Select Team</a></li>
                    <li class="breadcrumb-item"><a onclick="document.getElementById('proxySubmit')?.click();">Manage Memebers</a></li>
                    <li class="breadcrumb-item"><a
                        onclick="document.getElementById('manageRosterUrl')?.click();">Manage Roster</a>
                    </li>
                    <li class="breadcrumb-item"><a
                            onclick="document.getElementById('manageRegistrationUrl')?.click();">Manage Registration</a></li>
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
                <form id="eventNotify" method="POST" action="{{ route('participant.memberManage.action') }}">
                    @csrf
                    <input type="hidden" value="{{$id}}" name="eventId">
                    <input type="hidden" value="{{$selectTeam->id}}" name="teamId">
                    <div class="text-center d-none">
                        <input type="submit" class="choose-payment-method" value="Done">
                    </div>
                </form>
                <div class="text-center">
                    <input onclick="goToViewEvent()" type="submit" class="choose-payment-method" value="Done">
                </div>
                <div class="text-center">
                    <button onclick="goToRegistrationScreen()" class="oceans-gaming-default-button oceans-gaming-transparent-button"> See Registration Status </button>
                </div>
                <br><br>
            </div>
        </div>
        <div class="d-flex box-width back-next">
            <span></span>
            {{-- <button onclick="goToNextScreen('step-1', 'none')" type="button"
                class="btn border-dark rounded-pill py-2 px-4"> Back </button> --}}
            <button form="eventNotify" type="submit"
                id="proxySubmit" 
                class="btn btn-primary text-light rounded-pill py-2 px-4"
            > Next > </button>
        </div>
    </main>
    <script src="{{ asset('/assets/js/participant/EventNotify.js') }}"></script>
    

</body>

</html>
