<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Invitation</title>
     @include('includes.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    {{-- <meta name="page-component" content="teamSelect"> --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/teamSelect.js'])    
</head>

@php
    $dateArray = $event->startDatesStr($event->startDate, $event->startTime);
    extract($dateArray);
@endphp

<body>
    @include('googletagmanager::body')
        @include('includes.Navbar')
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

            @include('includes.CreateEditEvent.CreateEventSuccessTimelineBox')
            <br>
            <div class="text-center bg-white border rounded rounded-3  border-light d-flex justify-content-center flex-column mx-auto" style="width: min(800px, 95%);">
                <div>
                    <br>
                    <u>
                        <h5>Invite List</h5>
                    </u>
                    <br>
                    <div>
                        <div class="w-75 mx-auto">
                            <select id="team-select"  class="d-block w-100" placeholder="Select a team..." >
                                <option value="">Select a team...</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <br>
                        <button onclick="addParticant();" class="oceans-gaming-default-button">Add </button>
                    </div>
                </div>
                <div class="added-participant">
                    @forelse ($event->invitationList as $key => $invitation)
                        <div class="card my-2 mx-auto" style="width: min(95%, 600px);">
                            <div id="invite-{{$invitation->id}}" class="card-body d-flex text-center my-2 mx-3 align-items-center justify-content-between">
                                  
                                <p class="ms-3 me-4 d-inline my-0 py-0"> 
                                      <img src="/storage/{{ $invitation->team?->teamBanner }}" 
                                    class="team-banner border border-dark  me-2 object-fit-cover rounded-circle "  
                                    onerror="this.src='/assets/images/404q.png';"
                                    width="30" height="30"
                                >
                                    <span class="me-2 ">Team #{{$key+1}}</span> 
                                    <span class="text-primary">{{ $invitation->team?->teamName }}</span>
                                </p>
                                <button  data-invitation-id = {{$invitation->id}}
                                    onclick="removeParticant(event)"
                                    class="btn border-red text-red  rounded-pill btn-sm "    
                                >

                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                                    
                                        class="text-red border border-danger cursor-pointer rounded-circle p-0 me-1"
                                    >
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                    <small> Remove </small>
                                </button>
                            </div>
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