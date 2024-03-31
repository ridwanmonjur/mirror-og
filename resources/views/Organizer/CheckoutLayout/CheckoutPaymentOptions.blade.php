<div class="grid-2-columns mx-4" id="payment-discount-view">
    <div class="mx-2">
        <h4>Payment Method</h4>
        @if (session('errorCheckout'))
            <div class="text-danger my-2">
                {{ session('errorCheckout') }}
            </div>
        @endif
        <br>

        <div id="payment-element-view">
            {{-- <div class="text-center" onclick="changeScreen();"> Close </div> --}}
            <div id="cardLogoId" class="payment-element-children-view">
                <form method="POST" onsubmit="finalizeStripeCardPayment(event);">
                    <input type="hidden" name="user_id" value="{{ $event->userId }}" />
                    <div>
                        <div id="address-element" class="my-2"> </div>
                        <div id="card-element" class="my-2"> </div>
                    </div>
                    <div class="d-flex justify-content-center my-3 d-none" id="submit-button-element">
                        <button class="oceans-gaming-default-button" type="submit"> Submit </button>
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
                <div class="accordion-arrows"> @include('Organizer.CheckoutLayout.AccordionArrows') </div>
            </div>
            <div class="collapse px-3 py-2 multi-collapse" id="card-accordion">
                @include('Organizer.CheckoutLayout.CheckoutCardOption')
            </div>
        </div>
        <div class="me-5 pb-2 mb-2 d-none">
            <div onclick="toggleArrows(event);"
                class="cursor-pointer rounded-box px-3 py-2 d-flex justify-content-between" data-bs-toggle="collapse"
                href="#eWallet-accordion" aria-expanded="false" aria-controls="eWallet-accordion">
                <div> eWallet </div>
                <div class="accordion-arrows"> @include('Organizer.CheckoutLayout.AccordionArrows') </div>
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
                <div class="accordion-arrows"> @include('Organizer.CheckoutLayout.AccordionArrows') </div>
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
                <div class="accordion-arrows"> @include('Organizer.CheckoutLayout.AccordionArrows') </div>
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
    <div class="mx-2 d-none" id="payment-summary">
        <h4>Payment Summary</h4>
        <br>
        <div>
            <div>Event Categories</div>
            <div class="ms-3">Game: <span id="paymentType">{{ $event->game->gameTitle }}</span></div>
            <div class="ms-3">Type: <span id="paymentType">{{ $event->type->eventType }}</span></div>
            <div class="ms-3">Tier: <span id="paymentTier">{{ $event->tier->eventTier }}</span></div>
            <div class="ms-3">Region: <span id="paymentTier">South East Asia (SEA)</span></div>
            <br>
            @php

            @endphp
            <div class="flexbox w-75">
                <span>Subtotal</span>
                <span id="subtotal">RM
                    <span class="transform-number"> {{ $fee['entryFee'] }} </span>
                </span>
            </div>
            <div class="flexbox w-75">
                <span>Event Creation Fee Rate</span>
                <span id="paymentRate">20%</span>
            </div>
            <br>
            <div class="flexbox w-75">
                <h5> TOTAL </h5>
                <h5 id="paymentTotal">RM
                    @if ($fee['discountFee'] > 0)
                        <span class="transform-number me-1"
                            style="text-decoration: line-through;">{{ $fee['totalFee'] }} </span>
                        <span class="transform-number">{{ $fee['finalFee'] }} </span>
                    @else
                        <span class="transform-number">{{ $fee['finalFee'] }} </span>
                    @endif
                </h5>
            </div>
            <br>
            <div>Promo Code</div>
            <form method="GET">
                <div class="form-group w-75 d-flex">
                    <input type="text" name="coupon"
                        value="{{ app()->request->coupon ? app()->request->coupon : '' }}">
                    <div class="d-inline-block px-2"></div>
                    <button class="px-3 oceans-gaming-default-button" style="background-color: #95ADBD;">
                        <span> Apply </span>
                    </button>
                </div>
            </form>
            @if (session('errorMessageCoupon'))
                <div class="text-danger">
                    {{ session('errorMessageCoupon') }}
                </div>
            @endif
            @if (session('successMessageCoupon'))
                <div class="text-success">
                    {{ session('successMessageCoupon') }}
                </div>
            @endif
            {{-- <div class="d-flex justify-content-center w-75">
                <button type="button" onclick="changeScreen()"
                    class="payment-button oceans-gaming-default-button-base oceans-gaming-gray-button px-4 py-3 mt-2">
                    <div class="submit-texts"> Confirm & Pay </div>
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div> --}}
            <div class="d-flex justify-content-center w-75">
                <button type="submit"
                    class="oceans-gaming-default-button-base oceans-gaming-transparent-button px-2 py-2 mt-2">
                    <a href="{{ route('event.show', $event->id) }}" class="submit-texts d-block"> Cancel </a>
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
