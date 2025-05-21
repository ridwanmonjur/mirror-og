@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/fullpage.css') }}">
</head>
@section('content')
    @include('includes.__Navbar.NavbarGoToSearchPage')

    <div class="row mt-4">
       

        <div class="mx-auto" style="max-width: 600px;">
             
            <div class="card">
                <div class="card-header">Your Wallet</div>

                <div class="card-body">
                @include('includes.Flash')
                    <h3>Current Balance: {{ number_format($wallet->usable_balance, 2) }} MYR</h3>

                    @if ($wallet->has_bank_account)
                        <div class="mt-3">
                            <p>Withdrawal Method: {{ $wallet->bank_name }} (****{{ $wallet->bank_last4 }})</p>

                            @if ($wallet->usable_balance > 0)
                                <form action="{{ route('wallet.withdraw') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="form-group mb-3">
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
                                        <input type="number" id="topup_amount" name="topup_amount" class="form-control"
                                            min="5" step="0.01" required>
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
                                            <input type="text" id="coupon_code" name="coupon_code" class="form-control"
                                                required>
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
@endsection
