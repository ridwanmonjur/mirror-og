<div class="grid-2-columns mx-4">
    <div class="mx-2">
        <h4>Payment Method</h4>
        <br>
        <form id="card-form">
            @csrf
            <div class="form-group form-group2">
                <label for="card-name" class="">Your name</label>
                <input type="text" name="name" id="card-name" class="">
            </div>
            <div class="form-group form-group2">
                <label for="email" class="">Email</label>
                <input type="email" name="email" id="email" class="">
            </div>
            <div class="form-group form-group2">
                <label for="card" class="">Card details</label>
                <div class="form-group form-group2">
                    <div id="card"></div>
                </div>
            </div>
            <button type="submit" class="oceans-gaming-default-button">
                <div class="submit-texts"> Pay 👉 </div>
                <div class="spinner-border d-none" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </button>
        </form>
    </div>
    <div class="mx-2">
        <h4>Payment Summary</h4>
        <br>
        <div>
            <div>Event Categories</div>
            <div class="ml-3">Game: <span id="paymentType">{{ $event->game->gameTitle }}</span></div>
            <div class="ml-3">Type: <span id="paymentType">{{ $event->type->eventType }}</span></div>
            <div class="ml-3">Tier: <span id="paymentTier">{{ $event->tier->eventTier }}</span></div>
            <div class="ml-3">Region: <span id="paymentTier">South East Asia (SEA)</span></div>
            <br>
            @php
                $entryFee = $event->tier->tierEntryFee * 1000;
                $finalFee = $entryFee + $entryFee * 0.2;
            @endphp
            <div class="flexbox w-75">
                <span>Subtotal</span>
                <span id="subtotal">RM
                    <span class="transform-number"> {{ $entryFee }} </span>
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
                    <span class="transform-number">{{ $finalFee }} </span>
                </h5>
            </div>
            <br>
            <div>Promo Code</div>
            <div class="form-group w-75 d-flex">
                <input type="text" name="name" id="" class="">
                <div class="d-inline-block px-2"></div>
                <button class="px-3 oceans-gaming-default-button" style="background-color: #95ADBD;">
                    <span> Apply </span>
                    <span class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </span>
                </button>
            </div>
            <div class="d-flex justify-content-center w-75">
                <button type="submit" class="oceans-gaming-default-button-base oceans-gaming-gray-button px-4 py-3 mt-2">
                    <div class="submit-texts"> Confirm & Pay </div>
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
            <div class="d-flex justify-content-center w-75">
                <button type="submit" class="oceans-gaming-default-button-base oceans-gaming-transparent-button px-2 py-2 mt-2">
                    <div class="submit-texts"> Cancel </div>
                    <div class="spinner-border d-none" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
