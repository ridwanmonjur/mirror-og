@include('Organizer.Layout.ViewEventHeadTag')

@php

$stylesEventStatus = '';
$stylesEventStatus .= 'padding-top: -150px; ';
$stylesEventStatus .= 'background-color: ' . $mappingEventState[$event->eventStatus]['buttonBackgroundColor'] .' ;' ;
$stylesEventStatus .= 'color: ' . $mappingEventState[$event->eventStatus]['buttonTextColor'] .' ; ' ;
$stylesEventStatus .= 'border: 1px solid ' . $mappingEventState[$event->eventStatus]['borderColor'] .' ; ';

$stylesEventRatio = '';
$ratio = (double) $event->registeredParticipants / $event->totalParticipants;
if ($ratio > 0.9){
$stylesEventRatio .= "background-color: red; color: white;";
}
elseif ($ratio == 0){
$stylesEventRatio .= "background-color: #8CCD39; color: white;";
}
elseif ($ratio > 0.5){
$stylesEventRatio .= "background-color: #FA831F; color: white;";
}
elseif ($ratio <= 0.5){ $stylesEventRatio .="background-color: #FFE325; color: black;" ; } $eventTierLower=strtolower($event->eventTier);

    $date = \Carbon\Carbon::parse($event->startDateTime)->setTimezone('Asia/Singapore');
    $dayStr = $date->englishDayOfWeek;
    $timeStr = $date->toFormattedDateString();
    $dateStr = $date->isoFormat('h:mm a');

    @endphp

    <body>
        <nav class="navbar">
            <div class="logo">
                <img width="160px" height="60px" src="{{ asset('/assets/images/logo-default.png') }}" alt="">
            </div>
            <svg style="margin-top: 10px; margin-right: 10px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-menu menu-toggle" onclick="toggleNavbar()">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
            <div class="search-bar d-none-at-mobile">
                <input type="text" name="search" id="search" placeholder="Search for events">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="nav-buttons">
                <button class="oceans-gaming-default-button oceans-gaming-gray-button"> Where is moop? </button>
                <img style="position: relative; top: 15px;" width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
                <img style="position: relative; top: 15px;" width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
            </div>
        </nav>
        <nav class="mobile-navbar d-centered-at-mobile d-none">
            <div class="search-bar search-bar-mobile ">
                <input type="text" name="search" id="search" placeholder="Search for events">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search" style="left: 40px;">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            <div class="nav-buttons search-bar-mobile d-centered-at-mobile">
                <img width="50px" height="40px" src="{{ asset('/assets/images/navbar-account.png') }}" alt="">
                <img width="70px" height="40px" src="{{ asset('/assets/images/navbar-crown.png') }}" alt="">
            </div>
        </nav>
        <main>
            <br class="d-none-at-desktop">
            <div class="">
                <header class="flexbox-welcome">
                    <u>
                        <h3>
                            Manage your events
                        </h3>
                    </u>
                    <input type="submit" value="Create Event">
                </header>
            </div>
            <br><br>
            <div class="grid-container">
                <div class="{{'side-image side-image-' . $eventTierLower }}">
                    <img class="side-image-absolute-bottom" src="{{  asset( '/assets/images/'. $eventTierLower . '.png' ) }}" width="180" height="125">
                </div>
                <div>
                    <div style="padding-left: 20px;padding-right:20px;">
                        <div>
                            <img class="{{'card-image card-image-' . $eventTierLower }}" src="{{ asset('/assets/images/1.png') }}" alt="">
                        </div>
                        <div class="grid-container-two-columns-at-desktop">
                            <div class="card-text">
                                <div>
                                    <br>
                                    <div class="flexbox-centered-space">
                                        <p style="height:60px;text-overflow:ellipsis; overflow:hidden;font-size:20px;margin-right:60px;margin-bottom:20px">
                                            <u>{{$event->eventName}}</u>
                                        </p>
                                        <svg style="margin-top: -30px; margin-left: -60px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2">
                                            <circle cx="18" cy="5" r="3"></circle>
                                            <circle cx="6" cy="12" r="3"></circle>
                                            <circle cx="18" cy="19" r="3"></circle>
                                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                                        </svg>
                                    </div>
                                    <div class="flexbox-centered-space card-subtitle">
                                        <div class="flexbox-centered-space">
                                            <img style="display: inline;" src="{{ asset('/assets/images/menu.png') }}" class="{{ 'rounded-image rounded-box-' . $eventTierLower }}" alt="menu">
                                            &nbsp;
                                            <div class="card-organizer">
                                                <p style="display: inline;"><u> {{$event->organizerName}} </u> </p>
                                                <p class="small-text"> <i> 104 followers </i> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <h4> <u> @php echo $dayStr .', ' . $dateStr; @endphp </u> </h4>
                                    <h4> <u> @php echo $timeStr; @endphp </u> </h4>
                                    <br>
                                    <div>
                                        <div class="tab">
                                            <button class="{{ 'side-image-' . $eventTierLower . ' tablinks active' }}" onclick="openTab(event, 'Overview')">Overview</button>
                                            <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}" onclick="openTab(event, 'Bracket')">Bracket</button>
                                            <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}" onclick="openTab(event, 'Teams')">Teams</button>
                                            <button class="{{ 'side-image-' . $eventTierLower . ' tablinks' }}" onclick="openTab(event, 'Result')">Result</button>

                                        </div>
                                        <br>
                                        <div id="Overview" class="tabcontent" style="display: block;">
                                            <h3><u>About this event</u></h3>
                                            <p>{{ $event->eventDescription }}</p>
                                        </div>

                                        <div id="Bracket" class="tabcontent">
                                            <h3><u>Bracket</u></h3>
                                            <p>Bracket is the capital of France.</p>
                                        </div>

                                        <div id="Teams" class="tabcontent">
                                            <h3><u>Teams</u></h3>
                                            <p>Teams tab.</p>
                                        </div>
                                        <div id="Result" class="tabcontent">
                                            <h3><u>Result</u></h3>
                                            <p>Result tab.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <br><br>
                                <button class="oceans-gaming-default-button" style="@php echo $stylesEventStatus; @endphp">
                                    <u>{{$event->eventStatus}}</u>
                                </button>
                                <br><br>
                                <div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                        &nbsp;
                                        <span>{{$event->prize}}</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                            <line x1="12" y1="1" x2="12" y2="23"></line>
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                        </svg>
                                        &nbsp;
                                        <span>{{$event->fee ? $event->fee : 'Free'}}</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                        &nbsp;
                                        <span>{{ $event->region }}</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart-2">
                                            <line x1="18" y1="20" x2="18" y2="10"></line>
                                            <line x1="12" y1="20" x2="12" y2="4"></line>
                                            <line x1="6" y1="20" x2="6" y2="14"></line>
                                        </svg>
                                        &nbsp;
                                        <span>{{$event->registeredParticipants}} / {{$event->totalParticipants}}</span>
                                    </div>
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        &nbsp;
                                        <span>{{$event->eventGroupStructure}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="{{'side-image side-image-' . $eventTierLower }} ">
                    <img class="side-image-absolute-top" src="{{  asset( '/assets/images/'. $eventTierLower . '.png' ) }}" width="180" height="125">
                </div>
            </div>
        </main>

        <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
        <script src="{{ asset('/assets/js/tab/tab.js') }}"></script>
        <!-- <script src="script.js"></script> -->