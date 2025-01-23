<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @include('__CommonPartials.HeadIcon')
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/eventResults.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
@php
    use Carbon\Carbon;
    $tier = $event->tier ? $event->tier?->eventTier : null;
    $eventTierLower = bladeEventTowerLowerClass($tier);
    $eventTierLowerImg = bladeEventTierImage($tier);
    $eventBannerImg = bladeImageNull($event->eventBanner);
@endphp

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')

    <main class="px-2">
        <input type="hidden" id="currentUrlInput" value="{{ route('event.awards.index', ['id' => $event->id]) }}">
        <input type="hidden" id="profile-route" value="{{ route('public.participant.view', ['id' => ':id']) }}">
        <input type="hidden" id="create-event-route" value="{{ route('event.create') }}">
        <input type="hidden" id="edit-event-route" value="{{ route('event.edit', $event->id) }}">
        <input type="hidden" id="team-route" value="{{ route('public.team.view', ['id' => ':id']) }}">
        <input type="hidden" id="event-results-store-route" value="{{ route('event.results.store', ['id' => $event->id]) }}">
        <input type="hidden" id="event-awards-store-route" value="{{ route('event.awards.store', ['id' => $event->id]) }}">
        <input type="hidden" id="event-achievements-store-route" value="{{ route('event.achievements.store', ['id' => $event->id]) }}">
        <input type="hidden" id="event-awards-destroy-route" value="{{ route('event.awards.destroy', ['id' => $event->id, 'awardId' => ':id']) }}">
        <input type="hidden" id="event-achievements-destroy-route" value="{{ route('event.achievements.destroy', ['achievementId' => ':id']) }}">
        <input type="hidden" id="eventId" value="{{ $event->id }}">
        <br>
        <div class="ms-5">
            <u>
                <h3>
                    Manage your event results
                </h3>
            </u>
        </div> <br>
        <div>
            <div>
                <div>
                    <div>
                        <div style="border: 3px solid black; background: white !important;" class="text-start tab-size px-4 py-3">
                            <div>
                                <img {!! trustedBladeHandleImageFailureBanner() !!}
                                    src="{{ '/storage' . '/'.  $event->eventBanner }}"
                                    class="object-fit-cover border border-primary rounded-circle me-1" width="30" height="30"
                                >
                                <p class=" d-inline my-0 ms-2"> {{ $event->eventName }} </p>
                            </div>
                            <div class="d-flex mt-3 mb-2 justify-content-start">
                                <img {!! trustedBladeHandleImageFailureBanner() !!} 
                                    src="{{ '/storage' . '/'. $event?->user?->userBanner }}" width="30"
                                    height="30" class="me-1 border border-warning rounded-circle object-fit-cover "
                                >
                                <div class="ms-2">
                                    <small class="d-block py-0 my-0">
                                        {{ $event?->user?->name ?? 'N/A' }}
                                    </small>
                                    <small>
                                        Description: {{ $event->eventDescription }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="tabs">
                            <button id="PositionBtn" class="tab-button py-2  outer-tab tab-button-active"
                                onclick="showTab(event, 'Position', 'outer-tab')">Position
                            </button>
                            {{-- <button id="AwardsBtn" class="tab-button py-2  outer-tab"
                                onclick="showTab(event, 'Awards', 'outer-tab')">
                                Awards
                            </button>
                            <button id="AchievementsBtn" class="tab-button py-2  outer-tab"
                                onclick="showTab(event, 'Achievements', 'outer-tab')">
                                Achievements
                            </button> --}}
                        </div>
                        <br>
                        <div class="tab-content pb-4 tab-size outer-tab mx-auto" id="Position">
                            <table class="mx-auto member-table responsive" style="margin-left: 5px;">
                                
                                @if (isset($joinEventAndTeamList[0]))
                                    <thead class="accepted-member-table text-start">
                                        <th></th>
                                    
                                        <th class="text-start">
                                            Team Name
                                        </th>
                                         <th class="text-start" >
                                            Team Position
                                        </th>

                                        <th class="text-start" >
                                            Join Status
                                        </th>

                                        <th class="text-start" >
                                            Team Created
                                        </th>
                                       
                                    </thead>
                                    <tbody class="accepted-member-table text-start">
                                        @foreach ($joinEventAndTeamList as $key => $joinEventAndTeam)
                                            <tr class="st">
                                                <td class="colorless-col text-start px-0">
                                                    <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                        class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20" fill="currentColor"
                                                        class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                        <path
                                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                    </svg>
                                                </td>
                                                
                                                <td class="coloured-cell px-2 text-start   cursor-pointer  " style="width: 30%;" onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});">
                                                    <img
                                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                                        src="{{ '/storage' . '/'. $joinEventAndTeam->teamBanner }}"
                                                        {!! trustedBladeHandleImageFailure() !!} 
                                                        height="40"
                                                        width="40"
                                                    > 
                                                    {{ $joinEventAndTeam->teamName }}
                                                </td>
                                                   <td class="coloured-cell text-start px-2 ">
                                                    {{ $joinEventAndTeam->position ? $joinEventAndTeam->position : '-' }}
                                                </td>
                                                </td>
                                                   <td class="coloured-cell text-start px-2 ">
                                                    {{ $joinEventAndTeam->join_status }}
                                                </td>
                                                <td class="coloured-cell px-2 text-start ">
                                                    {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                                </td>
                                            
                                                <td class="colorless-col px-0 ps-2 cursor-pointer text-start"
                                                >
                                                    <svg data-bs-toggle="modal"
                                                        data-bs-target="{{ '#rank' . $joinEventAndTeam->id1 . '-modal' }}"
                                                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                        <path
                                                            d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                                    </svg>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="{{ 'rank' . $joinEventAndTeam->id1 . '-modal' }}"
                                                tabindex="-1"
                                                aria-labelledby="{{ 'rank' . $joinEventAndTeam->id1 . '-modal' . 'label' }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-body">
                                                            <form onsubmit="editCreatePosition(event);">
                                                                <div class="mx-auto text-center mt-3">
                                                                    <h5 class="text-primary"> Choose a position for team:
                                                                        {{ $joinEventAndTeam->teamName }}. </h5>
                                                                    <br>
                                                                    <p> Choose between 1 and
                                                                        {{ $event->tier?->tierTeamSlot }}</p>
                                                                    <br>
                                                                    <div
                                                                        class="input-group mb-3 mx-auto d-flex justify-content-center">
                                                                        <span class="input-group-text bg-primary text-light"
                                                                            id="basic-addon2">#</span>
                                                                        <input class="form-control"
                                                                            placeholder="{{'1-'.  $event->tier?->tierTeamSlot }}"
                                                                            style="max-width: 100px !important;"
                                                                            type="number" name="position" min="1"
                                                                            max="{{ $event->tier?->tierTeamSlot }}">
                                                                        <input type="hidden" name="eventName"
                                                                            value="{{ $event->eventName ?? 'No name yet' }}">
                                                                        <input type="hidden" name="teamName"
                                                                            value="{{ $joinEventAndTeam->teamName }}">
                                                                        <input type="hidden" name="creator_id"
                                                                            value="{{ $joinEventAndTeam->creator_id }}">
                                                                        <input type="hidden" name="teamBanner"
                                                                            value="{{ $joinEventAndTeam->teamBanner }}">
                                                                        <input type="hidden" name="id"
                                                                            value="{{ $joinEventAndTeam->id1 }}">
                                                                        <input type="hidden" name="team_id"
                                                                            value="{{ $joinEventAndTeam->team_id }}">
                                                                    </div>
                                                                    <br>
                                                                    <button type="submit"
                                                                        class="oceans-gaming-default-button me-3">Submit
                                                                    </button>
                                                                    <button type="button"
                                                                        class="oceans-gaming-default-button oceans-gaming-gray-button"
                                                                        data-bs-dismiss="modal">Close
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                        @endforeach
                                    </tbody>
                                @else
                                    <p class="text-center mt-5">  No teams joined yet.</p>
                                @endif
                            </table>
                        </div>
                        {{-- <div class="tab-content tab-size outer-tab d-none mx-auto" id="Awards">
                            <div class="mx-auto member-table d-flex justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="{{ '#award' . '-modal' }}"
                                    class="oceans-gaming-default-button">
                                    Add award
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                            fill="currentColor" class="text-light pt-1 bi bi-plus-circle ms-3"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <br>
                            @if (isset($awardAndTeamList[0]))
                                <table class="member-table responsive   mx-auto">
                                    <thead class="accepted-member-table text-start">
                                        <th></th>
                                         <th class="text-start">
                                            Team Name
                                        </th>
                                          <th class="text-start">
                                            Team Status
                                        </th>
                                        <th class="text-start">
                                            Award
                                        </th>
                                       
                                         
                                     
                                        <th class="text-start" style="width: 180px;">
                                            Team Created
                                        </th>
                                    </thead>
                                    <tbody class="accepted-member-table text-start">
                                        @foreach ($awardAndTeamList as $key => $joinEventAndTeam)
                                                <tr class="st">
                                                    <td class="colorless-col  text-center  px-0">
                                                        <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                            class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                            width="20" height="20" fill="currentColor"
                                                            class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                            <path
                                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                        </svg>
                                                    </td>
                                                    <td class="coloured-cell px-2   cursor-pointer  text-start"
                                                        onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                    >
                                                        <img
                                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                                        src="{{ '/storage' . '/'. $joinEventAndTeam->teamBanner }}"
                                                        {!! trustedBladeHandleImageFailure() !!} 
                                                        height="40"
                                                        width="40"
                                                        > 
                                                        {{ $joinEventAndTeam->teamName }}
                                                    </td>
                                                    <td  class="coloured-cell text-start px-2">
                                                        {{ $joinEventAndTeam->join_status }}
                                                    </td>
                                                    <td class="coloured-cell text-start px-2">
                                                        @if (is_null($joinEventAndTeam->awards_image))
                                                            No awards
                                                        @else
                                                            <img src="{{ '/storage' . '/' . $joinEventAndTeam->awards_image }}"
                                                                class="object-fit-cover rounded-circle border border-primary" width="40" height="40">
                                                        @endif
                                                        {{ $joinEventAndTeam->awards_title ? $joinEventAndTeam->awards_title : '' }}
                                                    </td>
                                                 
                                                    
                                                    <td class="coloured-cell px-2 text-start">
                                                        {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                                    </td>
                                                    <td class="colorless-col text-center  px-1  text-center">
                                                        <svg onclick="deleteAward({{ $joinEventAndTeam->results_id }})"
                                                            xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-trash"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                            <path
                                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                        </svg>
                                                    </td>
                                                </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-center mt-3">  No positions joined yet.</p>
                            @endif

                                     
                            <div class="modal fade" id='award-modal' tabindex="-1"
                                aria-labelledby={{ 'award-modal-label' }} aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form onsubmit="addAward(event);">
                                            <div class="modal-body modal-body-overflow scrollbarline pe-4">
                                                <div class="mx-auto text-center mt-3 px-3">

                                                    <h5 class="text-primary"> Choose an award for your team </h5>
                                                    <br>
                                                    <div>
                                                        <input type="hidden" name="eventName"
                                                            value="{{ $event->eventName ?? 'No name yet' }}"
                                                        >
                                                        
                                                        
                                                        <label class="form-check-label fw-bold">
                                                            Choose team
                                                        </label>
                                                        @if (isset($joinEventAndTeamList[0]))
                                                            <select class="form-select mx-auto" name="teamId"
                                                                aria-label="Select Team"
                                                                style="max-width: 200px !important;">
                                                                @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                                                                    <option
                                                                        value="{{ $joinEventAndTeam->team_id }}">
                                                                        {{ $joinEventAndTeam->teamName }}</option>
                                                                @endforeach
                                                            </select>
                                                        @else 
                                                            <p class="text-center"> No teams joined yet! </p>
                                                        @endif
                                                    </div>
                                                    <br>
                                                    <label class="form-check-label mb-2 fw-bold">
                                                        Choose award
                                                    </label>
                                                    <div
                                                        class="d-flex flex-row flex-nowrap justify-content-start ps-2 pe-5 mt-0">
                                                        @foreach ($awardList as $award)
                                                            <input type="hidden" name="awardName"
                                                                    value="{{ $award->title }}">
                                                            <div
                                                                class="form-check mx-auto px-2 me-5"
                                                            >
                                                                <input
                                                                    class="form-check-input d-block text-center ms-2"
                                                                    type="radio" name="awardId"
                                                                    value="{{ $award->id }}"
                                                                ><br>
                                                                
                                                                <span class="mt-2 text-truncate  d-inline-block text-start" style="height: 40px;"> {{ $award->title }} </span>
                                                                <label class="form-check-label"
                                                                    for="awardId">
                                                                    <img src="{{ '/storage' . '/' . $award->image }}"
                                                                        width="60"
                                                                        height="60"
                                                                        class="object-fit-cover rounded-circle border border-2 border-primary"
                                                                    > </span>
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <br>
                                                    <button type="submit"
                                                        class="oceans-gaming-default-button me-2">Submit
                                                    </button>
                                                    <button type="button"
                                                        class="oceans-gaming-default-button oceans-gaming-gray-button"
                                                        data-bs-dismiss="modal">Close
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content tab-size pb-4  outer-tab d-none mx-auto" id="Achievements">
                            <div class="mx-auto member-table d-flex justify-content-center">
                                <button data-bs-toggle="modal" data-bs-target="{{ '#achievements-modal' }}"
                                    class="oceans-gaming-default-button">
                                    Add an achievement
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"
                                            fill="currentColor" class="text-light pt-1 bi bi-plus-circle ms-3"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                            <br>
                            <table class="member-table responsive   mx-auto" >
                                @if (isset($achievementsAndTeamList[0]))
                                    <thead class="accepted-member-table text-start">
                                        <th></th>
                                        <th class="text-start">
                                            Team Name
                                        </th>
                                          <th class="text-start">
                                            Join Status
                                        </th>
                                        <th class="text-start">
                                            Achievement
                                        </th>
                                       
                                        <th class="text-start">
                                            Description
                                        </th>
                                      
                                    </thead>
                                    <tbody class="accepted-member-table text-start">
                                        @foreach ($achievementsAndTeamList as $key => $joinEventAndTeam)
                                                <tr class="st py-3">
                                                    <td class="colorless-col text-lg-start text-center   cursor-pointer  ">
                                                        <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                            class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                            width="20" height="20" fill="currentColor"
                                                            class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                            <path
                                                                d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                        </svg>
                                                    </td>
                                                    <td class="coloured-cell px-2 text-start   cursor-pointer " onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});">
                                                        <img
                                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                                        src="{{ '/storage' . '/'. $joinEventAndTeam->teamBanner }}"
                                                        {!! trustedBladeHandleImageFailure() !!} 
                                                        height="40"
                                                        width="40"
                                                        > 
                                                        {{ $joinEventAndTeam->teamName }}
                                                    </td>
                                                    <td class="coloured-cell text-center px-2"> 
                                                        {{ $joinEventAndTeam->join_status }} 
                                                    </td>
                                                    <td class="coloured-cell text-center px-2 "> 
                                                        {{ $joinEventAndTeam->achievements_title ? $joinEventAndTeam->achievements_title : '' }} 
                                                        ({ \Carbon\Carbon::parse($joinEventAndTeam->achievements_created_at)->format('Y') })
                                                    </td>
                                                    <td class="coloured-cell px-2 text-start" style="width: 40%;">
                                                        {{ $joinEventAndTeam->achievements_description }}
                                                    </td>
                                                   
                                                    <td class="colorless-col text-center  px-1 ps-2 pt-2 text-lg-start text-center">
                                                        <svg onclick="deleteAchievement({{ $joinEventAndTeam->achievements_id }})"
                                                            xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-trash"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z" />
                                                            <path
                                                                d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z" />
                                                        </svg>
                                                    </td>
                                                </tr>
                                        @endforeach
                                    </tbody>
                                @else
                                    <p class="text-center mt-3">  No achievement given yet.</p>
                                @endif
                                    <div class="modal fade" id='achievements-modal' tabindex="-1"
                                        aria-labelledby={{ 'achievements-modal-label' }} aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form onsubmit="addAchievement(event);">
                                                    <div class="modal-body modal-body-overflow scrollbarline pe-4">
                                                        <div class="mx-auto text-center mt-3 px-3">
                                                            <h5 class="text-primary"> Choose an achievement </h5>
                                                            <br>
                                                            <div>
                                                                <label class="form-check-label fw-bold">
                                                                    Choose team
                                                                </label>
                                                                @if (isset($joinEventAndTeamList[0]))
                                                                    <select class="form-select" name="teamId"
                                                                        aria-label="Select Team"
                                                                        placeholder="Select a team..."
                                                                    >
                                                                        @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                                                                            <option
                                                                                value="{{ $joinEventAndTeam->team_id }}">
                                                                                {{ $joinEventAndTeam->teamName }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                @else 
                                                                    <p class="text-center" > No teams joined yet!</p>
                                                                @endif
                                                            </div>
                                                            <br>
                                                            <input type="hidden" name="eventName"
                                                                value="{{ $event->eventName ?? 'No name yet' }}">
                                                            <label class="form-check-label fw-bold">
                                                                Achievement Title
                                                            </label>
                                                            <input type="text" class="form-control rounded-lg shadow-sm" name="title" placeholder="Please enter a title...">
                                                            <br>
                                                            <label class="form-check-label fw-bold">
                                                                Achievement Description
                                                            </label>
                                                            <input type="text" class="form-control rounded-lg shadow-sm" name="description" placeholder="Please enter a description...">
                                                            <br><br>
                                                            <button type="submit"
                                                                class="oceans-gaming-default-button me-2">Submit
                                                            </button>
                                                            <button type="button"
                                                                class="oceans-gaming-default-button oceans-gaming-gray-button"
                                                                data-bs-dismiss="modal">Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </tbody>
                            </table>
                        </div> --}}
                    </div>
                </div>
            </div>
            <br>
    </main>
    @stack('script')
    
    <script src="{{ asset('/assets/js/organizer/EventResults.js') }}"></script>

