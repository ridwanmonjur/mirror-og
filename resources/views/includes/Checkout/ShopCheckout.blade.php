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
                            <h5 class="mt-4 mb-3 text-success">Pay using your remaining wallet funds!</h5>
                            <small> Avoid paying for this order by using funds from your wallet.</small>
                            <br><br>
                            @if ($walletStatusEnums['ABSENT'] == $walletStatus || $walletStatusEnums['INVALID'] == $walletStatus)
                                <br> <br>
                                <p class="text-center text-red">Ooops, no wallet fund </p> 
                            @else
                                <p> 

                                    You have <span class="text-success" id="wallet_amount">RM {{$user_wallet->usable_balance}}</span> usable balance in your wallet.
                                    @if ($walletStatusEnums['COMPLETE'] == $walletStatus )
                                        <span> You can complete payment of <span>RM {{$payment_amount_min}}</span> with your wallet. </span>
                                        
                                    @elseif ($walletStatusEnums['PARTIAL'] == $walletStatus )
                                        @if ( $paymentLowerMin < $payment_amount_min )
                                            <span> You can apply <span>RM {{$payment_amount_min}}</span> towards your order. </span>
                                        @endif
                                        <br>
                                        <span class="text-red"> Note: the minimum payment for a transaction is about 5 RM, depending on currency rates.</span>
                                    @endif
                                </p>
                                <div class="text-center mx-auto input-group mt-4 w-75">
                                    <input type="hidden" id="amount" name="amount" value="{{ $payment_amount_min }}">
                                    <input type="hidden" id="coupon_code" name="coupon_code" value="{{ $prevForm['coupon_code'] ?? '' }}">
                                </div>
                                <div class="mx-auto text-center">
                                    @if ($paymentLowerMin <= $payment_amount_min)
                                        <button 
                                            type="submit"
                                            class="mt-2 ms-4 btn rounded-pill text-light px-4 py-2 btn-primary">Apply RM {{$payment_amount_min}} towards
                                            payment
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
        <h5 class="my-0 py-0">Payment Method</h5>
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
                            <div id="address-element" class="my-2"> </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div id="discount-element" class="mt-3 d-none">
                                @if ($walletStatusEnums['ABSENT'] == $walletStatus) 
                                    You have no discount to apply.
                                @elseif ($walletStatusEnums['INVALID'] == $walletStatus)
                                        You have RM {{$user_wallet->current_balance}} net balance and RM {{$user_wallet->usable_balance}} usable balance in your wallet to apply towards this order.
                                        But this is not enough for the next transaction.
                                @elseif ($user_wallet->usable_balance)
                                    <span> 
                                        
                                        You can apply RM {{$user_wallet->usable_balance}} from your wallet to pay towards this order
                                    </span>
                                    <a 
                                         data-bs-toggle="modal"
                                        data-bs-target="#discountModal"
                                        class="my-0 btn btn-link py-0" style="color: #43A4D7 !important" type="button"
                                    > 
                                        <u> Apply </u> 
                                    </a>
                                @endif
                            </div>
                            <div id="card-element" class="my-2"> </div>
                            <div class="my-4">
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
        <h4>Payment Summary</h4>
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
            <div>Promo Code</div>
            <form method="GET" class="row mx-0 px-0 mb-1">
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
                <div class="text-success mb-2">
                    We have applied your coupon: {{$prevForm['coupon_code'] ?? ''}} successfully!
                </div>
            @endif
            <div class="d-flex justify-content-center w-75">
          
                    <a href="{{ route('shop.index') }}" class="oceans-gaming-default-button-base oceans-gaming-transparent-button px-2 py-2 mt-2 submit-texts d-block"> Cancel </a>
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
            </div>
        </div>
    </div>
</div>