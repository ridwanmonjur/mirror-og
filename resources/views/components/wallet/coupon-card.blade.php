@props([
    'coupon' => $coupon,
    'className' => $className,
])

@php
    if ($coupon['is_public']) {
        $userCoupon = isset($coupon['user_coupons'][0]) ? $coupon['user_coupons'][0] : null;
        $redeemCount = $userCoupon ? $userCoupon['redeemable_count'] : 1;
    } else {
        $userCoupon = isset($coupon['user_coupons'][0]) ? $coupon['user_coupons'][0] : null;
        $redeemCount = $userCoupon ? $userCoupon['redeemable_count'] : 0;
    }
   
@endphp
 <a class="{{ $className }} d-flex justify-content-center  " data-coupon-code = "{{$coupon['code']}}" 
    href="{{route('wallet.coupons', ['code' => $coupon['code'] ]) }}"
>
   
        <div class="me-2">
            <div class="coupon  position-relative my-1 ">
                <div class=" w-100 row px-0 py-0 ">
                    <div class="col-lg-5 col-xl-4 d-none d-lg-flex flex-column justify-content-center py-0 ps-1 pe-1">
                        <h5 class="text-white fs-7 position-relative my-2 py-0 " style="z-index: 4;">
                            RM {{ $coupon['amount'] }} 
                            @if ($redeemCount)
                                <small class="bg-secondary badge badge-secondary  px-1 position-absolute right-0 ms-1 rounded-circle " style="top: 0px; font-size: 12px;">x{{ $redeemCount }}</small>
                            @endif 
                        </h5>
                        <small class="text-light fst-italic">{{ $coupon['expires_at_human'] }}</small>
                    </div>
                    <div class="coupon__details py-2  col-lg-7 col-xl-8 pe-0 ">
                        <div class="py-0 my-0 fs-5 d-lg-none text-light mb-2">
                            <u>RM
                                {{ $coupon['amount'] }} ({{ $coupon['expires_at_human'] }}) <br>
                            </u>
                        </div>
                        <div class="coupon__subtitle w-100  "
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="{{ $coupon['description'] ?? 'No description.' }}"
                        >
                            {{ $coupon['description'] ?? 'No description.' }}
                            
                        </div>
                        <div class="coupon__subtitle mt-2  text-light">
                            Code: {{ $coupon['code'] }}
                        
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <span  data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="{{ $coupon['description'] ?? 'No description.' }}"
            class=" d-none cursor-pointer float-start d-lg-flex flex-column justify-content-center  ">
            <svg height="20px" width="20px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#b1ccdd;} </style> <g> <path class="st0" d="M256,0C114.615,0,0,114.615,0,256s114.615,256,256,256s256-114.615,256-256S397.385,0,256,0z M256,86.069 c28.463,0,51.538,23.074,51.538,51.538c0,28.464-23.074,51.538-51.538,51.538c-28.463,0-51.538-23.074-51.538-51.538 C204.462,109.143,227.537,86.069,256,86.069z M310.491,425.931H201.51v-43.593h35.667V276.329H215.38v-43.593h65.389v3.963v39.63 v106.009h29.722V425.931z"></path> </g> </g></svg>
        </span>
</a>
