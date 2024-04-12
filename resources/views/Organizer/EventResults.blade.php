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
            <div class="card-text">
                <div>
                    <div>
                        <div class="card-organizer">
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
                                    Team Created
                                </th>
                                <th class="text-start">
                                    Action
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
                                            {{ $joinEventAndTeam->position ? $joinEventAndTeam->position : 'N/A' }}
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
                                            <svg data-bs-toggle="modal" data-bs-target="{{ '#rank' . $joinEventAndTeam->id . '-modal' }}" 
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                            </svg>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="{{'rank' .  $joinEventAndTeam->id . '-modal' }}" tabindex="-1"
                                        aria-labelledby="{{ 'rank'.  $joinEventAndTeam->id . '-modal' . 'label' }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                        <br><br>
                        <h5> <u> Awards </u> </h5>
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
                                            {{ $joinEventAndTeam->position ? $joinEventAndTeam->position : 'N/A' }}
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

                                        <td class="coloured-cell px-1 text-start"
                                            style="padding: 0; margin: 0; width: 150px;">
                                            <button onclick="setModalDaya" class="btn btn-sm btn-success me-2"
                                                data-bs-toggle="modal" data-bs-target={{ '#award-modal' }}>
                                                Awards
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                <div class="modal fade" id='award-modal' tabindex="-1"
                                    aria-labelledby={{ 'award-modal' . 'label' }} aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
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

    <script>
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
