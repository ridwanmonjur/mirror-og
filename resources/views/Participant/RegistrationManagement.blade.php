<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Management</title>
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/teamAdmin.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/pie-chart.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/timeline.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/participant/manage_team.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>
<body
    @style(["min-height: 100vh;" => $isRedirect])
>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main 
        @style(["height: 95vh" => $isRedirect])
     class="main2">

    @if ($isRedirect)
        <form method="POST" action="{{ route('participant.memberManage.action') }}">
            @csrf
            <input type="hidden" value="{{$eventId}}" name="eventId">
            <input type="hidden" value="{{$selectTeam->id}}" name="teamId">
            <input id="manageMemberButton" type="submit" class="d-none" value="Done">
            </div>
        </form>
        <a class="d-none" id="manageRosterUrl" href="{{route('participant.roster.manage', ['id' => $eventId, 'teamId' => $selectTeam->id, 'redirect' => 'true' ] ) }}"> </a>
        <a class="d-none" id="manageRegistrationUrl" href="{{route('participant.register.manage', ['id' => $selectTeam->id, 'eventId' => $eventId ] ) }}"> </a>
        <a class="d-none" id="eventUrl" href="{{route('participant.event.view', ['id' => $eventId ] ) }}"> </a>

        <div class="time-line-box mx-auto" id="timeline-box">
            <div class="swiper-container text-center">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-slide__left" id="timeline-1">
                        <div class="timestamp" onclick="window.toastError('Cannot go back to \'Select Team\'.');"><span
                                class="cat">Select Team</span></div>
                        <div class="status__left" onclick="window.toastError('Cannot go back to \'Select Team\'.');">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-2">
                        <div class="timestamp" onclick="document.getElementById('manageMemberButton')?.click();"><span >Manage Members</span></div>
                        <div class="status" onclick="document.getElementById('manageMemberButton')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide" id="timeline-launch">
                        <div class="timestamp" onclick="document.getElementById('manageRosterUrl')?.click();"><span
                                class="date">Manage Roster</span></div>
                        <div class="status" onclick="document.getElementById('manageRosterUrl')?.click();">
                            <span><small></small></span></div>
                    </div>
                    <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                        <div class="timestamp text-primary"
                            onclick="document.getElementById('manageRegistrationUrl').click();">
                            <span>Manage Registration</span></div>
                        <div class="status__right"
                            onclick="document.getElementById('manageRegistrationUrl').click();">
                            <span><small class="bg-primary"></small></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="breadcrumb-top">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a onclick="window.toastError('Cannot go back to \'Select Team\'.');">Select Team</a></li>
                    <li class="breadcrumb-item"><a onclick="document.getElementById('manageMemberButton')?.click();">Manage Members</a></li>
                    <li class="breadcrumb-item"><a 
                            onclick="document.getElementById('manageRosterUrl').click();">Manage Roster</a>
                    </li>
                    <li class="breadcrumb-item"><a class="text-primary" 
                            onclick="document.getElementById('manageRegistrationUrl').click();">Manage Registration</a></li>
                </ol>
            </nav>
        </div>
    @else
        @include('Participant.__Partials.TeamHead') 
    @endif
        <div id="Overview">
            @if (!$isRedirect)
                <br>
                @if (session('successMessage'))
                    <div class="text-success text-center">{{session('successMessage')}}</div><br>
                @elseif (session('errorMessage'))
                    <div class="text-red text-center">{{session('errorMessage')}}</div><br>
                @else
                    <br>
                @endif
                <div class="tab-size"><b>Outstanding Registrations</b></div><br> <br>
            @endif
            
            @if (!isset($joinEvents[0]))
                <p class="tab-size text-start mx-auto ">No events available</p>
            @else
            <div @class(["event-carousel-styles" => !$isRedirect, "mx-5 px-5"])>
                @foreach ($joinEvents as $joinEvent)
                    @if ($isRedirect) 
                        <div class="text-center">
                            <h5><u>Event Registration</u></h5>
                            <p>You can pay now, or complete payment later...</p>
                        </div>
                    @else  
                        @include('Participant.__Partials.RosterView', ['isRegistrationView' => false])
                    @endif
                    @include('Participant.__Partials.PieChart', ['isInvited' => false])
                @endforeach
            </div>

            @endif
        </div>
        @if (!$isRedirect)
            <div id="Invitation">
                <br><br>
                <div class="tab-size"><b>Event Invitations</b></div>
                <br> <br>
                <div class="position-relative d-flex justify-content-center">
                    @if (!isset($invitedEvents[0]))
                        <p class="tab-size text-start mx-auto">No events available</p>
                    @else
                        <div class="event-carousel-styles mx-5 px-5">
                            @foreach ($invitedEvents as $key => $joinEvent)
                                @include('Participant.__Partials.RosterView', ['isRegistrationView' => false])
                                @include('Participant.__Partials.PieChart', ['isInvited' => true])
                            @endforeach
                        </div>
                    @endif
                <br> <br>
            </div>
        @endif
        @if ($isRedirect)
            <div class="d-flex box-width back-next mb-5">
                <button type="button"
                    class="btn border-dark rounded-pill py-2 px-4" onclick="document.getElementById('manageRosterUrl')?.click();"> Back </button>
                <button type="button" 
                    class="btn btn-success text-dark rounded-pill py-2 px-4"
                    onclick="document.getElementById('eventUrl')?.click();">  View Event  </button>
            </div>
        @else 
            <br><br><br><br><br><br>
        @endif
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function submitConfirmCancelForm(event, text, id) {
            let form = event.target.dataset.form;
            window.dialogOpen(text, ()=> {
                document.querySelector(`#${id}.${form}`).submit();
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
