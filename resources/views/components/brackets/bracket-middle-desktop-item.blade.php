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
    style="width: 35px; height: 28px;"
>
    @if ($teamId1)
        <img src="{{ asset('storage/'.$teamBanner1) }}" 
            onerror="this.src='{{ asset('assets/images/404.svg') }}';"
            class="cursor-pointer position-absolute w-100 h-100 object-fit-cover z-99 me-2" alt="Team View"
            data-position="{{$position1}}" 
            onclick="reportModalShow(event);" 
        >
    @else 
        <small 
            data-position="{{$position1}}" 
            onclick="reportModalShow(event);" 
            class="cursor-pointer ms-1 user-select-none position-absolute  replace_me_with_image" style="z-index: 99;"
        >{{$position1}}</small>
     @endif
    <span></span>
     
    <span></span>
</div>


<div
    class="{{ 'tournament-bracket__box  position-relative mx-auto popover-button tournament bg-light ' 
        . 'data-position-'. $position2
    }}"
    data-position="{{$position2}}"
    style="width: 35px; height: 28px;"
>
    @if ($teamId2)
        <img src="{{ asset('storage/'.$teamBanner2) }}" 
            onerror="this.src='{{ asset('assets/images/404.svg') }}';"
            class="cursor-pointer position-absolute w-100 h-100 z-99 d-none-when-hover object-fit-cover me-2" alt="Team View"
            data-position="{{$position2}}" 
            onclick="reportModalShow(event);" 
        >
        
    @else 
        <small class="cursor-pointer ms-1 user-select-none position-absolute replace_me_with_image" 
            style="z-index: 99;"
            data-position="{{$position2}}" 
            onclick="reportModalShow(event);" 
        >{{$position2}}</small>
     @endif
    <span></span>
     
    <span></span>
</div>