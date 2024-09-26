<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @include('__CommonPartials.HeadIcon')

    @vite([
        'resources/sass/app.scss', 
        'resources/js/app.js', 
        'resources/js/alpine.js', 
        'resources/js/lightgallery.js',
        'resources/sass/lightgallery.scss',   
        'resources/js/file-upload-preview.js',
        'resources/sass/file-upload-preview.scss',
        'resources/js/file-edit.js',
        'resources/sass/file-edit.scss',
        'resources/js/colorpicker.js',
        'resources/sass/colorpicker.scss',
    ])
    <link rel="stylesheet" href="{{ asset('/assets/css/chat/inpage-message.css') }}">

</head>
@php
    use Carbon\Carbon;
    $isUserSame = false;

    [   
        'backgroundStyles' => $backgroundStyles, 
        'fontStyles' => $fontStyles, 
        'frameStyles' => $frameStyles
    ] = $userProfile->profile?->generateStyles();
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
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main x-data="alpineDataComponent">
        @include('Participant.__ProfilePartials.BackgroundModal')

        @include('Participant.__ProfilePartials.Forms')
        <div id="backgroundBanner" class="member-section px-2 pt-2"
            @style([
                "background-size: cover; background-repeat: no-repeat;"
            ])
        >
            @if($isOwnProfile) 
                <input type="hidden" id="userBannerInput" value="{{ $userProfile->userBanner }}">
                <input type="hidden" id="backgroundColorInput" value="{{ $userProfile->profile?->backgroundColor }}">
                <input type="hidden" id="fontColorInput" value="{{ $userProfile->profile?->fontColor }}">

            @endif
            <input type="hidden" id="games_data_input" value="{{ $userProfile->participant?->games_data ?? json_encode([]) }}">
            <input type="hidden" id="region_details_input" value="{{ json_encode($userProfile->participant?->getRegionDetails()) }}">
            <input type="hidden" id="initialUserData" value="{{json_encode($userProfile?->only(["id", "name"]))}}">
            <input type="hidden" id="initialParticipantData" value="{{json_encode($userProfile->participant)}}">
                <div class="d-flex justify-content-end py-0 my-0 mb-2 flex-wrap">
                    @if ($isUserSame)
                    <input type="file" id="backgroundInput" class="d-none"> 
                    <button 
                        data-bs-toggle="offcanvas"
                        data-bs-target="#profileDrawer"
                        x-on:click="isEditMode=false"
                        x-cloak
                        x-show="!isEditMode"
                        {{-- onclick="document.getElementById('backgroundInput').click();" --}}
                        class="btn btn-secondary text-light rounded-pill py-2 me-3 fs-7"
                    > 
                        Change Background
                    </button>
                    <button 
                        x-cloak
                        x-show="!isEditMode"
                        x-on:click="reset(); isEditMode = true;"
                        class="oceans-gaming-default-button oceans-gaming-primary-button px-3 py-2 fs-7"> 
                        Edit Profile
                    </button>
                    <a 
                        x-cloak
                        x-show="isEditMode"
                        x-on:click="submitEditProfile(event)"
                        data-url="{{route('participant.profile.update')}}"
                        class="oceans-gaming-default-button oceans-gaming-transparent-button btn simple-button cursor-pointer px-3 py-2 me-3 fs-7"> 
                        Save
                    </a>
                    {{-- Close icon --}}
                    <svg 
                        x-cloak
                        style="top: 10px; color: black;"
                        x-show="isEditMode"
                        x-on:click="isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-circle cursor-pointer align-middle position-relative" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                    @endif
                @if (!$isOwnProfile && !$isUserSame)
                    @include('Participant.__ProfilePartials.FriendManagement')
                @endif
            </div>
            <div class="d-flex justify-content-center align-items-start flex-wrap">
                <div class="member-image align-middle">
                    <div class="upload-container">
                        <label class="upload-label">
                            <div class="circle-container">
                                <div class="uploaded-image"
                                    style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }} ); background-size: cover; 
                                        z-index: 99; background-repeat: no-repeat; background-position: center; {{$frameStyles}}"
                                >
                                </div>
                                <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                                    <a aria-hidden="true" data-fslightbox="lightbox" href="{{ '/' . 'storage/' . $userProfile->userBanner }}" class="hyperlink-lightbox">
                                        <button class="btn btn-sm p-0 me-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                        </svg> 
                                        </button>
                                    </a>    
                                    @if ($isUserSame)
                                        <button x-cloak x-show="isEditMode"  id="upload-button2" class="btn btn-sm p-0" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </label>
                       
                    </div>
                </div>
                <div class="member-details">
                        <div x-show="errorMessage != null && isEditMode" class="text-red" x-text="errorMessage"> </div>
                        <div x-cloak x-show="isEditMode" style="color: black;">
                            <input 
                                placeholder = "Enter your nickname..."
                                style="width: 250px;"
                                autocomplete="off"
                                autocomplete="nope"
                                class="form-control border-secondary player-profile__input d-inline" 
                                x-model="participant.nickname" 
                            > 
                            <input type="file" id="image-upload" accept="image/*" x-cloak x-show="isEditMode" class="d-none">
                            <br>
                            <span class="d-inline-flex justify-content-between align-items-center">
                                <input
                                    placeholder = "Your name"
                                    style="width: 200px;"
                                    type="text"
                                    autocomplete="off"
                                    autocomplete="nope"
                                    class="form-control border-secondary player-profile__input d-inline me-3" 
                                    x-model="user.name" 
                                > 
                                <input 
                                    placeholder="Birthday"
                                    type="date"
                                    style="width: 150px;"
                                    default="1999-05-05"
                                    id="birthdate"
                                    class="form-control border-secondary player-profile__input d-inline me-2" 
                                    x-model="participant.birthday" 
                                >
                                <template x-if="!participant.isAgeVisible">
                                    {{-- Eye invisible icon --}}
                                    <svg 
                                        x-on:click="participant.isAgeVisible = true"
                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-slash-fill cursor-pointer" viewBox="0 0 16 16">
                                    <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                                    </svg>
                                </template>
                                <template x-if="participant.isAgeVisible">
                                    {{-- Eye visible --}}
                                    <svg  
                                        x-on:click="participant.isAgeVisible = false" 
                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill cursor-pointer" viewBox="0 0 16 16">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                    </svg>
                                </template>
                            </span> 
                            <br>
                                 <input
                                    placeholder = "Write a description"
                                    style="width: 370px;"
                                    type="text"
                                    autocomplete="off"
                                    autocomplete="nope"
                                    class="form-control border-secondary player-profile__input d-inline me-3" 
                                    x-model="participant.bio" 
                                > 
                            <br> <br>
                            <div class="w-100 d-flex justify-content-start align-items-center flex-wrap">
                                <span class="me-3 d-flex justify-content-center align-items-center">
                                    <svg
                                        class="me-2 align-middle" 
                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                        <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <template x-if="countries">
                                        <select x-on:change="changeFlagEmoji" id="select2-country3" style="width: 150px;" class="d-inline form-control"  data-placeholder="Select a country" x-model="participant.region"> 
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
                                        autocomplete="off"
                                        autocomplete="nope"
                                        class="form-control border-secondary player-profile__input d-inline" 
                                        x-model="participant.domain"
                                    > 
                                </span>
                                <br> <br> <br> 
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
                            <br>
                        </div>
                    <div x-cloak x-show="!isEditMode" class="ms-2">
                        <br>
                        @if($userProfile->participant?->nickname)           
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                <h4 class="my-0 me-4">{{$userProfile->participant->nickname}}</h4>
                            </div>
                        @else
                            <div class="d-flex justify-content-start align-items-center flex-wrap">
                                <h4 class="my-0 me-4">{{$userProfile->name}}</h4>
                            </div>
                        @endif
                        <div class="my-2">
                            @if($userProfile->participant?->nickname)
                                <span>{{ $userProfile->name }}</span>
                            @endif
                            @if($userProfile->participant?->birthday && $userProfile->participant->nickname && $userProfile->participant?->isAgeVisible && $userProfile->participant->age)
                                <span style="margin-left: -5px;">,</span>
                            @endif
                          
                            @if ($userProfile->participant?->birthday)
                                <span>
                                    @if($userProfile->participant?->isAgeVisible)
                                        <span>{{$userProfile->participant->age}}</span>
                                    @endif
                                    {{-- Calendar --}}
                                    <svg 
                                        @class(['ms-4' => $userProfile->participant->age && $userProfile->participant->isAgeVisible || $userProfile->participant->nickname]) 
                                        xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16"
                                    >
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                                    </svg>
                                    @if ($userProfile->participant?->birthday)
                                        <span class="me-2">{{$userProfile->participant->birthday}}</span>
                                    @endif
                                    @if ($isOwnProfile)                         
                                        @if ($userProfile->participant?->isAgeVisible)
                                           {{-- Eye visible icon --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                            </svg>
                                        @else
                                            {{-- Eye invisible icon --}}
                                            <svg 
                                                xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
                                            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
                                            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
                                            </svg>
                                         @endif
                                    @endif
                                </span>
                            @endif
                        </div>
                        
                        @if($userProfile->participant?->bio)
                            <p>{{ $userProfile->participant->bio }}</p>
                        @endif

                        <div class="d-flex justify-content-start flex-wrap w-100">
                            @if($userProfile->participant?->region_name)
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill me-2" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                    </svg>
                                    <span class="me-1">{{$userProfile->participant->region_name}}</span>
                                </span>
                            @endif
                            @if($userProfile->participant?->domain)
                                <span class="me-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-link-45deg me-2" viewBox="0 0 16 16">
                                    <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
                                    <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
                                    </svg>
                                    <span class="me-1">{{$userProfile->participant->domain}}</span>
                                </span>
                            @endif
                            <span class="me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person me-2" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                                <span>Joined {{Carbon::parse($userProfile->participant->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                            </span>
                        </div>
                        <div class="mt-4 my-2 text-wrap">
                            @livewire('shared.profile.participant-friends-follows-count', [
                                'userId' => $userProfile->id, 'name' => 'follower' 
                            ])
                            @livewire('shared.profile.participant-friends-follows-count', [
                                'userId' => $userProfile->id, 'name' => 'followee' 
                            ])
                            @livewire('shared.profile.participant-friends-follows-count', [
                                'userId' => $userProfile->id, 'name' => 'friend' 
                            ])
                        </div>
                        <br><br>

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
                    <div @class(["event-carousel-works", 
                        "event-carousel-styles" => isset($joinEvents[1]),
                        "d-flex justify-content-center " => !isset($joinEvents[1])
                    ])
                    >
                        @foreach ($joinEvents as $key => $joinEvent)
                            @include('Participant.__Partials.RosterView',  ['isRegistrationView' => false])
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="row px-4">
                <div class="showcase col-12 col-lg-6">
                    <div><b>Showcase</b></div>
                    <br>
                    <div class="showcase-box d-none-until-hover-parent row">
                        <div @class([
                            "col-6",
                            "col-12" => isset($awardList[2])
                        ])>
                            <p>Events Joined: {{ $totalEventsCount }}</p>
                            <p>Wins: {{ $wins }}</p>
                            <p>Win Streak: {{ $streak }}</p>
                        </div>
                        <div @class([
                            "d-none-until-hover",
                            "col-6",
                            "col-12" => isset($awardList[2])
                        ])>
                            <div class="d-flex justify-content-between">
                                @foreach ($awardList as $award)
                                    <div>
                                        <img src="{{ '/' . 'storage/' . $award->awards_image }} " width="100" height="100" alt="Trophy" class="me-2">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="achievements  col-12 col-lg-6">
                    <div><b>Achievements</b></div><br>
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
                <br><br><br>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Participant.__Partials.RosterView', ['isRegistrationView' => false])
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
                        @include('Participant.__Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tab-content pb-4  outer-tab d-none" id="Teams">
             <br>
            <div class="tab-size"><b>Current Teams</b></div>
            <div class="tab-size pt-4">
            @if (isset($teamList[0]))
                <table id="current_teams" class="member-table responsive  ">
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
                                <td style="width: 60px !important;" class="py-0 px-0 mx-0"> 
                                    <a href="{{route('public.team.view', ['id' => $team->id])}}"> 
                                         <svg class="gear-icon-btn"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path
                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                    </a>
                                </td>
                                <td class="d-flex align-items-center colored-cell">
                                    <img
                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/'. $team->teamBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} 
                                        height="40"
                                        width="40"
                                    > 
                                    <span>{{$team->teamName}}</span>
                                </td>
                                <td style="font-size: 1.5rem;" class="colored-cell">{{$team->country_flag}}</td>
                                <td class="colored-cell">{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No current teams</div>
            @endif
            </div>
            <br> 
            <div class="tab-size"><b>Past Teams</b></div>
            <div class="tab-size pt-4">
            @if (isset($pastTeam[0]))
                <table id="past_teams" class="member-table responsive  ">
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
                                <td style="width: 60px !important;" class="py-0 px-0 mx-0"> 
                                    <a href="{{route('public.team.view', ['id' => $team->id])}}"> 
                                         <svg class="gear-icon-btn"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                            class="bi bi-eye-fill" viewBox="0 0 16 16">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path
                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                    </a>
                                </td>
                                <td class="d-flex align-items-center colored-cell">
                                    <img
                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                        src="{{ '/storage' . '/'. $team->teamBanner }}"
                                        {!! trustedBladeHandleImageFailure() !!} 
                                        height="40"
                                        width="40"
                                    > 
                                    <span>{{$team->teamName}}</span>
                                </td>
                                <td style="font-size: 1.5rem;" class="colored-cell">{{$team->country_flag}}</td>
                                <td class="colored-cell">{{$team->members_count}}/5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="tab-size pt-3">No past teams</div>
            @endif
            </div>
        </div>

    </main>

    <script>
    
    </script>
</body>
@include('Participant.__ProfilePartials.Scripts')
@livewireScripts
</html>
