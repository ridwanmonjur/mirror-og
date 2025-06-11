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

<div class="{{ $className }} d-flex justify-content-center  " data-coupon-code = "{{$coupon['code']}}" onclick="populateCoupons(event)">
    <div class="me-2">
        <div class="coupon  position-relative my-1 ">
            <div class=" w-100 row px-0 py-0 ">
                <div class="col-lg-4  d-none d-lg-flex flex-column justify-content-center py-0 ps-1 pe-1">
                    <h5 class="text-white my-2 py-0">
                        RM {{ $coupon['amount'] }} 
                        @if ($redeemCount)
                            <small class="bg-secondary px-1">x{{ $redeemCount }}</small>
                        @endif 
                    </h5>
                    <small class="text-light fst-italic">{{ $coupon['expires_at_human'] }}</small>
                </div>
                <div class="coupon__details py-2  col-lg-8 pe-0 ">
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
                        <span class="d-lg-none coupon__info-icon py-2 ms-2" >
                            <svg width="16px" height="16px" viewBox="0 0 60.601004 60.601004" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#b1ccdd"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <defs></defs> <metadata> <rdf:rdf> <cc:work rdf:about=""> <dc:format>image/svg+xml</dc:format> <dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"></dc:type> <dc:title>情報コーナー; Information</dc:title> <dc:source>http://www.ecomo.or.jp/barrierfree/pictogram/data/zukigo_panfu_jis110.pdf</dc:source> </cc:work> </rdf:rdf> </metadata> <path d="m 0,0 c 0,12.079 -9.793,21.872 -21.872,21.872 -12.079,0 -21.871,-9.793 -21.871,-21.872 0,-12.079 9.792,-21.872 21.871,-21.872 C -9.793,-21.872 0,-12.079 0,0 Z" style="stroke-linejoin:miter;stroke-opacity:1;fill-opacity:1;stroke:#b1ccdd;stroke-linecap:butt;stroke-miterlimit:4;stroke-dasharray:none;stroke-width:0.48500001;fill:#ffffff" transform="matrix(0.000000,1.250000,1.250000,0.000000,30.300503,57.639880)"></path> <path d="m 34.961753,47.76613 0,-23.573752 -13.182501,0 0,2.575 3.85875,0 0,20.998752 -3.85875,0 0,2.57375 17.041251,0 0,-2.57375 -3.85875,0" style="fill:#b1ccdd;fill-opacity:1;fill-rule:nonzero;stroke:none"></path> <path d="m 30.299253,21.526128 c 3.1975,0 5.78875,-2.59125 5.78875,-5.78875 0,-3.197501 -2.59125,-5.7900009 -5.78875,-5.7900009 -3.19875,0 -5.79,2.5924999 -5.79,5.7900009 0,3.1975 2.59125,5.78875 5.79,5.78875" style="fill:#b1ccdd;fill-opacity:1;fill-rule:nonzero;stroke:none"></path> </g></svg>
                        </span>
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
</div>
