<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/pie-chart.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
    <script src="{{ asset('assets/js/participant/RegistrationManagement.js') }}"></script>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>
<body
    @style(["min-height: 100vh;" => $isRedirect])
>
    @include('googletagmanager::body')

    @include('__CommonPartials.NavbarGoToSearchPage')
    <main 
        @style(["height: 95vh" => $isRedirect])
     class="main2">

    @if ($isRedirect)
        <form method="POST" action="{{ route('participant.memberManage.action') }}">
            @csrf
            <input type="hidden" value="{{$eventId}}" name="eventId">
            <input type="hidden" value="{{$selectTeam->id}}" name="teamId">
            <input id="manageMemberButton" type="submit" class="d-none" value="Done">
            </div>
        </form>
        <a class="d-none" id="manageRosterUrl" href="{{route('participant.roster.manage', ['id' => $eventId, 'teamId' => $selectTeam->id, 'redirect' => 'true' ] ) }}"> </a>
        <a class="d-none" id="manageRegistrationUrl" href="{{route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $eventId ] ) }}"> </a>
        <a class="d-none" id="eventUrl" href="{{route('participant.event.view', ['id' => $eventId ] ) }}"> </a>

        <div class="time-line-box mx-auto" id="timeline-box">
            <div class="swiper-container text-center">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                        <div class="timestamp" onclick="window.toastError('Cannot go back to \'Select Team\'.');"><span
                                class="cat">Select Team</span></div>
                        <div class="status__left" onclick="window.toastError('Cannot go back to \'Select Team\'.');">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2">
                        <div class="timestamp" onclick="document.getElementById('manageMemberButton')?.click();"><span >Manage Members</span></div>
                        <div class="status" onclick="document.getElementById('manageMemberButton')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-launch">
                        <div class="timestamp" onclick="document.getElementById('manageRosterUrl')?.click();"><span
                                class="date">Manage Roster</span></div>
                        <div class="status" onclick="document.getElementById('manageRosterUrl')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                        <div class="timestamp text-primary"
                            onclick="document.getElementById('manageRegistrationUrl').click();">
                            <span>Manage Registration</span></div>
                        <div class="status__right"
                            onclick="document.getElementById('manageRegistrationUrl').click();">
                            <span><small class="bg-primary"></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb-top">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a onclick="window.toastError('Cannot go back to \'Select Team\'.');">Select Team</a></li>
                    <li class="breadcrumb-item"><a onclick="document.getElementById('manageMemberButton')?.click();">Manage Members</a></li>
                    <li class="breadcrumb-item"><a 
                            onclick="document.getElementById('manageRosterUrl').click();">Manage Roster</a>
                    </li>
                    <li class="breadcrumb-item"><a class="text-primary" 
                            onclick="document.getElementById('manageRegistrationUrl').click();">Manage Registration</a></li>
                </ol>
            </nav>
        </div>
    @else
        @include('Participant.__Partials.TeamHead') 
    @endif
        <div id="Overview">
            @if (!$isRedirect)
                <br>
                @if (session('successMessage'))
                    <div class="text-success text-center">{{session('successMessage')}}</div><br>
                @elseif (session('errorMessage'))
                    <div class="text-red text-center">{{session('errorMessage')}}</div><br>
                @else
                    <br>
                @endif
                <div class="tab-size"><b>Outstanding Registrations</b></div><br> <br>
            @endif
            
            @if (!isset($joinEvents[0]))
                <p class="tab-size text-start mx-auto ">No events available</p>
            @else
            <div @class(["event-carousel-styles" => !$isRedirect, "mx-5 px-5"])>
                @foreach ($joinEvents as $joinEvent)
                    @if ($isRedirect) 
                        <div class="text-center">
                            <h5><u>Event Registration</u></h5>
                            <p>You can pay now, or complete payment later...</p>
                        </div>
                    @else  
                        @include('Participant.__Partials.RosterView', ['isRegistrationView' => false])
                    @endif
                    @include('Participant.__Partials.PieChart', ['isInvited' => false])
                @endforeach
            </div>

            @endif
        </div>
        @if (!$isRedirect)
            <div id="Invitation">
                <br><br>
                <div class="tab-size"><b>Event Invitations</b></div>
                <br> <br>
                <div class="position-relative d-flex justify-content-center">
                    @if (!isset($invitedEvents[0]))
                        <p class="tab-size text-start mx-auto">No events available</p>
                    @else
                        <div class="event-carousel-styles mx-5 px-5">
                            @foreach ($invitedEvents as $key => $joinEvent)
                                @include('Participant.__Partials.RosterView', ['isRegistrationView' => false])
                                @include('Participant.__Partials.PieChart', ['isInvited' => true])
                            @endforeach
                        </div>
                    @endif
                <br> <br>
            </div>
        @endif
        @if ($isRedirect)
            <div class="d-flex box-width back-next mb-5">
                <button type="button"
                    class="btn border-dark rounded-pill py-2 px-4" onclick="document.getElementById('manageRosterUrl')?.click();"> Back </button>
                <button type="button" 
                    class="btn btn-success text-dark rounded-pill py-2 px-4"
                    onclick="document.getElementById('eventUrl')?.click();">  View Event  </button>
            </div>
        @else 
            <br><br><br><br><br><br>
        @endif
    </main>
    
   
</body>
