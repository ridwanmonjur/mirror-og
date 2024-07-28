<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Member Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main class="main2">
        @if (isset($redirect) && $redirect)
            <div class="time-line-box mx-auto" id="timeline-box">
                <div class="swiper-container text-center">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide swiper-slide__left" id="timeline-1">
                            <div class="timestamp" onclick="window.toastError('The selected team cannot be changed!');"><span
                                    class="cat">Select Team</span></div>
                            <div class="status__left" onclick="window.toastError('The selected team cannot be changed!');">
                                <span><small></small></span></div>
                        </div>
                        <div class="swiper-slide" id="timeline-2">
                            <div class="timestamp" onclick="window.toastError('This is the current url.');"><span class="text-primary">Manage Members</span></div>
                            <div class="status" onclick="window.toastError('This is the current url.');">
                                <span><small class="bg-primary"></small></span></div>
                        </div>
                        <div class="swiper-slide" id="timeline-launch">
                            <div class="timestamp" onclick="document.getElementById('manageRosterUrl').click();"><span
                                    class="date">Manage Roster</span></div>
                            <div class="status" onclick="document.getElementById('manageRosterUrl').click();">
                                <span><small></small></span></div>
                        </div>
                        <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                            <div class="timestamp"
                                onclick="document.getElementById('manageRegistrationUrl').click();">
                                <span>Manage Registration</span></div>
                            <div class="status__right"
                                onclick="document.getElementById('manageRegistrationUrl').click();">
                                <span><small></small></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="breadcrumb-top">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a onclick="window.toastError('The selected team cannot be changed!');">Select Team</a></li>
                        <li class="breadcrumb-item"><a class="text-primary" onclick="window.toastError('This is the current tab!');">Manage Members</a></li>
                        <li class="breadcrumb-item"><a
                                onclick="document.getElementById('manageRosterUrl').click();">Manage Roster</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                onclick="goToNextScreen('step-launch-1', 'timeline-launch')">Manage Registration</a></li>
                    </ol>
                </nav>
            </div>
        @else
            @include('Participant.__Partials.TeamHead') 
        @endif
        @include('Participant.__MemberManagementPartials.MemberManagement')
        @if (isset($redirect) && $redirect)
            <div class="d-flex box-width back-next mb-5">
                <button onclick="goBackScreens()" type="button"
                    class="btn border-dark rounded-pill py-2 px-4"> Back </button>
                <button onclick="goNextScreens()" type="button" 
                    class="btn btn-primary text-light rounded-pill py-2 px-4"
                    onclick=""> Next > </button>
            </div>
            <br><br><br><br><br><br>
        @endif
    </main>
    
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    <script src="{{ asset('/assets/js/window/addOnload.js') }}"></script>
    @include('Participant.__MemberManagementPartials.MemberManagementScripts')

    <script>
        let currentTabIndexForNextBack = 0;
        function goBackScreens () {
            if (currentTabIndexForNextBack <=0 ) {
                Toast.fire({
                    'icon': 'success',
                    'text': 'Notifications sent already!'
                });
            } else {
                let tabs = document.querySelectorAll('.tab-content');
                console.log({tabs, tabsChildren: tabs});
                for (let tabElement of tabs) {
                    tabElement.classList.add('d-none');
                }

                currentTabIndexForNextBack--;
                tabs[currentTabIndexForNextBack].classList.remove('d-none');
            }
        }

        function goNextScreens () {
            if (currentTabIndexForNextBack >= 2) {
                document.getElementById('manageRosterUrl').click();
            } else {

                let tabs = document.querySelectorAll('.tab-content');
                console.log({tabs, tabsChildren: tabs, currentTabIndexForNextBack});

                for (let tabElement of tabs) {
                    tabElement.classList.add('d-none');
                }

                currentTabIndexForNextBack++;
                tabs[currentTabIndexForNextBack].classList.remove('d-none');
            }
        }

        let actionMap = {
            'approve': approveMemberAction,
            'disapprove': disapproveMemberAction,
            'captain': capatainMemberAction,
            'deleteCaptain': deleteCaptainAction,
            'invite': inviteMemberAction,
            'deleteInvite': withdrawInviteMemberAction
        };

        let dialogForMember = new DialogForMember();

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

        function loadTab() {
            let pageValue = localStorage.getItem('page');

            if (Number(pageValue)) {
                document.getElementById('NewMembersBtn').click();
            }
        }

        function generateHeaders() {
            return {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                ...window.loadBearerHeader(), 
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            };
        }


        addOnLoad( () => { window.loadMessage(); loadTab(); } )
       

        function reloadUrl(currentUrl, buttonName) {
            if (currentUrl.includes('?')) {
                currentUrl = currentUrl.split('?')[0];
            } 

            localStorage.setItem('success', 'true');
            localStorage.setItem('message', 'Successfully updated user.');
            localStorage.setItem('tab', buttonName);            
            window.location.replace(currentUrl);
        }

        function toastError(message, error = null) {
            console.error(error)
            Toast.fire({
                icon: 'error',
                text: message
            });
        }

        function takeYesAction() {
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            })

            const actionFunction = actionMap[dialogForMember.getActionName()];
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
            dialogForMember.reset();
        }

        function approveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('approve')
            window.dialogOpen('Continue with approval?', takeYesAction, takeNoAction)
        }

        function inviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('invite')
            window.dialogOpen('Are you sure you want to send invite to this member?', takeYesAction, takeNoAction)
        }

        function captainMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('captain')
            window.dialogOpen('Are you sure you want to this user captain?', takeYesAction, takeNoAction)
        }

        function deleteCaptain(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('deleteCaptain')
            window.dialogOpen('Are you sure you want to remove this user from captain?', takeYesAction, takeNoAction)
        }

        function withdrawInviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('deleteInvite')
            window.dialogOpen('Are you sure you want to delete your invite to this member??', takeYesAction, takeNoAction)
        }

        function disapproveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            window.dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
        }


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

        function approveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.update', ['id' => ':id']) }}".replace(':id', memberId);

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error accepting member.', error);},  
                {
                    headers: generateHeaders(), 
                    body: JSON.stringify({
                       'actor' : 'team', 'status' : 'accepted'
                    })
                }
            );
        }

        async function disapproveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.update', ['id' => ':id']) }}".replace(':id', memberId);
            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) { toastError('Error disapproving member.', error);}, 
                {
                    headers: generateHeaders(), 
                    body: JSON.stringify({
                       'actor' : 'team', 'status' : 'left'
                    })
                }
            );
        }

        async function capatainMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.captain', ['id' => ':id', 'memberId' => ':memberId']) }}"
                .replace(':memberId', memberId)
                .replace(':id', teamId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error making captain.', error); }, 
                {   headers: generateHeaders(), }
            );
        }

        async function deleteCaptainAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.deleteCaptain', ['id' => ':id', 'memberId' => ':memberId']) }}"
                .replace(':memberId', memberId)
                .replace(':id', teamId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'CurrentMembersBtn');
                    } else {
                       toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error removing captain.', error);
                }, { headers: generateHeaders(), }
            );
        }

        async function inviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}"
                .replace(':userId', memberId).replace(':id', teamId);

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                       toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error inviting members.', error); }, 
                {  headers: generateHeaders(),  }
            );
        }

        async function withdrawInviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.deleteInvite', ['id' => ':id']) }}"
                .replace(':id', memberId);

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'PendingMembersBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error deleting invite members.', error);}, 
                {  headers: generateHeaders()  }
            );
        }

        async function fetchParticipants(event) {
            let input = event.currentTarget;
            let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        reloadUrl(currentUrl, 'NewMembersBtn');
                        window.location.replace(currentUrl);
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error fetching participants.', error);
                }, {
                    headers: generateHeaders(), 
                }
            );
        }

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

        function redirectToProfilePage(userId) {
            window.location.href = "{{route('public.participant.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }
    </script>
</body>
