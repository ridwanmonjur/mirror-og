<div class="input-group card-select-view d-none">
    <button type="button" role="button" onclick="onChoosePayment(event, 'card', 'visa');"
        class="text-left oceans-gaming-default-button-base justify-content-start oceans-gaming-transparent-button py-1 ps-3 pe-3 w-100 mb-2 payment-element">
        <div class="d-flex justify-content-between align-items-center">
            <span>
                <img class="d-inline pe-3" width="60" height="30"
                    src="{{ asset('/assets/images/logo/visa-card-logo.jpg') }}">
                <span> **** **** **** 1234 </span>
            </span>
            <i>Default</i>
        </div>
    </button>
    <button type="button" role="button" onclick="onChoosePayment(event, 'card', 'master');"
        class="text-left oceans-gaming-default-button-base justify-content-start oceans-gaming-transparent-button py-1 ps-3 pe-3 w-100 mb-2 payment-element">
        <div class="d-flex justify-content-between align-items-center">
            <span>
                <img class="d-inline pe-3" width="70" height="30"
                    src="{{ asset('/assets/images/logo/master-card-logo.png') }}">
                <span> **** **** **** 5678 </span>
            </span>
            <div> </div>
        </div>
    </button>
    <button type="button" role="button" onclick="onChoosePayment(event, 'card', 'any');"
        class="text-left oceans-gaming-default-button-base justify-content-start oceans-gaming-transparent-button py-1 ps-3 pe-3 w-100 payment-element">
        <div class="d-flex justify-content-between align-items-center">
            <span>
                <img class="d-inline pe-3" width="90" height="30"
                    src="{{ asset('/assets/images/logo/visa-master-card-logo.png') }}">
                <span> Add a credit or debit card </span>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-chevron-right hover-bigger" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
            </svg>
        </div>
    </button>
</div>
<div class="input-group card-focus-view" onclick="selectCards();">
    <button type="button" role="button"
        class="text-left oceans-gaming-default-button-base justify-content-start oceans-gaming-transparent-button py-1 ps-3 pe-3 w-100">
        <div class="d-flex justify-content-between align-items-center">
            <span>
                <img class="d-inline pe-3" width="90" height="30"
                    src="{{ asset('/assets/images/logo/visa-master-card-logo.png') }}">
                <span> Add a credit or debit card </span>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-chevron-right hover-bigger" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708" />
            </svg>
        </div>
    </button>
</div>
