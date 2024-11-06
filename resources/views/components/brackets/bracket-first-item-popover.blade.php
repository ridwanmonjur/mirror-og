<!-- tournament-bracket-box.blade.php -->
@props([
    'position',
    'teamBanner',
    'teamId',
    'teamName',
    'roster' => []
])

<div
    class="tournament-bracket__box popover-parent position-relative {{ $position }} tournament bg-light"
    style="width: 32px; height: 28px;"
>
    <div class="popover-content d-none" style="opacity: 1; z-index: 999 !important;">
        <div class="popover-box row justify-content-start px-2 py-4" style="min-width: 400px; background: white !important;">
            <div class="col-12 col-lg-5 text-center border-dark" style="border-right: 2px solid black;">
                <div >
                    <img src="{{ asset('storage/' . $teamBanner) }}" alt="Team Banner" width="100"
                        height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                        class="popover-team-img object-fit-cover rounded-circle border border-dark border-2"
                    >
                    
                </div>
                <p class="mt-3 mb-4 py-0 fs-7"> {{$teamName}} </p>
            </div>
            <div class="col-12 col-lg-7">
                @if (isset($roster[0]))
                    <ul class="d-block ms-0 ps-0">
                        @foreach ($roster as $rosterItem)
                            <li class="d-inline">
                                <img width="30" height="30" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                                    src="{{ asset('storage/' . $rosterItem->user->userBanner) }}" alt="User Banner"
                                    class="mb-2 rounded-circle border border-dark border-2 object-fit-cover me-3">
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
        <img src="{{ asset('storage/' . $teamBanner) }}" width="100%" height="25"
            onerror="this.src='{{ asset('assets/images/404.png') }}';"
            class="{{ 'popover-button position-absolute d-none-when-hover object-fit-cover me-2 ' 
                    . 'data-position-'. $position }}" 
            alt="Team View"
            style="z-index: 99;"
            data-position="{{$position}}"
            onclick="reportModalShow(event);" 
        >
    @else 
        <span data-position="{{$position}}" class="replace_me_with_image popover-button"></span>
     @endif
    <span></span>
</div>