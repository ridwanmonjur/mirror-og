<head>
    @vite([
        'resources/sass/app.scss', 
        'resources/js/app.js', 
        'resources/js/alpine.js', 
        'resources/js/lightgallery.js',
        'resources/sass/lightgallery.scss',
        'resources/js/file-upload-preview.js',
        'resources/sass/file-upload-preview.scss',
        'resources/js/colorpicker.js',
        'resources/sass/colorpicker.scss',
    ])

</head>
@php
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
            ->where('user_id', $user->id)->get();
        if (isset($teamMember[0])) {
            $status = $teamMember[0]->status;
            if ($status == 'rejected' && $teamMember[0]?->actor == 'user') {
                $status = 'rejected_me';
            }
        } else {
            $status = null;
        }

        $statusMessage = [
            'accepted' => "You're a member of this team.",
            "invited" => "You've been invited to this team.",
            "rejected_me" => "You've declined to join this team.",
            "rejected" => "You've been rejected to this team.",
            "pending" => "You've requested to join this team."
        ];

        $isCreator = $selectTeam->creator_id == $user->id;
    @endphp
@endauth
<main class="main1" 
        id="backgroundBanner" class="member-section px-2 py-2"
        @style([
            "background-size: cover; background-repeat: no-repeat; min-height: 35vh;" 
        ])
    >    
    <div class="modal fade" id="profileModal" tabindex="2" aria-labelledby="#profileModal" aria-hidden="true" style="color: black;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body" style="margin: auto;">
                    <div class="tabs mx-0 d-flex flex-row justify-content-center px-0" style="width: 100% !important;">
                        <button class="tab-button  ms-0 px-0 modal-tab tab-button-override tab-button-active"
                            onclick="showTab(event, 'BackgroundPhoto', 'modal-tab')">Background Photo</button>
                        <button class="tab-button  ms-0 px-0 modal-tab tab-button-override"
                            onclick="showTab(event, 'BackgroundColor', 'modal-tab')">Background
                            Color</button>
                        <button class="tab-button ms-0 px-0 modal-tab tab-button-override "
                            onclick="showTab(event, 'ForeColor', 'modal-tab')">Foreground
                            Color</button>
                    </div>
                    <div class="tab-content pb-4 modal-tab" id="BackgroundPhoto">
                        <form id="updateBgColorRequest" class="d-inline" method="POST" action="{{ route('user.userBackground.action', ['id' => $selectTeam->id] ) }}"> 
                            @csrf
                            <input type="hidden" name="backgroundColor" value="{{ $selectTeam->profile?->backgroundColor }}">
                            <input type="hidden" name="backgroundGradient" value="{{ $selectTeam->profile?->backgroundGradient }}">
                            <input type="hidden" name="teamId" value="{{ $selectTeam->id }}">
                        </form>
                        <div class="mx-auto"  style="max-width: max(400px, 75%);">
                            <br>
                            <h5> Choose a background banner</h5>
                            <div class="custom-file-container" data-upload-id="file-upload-preview-1"></div>
                            <br>

                            <div class="d-flex justify-content-center" >
                                <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                    data-bs-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content pb-4 modal-tab d-none" id="BackgroundColor">
                        <div class="mx-auto"  style="max-width: max(400px, 75%);">
                            <br>
                            <h5> Choose a solid color</h5>
                            <div class="mx-auto">
                                @foreach ([
            'black' => 'Black',
            '#545454' => '#545454',
            '#737373' => '#737373',
            'gray' => 'Gray',
            'lightgray' => 'Light gray',
            'white' => 'White',
            '#e0ffff' => 'Light Cyan',
            '#add8e6' => 'Light Blue',
            '#b0c4de' => 'Light Steel Blue',
            '#ffe4b5' => 'Moccasin',
            '#ffffe0' => 'Light Yellow',
            '#f0e68c' => 'Khaki',
            '#dda0dd' => 'Plum',
            '#cd5c5c' => 'Indian Red',
            '#ffa07a' => 'Light Salmon',
            '#ff7f50' => 'Coral',
            '#ff6347' => 'Tomato',
            '#f08080' => 'Light Coral',
            '#800080' => '#800080',
            '#00ff7f' => 'Spring Green',
            '#4682b4' => 'Steel Blue',
            '#e9967a' => 'Dark Salmon',
            '#8fbc8f' => 'Dark Sea Green',
            'brown' => 'Brown',
            'maroon' => 'Maroon',
            'red' => 'Red',
            '#ff4500' => 'Orange Red',
            'orange' => 'Orange',
            'yellow' => 'Yellow',
            'green' => 'Green',
            'lime' => 'Lime',
            'cyan' => 'Cyan',
            'blue' => 'Blue',
            'navy' => 'Navy',
            'purple' => 'Purple',
            'magenta' => 'Magenta',
            'pink' => 'Pink',
            '#ff00ff' => 'Fuchsia',
            'lightgray' => 'Light gray',
        ] as $color => $name)
                                    <div onclick="chooseColor(event, '{{ $color }}')"
                                        class="d-inline-block rounded color-pallete"
                                        style="{{ 'background-color: ' . $color . ';' . 'width: 30px; height: 30px;' }}">
                                    </div>
                                @endforeach
                            </div>
                            <br>
                            <p class="my-0"> Choose a custom color </p>
                            <button data-bs-auto-close="outside"
                                style="{{ 'background-color: #f6b73c;' . 'width: 60px; height: 30px;' }}"
                                class="btn btn-link color-pallete" type="button" id="dropdownColorButton"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownColorButton">
                                <div id="div-color-picker"> </div>
                            </div>
                            <br><br>
                            <h5> Choose a gradient color for background</h5>
                            <div class="mx-auto" >
                                @php
                                    $colors = [
                                        ['#000000', '#545454', '#737373'], // Black to Gray
                                        ['#800080', '#4682b4', '#add8e6'], // Purple to Light Blue
                                        ['#800080', '#ff00ff', '#dda0dd'], // Purple to Plum
                                        ['#4682b4', '#b0c4de', '#e0ffff'], // Steel Blue to Light Cyan
                                        ['#cd5c5c', '#e9967a', '#ffa07a'], // Indian Red to Light Salmon
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                        ['#ff6347', '#ffa07a', '#f08080'], // Tomato to Light Coral
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                        ['#ff00ff', '#dda0dd', '#e0ffff'], // Fuchsia to Light Cyan
                                        ['#dda0dd', '#f08080', '#f0e68c'], // Plum to Khaki
                                        ['#ff6347', '#ffa07a', '#ffe4b5'], // Tomato to Moccasin
                                        ['#8fbc8f', '#00ff7f', '#ffffe0'], // Dark Sea Green to Light Yellow
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                        ['#4682b4', '#b0c4de', '#e0ffff'], // Steel Blue to Light Cyan
                                        ['#f08080', '#ffa07a', '#ffe4b5'], // Light Coral to Moccasin
                                        ['#ff00ff', '#dda0dd', '#f08080'], // Fuchsia to Light Coral
                                        ['#e9967a', '#ffa07a', '#ffe4b5'], // Dark Salmon to Moccasin
                                        ['#4682b4', '#b0c4de', '#e0ffff'], // Steel Blue to Light Cyan
                                        ['#ff00ff', '#dda0dd', '#f0e68c'], // Fuchsia to Khaki
                                        ['#ff4500', '#ff7f50', '#ffe4b5'], // Orange Red to Moccasin
                                    ];
                                @endphp
                                @foreach ($colors as $colorGroup)
                                    @php
                                        $gradient = 'linear-gradient(' . implode(', ', $colorGroup) . ')';
                                    @endphp
                                    <div onclick="chooseGradient(event, '{{ $gradient }}')"
                                        class="d-inline-block rounded color-pallete"
                                        style="{{ 'background: ' . $gradient . '; width: 30px; height: 30px;' }}"></div>
                                @endforeach
                            </div>
                            <br>
                            <p class="my-0"> Choose a custom gradient </p>
                            <button data-bs-auto-close="outside"
                                style="{{ 'background: linear-gradient(#4682b4, #b0c4de, #e0ffff);' . 'width: 60px; height: 30px;' }}"
                                class="btn btn-link color-pallete" type="button" id="dropdownGradientButton"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownGradientButton">
                                <div id="div-gradient-picker"> </div>
                            </div>
                            <br>
                            <br>
                            <br>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button onclick="formRequestSubmitById(null, 'updateBgColorRequest')" type="button" class="oceans-gaming-default-button me-3">Save
                            </button>
                            <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                data-bs-dismiss="modal">Close
                            </button>
                        </div>
                    </div>
                    <div class="tab-content pb-4 modal-tab d-none" id="ForeColor">
                        <div class="mx-auto"  style="max-width: max(400px, 75%);">
                            <br>
                            <h5> Choose a solid font color</h5>                    
                            <form id="updateForegroundColorRequest" class="d-inline" method="POST" action="{{ route('user.userBackground.action', ['id' => $selectTeam->id] ) }}"> 
                                @csrf
                                <input type="hidden" name="frameColor" value="{{ $selectTeam->profile?->frameColor }}">
                                <input type="hidden" name="fontColor" value="{{ $selectTeam->profile?->fontColor }}">
                                <input type="hidden" name="teamId" value="{{ $selectTeam->id }}">
                            </form>
                            <button data-bs-auto-close="outside"
                                style="{{ 'background-color: gray;' . 'width: 60px; height: 30px;' }}"
                                class="btn btn-link color-pallete" type="button" id="dropdownFontColorBgButton"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownFontColorBgButton">
                                <div id="div-font-color-picker-with-bg"> </div>
                            </div>
                            <br> <br>
                            <h5> Choose a solid color for your profile frame</h5>
                            <button data-bs-auto-close="outside"
                                style="{{ 'background-color: green;' . 'width: 60px; height: 30px;' }}"
                                class="btn btn-link color-pallete" type="button" id="dropdownFrameColorBgButton"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownFrameColorBgButton">
                                <div id="div-font-color-picker-with-frame"> </div>
                            </div>
                            <div class="upload-container">
                                <label for="image-upload" class="upload-label">
                                    <div class="circle-container">
                                        <div class="uploaded-image"
                                            style="background-image: url({{ '/storage' . '/'. $selectTeam->userBanner }} ); background-size: cover; 
                                                background-repeat: no-repeat; background-position: center; {{$frameStyles}}"
                                        >
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <br> <br>
                            <div class="d-flex justify-content-center">
                                <button onclick="formRequestSubmitById(null, 'updateForegroundColorRequest')" class="oceans-gaming-default-button me-3">Save</button>
                                <button type="button" class="oceans-gaming-default-button oceans-gaming-gray-button"
                                    data-bs-dismiss="modal">Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    data-bs-toggle="modal"
                    data-bs-target="#profileModal"
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
    {{-- @else --}}
        {{-- <div class="team-section"> --}}
    {{-- @endif --}}
        <div :class="{'upload-container': true, }">
            <label class="upload-label">
                <div class="circle-container">
                    <div class="uploaded-image"
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
            <div :class="{'team-info': !isEditMode}">
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
                            <select x-on:change="changeFlagEmoji" id="select2-country" style="width: 150px;" class="d-inline form-control"  data-placeholder="Select a country" x-model="country"> 
                            </select>
                        </span>
                    </div>
                </div>
                <span
                    x-cloak 
                    x-show.important="!isEditMode"
                >
                    <h3 style="{{$fontStyles}}" class="team-name" id="team-name">{{$selectTeam->teamName}}</h3>
                </span>
                @else
                    <h3 style="{{$fontStyles}}" class="team-name" id="team-name">{{$selectTeam->teamName}}</h3>
                @endif
                @auth
                    @if ($user->role == "PARTICIPANT")
                    <div class="dropdown" data-bs-auto-close="outside">
                        <button
                            x-cloak
                            x-show.important="!isEditMode"
                            class="gear-icon-btn me-2 position-relative" style="top: 10px;" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="{{$selectTeam->profile?->fontColor}}" class="bi bi-gear-fill" viewBox="0 0 16 16">
                                <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu py-0" style="font-size: 0.875rem;" aria-labelledby="dropdownMenuButton">
                            <div>
                                @if (is_null($status))
                                    <form class="dropdown-item" method="POST" action="{{route('participant.member.pending', ['id' => $selectTeam->id]) }}">
                                        @csrf()
                                        <button style="font-size: 0.875rem;" class="btn btn-link" type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-lines-fill" viewBox="0 0 16 16">
                                            <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5m.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1z"/>
                                            </svg>
                                            <span class="ms-2"> Join Team </span>
                                        </button>
                                    </form>
                                @else 
                                    <button class="px-2 w-100 h-100 gear-icon-btn-2 pt-1 pb-2 border-1 border-dark border-bottom" type="button">
                                        <small style="font-size: 0.875rem; font-weight: 500;" class="ms-2">  
                                            @if ($isCreator)
                                                You've created this team
                                            @else
                                                {{$statusMessage[$status]}}
                                            @endif
                                        </small>
                                    </button>
                                @endif
                            </div>
                            <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/manage">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                                </svg>
                                <span class="ms-2"> Team Profile </span>
                            </a>
                            @if ($status == "accepted")
                                <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/register">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-currency-dollar" viewBox="0 0 16 16">
                                    <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z"/>
                                    </svg>
                                    <span class="ms-2"> Register Team </span>
                                </a>
                            @endif
                            @if ($isCreator)                           
                                <a class="dropdown-item" href="/participant/team/{{ $selectTeam->id }}/manage/member">
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
                    </div>
                    @endif
                @endauth
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
                    <button 
                        x-on:click="submitEditProfile(event);"
                        data-url="{{route('participant.team.update')}}"
                        class="mt-4 oceans-gaming-default-button oceans-gaming-transparent-button px-3 py-1 rounded mx-auto me-3 mb-4"> 
                        Save
                    </button>
                    {{-- Close icon --}}
                    <svg 
                        style="top: 10px;"
                        x-on:click="isEditMode = false;"
                        xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" class="bi bi-x-circle cursor-pointer text-red position-relative mb-4" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                </div>
                <div>
                    <span class="ms-2" x-cloak x-show="!isEditMode">{{$selectTeam->teamDescription}}</span>
                    <span class="ms-2 fs-3"x-show="!isEditMode">{{$selectTeam->country_flag}}</span>
                </div>
            @else
                <p>
                    <span class="d-inline">{{$selectTeam->teamDescription}}</span>
                    <span class="d-inline ms-2 fs-3">{{$selectTeam->country_flag}}</span>
                </p>
            @endif
        <div class="mx-auto text-center mt-1">
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
<script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>

