<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Profile Page</title>
    @include('__CommonPartials.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/alpine/organizer.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/player_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
</head>
@php
    use Carbon\Carbon;
    $isUserSame = false;
    [   
        'backgroundStyles' => $backgroundStyles, 
        'fontStyles' => $fontStyles, 
        'frameStyles' => $frameStyles
    ] = $userProfile->profile?->generateStyles();
    $loggedUserId = $loggedUserRole = null;
    if (!$backgroundStyles) {
        $backgroundStyles = "background-color: #fffdfb;"; // Default gray
    }
@endphp
@auth
    @php
        if (!isset($user)) {
            $user = auth()->user();
        }
        
        $loggedUserId = $user->id;
        $loggedUserRole = $user->role; 
        $isUserSame = $user->id == $userProfile->id;
    @endphp
@endauth
<body>
    @include('googletagmanager::body')
    @include('Organizer.__ProfilePartials.BackgroundModal')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main 
        id="app"
        v-cloak
        v-scope="OrganizerData()"
        @vue:mounted="init"
    >
        <div 
            id="routeContainer"
            class="d-none"
            data-profile-route="{{ route('public.organizer.view', ['id' => ':id']) }}"
            data-login-route="{{ route('participant.signin.view') }}"
            data-logged-user-id="{{ $loggedUserId }}"
            data-logged-user-role="{{ $loggedUserRole }}"
        >
        </div>
        <div class="profile-storage d-none"
            data-route-signin="{{ route('participant.signin.view') }}"
            data-route-profile="{{ route('public.participant.view', ['id' => ':id']) }}"
            data-user-banner-url="{{ route('participant.userBanner.action', ['id' => $userProfile->id]) }}"
            data-route-background-api="{{ route('user.userBackgroundApi.action', ['id' => $userProfile->id]) }}"
            data-background-styles="<?php echo $backgroundStyles; ?>"
            data-font-styles="<?php echo $fontStyles; ?>"
        >
        </div>
        <input type="hidden" id="initialUserProfile" value="{{json_encode($userProfile)}}">
        <input type="hidden" id="initialOrganizer" value="{{json_encode($userProfile->organizer)}}">
        <input type="hidden" id="initialAddress" value="{{json_encode($userProfile->address)}}">
        {{-- <form action="{{route('organizer.profile.update')}}" method="POST">  --}}
        <div>
            <div id="backgroundBanner" class="member-section px-2 pt-2"
               
            > 
                 <div class="d-flex justify-content-end py-0 my-0 mb-2">
                    <button 
                     data-bs-toggle="offcanvas"
                        data-bs-target="#profileDrawer"
                        
                        v-show="isEditMode"
                        {{-- onclick="document.getElementById('backgroundInput').click();" --}}
                        class="btn btn-secondary text-light rounded-pill py-2 me-3 fs-7"
                    > 
                        Change Background
                    </button>
                </div>
                <br>
                <div class="member-image">
                    <div class="upload-container align-items-center">
                        <label  class="upload-label">
                            <div class="circle-container motion-logo">
                                <div  class="uploaded-image"
                                    style="background-image: url({{ '/storage' . '/'. $userProfile->userBanner }}  ); background-size: cover; 
                                        z-index: 99; background-repeat: no-repeat; background-position: center; {{$frameStyles}}"
                                >
                                </div>
                                <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                                    <a aria-hidden="true" data-fslightbox="lightbox" href="{{ '/' . 'storage/' . $userProfile->userBanner }}">
                                        <button class="btn btn-sm p-0 me-2"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                        </svg> 
                                        </button>
                                    </a>    
                                    @if ($isUserSame)
                                        <button  v-if="isEditMode" id="upload-button2" class="btn btn-sm text-white p-0 z-99" aria-hidden="true">
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
                            <input type="file" id="image-upload"   
                                accept=".png, .jpg, .jpeg, image/png, image/jpeg" 
                                style="display: none;"
                            >
                        @endif
                    </div>
                </div>
                <div class="member-details mx-auto text-center">
                    <div  v-show="isEditMode" class="pb-3">
                        <div v-show="errorMessage != null" class="text-red" v-text="errorMessage"> </div>
                        <input 
                            placeholder = "Enter your name..."
                            style="width: 250px;"
                            name="name"
                            autocomplete="off"
                            autocomplete="nope"
                            class="form-control border-secondary player-profile__input d-inline" 
                            v-model="userProfile.name"
                        >
                        <br>
                        <input 
                            placeholder = "Enter your company name..."
                            style="width: 300px;"
                            class="form-control border-secondary player-profile__input d-inline me-3" 
                            v-model="organizer.companyName"
                            autocomplete="off"
                            autocomplete="nope"
                        > 
                        <a 
                            type="submit"
                            data-url="{{route('organizer.profile.update')}}"
                            v-on:click="submitEditProfile(event);"
                            style="border-color: green;"
                            class="mt-4 oceans-gaming-default-button simple-button cursor-pointer bg-success btn-success btn text-dark px-3 py-1 me-2 text-success"> 
                            Save
                        </a>
                        <svg 
                        {{-- Close icon --}}
                        v-on:click="isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="mt-4 py-1 bi bi-x-circle cursor-pointer text-red" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        <br>
                    </div>
                    <div  v-show="!isEditMode" class="user-select-none ">
                        <h5 >
                            {{$userProfile->name}}
                        </h5>
                        <div class="my-2"> 
                            <template v-if="organizer.industry && organizer.industry!= ''">
                                <span>
                                    <span class="me-2"> </span>
                                    <span class="me-3" v-text="organizer.industry"> </span>
                                </span>
                            </template>
                            <span class="me-1"> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                </svg>
                            </span>
                            <span class="d-inline p-0 m-0" style="display: inline !important;">
                                <span data-follower-stats="{{$followersCount}}" class="cursor-pointer d-inline ps-0"  onclick="openModal('followers')">
                                    <span data-count="{{ $followersCount }}" @class(['followCounts' . $userProfile->id])> {{ $followersCount }}
                                        follower{{ bladePluralPrefix($followersCount) }}
                                    </span>
                                </span>
                            </span>
                            @include('__CommonPartials.ProfileStatsModal', [
                                'propsTeamOrUserId' => $userProfile->id,
                                'propsUserId' => $loggedUserId ?? '0',
                                'propsIsUserSame' => $isUserSame ? 1: 0, 
                                'propsRole' => "ORGANIZER", 
                                'propsUserRole' => $loggedUserRole 
                            ])

                        </div>
                        @if ($isOwnProfile)
                            <div class="text-center">
                                <button 
                                    
                                    v-on:click="reset(); isEditMode = true;"
                                    class="oceans-gaming-default-button oceans-gaming-primary-button py-1 px-5 me-3"> 
                                    Edit
                                </button>
                            </div>
                        @else
                            <div class="text-center">
                                <form id="followFormProfile" method="POST"
                                    class="d-inline me-3 followFormProfile"
                                    action="{{ route('participant.organizer.follow') }}">
                                    @csrf
                                    @auth
                                        <input type="hidden" name="role"
                                            value="{{ $user && $user->id ? $user->role : '' }}"
                                        >
                                        <input type="hidden" name="user_id"
                                            value="{{ $user && $user->id ? $user->id : '' }}"
                                        >
                                        <input type="hidden" name="organizer_id"
                                            value="{{ $userProfile?->id }}"
                                        >
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
                                <a href="{{route('user.message.view', ['userId' => $userProfile->id] )}}">
                                    <button 
                                        class="oceans-gaming-default-button oceans-gaming-transparent-button bg-light border-0 rounded px-3 py-2"> 
                                        Message
                                    </button>
                                </a>
                            </div>
                        @endif
                        <br>
                    </div>
                </div>
            </div>
        </div>
        <div class="tabs px-5"  v-show="!isEditMode">
            <button class="tab-button  outer-tab py-2 tab-button-active"
                onclick="showTab(event, 'Overview', 'outer-tab')">Overview</button>
            <button class="tab-button outer-tab" onclick="showTab(event, 'Events', 'outer-tab')">Events</button>
        </div>
        <div  v-show="!isEditMode" class="tab-content pb-4 outer-tab px-5" id="Overview">
            <br> 
            <div class="showcase tab-size showcase-box showcase-column pt-4 grid-4-columns text-center" style="width: min(800px,  80%);">
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
                    <button @class(["carousel-button position-absolute",
                       "carousel-button-disabled"  => empty($joinEvents[2])
                    ]) style="left: 20px;"
                        onclick="carouselWork(-2)">
                        &lt;
                    </button>
                    <button @class(["carousel-button position-absolute",
                       "carousel-button-disabled"  => empty($joinEvents[2])
                    ]) style="right: 30px;"
                        onclick="carouselWork(2)">
                        &gt;
                    </button>
                    <div @class(["event-carousel-works animation-container", 
                        "event-carousel-styles" => isset($joinEvents[1]),
                        "d-flex justify-content-center " => !isset($joinEvents[1])
                    ])
                    >
                        @foreach ($joinEvents as $key => $joinEvent)
                            @include('Organizer.__Partials.RosterView')
                        @endforeach
                    </div>
                 
                @endif
            </div>
        </div>

        <div  v-show="!isEditMode" class="tab-content pb-4  outer-tab d-none" id="Events">
            <br>
            <div class="mx-auto tab-size"><b>Active Events</b></div>
            <br>
            @if (!isset($joinEventsActive[0]))
                <p class="tab-size">
                    This profile has no active events
                </p>
                <br><br><br>
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <br>
                    @foreach ($joinEventsActive as $key => $joinEvent)
                        @include('Organizer.__Partials.RosterView')
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br>
            <div class="tab-size"><b>Past Events</b></div>
            <br>
            @if (!isset($joinEventsHistory[0]))
                <p class="tab-size">
                    This profile has no past events
                </p>
            @else
                <div id="activeRostersForm" class="animation-container text-center mx-auto">
                    <br>
                    @foreach ($joinEventsHistory as $key => $joinEvent)                        
                        @include('Organizer.__Partials.RosterView')
                        <br><br>
                    @endforeach
                </div>
            @endif
            <br><br>

        </div>
        <div class="grid-2-columns tab-size">
            <div class="">
                <br>
                <div > About </div>
                <br>
                <div class="pe-5"  v-show="isEditMode">
                    <textarea 
                        v-model="organizer.companyDescription"
                        class="form-control border-secondary player-profile__input d-inline" 
                        autocomplete="off"
                        autocomplete="nope"
                    >{{empty($userProfile->organizer?->companyDescription) ?'Enter your company description...' : $userProfile->organizer?->companyDescription}}
                    </textarea>
                    <br>
                    <select 
                        v-model="organizer.industry"
                        style="width: 220px;"
                        placeholder = "Enter your company industry..."
                        class="form-control form-select border-secondary player-profile__input d-inline" 
                    >
                        <option value="">Do not display</option>
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
                        autocomplete="off"
                        autocomplete="nope" 
                    >
                    <br><br>
                    <input
                        placeholder = "Enter your company type..."
                        class="form-control border-secondary player-profile__input d-inline" 
                        style="width: 300px;"
                        v-model="organizer.type"
                        autocomplete="off"
                        autocomplete="nope"
                    > 
                    <br> <br>
                    <input
                        placeholder = "Address Line 1"
                        class="form-control border-secondary player-profile__input d-inline" 
                        v-model="address.addressLine1"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                    <input 
                        placeholder = "Address Line 2"
                        class="form-control border-secondary player-profile__input d-inline me-4" 
                        v-model="address.addressLine2"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                    <input 
                        placeholder = "City"
                        style="width: 100px;"
                        class="form-control border-secondary player-profile__input d-inline me-4" 
                        v-model="address.city"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                    <input 
                        style="width: 150px;"
                        placeholder = "Country"
                        class="form-control border-secondary player-profile__input d-inline" 
                        v-model="address.country"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                    <br> <br>
                     
                    <span>
                        <svg
                            class="align-middle me-3"
                            xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        <span>Joined {{Carbon::parse($userProfile->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                    </span>

                </div>
                <div  v-show="!isEditMode">
                    <p v-text="organizer.companyDescription">  </p>
                        
                    <p class="mt-2"> 

                        <template v-if="organizer.industry && organizer.industry!= ''">
                            <span class="me-5" v-text="organizer.industry"></span>
                        </template>
                        <template v-if="organizer.type">
                            <span>ℹ  <span v-text="organizer.type"></span></span>
                        </template>
                    </p>
                    <template v-if="address.addressLine1 || address.addressLine2 || address.city || address.country">
                        <p class="mt-2"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-geo-alt me-1" viewBox="0 0 16 16">
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                            <span v-text="address.addressLine1"></span> 
                            <span v-text="address.addressLine2"></span> <br> 
                            <span v-text="address.city" class="ms-4"></span> <br>
                            <span v-text="address.country" class="ms-4"></span>
                        </p>
                    </template>
                    <template v-if="userProfile.mobile_no">
                        <p class="mt-2">
                             <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-telephone-fill me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                            </svg>
                            <span v-text="userProfile.mobile_no"></span>
                        </p>
                    </template>
                    <span class="mt-2">
                        <svg
                            class="align-middle me-1"
                            xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        <span>Joined {{Carbon::parse($userProfile->created_at)->isoFormat('Do MMMM YYYY')}}</span>
                    </span>
                </div>
            </div>
            <div class="">
                <br>
                <div > Links </div>
                <br>
                <div  v-show="isEditMode" class="pe-4">
                    <svg width="25px" height="25px" class="me-3 cursor-pointer" viewBox="0 0 1024 1024" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M512 599.6L107.9 311.1v19.7L512 619.3l404.1-288.6V311L512 599.6z" fill="#E73B37"></path><path d="M63.9 187v650h896.2V187H63.9z m852.2 598.5L672.2 611.3l-13.8 9.8L899.1 793H125.5l240.6-171.8-13.8-9.8-244.4 174.5V231h808.2v554.5z" fill="#39393A"></path><path d="M512.9 536.7m-10 0a10 10 0 1 0 20 0 10 10 0 1 0-20 0Z" fill="#E73B37"></path></g></svg>
                    <input 
                        placeholder = "Email"
                        v-model="userProfile.demo_email"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        autocomplete="off"
                        autocomplete="nope"
                    > 
                    <br><br>
                    <svg width="25px" height="25px" class="me-3 cursor-pointer" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="style=doutone" clip-path="url(#clip0_1_1831)"> <g id="web"> <path id="vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M10.4425 2.44429C10.0752 3.64002 9.32073 6.25569 8.89915 8.83258C9.99331 9.00921 11.0621 9.12209 12 9.12209C12.9379 9.12209 14.0067 9.00921 15.1009 8.83258C14.6793 6.25569 13.9248 3.64002 13.5575 2.44429C13.0509 2.3624 12.5307 2.31977 12 2.31977C11.4693 2.31977 10.9491 2.3624 10.4425 2.44429ZM15.3337 2.90893C15.737 4.305 16.2958 6.42828 16.6448 8.54737C18.1513 8.23703 19.5727 7.85824 20.605 7.56109C19.4986 5.42102 17.6172 3.74662 15.3337 2.90893ZM21.2129 9.01933C20.1222 9.33683 18.5423 9.76328 16.8594 10.1057C16.9295 10.7564 16.9709 11.3958 16.9709 12C16.9709 12.8816 16.8827 13.8411 16.7445 14.8058C18.759 14.3858 20.6068 13.849 21.5557 13.5575C21.6376 13.0509 21.6802 12.5307 21.6802 12C21.6802 10.959 21.5162 9.95751 21.2129 9.01933ZM21.0911 15.3337C19.9166 15.6729 18.229 16.1219 16.4634 16.4634C16.1219 18.229 15.6729 19.9166 15.3337 21.0911C17.9978 20.1138 20.1138 17.9978 21.0911 15.3337ZM13.5576 21.5557C13.849 20.6068 14.3858 18.759 14.8058 16.7445C13.8411 16.8827 12.8816 16.9709 12 16.9709C11.1184 16.9709 10.1589 16.8827 9.19423 16.7445C9.61421 18.759 10.151 20.6068 10.4425 21.5557C10.9491 21.6376 11.4693 21.6802 12 21.6802C12.5307 21.6802 13.0509 21.6376 13.5576 21.5557ZM8.66629 21.0911C8.32707 19.9166 7.8781 18.229 7.53658 16.4634C5.77099 16.1219 4.08335 15.6729 2.90891 15.3337C3.88622 17.9978 6.00216 20.1138 8.66629 21.0911ZM2.44429 13.5575C3.39316 13.849 5.24101 14.3858 7.25548 14.8058C7.1173 13.8411 7.02907 12.8816 7.02907 12C7.02907 11.3958 7.07048 10.7564 7.14056 10.1057C5.45769 9.76328 3.87779 9.33683 2.78712 9.01933C2.48383 9.95751 2.31977 10.959 2.31977 12C2.31977 12.5307 2.3624 13.0509 2.44429 13.5575ZM3.39504 7.56109C4.42731 7.85824 5.84865 8.23703 7.35522 8.54737C7.70416 6.42827 8.26303 4.305 8.66626 2.90893C6.38282 3.74662 4.50139 5.42102 3.39504 7.56109ZM8.68924 10.3888C8.63137 10.9544 8.59884 11.4968 8.59884 12C8.59884 12.9399 8.71224 14.012 8.88985 15.1102C9.98798 15.2878 11.0601 15.4012 12 15.4012C12.9399 15.4012 14.012 15.2878 15.1102 15.1102C15.2878 14.012 15.4012 12.9399 15.4012 12C15.4012 11.4968 15.3686 10.9544 15.3108 10.3888C14.1776 10.5703 13.0348 10.6919 12 10.6919C10.9652 10.6919 9.82236 10.5703 8.68924 10.3888ZM9.67273 0.991173C10.4243 0.833026 11.2029 0.75 12 0.75C12.7971 0.75 13.5757 0.833026 14.3273 0.991174C18.0108 1.76627 21.0281 4.34097 22.42 7.75174C22.9554 9.06356 23.25 10.4983 23.25 12C23.25 12.7971 23.167 13.5757 23.0088 14.3273C22.0943 18.6736 18.6736 22.0943 14.3273 23.0088C13.5757 23.167 12.7971 23.25 12 23.25C11.2029 23.25 10.4243 23.167 9.67273 23.0088C5.32644 22.0943 1.90572 18.6736 0.991173 14.3273C0.833026 13.5757 0.75 12.7971 0.75 12C0.75 10.4972 1.04509 9.06132 1.58123 7.74866C2.97369 4.33943 5.99026 1.76604 9.67273 0.991173Z" fill="#000000"></path> <path id="vector (Stroke)_2" fill-rule="evenodd" clip-rule="evenodd" d="M1.85178 7.08643L2.53631 7.2947C3.67698 7.64175 5.89906 8.2875 8.15116 8.70314C9.50118 8.9523 10.8492 9.12226 12 9.12226C13.1508 9.12226 14.4988 8.95225 15.8489 8.70309C18.1008 8.28747 20.3221 7.64438 21.4622 7.2975L22.147 7.08918L22.4174 7.75185C22.9524 9.06271 23.25 10.4974 23.25 12.0001C23.25 12.7972 23.167 13.5758 23.0088 14.3274L22.9147 14.7746L22.4794 14.9135C21.6743 15.1704 18.8475 16.0412 15.9286 16.5631C14.5988 16.8009 13.2243 16.9711 12 16.9711C10.7758 16.9711 9.40121 16.8009 8.07146 16.5632C5.15251 16.0413 2.32571 15.1704 1.52065 14.9136L1.08527 14.7746L0.991173 14.3274C0.833026 13.5759 0.75 12.7973 0.75 12.0002C0.75 10.4973 1.04509 9.06148 1.58123 7.74882L1.85178 7.08643ZM2.79056 9.00886C2.48507 9.9501 2.31977 10.9552 2.31977 12.0002C2.31977 12.5308 2.3624 13.0511 2.44429 13.5577C3.39316 13.8492 5.24101 14.3859 7.25548 14.8059C7.1173 13.8413 7.02907 12.8818 7.02907 12.0002C7.02907 11.3958 7.0705 10.7563 7.14061 10.1054C5.47136 9.76363 3.88678 9.32981 2.79056 9.00886ZM8.68924 10.3889C8.63137 10.9546 8.59884 11.497 8.59884 12.0002C8.59884 12.9401 8.71224 14.0122 8.88985 15.1103C9.98798 15.2879 11.0601 15.4013 12 15.4013C12.9399 15.4013 14.012 15.2879 15.1102 15.1103C15.2878 14.0121 15.4012 12.94 15.4012 12.0001C15.4012 11.4969 15.3686 10.9545 15.3108 10.3889C14.1777 10.5704 13.0348 10.692 12 10.692C10.9652 10.692 9.82236 10.5704 8.68924 10.3889ZM16.8594 10.1054C16.9295 10.7563 16.9709 11.3958 16.9709 12.0001C16.9709 12.8817 16.8827 13.8412 16.7445 14.8059C18.759 14.3859 20.6069 13.8491 21.5557 13.5577C21.6376 13.051 21.6802 12.5308 21.6802 12.0001C21.6802 10.9568 21.5137 9.95256 21.2079 9.01147C20.1116 9.33193 18.5279 9.76413 16.8594 10.1054Z" fill="#BFBFBF"></path> </g> </g> <defs> <clipPath id="clip0_1_1831"> <rect width="24" height="24" fill="white"></rect> </clipPath> </defs> </g></svg>
                    <input 
                        placeholder = "Website Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        v-model="organizer.website_link"
                        autocomplete="off"
                        autocomplete="nope"
                    > 
                    <br><br>
                    <svg width="25px" height="25px" class="me-3 cursor-pointer" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect x="2" y="2" width="28" height="28" rx="6" fill="url(#paint0_radial_87_7153)"></rect> <rect x="2" y="2" width="28" height="28" rx="6" fill="url(#paint1_radial_87_7153)"></rect> <rect x="2" y="2" width="28" height="28" rx="6" fill="url(#paint2_radial_87_7153)"></rect> <path d="M23 10.5C23 11.3284 22.3284 12 21.5 12C20.6716 12 20 11.3284 20 10.5C20 9.67157 20.6716 9 21.5 9C22.3284 9 23 9.67157 23 10.5Z" fill="white"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M16 21C18.7614 21 21 18.7614 21 16C21 13.2386 18.7614 11 16 11C13.2386 11 11 13.2386 11 16C11 18.7614 13.2386 21 16 21ZM16 19C17.6569 19 19 17.6569 19 16C19 14.3431 17.6569 13 16 13C14.3431 13 13 14.3431 13 16C13 17.6569 14.3431 19 16 19Z" fill="white"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6 15.6C6 12.2397 6 10.5595 6.65396 9.27606C7.2292 8.14708 8.14708 7.2292 9.27606 6.65396C10.5595 6 12.2397 6 15.6 6H16.4C19.7603 6 21.4405 6 22.7239 6.65396C23.8529 7.2292 24.7708 8.14708 25.346 9.27606C26 10.5595 26 12.2397 26 15.6V16.4C26 19.7603 26 21.4405 25.346 22.7239C24.7708 23.8529 23.8529 24.7708 22.7239 25.346C21.4405 26 19.7603 26 16.4 26H15.6C12.2397 26 10.5595 26 9.27606 25.346C8.14708 24.7708 7.2292 23.8529 6.65396 22.7239C6 21.4405 6 19.7603 6 16.4V15.6ZM15.6 8H16.4C18.1132 8 19.2777 8.00156 20.1779 8.0751C21.0548 8.14674 21.5032 8.27659 21.816 8.43597C22.5686 8.81947 23.1805 9.43139 23.564 10.184C23.7234 10.4968 23.8533 10.9452 23.9249 11.8221C23.9984 12.7223 24 13.8868 24 15.6V16.4C24 18.1132 23.9984 19.2777 23.9249 20.1779C23.8533 21.0548 23.7234 21.5032 23.564 21.816C23.1805 22.5686 22.5686 23.1805 21.816 23.564C21.5032 23.7234 21.0548 23.8533 20.1779 23.9249C19.2777 23.9984 18.1132 24 16.4 24H15.6C13.8868 24 12.7223 23.9984 11.8221 23.9249C10.9452 23.8533 10.4968 23.7234 10.184 23.564C9.43139 23.1805 8.81947 22.5686 8.43597 21.816C8.27659 21.5032 8.14674 21.0548 8.0751 20.1779C8.00156 19.2777 8 18.1132 8 16.4V15.6C8 13.8868 8.00156 12.7223 8.0751 11.8221C8.14674 10.9452 8.27659 10.4968 8.43597 10.184C8.81947 9.43139 9.43139 8.81947 10.184 8.43597C10.4968 8.27659 10.9452 8.14674 11.8221 8.0751C12.7223 8.00156 13.8868 8 15.6 8Z" fill="white"></path> <defs> <radialGradient id="paint0_radial_87_7153" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(12 23) rotate(-55.3758) scale(25.5196)"> <stop stop-color="#B13589"></stop> <stop offset="0.79309" stop-color="#C62F94"></stop> <stop offset="1" stop-color="#8A3AC8"></stop> </radialGradient> <radialGradient id="paint1_radial_87_7153" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(11 31) rotate(-65.1363) scale(22.5942)"> <stop stop-color="#E0E8B7"></stop> <stop offset="0.444662" stop-color="#FB8A2E"></stop> <stop offset="0.71474" stop-color="#E2425C"></stop> <stop offset="1" stop-color="#E2425C" stop-opacity="0"></stop> </radialGradient> <radialGradient id="paint2_radial_87_7153" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(0.500002 3) rotate(-8.1301) scale(38.8909 8.31836)"> <stop offset="0.156701" stop-color="#406ADC"></stop> <stop offset="0.467799" stop-color="#6A45BE"></stop> <stop offset="1" stop-color="#6A45BE" stop-opacity="0"></stop> </radialGradient> </defs> </g></svg>                    <input 
                        placeholder = "Instagram Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        v-model="organizer.instagram_link"
                        autocomplete="off"
                        autocomplete="nope"
                    > 
                    <br><br>
                    <svg width="25px" height="25px" class="me-3 cursor-pointer" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <circle cx="16" cy="16" r="14" fill="url(#paint0_linear_87_7208)"></circle> <path d="M21.2137 20.2816L21.8356 16.3301H17.9452V13.767C17.9452 12.6857 18.4877 11.6311 20.2302 11.6311H22V8.26699C22 8.26699 20.3945 8 18.8603 8C15.6548 8 13.5617 9.89294 13.5617 13.3184V16.3301H10V20.2816H13.5617V29.8345C14.2767 29.944 15.0082 30 15.7534 30C16.4986 30 17.2302 29.944 17.9452 29.8345V20.2816H21.2137Z" fill="white"></path> <defs> <linearGradient id="paint0_linear_87_7208" x1="16" y1="2" x2="16" y2="29.917" gradientUnits="userSpaceOnUse"> <stop stop-color="#18ACFE"></stop> <stop offset="1" stop-color="#0163E0"></stop> </linearGradient> </defs> </g></svg>
                    <input 
                        placeholder = "Facebook Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        v-model="organizer.facebook_link"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                    <br><br>
                    <svg width="25px" height="25px" class="me-3 cursor-pointer"  viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11.7887 28C8.55374 28 5.53817 27.0591 3 25.4356C5.15499 25.5751 8.95807 25.2411 11.3236 22.9848C7.76508 22.8215 6.16026 20.0923 5.95094 18.926C6.25329 19.0426 7.6953 19.1826 8.50934 18.856C4.4159 17.8296 3.78793 14.2373 3.92748 13.141C4.695 13.6775 5.99745 13.8641 6.50913 13.8174C2.69479 11.0882 4.06703 6.98276 4.74151 6.09635C7.47882 9.88867 11.5812 12.0186 16.6564 12.137C16.5607 11.7174 16.5102 11.2804 16.5102 10.8316C16.5102 7.61092 19.1134 5 22.3247 5C24.0025 5 25.5144 5.71275 26.5757 6.85284C27.6969 6.59011 29.3843 5.97507 30.2092 5.4432C29.7934 6.93611 28.4989 8.18149 27.7159 8.64308C27.7224 8.65878 27.7095 8.62731 27.7159 8.64308C28.4037 8.53904 30.2648 8.18137 31 7.68256C30.6364 8.52125 29.264 9.91573 28.1377 10.6964C28.3473 19.9381 21.2765 28 11.7887 28Z" fill="#47ACDF"></path> </g></svg>

                    <input 
                        placeholder = "Twitter Link"
                        class="form-control w-75 border-secondary player-profile__input d-inline" 
                        v-model="organizer.twitter_link"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                </div>
                <div  v-show="!isEditMode">
                   <a href="mailto:{{$userProfile->demo_email ?? ''}}" class="mt-2 cursor-pointer custom-link" > 
                    <p class="mt-2 cursor-pointer">
                        <svg width="25px" height="25px" viewBox="0 0 1024 1024" class="icon me-3 cursor-pointer" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M512 599.6L107.9 311.1v19.7L512 619.3l404.1-288.6V311L512 599.6z" fill="#E73B37"></path><path d="M63.9 187v650h896.2V187H63.9z m852.2 598.5L672.2 611.3l-13.8 9.8L899.1 793H125.5l240.6-171.8-13.8-9.8-244.4 174.5V231h808.2v554.5z" fill="#39393A"></path><path d="M512.9 536.7m-10 0a10 10 0 1 0 20 0 10 10 0 1 0-20 0Z" fill="#E73B37"></path></g></svg>
                        <span>{{$userProfile->demo_email ?? 'Contact email not provided'}}</span> 
                    </p>
                    </a>
                    <template v-if="organizer.website_link">
                        <p class="mt-2 cursor-pointer "> 
                            <a v-bind:href="organizer.website_link"  class="custom-link">
                            <svg width="25px" height="25px" viewBox="0 0 24 24" fill="none" class="me-3 cursor-pointer" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="style=doutone" clip-path="url(#clip0_1_1831)"> <g id="web"> <path id="vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M10.4425 2.44429C10.0752 3.64002 9.32073 6.25569 8.89915 8.83258C9.99331 9.00921 11.0621 9.12209 12 9.12209C12.9379 9.12209 14.0067 9.00921 15.1009 8.83258C14.6793 6.25569 13.9248 3.64002 13.5575 2.44429C13.0509 2.3624 12.5307 2.31977 12 2.31977C11.4693 2.31977 10.9491 2.3624 10.4425 2.44429ZM15.3337 2.90893C15.737 4.305 16.2958 6.42828 16.6448 8.54737C18.1513 8.23703 19.5727 7.85824 20.605 7.56109C19.4986 5.42102 17.6172 3.74662 15.3337 2.90893ZM21.2129 9.01933C20.1222 9.33683 18.5423 9.76328 16.8594 10.1057C16.9295 10.7564 16.9709 11.3958 16.9709 12C16.9709 12.8816 16.8827 13.8411 16.7445 14.8058C18.759 14.3858 20.6068 13.849 21.5557 13.5575C21.6376 13.0509 21.6802 12.5307 21.6802 12C21.6802 10.959 21.5162 9.95751 21.2129 9.01933ZM21.0911 15.3337C19.9166 15.6729 18.229 16.1219 16.4634 16.4634C16.1219 18.229 15.6729 19.9166 15.3337 21.0911C17.9978 20.1138 20.1138 17.9978 21.0911 15.3337ZM13.5576 21.5557C13.849 20.6068 14.3858 18.759 14.8058 16.7445C13.8411 16.8827 12.8816 16.9709 12 16.9709C11.1184 16.9709 10.1589 16.8827 9.19423 16.7445C9.61421 18.759 10.151 20.6068 10.4425 21.5557C10.9491 21.6376 11.4693 21.6802 12 21.6802C12.5307 21.6802 13.0509 21.6376 13.5576 21.5557ZM8.66629 21.0911C8.32707 19.9166 7.8781 18.229 7.53658 16.4634C5.77099 16.1219 4.08335 15.6729 2.90891 15.3337C3.88622 17.9978 6.00216 20.1138 8.66629 21.0911ZM2.44429 13.5575C3.39316 13.849 5.24101 14.3858 7.25548 14.8058C7.1173 13.8411 7.02907 12.8816 7.02907 12C7.02907 11.3958 7.07048 10.7564 7.14056 10.1057C5.45769 9.76328 3.87779 9.33683 2.78712 9.01933C2.48383 9.95751 2.31977 10.959 2.31977 12C2.31977 12.5307 2.3624 13.0509 2.44429 13.5575ZM3.39504 7.56109C4.42731 7.85824 5.84865 8.23703 7.35522 8.54737C7.70416 6.42827 8.26303 4.305 8.66626 2.90893C6.38282 3.74662 4.50139 5.42102 3.39504 7.56109ZM8.68924 10.3888C8.63137 10.9544 8.59884 11.4968 8.59884 12C8.59884 12.9399 8.71224 14.012 8.88985 15.1102C9.98798 15.2878 11.0601 15.4012 12 15.4012C12.9399 15.4012 14.012 15.2878 15.1102 15.1102C15.2878 14.012 15.4012 12.9399 15.4012 12C15.4012 11.4968 15.3686 10.9544 15.3108 10.3888C14.1776 10.5703 13.0348 10.6919 12 10.6919C10.9652 10.6919 9.82236 10.5703 8.68924 10.3888ZM9.67273 0.991173C10.4243 0.833026 11.2029 0.75 12 0.75C12.7971 0.75 13.5757 0.833026 14.3273 0.991174C18.0108 1.76627 21.0281 4.34097 22.42 7.75174C22.9554 9.06356 23.25 10.4983 23.25 12C23.25 12.7971 23.167 13.5757 23.0088 14.3273C22.0943 18.6736 18.6736 22.0943 14.3273 23.0088C13.5757 23.167 12.7971 23.25 12 23.25C11.2029 23.25 10.4243 23.167 9.67273 23.0088C5.32644 22.0943 1.90572 18.6736 0.991173 14.3273C0.833026 13.5757 0.75 12.7971 0.75 12C0.75 10.4972 1.04509 9.06132 1.58123 7.74866C2.97369 4.33943 5.99026 1.76604 9.67273 0.991173Z" fill="#000000"></path> <path id="vector (Stroke)_2" fill-rule="evenodd" clip-rule="evenodd" d="M1.85178 7.08643L2.53631 7.2947C3.67698 7.64175 5.89906 8.2875 8.15116 8.70314C9.50118 8.9523 10.8492 9.12226 12 9.12226C13.1508 9.12226 14.4988 8.95225 15.8489 8.70309C18.1008 8.28747 20.3221 7.64438 21.4622 7.2975L22.147 7.08918L22.4174 7.75185C22.9524 9.06271 23.25 10.4974 23.25 12.0001C23.25 12.7972 23.167 13.5758 23.0088 14.3274L22.9147 14.7746L22.4794 14.9135C21.6743 15.1704 18.8475 16.0412 15.9286 16.5631C14.5988 16.8009 13.2243 16.9711 12 16.9711C10.7758 16.9711 9.40121 16.8009 8.07146 16.5632C5.15251 16.0413 2.32571 15.1704 1.52065 14.9136L1.08527 14.7746L0.991173 14.3274C0.833026 13.5759 0.75 12.7973 0.75 12.0002C0.75 10.4973 1.04509 9.06148 1.58123 7.74882L1.85178 7.08643ZM2.79056 9.00886C2.48507 9.9501 2.31977 10.9552 2.31977 12.0002C2.31977 12.5308 2.3624 13.0511 2.44429 13.5577C3.39316 13.8492 5.24101 14.3859 7.25548 14.8059C7.1173 13.8413 7.02907 12.8818 7.02907 12.0002C7.02907 11.3958 7.0705 10.7563 7.14061 10.1054C5.47136 9.76363 3.88678 9.32981 2.79056 9.00886ZM8.68924 10.3889C8.63137 10.9546 8.59884 11.497 8.59884 12.0002C8.59884 12.9401 8.71224 14.0122 8.88985 15.1103C9.98798 15.2879 11.0601 15.4013 12 15.4013C12.9399 15.4013 14.012 15.2879 15.1102 15.1103C15.2878 14.0121 15.4012 12.94 15.4012 12.0001C15.4012 11.4969 15.3686 10.9545 15.3108 10.3889C14.1777 10.5704 13.0348 10.692 12 10.692C10.9652 10.692 9.82236 10.5704 8.68924 10.3889ZM16.8594 10.1054C16.9295 10.7563 16.9709 11.3958 16.9709 12.0001C16.9709 12.8817 16.8827 13.8412 16.7445 14.8059C18.759 14.3859 20.6069 13.8491 21.5557 13.5577C21.6376 13.051 21.6802 12.5308 21.6802 12.0001C21.6802 10.9568 21.5137 9.95256 21.2079 9.01147C20.1116 9.33193 18.5279 9.76413 16.8594 10.1054Z" fill="#BFBFBF"></path> </g> </g> <defs> <clipPath id="clip0_1_1831"> <rect width="24" height="24" fill="white"></rect> </clipPath> </defs> </g></svg>
                                <span v-text="processUrl(organizer.website_link)"></span>
                            </p>
                        </a>
                    </template>
                    <template v-if="organizer.facebook_link">
                        <p class="mt-2"> 
                            <a v-bind:href="organizer.facebook_link" class="custom-link">
                                <svg width="25px" height="25px" class="me-3 cursor-pointer" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path fill="#1877F2" d="M15 8a7 7 0 00-7-7 7 7 0 00-1.094 13.915v-4.892H5.13V8h1.777V6.458c0-1.754 1.045-2.724 2.644-2.724.766 0 1.567.137 1.567.137v1.723h-.883c-.87 0-1.14.54-1.14 1.093V8h1.941l-.31 2.023H9.094v4.892A7.001 7.001 0 0015 8z"></path><path fill="#ffffff" d="M10.725 10.023L11.035 8H9.094V6.687c0-.553.27-1.093 1.14-1.093h.883V3.87s-.801-.137-1.567-.137c-1.6 0-2.644.97-2.644 2.724V8H5.13v2.023h1.777v4.892a7.037 7.037 0 002.188 0v-4.892h1.63z"></path></g></svg>
                                <span v-text="processUrl(organizer.facebook_link)"></span>
                            </a>
                        </p>
                    </template>
                    <template v-if="organizer.instagram_link">
                        <p class="mt-2"> 
                            <a v-bind:href="organizer.instagram_link" class="custom-link">
                                <svg width="25px" height="25px" class="me-3 cursor-pointer" viewBox="0 0 3364.7 3364.7" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><defs><radialGradient id="0" cx="217.76" cy="3290.99" r="4271.92" gradientUnits="userSpaceOnUse"><stop offset=".09" stop-color="#fa8f21"></stop><stop offset=".78" stop-color="#d82d7e"></stop></radialGradient><radialGradient id="1" cx="2330.61" cy="3182.95" r="3759.33" gradientUnits="userSpaceOnUse"><stop offset=".64" stop-color="#8c3aaa" stop-opacity="0"></stop><stop offset="1" stop-color="#8c3aaa"></stop></radialGradient></defs><path d="M853.2,3352.8c-200.1-9.1-308.8-42.4-381.1-70.6-95.8-37.3-164.1-81.7-236-153.5S119.7,2988.6,82.6,2892.8c-28.2-72.3-61.5-181-70.6-381.1C2,2295.4,0,2230.5,0,1682.5s2.2-612.8,11.9-829.3C21,653.1,54.5,544.6,82.5,472.1,119.8,376.3,164.3,308,236,236c71.8-71.8,140.1-116.4,236-153.5C544.3,54.3,653,21,853.1,11.9,1069.5,2,1134.5,0,1682.3,0c548,0,612.8,2.2,829.3,11.9,200.1,9.1,308.6,42.6,381.1,70.6,95.8,37.1,164.1,81.7,236,153.5s116.2,140.2,153.5,236c28.2,72.3,61.5,181,70.6,381.1,9.9,216.5,11.9,281.3,11.9,829.3,0,547.8-2,612.8-11.9,829.3-9.1,200.1-42.6,308.8-70.6,381.1-37.3,95.8-81.7,164.1-153.5,235.9s-140.2,116.2-236,153.5c-72.3,28.2-181,61.5-381.1,70.6-216.3,9.9-281.3,11.9-829.3,11.9-547.8,0-612.8-1.9-829.1-11.9" fill="url(#0)"></path><path d="M853.2,3352.8c-200.1-9.1-308.8-42.4-381.1-70.6-95.8-37.3-164.1-81.7-236-153.5S119.7,2988.6,82.6,2892.8c-28.2-72.3-61.5-181-70.6-381.1C2,2295.4,0,2230.5,0,1682.5s2.2-612.8,11.9-829.3C21,653.1,54.5,544.6,82.5,472.1,119.8,376.3,164.3,308,236,236c71.8-71.8,140.1-116.4,236-153.5C544.3,54.3,653,21,853.1,11.9,1069.5,2,1134.5,0,1682.3,0c548,0,612.8,2.2,829.3,11.9,200.1,9.1,308.6,42.6,381.1,70.6,95.8,37.1,164.1,81.7,236,153.5s116.2,140.2,153.5,236c28.2,72.3,61.5,181,70.6,381.1,9.9,216.5,11.9,281.3,11.9,829.3,0,547.8-2,612.8-11.9,829.3-9.1,200.1-42.6,308.8-70.6,381.1-37.3,95.8-81.7,164.1-153.5,235.9s-140.2,116.2-236,153.5c-72.3,28.2-181,61.5-381.1,70.6-216.3,9.9-281.3,11.9-829.3,11.9-547.8,0-612.8-1.9-829.1-11.9" fill="url(#1)"></path><path d="M1269.25,1689.52c0-230.11,186.49-416.7,416.6-416.7s416.7,186.59,416.7,416.7-186.59,416.7-416.7,416.7-416.6-186.59-416.6-416.7m-225.26,0c0,354.5,287.36,641.86,641.86,641.86s641.86-287.36,641.86-641.86-287.36-641.86-641.86-641.86S1044,1335,1044,1689.52m1159.13-667.31a150,150,0,1,0,150.06-149.94h-0.06a150.07,150.07,0,0,0-150,149.94M1180.85,2707c-121.87-5.55-188.11-25.85-232.13-43-58.36-22.72-100-49.78-143.78-93.5s-70.88-85.32-93.5-143.68c-17.16-44-37.46-110.26-43-232.13-6.06-131.76-7.27-171.34-7.27-505.15s1.31-373.28,7.27-505.15c5.55-121.87,26-188,43-232.13,22.72-58.36,49.78-100,93.5-143.78s85.32-70.88,143.78-93.5c44-17.16,110.26-37.46,232.13-43,131.76-6.06,171.34-7.27,505-7.27S2059.13,666,2191,672c121.87,5.55,188,26,232.13,43,58.36,22.62,100,49.78,143.78,93.5s70.78,85.42,93.5,143.78c17.16,44,37.46,110.26,43,232.13,6.06,131.87,7.27,171.34,7.27,505.15s-1.21,373.28-7.27,505.15c-5.55,121.87-25.95,188.11-43,232.13-22.72,58.36-49.78,100-93.5,143.68s-85.42,70.78-143.78,93.5c-44,17.16-110.26,37.46-232.13,43-131.76,6.06-171.34,7.27-505.15,7.27s-373.28-1.21-505-7.27M1170.5,447.09c-133.07,6.06-224,27.16-303.41,58.06-82.19,31.91-151.86,74.72-221.43,144.18S533.39,788.47,501.48,870.76c-30.9,79.46-52,170.34-58.06,303.41-6.16,133.28-7.57,175.89-7.57,515.35s1.41,382.07,7.57,515.35c6.06,133.08,27.16,223.95,58.06,303.41,31.91,82.19,74.62,152,144.18,221.43s139.14,112.18,221.43,144.18c79.56,30.9,170.34,52,303.41,58.06,133.35,6.06,175.89,7.57,515.35,7.57s382.07-1.41,515.35-7.57c133.08-6.06,223.95-27.16,303.41-58.06,82.19-32,151.86-74.72,221.43-144.18s112.18-139.24,144.18-221.43c30.9-79.46,52.1-170.34,58.06-303.41,6.06-133.38,7.47-175.89,7.47-515.35s-1.41-382.07-7.47-515.35c-6.06-133.08-27.16-224-58.06-303.41-32-82.19-74.72-151.86-144.18-221.43S2586.8,537.06,2504.71,505.15c-79.56-30.9-170.44-52.1-303.41-58.06C2068,441,2025.41,439.52,1686,439.52s-382.1,1.41-515.45,7.57" fill="#ffffff"></path></g></svg>        
                                <span v-text="processUrl(organizer.instagram_link)"></span>
                            </a>
                        </p>
                    </template>
                    <template v-if="organizer.twitter_link">
                        <p class="mt-2"> 
                            <a v-bind:href="organizer.twitter_link"  class="custom-link">
                                <svg width="25px" height="25px" class="me-3 cursor-pointer"  viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M11.7887 28C8.55374 28 5.53817 27.0591 3 25.4356C5.15499 25.5751 8.95807 25.2411 11.3236 22.9848C7.76508 22.8215 6.16026 20.0923 5.95094 18.926C6.25329 19.0426 7.6953 19.1826 8.50934 18.856C4.4159 17.8296 3.78793 14.2373 3.92748 13.141C4.695 13.6775 5.99745 13.8641 6.50913 13.8174C2.69479 11.0882 4.06703 6.98276 4.74151 6.09635C7.47882 9.88867 11.5812 12.0186 16.6564 12.137C16.5607 11.7174 16.5102 11.2804 16.5102 10.8316C16.5102 7.61092 19.1134 5 22.3247 5C24.0025 5 25.5144 5.71275 26.5757 6.85284C27.6969 6.59011 29.3843 5.97507 30.2092 5.4432C29.7934 6.93611 28.4989 8.18149 27.7159 8.64308C27.7224 8.65878 27.7095 8.62731 27.7159 8.64308C28.4037 8.53904 30.2648 8.18137 31 7.68256C30.6364 8.52125 29.264 9.91573 28.1377 10.6964C28.3473 19.9381 21.2765 28 11.7887 28Z" fill="#47ACDF"></path> </g></svg>
                                <span v-text="processUrl(organizer.twitter_link)"></span>
                            </a>
                        </p>
                    </template>
                </div>
                
            </div>
        </div>
        <br> <br>
        {{-- </form> --}}
    </main>
    @include('__CommonPartials.Cropper')

</body>
<script src="{{ asset('/assets/js/organizer/PlayerProfile.js') }}"></script>

 

</html>
