@include('Organizer.includes.CreateEventHeadTag')
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
                data-route-invitation-store="{{ route('event.invitation.store', $event->id) }}">
            </div>

            @include('Organizer.__CreateEditPartials.CreateEventTimelineBox')
            <br>
            <div class="text-center" style="margin:auto; border-color: black; background: white; max-width: 60vw; min-height: 60vh;display:flex;flex-direction:column; justify-content:space-between;">
                <div>
                    <br>
                    <u>
                        <h5>Invite List</h5>
                    </u>
                    <br>
                    <div>
                        <select class="form-control form-select" style="max-width: 300px; margin: auto;">
                            @foreach ($teamList as $team)
                                <option value="{{$team->id}}">{{ $team->teamName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <br>
                        <button onclick="addParticant();" class="oceans-gaming-default-button">Add </button>
                    </div>
                </div>
                <div class="added-participant">
                    <br>
                    @forelse ($event->invitationList as $invitation)
                    <p>{{ $invitation->team?->teamName }}</p>
                    @empty
                    <p class="hide-if-participant">No teams invited yet</p>
                    @endforelse
                </div>
                <button onclick="goToManageScreen();" class="oceans-gaming-default-button" style="padding: 5px 20px; background-color: white; color: #2e4b59; border: 1px solid black; margin: auto;">
                    Go to event page
                </button>
                <br>
            </div>
            <script src="{{ asset('/assets/js/organizer/Invitation.js') }}"></script>
        </div>
        <br><br>
    </main>
    <script src="{{ asset('/assets/js/organizer/event_creation/CreateEventPart1.js') }}"></script>
    <script src="{{ asset('/assets/js/organizer/event_creation/timeline.js') }}"></script>
    <script src="{{ asset('/assets/js/organizer/event_creation/event_create.js') }}"></script>
    
    <script src="{{ asset('/assets/js/organizer/event_creation/CreateEventPart2.js') }}"></script>
</body>