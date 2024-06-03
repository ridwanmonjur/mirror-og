<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">

    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/lightgallery.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

</head>
@php
    use Carbon\Carbon;
    $isUserSame = false;

    // dd($userProfile->participant);
@endphp
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }

        $isUserSame = $user?->id == $userProfile->id;
    @endphp
@endauth
<body>
    @include('CommonPartials.NavbarGoToSearchPage')

    <main x-data="alpineDataComponent">
        @include('Participant.Profile.Forms')
        <div id="backgroundBanner" class="member-section px-2 pt-2"
            style="background-image: url({{ '/storage' . '/'. $userProfile->participant->backgroundBanner }} );
                background-size: cover; background-repeat: no-repeat;"
        >
            @if(!$isOwnProfile) <br> @endif
            <input type="hidden" id="games_data_input" value="{{ $userProfile->participant->games_data ?? json_encode([]) }}">
            <input type="hidden" id="region_details_input" value="{{ json_encode($userProfile->participant->getRegionDetails()) }}">
            @if($isOwnProfile)
                <div class="d-flex justify-content-end py-0 my-0 mb-2">
                    <input type="file" id="backgroundInput" class="d-none"> 
                    <button 
                        onclick="document.getElementById('backgroundInput').click();"
                        class="btn btn-secondary text-light rounded-pill py-2 me-3 fs-7"> 
                        Change Background
                    </button>
                    <button 
                        x-cloak
                        x-show="!isEditMode"
                        x-on:click="isEditMode = true; fetchCountries(); fetchGames();"
                        class="oceans-gaming-default-button oceans-gaming-primary-button px-3 py-2 fs-7"> 
                        Edit Profile
                    </button>
                    <button 
                        x-cloak
                        x-show="isEditMode"
                        x-on:click="submitEditProfile(event)"
                        data-url="{{route('participant.profile.update')}}"
                        class="oceans-gaming-default-button oceans-gaming-transparent-button px-3 py-2 me-3 fs-7"> 
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
                                >
                                </div>
                                <div class="d-flex flex-column align-items-center justify-content-center upload-button pt-3">
                                    <a aria-hidden="true" data-fslightbox href="{{ '/' . 'storage/' . $userProfile->userBanner }}">
                                        <button class="btn btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                        </svg> 
                                        </button>
                                    </a>    
                                    @if ($isUserSame)
                                        <button id="upload-button" class="btn btn-sm" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @if ($isUserSame)
                            <input type="file" id="image-upload" accept="image/*" style="display: none;">
                        @endif
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
                                    default="1999-05-05"
                                    id="birthdate"
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
                                    <template x-if="countries">
                                        <select
                                            id="region_select_input"
                                            x-model="participant.region" 
                                            x-on:change="changeFlagEmoji"
                                            style="width: 150px;"    
                                            class="form-control d-inline rounded-pill"
                                        >
                                            <template x-for="country in countries">
                                                <option 
                                                    :value="country.id" 
                                                    :selected="country.id==participant.region">
                                                <span x-text="country.emoji_flag" class="mx-3"> </span>  
                                                <span x-text="country.name.en"> </span>
                                                </option>
                                            </template>
                                        </select> 
                                    </template>
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
                                <div class="d-flex justify-content-start flex-wrap">
                                    <template x-for="game in games_data">
                                        <span
                                            x-on:click="deleteGames(game.id)"
                                            class="me-3 border px-3 mb-2 rounded-pill py-2 border-secondary cursor-pointer me-2"
                                        >
                                            <img
                                                width="25"
                                                height="25"
                                                :src="'/storage/' + game.image"
                                                class="object-fit-cover"
                                            > 
                                            <span class="me-3" x-text="game.name"> </span> 
                                             {{-- Close icon --}}
                                            <span class="mt-2"> 
                                                <svg 
                                                    x-on:click="deleteGames(game.id)"
                                                    xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle cursor-pointer align-middle position-relative" viewBox="0 0 16 16">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                </svg>
                                            </span>      
                                        </span>
                                    </template>
                                    <select
                                        x-cloak
                                        x-ref="select" 
                                        x-show.important="isAddGamesMode"
                                        style="width: 200px;"    
                                        data-trigger="true"
                                        class="selectpicker form-control d-inline rounded-pill"
                                    >
                                    </select> 
                                </div>
                            </div>
                            <br><br>
                            <br>
                        </div>
                    <div x-cloak x-show="!isEditMode" class="ms-2">
                        <template x-if="participant.nickname">
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                <h4 class="my-0 me-4" x-text="participant.nickname"></h4>
                                @include('Participant.Profile.FriendManagement')
                            </div>
                        </template>
                        <template x-if="!participant.nickname">
                             <div class="d-flex justify-content-start align-items-center flex-wrap">
                                <h4 class="my-0 me-4"> {{$userProfile->name}} </h4>
                                @include('Participant.Profile.FriendManagement')
                            </div>
                        </template>
                        <div>
                            <template x-if="participant.nickname">
                                <span> {{$userProfile->name}},</span>
                            </template> 
                          
                            <template x-if="participant.birthday">
                                <span>
                                    <span class="me-4" x-text="participant.age"></span>
                                    {{-- Calendar --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                    </svg>
                                    <span x-text="participant.birthday"></span>
                                </span>
                            </template>
                        </div>
                        
                        <template x-if="participant.bio">
                            <p x-text="participant.bio"> </p>
                        </template>
                        <template x-if="!participant.bio">
                            <p> This is the player's bio. There will be a minimum of 150 characters. 
                                This field will accept emojis.  
                            </p>
                        </template>
                        <div class="d-flex justify-content-start flex-wrap w-100">
                            <template x-if="participant.region_name">
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill me-2" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <span class="me-1" x-html="participant.region_name"></span>
                                </span>
                            </template>
                              <template x-if="!participant.region">
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill me-2" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <span class="me-1" x-text="'Add a country'"></span>
                                </span>
                            </template>
                            <template x-if="participant.domain">
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg me-2" viewBox="0 0 16 16">
                                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                    </svg>
                                    <span class="me-1" x-text="participant.domain"></span>
                                </span>
                            </template>
                            <template x-if="!participant.domain">
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg me-2" viewBox="0 0 16 16">
                                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                    </svg>
                                    <span class="me-1" x-text="'Add a domain'"></span>
                                </span>
                            </template>
                            <span class="me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                                <span>Joined {{Carbon::parse($userProfile->participant->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                            </span>
                        </div>
                        <br>
                        <div class="d-flex justify-content-start align-items-center flex-wrap">
                            <template x-for="(game, idx) in games_data" :key="game.id">
                                <span
                                    :class="{ 'd-none': ( games_data[3] && idx >= 3 ) }"
                                    class="me-3 border px-3 mb-2 rounded-pill py-2 border-secondary cursor-pointer me-2 show-first-few"
                                >
                                    <img
                                        width="25"
                                        height="25"
                                        :src="'/storage/' + game.image"
                                        class="object-fit-cover"
                                    > 
                                    <span x-text="game.name"> </span> 
                                                                     
                                </span>
                            </template>
                            <span :class="{ 'd-none': !games_data[3] }"
                                onclick="visibleElements()"
                                class="show-more cursor-pointer"><u>Show more</u></span>
                        </div>
                        <template x-if="games_data">
                        <br><br><br>
                        </template>

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
            <livewire:participant.profile.show-activity-logs :userId="$userProfile->id" :duration="'new'"> </livewire>
            <div class="tab-size"><b>Recent</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$userProfile->id" :duration="'recent'"> </livewire>
            <div class="tab-size"><b>Older</b></div>
            <livewire:participant.profile.show-activity-logs :userId="$userProfile->id" :duration="'older'"> </livewire>
            
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
                                <td style="font-size: 25px;">{{$team->country_flag}}</td>
                                <td>{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No current teams</div>
            @endif
            <br> 
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
                                <td style="font-size: 25px;">{{$team->country_flag}}</td>
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
@include('Participant.Profile.Scripts')
</html>
