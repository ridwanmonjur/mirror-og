@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/js/alpine/settings.js'])
</head>
@section('content')
    @include('includes.Navbar.NavbarGoToSearchPage')

    <main class="wallet" v-scope="TransactionComponent()" class="row" @vue:mounted="init">
        <input type="hidden" id="transactions-data" value='@json($transactions)'>

        <div class="row container-main mx-auto" id="wallet-main">
            <div class="col-12 col-xl-5">
                <div class="card mb-2  py-1 border border-3 border-primary  rounded-30px ">
                    <div class="card-body">
                        <h5 class="mt-1 mb-3 text-secondary">Current Balance </h5>
                        <h2 class="text-primary my-3"> MYR {{ number_format($wallet->usable_balance, 2) }} </h2>
                        <div class="mb-2">
                            <button type="button"
                                class="btn d-inline-block me-2 mb-1 rounded-pill btn-primary  text-light "
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
                            <button type="button" class="btn rounded-pill mb-1 btn-secondary text-light "
                                onclick="openTab('wallet-withdraw-fund')">
                                <small>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                        fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
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
                <div class="card mb-2  px-1 py-1 border border-2 border-secondary rounded-30px">
                    <div class="card-body">
                        <div class="row text-secondary">
                            <p class="col-6 text-start">Coupons expiring soon</p>
                            <p class="col-6 text-end cursor-pointer" onclick="openTab('wallet-view-coupons')">
                                <i class="d-inline">View all my coupons </i>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                    </svg>
                                </span>
                            </p>
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
            <div class="col-12 mb-2 col-xl-7">
                <div class="card px-0 py-0 border border-2 border-secondary rounded-30px">
                    <div class="card-body px-0 py-0">
                        <div class="container-fluid px-0 py-0">
                            <div class="transaction-history">
                                <div class="row py-0 px-3 my-0">
                                    <h3 class="transaction-history__title  col-6 text-secondary">Most recent transactions
                                    </h3>
                                    <div onclick="openTab('wallet-view-transactions')"
                                        class="col-6 text-end text-secondary cursor-pointer">
                                        View full <span class="d-none d-lg-inline">transaction</span> history
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <div class="table-responsive my-2">
                                    <table class="transaction-history__table table "
                                        v-if="demoTransactions && demoTransactions[0]">
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
                                            <tr v-for="transaction in demoTransactions" :key="transaction.id"
                                                class="transaction-row">
                                                <x-wallet.transaction-item />
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

        <div class="d-none mx-auto px-0 container-main min-h-85vh" id="wallet-add-fund">
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
                                <h2 class=" my-3 fw-normal"> MYR {{ number_format($wallet->usable_balance, 2) }} </h2>
                            </div>
                            @include('includes.Flash')

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
                                                min="5" step="0.01" required
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
                                            class="btn border-secondary text-dark rounded-pill">
                                            Cancel
                                        </button>

                                        <button type="submit"
                                            class="btn btn-primary text-light rounded-pill">Confirm</button>

                                    </div>

                                </form>
                            </div>


                        </div>

                        @if ($wallet->last_payout_at)
                            <div class="my-3">
                                <p>Last withdrawal: {{ $wallet->last_payout_at->format('F j, Y, g:i a') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none mx-auto px-0 container-main min-h-85vh" id="wallet-withdraw-fund">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-95-lg-50 rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class=" px-2 py-2">
                        <div class=" px-1 py-0 my-0">
                            <h5 class="text-center mt-2 mb-3 text-secondary">Withdraw funds from your wallet</h5>

                        </div>
                        <div class="mt-3">
                            <div class="text-center mx-auto my-3 py-3 text-light bg-primary rounded-30px"
                                style="width: min(300px, 80%);">
                                <h5 class="mt-1 mb-3 fw-normal">Current Wallet Balance </h5>
                                <h2 class=" my-3 fw-normal"> MYR {{ number_format($wallet->usable_balance, 2) }} </h2>
                            </div>
                            @include('includes.Flash')

                            <!-- Topup Form -->
                            <div class="my-2">
                                <form action="{{ route('wallet.withdraw') }}" method="POST">
                                    @csrf
                                    <div class="text-center mb-4">
                                        <label for="topup_amount">Please enter your amount to withdraw </label>
                                        <div class=" input-group  d-flex justify-content-center mx-auto ">
                                            <button class="btn mx-auto pe-none btn-outline-secondary my-2 me-0 py-0"
                                                type="button" id="button-addon1">RM </button>
                                            <input type="number" id="topup_amount" name="topup_amount"
                                                class="d-inline mx-auto my-2 border-secondary ms-0 form-control"
                                                min="5" step="0.01" required
                                                style="width: min-content; max-width: 200px;">
                                        </div>
                                        {{-- <small class="text-muted fst-italic ">Minimum amount is RM 5.00</small> --}}
                                    </div>
                                    <div class="d-flex justify-content-around w-75 mb-4 mx-auto">
                                        <button onclick="openTab('wallet-main')" type="button"
                                            class="btn border-secondary text-dark rounded-pill">
                                            Cancel
                                        </button>

                                        <button type="submit"
                                            class="btn btn-primary text-light rounded-pill">Confirm</button>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none container-main mx-auto px-0 min-h-85vh" id="wallet-view-transactions">
            <div class="center-container mx-auto">
                <div class="card px-3 py-3 border border-2 mx-auto border-secondary  min-w-95vw rounded-30px">
                    <div class="card-body px-2 py-2">
                        <div class="d-flex justify-content-between cursor-pointer">
                            <h3 class="transaction-history__title ">My Transactions</h3>
                            <span onclick="openTab('wallet-main')" class="cursor-pointer">
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                                    </svg>
                                </span>
                                <span>Go back</span>
                            </span>
                        </div>
                        <div class="mt-3">

                            <div class="table-responsive mb-4" v-if="transactions && transactions[0]">
                                <table class="transaction-history__table table ">
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
                                                class="transaction-table__header-cell bg-secondary text-white py-3">Total
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr v-for="transaction in transactions" :key="transaction.id"
                                            class="transaction-row">
                                            <x-wallet.transaction-item />
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else>
                                <x-wallet.no-list :text="'No transactions available!'" />
                            </div>

                            <div v-if="hasMore" class="text-center my-4">
                                <button v-on:click="loadMore" :disabled="loading"
                                    class="btn text-light rounded-pill btn-primary">
                                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                                    <span>@{{ loading ? 'Loading...' : 'Load More' }}</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        <div class="d-none mx-auto px-0 container-main min-h-85vh" id="wallet-view-coupons">
            <div class="center-container mx-auto">
                <div class="card px-3 py-3 border border-2 mx-auto border-secondary  min-w-95vw rounded-30px">
                    <div class="card-body px-2 py-2">
                        <div class="row ">
                            <h3 class="col-6 text-start transaction-history__title ">My Coupons</h3>
                            <div class="col-6 text-end">
                                <button type="button" onclick="openTab('wallet-redeem-coupons')" class="btn rounded-pill border-secondary text-dark">
                                    Redeem coupons
                                </button>
                            </div>
                            <span onclick="openTab('wallet-main')" class="col-6 text-start cursor-pointer">
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd"
                                            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                                    </svg>
                                </span>
                                <span>Go back</span>
                            </span>
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


        <div class="d-none mx-auto px-0 container-main min-h-85vh" id="wallet-redeem-coupons">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-95-lg-50 rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class=" px-2 py-2">
                        <div class=" px-1 py-0 my-0">
                            <h5 class="text-center mt-2 mb-3 text-secondary">Have a coupon?</h5>
                        </div>
                        <div class="mt-3">
                            <!-- Topup Form -->
                            <div class="my-2">
                                <form action="{{ route('wallet.redeem-coupon') }}" method="POST">
                                    @csrf
                                    <div class="w-50 mx-auto text-center my-3">
                                        <label for="coupon_code">Coupon Code</label>
                                        <input type="text" id="coupon_code" name="coupon_code"
                                            class="form-control border-secondary text-center rounded-pill w-100" required>
                                    </div>
                                    
                                    <div class="d-flex justify-content-around w-75 mb-4 mx-auto">
                                        <button onclick="openTab('wallet-main')" type="button"
                                            class="btn border-secondary text-dark rounded-pill">
                                            Cancel
                                        </button>

                                        <button type="submit"
                                            class="btn btn-primary text-light rounded-pill">Confirm</button>

                                    </div>

                                </form>
                            </div>


                        </div>

                       
                    </div>
                </div>
            </div>
        </div>

        

        {{-- <div class="d-none mx-auto px-0 container-main " id="wallet-redeem-coupons">
            <div class="center-container mx-auto ">
                <div class="card px-2 py-0 border border-2 mx-auto border-secondary rounded-30px min-w-95vw">
                    <div class="card-body px-2 py-0">
                        <div class="container-fluid px-0 py-0">
                            <div class="my-4">
                                <div class="d-flex justify-content-between ">
                                    <h3 class="transaction-history__title ">Redeem Coupon</h3>
                                    <span onclick="openTab('wallet-main')" class="cursor-pointer">
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                                            </svg>
                                        </span>
                                        <span>Go back</span>
                                    </span>
                                </div>
                                <form action="{{ route('wallet.redeem-coupon') }}" method="POST">
                                    @csrf
                                    <div class=" my-3">
                                        <label for="coupon_code">Coupon Code</label>
                                        <input type="text" id="coupon_code" name="coupon_code"
                                            class="form-control w-100" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary rounded-pill text-light">Redeem
                                        Coupon</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

    </main>


@endsection
