<html>

<head>
    @include('googletagmanager::head')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/settings.js'])
    @include('includes.HeadIcon')
</head>
@php
    use Carbon\Carbon;
 
@endphp

<body class="settings">
    @include('googletagmanager::body')
    @include('includes.Navbar')
    <main id="app" >
        <br>
        <input type="hidden" id="initialUserProfile" value="{{ json_encode($user) }}">
        <input type="hidden" id="wallet" value="{{ json_encode($wallet) }}">
        <input type="hidden" id="transactions-data" value="{{json_encode($transactions)}}">

        <div class="accordion-container mx-auto" v-scope="AccountComponent()">
            <div class="accordion accordion-flush " id="accordionExample">
                <div  class="accordion-item border-0 pb-0 mb-0" @vue:mounted="init">
                    <h1 class="accordion-header " id="headingOne">
                        <div class="accordion-button pb-4 rounded-pill border 
                            border-0 bg-white"
                            style="padding-top: 35px;" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <b>Account details and security </b>
                        </div>
                    </h1>
                    <div id="collapseOne" class="accordion-collapse collapse {{ !$isShowNextAccordion ? 'show' : '' }}"
                        aria-labelledby="headingOne">
                        <div class="accordion-body border-0">
                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Email Address </p>
                                    <small class="text-primary" v-text="emailAddress"></small>
                                </div>
                                <button v-on:click="changeEmailAddress(event)"
                                    data-route="{{ route('user.settings.action') }}"
                                    data-event-type="{{ $settingsAction['CHANGE_EMAIL']['key'] }}"
                                    class="btn btn-sm btn-white btn-size border-secondary py-2 px-3 rounded-pill"
                                    style="background: white;">
                                    Change Email Address
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
                                    data-route="{{ route('user.settings.action') }}"
                                    data-event-type="{{ $settingsAction['CHANGE_RECOVERY_EMAIL']['key'] }}">
                                    Change Recovery Email
                                </button>
                            </div>
                            
                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Password </p>
                                </div>
                                <button v-on:click="changePassword(event)"
                                    dat-is-password-null="{{ $user->is_null_password }}"
                                    data-route="{{ route('user.settings.action') }}"
                                    data-event-one-type="{{ $settingsAction['COMPARE_PASSWORD']['key'] }}"
                                    data-event-two-type="{{ $settingsAction['CHANGE_PASSWORD']['key'] }}"
                                    class="btn btn-sm btn-size text-light bg-secondary py-2 px-3 rounded-pill">
                                    Change Password
                                </button>
                            </div>
                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Account Type</p>
                                    <small class="text-secondary fw-bold text-capitalize"><u>{{strtolower($user->role)}}</u></small>
                                </div>
                              
                            </div>
                             <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Account Creation </p>
                                    <small class="text-secondary fw-bold text-capitalize">{{$user->createdIsoFormat()}}</small>
                                </div>
                                <div></div>
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
                <div class="accordion-item accordion-flush mb-4 border-0">
                    <h2 class="accordion-header pb-2 border-0" id="headingTwo">
                        <div class="accordion-button  pt-4 pb-4 rounded-pill {{ $isShowNextAccordion ? '' : 'collapsed' }} border-0 bg-white"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                            aria-controls="collapseTwo">
                            <b>Payment info</b>
                        </div>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse {{ $isShowNextAccordion ? 'show' : '' }}"
                        aria-labelledby="headingTwo">
                        <div class="accordion-body border-0 py-0 pt-n2">

                            <div
                                class="d-grid  flex-wrap  justify-content-between align-items-center border-top   py-3">
                                <div>
                                    <p class="py-0 my-0"> Linked Bank Account </p>
                                    <small v-if="wallet.has_bank_account" class="text-primary text-capitalize">
                                        <span v-text="wallet.bank_name"  ></span>
                                        ****
                                        <span v-text="wallet.bank_last4"  ></span>
                                    </small>
                                    <small v-else>
                                        No linked account
                                    </small>
                                </div>
                                <button v-if="wallet.has_bank_account" v-on:click="unlinkBankAccount(event)"
                                    data-route="{{ route('wallet.unlink') }}"
                                    class="btn btn-sm btn-size text-light bg-red py-2 px-3 rounded-pill">
                                    Unlink
                                </button>
                                <a href="{{route('wallet.payment-method')}}" v-else>
                                    <button 
                                    class="btn btn-sm btn-size text-dark bg-success py-2 px-3 rounded-pill">
                                    Link Bank Account
                                </button>
                                </a>
                            </div>
                            
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
                                            <div class="px-0 accordion-button border-top py-4  {{ $isShowSecondInnerAccordion ? 'collapsed' : '' }} bg-white"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#nestedCollapse2" aria-expanded="false"
                                                aria-controls="nestedCollapse2">
                                                Payment Methods
                                            </div>
                                        </h3>
                                        <div id="nestedCollapse2"
                                            class="accordion-collapse collapse {{ $isShowSecondInnerAccordion ? '' : 'show' }}"
                                            aria-labelledby="nestedHeading2" data-bs-parent="#nestedAccordion2">
                                            <div class="accordion-body border-0 py-0 px-0">
                                                @if ($paymentMethods->first() != null)
                                                    <div class=" ">
                                                        <div class=" ms-2">
                                                            <table class="table transaction-table table-sm responsive table-borderless"
                                                                id="payment-methods-table">
                                                                <thead class="border-bottom border-secondary">
                                                                    <tr>
                                                                        <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">#</th>
                                                                        <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">Card Type</th>
                                                                        <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">Last 4</th>
                                                                        <th scope="col" class="transaction-table__header-cell bg-secondary text-white py-3">Expiry</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($paymentMethods as $method)
                                                                        @if ($loop->index <= $limit_methods - 1)
                                                                            <tr>
                                                                                <td style="width: 50px !important;" class="transaction-row__cell transaction-row__cell--type py-3">
                                                                                    {{ $loop->index + 1 }}
                                                                                </td>
                                                                                <td class="transaction-row__cell transaction-row__cell-- py-3">
                                                                                    <div
                                                                                    
                                                                                        class="d-flex align-items-center">
                                                                                        <svg width="32" height='25' version="1.0" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="me-2" viewBox="0 0 24 24" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Guides"> <g id="_x32_0_px_2_"> </g> <g id="_x32_0px"> </g> <g id="_x34_0px"> </g> <g id="_x34_4_px"> </g> <g id="_x34_8px"> <g id="_x31_6px"> </g> <g id="square_4px"> <g id="_x32_8_px"> <g id="square_4px_2_"> </g> <g id="square_4px_3_"> </g> <g id="square_4px_1_"> </g> <g id="_x32_4_px_2_"> </g> <g id="_x31_2_px"> </g> </g> </g> </g> <g id="Icons"> </g> <g id="_x32_0_px"> </g> <g id="square_6px"> <g id="_x31_2_PX"> </g> </g> <g id="_x33_6_px"> <g id="_x33_2_px"> <g id="_x32_8_px_1_"> <g id="square_6px_1_"> </g> <g id="_x32_0_px_1_"> <g id="_x31_2_PX_2_"> </g> <g id="_x34_8_px"> <g id="_x32_4_px"> </g> <g id="_x32_4_px_1_"> </g> </g> </g> </g> </g> </g> <g id="_x32_0_px_3_"> </g> <g id="_x32_0_px_4_"> </g> <g id="New_Symbol_8"> <g id="_x32_4_px_3_"> </g> </g> </g> <g id="Artboard"> </g> <g id="Free_Icons"> <g> <polygon style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" points="22.5,19.5 0.5,19.5 1.5,4.5 23.5,4.5 "></polygon> <line style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="4" y1="15.5" x2="6" y2="15.5"></line> <line style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="8" y1="15.5" x2="13" y2="15.5"></line> <line style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="1.3" y1="7.5" x2="23.285" y2="7.5"></line> <line style="fill:none;stroke:#000000;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" x1="1.1" y1="10.5" x2="23.1" y2="10.5"></line> </g> </g> </g></svg>
                                                                                        {{ ucfirst($method->brand) }}
                                                                                    </div>
                                                                                </td>
                                                                                <td class="transaction-row__cell transaction-row__cell--type py-3">**** {{ $method->last4 }} 
                                                                                    @if ($method->is_default) 
                                                                                        <small class="bg-primary text-white ms-2 px-1 py-0 rounded rounded-3 "> Default </small>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="transaction-row__cell transaction-row__cell--type py-3">{{ $method->exp_month }}/{{ $method->exp_year }}
                                                                                </td>
                                                                               
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>

                                                            @if ($hasMorePayments)
                                                                <div
                                                                    class="d-flex justify-content-start ps-3 mt-1 mb-3">
                                                                    <form method="GET" action="">
                                                                        @csrf
                                                                        <input type="hidden" name="methods_limit"
                                                                            value="{{ $limit_methods + 10 }}">
                                                                        <button type="submit"
                                                                            class="btn btn-primary btn-sm text-light px-3  rounded-pill"><b>Load
                                                                                More</b></button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>
                                                        <p class="my-2 pt-2 pb-5"> <i> You have no payment methods up
                                                                till now... </i> </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                

                                <!-- Third nested accordion -->
                                {{-- <div class="accordion  " id="nestedAccordion3">
                                    <div class="accordion-item  ">
                                        <h3 class="accordion-header" id="nestedHeading3">
                                            <div class="px-0 accordion-button border-top  border-1  py-4 {{ $isShowSecondInnerAccordion ? '' : 'collapsed' }} bg-white"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#nestedCollapse3" aria-expanded="false"
                                                aria-controls="nestedCollapse3">
                                                Payment History
                                            </div>
                                        </h3>
                                        <div id="nestedCollapse3"
                                            class="accordion-collapse collapse {{ $isShowSecondInnerAccordion ? 'show' : '' }} border-bottom border-1 "
                                            aria-labelledby="nestedHeading3" data-bs-parent="#nestedAccordion3">
                                            <div class="accordion-body border-0 py-0 px-0">
                                                @if ($paymentHistory->first() != null)
                                                    <div class=" ">
                                                        <div class=" ms-2">
                                                            <table class="table table-sm responsive table-borderless"
                                                                id="payment-methods-table">
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
                                                                        @if ($loop->index <= $limit_history - 1)
                                                                            <tr>
                                                                                <td>
                                                                                    {{ $loop->index + 1 }}
                                                                                </td>

                                                                                <td>
                                                                                    {{ $history->amount }}
                                                                                </td>
                                                                                <td>{{ strtoupper($history->currency) }}
                                                                                </td>
                                                                                <td>{{ $history->status }}</td>
                                                                                <td>
                                                                                    {{ Carbon::createFromTimestamp($history->created_at)->format('Y-m-d') }}
                                                                                    <span
                                                                                        class="badge ms-2 bg-secondary badge-size">
                                                                                        <span>
                                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                                width="16"
                                                                                                height="16"
                                                                                                fill="currentColor"
                                                                                                class="bi bi-clock"
                                                                                                viewBox="0 0 16 16">
                                                                                                <path
                                                                                                    d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                                                                                <path
                                                                                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
                                                                                            </svg>
                                                                                        </span>
                                                                                        {{ Carbon::createFromTimestamp($history->created_at)->format('g:i A') }}
                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        @endif
                                                                    @endforeach
                                                                </tbody>
                                                            </table>

                                                            @if ($hasMoreHistory)
                                                                <div
                                                                    class="d-flex justify-content-start ps-3 mt-1 mb-3">
                                                                    <form method="GET" action="">
                                                                        @csrf
                                                                        <input type="hidden" name="history_limit"
                                                                            value="{{ $limit_history + 10 }}">
                                                                        <button type="submit"
                                                                            class="btn btn-primary btn-sm text-light px-3  rounded-pill"><b>Load
                                                                                More</b></button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>
                                                        <p class="my-2 pt-2 pb-5"> <i> You have no history up till
                                                                now... </i> </p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}


                                <div class="accordion pb-5 " v-scope="TransactionComponent()" @vue:mounted="init" id="nestedAccordion4">
                                    <div class="accordion-item  ">
                                        <h3 class="accordion-header" id="nestedHeading4">
                                            <div class="px-0 accordion-button border-top collapsed border-1  py-4  bg-white"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#nestedCollapse4" aria-expanded="false"
                                                aria-controls="nestedCollapse4">
                                                Transaction History
                                            </div>
                                        </h3>
                                        <div id="nestedCollapse4"
                                            class="accordion-collapse collapse  border-bottom border-1 "
                                            aria-labelledby="nestedHeading4" data-bs-parent="#nestedAccordion4">
                                            <div class="accordion-body border-0 py-0 px-0">
                                                <div class="my-3">
                                                    <x-wallet.transanction-list  />

                                                    <div v-else>
                                                        <p class="my-2 pt-2 pb-5"> <i> You have no transactions up till
                                                                now... </i> </p>
                                                    </div>

                                                    <div v-if="hasMore" class="text-center  my-4" v-cloak>
                                                        <button v-on:click="loadMore" :disabled="loading"
                                                            class="btn text-light rounded-pill btn-primary">
                                                            <span v-if="loading" v-cloak class="spinner-border spinner-border-sm me-2"></span>
                                                            <span v-if="loading" v-cloak>Loading...</span>
                                                            <span v-else>Load More</span>
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
            </div>
    </main>
</body>


</html>
