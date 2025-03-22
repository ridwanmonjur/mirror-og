@php
    use Carbon\Carbon;
    if (!function_exists('getMedalSvg')) {
        function getMedalSvg($position)
        {
            // Default SVG for positions beyond 5
            $defaultSvg =
                '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 32" width="36" height="32">
                            <circle cx="18" cy="16" r="14" fill="#1E90FF"/>
                            <circle cx="18" cy="16" r="13" fill="#1E90FF" stroke="#0066CC" stroke-width="0.8"/>
                            <path d="M8,24 L5,28 L8,32 L18,29 L28,32 L31,28 L28,24" fill="#0066CC"/>
                            <text x="18" y="20" text-anchor="middle" font-size="12" font-weight="bold" fill="white">' .
                'P' .
                '</text>
                            <path d="M15,16 Q18,12 21,16" fill="none" stroke="#FFFFFF" stroke-width="0.5" opacity="0.4"/>
                        </svg>';

            // Array of medal colors and properties
            $medals = [
                1 => ['fill' => '#FFD700', 'stroke' => '#DAA520', 'color' => 'black'],
                2 => ['fill' => '#C0C0C0', 'stroke' => '#808080', 'color' => 'white'],
                3 => ['fill' => '#CD7F32', 'stroke' => '#8B4513', 'color' => 'white'],
                4 => ['fill' => '#9933FF', 'stroke' => '#6600CC', 'color' => 'white'],
                5 => ['fill' => '#009933', 'stroke' => '#006622', 'color' => 'white'],
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
<h5 class="mb-3"><u>Result</u></h5>
<div class=" pb-4 outer-tab mx-0" id="current-positions">
    <div class="card border-0 py-0 my-0 mx-0" style="background: none;">
        @if (isset($joinEventAndTeamList[0]))
            <div class="d-flex flex-column gap-3">
                @foreach ($joinEventAndTeamList as $joinEventAndTeam)
                    <div class="card border-2 bg-white hover-shadow-sm position-relative">
                        onmouseover="this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.transform='translateY(0)'"
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-12 col-lg-10 my-1 d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img src="{{ '/storage' . '/' . $joinEventAndTeam->teamBanner }}"
                                            {!! trustedBladeHandleImageFailure() !!}
                                            class="rounded-circle object-fit-cover border border-primary"
                                            style="width: 48px; height: 48px;" alt="Team banner">
                                    </div>
                                    <div class="d-inline-flex text-wrap flex-column justify-content-center">
                                        <h6 class="mb-1 text-wrap py-0">{{ $joinEventAndTeam->teamName }} <span
                                                class="ms-2"
                                                style="font-size: 1.5rem;">{{ $joinEventAndTeam->country_flag }}</span>
                                        </h6>

                                        <div class="text-body-secondary text-muted text-wrap">
                                            <span>
                                                {{ is_null($joinEventAndTeam->created_at) ? '' : Carbon::parse($joinEventAndTeam->created_at)->diffForHumans() }}
                                            </span>

                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-lg-2 my-1 d-flex justify-content-start align-items-center gap-1">
                                    @if ($joinEventAndTeam->position)
                                        <div class="d-flex align-items-center text-body-secondary small">
                                            <span class="me-1">{!! getMedalSvg($joinEventAndTeam->position) !!}</span>
                                            {{ bladeOrdinalPrefix($joinEventAndTeam->position) }}
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-body-secondary small">
                                            <span class="me-1">{!! getMedalSvg($joinEventAndTeam->position) !!}</span>
                                        </div>
                                    @endif

                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <a class="position-absolute top-0 start-0 w-100 h-100"
                                    href="{{ route('public.team.view', ['id' => $team->id]) }}"></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div>
                <div class="text-start pt-2">
                    <svg class="ms-4" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <p class="d-inline text-body-secondary text-center  mb-0">No results yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
