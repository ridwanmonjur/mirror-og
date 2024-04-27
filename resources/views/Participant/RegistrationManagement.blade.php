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
<body>
    @include('CommonPartials.NavbarGoToSearchPage')
    @include('Participant.Partials.TeamHead')
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
                            @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                            @include('Participant.Partials.PieChart', ['isInvited' => false])
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
                            @include('Participant.Partials.RosterView', ['isRegistrationView' => false])
                            @include('Participant.Partials.PieChart', ['isInvited' => true])
                        @endforeach
                    </div>
                @endif
            <br> <br>
        </div>
            
        
    </main>

    @include('CommonPartials.BootstrapV5Js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let registrationPaymentModalMap = {}; 

        function updateInput(input) {
            let modalId = input.dataset.modalId;

            if (!(registrationPaymentModalMap.hasOwnProperty(modalId))) {
                registrationPaymentModalMap[modalId] = 0;
            } 

            let total = Number(input.dataset.totalAmount);
            let index = registrationPaymentModalMap[modalId];
            let totalLetters = 4;
            let newValue = input.value.replace(/[^\d]/g, '');
            let numNewValue = newValue;
            let total = Number(input.dataset.totalAmount);
            let lettersToTake = index - totalLetters;
            let isMoreThanTotalLetters = lettersToTake >= 0;
            console.log({numNewValue})
            if (numNewValue >= total) {
                newValue = total.toFixed(2);
            } else {
                if (isMoreThanTotalLetters) {
                    console.log("sss")
                    let length = newValue.length;
                    newValue = newValue.substr(0, lettersToTake + 3) + '.' + newValue.substr(lettersToTake + 3, 2);
                } else { 
                    console.log({total, newValue})
                    newValue = newValue.substr(1, 2) + '.' + newValue.substr(3, 2);
                }
            }
            
            registrationPaymentModalMap[modalId] ++;
            
            input.value = newValue;
            putAmount(input.dataset.modalId, newValue, total, Number(input.dataset.existingAmount));
        }

        function keydown(input) {
            let modalId = input.dataset.modalId;
            if (event.key === "Backspace" || event.key === "Delete") { 
                event.preventDefault();
            }
            if (event.key.length === 1 && !/\d/.test(event.key)) {
                event.preventDefault();
            }
        }

        function moveCursorToEnd(input) {
            input.focus(); 
            input.setSelectionRange(input.value.length, input.value.length);
        }

        function putAmount(modalId, inputValue, total, existing) {
            let putAmountTextSpan = document.querySelector('#payModal' + modalId + ' .putAmountClass');
            let pieChart = document.querySelector('#payModal' + modalId + ' .pie');
            inputValue = Number(inputValue);
            console.log({inputValue, existing, total})
            let percent = ((existing + inputValue) * 100) / total; 
            pieChart.style.setProperty('--p', percent);
            pieChart.innerText = percent.toFixed(2) + "%" ;
            putAmountTextSpan.innerText = inputValue.toFixed(2);
        }

        function resetInput(button) {
            let input = document.querySelector('#payModal' + button.dataset.modalId + " input[name='amount']");
            registrationPaymentModalMap[button.dataset.modalId] = 0;
            input.value = input.defaultValue;
            putAmount(button.dataset.modalId, 0.00, Number(button.dataset.totalAmount), Number(button.dataset.existingAmount));
        }
    </script>
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
