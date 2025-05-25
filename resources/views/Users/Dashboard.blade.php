@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite([  'resources/js/alpine/settings.js'])
</head>
@section('content')
    @include('includes.Navbar.NavbarGoToSearchPage')
    <main class="wallet">
        <div class="row container-main d-noneX mx-auto" id="wallet-main">
            <div class="col-12 col-lg-6 col-xl-5">
                <div class="card mb-2  py-1 border border-3 border-primary rounded-30px ">
                    <div class="card-body">
                        <h5 class="mt-1 mb-3 text-secondary">Current Balance </h5>
                        <h2 class="text-primary my-3"> MYR {{ number_format($wallet->usable_balance, 2) }} </h2>
                        <div class="mb-2">
                            <button type="button"
                                class="btn d-inline-block me-2 mb-1 rounded-pill btn-primary  text-light ">
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
                            <button type="button" class="btn rounded-pill mb-1 btn-secondary text-light ">
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
                            <p class="col-6 text-end">
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
                                @foreach ($demoCoupons as $coupon)
                                    <x-wallet.coupon-card :coupon="$coupon" :className="'col-12'" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-2 col-lg-6 col-xl-7">
                <div class="card px-0 py-0 border border-2 border-secondary rounded-30px">
                    <div class="card-body px-0 py-0">
                        <div class="container-fluid px-0 py-0">
                            <!-- Block: transaction-history -->
                            <div class="transaction-history">
                                <!-- Element: transaction-history__header -->
                                <div class="transaction-history__header d-flex justify-content-between">
                                    <!-- Element: transaction-history__title -->
                                    <h3 class="transaction-history__title text-secondary">Most recent transactions</h3>
                                    <!-- Element: transaction-history__view-link -->
                                    <a href="#" class="transaction-history__view-link text-secondary">
                                        View full transaction history
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd"
                                                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
                                            </svg>
                                        </span>
                                    </a>
                                </div>

                                <!-- Transaction Table Container -->
                                <div class="table-responsive mb-4">
                                    <!-- Element: transaction-history__table -->
                                    <table class="transaction-history__table table responsive">
                                        <!-- Table Header with Block: transaction-table -->
                                        <thead class="transaction-table__header">
                                            <tr>
                                                <th scope="col" class="transaction-table__header-cell">Date</th>
                                                <th scope="col" class="transaction-table__header-cell">Transaction</th>
                                                <th scope="col" class="transaction-table__header-cell">Type</th>
                                                <th scope="col" class="transaction-table__header-cell">Total
                                                </th>
                                            </tr>
                                        </thead>

                                        <!-- Table Body -->
                                        <tbody>
                                            <!-- Block: transaction-row -->
                                            <tr class="transaction-row">
                                                <!-- Element: transaction-row__cell with Modifier: --date -->
                                                <td class="transaction-row__cell transaction-row__cell--date">
                                                    21 May 2025,<br>
                                                    8:02 PM
                                                </td>
                                                <!-- Element: transaction-row__cell with Modifier: --transaction -->
                                                <td class="transaction-row__cell transaction-row__cell--transaction">
                                                    <!-- Block: transaction-details -->
                                                    <div class="transaction-details__title">Welcome to Driftwood: Into The
                                                        Ocean</div>
                                                    <p class="transaction-details__subtitle">Dota 2, Tournament, Starfish
                                                    </p>
                                                </td>
                                                <!-- Element: transaction-row__cell with Modifier: --type -->
                                                <td class="transaction-row__cell transaction-row__cell--type">Event Entry
                                                    Fees</td>
                                                <!-- Element: transaction-row__cell with Modifier: --total -->
                                                <td class="transaction-row__cell transaction-row__cell--total">RM 25.00</td>
                                            </tr>

                                            <tr class="transaction-row">
                                                <td class="transaction-row__cell transaction-row__cell--date">
                                                    15 May 2025,<br>
                                                    3:45 PM
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--transaction">
                                                    <div class="transaction-details__title">Wallet Funds Purchase RM 50.00
                                                    </div>
                                                    <p class="transaction-details__subtitle">Visa •••• 3865</p>
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--type">Funds Purchase
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--total">RM 50.00</td>
                                            </tr>

                                            <tr class="transaction-row">
                                                <td class="transaction-row__cell transaction-row__cell--date">
                                                    10 May 2025,<br>
                                                    7:50 PM
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--transaction">
                                                    <div class="transaction-details__title">Coastal Clash</div>
                                                    <p class="transaction-details__subtitle">Valorant, Tournament, Starfish
                                                    </p>
                                                    <!-- Block: prize-badge with Modifier: --first-place -->
                                                    <span class="prize-badge prize-badge--first-place">1st place</span>
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--type">Prize
                                                    Winnings
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--total">RM 150.00
                                                </td>
                                            </tr>

                                            <tr class="transaction-row">
                                                <td class="transaction-row__cell transaction-row__cell--date">
                                                    05 May 2025,<br>
                                                    3:45 PM
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--transaction">
                                                    <div class="transaction-details__title">Wallet Funds Withdrawal RM
                                                        250.00</div>
                                                    <p class="transaction-details__subtitle">Maybank •••• 5921</p>
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--type">Funds
                                                    Withdrawal</td>
                                                <td class="transaction-row__cell transaction-row__cell--total">RM 250.00
                                                </td>
                                            </tr>

                                            <tr class="transaction-row">
                                                <td class="transaction-row__cell transaction-row__cell--date">
                                                    25 Apr 2025,<br>
                                                    5:20 AM
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--transaction">
                                                    <div class="transaction-details__title">Wallet Funds Purchase RM250.00
                                                    </div>
                                                    <p class="transaction-details__subtitle">Mastercard •••• 4570</p>
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--type">Funds
                                                    Purchase
                                                </td>
                                                <td class="transaction-row__cell transaction-row__cell--total">RM 250.00
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <div class="d-none mx-auto px-0 container-main " id="wallet-add-fund">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary center-container rounded-30px">
                <div class="card-body px-0 py-0">
                    <div class="container-fluid px-0 py-0">
                        @if ($wallet->has_bank_account)
                            <div class="mt-3">
                                @include('includes.Flash')

                                <p>Withdrawal Method: {{ $wallet->bank_name }} (****{{ $wallet->bank_last4 }})</p>

                                @if ($wallet->usable_balance > 0)
                                    <form action="{{ route('wallet.withdraw') }}" method="POST" class="mb-4">
                                        @csrf
                                        <div class="form-ms-group mb-3">
                                            <label for="withdrawal_amount">Withdrawal Amount (RM)</label>
                                            <input type="number" id="withdrawal_amount" name="withdrawal_amount"
                                                class="form-control" min="5" max="{{ $wallet->usable_balance }}"
                                                step="0.01" required value="{{ $wallet->usable_balance }}">
                                            <small class="text-muted">Available balance: RM
                                                {{ number_format($wallet->usable_balance, 2) }}</small>
                                            <small class="text-muted">Minimum withdrawal: RM 5.00</small>
                                        </div>
                                        <button type="submit" class="btn btn-primary text-light ">Withdraw Funds</button>
                                    </form>
                                @else
                                    <div class="alert alert-info">
                                        No funds available for withdrawal.
                                    </div>
                                @endif

                                <!-- Topup Form -->
                                <div class="mt-4">
                                    <h4>Add Funds</h4>
                                    <form action="{{ route('wallet.checkout') }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="topup_amount">Topup Amount (RM)</label>
                                            <input type="number" id="topup_amount" name="topup_amount"
                                                class="form-control" min="5" step="0.01" required>
                                            <small class="text-muted">Minimum topup: RM 5.00</small>
                                        </div>
                                        <button type="submit" class="btn btn-success">Add Funds</button>
                                    </form>
                                </div>

                                <!-- Coupon Redemption Form (if coupons exist) -->
                                @if (isset($has_coupons) && $has_coupons)
                                    <div class="mt-4">
                                        <h4>Redeem Coupon</h4>
                                        <form action="{{ route('wallet.redeem-coupon') }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label for="coupon_code">Coupon Code</label>
                                                <input type="text" id="coupon_code" name="coupon_code"
                                                    class="form-control" required>
                                            </div>
                                            <button type="submit" class="btn btn-info">Redeem Coupon</button>
                                        </form>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <a href="{{ route('wallet.payment-method') }}"
                                        class="btn rounded-pill btn-primary text-light ">
                                        Change Payment Method
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <p>You need to add a payment method to withdraw your funds.</p>
                                <a href="{{ route('wallet.payment-method') }}"
                                    class="btn rounded-pill btn-primary text-light text-light mt-2">
                                    Add Payment Method
                                </a>
                            </div>
                        @endif

                        @if ($wallet->last_payout_at)
                            <div class="mt-3">
                                <p>Last withdrawal: {{ $wallet->last_payout_at->format('F j, Y, g:i a') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none mx-auto px-0 container-main " id="wallet-topup-fund">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary center-container rounded-30px">
                <div class="card-body px-0 py-0">
                    <div class="container-fluid px-0 py-0">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none container-main mx-auto px-0 " id="wallet-view-transactions">
            
        </div>

        <div class=" mx-auto px-0 container-main " id="wallet-view-coupons">
            <div class="center-container mx-auto" >
                <div class="card px-3 py-3 border border-2 mx-auto border-secondary  rounded-30px" style="min-width: 95vw;">
                    <div class="card-body px-2 py-2">
                        <div class="d-flex justify-content-start">
                            <h3 class="transaction-history__title ">My Coupons</h3>
                        </div>
                        <div class="px-0 pt-4 pb-4 row">
                            @foreach ($coupons as $coupon)
                                <x-wallet.coupon-card :coupon="$coupon" :className="' col-lg-6 col-xl-4 '" />
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none mx-auto px-0 container-main " id="wallet-redeem-coupons">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary center-container rounded-30px">
                <div class="card-body px-0 py-0">
                    <div class="container-fluid px-0 py-0">
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection
