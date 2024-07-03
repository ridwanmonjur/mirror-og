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
    @include('Participant.TeamHeadPartials.TeamHead')
    <main class="main2">
        <div id="Overview">
            <br>
            @if (session('successMessage'))
                <div class="text-success text-center">{{session('successMessage')}}</div>
                <br>
            @elseif (session('errorMessage'))
                <div class="text-red text-center">{{session('errorMessage')}}</div>
                <br>
            @else
                <br>
            @endif
            <div class="tab-size"><b>Outstanding Registrations</b></div>
            <br> <br>
            <div @class(["event-carousel-works", 
                "event-carousel-styles" => isset($joinEvents[1]),
                "d-flex justify-content-center " => !isset($joinEvents[1])
            ])
            >
                @foreach ($joinEvents as $key => $joinEvent)
                    @include('Participant.Partials.RosterView',  ['isRegistrationView' => false])
                @endforeach
            </div>
        </div>
        <div id="Overview">
            <br><br>
            <div class="tab-size"><b>Event Invitations</b></div>
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
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function submitConfirmCancelForm(text) {
            window.dialogOpen(text, ()=> {
                document.getElementById('confirmRegistration').submit();
            }, null)
        }
        let registrationPaymentModalMap = {}; 

        function updateInput(input) {
            let ogTotal = Number(input.dataset.totalAmount);
            let total = ogTotal;
            let pending = Number(input.dataset.pendingAmount);
            let modalId = input.dataset.modalId;

            if (!(registrationPaymentModalMap.hasOwnProperty(modalId))) {
                registrationPaymentModalMap[modalId] = 0;
            } 

            let index = registrationPaymentModalMap[modalId];
            let totalLetters = 4;
            let newValue = input.value.replace(/[^\d]/g, '');
            let lettersToTake = index - totalLetters;
            let isMoreThanTotalLetters = lettersToTake >= 0;
            if (isMoreThanTotalLetters) {
                let length = newValue.length;
                newValue = newValue.substr(0, lettersToTake + 3) + '.' + newValue.substr(lettersToTake + 3, 2);
            } else { 
                newValue = newValue.substr(1, 2) + '.' + newValue.substr(3, 2);
            }
            console.log({
                plusValue: +newValue
            })

            if (+newValue > total) {
                newValue = pending.toFixed(2);
            }

            registrationPaymentModalMap[modalId] ++;
            
            input.value = newValue;
            putAmount(input.dataset.modalId, newValue, ogTotal, pending, Number(input.dataset.existingAmount));
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

        function putAmount(modalId, inputValue, total, pending, existing) {
            let putAmountTextSpan = document.querySelector('#payModal' + modalId + ' .putAmountClass');
            let pieChart = document.querySelector('#payModal' + modalId + ' .pie');
            inputValue = Number(inputValue);
            console.log({inputValue, existing, total})
            let percent = ((existing + inputValue) * 100) / total; 
            console.log({inputValue, existing, total, percent})
            pieChart.style.setProperty('--p', percent);
            pieChart.innerText = percent.toFixed(0) + "%" ;
            putAmountTextSpan.innerText = inputValue.toFixed(2);
        }

        function resetInput(button) {
            let input = document.querySelector('#payModal' + button.dataset.modalId + " input[name='amount']");
            registrationPaymentModalMap[button.dataset.modalId] = 0;
            input.value = input.defaultValue;
            putAmount(button.dataset.modalId, 0.00, Number(button.dataset.totalAmount), Number(button.dataset.pendingAmount), Number(button.dataset.existingAmount));
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
