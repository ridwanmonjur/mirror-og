<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Results</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/organizer/event-creation.css') }}">
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>
@php
    use Carbon\Carbon;
    if ($event?->tier) {
        $tier = $event->tier->eventTier;
        $icon = $event->tier->tierIcon;
    } else {
        $tier = $icon = null;
    }
    $tier = $event->tier ? $event->tier?->eventTier : null;
    
    $eventTierLower = bladeEventTowerLowerClass($tier);
    $eventTierLowerImg = bladeImageNull($icon);
    $eventBannerImg = bladeImageNull($event->eventBanner);

    if (!function_exists('getMedalSvg')) {

        function getMedalSvg($position)
        {
            // Default SVG for positions beyond 5
            $defaultSvg =
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 32" width="36" height="32">
            <circle cx="18" cy="16" r="14" fill="#1E90FF"/>
            <circle cx="18" cy="16" r="13" fill="#1E90FF" stroke="#0066CC" stroke-width="0.8"/>
            <path d="M8,24 L5,28 L8,32 L18,29 L28,32 L31,28 L28,24" fill="#0066CC"/>
            <text x="18" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="white">' .
                'P' .
                '</text>
            <path d="M15,16 Q18,12 21,16" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.4"/>
        </svg>';

            // Array of medal colors and properties
            $medals = [
                1 => ['fill' => '#FFD700', 'stroke' => '#DAA520', 'color' => 'black'],
                2 => ['fill' => '#C0C0C0', 'stroke' => '#808080', 'color' => 'white'],
                3 => ['fill' => '#CD7F32', 'stroke' => '#8B4513', 'color' => 'white'],
                4 => ['fill' => '#9933FF', 'stroke' => '#6600CC', 'color' => 'white'],
                5 => ['fill' => '#009933', 'stroke' => '#006622', 'color' => 'white'],
            ];

            // Return default for positions beyond 5
            if (!isset($medals[$position])) {
                return $defaultSvg;
            }

            // Generate medal SVG with position number
            return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 32" width="36" height="32">
            <circle cx="18" cy="16" r="14" fill="' .
                $medals[$position]['fill'] .
                '"/>
            <circle cx="18" cy="16" r="13" fill="' .
                $medals[$position]['fill'] .
                '" stroke="' .
                $medals[$position]['stroke'] .
                '" stroke-width="0.8"/>
            <path d="M8,24 L5,28 L8,32 L18,29 L28,32 L31,28 L28,24" fill="' .
                $medals[$position]['stroke'] .
                '"/>
            <text x="18" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="' .
                $medals[$position]['color'] .
                '">' .
                $position .
                '</text>
            <path d="M15,16 Q18,12 21,16" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.4"/>
        </svg>';
        }
    }
