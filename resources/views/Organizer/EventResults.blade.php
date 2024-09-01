<!DOCTYPE html>
<html lang="en">

<head>
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
    @include('__CommonPartials.NavbarGoToSearchPage')

    <main class="ps-5">
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
                        <div class="text-start">
                            <h5 class="card-text-2-lines text-center">
                                {{ $event->eventName ?? 'No name yet' }}
                            </h5>
                            <p class="text-center"> {{ $event->eventDescription }} </p>
                        </div>
                        <div class="tabs">
                            <button id="PositionBtn" class="tab-button py-2  outer-tab tab-button-active"
                                onclick="showTab(event, 'Position', 'outer-tab')">Position
                            </button>
                            <button id="AwardsBtn" class="tab-button py-2  outer-tab"
                                onclick="showTab(event, 'Awards', 'outer-tab')">
                                Awards
                            </button>
                            <button id="AchievementsBtn" class="tab-button py-2  outer-tab"
                                onclick="showTab(event, 'Achievements', 'outer-tab')">
                                Achievements
                            </button>
                        </div>
                        <div class="tab-content pb-4  outer-tab mx-auto" id="Position">
                            <table class="mx-auto member-table text-start" style="margin-left: 5px;">
                                <thead class="accepted-member-table text-start">
                                    <th></th>
                                    <th class="text-start">
                                        Team Position
                                    </th>
                                    <th class="text-start">
                                        Team Name
                                    </th>
                                    <th class="text-start">
                                        Team Description
                                    </th>
                                    <th class="text-start">
                                        Team Created
                                    </th>
                                </thead>
                                <tbody class="accepted-member-table text-start">
                                    @foreach ($joinEventAndTeamList as $key => $joinEventAndTeam)
                                        <tr class="st">
                                            <td class="colorless-col px-3">
                                                <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                    class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                    width="20" height="20" fill="currentColor"
                                                    class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                    <path
                                                        d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                </svg>
                                            </td>
                                            <td class="coloured-cell text-center px-2">
                                                {{ $joinEventAndTeam->position ? $joinEventAndTeam->position : '-' }}
                                            </td>
                                            <td class="coloured-cell px-3 text-start">
                                                {{ $joinEventAndTeam->teamName }}
                                            </td>
                                            <td class="coloured-cell px-3 text-start">
                                                {{ $joinEventAndTeam->teamDescription }}
                                            </td>
                                            <td class="coloured-cell px-3 text-start">
                                                {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                            </td>
                                            <td class="colorless-column px-1 ps-4 text-start"
                                                style="padding: 0; margin: 0; width: 150px;">
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
                                                                <h5> Choose a position for team:
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
                            </table>
                        </div>
                        <div class="tab-content pb-4  outer-tab d-none mx-auto" id="Awards">
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
                            <table class="member-table text-start mx-auto" style="margin-left: -5px;">
                                <thead class="accepted-member-table text-start">
                                    <th></th>
                                    <th class="text-start">
                                        Award
                                    </th>
                                    <th class="text-start">
                                        Team Name
                                    </th>
                                    <th class="text-start">
                                        Team Description
                                    </th>
                                    <th class="text-start">
                                        Team Created
                                    </th>
                                </thead>
                                <tbody class="accepted-member-table text-start">
                                    @foreach ($awardAndTeamList as $key => $joinEventAndTeam)
                                        @if (!is_null($joinEventAndTeam->award_id))
                                            <tr class="st">
                                                <td class="colorless-col px-3">
                                                    <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                        class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20" fill="currentColor"
                                                        class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                        <path
                                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                    </svg>
                                                </td>
                                                <td class="coloured-cell text-center px-2">
                                                    @if (is_null($joinEventAndTeam->awards_image))
                                                        No awards
                                                    @else
                                                        <img src="{{ '/storage' . '/' . $joinEventAndTeam->awards_image }}"
                                                            style="object-fit: cover;" width="50px" height="25px">
                                                    @endif
                                                    {{ $joinEventAndTeam->awards_title ? $joinEventAndTeam->awards_title : '' }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ $joinEventAndTeam->teamName }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ $joinEventAndTeam->teamDescription }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                                </td>
                                                <td class="colorless-column px-1 ps-4 text-start">
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
                                        @endif
                                    @endforeach
                                    <div class="modal fade" id='award-modal' tabindex="-1"
                                        aria-labelledby={{ 'award-modal-label' }} aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form onsubmit="addAward(event);">
                                                    <div class="modal-body modal-body-overflow scrollbarline pe-4">
                                                        <div class="mx-auto text-center mt-3">
                                                            <h5> Choose an award and team </h5>
                                                            <br>
                                                            <div>
                                                                <input type="hidden" name="eventName"
                                                                    value="{{ $event->eventName ?? 'No name yet' }}">
                                                                
                                                                <label class="form-check-label fw-bold">
                                                                    Choose team
                                                                </label>
                                                                <select class="form-select mx-auto" name="teamId"
                                                                    aria-label="Select Team"
                                                                    style="max-width: 200px !important;">
                                                                    @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                                                                        <option
                                                                            value="{{ $joinEventAndTeam->team_id }}">
                                                                            {{ $joinEventAndTeam->teamName }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <br>
                                                            <label class="form-check-label fw-bold">
                                                                Choose award
                                                            </label>
                                                            <div
                                                                class="d-flex flex-row justify-content-start ps-2 pe-5 mt-0">
                                                                @foreach ($awardList as $award)
                                                                    <div
                                                                        class="form-check mx-auto justify-content-start px-2">
                                                                        <input
                                                                            class="form-check-input text-center mx-auto"
                                                                            type="radio" name="awardId"
                                                                            value="{{ $award->id }}">
                                                                        <input type="hidden" name="awardName"
                                                                            value="{{ $award->title }}">
                                                                        <span> {{ $award->title }} </span>
                                                                        <label class="form-check-label"
                                                                            for="awardId">
                                                                            <img src="{{ '/storage' . '/' . $award->image }}"
                                                                                width="150"
                                                                                style="object-fit: cover;"> </span>
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <br>
                                                            <button type="submit"
                                                                class="oceans-gaming-default-button">Submit
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
                        </div>
                        <div class="tab-content pb-4  outer-tab d-none mx-auto" id="Achievements">
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
                            <table class="member-table text-start mx-auto" style="margin-left: -5px;">
                                <thead class="accepted-member-table text-start">
                                    <th></th>
                                    <th class="text-start">
                                        Achievement
                                    </th>
                                    <th class="text-start">
                                        Description
                                    </th>
                                    <th class="text-start">
                                        Year
                                    </th>
                                    <th class="text-start">
                                        Team Name
                                    </th>
                                    <th class="text-start">
                                        Team Description
                                    </th>
                                    <th class="text-start">
                                        Team Created
                                    </th>
                                </thead>
                                <tbody class="accepted-member-table text-start">
                                    @foreach ($achievementsAndTeamList as $key => $joinEventAndTeam)
                                        @if (!is_null($joinEventAndTeam->achievements_id))
                                            <tr class="st">
                                                <td class="colorless-col px-3">
                                                    <svg onclick="redirectToTeamPage({{ $joinEventAndTeam->team_id }});"
                                                        class="gear-icon-btn" xmlns="http://www.w3.org/2000/svg"
                                                        width="20" height="20" fill="currentColor"
                                                        class="bi bi-eye-fill" viewBox="0 0 16 16">
                                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                                        <path
                                                            d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                                    </svg>
                                                </td>
                                                <td class="coloured-cell text-center px-2"> 
                                                    {{ $joinEventAndTeam->achievements_title ? $joinEventAndTeam->achievements_title : '' }}
                                                </td>
                                                 <td class="coloured-cell px-3 text-start">
                                                    {{ $joinEventAndTeam->achievements_description }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ \Carbon\Carbon::parse($joinEventAndTeam->achievements_created_at)->format('Y') }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ $joinEventAndTeam->teamName }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ $joinEventAndTeam->teamDescription }}
                                                </td>
                                                <td class="coloured-cell px-3 text-start">
                                                    {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                                </td>
                                                <td class="colorless-column px-1 ps-4 text-start">
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
                                        @endif
                                    @endforeach
                                    <div class="modal fade" id='achievements-modal' tabindex="-1"
                                        aria-labelledby={{ 'achievements-modal-label' }} aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form onsubmit="addAchievement(event);">
                                                    <div class="modal-body modal-body-overflow scrollbarline pe-4">
                                                        <div class="mx-auto text-center mt-3">
                                                            <h5> Choose an achievement </h5>
                                                            <br>
                                                            <div>
                                                                <label class="form-check-label fw-bold">
                                                                    Choose team
                                                                </label>
                                                                <select class="form-select mx-auto" name="teamId"
                                                                    aria-label="Select Team"
                                                                    style="max-width: 200px !important;">
                                                                    @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                                                                        <option
                                                                            value="{{ $joinEventAndTeam->team_id }}">
                                                                            {{ $joinEventAndTeam->teamName }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <br>
                                                            <input type="hidden" name="eventName"
                                                                value="{{ $event->eventName ?? 'No name yet' }}">
                                                            <label class="form-check-label fw-bold">
                                                                Achievement Title
                                                            </label>
                                                            <input type="text" class="form-control mx-auto" name="title" style="width: 250px;">
                                                            <br>
                                                            <label class="form-check-label fw-bold">
                                                                Achievement Description
                                                            </label>
                                                            <input type="text" class="form-control" name="description">
                                                            <br><br>
                                                            <button type="submit"
                                                                class="oceans-gaming-default-button">Submit
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
                        </div>
                    </div>
                </div>
            </div>
            <br>
    </main>
    @stack('script')
    
    <script>
        var awardToDeleteId = null;
        var achievementToDeleteId = null;
        var actionToTake = null;
        const actionMap = {
            'achievement': deleteAchievementsAction,
            'award': deleteAwardAction
        };

        function showTab(event, tabName, extraClassNameToFilter = "outer-tab") {
            const tabContents = document.querySelectorAll(`.tab-content.${extraClassNameToFilter}`);
            tabContents.forEach(content => {
                content.classList.add("d-none");
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.classList.remove('d-none');
                selectedTab.classList.add('tab-button-active');
            }

            const tabButtons = document.querySelectorAll(`.tab-button-active.${extraClassNameToFilter}`);
            tabButtons.forEach(button => {
                button.classList.remove("tab-button-active");
            });

            let target = event.currentTarget;
            target.classList.add('tab-button-active');
        }

        window.onload = () => { window.loadMessage(); }

        function reloadUrl(currentUrl, message, tab) {
            if (currentUrl.includes('?')) {
                currentUrl = currentUrl.split('?')[0];
            }

            localStorage.setItem('success', 'true');
            localStorage.setItem('message', message);
            localStorage.setItem('tab', tab);
            window.location.replace(currentUrl);
        }

        function takeYesAction() {
            const actionFunction = actionMap[actionToTake];
            if (actionFunction) {
                actionFunction();
            } else {
                Toast.fire({
                    icon: 'error',
                    text: "No action found."
                })
            }
        }

        function takeNoAction() {
            awardToDeleteId = null;
            achievementToDeleteId = null;
            actionToTake = null;
        }

        function deleteAward(id) {
            awardToDeleteId = id;
            actionToTake = 'award';
            window.dialogOpen('Are you sure you want to remove this award from this user?', takeYesAction, takeNoAction)
        }

        function deleteAchievement(id) {
            achievementToDeleteId = id;
            actionToTake = 'achievement';
            window.dialogOpen('Are you sure you want to remove this achievement from this user?', takeYesAction, takeNoAction)
        }

        function editCreatePosition(event) {
            event.preventDefault();
            let formData = new FormData(event.target);
            let joinEventId = formData.get('id');
            let joinEventPosition = formData.get('position');
            const url = "{{ route('event.results.store', ['id' => $event->id]) }}";
            
            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.awards.index', ['id' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message, 'PositionBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error changing position.', error);  }, 
                {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                        ...window.loadBearerCompleteHeader() 
                    },                     
                    body: JSON.stringify({
                        'join_events_id': joinEventId,
                        'position': joinEventPosition,
                        'teamName': formData.get('teamName'),
                        'team_id': formData.get('team_id'),
                        'eventName': formData.get('eventName'),
                        'teamBanner': formData.get('teamBanner'),
                        'creator_id': formData.get('creator_id')
                    })
                }
            );
        }

        async function addAward(event) {
            event.preventDefault();
            let formData = new FormData(event.target);
            let teamId = formData.get('teamId');
            let awardId = formData.get('awardId');
            const url = "{{ route('event.awards.store', ['id' => $event->id]) }}";

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.awards.index', ['id' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message, 'AwardsBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) { toastError('Error changing awards.', error);  }, 
                {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', ...window.loadBearerCompleteHeader() },    
                    body: JSON.stringify({
                        'team_id': Number(teamId),
                        'award_id': Number(awardId),
                        'event_details_id': {{ $event->id }},
                        'award': formData.get('awardName')
                    })
                }
            );
        }

        async function addAchievement(event) {
            event.preventDefault();
            let formData = new FormData(event.target);
            let teamId = formData.get('teamId');
            let title = formData.get('title');
            let description = formData.get('description');
            const url = "{{ route('event.achievements.store', ['id' => $event->id]) }}";

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.awards.index', ['id' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message, 'AchievementsBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) { toastError('Error changing awards.', error);  }, 
                {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', ...window.loadBearerCompleteHeader() },    
                    body: JSON.stringify({
                        'team_id': Number(teamId),
                        title,
                        description,
                        'event_details_id': {{ $event->id }}
                    })
                }
            );
        }

        async function deleteAwardAction() {

            const url = "{{ route('event.awards.destroy', ['id' => $event->id, 'awardId' => ':id']) }}"
                .replace(':id', awardToDeleteId)

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.awards.index', ['id' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message, 'AwardsBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error changing awards.', error);  }, 
                {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', ...window.loadBearerCompleteHeader() },  
                }
            );
        }

         async function deleteAchievementsAction() {
            const url = "{{ route('event.achievements.destroy', ['achievementId' => ':id']) }}"
                .replace(':id', achievementToDeleteId)

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('event.awards.index', ['id' => $event->id]) }}";
                        reloadUrl(currentUrl, responseData.message, 'AchievementsBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error changing achievements.', error);  }, 
                {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', ...window.loadBearerCompleteHeader() },  
                }
            );
        }

        function redirectToProfilePage(userId) {
            window.location.href = "{{ route('public.participant.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

        function goToCreateScreen() {
            let url = "{{ route('event.create') }}";
            window.location.href = url;
        }

        function goToEditScreen() {
            let url = "{{ route('event.edit', $event->id) }}";
            window.location.href = url;
        }

        function redirectToTeamPage(teamId) {
            window.location.href = "{{ route('public.team.view', ['id' => ':id']) }}"
                .replace(':id', teamId);
        }
    </script>
    <script src="{{ asset('/assets/js/jsUtils.js') }}"></script>
