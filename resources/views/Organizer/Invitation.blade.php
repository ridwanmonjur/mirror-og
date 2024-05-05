@include('Organizer.Partials.CreateEventHeadTag')
@php
$dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
extract($dateArray);
@endphp

<body>
        @include('CommonPartials.NavbarGoToSearchPage')
    <main>
        <div>
            @include('Organizer.CreateEditPartials.CreateEventTimelineBox')
            <br>
            <div class="text-center" style="margin:auto; border-color: black; background: white; max-width: 60vw; min-height: 60vh;display:flex;flex-direction:column; justify-content:space-between;">
                <div>
                    <br>
                    <u>
                        <h5>Invite List</h5>
                    </u>
                    <br>
                    <div>
                        <select class="form-control" style="max-width: 300px; margin: auto;">
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
                    <p>{{ $invitation->team->teamName }}</p>
                    @empty
                    <p class="hide-if-participant">No teams invited yet</p>
                    @endforelse
                </div>
                <button onclick="goToManageScreen();" class="oceans-gaming-default-button" style="padding: 5px 20px; background-color: white; color: black; border: 1px solid black; margin: auto;">
                    Go to event page
                </button>
                <br>
            </div>
            <script>
                const goToManageScreen = () => {
                    window.location.href = "{{route('event.show', $event->id) }}";
                }

                function addParticant() {
                    const teamId = document.querySelector('select').value;
                    const teamName = document.querySelector('select').value;
                    const addedTeam = document.querySelector('.added-participant');
                    const hideIfTeam = document.querySelector('.hide-if-participant');
                    if (hideIfTeam) {
                        hideIfTeam.classList.add('d-none');
                    }
                
                    let data = {
                        event_id: "{{ $event->id }}",
                        team_id: teamId,
                        organizer_id: "{{ $user_id }}",
                    };
                
                    fetch("{{ route('event.invitation.store', $event->id) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => {
                            return response.json()
                        })
                        .then(responseData => {
                            console.log({responseData})
                            const teamElement = document.createElement('p');
                            teamElement.textContent = responseData?.data.team.teamName;
                            addedTeam.appendChild(teamElement);
                            
                            Toast.fire({
                                icon: 'success',
                                text: "Successfully added user."
                            })
                        })
                        .catch(error => {
                            console.error(error);
                        })
                } 
            </script>
        </div>
        <br><br>
    </main>
    @include('Organizer.CreateEditPartials.CreateEventScripts')
    

    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
    </script>

</body>