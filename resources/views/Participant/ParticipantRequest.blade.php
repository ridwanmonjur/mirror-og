<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamList.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    @include('Participant.__ParticipantRequestPartials.RequestManagement')
    <script src="{{ asset('/assets/js/window/addOnload.js') }}"></script>
    <script src="{{ asset('/assets/js/models/DialogForMember.js') }}"></script>
    <script src="{{ asset('/assets/js/fetch/fetch.js') }}"></script>  
    <script>
       function generateHeaders() {
            return {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                ...window.loadBearerHeader(), 
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            };
        }
  
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

        addOnLoad( () => { window.loadTab(); } );

         let actionMap = {
            'approve': approveTeamAction,
            'disapprove': disapproveTeamAction,
            'deleteInvite': withdrawInviteMemberAction
        };

        function reloadUrl(currentUrl, buttonName, teamName) {
            if (currentUrl.includes('?')) {
                currentUrl = currentUrl.split('?')[0];
            } 

            currentUrl += `?tab=${buttonName}&success=true&team=${teamName}`;
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
            window.dialogOpen('Continue with approval?', takeYesAction, takeNoAction)
        }

        function inviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('invite')
            window.dialogOpen('Are you sure you want to send invite to this member?', takeYesAction, takeNoAction)
        }

        function withdrawInviteMember(memberId, teamId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setTeamId(teamId);
            dialogForMember.setActionName('deleteInvite')
            window.dialogOpen('Are you sure you want to delete your request to this team??', takeYesAction, takeNoAction)
        }

        function disapproveTeam(memberId) {
            dialogForMember.setMemberId(memberId);
            dialogForMember.setActionName('disapprove')
            window.dialogOpen('Continue with reject?', takeYesAction, takeNoAction)
        }

        function approveTeamAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.update', ['id' => ':id']) }}".replace(':id', memberId);
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
                function(error) { toastError('Error accepting member.', error);},  
                {
                    headers: generateHeaders(), 
                    body: JSON.stringify({
                       'actor' : 'user', 'status' : 'accepted'
                    })
                }
            );
        }

        async function disapproveTeamAction() {
            const memberId = dialogForMember.getMemberId();
            const url = "{{ route('participant.member.update', ['id' => ':id']) }}".replace(':id', memberId);

            fetchData(url,
                function(responseData) {
                    if (responseData.success) {
                        let currentUrl = "{{ route('participant.request.view') }}";
                        reloadUrl(currentUrl, 'InvitatedTeamBtn');
                    } else {
                        toastError(responseData.message)
                    }
                },
                function(error) { toastError('Error disapproving member.', error);}, 
                {
                    headers: generateHeaders(), 
                    body: JSON.stringify({
                       'actor' : 'user', 'status' : 'rejected'
                    })
                }
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
                        let currentUrl = "{{ route('participant.request.view') }}";
                        reloadUrl(currentUrl, 'PendingTeamBtn');
                    } else {
                        toastError(responseData.message);
                    }
                },
                function(error) { toastError('Error deleting member.', error);  }, 
                {  headers: generateHeaders(), }
            );
        }

        function redirectToProfilePage(userId) {
            window.location.href = "{{route('public.participant.view', ['id' => ':id']) }}"
                .replace(':id', userId);
        }

        function redirectToTeamPage(teamId) {
            window.location.href = "{{route('participant.team.manage', ['id' => ':id']) }}"
                .replace(':id', teamId);
        }
        
    </script>

</body>
