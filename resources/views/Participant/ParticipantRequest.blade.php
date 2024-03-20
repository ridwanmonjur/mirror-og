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
    <main class="main2">
        @include('Participant.ParticipantRequest.MemberManagement')
    </main>

    @include('CommonLayout.BootstrapV5Js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    @include('CommonLayout.Toast')
    @include('CommonLayout.Dialog')
    @include('Participant.ParticipantRequest.MemberManagementScripts')
    <script>
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
            let currentUrl = window.location.href;
            let urlParams = new URLSearchParams(window.location.search);
            let tabValue = urlParams.get('tab');
            let successValue = urlParams.get('success');
            let pageValue = urlParams.get('page');

            if (tabValue) {
                document.getElementById(tabValue).click();
            }

            if (Number(pageValue)) {
                document.getElementById('PrivateInvitationsBtn').click();
            }

            if (successValue == 'true') {
                Toast.fire({
                    icon: 'success',
                    text: "Successfully updated user."
                })
            }
        }

        loadTab();

        function takeYesAction() {
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            })

            if (dialogForMember.getActionName() == 'approve') {
                approveMemberAction();
            } else if (dialogForMember.getActionName() == 'disapprove') {
                disapproveMemberAction();
            } else if (dialogForMember.getActionName() == 'captain') {
                disapproveMemberAction();
            } else if (dialogForMember.getActionName() == 'invite') {
                inviteMemberAction();
            } else if (dialogForMember.getActionName() == 'uninvite') {
                uninviteMemberAction();
            }
        }

        function takeNoAction() {
            dialogForMember.reset();
        }

        function approveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('approve')
            dialogOpen('Continue with approval?', takeYesAction, takeNoAction)
        }

        function inviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('invite')
            dialogOpen('Are you sure you want to send invite to this member?', takeYesAction, takeNoAction)
        }

        function uninviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('uninvite')
            dialogOpen('Are you sure you want to delete your invite to this member??', takeYesAction, takeNoAction)
        }

        function disapproveMember(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            dialogOpen('Continue with disapproval?', takeYesAction, takeNoAction)
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
            const url = "{{ route('participant.member.approve', ['id' => ':id']) }}".replace(':id', memberId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') +
                            'tab=PendingTeamBtn&success=true';
                        window.location.replace(currentUrl);
                    } else {
                        console.error('Error updating member status:', responseData.message);
                    }
                },
                function(error) {
                    console.error('Error approving member:', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function disapproveMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.disapprove', ['id' => ':id']) }}".replace(':id', memberId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') +
                            'tab=SentTeamBtn&success=true';
                        window.location.replace(currentUrl);
                    } else {
                        console.error('Error updating member status:', responseData.message);
                    }
                },
                function(error) {
                    console.error('Error disapproving member:', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function inviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}"
                .replace(':userId', memberId)
                .replace(':id', teamId);
            console.log({ memberId: dialogForMember.getMemberId(), action: dialogForMember.getActionName() });

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') + 'tab=SentTeamBtn&success=true';
                        window.location.replace(currentUrl);
                    } else {
                        console.error('Error updating member status:', responseData.message);
                    }
                },
                function(error) {
                    console.error('Error inviting member:', error);
                },
                {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function unInviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const teamId = dialogForMember.getTeamId();
            const url = "{{ route('participant.member.invite', ['id' => ':id', 'userId' => ':userId']) }}"
                .replace(':userId', memberId)
                .replace(':id', teamId);

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.member.manage', ['id' => $selectTeam->id]) }}";
                        currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') + 'tab=SentTeamBtn&success=true';
                        window.location.replace(currentUrl);
                    } else {
                        console.error('Error updating member status:', responseData.message);
                    }
                },
                function(error) {
                    console.error('Error uninviting member:', error);
                },
                {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
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
                        currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') + 'tab=SentTeamBtn&success=true';
                        window.location.replace(currentUrl);
                    } else {
                        console.error('Error updating member status:', responseData.message);
                    }
                },
                function(error) {
                    console.error('Error uninviting member:', error);
                },
                {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
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
    </script>

</body>
