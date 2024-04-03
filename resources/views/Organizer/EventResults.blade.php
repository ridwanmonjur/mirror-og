<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/viewEvent.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/eventResults.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
</head>
@php
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
                    View your events
                </h3>
            </u>
        </div> <br>
        <div class="grid-container">
            <div></div>
            <div>
                <img height="auto" style="aspect-ratio: 7/3; object-fit: cover; margin: auto;"
                    @class(['rounded-banner', 'rounded-box-' . $eventTierLower]) {!! trustedBladeHandleImageFailureBanner() !!} src="{{ $eventBannerImg }}" alt="">
                @if ($event->eventBanner)
                @else
                    <h5>
                        Please enter a banner image.
                    </h5>
                    <br><br>
                @endif
                <div class="card-text">
                    <div>
                        <br>
                        <div>
                            <div class="card-organizer">
                                <p>
                                    <u>{{ $event->eventName ?? 'No name yet' }}</u>
                                </p>
                                <p> {{ $event->eventDescription }} </p>
                            </div>
                            <table class="member-table">
                                <tbody class="accepted-member-table">
                                    @foreach ($joinEventList as $key => $joinEvent)
                                        <tr class="st">
                                            <td class="colorless-col">
                                                <svg    
                                                    onclick="redirectToTeamPage({{$joinEvent->team_id}});"
                                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg" width="20"
                                                    height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                    <path
                                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                </svg>
                                            </td>
                                            <td class="coloured-cell px-3">
                                                {{ $joinEvent->teamName }}
                                            </td>
                                            <td class="coloured-cell px-3">
                                                {{ $joinEvent->teamDescription }}
                                            </td>
                                            <td class="coloured-cell px-3">
                                                Hi
                                            </td>
                                            <td class="colorless-col">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div></div>
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
            window.location.href = "{{route('public.team.view', ['id' => ':id']) }}"
                .replace(':id', teamId);
        }
    </script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
    <script src="{{ asset('/assets/js/tab/tab.js') }}"></script>
