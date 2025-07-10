<head>
    @vite([
        'resources/sass/app.scss',
        'resources/js/app.js',
        'resources/js/alpine/teamhead.js',
    ])
    <style>
        .category-icon {
            width: 20px;
            height: 20px;
        }

        .category-button {
            max-width: 130px;
        }

        .category-input {
            width: 200px;
        }

        .ts-wrapper {
            display: inline-block !important;
            background-color: white;
        }
        .ts-control {
            background-color: white;
            color: black;
            width: 200px !important;
            padding-top: 0.4rem !important;
            padding-bottom: 0.4rem !important;
            font-size: 1rem !important;
        }

         .ts-control, .ts-control input, .ts-control input::placeholder, .ts-dropdown,
        .ts-control, .ts-control .item, .ts-wrapper.single.input-active .ts-control {
            font-size: 0.9rem;
            color: rgb(46, 75, 89) !important;
        }

        .form-select {
            background-image: none;
        }

        #backgroundBanner svg:hover {
           scale: 1.2;
        }

        #backgroundBanner svg:active {
            fill: white !important;
            stroke: white !important;
            animation-duration: 3s;
        }

        .border-red {
            border-color: red !important;
        }
    </style>
</head>
@php
    $allCategorys = \App\Models\Game::all(['id', 'gameTitle', 'gameIcon'])
        ->mapWithKeys(function ($category) {
            return [
                $category->id => [
                    'id' => $category->id,
                    'gameTitle' => $category->gameTitle,
                    'gameIcon' => $category->gameIcon,
                ],
            ];
        });
    if (isset($user)) {
        $userId = $user->id;
        $role = $user->role;
        $teamMember = $selectTeam->findTeamMemberByUserId($user->id);
        $isUserFollowingTeam = $selectTeam->findTeamFollowerByUserId($user->id);
    } else {
        $userId = $role = $teamMember = null;
        $isUserFollowingTeam = false;
    }

    if (!isset($isCompactView)) {
        $isCompactView = false;
    }
    [
        'backgroundStyles' => $backgroundStyles,
        'fontStyles' => $fontStyles,
        'frameStyles' => $frameStyles
    ] = $selectTeam->profile?->generateStyles();
    if (!$backgroundStyles) {
        $backgroundStyles = "background-color: #fffdfb;"; // Default gray
    }
@endphp
@guest
    @php
        $teamMember = null;
        $isCreator = false;
        $loggedUserId = null;
        $status = "not_signed";
        $statusMessage = "Please sign in!";
        $acceptedTeamMemberCount = 0
    @endphp
@endguest
@auth
    @php
        if (isset($teamMember)) {
            $status = $teamMember->status;
            if ($teamMember?->actor == 'user') {
                $status .= '_me';
            } else {
                if ($status) $status .= '_team';
            }
        } else {
            $status = null;
        }

        $loggedUserId = $user->id;
        $isCreator = $selectTeam->creator_id == $loggedUserId;
        ['accepted' => $acceptedTeamMemberCount] = $selectTeam->getMembersAndTeamCount();
    @endphp
@endauth
<main class="main1 teamhead"
    v-scope="TeamHead()"
    @vue:mounted="init"
    id="backgroundBanner" 
    class="member-section px-2 py-2"
