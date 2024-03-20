<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Event</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/viewEvent.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
</head>

@php
    $status = $event->statusResolved();
    $stylesEventStatus = bladeEventStatusStyleMapping($status);
    $stylesEventRatio = bladeEventRatioStyleMapping($event->registeredParticipants, $event->totalParticipants);
    $tier = $event->tier ? $event->tier->eventTier : null;
    $eventTierLower = bladeEventTowerLowerClass($tier);
    $dateArray = bladeGenerateEventStartEndDateStr($event->startDate, $event->startTime);
    extract($dateArray);
    $eventTierLowerImg = bladeEventTierImage($tier);
    $eventBannerImg = bladeImageNull($event->eventBanner);
@endphp

<body>
    @include('CommonLayout.NavbarforParticipant')
    <main>
        <br class="d-none-at-desktop">
        <div class="pt-2">
            <header class="flexbox-welcome">
                <u>
                    <h3>
                        View your events
                    </h3>
                </u>
            </header>
        </div>
        <br><br>
        <div class="grid-container">
            @if ($tier)
                <div class="{{ 'side-image side-image-' . $eventTierLower }}">
                    <img class="side-image-absolute-bottom" src="{{ $eventTierLowerImg }}" width="180"
                        height="125">
                </div>
            @else
                <div>
                </div>
            @endif
            <div>
                <div style="padding-left: 20px;padding-right:20px;">
                    <div class="mx-2 position-relative">

                        <div class="d-flex justify-content-center d-lg-none">
                            <img class="image-at-top" src="{{ $eventTierLowerImg }}" {!! trustedBladeHandleImageFailureResize() !!}
                                width="120" height="90">
                        </div>
                        <img width="100%" height="auto" style="aspect-ratio: 7/3; object-fit: cover; margin: auto;"
                            @class(['rounded-banner', 'rounded-box-' . $eventTierLower]) {!! trustedBladeHandleImageFailureBanner() !!} src="{{ $eventBannerImg }}"
                            alt="">
                        @if ($event->eventBanner)
                        @else
                            <h5>
                                Please enter a banner image.
                            </h5>
                            <br><br>
                        @endif
                    </div>
                </div>
                <div class="grid-container-two-columns-at-desktop">
                    <div class="card-text">
                        <div>
                            <br>
                            <div class="flexbox-centered-space" style="align-items: center;">
                                <p
                                    style="height:60px;text-overflow:ellipsis; overflow:hidden;font-size:20px;margin-right:60px;margin-bottom:20px">
                                    <u>{{ $event->eventName ?? 'No name yet' }}</u>
                                </p>
                                <svg style="position: relative; top: -20px; margin-left: -60px;"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-share-2">
                                    <circle cx="18" cy="5" r="3"></circle>
                                    <circle cx="6" cy="12" r="3"></circle>
                                    <circle cx="18" cy="19" r="3"></circle>
                                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                </svg>
                            </div>
                            <div class="flexbox-centered-space card-subtitle">
                                <div class="flexbox-centered-space">
                                    <img style="display: inline;" src="{{ asset('/assets/images/menu.png') }}"
                                        class="{{ 'rounded-image rounded-box-' . $eventTierLower }}" alt="menu">
                                    &nbsp;
                                    <div class="card-organizer">
                                        <p style="display: inline;"><u>
                                                {{ $event?->user?->organizer?->companyName ?? 'Add' }} </u> </p>
                                        <p class="small-text"> <i> {{ $followersCount }} followers </i> </p>
                                    </div>
                                </div>

                                <form id="followForm" method="POST"
                                    action="{{ route('participant.organizer.follow') }}">
                                    @csrf
                                    <input type="hidden" name="user_id"
                                        value="{{ $user && $user->id ? $user->id : '' }}">
                                    <input type="hidden" name="organizer_id"
                                        value="{{ $event?->user_id }}">
                                    <button type="submit" id="followButton"
                                        style="background-color: {{ $user && $user->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $user && $user->isFollowing ? 'black' : 'white' }};  padding: 5px 10px; font-size: 14px; border-radius: 10px; border: none;">
                                        {{ $user && $user->isFollowing ? 'Following' : 'Follow' }}
                                    </button>
                                </form>
                            </div>
                            <br>
                            <h4> <u> {{ $combinedStr }} </u> </h4>
                            <h4> <u> {{ strtoupper($timePart) }} </u> </h4>
                            <br>
                            <div>
                                <div class="tab">
                                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks active' }}"
                                        onclick="openTab(event, 'Overview')">Overview</button>
                                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}"
                                        onclick="openTab(event, 'Bracket')">Bracket</button>
                                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}"
                                        onclick="openTab(event, 'Teams')">Teams</button>
                                    <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}"
                                        onclick="openTab(event, 'Result')">Result</button>
                                </div>
                                <br>
                                <div id="Overview" class="tabcontent" style="display: block;">
                                    <h3><u>About this event</u></h3>
                                    <p>{{ $event->eventDescription ?? 'Not added description yet' }} </p>
                                </div>

                                <div id="Bracket" class="tabcontent">
                                    <h3><u>Bracket</u></h3>
                                    <p>Bracket is the capital of France.</p>
                                </div>

                                <div id="Teams" class="tabcontent">
                                    <h3><u>Teams</u></h3>
                                    <p>Teams tab.</p>
                                </div>
                                <div id="Result" class="tabcontent">
                                    <h3><u>Result</u></h3>
                                    <p>Result tab.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ps-3">
                        <br><br>
                        @if (session('errorMessage'))
                            <div class="error-message">
                                {{ session('errorMessage') }}
                            </div>
                        @endif

                        <form method="POST" name="joinForm" action="{{ route('participant.event.selectOrCreateTeam.redirect', ['id' => $event->id]) }}">
                            @csrf

                            @if ($existingJoint)
                                <button type="button" class="oceans-gaming-default-button" disabled>
                                    <span>Joined</span>
                                </button>
                                <br><br>
                                <a href="{{route('participant.register.manage', ['id' => $existingJoint->team_id])}}"><u>Manage registration</u></a>
                            @else
                                <button type="submit" class="oceans-gaming-default-button">
                                    <span>Join</span>
                                </button>
                            @endif
                        </form>

                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                &nbsp;
                                @if ($event->tier)
                                    <span style="position: relative; top: 5px;"> RM
                                        {{ $event->tier->tierPrizePool ?? 'No Prize' }} Prize Pool</span>
                                @else
                                    <p>Tier PrizePool: Not available</p>
                                @endif
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-dollar-sign">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                &nbsp;
                                @if ($event->tier)
                                    <span style="position: relative; top: 5px;">RM
                                        {{ $event->tier->tierEntryFee ?? 'Free' }} Entry Fees</span>
                                @else
                                    <p>Tier Entry Fee: Not available</p>
                                @endif
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                &nbsp;
                                <span style="position: relative; top: 5px;">{{ $event->venue ?? 'SEA' }}</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-bar-chart-2">
                                    <line x1="18" y1="20" x2="18" y2="10"></line>
                                    <line x1="12" y1="20" x2="12" y2="4"></line>
                                    <line x1="6" y1="20" x2="6" y2="14"></line>
                                </svg>
                                &nbsp;
                                <span style="position: relative; top: 5px;">8 / 14</span>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-info">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="16" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                </svg>
                                &nbsp;
                                <span style="position: relative; top: 5px;">{{ $tier ?? 'Choose event type' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @if ($tier)
            <div class="{{ 'side-image side-image-' . $eventTierLower }} ">
                <img class="side-image-absolute-top" src="{{ $eventTierLowerImg }}" width="180" height="125">
            </div>
        @else
            <div></div>
        @endif
        </div>
        <br>
    </main>
    @stack('script')
    <script>
        function goToCreateScreen() {
            let url = "{{ route('event.create') }}";
            window.location.href = url;
        }

        function goToEditScreen() {
            let url = "{{ route('event.edit', $event->id) }}";
            window.location.href = url;
        }

        document.getElementById('followForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            let followButton = document.getElementById('followButton');
            let form = this;
            let formData = new FormData(form);
            followButton.style.setProperty('pointer-events', 'none');
    
            try {
                let response = await fetch(form.action, {
                    method: form.method,
                    body: formData
                });

                let data = await response.json();
                let followButton = document.getElementById('followButton');
                followButton.style.setProperty('pointer-events', 'none')

                if (data.isFollowing) {
                    followButton.innerText = 'Following';
                    followButton.style.backgroundColor = '#8CCD39';
                    followButton.style.color = 'black';
                } else {
                    followButton.innerText = 'Follow';
                    followButton.style.backgroundColor = '#43A4D7';
                    followButton.style.color = 'white';
                }
                
                followButton.style.setProperty('pointer-events', 'auto');
            } catch (error) {
                followButton.style.setProperty('pointer-events', 'auto');
                console.error('Error:', error);
            }
        });
    </script>
    @include('CommonLayout.BootstrapV5Js')
    <script src="{{ asset('/assets/js/tab/tab.js') }}"></script>

</html>
