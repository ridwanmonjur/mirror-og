<div class="row px-5 mb-2" id="payment-discount-view">
    <div class="modal fade" id="discountModal" tabindex="-1"
        aria-labelledby="#discountModal" aria-hidden="true">
        @php
            $isDiscountWallet = is_null($discount_wallet);
        @endphp
        <div class="modal-dialog">
            <form 
                data-redirect_url="{{route('participant.register.manage', ['id' => $teamId])}}"
                method="POST"
                onsubmit="handleSubmit(event);"
                id="discountPaymentForm"
            >
                @csrf
                    <div class="modal-content">
                        <div class="modal-body py-4 px-5">
                            <h5 class="mt-4 mb-0">Pay using your previous discount coupons!</h5>
                            <small> Avoid paying for this event by using refunds and coupons from previous events.</small>
                            <br><br>
                            @if ($isDiscountWallet)
                                <br> <br>
                                <p class="text-center">Ooops, no coupons </p> 
                            @else
                             <p> 
                                You have <span class="text-success" id="wallet_amount">RM {{$discount_wallet->amount}}</span> of discounts to apply towards this event.
                            </p>
                            <div class="text-center mx-auto input-group mt-4 w-75">
                                <input type="hidden" id="payment_amount_input" name="payment_amount" value="{{ $amount }}">
                                <input type="hidden" id="member_id" name="member_id" value="{{ $memberId }}">
                                <input type="hidden" id="joinEventId" name="joinEventId" value="{{ $joinEventId }}">
                                <input type="hidden" id="payment_intent_id" name="payment_intent_id"> 
                                <br>
                                <span class="input-group-text bg-primary text-light" id="inputGroup-sizing-sm">RM </span>
                                <input 
                                    data-amount="{{$amount}}"
                                    data-wallet="{{$discount_wallet->amount}}"
                                    name="discount_applied_amount" 
                                    class="form-control text-auto" 
                                    type="number" 
                                    placeholder="0.00"
                                    default="0.00"
                                    step=".01"
                                    pattern="^\d+(?:\.\d{1,2})?$" 
                                >
                            </div>
                            <br>
                            <div class="mx-auto text-center">
                                <button 
                                    type="submit"
                                    class="mt-2 ms-4 btn rounded-pill text-dark px-4 py-2 btn-success">Apply towards
                                    payment
                                </button>
                                <button type="button" data-bs-dismiss="modal"
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
        <h5 class="my-0">Payment Method</h5>
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
                    <input type="hidden" name="user_id" value="{{ $event->userId }}" />
                    <br>
                    <div id="spinner-element" class="d-flex justify-content-center mt-5">
                        <div class="spinner-border text-primary" role="status">
                            <br><br>
                            <br><br>
                            <span class="visually-hidden text-center ">Loading...</span>
                        </div>
                    </div>
                    <div class="row w-100">
                        <div class="col-12 col-lg-6">
                            <div id="address-element" class="my-2"> </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div id="discount-element" class="mt-3 d-none">
                                @if ($isDiscountWallet) 
                                    You have no discount to apply.
                                @else
                                    <span> 
                                        You have RM {{$discount_wallet->amount}} of discounts to apply towards this event.
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
                            <div class="d-none d-lg-block">

                                <br><br><br><br>
                            </div>
                            <div class="d-flex justify-content-center my-3 d-none" id="submit-button-element">
                                <button class="oceans-gaming-default-button" type="submit"> Submit </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div id="eWalletLogoId" class="payment-element-children-view d-none">Ewallet view</div>
            <div id="bankLogoId" class="payment-element-children-view d-none">Bank view</div>
            <div id="otherEWalletLogoId" class="payment-element-children-view d-none">
                <div id="express-apple-checkout-element"> </div>
            </div>
        </div>
        <div class="me-5 pb-2 mb-2 d-none">
            <div onclick="toggleArrows(event);"
                class="cursor-pointer rounded-box px-3 py-2 d-flex justify-content-between" data-bs-toggle="collapse"
                href="#card-accordion" aria-expanded="false" aria-controls="card-accordion">
                <div> Credit / Debit Card </div>
                <div class="accordion-arrows"> @include('Organizer.__CheckoutPartials.AccordionArrows') </div>
            </div>
            <div class="collapse px-3 py-2 multi-collapse" id="card-accordion">
                @include('Organizer.__CheckoutPartials.CheckoutCardOption')
            </div>
        </div>
        <div class="me-5 pb-2 mb-2 d-none">
            <div onclick="toggleArrows(event);"
                class="cursor-pointer rounded-box px-3 py-2 d-flex justify-content-between" data-bs-toggle="collapse"
                href="#eWallet-accordion" aria-expanded="false" aria-controls="eWallet-accordion">
                <div> eWallet </div>
                <div class="accordion-arrows"> @include('Organizer.__CheckoutPartials.AccordionArrows') </div>
            </div>
            <div class="collapse px-3 py-2 multi-collapse" id="eWallet-accordion">
                <div class="grid-4-columns">
                    @foreach (bladeGetPaymentLogos('eWallet') as $logo)
                        <div class="position-relative" style="width: min-content;">
                            <img src="{{ asset('/assets/images/logo/' . $logo['src']) }}" alt="{{ $logo['name'] }}"
                                width="{{ $logo['width'] }}" height="{{ $logo['height'] }}"
                                onclick="onChoosePayment(event, 'eWallet', '{{ $logo['name'] }}');"
                                @class([
                                    'payment-element',
                                    'mt-3',
                                    'hover-bigger',
                                    'object-fit-cover' => $logo['cover'],
                                ])>
                            <div class="rounded-circle position-absolute px-1 check-tick d-none">✔</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="me-5 pb-2 mb-2 d-none">
            <div onclick="toggleArrows(event);"
                class="cursor-pointer rounded-box px-3 py-2 d-flex justify-content-between" data-bs-toggle="collapse"
                href="#online-banking-accordion" aria-expanded="false" aria-controls="online-banking-accordion">
                <div> Online Banking (FPX) </div>
                <div class="accordion-arrows"> @include('Organizer.__CheckoutPartials.AccordionArrows') </div>
            </div>
            <div class="collapse px-3 py-2 multi-collapse" id="online-banking-accordion">
                <div class="grid-5-columns">
                    @foreach (bladeGetPaymentLogos('bank') as $logo)
                        <div class="position-relative" style="width: min-content;">
                            <img src="{{ asset('/assets/images/logo/' . $logo['src']) }}" alt="{{ $logo['name'] }}"
                                width="{{ $logo['width'] }}" height="{{ $logo['height'] }}"
                                @class([
                                    'payment-element',
                                    'mt-3',
                                    'hover-bigger',
                                    'object-fit-cover' => $logo['cover'],
                                ])
                                onclick="onChoosePayment(event, 'bank', '{{ $logo['name'] }}');">
                            <div class="rounded-circle position-absolute px-1 check-tick d-none">✔</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="me-5 pb-2 mb-2 d-none">
            <div onclick="toggleArrows(event);"
                class="cursor-pointer rounded-box px-3 py-2 d-flex justify-content-between" data-bs-toggle="collapse"
                href="#other-methods-accordion" aria-expanded="false" aria-controls="other-methods-accordion">
                <div> Other Methods </div>
                <div class="accordion-arrows"> @include('Organizer.__CheckoutPartials.AccordionArrows') </div>
            </div>
            <div class="collapse px-3 py-2 multi-collapse" id="other-methods-accordion">
                <div class="grid-4-columns">
                    @foreach (bladeGetPaymentLogos('otherEWallet') as $logo)
                        <div class="position-relative" style="width: min-content;">
                            <img src="{{ asset('/assets/images/logo/' . $logo['src']) }}" alt="{{ $logo['name'] }}"
                                width="{{ $logo['width'] }}" height="{{ $logo['height'] }}"
                                @class([
                                    'payment-element',
                                    'mt-3',
                                    'hover-bigger',
                                    'object-fit-cover' => $logo['cover'],
                                ])
                                onclick="onChoosePayment(event, 'otherEWallet', '{{ $logo['name'] }}');">
                            <div class="rounded-circle position-absolute  px-1 check-tick d-none">✔</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4" id="payment-summary">
        <h4>Payment Summary</h4>
        <br>
        <div> 
            <div><strong>Team Member Details</strong></div>
            <div class="ms-3">Team: <span>{{ $teamName }}</span></div>
            <div class="ms-3">Member: <span>{{ $user->name }}</span></div>
            <div class="ms-3">Email: <span>{{ $user->email }}</span></div>
        </div>
        <br>
        <div>
            <div><b>Event Details</b></div>
            <div class="ms-3">Event: <span id="eventName">{{ $event->eventName }}</span></div>
            <div class="ms-3">Game: <span id="paymentType">{{ $event->game?->gameTitle }}</span></div>
            <div class="ms-3">Type: <span id="paymentType">{{ $event->type?->eventType }}</span></div>
            <div class="ms-3">Tier: <span id="paymentTier">{{ $event->tier?->eventTier }}</span></div>
            <div class="ms-3">Region: <span id="paymentTier">South East Asia (SEA)</span></div>
            <br>
            <br>
            <div class=" d-flex justify-content-between w-75">
                <h5> TOTAL </h5>
                <h5 id="paymentTotal">RM
                    <span id="actualPaymentTable" class="transform-number me-1">{{ $amount }} </span>
                </h5>
            </div>
            <div class="d-flex justify-content-center w-75">
                <button type="submit"
                    class="oceans-gaming-default-button-base oceans-gaming-transparent-button px-2 py-2 mt-2">
                    <a href="{{ route('participant.register.manage', ['id' => $teamId]) }}" class="submit-texts d-block"> Cancel </a>
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
