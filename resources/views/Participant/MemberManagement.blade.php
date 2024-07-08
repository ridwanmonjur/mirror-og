<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    

</head>

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    @include('Participant.TeamHeadPartials.TeamHead')

    <main class="main2">
        @include('Participant.MemberManagementPartials.MemberManagement')
    </main>

    
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    <script src="{{ asset('/assets/js/window/addOnload.js') }}"></script>
    @include('Participant.MemberManagementPartials.MemberManagementScripts')
    <script src="{{ asset('/assets/js/window/addOnload.js') }}"></script>

    <script>
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
