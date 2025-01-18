<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/registerManage.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
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
     class="main2"
     >
    <div id="blade-data" style="display: none;"
        
        data-approve-url="{{ route('participant.roster.approve') }}"
        data-disapprove-url="{{ route('participant.roster.disapprove') }}"
        data-user-id="{{ $user->id }}"
        data-rostercaptain-url="{{ route('participant.roster.captain') }}"
        data-team-id="{{ $selectTeam->id }}"
        data-max-roster-size="{{ $maxRosterSize }}"
        data-payment-lower="{{$paymentLowerMin}}"
        data-vote-url="{{ route('participant.roster.vote') }}"
        data-register-url="{{ 
            $isRedirect 
            ? route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $joinEvents[0]->eventDetails?->id])
            : route('participant.register.manage', ['id' => $selectTeam->id])
        }}"
        data-success-message="{{ session('successMessage') }}"
        data-error-message="{{ session('errorMessage') }}"
        data-scroll="{{session('scroll')}}"
    >
    </div>
    <div class="modal fade" id="addRosterModal" tabindex="-1" aria-labelledby="addRosterModal" >
        <div class="modal-dialog modal-lg  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header ps-4 border-0 pt-3 pb-2">
                    <h5 class="modal-title  text-primary my-0 py-0 ps-3" id="eventModalLabel"><u>Add Roster Member</u></h5>
                </div>
                <div class="modal-body py-0">
                </div>
                <div class="modal-footer border-0 mb-3">
                    <button type="button" class="btn mx-auto rounded-pill btn-primary text-white" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if ($isRedirect)
        <form method="POST" action="{{ route('participant.memberManage.action') }}">
            @csrf
            <input type="hidden" value="{{$eventId}}" name="eventId">
            <input type="hidden" value="{{$selectTeam->id}}" name="teamId">
            <input id="manageMemberButton" type="submit" class="d-none" value="Done">
            </div>
        </form>
        <a class="d-none" id="manageRegistrationUrl" href="{{route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $eventId ] ) }}"> </a>
        <a class="d-none" id="eventUrl" href="{{route('participant.event.view', ['id' => $eventId ] ) }}"> </a>
        @include('Participant.__Partials.TeamHead', ['isCompactView' => true ]) 
        <br><br>

    @else
        @include('Participant.__Partials.TeamHead') 
    @endif
        <div id="Overview">
            <div @class(['my-2 py-2 ' => session('successMessage') || session('successMessage')])>
                @if (session('successMessage'))
                    <div class="text-success text-center">{{session('successMessage')}}</div>
                @elseif (session('errorMessage'))
                    <div class="text-red text-center">{{session('errorMessage')}}</div>
                @endif
            </div>
            @if (!$isRedirect)
                <div class="tab-size mt-3"><b>Outstanding Registrations</b></div><br> <br>
            @endif
            
            @if (!isset($joinEvents[0]))
                <p class="tab-size text-start mx-auto ">No events available</p>
            @else
            {{-- IS REDURECT CHANGE--}}
           
            <div class="event-carousel-styles  px-5">
                @foreach ($joinEvents as $joinEvent)
                       
                    @include('Participant.__Partials.RosterViewRegister', ['isRegistrationView' => false])
                    @include('Participant.__Partials.PieChart', ['isInvited' => false])
                @endforeach
            </div>
            {{-- IS REDURECT CHANGE--}}
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
                        <div class="event-carousel-styles px-5">
                            @foreach ($invitedEvents as $key => $joinEvent)
                                @include('Participant.__Partials.RosterViewRegister', ['isRegistrationView' => false])
                                @include('Participant.__Partials.PieChart', ['isInvited' => true])
                            @endforeach
                        </div>
                    @endif
                <br> <br>
            </div>
        @endif
        @if ($isRedirect)
        @else 
            <br><br><br><br><br><br>
        @endif
        
        <script src="{{ asset('assets/js/participant/RegistrationManagement.js') }}"></script>

    </main>
    
   
</body>
