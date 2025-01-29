<!-- tournament-bracket-box.blade.php -->
@props([
    'position1',
    'teamBanner1',
    'teamId1',
    'position2',
    'teamBanner2',
    'teamId2',
])


<div
    class="{{ 
        'tournament-bracket__box  position-relative mx-auto popover-button tournament bg-light ' .  
        'data-position-'. $position1
    }}"
    data-position="{{$position1}}"
    style="width: 42px; height: 35px;"
>
    @if ($teamId1)
        <img src="{{ bladeImageNull($teamBanner1) }}" width="40" height="33"
            onerror="this.src='{{ asset('assets/images/404.png') }}';"
            class="cursor-pointer position-absolute w-100 h-100 object-fit-cover me-2" alt="Team View"
            style="z-index: 99;"
            data-position="{{$position1}}" 
            onclick="reportModalShow(event);" 
        >
    @else 
        <small 
            data-position="{{$position1}}" 
            onclick="reportModalShow(event);" 
            class="cursor-pointer ms-1 position-absolute  cursor-pointer me-3 py-2 replace_me_with_image" style="z-index: 99;"
        >&nbsp;&nbsp;&nbsp;?</small>
     @endif
    <span></span>
     
    <span></span>
</div>

