<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.3.0/tagify.css">
    <link rel="stylesheet" href="{{ asset('/assets/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    @include('CommonLayout.NavbarforParticipant')
    @include('Participant.ParticipantRequest.RequestManagement')

    @include('CommonLayout.BootstrapV5Js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.6/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>
    @include('CommonLayout.Toast')
    @include('CommonLayout.Dialog')
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
            let teamName = urlParams.get('teamName');
            let pageValue = urlParams.get('page');

            if (tabValue) {
                document.getElementById(tabValue).click();
            }

            if (Number(pageValue)) {
                document.getElementById('PrivateInvitationsBtn').click();
            }

            if (successValue == 'true') {
                if (teamName) {
                    Toast.fire({
                        icon: 'success',
                        text: `Successfully added team ${teamName}.`
                    })
                } else {
                    Toast.fire({
                        icon: 'success',
                        text: "Successfully updated team."
                    })
                }
            }
        }

        loadTab();

         let actionMap = {
            'approve': approveTeamAction,
            'disapprove': disapproveTeamAction,
            'deleteInvite': deleteInviteMemberAction
        };

        function reloadUrl(currentUrl, buttonName, teamName) {
            currentUrl += (currentUrl.indexOf('?') !== -1 ? '&' : '?') + `tab=${buttonName}&success=true&team=${teamName}`;
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

        function approveTeam(memberId) {
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

        function deleteInviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('deleteInvite')
            dialogOpen('Are you sure you want to delete your request to this team??', takeYesAction, takeNoAction)
        }

        function disapproveTeam(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            dialogOpen('Continue with reject?', takeYesAction, takeNoAction)
        }

        function approveTeamAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.approve', ['id' => ':id']) }}".replace(':id', memberId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.request.view') }}";
                        reloadUrl(currentUrl, 'InvitatedTeamBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error making captain.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function disapproveTeamAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.rejectInvite', ['id' => ':id']) }}".replace(':id', memberId);
            console.log({
                memberId: dialogForMember.getMemberId(),
                action: dialogForMember.getActionName()
            });

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.request.view') }}";
                        reloadUrl(currentUrl, 'InvitatedTeamBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) {
                    toastError('Error disapproving member.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        async function deleteInviteMemberAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.deleteInvite', ['id' => ':id']) }}"
                .replace(':id', memberId);

            fetchData(
                url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.request.view') }}";
                        reloadUrl(currentUrl, 'PendingTeamBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) {
                    toastError('Error deleteInvite members.', error);
                }, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            );
        }

        function redirectToProfilePage(userId) {
            window.location.href = "{{route('participant.profile.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

        function redirectToTeamPage(teamId) {
            window.location.href = "{{route('participant.team.manage', ['id' => ':id']) }}"
                .replace(':id', teamId);
        }
        
    </script>

</body>
