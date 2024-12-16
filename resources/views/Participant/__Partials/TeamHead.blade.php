<head>
    @vite([
        'resources/sass/app.scss', 
        'resources/js/app.js', 
        'resources/js/alpine/teamhead.js', 
        'resources/js/libraries/lightgallery.js',
        'resources/js/libraries/motion.js',
        'resources/sass/libraries/lightgallery.scss',
        'resources/js/libraries/colorpicker.js',
        'resources/sass/libraries/colorpicker.scss',
        'resources/js/libraries/file-edit.js',
        'resources/sass/libraries/file-edit.scss',
    ])
</head>
@php
    if (isset($user)) {
        $role = $user->role;
    } else {
        $role = null;
    }

    if (!isset($isCompactView)) {
        $isCompactView = false;
    }
    [   
        'backgroundStyles' => $backgroundStyles, 
        'fontStyles' => $fontStyles, 
        'frameStyles' => $frameStyles
    ] = $selectTeam->profile?->generateStyles();
@endphp
@guest
    @php
        $teamMember = null;
        $isCreator = false;
        $status = "not_signed";
        $statusMessage = "Please sign in!";
    @endphp
@endguest
@auth
    @php
        $teamMember = App\Models\TeamMember::where('team_id', $selectTeam->id)
            ->where('user_id', $user->id)->first();
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

        $isCreator = $selectTeam->creator_id == $user->id;
        // $countsTeamHead = $selectTeam->getMembersAndTeamCount();
    @endphp
@endauth
<main class="main1" 
    id="backgroundBanner" class="member-section px-2 py-2"
    @style([
        "background-size: cover; background-repeat: no-repeat; min-height: 35vh;" 
    ])