>

    <div class="team-head-storage d-none  "
        data-success-join="{{session('successJoin')}}"
        data-error-join="{{session('errorJoin')}}"
        data-route-signin="{{ route('participant.signin.view') }}"
        data-route-profile="{{ route('public.participant.view', ['id' => ':id']) }}"
        data-route-background-api="{{ route('user.userBackgroundApi.action', ['id' => $selectTeam->id]) }}"
        data-background-styles="<?php echo $backgroundStyles; ?>"
        data-font-styles="<?php echo $fontStyles; ?>"
        data-logged-user-id="{{ $loggedUserId }}"
        data-all-categories="{{json_encode($allCategorys)}}"
        data-logged-user-role="{{ $role }}"
    >
    </div>
    <input type="hidden" id="currentMemberUrl" value="{{ url()->current() }}">
    <input type="hidden" name="isRedirectInput" id="isRedirectInput" value="{{isset($redirect) && $redirect}}">
    <input type="hidden" id="participantMemberUpdateUrl" value="{{ route('participant.member.update', ['id' => ':id']) }}">
    <input type="hidden" id="participantMemberDeleteInviteUrl" value="{{ route('participant.member.deleteInvite', ['id' => ':id']) }}">
    <input type="hidden" id="participantMemberInviteUrl" value="{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}">

    <input type="hidden" id="teamData" value="{{json_encode($selectTeam->makeHidden(['members'])->toArray() ) }}">
    <input type="file" id="backgroundInput" class="d-none">
    {{-- @if ($isCreator) --}}
        <div class="team-section position-relative"
            {{-- x-data="alpineDataComponent" --}}
        >
            <div  v-cloak class="position-lg-absolute d-flex w-100 justify-content-end py-0 my-0 mt-2">
                <form method="POST" action="{{ route('participant.team.follow', ['id'=> $selectTeam->id]) }}">
                    @csrf
                    <button
                        type="submit"
                        v-if="!isEditMode"
                        @class(["
                            btn rounded-pill py-2 px-4 fs-7 mt-2",
                            'btn-primary text-light' => !$isUserFollowingTeam,
                            'btn-success' => $isUserFollowingTeam
                        ])
                    >
                        {{ $isUserFollowingTeam?  "Following" : "Follow" }}
                    </button>
                </form>
                @if ($isCreator)
                    <button
                         v-if="isEditMode"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#profileDrawer"
                        
                        {{-- onclick="document.getElementById('backgroundInput').click();" --}}
                        class="btn btn-secondary text-light rounded-pill py-2 fs-7 mt-2"
                    >
                        Change Background
                    </button>
                @endif
            </div>

        <div v-cloak :class="{'upload-container my-2': true, }" >
            <label class="upload-label">
                <div class="circle-container mt-1">
                    <div class="uploaded-image motion-logo "
                        style="background-image: url({{ '/storage' . '/'. $selectTeam->teamBanner  }} ), url({{asset('assets/images/404.png')}}) ; object-fit:cover; {{$frameStyles}}"
                    ></div>
                    <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                        <a aria-hidden="true" data-fslightbox="lightbox" data-href="{{ '/' . 'storage/' . $selectTeam->teamBanner }}">
                            <button class="btn btn-sm simple-button"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                            </svg>
                            </button>
                        </a>
                        @if ($isCreator)
                            <button  v-show="isEditMode"  id="upload-button2" class="simple-button btn btn-sm" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </button>
                            <button  v-show="isEditMode" v-on:click="removeProfile(event);"  id="trash-button3" class="simple-button btn btn-sm" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            </label>
        </div>
        <div>
            <div v-cloak :class="{'team-info  ': !isEditMode, '': true}">
                @if ($isCreator)
                    <div   v-show="isEditMode">
                        <input 
                            type="file" 
                            id="image-upload" 
                            accept="image/*" 
                            style="display: none;"
                        >
                        <div  v-show="errorMessage != null" class="text-red" v-text="errorMessage"> </div>
                        <div class="d-flex flex-row flex-wrap mt-1 justify-content-center">
                            <input
                                placeholder="Enter your team name..."
                                style="width: 200px;"
                                class="form-control border-secondary mb-2 player-profile__input d-inline me-4 d-inline"
                                v-model="teamName"
                                autocomplete="off"
                                autocomplete="nope"
                                type="text"
                                :style="{ color: fontColor  }"
                            >
                            <span  style="color: black !important;">
                                <svg
                                    class="me-2 mb-2"
                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" v-bind:fill="fontColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                </svg>
                                <select 
                                    style=" color: '#94087'; background-color: 'white'; width: 200px; font-size: 0.9rem;"
                                    v-on:change="changeFlagEmoji" 
                                    id="select2-country3" 
                                    class="d-inline form-select"  
                                    data-placeholder="Select a region" 
                                    v-bind:value="country || ''"
                                    v-bind:name="country"
                                >
                                </select>
                            </span>
                        </div>
                    </div>

                @endif
                    <h3    v-if="!isEditMode" style="{{$fontStyles}}" class="team-name ms-0 me-2 mt-2 py-0" id="team-name">
                        {{$selectTeam->teamName}}
                    </h3>

                @auth
                    @if ($user->role == "PARTICIPANT")
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button
                            
                             v-if="!isEditMode"
                            class="gear-icon-btn me-2 position-relative z-99"  type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="{{$selectTeam->profile?->fontColor}}" class="bi bi-gear-fill" viewBox="0 0 16 16">
                                <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu border border-secondary shadow-lg z-999 py-0" style="font-size: 0.875rem;" aria-labelledby="dropdownMenuButton">
                            <div>
                                @if ($isCreator)
                                    <p class="w-100 btn btn-light rounded-none h-100 py-2 my-0" style="background: white;">
                                        <small class="ms-2">
                                            You've created this team
                                        </small>
                                    </p>
                                    <hr class="py-0 my-0">
                                @endif
                            </div>
                            <a class="dropdown-item py-2" href="/participant/team/{{ $selectTeam->id }}/manage">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                                </svg>
                                <span class="ms-2"> Team Profile </span>
                            </a>
                            @if ($status == "accepted_team" || $status == "accepted_me")
                                <a class="dropdown-item py-2" href="/participant/team/{{ $selectTeam->id }}/register">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16">
                                    <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z"/>
                                    </svg>
                                    <span class="ms-2"> Manage Registration </span>
                                </a>
                            @endif
                            @if ($isCreator)
                                <a class="dropdown-item py-2" href="/participant/team/{{ $selectTeam->id }}/manage/member">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                    <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                                    </svg>
                                    <span class="ms-2"> Manage Members </span>
                                </a>
                            @endif
                        </div>
                        @if ($isCreator)
                            <button
                                v-if="!isEditMode"
                                v-on:click=" isEditMode = true;"
                                class="gear-icon-btn me-2 position-relative"
                                 type="button" id="editModalBtn"
                                aria-expanded="false"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="{{$selectTeam->profile?->fontColor}}" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                                </svg>
                            </button>
                        @endif
                        @if ($status == "accepted_me" || $status == "accepted_team")
                            <button
                                
                                 v-if="!isEditMode"
                                style=" background-color: transparent; "
                                class="me-2 badge  btn bg-primary text-white  px-2 position-relative"
                            >
                                <svg style="position: relative; top: -1px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color  bi-award" viewBox="0 0 16 16">
                                    <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z"/>
                                    <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z"/>
                                </svg>
                                <span> Your team </span>
                            </button>
                        @elseif ($status == "left_team")
                            <button
                                
                                 v-if="!isEditMode"
                                style=" background-color: #299e29; "
                                class="me-2 badge  btn  text-white px-2 position-relative"
                            >
                                <svg style="position: relative; top: -1px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color  bi-award" viewBox="0 0 16 16">
                                    <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z"/>
                                    <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z"/>
                                </svg>
                                <span> Your previous team </span>
                            </button>
                        @elseif ($status == "left_me" )
                            <button
                                
                                 v-if="!isEditMode"
                                style=" background-color: #299e29; "
                                class="me-2 badge  btn  text-white px-2 position-relative"
                            >
                                <svg style="position: relative; top: -1px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color  bi-award" viewBox="0 0 16 16">
                                    <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z"/>
                                    <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z"/>
                                </svg>
                                <span> Your previous team </span>
                            </button>
                                <button  class="btn rounded-pill border-2 bg-white border-primary btn-sm  position-relative "
                                    type="button"  onclick="rejoinTean({{$teamMember->id}})"
                                >
                                    <span class="text-primary   "> Rejoin team! </span>
                                </button>
                        @endif
                    </div>
                    @endif
                @endauth
                @if ($role == "PARTICIPANT")
                    @if (is_null($status))
                        @if ($acceptedTeamMemberCount>=10)
                            <button  class="btn btn-primary bg-white rounded-pill border-2 btn-sm" type="button" disabled>
                                <span> Full </span>
                            </button>
                        @else
                            @if ($acceptedTeamMemberCount>=10)
                                <button  class="btn btn-primary bg-secondary rounded-pill border-2 btn-sm" type="button" disabled>
                                    <span> Full </span>
                                </button>
                            @else
                                @if ($selectTeam->status == "private")
                                    <button  class="btn btn-primary bg-secondary rounded-pill border-2 btn-sm" type="button" disabled>
                                        <span> Private </span>
                                    </button>
                                @elseif ($selectTeam->status == "open")

                                    <form  v-if="!isEditMode"  class="d-block d-lg-inline-block  px-0" method="POST" action="{{route('participant.member.pending', ['id' => $selectTeam->id]) }}">
                                        @csrf()
                                        <button  class="btn btn-primary bg-white rounded-pill border-2 btn-sm" type="submit">
                                            <span class="text-primary"> Join Team </span>
                                        </button>
                                    </form>
                                @else 
                                    <form  v-if="!isEditMode"  class="d-block d-lg-inline-block  px-0" method="POST" action="{{route('participant.member.pending', ['id' => $selectTeam->id]) }}">
                                        @csrf()
                                        <button  class="btn btn-primary bg-white rounded-pill border-2 btn-sm" type="submit">
                                            <span class="text-primary"> Apply to join </span>
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                        
                    @elseif ($status == "pending_me")
                        <div  v-if="!isEditMode"  class="d-block d-lg-inline-block   px-0" >
                            <span  class=" btn btn-success text-dark btn-sm " type="button">
                                 Applied to join 
                            </span>
                            <button class="gear-icon-btn mt-0 ms-1"
                                onclick="withdrawInviteMember({{ $teamMember->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                            </button>
                        </div>

                    @elseif ($status == "pending_team" )
                        <div  v-if="!isEditMode"  class="d-block d-lg-inline-block   px-0" >
                            <button onclick="approveMember({{$teamMember->id}})"  class="btn rounded-pill border-2 btn-success bg-white btn-sm me-1" type="button">
                                <span class="text-success"> Yes, join team </span>
                            </button>
                            </button>
                            <button onclick="rejectMember({{$teamMember->id}})"  class="btn rounded-pill border-2 border border-red bg-white btn-sm  " type="button">
                                 <span class="text-red">Reject</span>   
                            </button>
                        </div>
                    @elseif ($status == "rejected_me" )
                        <div  v-if="!isEditMode"  class="d-block d-lg-inline-block   px-0" >
                            <button
                                class="me-2 btn badge text-red bg-white py-1 px-2"
                                style="border: 1px solid red; pointer-events: none;"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                                <span> You rejected team</span>
                            </button>
                            <button onclick="approveMember({{$teamMember->id}})"  class="btn border rounded-pill border-2 border-success bg-white btn-sm text-success " type="button">
                                No, Join
                            </button>
                        </div>
                    @elseif ($status == "rejected_team" )
                        <div  v-if="!isEditMode"  class="d-block d-lg-inline-block  pt-1 px-0" >
                            <button
                                disabled
                                style="pointer-events: none; border: none;"
                                class="me-2 badge bg-white text-red py-1 px-2"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                                <span> Rejected by team </span>
                            </button>
                            {{-- <small>Perhaps, they will change their mind in future.</small> --}}
                        </div>
                    @endif
                @endif
                
            </div>
            <div class="text-center my-0 py-0 text-red">@if (session('errorJoin'))
                    <div>{{session('errorJoin')}}</div>
                @endif</div>
        </div>
            @if ($isCreator)
                <div v-if="isEditMode" v-cloak>
                <div
                    class="d-flex justify-content-center mt-2 align-items-center"
                    
                >
                
                    <input
                        placeholder="Enter your team description..."
                        class="form-control border-secondary player-profile__input d-inline-block py-2 mx-3"
                        v-model="teamDescription"
                        autocomplete="off"
                        autocomplete="nope"
                        :style="{ color: fontColor, borderColor: fontColor, width: 'min(100vw,450px)'  }"    
                    >
                   
                </div> 
                <div class=" my-3">
                    <div v-scope="CategoryManager()" @vue:mounted="init" class="text-center mx-auto category-management">
                        
                        <div class="d-inline-flex mb-2 justify-content-between align-items-center flex-wrap" v-for="element in userCategoriesArr" :key="element.id">
                            <div class=" d-flex flex-col rounded-2 justify-content-between align-items-center px-0 py-0  mx-1"
                                :class="{'invisible-until-hover-parent': element.id != defaultCategory }"
                                :style="{ color: '#43a4d7', backgroundColor: 'white', border: '1px solid #e7e7e7'  }"
                            >
                                <div class="category-button d-inline-block text-ellipsis me-2">
                                    <img class="object-fit-cover rounded rounded-2" v-bind:src="'/storage/' + element.gameIcon" width="30" height="30">
                                    <span>@{{ element?.gameTitle }}</span>
                                </div>
                                <span class="ms-2">
                                    <small class="cursor-pointer" v-if="element.id == defaultCategory">
                                        <svg width="14" height="14" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#f3ecec" stroke="#f3ecec"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="m 8 0 c -4.40625 0 -8 3.59375 -8 8 s 3.59375 8 8 8 s 8 -3.59375 8 -8 s -3.59375 -8 -8 -8 z m 3.398438 4.507812 c 0.265624 -0.027343 0.527343 0.050782 0.734374 0.21875 c 0.425782 0.351563 0.488282 0.980469 0.140626 1.40625 l -4.5 5.5 c -0.179688 0.21875 -0.441407 0.351563 -0.722657 0.367188 c -0.28125 0.011719 -0.558593 -0.09375 -0.757812 -0.292969 l -2.5 -2.5 c -0.390625 -0.390625 -0.390625 -1.023437 0 -1.414062 s 1.023437 -0.390625 1.414062 0 l 1.71875 1.71875 l 3.800781 -4.644531 c 0.167969 -0.203126 0.410157 -0.335938 0.671876 -0.363282 z m 0 0" fill="#43a4d7"></path> </g></svg>
                                    </small>
                                    <small v-else class="cursor-pointer invisible-until-hover" v-on:click="makeDefaultCategory(event, element.id)">
                                        <svg width="14" height="14" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="#f3ecec" stroke="#f3ecec"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="m 8 0 c -4.40625 0 -8 3.59375 -8 8 s 3.59375 8 8 8 s 8 -3.59375 8 -8 s -3.59375 -8 -8 -8 z m 3.398438 4.507812 c 0.265624 -0.027343 0.527343 0.050782 0.734374 0.21875 c 0.425782 0.351563 0.488282 0.980469 0.140626 1.40625 l -4.5 5.5 c -0.179688 0.21875 -0.441407 0.351563 -0.722657 0.367188 c -0.28125 0.011719 -0.558593 -0.09375 -0.757812 -0.292969 l -2.5 -2.5 c -0.390625 -0.390625 -0.390625 -1.023437 0 -1.414062 s 1.023437 -0.390625 1.414062 0 l 1.71875 1.71875 l 3.800781 -4.644531 c 0.167969 -0.203126 0.410157 -0.335938 0.671876 -0.363282 z m 0 0" fill="#43a4d7"></path> </g></svg>
                                    </small>
                                    <small class="cursor-pointer me-1" v-on:click="removeCategory(event, element.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-circle cursor-pointer text-red " viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"></path>
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"></path>
                                        </svg>
                                    </small>
                                </span>
                            </div>
                        </div>
                        <select id="default-category" class="category-input text-start" >
                            <option value="">Add your game title</option>
                        </select>
                    </div>
                </div>
                <div class="my-3 d-flex justify-content-center flex-wrap" v-scope="TeamSettings()"> 
                        
                    <small class="form-check  form-switch mb-1 me-3">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="receiveInvites"
                            :checked="settings.receiveInvites"
                            v-on:change="settings.setReceiveInvites($event.target.checked)"
                            v-model="settings.receiveInvites"
                        >
                        <label class="form-check-label" for="receiveInvites">
                            Receive invites from participants
                        </label>
                    </small>

                    <small class="form-check  form-switch mb-1 me-3">
                        <input 
                            class="form-check-input" 
                            type="checkbox" 
                            id="needsPermission"
                            v-model="settings.needsPermission"
                            :disabled="!settings.receiveInvites"
                        >
                        <label class="form-check-label" for="needsPermission">
                            Participants need permission to join
                        </label>
                    </small>

                    <small class=" mb-1">
                        <input 
                            class="form-control d-inline-block py-0 me-2" 
                            style="width: 65px;" 
                            type="number"
                            id="member_limit"
                            min="5"
                            max="50"
                            name="member_limit"
                            v-model="member_limit" 
                            placeholder="Enter limit"
                        >
                        <label class="form-check-label " for="member_limit">
                            Team member limit:
                        </label>
                        
                    </small>


                </div>
                <div class="d-flex justify-content-center my-3">
                    <span
                        v-on:click="submitEditProfile(event);"
                        data-url="{{route('participant.team.update')}}"
                        :style="{  borderColor: fontColor, color: fontColor  }" 
                        class="rounded-3 btn  px-3 py-1  rounded cursor-pointer me-3   ">
                        Save
                    </span>
                    <svg
                        v-on:click="reset();isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-x-circle cursor-pointer text-red " viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg> 
                </div>
                </div> 
                <div class="my-0 d-flex justify-content-center align-items-center" v-cloak  v-if="!isEditMode">
                    <span class="ms-2" >{{$selectTeam->teamDescription ?? 'Add a team description'}}</span>
                    @if ($selectTeam->country_flag)
                        <span class="ms-2 mt-2 fs-5">{{$selectTeam->country_flag}}</span>
                        <span class="fw-bold  fs-7 ms-2 me-2 ">{{ $selectTeam->country_name }}</span>  
                    @endif
                    <span class="ms-1  badge bg-primary" data-bs-toggle="tooltip" v-bind:title="teamStatus[1]">@{{teamStatus[0]}} Team
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-question-circle ms-1 cursor-pointer" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
                        </svg>
                    </span>
                </div>
            @else
                @if ($selectTeam->teamDescription != '' || $selectTeam->country_flag != '' || isset($teamMember))
                    <p  v-cloak class="mb-0 mt-2 py-0 d-flex justify-content-center align-items-center">
                        <span class="d-inline">{{$selectTeam->teamDescription}}</span>
                        @if ($selectTeam->country_flag)
                            <span class="d-inline ms-2 fs-5">{{$selectTeam->country_flag}}</span>
                            <span class="fw-bold  fs-7 ms-2 me-2 ">{{ $selectTeam->country_name }}</span>  
                        @endif
                        <span class="ms-2 badge bg-primary" data-bs-toggle="tooltip" v-bind:title="teamStatus[1]">@{{teamStatus[0]}} Team 
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1 cursor-pointer bi bi-question-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94"/>
                            </svg>
                        </small>
                    </p>
                 @endif
            @endif
            <div  v-cloak v-show="!isEditMode" class="row w-75-lg-60 py-1 mt-2 mb-1  ">
                <div class="col-12 mb-2 mt-0">
                    <div v-scope="CategoryManager()" @vue:mounted="init" class="text-center mx-auto ">
                        <div class="d-inline-flex cursor-pointer justify-content-center align-items-center position-relative mx-1" v-for="element in userCategoriesArr" :key="element.id">
                            <div class="rounded-2 text-primary"
                                
                                {{-- style="background-color: white; color: #43a4d7; padding: 2px;" --}}
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                v-bind:title="element.gameTitle"
                            >
                                
                                <img :class="{'border border-2 border-primary': element.id == defaultCategory }" class="object-fit-cover rounded-2" v-bind:src="'/storage/' + element.gameIcon" width="45" height="40">
                                <span class="position-absolute rounded-circle d-inline-block " style="right: 0%; top: -25%;  z-index: 50; width: 15px; height: 15px;" v-if="element.id == defaultCategory">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white"  class="bi bi-check-circle-fill text-primary" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="col-12 col-lg-4 text-center mx-auto"> 
                        <svg fill="currentColor" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                            width="25px" height="25px" viewBox="0 0 96.979 96.979" xml:space="preserve" class="me-2"
                        ><g id="SVGRepo_bgCarrier" stroke-width="0">
                            </g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <g> <path d="M59.07,46.021L59.07,46.021c4.576-3.373,7.31-8.754,7.31-14.393c0-9.863-8.025-17.889-17.89-17.889 c-9.864,0-17.889,8.025-17.889,17.889c0,5.717,2.66,10.959,7.297,14.385c-18.244,6.451-21.092,28.71-21.531,35.378 c-0.031,0.479,0.137,0.949,0.465,1.3c0.328,0.35,0.785,0.549,1.264,0.549h60.788c0.479,0,0.938-0.199,1.266-0.549 c0.328-0.351,0.496-0.82,0.465-1.3C80.175,74.736,77.32,52.511,59.07,46.021z"></path> <path d="M82.761,46.861c3.02-2.227,4.821-5.779,4.821-9.502c0-6.508-5.297-11.805-11.807-11.805c-1.867,0-3.627,0.447-5.199,1.223 c0.345,1.564,0.529,3.184,0.529,4.852c0,4.68-1.484,9.219-4.137,12.988c10.448,6.572,14.981,18.07,16.944,26.81h11.923 c0.315,0,0.618-0.131,0.836-0.361c0.215-0.23,0.325-0.541,0.305-0.857C96.688,65.812,94.805,51.144,82.761,46.861z"></path> <path d="M29.976,44.617c-2.654-3.748-4.104-8.238-4.104-12.988c0-1.668,0.188-3.287,0.531-4.852 c-1.572-0.775-3.332-1.223-5.199-1.223c-6.51,0-11.807,5.297-11.807,11.805c0,3.775,1.754,7.236,4.816,9.496 C2.172,51.113,0.291,65.806,0.002,70.207c-0.021,0.316,0.09,0.627,0.307,0.857c0.217,0.229,0.52,0.36,0.836,0.36H13.06 C15.019,62.685,19.543,51.179,29.976,44.617z"></path> </g> </g> </g>
                        </svg>
                        <span>{{$acceptedTeamMemberCount}}/{{$selectTeam->member_limit}} Members </span>
                    </div>
                    <div onclick="openModal('followers')" class="user-select-none position-relative col-12 col-lg-4 text-center mx-auto cursor-pointer"
                        style="z-index: 998 !important; "
                    > 
                        <span class="me-2"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                            <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                            </svg>
                        </span>
                        <span data-follower-stats="{{ is_null($selectTeam?->profile?->follower_count) ? 0 : $selectTeam->profile->follower_count}}" >{{ is_null($selectTeam?->profile?->follower_count) ? 0 . ' Followers'  : $selectTeam->profile->follower_count. ' Follower'. bldPlural($selectTeam->profile->follower_count) }} </span>
                    </div>
                    <div class="col-12 col-lg-4 text-center mx-auto"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi me-2 bi-calendar-date" viewBox="0 0 16 16">
                        <path d="M6.445 11.688V6.354h-.633A13 13 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23"/>
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                        </svg>
                        {{$selectTeam->createdAtHumaReadable()}}
                    </div>
                </div>
            </div>
            @if ($status == "accepted_me" || $status == "accepted_team")
                <div v-cloak class="position-absolute d-flex w-100 justify-content-end py-0 my-0 mt-2" style="bottom: 20px;">
                    <button   v-if="!isEditMode" class="bg-red px-3 btn rounded-pill btn-sm py-2 text-white "
                        onclick="disapproveMember({{ $teamMember->id }})"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-in-right me-1" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                        <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                        <span class="d-none d-lg-inline">Leave Team</span>
                    </button>
                </div>
            @endif
       
    </div>
    @include('includes.Profile.ProfileStatsModal', [
        'propsTeamOrUserId' => $selectTeam->id,
        'propsUserId' => $userId ?? 0,
        'propsIsUserSame' => 0, 
        'propsRole' => "TEAM", 
        'propsUserRole' => $role
    ])
</main>

@include('includes.Team.TeamBackgroundModal')



<script src="{{ asset('/assets/js/organizer/DialogForMember.js') }}"></script>
<script src="{{ asset('/assets/js/participant/TeamHead.js') }}"></script>
@include('includes.Profile.Cropper')
