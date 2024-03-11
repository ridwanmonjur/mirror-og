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
{{-- @php
    dd($user);
@endphp --}}

<body>
    @include('CommonLayout.NavbarforParticipant')

    @include('Participant.Layout.TeamHeadNoFileChange')

    <main class="main2">
        @include('Participant.Layout.MemberManagement')
    </main>

    @include('CommonLayout.BootstrapV5Js')

    <script>
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

        async function approveMember(button) {
            const memberId = button.getAttribute('data-member-id');
            const url = "{{ route('participant.member.approve', ['id' => ':id']) }}".replace(':id', memberId);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();
                if (responseData.success) {
                    const memberRow = button.closest('tr');
                    memberRow.remove();
                } else {
                    console.error('Error updating member status:', responseData.message);
                }
            } catch (error) {
                console.error('Error approving member:', error);
            }
        }

        async function disapproveMember(memberId) {
            const url = "{{ route('participant.member.disapprove', ['id' => ':id']) }}".replace(':id', memberId);

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const responseData = await response.json();
                if (responseData.success) {
                    const memberRow = button.closest('tr');
                    memberRow.remove();
                } else {
                    console.error('Error updating member status:', responseData.message);
                }
            } catch (error) {
                console.error('Error approving member:', error);
            }
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