>    

    <div class="team-head-storage d-none"
        data-route-signin="{{ route('participant.signin.view') }}"
        data-route-profile="{{ route('public.participant.view', ['id' => ':id']) }}"
        data-route-team-banner="{{ route('participant.teamBanner.action', ['id' => $selectTeam->id]) }}"
        data-route-background-api="{{ route('user.userBackgroundApi.action', ['id' => $selectTeam->id]) }}"
        data-background-styles="<?php echo $backgroundStyles; ?>"
        data-font-styles="<?php echo $fontStyles; ?>"
    >
    </div>
    <input type="hidden" id="currentMemberUrl" value="{{ url()->current() }}">
    <input type="hidden" name="isRedirectInput" id="isRedirectInput" value="{{isset($redirect) && $redirect}}">
    <input type="hidden" id="participantMemberUpdateUrl" value="{{ route('participant.member.update', ['id' => ':id']) }}">
    <input type="hidden" id="participantMemberDeleteInviteUrl" value="{{ route('participant.member.deleteInvite', ['id' => ':id']) }}">
    <input type="hidden" id="participantMemberInviteUrl" value="{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}">

    <input type="hidden" id="teamData" value="{{json_encode($selectTeam)}}">
    <input type="file" id="backgroundInput" class="d-none"> 
    {{-- @if ($isCreator) --}}
        <div class="team-section" 
            x-data="alpineDataComponent"
        >
        @if ($isCreator)
            <div  class="d-flex w-100 justify-content-end py-0 my-0 mt-2">
                <button 
                x-show="isEditMode"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#profileDrawer"
                    x-cloak
                    {{-- onclick="document.getElementById('backgroundInput').click();" --}}
                    class="btn btn-secondary text-light rounded-pill py-2 fs-7 mt-2"
                > 
                    Change Background
                </button>
            </div>
        @else 
            <br>
        @endif
    
        <div :class="{'upload-container': true, }">
            <label class="upload-label">
                <div class="circle-container">
                    <div class="uploaded-image motion-logo "
                        style="background-image: url({{ '/storage' . '/'. $selectTeam->teamBanner  }} ), url({{asset('assets/images/404.png')}}) ; object-fit:cover; {{$frameStyles}}"
                    ></div>
                    <div class="d-flex align-items-center justify-content-center upload-button pt-3">
                        <a aria-hidden="true" data-fslightbox="lightbox" href="{{ '/' . 'storage/' . $selectTeam->teamBanner }}">
                            <button class="btn btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                            </svg> 
                            </button>
                        </a>    
                        @if ($isCreator)
                            <button x-show="isEditMode"  id="upload-button2" class="btn btn-sm" aria-hidden="true">
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
        <div>
            <div :class="{'team-info': !isEditMode, '': true}">
                @if ($isCreator)
                    <div x-cloak x-show.important="isEditMode">
                        <input type="file" id="image-upload" accept="image/*" style="display: none;">
                        <br>
                        <div x-show="errorMessage != null" class="text-red" x-text="errorMessage"> </div>
                        <div>
                            <input 
                                placeholder="Enter your team name..."
                                style="width: 200px;"
                                class="form-control border-secondary player-profile__input d-inline me-4 d-inline" 
                                x-model="teamName"
                                autocomplete="off"
                                autocomplete="nope"
                                type="text"
                            >
                            <span class="d-inline-flex justify-between" style="color: black !important;">
                                <svg
                                    class="me-2 mt-3" 
                                    xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
                                </svg>
                                <select x-on:change="changeFlagEmoji" id="select2-country3" style="width: 150px;" class="d-inline form-select"  data-placeholder="Select a country" x-model="country"> 
                                </select>
                            </span>
                        </div>
                    </div>
                   
                @endif
                    <h3  x-cloak x-show.important="!isEditMode" style="{{$fontStyles}}" class="team-name ms-0 me-2 mt-2 py-0" id="team-name">
                        {{$selectTeam->teamName}}
                    </h3>

                @auth
                    @if ($user->role == "PARTICIPANT")
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button
                            x-cloak
                            x-show.important="!isEditMode"
                            class="gear-icon-btn me-2 position-relative z-99" style="top: 10px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
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
                                x-cloak
                                x-show="!isEditMode"
                                x-on:click="reset(); isEditMode = true;"
                                class="gear-icon-btn me-2 position-relative" 
                                style="top: 10px;" type="button" id="editModalBtn" 
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
                                x-cloak
                                x-show="!isEditMode"
                                style="top: 10px; background-color: transparent; "
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
                                x-cloak
                                x-show="!isEditMode"
                                style="top: 10px; background-color: #299e29; "
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
                                x-cloak
                                x-show="!isEditMode"
                                style="top: 10px; background-color: #299e29; "
                                class="me-2 badge  btn  text-white px-2 position-relative" 
                            >
                                <svg style="position: relative; top: -1px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color  bi-award" viewBox="0 0 16 16">
                                    <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z"/>
                                    <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z"/>
                                </svg> 
                                <span> Your previous team </span>
                            </button>
                                <button x-cloak class="btn btn-primary bg-light badge btn-link position-relative py-2" 
                                    type="button" style="top: 10px;" onclick="approveMember({{$teamMember->id}})"
                                >
                                    <span class="text-primary"> Rejoin team! </span>
                                </button>
                        @endif
                    </div>
                    @endif
                @endauth
                @if ($role == "PARTICIPANT")
                    @if (is_null($status))
                        <form x-show.important="!isEditMode" x-cloak class="d-inline-block pt-1 px-0" method="POST" action="{{route('participant.member.pending', ['id' => $selectTeam->id]) }}">
                            @csrf()
                            <button style="font-size: 0.875rem;" class="btn btn-primary bg-light btn-sm btn-link" type="submit">
                                <span> Join Team </span>
                            </button>
                        </form>
                    @elseif ($status == "pending_me")
                        <div x-show.important="!isEditMode" x-cloak class="d-inline-block pt-1 px-0" >
                            <button style="font-size: 0.875rem;" class="btn btn-primary bg-light btn-sm btn-link" type="button">
                                <span> Requested, wait please... </span>
                            </button>
                            <button class="gear-icon-btn mt-0 ms-1"
                                onclick="withdrawInviteMember({{ $teamMember->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                    <path
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                </svg>
                            </button>
                        </div>
                        {{-- <span  x-show.important="!isEditMode" x-cloak class="d-inline-block mt-2 ps-2 ms-0 me-2 pt-2 badge rounded-pill border form-color d-inline"></span> --}}

                    @elseif ($status == "pending_team" )
                        <div x-show.important="!isEditMode" x-cloak class="d-inline-block pt-1 px-0" >
                            <button onclick="approveMember({{$teamMember->id}})" style="font-size: 0.875rem;" class="btn btn-success bg-light btn-sm btn-link me-1" type="button">
                                <span class="text-success"> Yes, join team </span>
                            </button>
                            <button onclick="rejectMember({{$teamMember->id}})" style="font-size: 0.875rem;" class="btn border border-danger bg-light btn-sm  btn-link" type="button">
                                <span class="text-red">Reject</span>
                            </button>
                        </div>
                        {{-- <span  x-show.important="!isEditMode" x-cloak class="d-inline-block mt-2 ps-2 ms-0 me-2 pt-2 badge rounded-pill border form-color d-inline"></span> --}}
                    @elseif ($status == "rejected_me" )
                        <div x-show.important="!isEditMode" x-cloak class="d-inline-block pt-1 px-0" >
                            <button 
                                class="me-2 btn btn-sm text-red bg-light py-1 px-2" 
                                style="border: 1px solid red; pointer-events: none;"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi svg-font-color bi-x-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                </svg>
                                <span> Rejected team</span>
                            </button>
                            <button onclick="approveMember({{$teamMember->id}})" style="font-size: 0.875rem;" class="btn border border-success bg-light btn-sm " type="button">
                                <span class="text-success">Change decision</span>
                            </button>
                        </div>
                    @elseif ($status == "rejected_team" )
                        <div x-show.important="!isEditMode" x-cloak class="d-inline-block pt-1 px-0" >
                            <button 
                                disabled
                                style="pointer-events: none; border: none;"
                                class="me-2 btn-sm bg-light text-red py-1 px-2" 
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
        </div>
            @if ($isCreator)
                <div
                    class="d-flex justify-content-center align-items-center"
                    x-cloak
                    x-show.important="isEditMode"
                >
                    <input 
                        placeholder="Enter your team description..."
                        class="form-control border-secondary player-profile__input d-inline py-2 me-5" 
                        x-model="teamDescription"
                        autocomplete="off"
                        autocomplete="nope"
                    >
                    <a 
                        x-on:click="submitEditProfile(event);"
                        data-url="{{route('participant.team.update')}}"
                        class="mt-4 oceans-gaming-default-button btn oceans-gaming-transparent-button simple-button px-3 py-1  rounded cursor-pointer  mx-auto me-3 mb-4"> 
                        Save
                    </a>
                    {{-- Close icon --}}
                    <svg 
                        style="top: 10px;"
                        x-on:click="isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-x-circle cursor-pointer text-red position-relative mb-4" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div >
                    <span class="ms-2" x-cloak x-show="!isEditMode">{{$selectTeam->teamDescription}}</span>
                    <span class="ms-2 mt-2 fs-3"x-show="!isEditMode">{{$selectTeam->country_flag}}</span>
                  
                </div>
            @else
                <p class="my-0 py-0">
                    <span class="d-inline">{{$selectTeam->teamDescription}}</span>
                    <span class="d-inline ms-2 fs-3">{{$selectTeam->country_flag}}</span>
                </p>
            @endif
       
        <div class="mx-auto text-center mt-1 ">
            @if (session('successJoin'))
                <span class="text-success">
                    {{ session('successJoin') }}
                </span> <br>
            @elseif (session('errorJoin'))
                <span class="text-red">
                    {{ session('errorJoin') }}
                </span> <br>
            @endif
        </div>
    </div>
</main>
@include('Participant.__Partials.BackgroundModal')
    <script src="{{ asset('/assets/js/organizer/DialogForMember.js') }}"></script>

<script src="{{ asset('/assets/js/participant/TeamHead.js') }}"></script>
        
@include('__CommonPartials.Cropper')
