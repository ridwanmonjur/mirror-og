@include('Organizer.Layout.CreateEventHeadTag')
<!-- https://stackoverflow.com/questions/895171/prevent-users-from-submitting-a-form-by-hitting-enter -->
@php
$dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
extract($dateArray);
@endphp

<body>
        @include('CommonLayout.NavbarGoToSearchPage')
    <main>
        <div>
            @include('Organizer.CreateEdit.CreateEventTimelineBox')
            <br>
            <div class="text-center" style="margin:auto; border-color: black; background: white; max-width: 60vw; min-height: 60vh;display:flex;flex-direction:column; justify-content:space-between;">
                <div>
                    <br>
                    <u>
                        <h5>Invite List</h5>
                    </u>
                    <br>
                    <div>

                        <select onchange="addParticant();" class="form-control" style="max-width: 300px; margin: auto;">
                            @foreach ($participationList as $participant)
                            <option value="{{$participant->id}}">{{ $participant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="added-participant">
                    <br>
                    @forelse ($event->invitationList as $invitation)
                    <p>{{ $invitation->participant->name }}</p>
                    @empty
                    <p class="hide-if-participant">No participants yet</p>
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
                    const participantListAll = {!!json_encode($participationList) !!};
                    console.log(participantListAll);
                    let participant = null;
                    const participantId = document.querySelector('select').value;
                    const addedParticipant = document.querySelector('.added-participant');
                    participant = participantListAll.filter((participantItem) => {
                        return participantItem.id == participantId;
                    });
                    if (participant[0]) {
                        participant = participant[0];
                    }
                    else{
                        Toast.error("Participant not found.");
                    }
                    if (participant) {
                        const hideIfParticipant = document.querySelector('.hide-if-participant');
                        if (hideIfParticipant) {
                            hideIfParticipant.classList.add('d-none');
                        }
                        let data = {
                            event_id: "{{ $event->id }}",
                            participant_id: participantId,
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
                                response.json()
                            })
                            .then(responseData => {
                                console.log(responseData);
                                const participantElement = document.createElement('p');
                                participantElement.textContent = participant?.name ?? "Can't find id of participant.";
                                addedParticipant.appendChild(participantElement);
                                Toast.fire({
                                    icon: 'success',
                                    text: "Successfully added user."
                                })
                            })
                            .catch(error => {
                                console.error(error);
                            })

                    }
                } {}
            </script>
        </div>
        <br><br>
    </main>
    @include('Organizer.CreateEdit.CreateEventScripts')
        @include('CommonLayout.BootstrapJs')

    <script>
        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }
    </script>

</body>