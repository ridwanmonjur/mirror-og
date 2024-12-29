<html>

<head>
    @include('googletagmanager::head')
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @include('__CommonPartials.HeadIcon')
</head>

<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main>
        <br>

        <div class="w-75 mx-auto">
            <div class="accordion accordion-flush" id="accordionExample">
                <div class="accordion-item border-0 pb-0 mb-0">
                    <h1 class="accordion-header " id="headingOne">
                        <div class="accordion-button pt-4 pb-2 rounded-pill border border-0 bg-white" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                            aria-controls="collapseOne">
                            <b>Account details and security </b>
                        </div>
                    </h1>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body border-0">
                            <div
                                class="d-flex flex-wrap  justify-content-between align-items-center border-top  botder-secondary py-3">
                                <div>
                                    <p class="py-0 my-0"> Email Address </p>
                                    <small class="text-primary">{{ $user->email }}</small>
                                </div>
                                <button class="btn btn-sm btn-white btn-size border-secondary py-2 px-3 rounded-pill"
                                    style="background: white;">
                                    Change Email Address
                                </button>
                            </div>
                            <div
                                class="d-flex flex-wrap  justify-content-between align-items-center border-top  botder-secondary py-3">
                                <div>
                                    <p class="py-0 my-0"> Password </p>
                                </div>
                                <button class="btn btn-sm btn-size text-light bg-secondary py-2 px-3 rounded-pill">
                                    Change Password
                                </button>
                            </div>
                            <div
                                class="d-flex flex-wrap  justify-content-between align-items-center border-top  botder-secondary py-3">
                                <div>
                                    <p class="py-0 my-0"> Account Recovery </p>
                                    <small class="text-primary">{{ $user->recovery_email }}</small>
                                </div>
                                <button class="btn btn-size btn-sm border-primary py-2 rounded-pill">
                                    Email Verification
                                </button>
                            </div>
                            <div
                                class="d-flex flex-wrap  justify-content-between align-items-center border-top  botder-secondary py-3">
                                <div>
                                    <p class="py-0 my-0"> Location </p>
                                    <small class="text-primary">{{ $user->recovery_email }}</small>
                                </div>
                                <button class="btn btn-white btn-size btn-sm border-primary py-2 rounded-pill">
                                    Change Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item accordion-flush border-0">
                    <h2 class="accordion-header pb-2 border-0" id="headingTwo">
                        <div class="accordion-button text-primary pt-2 pb-4 rounded-pill collapsed border-0 bg-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                            aria-controls="collapseTwo">
                            <b>Payment info</b>
                        </div>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo">
                        <div class="accordion-body border-0 py-0 pt-n2">
                            <!-- First nested accordion -->
                            <div class="accordion" id="nestedAccordion1">
                                <div class="accordion-item border-0">
                                    <h3 class="accordion-header  border-top border-1 botder-secondary" id="nestedHeading1">
                                        <div class="px-0 accordion-button border-0 collapsed  py-4 bg-white" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#nestedCollapse1"
                                            aria-expanded="false" aria-controls="nestedCollapse1">
                                            Credit Card Information
                                        </div>
                                    </h3>
                                    <div id="nestedCollapse1" class="accordion-collapse collapse"
                                        aria-labelledby="nestedHeading1" data-bs-parent="#nestedAccordion1">
                                        <div class="accordion-body border-0 py-0 px-0">
                                            <div
                                                class="d-flex flex-wrap  justify-content-between align-items-center px-0 pb-4">
                                                <div>
                                                    <p class="py-0 my-0">Card Details</p>
                                                    <small class="text-primary">**** **** **** 1234</small>
                                                </div>
                                                <button
                                                    class="btn btn-sm border-secondary btn-white btn-size py-2 px-3 rounded-pill">
                                                    Update Card
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Second nested accordion -->
                            <div class="accordion" id="nestedAccordion2">
                                <div class="accordion-item border-0">
                                    <h3 class="accordion-header " id="nestedHeading2">
                                        <div class="px-0 accordion-button border-top py-4 botder-secondary collapsed bg-white" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#nestedCollapse2"
                                            aria-expanded="false" aria-controls="nestedCollapse2">
                                            Billing Address
                                        </div>
                                    </h3>
                                    <div id="nestedCollapse2" class="accordion-collapse collapse"
                                        aria-labelledby="nestedHeading2" data-bs-parent="#nestedAccordion2">
                                        <div class="accordion-body border-0 py-0 px-0">
                                            <div
                                                class="d-flex flex-wrap  justify-content-between align-items-center py-3 pb-4">
                                                <div>
                                                    <p class="py-0 my-0">Current Address</p>
                                                    <small class="text-primary">123 Payment Street, City,
                                                        Country</small>
                                                </div>
                                                <button
                                                    class="btn btn-sm btn-white btn-size py-2 border border-secondary px-3 rounded-pill">
                                                    Update Address
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Third nested accordion -->
                            <div class="accordion pb-5 " id="nestedAccordion3">
                                <div class="accordion-item  ">
                                    <h3 class="accordion-header" id="nestedHeading3">
                                        <div class="px-0 accordion-button border-top  border-1 botder-secondary py-4 collapsed bg-white" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#nestedCollapse3"
                                            aria-expanded="false" aria-controls="nestedCollapse3">
                                            Payment History
                                        </div>
                                    </h3>
                                    <div id="nestedCollapse3" class="accordion-collapse collapse border-bottom border-1 botder-secondary"
                                        aria-labelledby="nestedHeading3" data-bs-parent="#nestedAccordion3">
                                        <div class="accordion-body border-0 py-0 px-0">
                                            <div
                                                class="d-flex flex-wrap  justify-content-between align-items-center py-3 pb-4">
                                                <div>
                                                    <p class="py-0 my-0">Recent Transactions</p>
                                                    <small class="text-primary">View your payment history</small>
                                                </div>
                                                <button
                                                    class="btn btn-sm btn-white btn-size border-secondary py-2 px-3 rounded-pill">
                                                    View Details
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>
<script src="{{ asset('/assets/js/shared/BackgroundModal.js') }}"></script>

<script src="{{ asset('/assets/js/chat.js') }}"></script>

</html>
