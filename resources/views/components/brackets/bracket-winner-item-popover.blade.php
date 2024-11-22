{{-- <!-- tournament-bracket-box.blade.php -->
@props([
    'position',
    'teamBanner',
    'teamId',
    'roster' => []
])

<div
    class="tournament-bracket__box popover-parent position-relative {{ $position }} tournament bg-light"
    style="width: 32px; height: 28px;"
>
    <div class="popover-content d-none" style="opacity: 1; z-index: 999 !important;">
        <div class="popover-box row justify-content-start px-2 pt-2 pb-2" style="min-width: 400px; background: white !important;">
            <div class="col-12 col-lg-5 text-end">
                <div class="text-end">
                    <img src="{{ bladeImageNull($teamBanner) }}" alt="Team Banner" width="100%"
                        height="100%" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                        class="popover-img popover-content-img"
                    >
                </div>
                <small>{{ $position }}</small>
            </div>
            <div class="roster-container col-12 col-lg-7">
                @if (isset($roster[0]))
                    <ul class="d-block ms-0 ps-0">
                        @foreach ($roster as $rosterItem)
                            <li class="d-inline">
                                <img width="25" height="25" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    src="{{ bladeImageNull($rosterItem->user->userBanner) }}" alt="User Banner"
                                    class="mb-2 rounded-circle object-fit-cover me-3">
                                {{ $rosterItem->user->name }}
                            </li>
                            <br>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">The team roster is empty.</p>
                @endif
            </div>
        </div>
    </div>
    @if ($teamId)
        <img src="{{ bladeImageNull($teamBanner) }}" width="100%" height="25"
            onerror="this.src='{{ asset('assets/images/404.png') }}';"
            class="popover-button position-absolute d-none-when-hover object-fit-cover me-2" alt="Team View"
            style="z-index: 99;">
    @else
        <span class="replace_me_with_image"> </span>
    @endif
    <span></span>
</div> --}}