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
                            <h3>
                                {{ $event->eventName ?? 'No name yet' }}
                            </h3>
                            <p> {{ $event->eventDescription }} </p>
                        </div>
                        <table class="member-table text-start">
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
                                            {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                        </td>
                    
                                        <td class="coloured-cell px-1 text-start" style="padding: 0; margin: 0; width: 150px;">
                                            <button
                                                onclick="setModalDaya"
                                                class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target={{'#rank-modal'}}
                                            >
                                                    Rank
                                            </button>
                                            
                                            <button class="btn btn-sm btn-warning">Awards</button>
                                        </td>
                                    </tr>
                                @endforeach
                                <div class="modal fade" id='rank-modal' tabindex="-1" aria-labelledby={{'rank-modal' . 'label'}} aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            ...
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
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