@endphp

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="px-2 d-none">
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
        <div class="heading">
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
                        <div  class="d-flex justify-content-center">
                            <div style="width:min(600px, 80vw);" class="border bg-white d-inline-block border-primary shadow-xl px-5 mx-auto text-start  py-3">
                                <div class="d-flex justify-content-start align-items-center">
                                    <img {!! trustedBladeHandleImageFailureBanner() !!}
                                        src="{{ '/storage' . '/'.  $event->eventBanner }}"
                                        class="object-fit-cover float-left border border-primary rounded-circle me-1" width="30" height="30"
                                    >
                                    <div class="position-relative w-100">
                                        <p class="py-0 my-0 mx-2 mb-2"> {{ $event->eventName }} </p>
                                        <div class="py-0 my-0 mx-2 d-inline-block w-100 text-truncate">
                                            Description: {{ $event->eventDescription }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex mt-3 mb-2 align-items-center justify-content-start">
                                    <img {!! trustedBladeHandleImageFailureBanner() !!} 
                                        src="{{ '/storage' . '/'. $event?->user?->userBanner }}" width="30"
                                        height="30" class="me-1 border border-warning rounded-circle object-fit-cover "
                                    >
                                    <div class="ms-2">
                                        <small class="d-block py-0 my-0">
                                            {{ $event?->user?->name ?? 'Name Pending' }}
                                        </small>
                                         <small class="d-block py-0 my-0">
                                            Joined: {{ $event?->user?->createdAtDiffForHumans() ?? 'Recently' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="tabs">
                            <button id="PositionBtn" class="tab-button py-2  outer-tab"
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
                        <div class="tab-content pb-4 tab-size d-none outer-tab mx-auto" id="Position">
                            <!-- Main Container -->
                            <div class="card border-0 py-0 my-0 mx-auto" style="background: none; width: 90%;">
                                @if (isset($joinEventAndTeamList[0]))
                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                                            <div class="card border-2 bg-white hover-shadow-sm position-relative">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <!-- Left side with image and team details -->
                                                        <div class="col-12 col-lg-10 d-flex align-items-center gap-3">
                                                            <!-- View Icon -->
                                                            <div class="cursor-pointer" onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                                                    class="bi bi-eye-fill text-body-secondary" viewBox="0 0 16 16">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                                </svg>
                                                            </div>

                                                            <!-- Team Image and Name -->
                                                            <div class="position-relative">
                                                                <img src="{{ '/storage' . '/'. $joinEventAndTeam->teamBanner }}"
                                                                    {!! trustedBladeHandleImageFailure() !!}
                                                                    class="rounded-circle object-fit-cover border border-primary"
                                                                    style="width: 48px; height: 48px;"
                                                                    alt="Team banner">
                                                            </div>
                                                            <div class="d-flex flex-column justify-content-center my-2">
                                                                <h6 class="mb-1 text-wrap py-0">{{ $joinEventAndTeam->teamName }}
                                                                    <span class="ms-3 mb-1" style="font-size: 1.5rem;"> {{ $joinEventAndTeam->country_flag }} </span>
                                                                </h6>
                                                                <div class="text-body-secondary py-1 text-wrap">
                                                                    <span>Created: {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Right side with position and edit -->
                                                        <div class="col-12 col-lg-2 d-flex justify-content-start my-2 align-items-center gap-3">
                                                            <!-- Position -->
                                                            @if ($joinEventAndTeam->position)
                                                                <div class="d-flex align-items-center text-body-secondary small">
                                                                    <span class="me-2">{!! getMedalSvg($joinEventAndTeam->position) !!} </span>
                                                                    <span class="me-2">{{ bladeOrdinalPrefix($joinEventAndTeam->position) }}</span>
                                                                </div>
                                                            @else
                                                                <span class="text-body-secondary small">-</span>
                                                            @endif

                                                            <!-- Edit Icon -->
                                                            <svg data-bs-toggle="modal" data-bs-target="{{ '#rank' . $joinEventAndTeam->id1 . '-modal' }}"
                                                                xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                                fill="currentColor" class="bi bi-pencil cursor-pointer" viewBox="0 0 16 16">
                                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal (Kept intact) -->
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
                                                                    <div class="input-group mb-3 mx-auto d-flex justify-content-center">
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
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div >
                                        <div class="text-start py-4">
                                            <svg class="ms-4" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="d-inline text-body-secondary text-center mb-0">No teams confirmed registration yet.</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
                                                    <td class="colored-cell px-2   cursor-pointer  text-start"
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
                                                    <td  class="colored-cell text-start px-2">
                                                        {{ $joinEventAndTeam->join_status }}
                                                    </td>
                                                    <td class="colored-cell text-start px-2">
                                                        @if (is_null($joinEventAndTeam->awards_image))
                                                            No awards
                                                        @else
                                                            <img src="{{ '/storage' . '/' . $joinEventAndTeam->awards_image }}"
                                                                class="object-fit-cover rounded-circle border border-primary" width="40" height="40">
                                                        @endif
                                                        {{ $joinEventAndTeam->awards_title ? $joinEventAndTeam->awards_title : '' }}
                                                    </td>
                                                 
                                                    
                                                    <td class="colored-cell px-2 text-start">
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
                                                            <p class="text-center"> No teams confirmed registration yet! </p>
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
                                                                
                                                                <span class="mt-2 text-wrap  d-inline-block text-start" style="height: 40px;"> {{ $award->title }} </span>
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
                                                    <td class="colored-cell px-2 text-start   cursor-pointer " onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});">
                                                        <img
                                                        class="rounded-circle d-inline-block object-fit-cover me-3"
                                                        src="{{ '/storage' . '/'. $joinEventAndTeam->teamBanner }}"
                                                        {!! trustedBladeHandleImageFailure() !!} 
                                                        height="40"
                                                        width="40"
                                                        > 
                                                        {{ $joinEventAndTeam->teamName }}
                                                    </td>
                                                    <td class="colored-cell text-center px-2"> 
                                                        {{ $joinEventAndTeam->join_status }} 
                                                    </td>
                                                    <td class="colored-cell text-center px-2 "> 
                                                        {{ $joinEventAndTeam->achievements_title ? $joinEventAndTeam->achievements_title : '' }} 
                                                        ({ \Carbon\Carbon::parse($joinEventAndTeam->achievements_created_at)->format('Y') })
                                                    </td>
                                                    <td class="colored-cell px-2 text-start" style="width: 40%;">
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
                                                                    <p class="text-center" > No teams confirmed registration yet!</p>
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
    
    
    <script src="{{ asset('/assets/js/organizer/EventResults.js') }}"></script>

