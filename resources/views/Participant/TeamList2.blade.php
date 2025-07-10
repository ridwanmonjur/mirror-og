

<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Teams</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/teamSelect.js'])
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/manageEvent.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @include('includes.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')
    <br>
     <input type="hidden" id="currentMemberUrl" value="{{ url()->current() }}">
    
    <input type="hidden" id="participantMemberUpdateUrl" value="{{ route('participant.member.update', ['id' => ':id']) }}">
    <input type="hidden" id="memberPendingUrl" value="{{ route('participant.member.pending', ['id' => ':id']) }}">
    <input type="hidden" id="participantMemberDeleteInviteUrl" value="{{ route('participant.member.deleteInvite', ['id' => ':id']) }}">
    <input type="hidden" id="participantMemberInviteUrl" value="{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}">

    <div class="team-head-storage d-none  " data-all-categories="{{json_encode($allCategorys)}}">
    </div>
    <main>
        <div class="d-flex justify-content-between mb-2">
            <h5>  
                @if($isModeMyTeams)
                    Your Teams
                @else
                    Find a Team to Join
                @endif
            </h5>
            <div>
                 <button onclick="setIsMyMode(event)"
                    class="d-inline-flex ms-4 py-2 rounded-pill px-4 text-light btn btn-primary me-2"
                >
                    
                    <span id="browse-teams-btn"> 
                        @if($isModeMyTeams)
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search mt-0 me-2" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                            Find a Team to Join
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cup-hot mt-0 me-2" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M.5 6a.5.5 0 0 0-.488.608l1.652 7.434A2.5 2.5 0 0 0 4.104 16h5.792a2.5 2.5 0 0 0 2.44-1.958l.131-.59a3 3 0 0 0 1.3-5.854l.221-.99A.5.5 0 0 0 13.5 6zM13 12.5a2 2 0 0 1-.316-.025l.867-3.898A2.001 2.001 0 0 1 13 12.5M2.64 13.825 1.123 7h11.754l-1.517 6.825A1.5 1.5 0 0 1 9.896 15H4.104a1.5 1.5 0 0 1-1.464-1.175"/>
                            <path d="m4.4.8-.003.004-.014.019a4 4 0 0 0-.204.31 2 2 0 0 0-.141.267c-.026.06-.034.092-.037.103v.004a.6.6 0 0 0 .091.248c.075.133.178.272.308.445l.01.012c.118.158.26.347.37.543.112.2.22.455.22.745 0 .188-.065.368-.119.494a3 3 0 0 1-.202.388 5 5 0 0 1-.253.382l-.018.025-.005.008-.002.002A.5.5 0 0 1 3.6 4.2l.003-.004.014-.019a4 4 0 0 0 .204-.31 2 2 0 0 0 .141-.267c.026-.06.034-.092.037-.103a.6.6 0 0 0-.09-.252A4 4 0 0 0 3.6 2.8l-.01-.012a5 5 0 0 1-.37-.543A1.53 1.53 0 0 1 3 1.5c0-.188.065-.368.119-.494.059-.138.134-.274.202-.388a6 6 0 0 1 .253-.382l.025-.035A.5.5 0 0 1 4.4.8m3 0-.003.004-.014.019a4 4 0 0 0-.204.31 2 2 0 0 0-.141.267c-.026.06-.034.092-.037.103v.004a.6.6 0 0 0 .091.248c.075.133.178.272.308.445l.01.012c.118.158.26.347.37.543.112.2.22.455.22.745 0 .188-.065.368-.119.494a3 3 0 0 1-.202.388 5 5 0 0 1-.253.382l-.018.025-.005.008-.002.002A.5.5 0 0 1 6.6 4.2l.003-.004.014-.019a4 4 0 0 0 .204-.31 2 2 0 0 0 .141-.267c.026-.06.034-.092.037-.103a.6.6 0 0 0-.09-.252A4 4 0 0 0 6.6 2.8l-.01-.012a5 5 0 0 1-.37-.543A1.53 1.53 0 0 1 6 1.5c0-.188.065-.368.119-.494.059-.138.134-.274.202-.388a6 6 0 0 1 .253-.382l.025-.035A.5.5 0 0 1 7.4.8m3 0-.003.004-.014.019a4 4 0 0 0-.204.31 2 2 0 0 0-.141.267c-.026.06-.034.092-.037.103v.004a.6.6 0 0 0 .091.248c.075.133.178.272.308.445l.01.012c.118.158.26.347.37.543.112.2.22.455.22.745 0 .188-.065.368-.119.494a3 3 0 0 1-.202.388 5 5 0 0 1-.252.382l-.019.025-.005.008-.002.002A.5.5 0 0 1 9.6 4.2l.003-.004.014-.019a4 4 0 0 0 .204-.31 2 2 0 0 0 .141-.267c.026-.06.034-.092.037-.103a.6.6 0 0 0-.09-.252A4 4 0 0 0 9.6 2.8l-.01-.012a5 5 0 0 1-.37-.543A1.53 1.53 0 0 1 9 1.5c0-.188.065-.368.119-.494.059-.138.134-.274.202-.388a6 6 0 0 1 .253-.382l.025-.035A.5.5 0 0 1 10.4.8"/>
                            </svg>
                            Your Teams
                        @endif
                    </span>
                </button>
                <a href="{{route('participant.team.create')}}" 
                    class="d-inline-flex ms-4 py-2 rounded-pill px-4 text-light btn btn-primary me-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                        class="mt-1 me-2"
                        viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                    Create Team
                </a>
                
            </div>
        </div>
        <form id="newTeamsForm">
            <div>
                <input type="hidden" id="countServer" value="{{ $count }}">
                <input type="hidden" id="teamListServer" value="{{ json_encode($teamList) }}">
                <input type="hidden" id="membersCountServer" value="{{ json_encode($membersCount) }}">
                <input type="hidden" id="userIdServer" value="{{ $user->id }}">
                <input type="hidden" name="sortKeys" id="sortKeys" value="">
                <input type="hidden" name="sortType" id="sortType" value="">
                <input name="search" style="width: min(90vw, 550px); font-size: 1rem;" class="rounded-pill mb-2 px-4 form-control d-inline-block me-3 cursor-pointer" type="text" placeholder="Search for player name/ email">
                <button type="button" class="btn btn-primary text-light px-2 border-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-search"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>

            </div>
            <div class="d-inline-flex justify-content-between align-items-center flex-wrap mt-2">
                <div>
                    <div class="cursor-pointer me-5 d-inline-block"
                        onclick="openElementById('close-option'); openElementById('filter-option');  closeElementById('sort-option');">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                            class="bi bi-filter mt-2" viewBox="0 0 16 16">
                            <path
                                d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
                        </svg>
                    </div>
                    <div id="filter-option" class="mx-0 px-0 mb-2 ms-3 d-inline-block">
                        <div class="d-flex justify-content-start flex-wrap">
                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Esports Title</span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();" class="dropdown-menu px-3 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                    <p class="my-1">Choose the game to search by</p>
                                         
                                    <select name="esports_title" id="game-select" class="mb-2 dropdown-size category-input text-start" >
                                        <option value="">Add your favourite games</option>
                                    </select>

                                </div>
                            </div>
                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Region</span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();" class="dropdown-menu px-0 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                    <div class="px-3 py-1" style="width: 280px;">
                                        <p class="my-1">Choose a region of origin</p>

                                        <select id="select2-country2" class="mb-2" name="region">
                                        </select>
                                        
                                    </div>
                                    <button type="button"
                                        class="my-2 d-none rounded-pill btn btn-sm btn-primary text-light"
                                        id="esports_titleResetButton"
                                        onclick="
                                            resetInput('esports_title');
                                        ">
                                        Reset 
                                    </button>
                                </div>
                            </div>

                            <div class="dropdown d-none me-3">

                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Region </span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation;"; class="dropdown-menu px-0 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                    <div class="px-3 py-1">
                                        <p class="mb-1">Choose a country/ region of origin</p>
                                       
                                        <button type="button"
                                            class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                            id="regionResetButton"
                                            onclick="
                                                resetInput('region');
                                            ">
                                            Reset 
                                        </button>
                                    </div>
                                </div>
                            </div>
                           
                            
                             <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Status</span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div onclick="event.stopPropagation();" class="dropdown-size dropdown-menu px-0 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                    @foreach ([['title' => 'Open (can join freely)', 'value' => 'open'],  ['title' => 'Public (free to apply)', 'value' => 'public'], ['title' => 'Private (cannot apply)', 'value' => 'private'] ] as $status)
                                        <div class="px-3 py-1" style="width: 200px;">
                                            <input type="checkbox" name="status" class="form-check-input" checked value="{{ $status['value'] }}">
                                            <label for="status">{{ $status['title'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div  @class(['dropdown me-3', ' d-none' => !$isModeMyTeams]) id="members-dropdown">
                                <button class="ps-0 pe-3 py-2 button-design-removed" type="button"
                                    id="dropdownFilterSort" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Members</span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div  onclick="event.stopPropagation();" class="dropdown-size dropdown-menu px-3 py-1"
                                    aria-labelledby="dropdownFilterSort">
                                        <p class="mb-1">Choose the minimumn number of members in team</p>
                                        <input type="range" class="form-range" name="membersCount" min="0" defaultValue="0" value="0" max="5" step="1" id="customRange3">
                                         <button type="button"
                                            class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                            id="membersCountResetButton"
                                            onclick="
                                                resetInput('membersCount');
                                            ">
                                            Reset 
                                        </button>
                                </div>
                            </div> 


                            <div class="dropdown me-3">
                                <button class="ps-0 pe-3 py-1 py-2 button-design-removed" type="button"
                                    id="dropdownFilterType" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">
                                    <span>Date Joined </span>
                                    <span class="dropbtn-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-chevron-down">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </span>
                                </button>
                                <div style="min-width: 250px;" onclick="event.stopPropagation();" class="dropdown-menu px-3"
                                    aria-labelledby="dropdownFilterSort">
                                    <p class="mb-1">Choose a date to filter team by creation time</p>
                                    <input type="date" class="form-control" name="created_at">
                                    <button id="created_atResetButton" type="button" class="my-2 rounded-pill btn btn-sm btn-primary text-light"
                                        onclick="
                                        resetInput('created_at');
                                    ">
                                        Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="sort-option" class="mx-0 px-0 mb-3 d-inline-block">
                    <div class="ddropdown dropdown-click-outside d-inline-block">
                        <span class="sort-icon-list" onclick="changeSortType()">
                            {{-- Ascending --}}
                            <svg data-value="asc-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="d-none cursor-pointer gear-icon-button bi bi-sort-alpha-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                            <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zm-8.46-.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.5.5 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707z"/>
                            </svg>
                            {{-- Descending --}}
                            <svg data-value="desc-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="d-none cursor-pointer gear-icon-button bi bi-sort-alpha-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371zm1.57-.785L11 2.687h-.047l-.652 2.157z"/>
                                <path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293z"/>
                            </svg>
                        </span>
                        <button class="dropbtn py-1 px-2 me-3" type="button" id="dropdownSortButton"
                            style="min-width: 150px; display: inline-block;" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <span id="sortByTitleId">Sort by:</span>
                            <span class="dropbtn-arrow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-chevron-down">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </span>
                        </button>
                        <div onclick="event.stopPropagation();" class="dropdown-menu px-3 ms-3"
                            aria-labelledby="dropdownSortButton">
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('recent');">
                                <label class="me-3 cursor-pointer" for="recent">Recent</label>
                            </div>
                           
                            {{-- <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('region');">
                                <label class="me-3 cursor-pointer" for="region">Region</label>
                            </div> --}}
                            <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('name');">
                                <label class="me-3 cursor-pointer" for="name">Name</label>
                            </div>
                            
                             <div class="sort-box d-block min-w-150px hover-bigger ps-3 py-1"
                                onclick="setSortForFetch('created_at');">
                                <label class="me-3 cursor-pointer" for="created_at">Date Joined</label>
                            </div>
                            <div class="d-block min-w-150px hover-bigger ps-3 py-1" onclick="resetInput('sortKeys');">
                                <button id="sortKeysResetButton" type="button" class="rounded-pill btn btn-sm btn-primary text-light">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="filter-search-results" class="pb-2">
                <span class="me-5 " >
                    <small class="me-4"></small>
                    <span class="">
                        <small data-form-parent="default-filter" class="me-2">
                            <small class="btn btn-secondary text-light d-none rounded-pill px-2 py-0">
                                Default
                            </small>
                        </small>
                        <small data-form-parent="created_at" class="me-2">
                        </small>
                        <small data-form-parent="esports_title" class="me-2">
                        </small>
                         <small data-form-parent="region2" class="me-2">
                        </small>
                        <small data-form-parent="region" class="me-2">
                        </small>
                        <small data-form-parent="status" class="me-2">
                        </small>
                        <small data-form-parent="membersCount" class="me-2">
                        </small>
                        <small data-form-parent="sortKeys">
                        </small>
                    </span>
                    {{-- <small  id="default-sorts" class="btn btn-primary text-light px-2 py-0">Default</small> --}}
                </span>
            </div>
            <div id="no-results-div" class="d-none mx-auto my-5 py-5 text-center">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-exclamation-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                    </svg>
                </div>
                <div>
                    <i>No teams to view.</i>
                </div>
            </div>
            <div class="grid-3-columns justify-content-center" id="filter-sort-results" style="grid-auto-rows : 1fr !important;">
            </div>
            <ul id="page-links" class="d-none pagination mx-auto text-center list-unstyled"> </ul>
        </form>
        <br>
        <br>
    </main>
    <script src="{{ asset('/assets/js/organizer/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/participant/TeamList.js') }}"></script>
</body>

</html>
