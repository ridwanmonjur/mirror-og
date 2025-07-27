
<br>
<div class="row px-5 mb-2" id="payment-discount-view">
    <div class="modal fade" id="discountModal" tabindex="-1"
        aria-labelledby="#discountModal" aria-hidden="true">
        <div class="modal-dialog">
            <form 
                action="{{route('shop.walletCheckout')}}"
                method="POST"
                onsubmit="handleSubmit(event);"
                id="discountPaymentForm"
            >
                @csrf
                    <div class="modal-content">
                        <div class="modal-body py-4 px-5">
                            <h5 class="mt-4 mb-3 ">Pay using your remaining wallet funds!</h5>
                            <small> Avoid paying for this order by using funds from your wallet.</small>
                            <br><br>
                            @if (!$has_wallet_balance)
                                <br> <br>
                                <p class="text-center text-red">Ooops, no wallet fund available.</p>
                                <div class="text-center">
                                    <a href="{{ route('wallet.dashboard') }}" class="btn btn-primary btn-sm">Load More Money</a>
                                </div>
                            @else
                                <p> 
                                    You have <span class="" id="wallet_amount">RM {{number_format($user_wallet->usable_balance, 2)}}</span> usable balance in your wallet.
                                    
                                    @if ($can_pay_full_amount)
                                        <br><span class="">You can pay the full amount of RM {{number_format($total_amount, 2)}} with your wallet!</span>
                                    @else
                                        <br><span class="text-warning">You need RM {{number_format($wallet_shortfall, 2)}} more to complete this payment.</span>
                                        <br><small class="text-muted">
                                            <a href="{{ route('wallet.dashboard') }}" class="text-primary">Top up your wallet</a> or add coupons to reduce the amount.
                                        </small>
                                    @endif
                                </p>
                                <div class="text-center mx-auto input-group mt-4 w-75">
                                    <input type="hidden" id="amount" name="amount" value="{{ $can_pay_full_amount ? $total_amount : 0 }}">
                                    <input type="hidden" id="coupon_code" name="coupon_code" value="{{ $prevForm['coupon_code'] ?? '' }}">
                                </div>
                                <div class="mx-auto text-center">
                                    @if ($can_pay_full_amount)
                                        <button 
                                            type="submit"
                                            class="mt-2 ms-4 btn rounded-pill text-light px-4 py-2 btn-primary">Pay RM {{number_format($total_amount, 2)}} with wallet
                                        </button>
                                    @endif

                                    <button type="button" data-bs-dismiss="modal" id="closeDiscountModal"
                                        class="mt-2 ms-4 py-2 btn oceans-gaming-default-button oceans-gaming-transparent-button">Cancel</button>
                                </div>
                            @endif
                        </div>
                    </div>
            </form>
            <br>
        </div>
    </div>

    <div class="d-none d-lg-block px-3">
    </div>
    <div class="col-12 col-xl-8 px-3">
        <h5 class="my-0 py-0 text-primary">Payment Method</h5>
        @if (session('errorCheckout'))
            <div class="text-red my-2">
                {{ session('errorCheckout') }}
            </div>
        @endif
        {{-- <br> --}}

        <div id="payment-element-view">
            {{-- <div class="text-center" onclick="changeScreen();"> Close </div> --}}
            <div id="cardLogoId" class="payment-element-children-view">
                <form method="POST" onsubmit="finalizeStripeCardPayment(event);">
                    <br>
                    <div id="spinner-element" class="d-flex justify-content-center mt-5">
                        <div class="spinner-border text-primary" role="status">
                            <br>
                            <span class="visually-hidden text-center ">Loading...</span>
                        </div>
                    </div>
                    <div class="row w-100">
                        <div class="col-12 col-lg-6">
                            <div class="billing-shipping-container d-none">
                                <div class="billing-section">
                                    <div class="billing-header clickable-header d-flex justify-content-between align-items-center text-primary border-bottom border-primary" data-section="billing">
                                        <h6 class="mb-0">Billing Details</h6>
                                        <svg class="arrow-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </div>
                                    <div id="billing-address-element" class="my-2 address-section" data-section="billing"> </div>
                                </div>
                                
                                <div class="shipping-section mt-3">
                                    <div class="shipping-header clickable-header d-flex justify-content-between align-items-center border-bottom border-secondary" data-section="shipping">
                                        <h6 class="mb-0">Shipping Details</h6>
                                        <svg class="arrow-icon arrow-collapsed" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="color: #6c757d;">
                                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </div>
                                    <div id="shipping-address-element" class="mt-3 mb-0 address-section d-none" data-section="shipping"> </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="payment-method-container d-none">
                                <div class="wallet-payment-section">
                                    <div class="wallet-payment-header clickable-header d-flex justify-content-between align-items-center text-primary border-bottom border-primary" data-section="wallet">
                                        <h6 class="mb-0">Pay Using Wallet</h6>
                                        <svg class="arrow-icon" width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </div>
                                    <div id="discount-element" class="mt-3 payment-section" data-section="wallet">
                                        @if (!$has_wallet_balance) 
                                            You have no wallet balance to apply.
                                        @elseif ($can_pay_full_amount)
                                            <span class=""> 
                                                You can pay the full amount of RM {{number_format($total_amount, 2)}} with your wallet balance of RM {{number_format($user_wallet->usable_balance, 2)}}
                                            </span>
                                            <a 
                                                 data-bs-toggle="modal"
                                                data-bs-target="#discountModal"
                                                class="my-0 btn btn-link py-0" style="color: #43A4D7 !important" type="button"
                                            > 
                                                <u> Use Wallet </u> 
                                            </a>
                                        @else
                                            <span class="text-warning"> 
                                                You have RM {{number_format($user_wallet->usable_balance, 2)}} but need RM {{number_format($wallet_shortfall, 2)}} more.
                                            </span>
                                            <a href="{{ route('wallet.dashboard') }}" class="btn btn-link btn-sm text-primary">Top up wallet</a>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="card-payment-section mt-3">
                                    <div class="card-payment-header clickable-header d-flex justify-content-between align-items-center border-bottom border-secondary" data-section="card">
                                        <h6 class="mb-0">Pay Using Card</h6>
                                        <svg class="arrow-icon arrow-collapsed" width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="color: #6c757d;">
                                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </div>
                                    <div id="card-element" class="mt-3 mb-0 payment-section d-none" data-section="card"> </div>
                                </div>
                            </div>
                            <div class="my-4 d-none" id="save-payment-container">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="save-payment">
                                        <label class="form-check-label" for="save-payment">
                                            Save Payment Information
                                        </label>
                                    </div>
                                </div>
                            <div class="d-none d-lg-block">

                            </div>
                            <div class="d-flex justify-content-center my-3 d-none" id="submit-button-element">
                                <button class="oceans-gaming-default-button" type="submit"> Submit </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4" id="payment-summary">
        <h4 class="text-primary">Payment Summary</h4>
        <br>
        <div> 
            <div><strong>Customer Details</strong></div>
            <div class="ms-3">Name: <span>{{ $user->name }}</span></div>
            <div class="ms-3">Email: <span>{{ $user->email }}</span></div>
        </div>
        <br>
        <div>
            <div><b>Order Details</b></div>
            <div class="ms-3">Items: <span id="cartItems">{{ $cart->getCount() }}</span></div>
            <div class="ms-3">Subtotal: <span id="cartSubtotal">RM {{ number_format($cart->getSubTotal(), 2) }}</span></div>
            @if (session()->has('coupon'))
                <div class="ms-3">Discount: <span id="cartDiscount">- RM {{ number_format($discount, 2) }}</span></div>
            @endif
            <br>
             <div class="mt-2 d-flex justify-content-between w-75">
                <h5> TOTAL </h5>
                <h5 id="paymentTotal">
                @if ($fee['discountFee'] > 0)
                    <span class=" me-1" style="text-decoration: line-through;">
                        RM {{ number_format($fee['totalFee'], 2)  }}
                    </span>
                    <span >
                        RM {{ number_format($fee['finalFee'], 2) }}
                    </span>
                @else
                    <span>
                        RM {{ number_format($fee['finalFee'], 2) }}
                    </span>
                @endif
            </h5>
            </div>
            <div class="d-none">Promo Code</div>
            <form  method="GET" class=" d-none row mx-0 px-0 mb-1">
                @csrf
                <div class="form-group mb-0 px-0 mx-0">
                    <input type="hidden" id="amount" name="amount" value="{{ $amount }}">
                    <input type="text" name="coupon_code" class="mb-2 px-3 w-75" style="padding-top: 6px; padding-bottom: 6px;"
                        value="{{ $prevForm['coupon_code'] ?? '' }}">
                    <button class="ms-2 oceans-gaming-default-button ps-3" style="background-color: #95ADBD;">
                        <span> Apply </span>
                    </button>
                </div>
            </form>
            
            @if (isset($couponStatus['error']) && $couponStatus['error'])
                <div class="text-red mb-2">
                    {{ $couponStatus['error'] }}
                </div>
            @endif
            
            @if (isset($couponStatus['success']) && $couponStatus['success'])
                <div class=" mb-2">
                    We have applied your coupon: {{$prevForm['coupon_code'] ?? ''}} successfully!
                </div>
            @endif
            <div class="d-flex justify-content-center w-75">
                    {{-- <a href="{{ route('shop.index') }}" class="oceans-gaming-default-button-base oceans-gaming-transparent-button px-2 py-2 mt-2 submit-texts d-block"> Cancel </a> --}}
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
            </div>
        </div>
    </div>
</div>