<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/pie-chart.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<script>
    let registrationPaymnentMap = {}; 
</script>
<body>
    @include('CommonLayout.NavbarGoToSearchPage')
    @include('Participant.Layout.TeamHead')
    <main class="main2">
        <div id="Overview">
            <br><br>
            <div class="mx-auto" style="width: 80%;"><b>Outstanding Registrations</b></div>
            <br> <br>
            <div class="position-relative d-flex justify-content-center">
                @if (!isset($joinEvents[0]))
                    <p>No events available</p>
                @else
                    <div class="event-carousel-styles">
                        @foreach ($joinEvents as $key => $joinEvent)
                            @include('Participant.Layout.RosterView', ['isRegistrationView' => true])
                            @include('Participant.Layout.PieChart', ['isInvited' => 'no'])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div id="Overview">
            <br><br>
            <div class="mx-auto" style="width: 80%;"><b>Event Invitations</b></div>
             <br> <br>
            <div class="position-relative d-flex justify-content-center">
                @if (!isset($invitedEvents[0]))
                    <p>No events available</p>
                @else
                    <div class="event-carousel-styles">
                        @foreach ($invitedEvents as $key => $joinEvent)
                            @include('Participant.Layout.RosterView', ['isRegistrationView' => true])
                            @include('Participant.Layout.PieChart', ['isInvited' => 'yes'])
                        @endforeach
                    </div>
                @endif
            <br> <br>
        </div>
            
        
    </main>

    @include('CommonLayout.BootstrapV5Js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

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
