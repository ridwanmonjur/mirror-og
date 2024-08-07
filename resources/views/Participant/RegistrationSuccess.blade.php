@include('Organizer.__Partials.CreateEventHeadTag')

<body>
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <div>
            <div>
                <br>
                <div class="time-line-box" id="timeline-box">
                    <div class="swiper-container text-center">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide swiper-slide__left" id="timeline-1">
                                <div class="timestamp" onclick="errorToast();"><span
                                        class="cat">Select Team</span></div>
                                <div class="status__left" onclick="errorToast();">
                                    <span><small></small></span></div>
                            </div>
                            <div class="swiper-slide" id="timeline-2">
                                <div class="timestamp" onclick="errorToast();">
                                    <span>Manage Members</span></div>
                                <div class="status" onclick="errorToast();">
                                    <span><small></small></span></div>
                            </div>
                            <div class="swiper-slide" id="timeline-launch">
                                <div class="timestamp" onclick="errorToast();">
                                    <span class="date">Manage Roster</span></div>
                                <div class="status" onclick="errorToast();">
                                    <span><small></small></span></div>
                            </div>
                            <div class="swiper-slide swiper-slide__right" id="timeline-payment">
                                <div class="timestamp"
                                    onclick="errorToast();">
                                    <span>Manage Registration</span></div>
                                <div class="status__right"
                                    onclick="errorToast();">
                                    <span><small></small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="breadcrumb-top">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    onclick="errorToast();">Select Team</a></li>
                            <li class="breadcrumb-item"><a onclick="errorToast();">Manage Members</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    onclick="errorToast();">Manage Roster</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    onclick="errorToast();">Manage Registration</a></li>
                        </ol>
                    </nav>
                </div>

                @include('Organizer.__CreateEditPartials.CreateEventSuccess', ['event' => $event])
            </div>
        </div>
        <br><br>
    </main>
    <script>
        function errorToast( ) {
            window.toastError('Cannot go back now. Edit from your team list.');
        }
    </script>
    <script src="{{ asset('/assets/js/navbar/toggleNavbar.js') }}"></script>
</body>
