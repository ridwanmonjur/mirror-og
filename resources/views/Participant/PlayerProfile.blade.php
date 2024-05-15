<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">

    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

</head>
@php
    use Carbon\Carbon;
@endphp
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
    @endphp
@endauth
<body>
    @include('CommonPartials.NavbarGoToSearchPage')

    <main x-data="alpineDataComponent">
        <div id="backgroundBanner" class="member-section px-2 pt-2"
            style="background-image: url({{ '/storage' . '/'. $userProfile->participant->backgroundBanner }} );
                background-size: cover; background-repeat: no-repeat;"
        >
            <input type="hidden" id="games_data_input" value="{{ $userProfile->participant->games_data ?? json_encode([]) }}">
            @if($isOwnProfile)
                <div class="d-flex justify-content-end py-0 my-0 mb-2">
                    <input type="file" id="backgroundInput" class="d-none"> 
                    <button 
                        onclick="document.getElementById('backgroundInput').click();"
                        class="btn btn-secondary text-light rounded-pill py-2 me-3"> 
                        Change Background
                    </button>
                    <button 
                        x-cloak
                        x-show="!isEditMode"
                        x-on:click="isEditMode = true; fetchCountries(); fetchGames();"
                        class="oceans-gaming-default-button oceans-gaming-primary-button px-3 py-2"> 
                        Edit Profile
                    </button>
                    <button 
                        x-cloak
                        x-show="isEditMode"
                        x-on:click="submitEditProfile(event)"
                        data-url="{{route('participant.profile.update')}}"
                        class="oceans-gaming-default-button oceans-gaming-transparent-button px-3 py-2 me-3"> 
                        Save
                    </button>
                    {{-- Close icon --}}
                    <svg 
                        style="top: 10px;"
                        x-show="isEditMode"
                        x-on:click="isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-circle cursor-pointer align-middle position-relative" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
             @endif
            <div class="d-flex justify-content-center align-items-start flex-wrap">
                <div class="member-image align-middle">
                    <div class="upload-container">
                        <label for="image-upload" class="upload-label">
                            <div class="circle-container">
                                  <div id="uploaded-image" class="uploaded-image"
                                        style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }} ); background-size: cover; 
                                        background-repeat: no-repeat; background-position: center;"
                                    ></div>
                                <button id="upload-button" class="upload-button" aria-hidden="true">Upload</button>
                            </div>
                        </label>
                        <input type="file" id="image-upload" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div class="member-details">
                        <div x-show="errorMessage != null" class="text-red" x-text="errorMessage"> </div>
                        <div x-cloak x-show="isEditMode">
                            <input 
                                placeholder = "Enter your nickname..."
                                style="width: 250px;"
                                class="form-control border-secondary player-profile__input d-inline" 
                                x-model="participant.nickname" 
                            > 
                            <br>
                            <span class="d-inline-flex justify-content-between align-items-center">
                                <input
                                    placeholder = "Your bio..."
                                    style="width: 200px;"
                                    class="form-control border-secondary player-profile__input d-inline me-3" 
                                    x-model="participant.bio" 
                                > 
                                <input 
                                    placeholder="Birthday"
                                    type="date"
                                    style="width: 150px;"
                                    class="form-control border-secondary player-profile__input d-inline" 
                                    x-model="participant.birthday" 
                                >
                            </span> 
                            <br> <br>
                            <div class="w-100 d-flex justify-content-start align-items-center flex-wrap">
                                <span class="me-3">
                                    <svg
                                        class="me-2 align-middle" 
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <select
                                        x-model="participant.region" 
                                        style="width: 150px;"    
                                        class="form-control d-inline rounded-pill"
                                    >
                                        <template x-for="country in countries">
                                            <option x-bind:value="country.emoji_flag+ ' ' + country.name.en">
                                            <span x-text="country.emoji_flag" class="mx-3"> </span>  
                                            <span x-text="country.name.en"> </span>
                                            </option>
                                        </template>
                                    </select> 
                                </span>
                                <span class="me-3">
                                    <svg 
                                        class="align-middle"
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
                                        <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                        <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                    </svg>
                                    <input 
                                        style="width: 150px;"
                                        placeholder = "Enter your domain..."
                                        class="form-control border-secondary player-profile__input d-inline" 
                                        x-model="participant.domain"
                                    > 
                                </span>
                                <span>
                                    <svg
                                        class="align-middle"
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                    </svg>
                                    <span>Joined {{Carbon::parse($userProfile->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                                </span>
                                
                            </div>
                            <br>
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                <small class="d-flex justify-content-start flex-wrap">
                                    <template x-for="game in games_data">
                                        <span
                                            class="me-3 border px-3 mb-2 rounded-pill py-2 border-secondary cursor-pointer me-2"
                                        >
                                            <img
                                                width="25"
                                                height="25"
                                                :src="'/storage/' + game.image"
                                                class="object-fit-cover"
                                            > 
                                            <span x-text="game.name"> </span>                                        </span>
                                    </template>
                                </small>
                                <select
                                    x-cloak
                                    x-ref="select" 
                                    x-show.important="isAddGamesMode"
                                    style="width: 200px;"    
                                    data-trigger="true"
                                    class="selectpicker form-control d-inline rounded-pill mb-2"
                                >
                                </select> 
                                <span
                                    x-cloak
                                    x-show.important="!isAddGamesMode"
                                    x-on:click="isAddGamesMode = true;"
                                    class="ms-2 border px-2 rounded-pill py-2 border-secondary cursor-pointer mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="cursor-pointer bi bi-plus-circle me-2" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                                    </svg>
                                    Add a game
                                </span> 
                            </div>
                            <br><br>
                            <br>
                        </div>
                    <div x-cloak x-show="!isEditMode">
                        <h4>
                            {{$userProfile->participant->nickname}}
                        </h4>
                        <p>
                            <span> {{$userProfile->name}}, {{$userProfile->participant->age}} </span>
                            @if (isset($userProfile->participant->birthday))
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-date ms-3 me-2" viewBox="0 0 16 16">
                                <path d="M6.445 11.688V6.354h-.633A13 13 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23"/>
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                </svg>
                                <span>{{$userProfile->participant->birthday}}</span>
                            @endif
                        </p>
                        
                        <p>
                            {{$userProfile->participant->bio}}
                        </p>
                        <div class="d-flex justify-content-between flex-wrap w-100">
                            @if (isset($userProfile->participant->region))
                                <span class="me-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill me-2" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <span>{{$userProfile->participant->region}}</span>
                                </span>
                            @endif
                            @if (isset($userProfile->participant->domain))
                            <span class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg me-2" viewBox="0 0 16 16">
                                <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                </svg>
                                <span>{{$userProfile->participant->domain}}</span>
                            </span>
                            @endif
                            <span class="me-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                                <span>Joined {{Carbon::parse($userProfile->participant->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                            </span>
                        </div>
                        <br>
                        <small class="d-flex justify-content-start flex-wrap">
                            <template x-for="game in games_data_already_saved">
                                <span
                                    class="me-3 border px-3 mb-2 rounded-pill py-2 border-secondary cursor-pointer me-2"
                                >
                                    <img
                                        width="25"
                                        height="25"
                                        :src="'/storage/' + game.image"
                                        class="object-fit-cover"
                                    > 
                                    <span x-text="game.name"> </span>                                        </span>
                            </template>
                        </small>
                        <br><br><br>

                    </div>
                </div>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Activity', 'outer-tab')">Activity</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Teams', 'outer-tab')">Teams</button>
        </div>
        <div class="tab-content pb-4  outer-tab" id="Overview">
            <br><br>
            <div class="d-flex justify-content-center"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center">
                @if (!isset($joinEvents[0]))
                    <p>No events available!</p>
                @else
                    <button class="carousel-button position-absolute" style="top: 100px; left: 20px;"
                        onclick="carouselWork(-2)">
                        &lt;
                    </button>
                    <button class="carousel-button position-absolute" style="top: 100px; right: 20px;"
                        onclick="carouselWork(2)">
                        &gt;
                    </button>
                    @if (!isset($joinEvents[1]))
                        <div class="d-flex justify-content-center event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @else
                        <div class="event-carousel-styles event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @endif
                 
                @endif
            </div>

            <div class="team-info">
                <div class="showcase">
                    <div><b>Showcase</b></div>
                    <br>
                    <div @class(["showcase-box d-none-until-hover-parent" , 
                            "d-flex justify-content-between flex-wrap" => !isset($awardList[2])
                    ])>
                        <div>
                            <p>Events Joined: {{ $totalEventsCount }}</p>
                            <p>Wins: {{ $wins }}</p>
                            <p>Win Streak: {{ $streak }}</p>
                        </div>
                        <div class="d-none-until-hover">
                            <div class="d-flex justify-content-between w-100 h-100">
                                @foreach ($awardList as $award)
                                    <div>
                                        <img src="{{ '/' . 'storage/' . $award->awards_image }} " alt="Trophy" class="trophy me-2">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements">
                    <div><b>Achievements</b></div>
                    @if (!isset($achievementList[0]))
                        <ul class="achievement-list mt-4">
                            <p>No achievements available</p>
                        </ul>
                    @else
                        <ul class="achievement-list">
                            @foreach ($achievementList as $achievement)
                                <li>
                                    <span class="additional-text d-flex justify-content-between">
                                        <span>
                                        {{ $achievement->title }} ({{ \Carbon\Carbon::parse($achievement->created_at)->format('Y') }})
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                        </svg>
                                    </span><br>
                                    <span class="ps-2"> {{ $achievement->description }} </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Activity">
            <br>
            <div class="tab-size"><b>New</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$user->id" :duration="'new'"> </livewire>
            <div class="tab-size"><b>Recent</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$user->id" :duration="'recent'"> </livewire>
            <div class="tab-size"><b>Older</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$user->id" :duration="'older'"> </livewire>
            
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Events">
             <br>
            <div class="tab-size"><b>Active Events</b></div>
            <br>
            @if (!isset($joinEventsActive[0]))
                <p class="tab-size">
                    This profile has no active events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br>
            <div class="tab-size"><b>Past Events</b></div>
            <br>
            @if (!isset($joinEventsHistory[0]))
                <p class="tab-size">
                    This profile have no past events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)
                        @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Teams">
             <br>
            <div class="tab-size"><b>Current Teams</b></div>
            @if (isset($teamList[0]))
                <table class="member-table">
                    <thead>
                        <tr>
                            <th> </th>
                            <th>Team name</th>
                            <th>Region</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teamList as $team)
                            <tr class="st">
                                <td> </td>
                                <td class="d-flex align-items-center">
                                    <img
                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/'. $team->teamBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} 
                                        height="40"
                                        width="40"
                                    > 
                                    <span>{{$team->teamName}}</span>
                                </td>
                                <td>China</td>
                                <td>{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No current teams</div>
            @endif
            <br> <br>
            <div class="tab-size"><b>Past Teams</b></div>

            @if (isset($pastTeam[0]))
                <table class="member-table">
                    <thead>
                        <tr>
                            <th> </th>
                            <th>Team name</th>
                            <th>Region</th>
                            <th>Members</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastTeam as $team)
                            <tr class="st">
                                <td> </td>
                                <td class="d-flex align-items-center">
                                    <img
                                        class="d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/'. $team->teamBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} 
                                        height="40"
                                        width="40"
                                    > 
                                    <span>{{$team->teamName}}</span>
                                </td>
                                <td>China</td>
                                <td>{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No past teams</div>
            @endif
        </div>


    </main>


</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@livewireScripts
<script>

    // const games_data = {{ $userProfile->participant->games_data }};
    window.onload = () => { loadMessage(); }
    document.addEventListener('alpine:init', () => {
        let gamesDataInput = document.getElementById('games_data_input');
        let gamesData = JSON.parse(gamesDataInput.value.trim()); 
        console.log({gamesData})
        Alpine.data('alpineDataComponent', () => {
        return  {
            isEditMode: true, 
            selectedGame: null,
            isAddGamesMode: true,
            games_data: gamesData,
            games_data_already_saved: gamesData,
            countries: 
            [
                {
                    name: { en: 'No country' },
                    emoji_flag: '𓆝'
                }
            ], 
            games: [
                {
                    id: null,
                    name: 'No game',
                    image: null
                }
            ],
            participant: {
                id: {{ $userProfile->participant->id }},
                nickname : '{{$userProfile->participant->nickname}}',
                bio: '{{$userProfile->participant->bio}}',
                birthday: '{{$userProfile->participant->birthday}}',
                domain: '{{$userProfile->participant->domain}}',
                region: '{{$userProfile->participant->region}}',
            },
            errorMessage: null, 
            isCountriesFetched: false ,
            async fetchCountries () {
                if (this.isCountriesFetched) return;
                return fetch('/countries')
                    .then(response => response.json())
                    .then(data => {
                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.countries = data?.data;
                        } else {
                            this.errorMessage = "Failed to get data!"
                            this.countries = [{
                                name: {
                                    en: 'No country'
                                },
                                emoji_flag: ' 𓆝 𓆟 𓆞 𓆝 𓆟 '
                            }];
                        }
                    })
                    .catch(error => console.error('Error fetching countries:', error));
            },
            async fetchGames () {
                if (this.isCountriesFetched) return;
                return fetch('/games')
                    .then(response => response.json())
                    .then(data => {
                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.games = data?.data;
                            this.select2 = $(this.$refs.select).select2({
                                minimumResultsForSearch: Infinity,
                                data: data.data,
                                templateResult: function (_game) {
                                    return $("<span><img class='object-fit-cover' width='25' height='25' src='/storage/" + _game.image +"'/> " + _game.name + "</span>");
                                },
                                templateSelection: function (_game) {
                                    return $("<span><img class='object-fit-cover' width='25' height='25' src='/storage/" + _game.image +"'/> " + _game.name + "</span>");
                                },
                                theme: "bootstrap-5",
                            }); 

                            console.log({select2: this.select2[0] })
                            console.log({select2: this.select2[0] })
                            console.log({select2: this.select2[0] })
                            console.log({select2: this.select2[0] })
                            console.log({select2: this.select2[0] })
                            this.select2[0].classList.add('mb-2');
                            
                            this.select2.on('select2:select', (event) => {
                                this.selectedGame = event.target.value;
                                console.log({
                                    games_data: this.games_data, 
                                    games: this.games,
                                    value: event.target.value, 
                                    selectedGame: this.selectedGame
                                })
                                const gameIndex = this.games.findIndex(game => game.id == this.selectedGame);

                                if (gameIndex !== -1) {
                                    this.games_data = [...this.games_data, this.games[gameIndex]];
                                    console.log(`Game "${data.name}" added successfully.`);
                                } else {
                                    console.log(`Game "${data.name}" already exists.`);
                                }

                                this.isAddGamesMode = false;
                                this.select2[0].classList.add('d-none');
                            });
                             this.$watch("isAddGamesMode", (value) => {
                                console.log({value})
                            });
                            this.$watch("selectedGame", (value) => {
                                this.select2.val(value).trigger("change");
                            });
                        } else {
                            this.errorMessage = "Failed to get data!"
                        }
                    })
                    .catch(error => console.error('Error fetching countries:', error));
            },
            // select2Alpine() {
            // },

            async submitEditProfile (event) {
                try {
                    event.preventDefault(); 
                    const url = event.target.dataset.url; 
                    this.participant.age = Number(this.participant.age);
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: window.loadBearerCompleteHeader(),
                        body: JSON.stringify({
                            ...Alpine.raw(this.participant),
                            games_data: JSON.stringify(this.games_data),
                        }),
                    });             
                    const data = await response.json();
                        
                    if (data.success) {
                        let currentUrl = window.location.href;
                        if (currentUrl.includes('?')) {
                            currentUrl = currentUrl.split('?')[0];
                        } 

                        localStorage.setItem('success', true);
                        localStorage.setItem('message', data.message);
                        // window.location.replace(currentUrl);
                    } else {
                        this.errorMessage = data.message;
                    }
                } catch (error) {
                    this.errorMessage = error.message;
                    console.error({error});
                } 
            },
            deleteGames (event) {
                
                console.log({participant: this.participant, _games_data: this.games_data})
                /*
               
                */
                
            }
        }})
    })

    const uploadButton = document.getElementById("upload-button");
    const imageUpload = document.getElementById("image-upload");
    const uploadedImage = document.getElementById("uploaded-image");
    const backgroundInput = document.getElementById("backgroundInput");
    const backgroundBanner = document.getElementById("backgroundBanner")
    uploadButton?.addEventListener("click", function() {
        imageUpload.click();
    });

     backgroundInput?.addEventListener("change", async function(e) {
        const file = e.target.files[0];

        try {
            const fileContent = await readFileAsBase64(file);
            const url = "{{ route('participant.userBackground.action', ['id' => $userProfile->id] ) }}";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-type': 'application/json',
                    'Accept': 'application/json',
                    ...window.loadBearerHeader()
                },
                body: JSON.stringify({
                    file: {
                        filename: file.name,
                        type: file.type,
                        size: file.size,
                        content: fileContent
                        }
                    }),
                });

               
                const data = await response.json();
                    
                if (data.success) {
                    backgroundBanner.style.backgroundImage = `url(${data.data.fileName})`;
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('There was a problem with the request:', error);
        }
    });

     imageUpload?.addEventListener("change", async function(e) {
        const file = e.target.files[0];

        try {
            const fileContent = await readFileAsBase64(file);
            const url = "{{ route('participant.userBanner.action', ['id' => $userProfile->id] ) }}";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-type': 'application/json',
                    'Accept': 'application/json',
                    ...window.loadBearerHeader()
                },
                body: JSON.stringify({
                    file: {
                        filename: file.name,
                        type: file.type,
                        size: file.size,
                        content: fileContent
                        }
                    }),
                });

                
                const data = await response.json();
                    
                if (data.success) {
                    uploadedImage.style.backgroundImage = `url(${data.data.fileName})`;
                } else {
                    console.error('Error updating member status:', data.message);
                }
            } catch (error) {
                console.error('There was a problem with the file upload:', error);
        }
    });

    async function readFileAsBase64(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = function(event) {
                const base64Content = event.target.result.split(';base64,')[1];
                resolve(base64Content);
            };

            reader.onerror = function(error) {
                reject(error);
            };

            reader.readAsDataURL(file);
        });
    }

    function reddirectToLoginWithIntened(route) {
        route = encodeURIComponent(route);
        let url = "{{ route('participant.signin.view') }}";
        url += `?url=${route}`;
        window.location.href = url;
    }

    function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
        const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
        tabContents.forEach(content => {
            content.classList.add("d-none");
        });
        console.log({
            tabContents
        });

        const selectedTab = document.getElementById(tabName);
        selectedTab.classList.remove('d-none');
        selectedTab.classList.add('tab-button-active');
        console.log({
            selectedTab
        });

        const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
        tabButtons.forEach(button => {
            button.classList.remove("tab-button-active");
        });
        console.log({
            tabButtons
        });

        let target = event.currentTarget;
        target.classList.add('tab-button-active');
    }

    let currentIndex = 0;

    function carouselWork(increment = 0) {
        const eventBoxes = document.querySelectorAll('.event-carousel-works > div');
        let boxLength = eventBoxes?.length || 0;
        let newSum = currentIndex + increment;
        if (newSum >= boxLength || newSum < 0) {
            return;
        } else {
            currentIndex = newSum;
        }

        // carousel top button working
        const button1 = document.querySelector('.carousel-button:nth-child(1)');
        const button2 = document.querySelector('.carousel-button:nth-child(2)');
        if (button1 && button2) {
            button1.style.opacity = (currentIndex <= 2) ? '0.4' : '1';
            button2.style.opacity = (currentIndex >= boxLength - 2) ? '0.4' : '1';

            // carousel swing
            for (let i = 0; i < currentIndex; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }

            for (let i = currentIndex; i < currentIndex + 2; i++) {
                eventBoxes[i]?.classList.remove('d-none');
            }

            for (let i = currentIndex + 2; i < boxLength; i++) {
                eventBoxes[i]?.classList.add('d-none');
            }
        }
    }

    carouselWork();


    function redirectToProfilePage(userId) {
        window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }

   
</script>

</html>
