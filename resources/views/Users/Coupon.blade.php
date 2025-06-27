@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/settings.js'])
</head>
@section('body-class', 'wallet')

@section('content')
    @include('includes.Navbar')

    <main class="px-1"   class="row" >
        <div class="row my-2 px-5 py-2"> 
            <h3 class="col-12 col-md-6 my-2 py-0 text-start">My Coupons</h3>
            <a 
                href="{{route('wallet.dashboard')}}" 
                style="z-index: 99;"
                id="main-nav"  class="cursor-pointer my-2 d py-0 position-relative col-12 col-md-6 text-start text-md-end">
                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                    </svg>
                </span>
                <span>Go back</span>
            </a>
        </div>
        
        
        <div class="d-none mx-auto px-0 container-main min-h-85vh"
            id="wallet-view-coupons"
        >
            <div class="center-container mx-auto" 
            >
                <div class="card px-3 py-2 border border-2 mx-auto border-secondary  min-w-95vw rounded-30px">
                    <div class="card-body px-2 py-2">
                        <div class="row ">
                            <h3 class="col-6 text-start transaction-history__title ">My Coupons {{$code}} {{$emptyCode}}</h3>
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


        <div 
            class="d-none mx-auto px-0 container-main min-h-85vh" id="wallet-redeem-coupons"
        >
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-95-lg-50 rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class=" px-2 py-2">
                        <div class=" px-1 py-0 my-0">
                            <h5 class="text-center mt-2 mb-3 text-secondary">Have a coupon?</h5>
                        </div>
                        <div class="mt-3">
                            <!-- Topup Form -->
                            <div class="my-2">
                                <div id="coupon-form" v-scope="CouponStatusComponent()">
                                    
                                    <form v-on:submit="submitCoupon">
                                        <div class="w-50 mx-auto text-center my-3">

                                            <label :class="statusClass" v-if="statusLabel" class="mb-0" >@{{ statusLabel }}</label>
                                            <label :class="statusClass" v-if="message" class="mb-2" >@{{ message }}</label>

                                            <!-- Status Icon and Label -->
                                            <div class="mb-2 input-group">
                                                <label 
                                                    :class="{ ' text-success border-success ': status === 'success', ' border-red text-red ': status === 'error' }"
                                                    v-html="statusIcon" class="input-group-text">
                                                </label>
                                                <input 
                                                    id="coupon_code"
                                                    type="text" 
                                                    spellcheck="false"
                                                    name="coupon_code"
                                                    class="px-4 form-control border-secondary text-start " 
                                                    :class="{ ' border-success text-success ': status === 'success', ' border-red text-red ': status === 'error' }"
                                                    placeholder="XXXXXXX"
                                                    :disabled="isSubmitting"
                                                    required
                                                >
                                            </div>
                                            
                                            
                                        </div>
                                        
                                        <div class="d-flex justify-content-around w-75 mb-4 mx-auto">
                                            <button 
                                                onclick="openTab('wallet-view-coupons')" 
                                                type="button"
                                                class="btn border-secondary text-dark rounded-pill"
                                                :disabled="isSubmitting">
                                                Cancel
                                            </button>

                                            <button 
                                                type="submit"
                                                class="btn btn-primary text-light rounded-pill"
                                                :disabled="isSubmitting ">
                                                <span v-if="isSubmitting">
                                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                    Processing...
                                                </span>
                                                <span v-else>Confirm</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        

        
    <input type="hidden" id="wallet" value="{{ json_encode($wallet) }}">

    </main>
    <script>
        addOnLoad(()=> {
            const urlParams = new URLSearchParams(window.location.search);
            const code = urlParams.get('code');
            const empty = urlParams.get('empty');
            const container = document.getElementById('wallet-view-coupons');
            

            if ((code && code.trim() !== '') || (empty && empty.trim() !== '')) {
                if (container) {
                    container.classList.add('d-none');
                }

                const couponContainer = document.getElementById('wallet-redeem-coupons');
                if (couponContainer) {
                    couponContainer.classList.remove('d-none');
                }
                if (code) {
                    let couponInput = document.getElementById('coupon_code');
                    if (couponInput) {
                        couponInput.value = code;
                    }
                }
            } else {
                if (container) {
                    container.classList.remove('d-none');
                }
            }
        })
    </script>

@endsection
