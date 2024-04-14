<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/viewEvent.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/eventResults.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
@php
    use Carbon\Carbon;
    $tier = $event->tier ? $event->tier->eventTier : null;
    $eventTierLower = bladeEventTowerLowerClass($tier);
    $eventTierLowerImg = bladeEventTierImage($tier);
    $eventBannerImg = bladeImageNull($event->eventBanner);
@endphp

<body>
    @include('CommonLayout.NavbarGoToSearchPage')

    <main>
        <br>
        <div>
            <u>
                <h3>
                    Manage your event results
                </h3>
            </u>
        </div> <br>
        <div>
            <div>
                <div>
                    <div>
                        <div>
                            <h5 class="card-text-2-lines">
                                {{ $event->eventName ?? 'No name yet' }}
                            </h5>
                            <p> {{ $event->eventDescription }} </p>
                        </div>
                        <h5> <u> Position </u> </h5>
                        <table class="member-table text-start" style="margin-left: 5px;">
                            <thead class="accepted-member-table text-start">
                                <th></th>
                                <th class="text-start">
                                    Team Position
                                </th>
                                <th class="text-start">
                                    Team Name
                                </th>
                                <th class="text-start">
                                    Team Description
                                </th>
                                <th class="text-start">
                                    Team Created
                                </th>
                            </thead>
                            <tbody class="accepted-member-table text-start">
                                @foreach ($joinEventAndTeamList as $key => $joinEventAndTeam)
                                    <tr class="st">
                                        <td class="colorless-col px-3">
                                            <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                                height="20" fill="currentColor" class="bi bi-eye-fill"
                                                viewBox="0 0 16 16">
                                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                <path
                                                    d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                            </svg>
                                        </td>
                                        <td class="coloured-cell text-center px-2">
                                            {{ $joinEventAndTeam->position ? $joinEventAndTeam->position : '-' }}
                                        </td>
                                        <td class="coloured-cell px-3 text-start">
                                            {{ $joinEventAndTeam->teamName }}
                                        </td>
                                        <td class="coloured-cell px-3 text-start">
                                            {{ $joinEventAndTeam->teamDescription }}
                                        </td>
                                        <td class="coloured-cell px-3 text-start">
                                            {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                        </td>
                                        <td class="colorless-column px-1 ps-4 text-start"
                                            style="padding: 0; margin: 0; width: 150px;">
                                            <svg data-bs-toggle="modal"
                                                data-bs-target="{{ '#rank' . $joinEventAndTeam->id1 . '-modal' }}"
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path
                                                    d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                            </svg>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="{{ 'rank' . $joinEventAndTeam->id1 . '-modal' }}"
                                        tabindex="-1"
                                        aria-labelledby="{{ 'rank' . $joinEventAndTeam->id1 . '-modal' . 'label' }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <form onsubmit="editCreatePosition(event);">
                                                    <div class="mx-auto text-center mt-3">
                                                        <h5> Choose a position for team:
                                                            '{{ $joinEventAndTeam->teamName }}'. </h5>
                                                        <br>
                                                        <p> Choose between 1 and {{ $event->tier->tierTeamSlot }}</p>
                                                        <br>
                                                        <div
                                                            class="input-group mb-3 mx-auto d-flex justify-content-center">
                                                            <span class="input-group-text bg-primary text-light"
                                                                id="basic-addon2">#</span>
                                                            <input class="form-control"
                                                                style="max-width: 100px !important;" 
                                                                type="number"
                                                                name="position" min="1"
                                                                max="{{ $event->tier->tierTeamSlot }}">
                                                            <input type="hidden" name="id" value="{{ $joinEventAndTeam->id1 }}"> 
                                                        </div>
                                                        <br>
                                                        <button
                                                            type="submit" class="oceans-gaming-default-button me-3"
                                                        >Submit
                                                        </button>
                                                        <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                                            data-bs-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                @endforeach
                            </tbody>
                        </table>
                        <br><br>
                        <h5>
                            <u> Awards </u>
                        </h5>
                        <div class="mx-auto member-table ms-5">

                            <button data-bs-toggle="modal" data-bs-target="{{ '#award' . '-modal' }}"
                                class="oceans-gaming-default-button">
                                Add award
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                        fill="currentColor" class="text-light pt-1 bi bi-plus-circle ms-3"
                                        viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <table class="member-table text-start" style="margin-left: -5px;">
                            <thead class="accepted-member-table text-start">
                                <th></th>
                                <th class="text-start">
                                    Award
                                </th>
                                <th class="text-start">
                                    Team Name
                                </th>
                                <th class="text-start">
                                    Team Description
                                </th>
                                <th class="text-start">
                                    Team Created
                                </th>
                            </thead>
                            <tbody class="accepted-member-table text-start">
                                @foreach ($awardAndTeamList as $key => $joinEventAndTeam)
                                    @if (!is_null($joinEventAndTeam->award_id))
                                        <tr class="st">
                                            <td class="colorless-col px-3">
                                                <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                    width="20" height="20" fill="currentColor"
                                                    class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                    <path
                                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                </svg>
                                            </td>
                                            <td class="coloured-cell text-center px-2">
                                                @if (is_null($joinEventAndTeam->awards_image))
                                                    No awards
                                                @else
                                                    <img src="{{ '/storage' . '/' . $joinEventAndTeam->awards_image }}" style="object-fit: cover;" width="50px" height="25px">
                                                @endif
                                                {{ $joinEventAndTeam->awards_title ? $joinEventAndTeam->awards_title : '' }}
                                            </td>
                                            <td class="coloured-cell px-3 text-start">
                                                {{ $joinEventAndTeam->teamName }}
                                            </td>
                                            <td class="coloured-cell px-3 text-start">
                                                {{ $joinEventAndTeam->teamDescription }}
                                            </td>
                                            <td class="coloured-cell px-3 text-start">
                                                {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                            </td>
                                            <td class="colorless-column px-1 ps-4 text-start">
                                                <svg 
                                                    onclick="deleteAward({{$joinEventAndTeam->results_id}})"
                                                    xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path
                                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                    <path
                                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                </svg>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                <div class="modal fade" id='award-modal' tabindex="-1"
                                    aria-labelledby={{ 'award-modal' . 'label' }} aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <form onsubmit="addAward(event);">
                                            <div class="modal-body modal-body-overflow scrollbarline pe-4">
                                                <div class="mx-auto text-center mt-3">
                                                    <h5> Choose an award and team </h5>
                                                    <br>
                                                    <div>
                                                        <label class="form-check-label fw-bold">
                                                            Choose team
                                                        </label>
                                                        <select 
                                                            class="form-select mx-auto" 
                                                            name="teamId"
                                                            aria-label="Select Team"
                                                            style="max-width: 200px !important;" 
                                                        >
                                                            @foreach($joinEventAndTeamList as $joinEventAndTeam)
                                                                <option value="{{$joinEventAndTeam->team_id}}">{{$joinEventAndTeam->teamName}}</option> 
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <br>
                                                    <label class="form-check-label fw-bold">
                                                            Choose award
                                                        </label>
                                                    <div class="d-flex flex-row justify-content-start ps-2 pe-5 mt-0" >
                                                        @foreach($awardList as $award)
                                                            <div class="form-check mx-auto text-center px-2" >
                                                                <input class="form-check-input text-center mx-auto" type="radio" name="awardId" value="{{$award->id}}">
                                                                <label class="form-check-label" for="awardId">
                                                                    <span> {{$award->title}} </span>
                                                                    <br>
                                                                    <img src="{{ '/storage' . '/' . $award->image}}" width="150" style="object-fit: cover;"> </span>
                                                                    <br>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <br>
                                                    <button type="submit" class="oceans-gaming-default-button"
                                                        >Submit
                                                    </button>
                                                    <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                                        data-bs-dismiss="modal">Close
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <br>
    </main>
    @stack('script')
    @include('CommonLayout.BootstrapV5Js')
    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    @include('CommonLayout.BootstrapV5Js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    @include('CommonLayout.Toast')
    @include('CommonLayout.Dialog')
    <script>
        let awardToDeleteId = null;

        function loadToast() {
            let currentUrl = window.location.href;
            let urlParams = new URLSearchParams(window.location.search);
            let successValue = urlParams.get('success');
            let message = urlParams.get('message');
            if (successValue == 'true') {
                Toast.fire({
                    icon: 'success',
                    text: message 
                })
            }
        }

        loadToast();

        function reloadUrl(currentUrl, message) {
            if (currentUrl.includes('?')) {
                currentUrl = currentUrl.split('?')[0];
            }

            currentUrl += `?success=true&message=${message}`;
            window.location.replace(currentUrl);
        }

        function toastError(message, error = null) {
            console.error(error)
            Toast.fire({
                icon: 'error',
                text: message
            });
        }

        function takeYesAction() {
            const actionFunction = actionMap[dialogForMember.getActionName()];
            if (actionFunction) {
                actionFunction();
            } else {
                Toast.fire({
                    icon: 'error',
                    text: "No action found."
                })
            }
        }

        function takeNoAction() {
            awardToDeleteId = null;
        }

        function deleteAward(id) {
            awardToDeleteId = id;
            dialogOpen('Are you sure you want to remove this award from this user?', deleteAwardAction, takeNoAction)
        }

        function editCreatePosition(event) {
            event.preventDefault(); 
            let formData = new FormData(event.target);
            let joinEventId = formData.get('id');
            let joinEventPosition = formData.get('position');
            const url = "{{ route('event.results.store', ['event' => $event->id]) }}";
            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.results.index', ['event' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message);
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error changing position.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'join_events_id': joinEventId,
                        'position': joinEventPosition
                    })
                }
            );
        }

        async function addAward(event) {
            event.preventDefault();
            let formData = new FormData(event.target);
            let teamId = formData.get('teamId');
            let awardId = formData.get('awardId');
            const url = "{{ route('event.awards.store', ['id' => $event->id]) }}";

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.results.index', ['event' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message);
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) {
                    toastError('Error adding award.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        'team_id': Number(teamId),
                        'award_id': Number(awardId),
                        'event_details_id': {{ $event->id }}
                    })
                }
            );
        }

        async function deleteAwardAction() {

            const url = "{{ route('event.awards.destroy', ['id' => $event->id, 'awardId' => ':id']) }}"
                .replace(':id', awardToDeleteId)

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.results.index', ['event' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message);
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error deleting award.', error);
                }, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                }
            );
        }

        function redirectToProfilePage(userId) {
            window.location.href = "{{ route('participant.profile.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

        function goToCreateScreen() {
            let url = "{{ route('event.create') }}";
            window.location.href = url;
        }

        function goToEditScreen() {
            let url = "{{ route('event.edit', $event->id) }}";
            window.location.href = url;
        }

        function redirectToTeamPage(teamId) {
            window.location.href = "{{ route('public.team.view', ['id' => ':id']) }}"
                .replace(':id', teamId);
        }
    </script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    <script src="{{ asset('/assets/js/tab/tab.js') }}"></script>
