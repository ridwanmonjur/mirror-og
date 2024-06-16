<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Profile Page</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@22.0.2/build/css/intlTelInput.css">
        <link href="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/styles/choices.min.css" rel="stylesheet">
    @vite(['resources/sass/app.scss', 
        'resources/js/app.js', 
        'resources/js/lightgallery.js',   
        'resources/js/file-upload-preview.js',
        'resources/sass/file-upload-preview.scss',
        'resources/js/colorpicker.js',
        'resources/sass/colorpicker.scss'
    ])
</head>
@php
    use Carbon\Carbon;
    $isUserSame = false;
@endphp
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }

        $isUserSame = $user->id == $userProfile->id;
        [   
            'backgroundStyles' => $backgroundStyles, 
            'fontStyles' => $fontStyles, 
            'frameStyles' => $frameStyles
        ] = $userProfile->profile->generateStyles();
    @endphp
@endauth
<body>
    @include('Organizer.Profile.BackgroundModal')
    @include('CommonPartials.NavbarGoToSearchPage')
    <main 
        x-data="alpineDataComponent"
    >
        {{-- <form action="{{route('organizer.profile.update')}}" method="POST">  --}}
        <div>
            <div id="backgroundBanner" class="member-section px-2 pt-2"
                @style([
                    "background-size: cover; background-repeat: no-repeat;"
                ])
            > 
                <br>
                <div class="member-image">
                    <div class="upload-container align-items-center">
                        <label for="image-upload" class="upload-label">
                            <div class="circle-container">
                                <div id="uploaded-image" class="uploaded-image"
                                    style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }}  ); background-size: cover; 
                                        z-index: 99; background-repeat: no-repeat; background-position: center; {{$frameStyles}}"
                                >
                                </div>
                                <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                                    <a aria-hidden="true" data-fslightbox href="{{ '/' . 'storage/' . $userProfile->userBanner }}">
                                        <button class="btn btn-sm p-0 me-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                        </svg> 
                                        </button>
                                    </a>    
                                    @if ($isUserSame)
                                        <button id="upload-button" class="btn btn-sm p-0" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
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
                <div class="member-details mx-auto text-center">
                    <div x-cloak x-show="isEditMode" class="pb-3">
                        <div x-show="errorMessage != null" class="text-red" x-text="errorMessage"> </div>
                        <input 
                            placeholder = "Enter your name..."
                            style="width: 250px;"
                            name="name"
                            class="form-control border-secondary player-profile__input d-inline" 
                            x-model="userProfile.name"
                        >
                        <br>
                        <input 
                            placeholder = "Enter your company name..."
                            style="width: 300px;"
                            class="form-control border-secondary player-profile__input d-inline me-3" 
                            x-model="organizer.companyName"
                        > 
                        <button 
                            type="submit"
                            data-url="{{route('organizer.profile.update')}}"
                            x-on:click="submitEditProfile(event);"
                            style="border-color: green;"
                            class="mt-4 oceans-gaming-default-button oceans-gaming-transparent-button px-3 py-1 me-2 text-success"> 
                            Save
                        </button>
                        <svg 
                        {{-- Close icon --}}
                        x-on:click="isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-circle cursor-pointer text-red" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        <br>
                    </div>
                    <div x-cloak x-show="!isEditMode">
                        <h5>
                            {{$userProfile->name}}
                        </h5>
                        <p> 
                            <span class="me-2"> </span>
                            <span class="me-3" x-text="organizer.industry"> </span>
                            <span class="me-1"> 📧 </span>
                            <span @class(["followCounts". $userProfile->id, ' me-3']) data-count="{{ $followersCount }}"> {{$followersCount}} follower{{bladePluralPrefix($followersCount)}} </span>
                        </p>
                        @if ($isOwnProfile)
                            <div class="text-center">
                                <button 
                                    x-on:click="isEditMode = true;"
                                    class="oceans-gaming-default-button oceans-gaming-primary-button py-1 px-5 me-3"> 
                                    Edit
                                </button>
                                <button 
                                    data-bs-toggle="modal"
                                    data-bs-target="#profileModal"
                                    {{-- onclick="document.getElementById('backgroundInput').click();" --}}
                                    class="btn btn-secondary text-light rounded-pill py-1 me-3 px-3"
                                > 
                                    Change Background
                                </button>
                            </div>
                        @else
                            <div class="text-center">
                                <form id="followFormProfile" method="POST"
                                    class="d-inline me-3"
                                    action="{{ route('participant.organizer.follow') }}">
                                    @csrf
                                    @auth
                                        <input type="hidden" name="role"
                                            value="{{ $user && $user->id ? $user->role : '' }}">
                                        <input type="hidden" name="user_id"
                                            value="{{ $user && $user->id ? $user->id : '' }}">
                                        <input type="hidden" name="organizer_id"
                                            value="{{ $userProfile?->id }}">
                                    @endauth
                                    @guest
                                        <button type="button"
                                            @class(["rounded px-3 py-2 ", 'followButton'. $userProfile->id])
                                            onclick="reddirectToLoginWithIntened('{{route('public.organizer.view', ['id'=> $userProfile->id])}}')"
                                            id="followButton"
                                            style="background-color: #43A4D7; color: white; border: none;">
                                            Follow
                                        </button>
                                    @endguest
                                    @auth
                                        @if ($user->role == 'PARTICIPANT')
                                            <button type="submit" id="followButton"
                                                @class(["rounded px-3 py-2 ", 'followButton'. $userProfile->id])
                                                style="background-color: {{ $user && $userProfile->isFollowing ? '#8CCD39' : '#43A4D7' }}; color: {{ $user && $userProfile->isFollowing ? 'black' : 'white' }}; border: none;">
                                                {{ $user && $userProfile->isFollowing ? 'Following' : 'Follow' }}
                                            </button>
                                        @else
                                            <button type="button"
                                                @class(["rounded px-3 py-2 ", 'followButton'. $userProfile->id])
                                                onclick="toastWarningAboutRole(this, 'Participants can follow only!');"
                                                id="followButton"
                                                style="background-color: #43A4D7; color: white; border: none;">
                                                Follow
                                            </button>
                                        @endif
                                    @endauth
                                </form>
                                <button 
                                    x-on:click="isEditMode = false;"
                                    class="oceans-gaming-default-button oceans-gaming-transparent-button bg-light border-0 rounded px-3 py-2"> 
                                    Message
                                </button>
                            </div>
                        @endif
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <div class="tabs px-5" x-cloak x-show="!isEditMode">
            <button class="tab-button  outer-tab tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
        </div>
        <div x-cloak x-show="!isEditMode" class="tab-content pb-4 outer-tab px-5" id="Overview">
            <br> 
            <div class="showcase tab-size showcase-box showcase-column pt-4 grid-4-columns text-center" style="width: max(500px, 55vw);">
                <div> 
                    <h3> {{$lastYearEventsCount}} </h3>
                    <p> Events Organized in Last Year </p>
                </div>
                 <div> 
                    <h3> {{$beforeLastYearEventsCount + $lastYearEventsCount}} </h3>
                    <p> Events Organized Across All Time </p>
                </div>
                 <div> 
                    <h3> {{$teamsCount}} </h3>
                    <p> Teams Registered Across All Time </p>
                </div>
                 <div> 
                    <h3> {{$tierPrizeCount}} </h3>
                    <p> Total Prize Pool Across All Time </p>
                </div>
            </div>
            <br><br>
            <div class="tab-size"><b>Recent Events</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center carousel-works">
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
                                @include('Organizer.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @else
                        <div class="event-carousel-styles event-carousel-works">
                            @foreach ($joinEvents as $key => $joinEvent)
                                @include('Organizer.Partials.RosterView',  ['isRegistrationView' => false])
                            @endforeach
                        </div>
                    @endif
                 
                @endif
            </div>
        </div>

        <div x-cloak x-show="!isEditMode" class="tab-content pb-4  outer-tab d-none" id="Events">
            <br>
            <div class="mx-auto tab-size"><b>Active Events</b></div>
            <br>
            @if (!isset($joinEventsActive[0]))
                <p class="tab-size">
                    This profile has no active events
                </p>
            @else
                <div id="activeRostersForm" class="tex-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Organizer.Partials.RosterView', ['isRegistrationView' => false])
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
                        @include('Organizer.Partials.RosterView', ['isRegistrationView' => false])
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br><br>

        </div>
        <div class="grid-2-columns tab-size">
            <div class="">
                <br>
                <div> About </div>
                <br>
                <div class="pe-5" x-cloak x-show.important="isEditMode">
                    <textarea 
                        x-model="organizer.companyDescription"
                        class="form-control border-secondary player-profile__input d-inline" 
                    >{{empty($userProfile->organizer?->companyDescription) ?'Enter your company description...' : $userProfile->organizer?->companyDescription}}
                    </textarea>
                    <br>
                    <select 
                        x-model="organizer.industry"
                        style="width: 220px;"
                        placeholder = "Enter your company industry..."
                        class="form-control border-secondary player-profile__input d-inline" 
                    >
                        @foreach([
                            "💻 Technology",
                            "⚕️ Healthcare",
                            "📈 Finance",
                            "🎓 Education",
                            "🏨 Hospitality",
                            "🎬 Entertainment",
                            "🚗 Automotive",
                            "🛍️ Retail",
                            "🏭 Manufacturing",
                            "🌾 Agriculture",
                            "⚡ Energy",
                            "🚚 Transportation",
                            "🏗️ Construction",
                            "📞 Telecommunications",
                            "📺 Media",
                            "🏡 Real Estate",
                            "👗 Fashion",
                            "🍽️ Food and Beverage",
                            "✈️ Travel",
                            "🌳 Environmental",
                            "💊 Pharmaceutical",
                            "🧬 Biotechnology",
                            "💸 Financial Services",
                            "🏋️‍♂️ Health & Fitness",
                            "🎮 Gaming"
                        ] as $industry)
                            <option
                                {{$userProfile->organizer && $userProfile->organizer->industry == $industry ? 'selected' : ''}} 
                                value="{{$industry}}"
                            >{{$industry}}
                            </option> 
                        @endforeach
                    </select> 
                    <br>
                    <input 
                        id="phone"
                        style="width: 250px;"
                        placeholder = "Mobile"
                        class="form-control border-secondary player-profile__input d-inline" 
                    >
                    <br><br>
                    <input
                        placeholder = "Enter your company type..."
                        class="form-control border-secondary player-profile__input d-inline" 
                        style="width: 300px;"
                        x-model="organizer.type"
                    > 
                    <br> <br>
                    <input
                        placeholder = "Address Line 1"
                        class="form-control border-secondary player-profile__input d-inline" 
                        x-model="address.addressLine1"
                    >
                    <input 
                        placeholder = "Address Line 2"
                        class="form-control border-secondary player-profile__input d-inline me-4" 
                        x-model="address.addressLine2"
                    >
                    <input 
                        placeholder = "City"
                        style="width: 100px;"
                        class="form-control border-secondary player-profile__input d-inline me-4" 
                        x-model="address.city"
                    >
                    <input 
                        style="width: 150px;"
                        placeholder = "Country"
                        class="form-control border-secondary player-profile__input d-inline" 
                        x-model="address.country"
                    >
                    <br> <br>
                     
                    <span>
                        <svg
                            class="align-middle me-4"
                            xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        <span>Joined {{Carbon::parse($userProfile->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                    </span>

                </div>
                <div x-cloak x-show.important="!isEditMode">
                    <p x-text="organizer.companyDescription">  </p>
                        
                    <p class="mt-2"> 
                        <template x-if="organizer.industry">
                            <span class="me-5" x-text="organizer.industry"></span>
                        </template>
                        <template x-if="organizer.type">
                            <span>ℹ  <span x-text="organizer.type"></span></span>
                        </template>
                    </p>
                    <template x-if="address.addressLine1 || address.addressLine2 || address.city || address.country">
                        <p class="mt-2"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-geo-alt me-1" viewBox="0 0 16 16">
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                            <span x-text="address.addressLine1"></span> 
                            <span x-text="address.addressLine2"></span> <br> 
                            <span x-text="address.city" class="ms-4"></span> <br>
                            <span x-text="address.country" class="ms-4"></span>
                        </p>
                    </template>
                    <template x-if="userProfile.mobile_no">
                        <p class="mt-2">
                             <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-telephone-fill me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                            </svg>
                            <span x-text="userProfile.mobile_no"></span>
                        </p>
                    </template>
                    <span class="mt-2">
                        
                        <svg
                            class="align-middle me-1"
                            xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        <span>Joined {{Carbon::parse($userProfile->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                    </span>
                </div>
            </div>
            <div class="">
                <br>
                <div> Links </div>
                <br>
                <div x-cloak x-show="isEditMode" class="pe-4">
                    <svg 
                        class="me-3"
                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                    </svg> 
                    <input 
                        placeholder = "Email"
                        disabled name="email"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        value="{{$userProfile->email}}"
                    > 
                    <br><br>
                    <svg 
                        class="me-3"
                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                        <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.7 13.7 0 0 1-.312 2.5m2.802-3.5a7 7 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/>
                    </svg>
                    <input 
                        placeholder = "Website Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        x-model="organizer.website_link"
                    > 
                    <br><br>
                    <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="red" class="bi bi-instagram" viewBox="0 0 16 16">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg>
                    <input 
                        placeholder = "Instagram Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        x-model="organizer.instagram_link"
                    > 
                    <br><br>
                    <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0866FF" class="bi bi-facebook" viewBox="0 0 16 16">
                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                    </svg>
                    <input 
                        placeholder = "Facebook Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        x-model="organizer.facebook_link"
                    >
                    <br><br>
                    <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#1D9BF0" class="bi bi-twitter" viewBox="0 0 16 16">
                        <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/>
                    </svg>
                    <input 
                        placeholder = "Twitter Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        x-model="organizer.twitter_link"
                    >
                </div>
                <div x-cloak x-show.important="!isEditMode">
                    <p class="mt-2"> 
                        <svg 
                        class="me-3"
                        xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                        </svg>
                        <span>{{$userProfile->email}}</span> 
                    </p>
                    <template x-if="organizer.website_link">
                        <p class="mt-2"> 
                            <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                                <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5zM8.5 5v2.5h2.99a12.5 12.5 0 0 0-.337-2.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5zM5.145 12q.208.58.468 1.068c.552 1.035 1.218 1.65 1.887 1.855V12zm.182 2.472a7 7 0 0 1-.597-.933A9.3 9.3 0 0 1 4.09 12H2.255a7 7 0 0 0 3.072 2.472M3.82 11a13.7 13.7 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5zm6.853 3.472A7 7 0 0 0 13.745 12H11.91a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933M8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855q.26-.487.468-1.068zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.7 13.7 0 0 1-.312 2.5m2.802-3.5a7 7 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7 7 0 0 0-3.072-2.472c.218.284.418.598.597.933M10.855 4a8 8 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4z"/>
                            </svg>
                            <span x-text="organizer.website_link"></span>
                        </p>
                    </template>
                    <template x-if="organizer.facebook_link">
                        <p class="mt-2"> 
                            <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0866FF" class="bi bi-facebook" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                            </svg>
                            <span x-text="organizer.facebook_link"></span>
                        </p>
                    </template>
                    <template x-if="organizer.instagram_link">
                        <p class="mt-2"> 
                        <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="red" class="bi bi-instagram" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg>
                            <span x-text="organizer.instagram_link"></span>
                        </p>
                    </template>
                    <template x-if="organizer.twitter_link">
                        <p class="mt-2"> 
                            <svg class="me-3" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#1D9BF0" class="bi bi-twitter" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q.002-.211-.006-.422A6.7 6.7 0 0 0 16 3.542a6.7 6.7 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.5 6.5 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.32 9.32 0 0 1-6.767-3.429 3.29 3.29 0 0 0 1.018 4.382A3.3 3.3 0 0 1 .64 6.575v.045a3.29 3.29 0 0 0 2.632 3.218 3.2 3.2 0 0 1-.865.115 3 3 0 0 1-.614-.057 3.28 3.28 0 0 0 3.067 2.277A6.6 6.6 0 0 1 .78 13.58a6 6 0 0 1-.78-.045A9.34 9.34 0 0 0 5.026 15"/>
                            </svg>
                            <span x-text="organizer.twitter_link"></span>
                        </p>
                    </template>
                </div>
                
            </div>
        </div>
        <br> <br>
        {{-- </form> --}}
    </main>

</body>
@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/choices.js@10.2.0/public/assets/scripts/choices.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@22.0.2/build/js/intlTelInput.min.js"></script>
<script>
    const input = document.querySelector("#phone");
    window.intlTelInput(input, {
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@22.0.2/build/js/utils.js",
    });
    const iti = window.intlTelInput.getInstance(input);
    

    document.addEventListener('alpine:init', () => {
        iti.setNumber('{{$userProfile->mobile_no}}');
        Alpine.data('alpineDataComponent', function() { return ({
            isEditMode: false, 
            userProfile: {
                id: '{{$userProfile->id}}',
                name: '{{$userProfile->name}}',
                mobile_no: '{{$userProfile->mobile_no}}',
            },
            organizer: {
                id: '{{$userProfile->organizer?->id}}',
                industry: '{{$userProfile->organizer?->industry}}',
                type: '{{$userProfile->organizer?->type}}',
                companyName: '{{$userProfile->organizer?->companyName}}',
                companyDescription: '{{$userProfile->organizer?->companyDescription}}',
                website_link: '{{$userProfile->organizer?->website_link}}',
                instagram_link: '{{$userProfile->organizer?->instagram_link}}',
                facebook_link: '{{$userProfile->organizer?->facebook_link}}',
                twitter_link: '{{$userProfile->organizer?->twitter_link}}'
            },
            address: { 
                user_id: {{$userProfile->id}},
                id: '{{$userProfile->address?->id}}',
                addressLine1: '{{$userProfile->address?->addressLine1}}',
                addressLine2: '{{$userProfile->address?->addressLine2}}',
                city: '{{$userProfile->address?->city}}',
                country: '{{$userProfile->address?->country}}'
            },
            countries: [], 
            errorMessage: null, 
            async submitEditProfile (event) {
                try {
                    event.preventDefault(); 
                    this.userProfile.mobile_no = iti.getNumber();

                    if (!iti.isValidNumber()) {
                        this.errorMessage = 'Valid phone number with country code is not chosen!'
                        return;
                    }

                    const url = event.target.dataset.url; 
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: window.loadBearerCompleteHeader(),
                        body: JSON.stringify({
                            address: Alpine.raw(this.address),
                            userProfile: Alpine.raw(this.userProfile),
                            organizer: Alpine.raw(this.organizer)
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
                        window.location.replace(currentUrl);
                    } else {
                        this.errorMessage = data.message;
                    }
                } catch (error) {
                    this.errorMessage = error.message;
                    console.error({error});
                } 
            },
            isCountriesFetched: false ,
             init() {
                this.fetchCountries();
                var backgroundStyles = "<?php echo $backgroundStyles; ?>";
                var fontStyles = "<?php echo $fontStyles; ?>";
                var banner = document.getElementById('backgroundBanner');
                banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
                this.$watch('isEditMode', value => {
                    if (value) {
                        banner.style.color = 'black';
                        banner.style.background = "auto";
                        banner.style.backgroundImage = "auto";
                        banner.style.backgroundColor = "rgb(211, 211, 211)";
                    } else {
                        banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
                    }
                });
            },
            fetchCountries () {
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
                                emoji_flag: '🇦🇫'
                            }];
                        }
                    })
                    .catch(error => console.error('Error fetching countries:', error));
                }
            })
        })
    })


    function reddirectToLoginWithIntened(route) {
        route = encodeURIComponent(route);
        let url = "{{ route('organizer.signin.view') }}";
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
        window.location.href = "{{ route('public.organizer.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }

</script>

<script>
    function reddirectToLoginWithIntened(route) {
            route = encodeURIComponent(route);
            let url = "{{ route('participant.signin.view') }}";
            url+= `?url=${route}`;
            window.location.href = url;
        }


        document.getElementById('followFormProfile')?.addEventListener('submit', async function(event) {
            event.preventDefault();
            let followButtons = document.getElementsByClassName("{{'followButton' . $userProfile->id}}");
            let followCounts = document.getElementsByClassName("{{'followCounts' . $userProfile->id}}");
            console.log({followButtons});
            let form = this;
            let formData = new FormData(form);
            [...followButtons].forEach((button) => {
                button.style.setProperty('pointer-events', 'none');
            });    

            try {
                let jsonObject = {}
                for (let [key, value] of formData.entries()) {
                    jsonObject[key] = value;
                }
                let response = await fetch(form.action, {
                    method: form.method,
                    body: JSON.stringify(jsonObject),
                    headers: {
                        ...window.loadBearerHeader(),
                        'Accept': 'application/json',
                        "Content-Type": "application/json",
                    }
                });

                let data = await response.json();
                [...followButtons].forEach( (followButton) => {
                    followButton.style.setProperty('pointer-events', 'none');

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
                });

                let count = Number(followCounts[0].dataset.count);
                if (data.isFollowing) {
                    count++;
                } else {
                    count--;
                }
                                
                [...followCounts].forEach( (followCount) => {
                    followCount.dataset.count = count;
                    if (count == 1) {
                        followCount.innerHTML = '1 follower';
                    } else if (count == 0) {
                        followCount.innerHTML = `0 followers`;
                    } else {
                        followCount.innerHTML = `${followCount.dataset.count} followers`;
                    }
                })
            } catch (error) {
                followButton.style.setProperty('pointer-events', 'auto');
                toastError('Error occured.', error);
            }
        });
</script>
@include('Organizer.Profile.Scripts')

</html>
