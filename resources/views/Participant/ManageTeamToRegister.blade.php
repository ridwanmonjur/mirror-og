<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    <main class="main1">
        <div class="team-section">
            <div class="upload-container">
                <label for="image-upload" class="upload-label">
                    <div class="circle-container">
                        <div id="uploaded-image" class="uploaded-image"></div>
                    </div>
                </label>
            </div>
            <div class="team-names">
                <div class="team-info">
                    <h3 class="team-name" id="team-name">{{ $selectTeam->teamName }}</h3>
                    <button class="gear-icon-btn">
                        <a href="/participant/team/{{ $selectTeam['id'] }}/register">
                            <i class="fas fa-cog"></i>
                        </a>
                    </button>
                </div>
            </div>
            <p>We are an awesome team with awesome members! Come be awesome together! Play some games and win some
                prizes GGEZ!
            </p>
        </div>
    </main>

    <main class="main2">
        <div class="tab-content" id="Overview">
            <div class="frame1">
                <div class="container">
                    <div class="left-col">
                        <p>
                            <img src="https://i.pinimg.com/originals/8a/8b/50/8a8b50da2bc4afa933718061fe291520.jpg"
                                class="logo2">
                        <p class="eventName"> {{ $joinEvent->eventDetails->eventName }} </p>
                    </div>
                    <div class="right-col">
                        <p>
                            <img src="/assets/images/dota.png" class="logo2">
                        <div
                            style="font-size: 14px; text-align: left; align-items: center; justify-content: space-between;"
                        >
                            <span>{{ $joinEvent->eventDetails->user->organizer->companyName ?? 'Add' }}</span>
                            <br>
                            <span
                                style="font-size: 12px;">{{ $followCounts[$joinEvent->eventDetails->user->organizer->id] ?? '0' }}
                                Followers</span>
                            <div style="align-items: center;">
                                <button
                                    style="background-color: #43A4D7; color: #FFFFFF; padding: 5px 10px; font-size: 12px; border-radius: 10px; margin-left: 30px;"
                                    type="submit">Follow</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </a>
        
        <div class="tab-content" id="Members" style="display: none; text-align: center;">
            <div class="member-tabs" style="display: flex; justify-content: center;">
                <button class="tab-button" onclick="showMemberTab('CurrentMembers')">Current Members</button>
            </div>
            <div class="tab-content" id="CurrentMembers" data-type="member"
                style="display: none; text-align: center;">
                <p style="text-align: center;">Team {{ $selectTeam->teamName }} has 1 members</p>
                <div class="cont">
                    <div class="leftC">
                        <span class="icon2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            <span> Filter </span>
                        </span>
                        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                        <span class="icon2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                                <path d="M15 7h6v6" />
                            </svg>
                            <span>
                                Sort
                            </span>
                        </span>
                    </div>
                    <div class="rightC">
                        <div class="search_box">
                            <i class="fa fa-search"></i>
                            <input class="nav__input" type="text" placeholder="Search for player name">
                        </div>
                        <div style="padding-right: 200px; transform: translateY(-95%);">
                            @if ($user->id == $selectTeam->user_id)
                                <img src="/assets/images/add.png" height="40px" width="40px">
                            @endif
                        </div>
                    </div>
                </div>
                <table class="member-table">
                    <tbody>
                        <tr class="st">
                            <td>
                                <div class="player-info">
                                    <div class="player-image"
                                        style="background-image: url('https://www.vhv.rs/dpng/d/511-5111355_register-super-admin-icon-png-transparent-png.png')">
                                        <span class="crown">&#x1F451;</span> <!-- Crown emoji -->
                                    </div>
                                    <span>{{ $selectTeam->user->name }}</span>

                                </div>
                            </td>
                            <td class="flag-cell">
                                <img class="nationality-flag" src="{{ asset('/assets/images/china.png') }}"
                                    alt="User's flag">
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="tab-content" id="Members" style="display: none; text-align: center;">
                    <div class="member-tabs" style="display: flex; justify-content: center;">
                        <button class="tab-button" onclick="showMemberTab('CurrentMembers')">Current
                            Members</button>
                        <button class="tab-button" onclick="showMemberTab('PendingMembers')">Pending
                            Members</button>
                    </div>
                    <div class="tab-content" id="CurrentMembers" data-type="member"
                        style="display: none; text-align: center;">
                        <p style="text-align: center;">Team {{ $selectTeam->teamName }} has 
                            {{ $teamMembersProcessed['accepted']['count'] }} members
                        </p>
                        <div class="cont">
                            <div class="leftC">
                                <span class="icon2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-filter">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                                        </polygon>
                                    </svg>
                                    <span> Filter </span>
                                </span>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <span class="icon2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                                        <path d="M15 7h6v6" />
                                    </svg>
                                    <span>
                                        Sort
                                    </span>
                                </span>
                            </div>

                            <div class="rightC">
                                <div class="search_box">
                                    <i class="fa fa-search"></i>
                                    <input class="nav__input" type="text"
                                        placeholder="Search for player name">
                                </div>
                                <div style="padding-right: 200px; transform: translateY(-95%);">
                                    @if ($user->id == $selectTeam->user_id)
                                        <img src="/assets/images/add.png" height="40px" width="40px">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <table class="member-table">
                            <tbody>
                                <tr class="st">
                                    <td>
                                        <div class="player-info">
                                            <div class="player-image"
                                                style="background-image: url('https://www.vhv.rs/dpng/d/511-5111355_register-super-admin-icon-png-transparent-png.png')">
                                                <span class="crown">&#x1F451;</span> <!-- Crown emoji -->
                                            </div>
                                            <span>{{ $selectTeam->user->name }}</span>
                                            <span style="margin-left: 400px;">Joined
                                                {{ $selectTeam['user']->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="flag-cell">
                                        <img class="nationality-flag"
                                            src="{{ asset('/assets/images/china.png') }}" alt="User's flag">
                                    </td>
                                </tr>
                                @foreach ($teamMembersProcessed['accepted']['members'] as $member)
                                    <tr class="st">
                                        <td>
                                            <div class="player-info">
                                                <div class="player-image"
                                                    style="background-image: url('https://cdn-icons-png.flaticon.com/512/149/149071.png')">
                                                </div>
                                                <span>{{ $member->user()->name }}</span>
                                                <span style="margin-left: 400px;">Joined
                                                    {{ $member->user()->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </td>
                                        <td class="flag-cell">
                                            <img class="nationality-flag"
                                                src="{{ asset('/assets/images/china.png') }}"
                                                alt="User's flag">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-content" id="PendingMembers" data-type="member"
                        style="display: none; text-align: center;">

                        <p style="text-align: center;">Team {{ $selectTeam->teamName }} has
                            {{ $teamMembersProcessed['pending']['count'] }} pending members
                        </p>
                        <div class="cont">
                            <div class="leftC">
                                <span class="icon2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-filter">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3">
                                        </polygon>
                                    </svg>
                                    <span> Filter </span>
                                </span>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                <span class="icon2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.2 7.8l-7.7 7.7-4-4-5.7 5.7" />
                                        <path d="M15 7h6v6" />
                                    </svg>
                                    <span>
                                        Sort
                                    </span>
                                </span>
                            </div>

                            <div class="rightC">
                                <div class="search_box">
                                    <i class="fa fa-search"></i>
                                    <input class="nav__input" type="text"
                                        placeholder="Search for player name">
                                </div>
                                <div style="padding-right: 200px; transform: translateY(-95%);">
                                    <img src="/assets/images/add.png" height="40px" width="40px">
                                </div>
                            </div>
                        </div>
                        <table class="member-table">
                            <tbody>
                                @foreach ($teamMembersProcessed['pending']['members'] as $member)
                                    <tr class="st">
                                        <td>
                                            <div class="player-info">
                                                <div class="player-image"
                                                    style="background-image: url('https://cdn-icons-png.flaticon.com/512/149/149071.png')">
                                                </div>
                                                <div class="player-image"
                                                    style="background-image: url('{{ $pendingMember->user->profile_image_url }}')">
                                                </div>
                                                <span>{{ $pendingMember->user()->name }}</span>
                                            </div>
                                        </td>
                                        <td class="flag-cell">
                                            <img class="nationality-flag"
                                                src="{{ asset('/assets/images/china.png') }}"
                                                alt="User's flag">
                                        </td>
                                        <td>
                                            @foreach ($teamManage as $team)
                                                @if ($user->id == $team->user_id)
                                                    <button data-member-id="{{ $pendingMember->id }}"
                                                        onclick="approveMember(this)"
                                                        style="background-color: #3498db; color: #fff; border: none; padding: 5px 10px; cursor: pointer; margin-right: 5px;">
                                                        ✔
                                                    </button>
                                                    <button onclick="rejectMember('{{ $pendingMember->id }}')"
                                                        style="background-color: #e74c3c; color: #fff; border: none; padding: 5px 10px; cursor: pointer;">✘</button>
                                                @endif
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </main>

    @include('CommonLayout.BootstrapV5Js')

    <script>
    

        function slideEvents(direction) {
            const eventBoxes = document.querySelectorAll('.event-box');

            const visibleEvents = Array.from(eventBoxes).filter(eventBox => eventBox.style.display !== 'none');

            eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

            let startIndex = 0;

            if (visibleEvents.length > 0) {
                startIndex = (Array.from(eventBoxes).indexOf(visibleEvents[0]) + direction + eventBoxes.length) % eventBoxes
                    .length;
            }

            for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
                const index = (startIndex + i + eventBoxes.length) % eventBoxes.length;
                eventBoxes[index].style.display = 'block';
            }
        }

        function showTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                content.style.display = 'none';
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';

                if (tabName === 'Active Rosters') {
                    const activeRostersForm = document.getElementById('activeRostersForm');
                    activeRostersForm.style.display = 'block';
                }
            }
        }


        function initializeEventsDisplay() {
            const eventBoxes = document.querySelectorAll('.event-box');

            eventBoxes.forEach(eventBox => (eventBox.style.display = 'none'));

            for (let i = 0; i < Math.min(2, eventBoxes.length); i++) {
                eventBoxes[i].style.display = 'block';
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            initializeEventsDisplay();
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const currentMembersTab = document.getElementById('CurrentMembers');
            const pendingMembersTab = document.getElementById('PendingMembers');

            currentMembersTab.addEventListener('click', function() {
                showMemberTab('CurrentMembers');
            });

            pendingMembersTab.addEventListener('click', function() {
                showMemberTab('PendingMembers');
            });
        });

        function showMemberTab(tabName) {
            const memberTabs = document.querySelectorAll('.tab-content[data-type="member"]');
            memberTabs.forEach(tab => {
                tab.style.display = 'none';
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';

                if (tabName === 'CurrentMembers') {} else if (tabName === 'PendingMembers') {}
            }
        }
    </script>

    <script>
        function approveMember(button) {
            const memberId = button.getAttribute('data-member-id');


            const url = "{{ route('participant.member.approve', ['id' => ':id']) }}".replace(':id', memberId);


            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const memberRow = button.closest('tr');
                        memberRow.remove();
                    } else {
                        console.error('Error updating member status:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error approving member:', error);
                });
        }
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const searchInputs = document.querySelectorAll('.search_box input');
            const memberTables = document.querySelectorAll('.member-table');

            searchInputs.forEach((searchInput, index) => {
                searchInput.addEventListener("input", function() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const memberRows = memberTables[index].querySelectorAll('tbody tr');

                    memberRows.forEach(row => {
                        const playerName = row.querySelector('.player-info span')
                            .textContent.toLowerCase();

                        if (playerName.includes(searchTerm)) {
                            row.style.display = 'table-row';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>

    {{-- End Javascript for Search Member  --}}


</body>