<script>
        let teamData = JSON.parse(document.getElementById('teamData').value);

        document.addEventListener('alpine:init', () => {

            Alpine.data('alpineDataComponent', () => ({
                select2: null,
                isEditMode: false, 
                ...teamData,
                isEditMode: false, 
                errorMessage: '', 
                isCountriesFetched: false,
                countries: 
                [
                    {
                        name: { en: 'No country' },
                        emoji_flag: ''
                    }
                ], 
                errorMessage: errorInput?.value, 
                changeFlagEmoji() {
                    let countryX = Alpine.raw(this.countries || []).find(elem => elem.id == this.country);
                    this.country_name = countryX?.name.en;
                    this.country_flag = countryX?.emoji_flag;
                },
                async fetchCountries () {
                    if (this.isCountriesFetched) return;
                    try {
                        const data = await storeFetchDataInLocalStorage('/countries');

                        if (data?.data) {
                            this.isCountriesFetched = true;
                            this.countries = data.data;

                            const choices2 = document.getElementById('select2-country');
                            let countriesHtml = "<option value=''>Choose a country</option>";
                            data?.data.forEach((value) => {
                                countriesHtml +=`
                                    <option value='${value.id}''>${value.emoji_flag} ${value.name.en}</option>
                                `;
                            });
                            console.log({countriesHtml});
                            choices2.innerHTML = countriesHtml;
                            choices2.selected = this.country;
                        } else {
                            this.errorMessage = "Failed to get data!";
                        }
                    } catch (error) {
                        console.error('Error fetching countries:', error);
                    }
                },
                async submitEditProfile (event) {
                    try {
                        event.preventDefault(); 
                        const url = event.target.dataset.url;
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                id: this.id, 
                                teamName: this.teamName, 
                                teamDescription: this.teamDescription,
                                country: this.country,
                                country_flag: this.country_flag,
                                country_name: this.country_name
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
                reset() {
                    Object.assign(this, teamData);
                },
                init() {
                    this.fetchCountries();
                    var backgroundStyles = "<?php echo $backgroundStyles; ?>";
                    var fontStyles = "<?php echo $fontStyles; ?>";
                    console.log({backgroundStyles, fontStyles})
                    var banner = document.getElementById('backgroundBanner');
                    banner.style.cssText += `${backgroundStyles} ${fontStyles}`;
                }
            })
    )});

    let colorOrGradient = null; 
    function applyBackground(event, colorOrGradient) {
        document.querySelectorAll('.color-active').forEach(element => {
            element.classList.remove('color-active');
        });

        event.target.classList.add('color-active');
    }

    function chooseColor(event, color) {
        if (event) applyBackground(event, color);
        document.querySelector("input[name='backgroundColor']").value = color;
        document.querySelector("input[name='backgroundGradient']").value = null;
        localStorage.setItem('colorOrGradient', color);
        document.getElementById('backgroundBanner').style.backgroundImage = 'none';
        document.getElementById('backgroundBanner').style.background = color;
    }

    function chooseGradient(event, gradient) {
        console.log({gradient});
        if (event) applyBackground(event, gradient);
        document.querySelector("input[name='backgroundColor']").value = null;
        document.querySelector("input[name='backgroundGradient']").value = gradient;
        localStorage.setItem('colorOrGradient', gradient);
        document.getElementById('backgroundBanner').style.backgroundImage = gradient;
        document.getElementById('backgroundBanner').style.background = 'auto';
    }

    let successInput = document.getElementById('successMessage');
    let errorInput = document.getElementById('errorMessage');

    function formRequestSubmitById(message, id) {
        const form = document.getElementById(id);

        if (message) {
            window.dialogOpen(message, ()=> {
                console.log({message, id})
                form?.submit();
            });
        } else {
            form?.submit();
        }
    }

    function visibleElements() {
        let elements = document.querySelectorAll('.show-first-few');

        elements.forEach((element) => element.classList.remove('d-none'));
        let element2 = document.querySelector('.show-more');
        element2.classList.add('d-none');
    }

    let newFunction = function() {
        if (oldOnLoad) {
            oldOnLoad();
        }
        localStorage.setItem('isInited', "false");
        
        if (successInput) {
            localStorage.setItem('success', 'true');
            localStorage.setItem('message', successInput.value);
        } else if (errorInput) {
            localStorage.setItem('error', 'true');
            localStorage.setItem('message', errorInput.value);
        }

        const bgUploadPreview = window.fileUploadPreviewById('file-upload-preview-1');

        window.createGradientPicker(document.getElementById('div-gradient-picker'),
            (gradient) => {
                chooseGradient(null, gradient);
            }
        );
        

        window.createColorPicker(document.getElementById('div-color-picker'), 
            (color) => {
                chooseColor(null, color);
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-bg'), 
            (color) => {
                document.querySelector("input[name='fontColor']").value = color;
                document.getElementById('backgroundBanner').style.color = color;
            }
        );

        window.createColorPicker(document.getElementById('div-font-color-picker-with-frame'), 
            (color) => {
                document.querySelectorAll('.uploaded-image').forEach((element)=> {
                    document.querySelector("input[name='frameColor']").value = color;
                    element.style.borderColor = color;
                }) 
            }
        );

        window.addEventListener(Events.IMAGE_ADDED, async (e) => {
            const { detail } = e ;
            const file = detail.files[0];
            const fileContent = await readFileAsBase64(file);
            await changeBackgroundDesignRequest({
                backgroundBanner: {
                    filename: file.name,
                    type: file.type,
                    size: file.size,
                    content: fileContent
                }
            }, (data)=> {
                backgroundBanner.style.backgroundImage = `url(/storage/${data.data.backgroundBanner})`;
                backgroundBanner.style.background = 'auto';
            }, (error)=> {
                console.error(error);
            })
        });

        window.loadMessage(); 
    }

    const oldOnLoad = window.onload;
    if (typeof window.onload !== 'function') {
        window.onload = newFunction;
    } else {
        window.onload = function() {
            if (oldOnLoad) {
                oldOnLoad();
            }
            newFunction();
        };
    }

    async function changeBackgroundDesignRequest(body, successCallback, errorCallback) {
        try {
            const url = "{{ route('user.userBackgroundApi.action', ['id' => $selectTeam->id] ) }}";
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-type': 'application/json',
                    'Accept': 'application/json',
                    ...window.loadBearerHeader()
                },
                body: JSON.stringify(body),
            });

            const data = await response.json();
            
            if (data.success) {
                successCallback(data);
            } else {
                errorCallback(data.message);
            }
        } catch (error) {
            errorCallback('There was a problem with the request: ' + error);
        }
    }
    
    const uploadButton = document.getElementById("upload-button");
    const uploadButton2 = document.getElementById("upload-button2");
    const imageUpload = document.getElementById("image-upload");
    const uploadedImageList = document.getElementsByClassName("uploaded-image");
    const uploadedImage = uploadedImageList[0];
    const backgroundBanner = document.getElementById("backgroundBanner")
   
    uploadButton2.addEventListener("click", function() {
        imageUpload.click();
    });

    imageUpload.addEventListener("change", async function(e) {
            const file = e.target.files[0];

            if (file) {
                const url = "{{ route('participant.teamBanner.action', ['id' => $selectTeam->id] ) }}";
                const formData = new FormData();
                formData.append('file', file);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: formData,
                    });
                    
                    const data = await response.json();
                    console.log({data})
                    if (data.success) {
                        uploadedImageList[0].style.backgroundImage = `url(/storage/${data.data.fileName})`;
                        uploadedImageList[1].style.backgroundImage = `url(/storage/${data.data.fileName})`;
                    } else {
                        console.error('Error updating member status:', data.message);
                    }
                } catch (error) {
                    console.error('Error approving member:', error);
                }
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

        const selectedTab = document.getElementById(tabName);
        selectedTab.classList.remove('d-none');
        selectedTab.classList.add('tab-button-active');

        const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
        tabButtons.forEach(button => {
            button.classList.remove("tab-button-active");
        });
       
        let target = event.currentTarget;
        target.classList.add('tab-button-active');
    }

    function redirectToProfilePage(userId) {
        window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
            .replace(':id', userId);
    }
</script>
