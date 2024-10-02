<!-- tournament-bracket-box.blade.php -->
@props([
    'position1',
    'teamBanner1',
    'teamId1',
    'score1',
    'position2',
    'teamBanner2',
    'teamId2',
    'score2',
])
 <div class="{{'popover-middle-content d-none ' . $position1 . ' ' . $position2 }}" style="opacity: 1; z-index: 999 !important;">
    <div class="popover-box row justify-content-start px-2 pt-2 pb-2" style="min-width: 400px; background: white !important;">
        <div class="col-12 col-lg-5 text-end">
            <div class="text-end">
                <img src="{{ asset('storage/' . $teamBanner1) }}" alt="Team Banner" width="100%"
                    height="100%" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    class="popover-content-img"
                >
            </div>
            <small>{{ $position1 }}</small>
        </div>
        <div class="col-12 col-lg-7">
            <div class="text-end">
                <img src="{{ asset('storage/' . $teamBanner2) }}" alt="Team Banner" width="100%"
                    height="100%" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    class="popover-content-img"
                >
            </div>
            <small>{{ $position2 }}</small>
        </div>
    </div>
</div>
<div
    class="tournament-bracket__box  position-relative mx-auto popover-button tournament bg-light"
    data-position="{{$position1}}"
    style="width: 35px; height: 28px;"
>
    @if ($teamId1)
        <img src="{{ asset('storage/' . $teamBanner1) }}" width="100%" height="25"
            onerror="this.src='{{ asset('assets/images/404.png') }}';"
            class=" position-absolute d-none-when-hover object-fit-cover me-2" alt="Team View"
            style="z-index: 99;">
    @else 
        <small class="ms-1 replace_me_with_image">{{$position1}}</small>
     @endif
    <span></span>
     
    <span></span>
</div>
<div
    class="tournament-bracket__box  position-relative mx-auto popover-button tournament bg-light"
    style="width: 35px; height: 28px;" data-position="{{$position2}}"
>
    @if ($teamId2)
        <img src="{{ asset('storage/' . $teamBanner2) }}" width="100%" height="25"
            onerror="this.src='{{ asset('assets/images/404.png') }}';"
            class="position-absolute d-none-when-hover object-fit-cover me-2" alt="Team View"
            style="z-index: 99;">
    @else 
        <small class="ms-1 replace_me_with_image">{{$position2}}</small>
     @endif
</div>