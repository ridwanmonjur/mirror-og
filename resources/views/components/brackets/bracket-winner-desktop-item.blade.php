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
        'tournament-bracket__box  position-relative mx-auto popover-button tournament border-dashed bg-light ' .  
        'data-position-'. $position1
    }}"
    data-position="{{$position1}}"
    style="width: 42px; height: 35px;"
>
    @if ($teamId1)
        <img src="{{ asset('storage/'.$teamBanner1) }}" width="40" height="33"
            onerror="this.src='{{ asset('assets/images/404.svg') }}';"
            class="cursor-pointer position-absolute w-100 h-100 z-99 object-fit-cover me-2" alt="Team View"
            data-position="{{$position1}}" 
            onclick="previousMatchReportShow(event);" 
        >
    @else 
        <small 
            data-position="{{$position1}}" 
            onclick="previousMatchReportShow(event);" 
            class="cursor-pointer ms-1 position-absolute py-3 replace_me_with_image" style="z-index: 99;"
        >{{$position1}}</small>
     @endif
    <span></span>
     
    <span></span>
</div>


<div
    class="{{ 'tournament-bracket__box border-dashed position-relative mx-auto popover-button tournament bg-light ' 
        . 'data-position-'. $position2
    }}"
    data-position="{{$position2}}"
    style="width: 42px; height: 35px;"
>
    @if ($teamId2)
        <img src="{{ asset('storage/'.$teamBanner2) }}" width="40" height="33"
            onerror="this.src='{{ asset('assets/images/404.svg') }}';"
            class="cursor-pointer position-absolute z-99 w-100 h-100 d-none-when-hover object-fit-cover me-2" alt="Team View"
            data-position="{{$position2}}" 
            onclick="previousMatchReportShow(event);" 
        >
        
    @else 
        <small class="cursor-pointer ms-1 py-2 position-absolute z-99 replace_me_with_image" 
            data-position="{{$position2}}" 
            onclick="previousMatchReportShow(event);" 
        >{{$position2}}</small>
     @endif
    <span></span>
     
    <span></span>
</div>