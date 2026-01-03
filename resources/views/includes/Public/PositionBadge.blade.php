@php
 if (!function_exists('getMedalSvg')) {

    function getMedalSvg($position)
    {
        // Default SVG for positions beyond 5
        $defaultSvg =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 32" width="36" height="32">
        <circle cx="18" cy="16" r="14" fill="transparent"/>
        <circle cx="18" cy="16" r="13" fill="transparent" stroke="#dee2e6" stroke-width="1.5"/>
        <path d="M8,24 L5,28 L8,32 L18,29 L28,32 L31,28 L28,24" fill="#dee2e6"/>
        <text x="18" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="#000">' .
            'P' .
            '</text>
        <path d="M15,16 Q18,12 21,16" fill="none" stroke="#dee2e6" stroke-width="0.5" opacity="0.4"/>
    </svg>';

        // Array of medal colors and properties
        $medals = [
            1 => ['fill' => 'transparent', 'stroke' => '#FFCA28', 'color' => '#FFCA28'],
            2 => ['fill' => 'transparent', 'stroke' => '#BDBDBD', 'color' => '#757575'],
            3 => ['fill' => 'transparent', 'stroke' => '#D4795B', 'color' => '#D4795B'],
            4 => ['fill' => 'transparent', 'stroke' => '#9333EA', 'color' => '#9333EA'],
            5 => ['fill' => 'transparent', 'stroke' => '#10B981', 'color' => '#10B981'],
        ];

        // Return default for positions beyond 5
        if (!isset($medals[$position])) {
            return $defaultSvg;
        }

        // Generate medal SVG with position number
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 32" width="36" height="32">
        <circle cx="18" cy="16" r="14" fill="' .
            $medals[$position]['fill'] .
            '"/>
        <circle cx="18" cy="16" r="13" fill="' .
            $medals[$position]['fill'] .
            '" stroke="' .
            $medals[$position]['stroke'] .
            '" stroke-width="0.8"/>
        <path d="M8,24 L5,28 L8,32 L18,29 L28,32 L31,28 L28,24" fill="' .
            $medals[$position]['stroke'] .
            '"/>
        <text x="18" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="' .
            $medals[$position]['color'] .
            '">' .
            $position .
            '</text>
        <path d="M15,16 Q18,12 21,16" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.4"/>
    </svg>';
    }
 }
@endphp
<div class="card border-0 py-0 my-0 mx-auto" style="background: none; width: 90%;">
        <!-- Header -->
        <!-- Event List -->
        @if (isset($joinEventAndTeamList[0]))
            <div class="d-flex flex-column gap-3">
                @foreach ($joinEventAndTeamList as $event)
                    <div class="card border-2 bg-white hover-shadow-sm position-relative"
                        onmouseover="this.style.transform='translateY(-2px)'" 
                        onmouseout="this.style.transform='translateY(0)'"
                    >
                        <div class="card-body  ">
                            <div class="row align-items-center">
                                <!-- Left side with image and event details -->
                                <div class="col-12 col-lg-9 d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img src="{{ '/storage' . '/' . $event->eventBanner }}" {!! bldImgF() !!}
                                            class="rounded-circle object-fit-cover border border-secondary"
                                            style="width: 48px; height: 48px;" alt="Event banner">

                                    </div>
                                    <div class="d-flex flex-column justify-content-center my-2">
                                        <a href="{{ route('public.event.view', ['id' => $event->id, 'title' => $event->slug ] ) }}"
                                             title="{{ $event->eventName }} Esports Tournament"
                                        >

                                            <h6 class="mb-1 text-wrap font-poppins py-0">{{ $event->eventName }}</h6>
                                            <div class="text-body-secondary py-1 text-wrap ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="16"
                                                    fill="currentColor" class="bi me-1 bi-person-circle"
                                                    viewBox="0 0 16 16">
                                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                                    <path fill-rule="evenodd"
                                                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                                                </svg>
                                                {{ $event->teamName }}
                                                @if (isset($event->member_limit) && $event->member_limit == 1)
                                                    <span class="badge bg-primary ms-2">Solo</span>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <!-- Right side with position and chevron -->
                                <div class="col-12 col-lg-3 d-flex justify-content-start my-2 align-items-center gap-3">
                                    @if ($event->position)
                                        <div class="d-flex align-items-center fw-semibold text-body-secondary small">
                                            <span class="me-2">{!! getMedalSvg($event->position) !!} </span>
                                            {{ bldOrdinal($event->position) }}
                                        </div>
                                    @else
                                        <span class="text-body-secondary small">-</span>
                                    @endif
                                    <a href="{{ route('public.event.view', $event->id) }}"
                                        title="{{ $event->eventName }} Esports Tournament"
                                        class="text-decoration-none text-body-secondary">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
        @else
            <div >
                <div class="text-start py-4">
                    <svg class="ms-4" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="d-inline text-body-secondary mb-0">No positions yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
