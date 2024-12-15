<!-- tournament-bracket-box.blade.php -->
@props([
    'currentPosition',
    'position1',
    'teamBanner1',
    'teamName1',
    'teamId1',
    'position2',
    'teamBanner2',
    'teamId2',
    'teamName2',
    'winner_next_position',
    'loser_next_position',
])
 <div class="{{'popover-middle-content text-center d-none py-0 px-0 ' . $position1 . ' ' . $position2 }}" 
    style="opacity: 1; z-index: 999 !important; "
>
   
    <div class="popover-box row justify-content-start border border-dark border rounded px-2 py-2" 
        style="background-color: white; min-width: 400px !important;"
    >
        <div class="text-center text-uppercase" >
            <h5> {{$winner_next_position}} </h5>
            <p class="text-success status-box">UPCOMING</p>
        </div>
        <div class="col-12 col-lg-4">
            <div>
                <img src="{{ bladeImageNull($teamBanner1) }}" alt="Team Banner" width="100"
                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    data-position="{{$position1.'middle'}}"
                    class="border border-4 popover-img popover-content-img rounded-circle object-fit-cover"
                >
            </div>
             <p class="mt-2 mb-0 py-0 text-center popover-title" data-position="{{$position1. 'middle'}}">{{$teamName1}}</p>
            <div class="{{ 'mt-2 mb-4 py-0 d-flex justify-content-center dotted-score-container ' . $position1 . ' ' . $position2  }}">
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-center align-items-center h-100">
                <h1 class="pe-4 dotted-score-box">0</h1>
                <h1>-</h3>
                <h1 class="ps-4 dotted-score-box">0</h1>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div >
                <img src="{{ bladeImageNull($teamBanner2) }}" alt="Team Banner" width="100"
                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    data-position="{{$position2. 'middle'}}" class="border border-4 popover-img popover-content-img rounded-circle object-fit-cover"
                >
            </div>
            <p class="mt-2 mb-2 py-0 text-center popover-title" data-position="{{$position2. 'middle'}}">{{$teamName2}}</p>
            <div class="{{ 'mt-2 mb-4 py-0 d-flex justify-content-center dotted-score-container ' . $position1 . ' ' . $position2 }}">
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>
            </div>
        </div>
    </div>
</div>