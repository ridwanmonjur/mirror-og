<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Team to Register</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/registerTeam.css') }}">
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <input type="hidden" id="eventUrl" value="{{ route('participant.event.view', $id) }}">
    <main class="d-flex justify-content-center flex-row">
        <div class="wrapper-height ">
            <div class="wrapper grid-2-at-screen mx-auto mx-2 user-select-none ">
                <div>

                    <header><u>Select Team to Register</u></header>
                    <br>
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button type="button" class="dropbtn px-0 py-2" onclick="toggleDropdown()">
                            <span id="selectedTeamLabel">Select Team</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div class="px-3 dropdown-content" id="teamList" style="z-index: 999;">
                            <input type="text" id="teamSearch" oninput="filterTeams()"
                                placeholder="Search for teams...">
                            <div>
                                <form id="selectTeam"
                                    action="{{ route('participant.selectTeamToJoin.action', ['id' => $id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="selectedTeamId" value="">
                                </form>
                                @foreach ($selectTeam as $item)
                                    <div class="px-0 py-0 mx-0 my-2 cursor-pointer"
                                        onclick="selectOption(this, '{{ addslashes($item->teamName) }}', '{{ $item->id }}')">
                                        <img src="{{ '/storage' . '/' . $item->teamBanner }}" width="35px"
                                            height="35px" class="rounded-circle  me-2 border border-primary object-fit-cover"
                                            onerror="this.onerror=null;this.src='/assets/images/404.png';">
                                        <a class="d-inline" data-team-id="{{ $item->id }}">{{ $item->teamName }}
                                        </a>
                                    </div>
                                @endforeach
                                @if ($count < 5)
                                    <form id="createTeam"
                                        action="{{ route('participant.event.createTeam.redirect', ['id' => $id]) }}"
                                        method="POST"
                                    >
                                         @csrf
                                        <button class="btn  text-primary px-0" type="submit">
                                            <u>Create Another Team</u>
                                        </button>
                                    </form>
                                    
                                @endif
                            </div>
                            <div class="mt-2 mb-2 d-flex justify-content-center">
                                <button class="btn btn-primary text-light btn-sm rounded-pill px-2" onclick="toggleDropdown()"> Close</button>
                            </div>

                        </div>
                            
                            <div>
                                <br>
                                <p>You are a member of {{ $count }} teams. </p> 
                                <p class="text-primary">You can be part of maximum 5 teams!</p>
                                @if ($count > 5)
                                    <p class="text-red"> You cannot create more teams.</p>
                                @endif
                            </div>
                    </div>
                </div>

                <div class="mt-5">
                    <p>All members in the team you select will be notified to join this event</p>

                    <p>Registration will NOT be confirmed until enough team members have accepted to join and
                        payment is
                        complete. Once enough team members have accepted and the entry fee has been paid,
                        registration
                        can
                        be confirmed.
                    </p>

                    <div class="text-center">
                        <button id="selectTeamButtonId" form="selectTeam" disabled style="max-width: 100%;" class="oceans-gaming-default-button" type="submit">
                            Confirm Team and Notify
                        </button>
                    </div>
                    <div class="text-center mt-2">
                        <button type="button" onclick="goToCancelButton();"
                            class="oceans-gaming-default-button oceans-gaming-white-button"> Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('/assets/js/participant/SelectTeamToRegister.js') }}"></script>
        
</body>

</html>
