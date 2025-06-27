@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/settings.js'])
</head>
@section('body-class', 'wallet')

@section('content')
    @include('includes.Navbar')

    <main class=" "  v-scope="TransactionComponent()" class="row" @vue:mounted="init">
        <input type="hidden" id="transactions-data" value="{{json_encode($transactions)}}">
        <div class="row my-2 px-5 py-2"> 
            <h3 class="col-12 col-md-6 my-2 py-0 text-start">My Wallet</h3>
            <h5 
                style="z-index: 99;"
                id="main-nav" onclick="openTab('wallet-main');" class="cursor-pointer my-2 d-none py-0 position-relative col-12 col-md-6 text-start text-md-end">
                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                    </svg>
                </span>
                <span>Go back</span>
            </h5>
        </div>
        <div class="row container-main " id="wallet-main">
            <div class="col-12 px-0 col-xl-5">
                <div class="card my-2 mx-2 py-0 border border-3 border-primary  rounded-30px ">
                    <div class="card-body">
                        <h5 class="my-3 text-secondary">Current Balance </h5>
                        <h2 class="text-primary my-3"> RM {{ number_format($wallet->current_balance, 2) }} </h2>
                        <div class="my-2">
                            <button type="button"
                                class="btn d-inline-block me-2 my-1 rounded-pill btn-primary  text-light "
                                onclick="openTab('wallet-add-fund')">
                                <small>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path
                                            d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                    </svg>
                                </small>
                                <small> Add funds </small>
                            </button>
                            <button type="button" class="btn rounded-pill my-1 btn-secondary text-light "
                                onclick="openTab('wallet-withdraw-fund')">
                                <small>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        fill="currentColor" class="bi bi-question-circle ms-2" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path
                                            d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286m1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94" />
                                    </svg>
                                </small>
                                <small>Request a withdrawal </small>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card my-2  mx-2 py-1 border border-2 border-secondary rounded-30px">
                    <div class="card-body">
                        <div class="row text-secondary">
                            <p class="col-6 text-start">Coupons expiring soon</p>
                            {{-- ahref --}}
                            <a href="{{route('wallet.coupons')}}" class="col-6 text-end cursor-pointer">
                                <span class="text-secondary">View all my coupons</span>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                    </svg>
                                </span>
                            </a>
                           
                            <div class="row  p-0">
                                @if (isset($demoCoupons[0]))
                                    @foreach ($demoCoupons as $coupon)
                                        <x-wallet.coupon-card :coupon="$coupon" :className="'col-12'" />
                                    @endforeach
                                @else
                                    <x-wallet.no-list :text="'No coupons available yet!'" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 my-2 col-xl-7 px-0">
                <div class="card mx-2 py-0 border border-2 border-secondary rounded-30px">
                    <div class="card-body px-0 py-0">
                        <div class="container-fluid px-0 py-0">
                            <div class="transaction-history">
                                <div class="row py-0 px-3 my-0">
                                    <h3 class="transaction-history__title  col-12 col-lg-6 text-secondary">Most recent transactions
                                    </h3>
                                    <a href="{{route('wallet.transactions')}}" class="col-12 col-lg-6 text-secondary text-start text-lg-end">
                                        View full <span class="d-none d-lg-inline">transaction</span> history
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </span>
                                    </a>
                                </div>

                                <div class="table-responsive my-2">
                                    <table class="transaction-history__table table "
                                        v-cloak 
                                        v-if="transactions && transactions[0]">
                                        <thead class="transaction-table__header">
                                            <tr>
                                                <th scope="col"
                                                    class="transaction-table__header-cell bg-secondary text-white py-3">Date
                                                </th>
                                                <th scope="col"
                                                    class="transaction-table__header-cell bg-secondary text-white py-3">
                                                    Transaction</th>
                                                <th scope="col"
                                                    class="transaction-table__header-cell bg-secondary text-white py-3">Type
                                                </th>
                                                <th scope="col"
                                                    class="transaction-table__header-cell bg-secondary text-white py-3">
                                                    Total
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="transaction in transactions" :key="transaction.id"
                                                class="transaction-row">
                                                <x-wallet.transaction-item :fullPage="false" />
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div v-else>
                                        <x-wallet.no-list :text="'No transactions yet!'" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none mx-auto px-0 container-main " id="wallet-add-fund">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-95-lg-50  rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class=" px-2 py-2">
                        <div class=" px-1 py-0 my-0">
                            <h5 class="text-center mt-2 mb-3 text-secondary">Add funds to your wallet</h5>

                        </div>
                        <div class="mt-3">
                            <div class="text-center mx-auto my-3 py-3 text-light bg-primary rounded-30px"
                                style="width: min(300px, 80%);">
                                <h5 class="mt-1 mb-3 fw-normal">Current Wallet Balance </h5>
                                <h2 class=" my-3 fw-normal"> RM {{ number_format($wallet->current_balance, 2) }} </h2>
                            </div>
                            
                            <!-- Topup Form -->
                            <div class="my-2">
                                <form action="{{ route('wallet.checkout') }}" method="POST">
                                    @csrf
                                    <div class="text-center mb-4">
                                        <label for="topup_amount">Please enter an amount to add </label>
                                        <div class=" input-group  d-flex justify-content-center mx-auto ">
                                            <button class="btn mx-auto pe-none btn-outline-secondary my-2 me-0 py-0"
                                                type="button" id="button-addon1">RM </button>
                                            <input type="number" id="topup_amount" name="topup_amount"
                                                class="d-inline mx-auto my-2 border-secondary ms-0 form-control"
                                                min="5" step="0.01" required value="5.00"
                                                style="width: min-content; max-width: 200px;">
                                        </div>
                                        <small class="text-muted fst-italic ">Minimum amount is RM 5.00</small>
                                    </div>
                                    <div class="d-flex justify-content-around w-75 my-4 mx-auto">
                                        <button onclick="fillInput('topup_amount', 10)" type="button"
                                            class="btn BG-secondary text-light rounded-pill">
                                            10 RM
                                        </button>

                                        <button onclick="fillInput('topup_amount', 25)" type="button"
                                            class="btn BG-secondary text-light rounded-pill">
                                            25 RM
                                        </button>

                                        <button onclick="fillInput('topup_amount', 50)" type="button"
                                            class="btn BG-secondary text-light rounded-pill">
                                            50 RM
                                        </button>

                                    </div>
                                    <div class="d-flex justify-content-around w-75 mb-4 mx-auto">
                                        <button onclick="openTab('wallet-main')" type="button"
                                            class="btn border-secondary text-dark rounded-pill px-4 py-2">
                                            Cancel
                                        </button>

                                        <button type="submit"
                                            class="btn btn-primary  text-light rounded-pill px-4 py-2">Next</button>

                                    </div>

                                </form>
                            </div>


                        </div>

                        @if ($wallet->last_payout_at)
                            <div class="my-3">
                                <p>Last withdrawal: {{ $wallet->last_payout_at->format('d M y, g:i a') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none mx-auto px-0 container-main " id="wallet-withdraw-fund">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary my-0 w-95-lg-50 rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class=" px-2 py-1">
                        <div class=" px-1 py-0 my-0">
                            <h5 class="text-center mt-2 mb-3 text-secondary">Withdraw funds from your wallet</h5>

                        </div>
                        <div class="mt-3">
                            <div class="text-center mx-auto my-2 py-2 text-light bg-secondary rounded-30px"
                                style="width: min(300px, 80%);">
                                <h5 class="my-1  fw-normal">Current Wallet Balance </h5>
                                <h4 class=" my-1 fw-normal"> RM {{ number_format($wallet->current_balance, 2) }} </h4>
                            </div>
                            <div class="text-center mx-auto">
                                <svg width="50px" height="50px" fill="#000000" viewBox="0 0 24 24" id="down-direction" data-name="Flat Color" xmlns="http://www.w3.org/2000/svg" class="icon flat-color"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path id="primary" d="M20.76,13.81l-2.6-3a1,1,0,0,0-1.41-.11L15,12.16V4a2,2,0,0,0-2-2H11A2,2,0,0,0,9,4v8.16l-1.75-1.5a1,1,0,0,0-1.41.11l-2.6,3a1,1,0,0,0,.11,1.41l7.35,6.3a2,2,0,0,0,2.6,0l7.35-6.3A1,1,0,0,0,20.76,13.81Z" style="fill: #a6a6a6;"></path></g></svg>
                            </div>
                            <div class="text-center mx-auto my-2 py-2 text-light bg-primary rounded-30px"
                                style="width: min(300px, 80%);">
                                <h5 class="my-1  fw-normal">New Wallet Balance </h5>
                                <h4 class=" my-1 fw-normal"> RM {{ number_format($wallet->current_balance, 2) }} </h4>
                            </div>
                            <div id="withdraw-status" class="text-center  d-none mx-auto">
                            </div>

                            <!-- Topup Form -->
                            <div class="my-2">
                                <form id="withdrawal-form" action="{{ route('wallet.withdraw') }}" method="POST">
                                    @csrf
                                    <div class="text-center mb-4">
                                        <label for="topup_amount">Please enter your amount to withdraw </label>
                                        <div class=" input-group  d-flex justify-content-center mx-auto ">
                                            <button class="btn mx-auto pe-none btn-outline-secondary my-2 me-0 py-0"
                                                type="button" id="button-addon1">RM </button>
                                            <input type="number" id="topup_amount" name="topup_amount"
                                                class="d-inline mx-auto my-2 border-secondary ms-0 form-control"
                                                min="5" step="0.01" required value="5.00"
                                                style="width: min-content; max-width: 200px;">
                                        </div>
                                        <small class="text-muted fst-italic ">Minimum amount is RM 5.00</small>
                                        @if ($wallet->has_bank_account) 
                                            <div class="text-center text-primary fst-italic mt-1 mb-2">Linked bank account: {{$wallet->bank_name}} **** {{$wallet->bank_last4}}</div>
                                        @else
                                            <div class="text-center mt-1 mb-2">No linked bank account</div>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-around w-75 mt-2 mb-4 mx-auto">
                                        <button onclick="openTab('wallet-main')" type="button"
                                            class="btn border-secondary text-dark rounded-pill px-4 py-2">
                                            Cancel
                                        </button>
                                        <button type="submit"
                                            class="btn btn-primary withdraw-button text-light rounded-pill px-4 py-2">Yes, request withdrawal</button>
                                    </div>
                                    <small class="fst-italic text-primary w-75 d-block text-center mx-auto">
                                        Please note that your withdrawal request will take seven (7) business days 
                                        to process. If you do not receive your funds after that period, you can ping 
                                        our support mains at supportmain@driftwood to check your request.
                                    </small>
                                    
                                </form>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <br>
        </div>


        <div class="d-none mx-auto px-0 container-main min-h-85vh" id="wallet-view-coupons">
            <div class="center-container mx-auto">
                <div class="card px-3 py-2 border border-2 mx-auto border-secondary  min-w-95vw rounded-30px">
                    <div class="card-body px-2 py-2">
                        <div class="row ">
                            <h3 class="col-6 text-start transaction-history__title ">My Coupons</h3>
                            <div class="col-6 text-end">
                                <a type="button" 
                                    href="{{route('wallet.coupons', ['empty' => 1 ]) }}"
                                    class="btn rounded-pill border-dark text-dark">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-ticket-perforated" viewBox="0 0 16 16">
                                        <path d="M4 4.85v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9zm-7 1.8v.9h1v-.9zm7 0v.9h1v-.9z"/>
                                        <path d="M1.5 3A1.5 1.5 0 0 0 0 4.5V6a.5.5 0 0 0 .5.5 1.5 1.5 0 1 1 0 3 .5.5 0 0 0-.5.5v1.5A1.5 1.5 0 0 0 1.5 13h13a1.5 1.5 0 0 0 1.5-1.5V10a.5.5 0 0 0-.5-.5 1.5 1.5 0 0 1 0-3A.5.5 0 0 0 16 6V4.5A1.5 1.5 0 0 0 14.5 3zM1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v1.05a2.5 2.5 0 0 0 0 4.9v1.05a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-1.05a2.5 2.5 0 0 0 0-4.9z"/>
                                        </svg>
                                    </span>
                                    Redeem coupons
                                </a>
                            </div>
                           
                        </div>
                        <div class="px-0 pt-4 pb-4 row">
                            @if (isset($coupons[0]))
                                @foreach ($coupons as $coupon)
                                    <x-wallet.coupon-card :coupon="$coupon" :className="' col-lg-6  col-xl-4 '" />
                                @endforeach
                            @else
                                <x-wallet.no-list :text="'No coupons available!'" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <input type="hidden" id="wallet" value="{{ json_encode($wallet) }}">

    </main>


@endsection
