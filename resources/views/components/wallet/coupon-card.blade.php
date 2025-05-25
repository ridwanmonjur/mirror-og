@props([
    'coupon' => $coupon,
    'className' => $className,
])
<div class="{{ $className }} d-flex justify-content-center  ">
    <div class="coupon my-1 ">
        <div class="coupon__content w-100 row px-0 py-0 ">
            <div
                class="col-lg-4  d-none d-lg-flex flex-column justify-content-center py-0 ps-1 pe-1">
                <h5 class="text-white my-0 py-0">RM {{ $coupon['amount'] }} </h5>
                <small class="text-light">{{ $coupon['expires_at_human'] }}</small>
            </div>
            <div class="coupon__details py-2 h-100  col-lg-8  ">
                <small class="py-0 my-0 d-lg-none text-light">
                    <u>RM
                        {{ $coupon['amount'] }} {{ $coupon['expires_at_human'] }}
                    </u>
                </small>
                <div class="coupon__subtitle w-100  "    >
                    {{ $coupon['description'] ?? 'No description. No description. No description. No description. No description.' }}
                </div>
                <div class="coupon__subtitle mt-2 text-light">Code: {{ $coupon['code'] }}</div>
                <svg class="d-lg-none coupon__info-icon " viewBox="0 0 24 24">
                    <path
                        d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17A1.5,1.5 0 0,1 10.5,15.5A1.5,1.5 0 0,1 12,14A1.5,1.5 0 0,1 13.5,15.5A1.5,1.5 0 0,1 12,17M12,10.5A1.5,1.5 0 0,1 10.5,9A1.5,1.5 0 0,1 12,7.5A1.5,1.5 0 0,1 13.5,9A1.5,1.5 0 0,1 12,10.5Z" />
                </svg>
            </div>


        </div>

    </div>
    {{-- <span class="col-lg-1 d-none d-lg-flex flex-column justify-content-center  ">
            <svg class="coupon__info-icon " viewBox="0 0 24 24">
                <path
                    d="M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,17A1.5,1.5 0 0,1 10.5,15.5A1.5,1.5 0 0,1 12,14A1.5,1.5 0 0,1 13.5,15.5A1.5,1.5 0 0,1 12,17M12,10.5A1.5,1.5 0 0,1 10.5,9A1.5,1.5 0 0,1 12,7.5A1.5,1.5 0 0,1 13.5,9A1.5,1.5 0 0,1 12,10.5Z" />
            </svg>
        </span> --}}
</div>
