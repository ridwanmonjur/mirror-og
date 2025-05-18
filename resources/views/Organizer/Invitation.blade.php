<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation</title>
     @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    <meta name="page-component" content="teamSelect">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])    
</head>

@php
    $dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
    extract($dateArray);
@endphp

<body>
    @include('googletagmanager::body')
        @include('includes.__Navbar.NavbarGoToSearchPage')
    <main>
        <div>
            <div id="eventData"
                data-event-id="{{ $event->id }}"
                data-user-id="{{ $user_id }}"
                data-route-show="{{ route('event.show', $event->id) }}"
                data-route-invitation-store="{{ route('event.invitation.store', $event->id) }}"
                data-route-invitation-destroy="{{ route('event.invitation.destroy', $event->id) }}"
            >
            </div>

            @include('includes.__CreateEditEvent.CreateEventSuccessTimelineBox')
            <br>
            <div class="text-center" style="margin:auto; border-color: black; background: white; max-width: 60vw; min-height: 60vh;display:flex;flex-direction:column; justify-content:space-between;">
                <div>
                    <br>
                    <u>
                        <h5>Invite List</h5>
                    </u>
                    <br>
                    <div>
                        <div class="w-75 mx-auto">
                            <select id="team-select"  class="d-block w-100" placeholder="Select a person..." >
                                <option value="">Select a person...</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <br>
                        <button onclick="addParticant();" class="oceans-gaming-default-button">Add </button>
                    </div>
                </div>
                <div class="added-participant">
                    @forelse ($event->invitationList as $invitation)
                        <div id="invite-{{$invitation->id}}" class="d-flex text-center my-2 mx-auto align-items-center justify-content-center">
                                <img src="/storage/{{ $invitation->team?->teamName }}" 
                                class="team-banner border border-dark  object-fit-cover rounded-circle "  
                                onerror="this.src='/assets/images/404q.png';"
                                width="30" height="30"
                            >
                            <p class="ms-1 me-1 d-inline my-0 py-0">{{ $invitation->team?->teamName }}</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                data-invitation-id = {{$invitation->id}}
                                onclick="removeParticant(event)"
                                class="text-red border border-danger cursor-pointer rounded-circle p-0 ms-2"
                            >
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </div>
                    @empty
                    <p class="hide-if-participant mt-3">No teams invited yet</p>
                    @endforelse
                </div>
                <br>
                <button onclick="goToManageScreen();" class="oceans-gaming-default-button" style="padding: 5px 20px; background-color: white; color: #2e4b59; border: 1px solid black; margin: auto;">
                    Go to event page
                </button>
                <br>
            </div>
            <script src="{{ asset('/assets/js/organizer/Invitation.js') }}"></script>
        </div>
        <br><br>
    </main>
   
</body>