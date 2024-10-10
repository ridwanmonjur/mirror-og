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
    'status'
])
 <div class="{{'popover-middle-content text-center d-none py-0 px-0 ' . $position1 . ' ' . $position2 }}" 
    style="opacity: 1; z-index: 999 !important; "
>
   
    <div class="popover-box row justify-content-start border border-dark border rounded px-2 py-2" 
        style="background-color: white; min-width: 400px !important;"
    >
        <div class="text-center text-uppercase" >
            <h5> {{$winner_next_position}} </h5>
            <p class="text-success">{{$status}}</p>
        </div>
        <div class="col-12 col-lg-4">
            <div>
                <img src="{{ asset('storage/' . $teamBanner1) }}" alt="Team Banner" width="100"
                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    class="border border-4 popover-content-img rounded-circle object-fit-cover"
                >
            </div>
             <p class="mt-1 mb-0 py-0">{{$teamName1}}</p>
            <div class="mt-1 mb-2 py-0">
                <div class="d-inline-block rounded-circle me-3 bg-secondary" style="width: 6px; height: 6px;"></div>
                <div class="d-inline-block rounded-circle bg-secondary" style="width: 6px; height: 6px;"></div> 
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-center align-items-center h-100">
                <h1 class="pe-4">0</h1>
                <h1>-</h3>
                <h1 class="ps-4">0</h1>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div >
                <img src="{{ asset('storage/' . $teamBanner2) }}" alt="Team Banner" width="100"
                    height="100" onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    class="border border-4 popover-content-img rounded-circle object-fit-cover"
                >
            </div>
            <p class="mt-1 mb-2 py-0">{{$teamName2}}</p>
            <div class="mt-1 mb-2 py-0 ">
                <div class="d-inline-block rounded-circle me-3 bg-secondary" style="width: 6px; height: 6px;"></div>
                <div class="d-inline-block rounded-circle  bg-secondary" style="width: 6px; height: 6px;"></div> 
            </div>
        </div>
    </div>
</div>