<html>

<head>
    <meta name="page-component" content="settings">
    @include('googletagmanager::head')
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @include('__CommonPartials.HeadIcon')
</head>
@php
    use Carbon\Carbon;
@endphp
<body>
    @include('googletagmanager::body')
    @include('__CommonPartials.NavbarGoToSearchPage')
    <main id="app">
        <br>
        <input type="hidden" id="initialUserProfile" value="{{ json_encode($user) }}">

        <div class="w-75 mx-auto">
            <div class="accordion accordion-flush " id="accordionExample">
                <div v-scope="AccountComponent()" class="accordion-item border-0 pb-0 mb-0">
                    <h1 class="accordion-header " id="headingOne">
                        <div class="accordion-button pb-4 rounded-pill border 
                            zcollapsed 
                            border-0 bg-white"
                            style="padding-top: 35px;"
                            type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true"
                            aria-controls="collapseOne">
                            <b>Account details and security </b>
                        </div>
                    </h1>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                        <div class="accordion-body border-0">
                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Email Address </p>
                                    <small class="text-primary" v-text="emailAddress"></small>
                                </div>
                                <button v-on:click="changeEmailAddress(event)"
                                    data-route="{{route('user.settings.action')}}"
                                    data-event-type="{{ $settingsAction['CHANGE_EMAIL']['key'] }}"
                                    class="btn btn-sm btn-white btn-size border-secondary py-2 px-3 rounded-pill"
                                    style="background: white;">
                                    Change Email Address
                                </button>
                            </div>
                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Password </p>
                                </div>
                                <button v-on:click="changePassword(event)"
                                    dat-is-password-null="{{$user->is_null_password}}"
                                    data-route="{{route('user.settings.action')}}"
                                    data-event-one-type="{{ $settingsAction['COMPARE_PASSWORD']['key'] }}"
                                    data-event-two-type="{{ $settingsAction['CHANGE_PASSWORD']['key'] }}"
                                    class="btn btn-sm btn-size text-light bg-secondary py-2 px-3 rounded-pill">
                                    Change Password
                                </button>
                            </div>
                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Account Recovery </p>
                                    <small class="text-primary" v-text="recoveryAddress"></small>
                                </div>
                                <button v-on:click="changeRecoveryEmailAddress(event)"
                                    class="btn btn-size btn-sm border-primary py-2 rounded-pill"
                                    data-route="{{route('user.settings.action')}}"
                                    data-event-type="{{ $settingsAction['CHANGE_RECOVERY_EMAIL']['key'] }}"
                                >
                                    Change Recovery Email
                                </button>
                            </div>
                            {{-- <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Location </p>
                                    <small class="text-primary">{{ $user->recovery_email }}</small>
                                </div>
                                <button class="btn btn-white btn-size btn-sm border-primary py-2 rounded-pill">
                                    Change Location
                                </button>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="accordion-item accordion-flush border-0">
                    <h2 class="accordion-header pb-2 border-0" id="headingTwo">
                        <div class="accordion-button  pt-4 pb-4 rounded-pill zcollapsed border-0 bg-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                            aria-controls="collapseTwo">
                            <b>Payment info</b>
                        </div>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo">
                        <div class="accordion-body border-0 py-0 pt-n2">
                            <!-- First nested accordion -->
                            <div class="accordion" id="nestedAccordion1">
                                {{-- <div class="accordion-item border-0">
                                    <h3 class="accordion-header  border-top border-1 "
                                        id="nestedHeading1">
                                        <div class="px-0 accordion-button border-0 collapsed show py-4 bg-white"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#nestedCollapse1"
                                            aria-expanded="false" aria-controls="nestedCollapse1">
                                            Credit Card Information
                                        </div>
                                    </h3>
                                    <div id="nestedCollapse1" class="accordion-collapse collapse"
                                        aria-labelledby="nestedHeading1" data-bs-parent="#nestedAccordion1">
                                        <div class="accordion-body border-0 py-0 px-0">
                                            <div
                                                class="d-grid  flex-wrap  justify-content-between align-items-center px-0 pb-4">
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
                            </div> --}}

                            <!-- Second nested accordion -->
                            <div class="accordion" id="nestedAccordion2">
                                <div class="accordion-item border-0">
                                    <h3 class="accordion-header " id="nestedHeading2">
                                        <div class="px-0 accordion-button border-top py-4  collapsed bg-white"
                                            type="button" data-bs-toggle="collapse" data-bs-target="#nestedCollapse2"
                                            aria-expanded="false" aria-controls="nestedCollapse2">
                                            Payment Methods
                                        </div>
                                    </h3>
                                    <div id="nestedCollapse2" class="accordion-collapse collapse"
                                        aria-labelledby="nestedHeading2" data-bs-parent="#nestedAccordion2">
                                        <div class="accordion-body border-0 py-0 px-0">
                                            @if ($paymentMethods->first() != null)
                                                <div
                                                    class=" ">
                                                    <div class=" ms-2">
                                                        <table class="table table-sm responsive table-borderless" id="payment-methods-table">
                                                            <thead class="border-bottom border-secondary">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Card Type</th>
                                                                    <th>Last 4</th>
                                                                    <th>Expiry</th>
                                                                    <th>Date</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($paymentMethods as $method)
                                                                    @if ($loop->index !== $limit_methods)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $loop->index + 1 }}
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex align-items-center">
                                                                                    <img src="{{asset('/assets/images/logo/'. strtolower($method->card->brand). '-card-logo.jpg')}}"
                                                                                        alt="{{ $method->card->brand }}"
                                                                                        class="me-2"
                                                                                        style="width: 32px; height: 20px;">
                                                                                    {{ ucfirst($method->card->brand) }}
                                                                                </div>
                                                                            </td>
                                                                            <td>**** {{ $method->card->last4 }}</td>
                                                                            <td>{{ $method->card->exp_month }}/{{ $method->card->exp_year }}</td>
                                                                            <td>
                                                                                
                                                                                {{ Carbon::createFromTimestamp($method->created)->format('Y-m-d') }}
                                                                                <span class="badge ms-2 bg-secondary badge-size">
                                                                                    <span>
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                                                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                                                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                                                                                        </svg>
                                                                                    </span>
                                                                                    {{ Carbon::createFromTimestamp($method->created)->format('g:i A') }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                        @if ($hasMorePayments)
                                                            <div class="d-flex justify-content-start ps-3 mt-1 mb-3">
                                                                <form  method="GET" action="">
                                                                    @csrf
                                                                    <input type="hidden" name="methods_limit"
                                                                        value="{{ $limit_methods + 10 }}">
                                                                    <button type="submit" class="btn btn-primary btn-sm text-light px-3  rounded-pill"><b>Load
                                                                        More</b></button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <p class="my-2 pt-2 pb-5"> <i> You have no payment methods up till now... </i> </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Third nested accordion -->
                            <div class="accordion pb-5 " id="nestedAccordion3">
                                <div class="accordion-item  ">
                                    <h3 class="accordion-header" id="nestedHeading3">
                                        <div class="px-0 accordion-button border-top  border-1  py-4 collapsed bg-white"
                                            type="button" data-bs-toggle="collapse"
                                            data-bs-target="#nestedCollapse3" aria-expanded="false"
                                            aria-controls="nestedCollapse3">
                                            Payment History
                                        </div>
                                    </h3>
                                    <div id="nestedCollapse3"
                                        class="accordion-collapse collapse border-bottom border-1 "
                                        aria-labelledby="nestedHeading3" data-bs-parent="#nestedAccordion3">
                                        <div class="accordion-body border-0 py-0 px-0">
                                            @if ($paymentHistory->first() != null)
                                                <div
                                                    class=" ">
                                                    <div class=" ms-2">
                                                        <table class="table table-sm responsive table-borderless" id="payment-methods-table">
                                                            <thead class="border-bottom border-secondary">
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Amount</th>
                                                                    <th>Currency</th>
                                                                    <th>Status</th>
                                                                    <th>Date</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($paymentHistory as $history)
                                                                    @if ($loop->index !== $limit_history)
                                                                        <tr>
                                                                            <td>
                                                                                {{ $loop->index + 1 }}
                                                                            </td>
                                                                           
                                                                            <td>
                                                                                {{ $history->amount/100 }}
                                                                            </td>
                                                                            <td>{{ strtoupper($history->currency) }}</td>
                                                                            <td>{{ $history->status }}</td>
                                                                           <td>
                                                                                {{ Carbon::createFromTimestamp($history->created)->format('Y-m-d') }}
                                                                                <span class="badge ms-2 bg-secondary badge-size">
                                                                                    <span>
                                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                                                                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                                                                                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                                                                                        </svg>
                                                                                    </span>
                                                                                    {{ Carbon::createFromTimestamp($history->created)->format('g:i A') }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                            </tbody>
                                                        </table>

                                                        @if ($hasMoreHistory)
                                                            <div class="d-flex justify-content-start ps-2 mt-1 mb-3">

                                                                @if ($page_history - 1 >= 1)
                                                                    <form  method="GET" action="" class="me-3">
                                                                        @csrf
                                                                        <input type="hidden" name="history_page"
                                                                            value="{{ $page_history - 1 }}">
                                                                        <input type="hidden" name="page_next"
                                                                            value="{{ null }}">
                                                                        <button type="submit" class="btn btn-primary btn-sm btn-sm-size text-light px-3 mt-1 rounded-pill"><b>
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
  [                                                                          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                                                                            </svg> First Page
                                                                        </b></button>
                                                                    </form>
                                                                @endif
                                                                                                                                <p class="py-0 mt-0 d-inline mb-2 mt-2 me-3 text-success">Showing Page {{$page_history}}, items {{$count_history}} to {{$count_history + $limit_history}}.</p>

                                                                <form  method="GET" action="">
                                                                    @csrf
                                                                    <input type="hidden" name="history_page"
                                                                        value="{{ $page_history + 1 }}">
                                                                    <input type="hidden" name="page_next"
                                                                        value="{{ $paymentHistory->next_page }}">
                                                                    <button type="submit" class="btn btn-primary btn-sm btn-sm-size text-light px-3 mt-1 rounded-pill"><b>
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
                                                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                                                                    </svg>
                                                                    
                                                                    Next</b></button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <p class="my-2 pt-2 pb-5"> <i> You have no history up till now... </i> </p>
                                                </div>
                                            @endif
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


</html>
