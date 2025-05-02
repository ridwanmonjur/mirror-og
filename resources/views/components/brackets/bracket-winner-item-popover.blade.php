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
    'deadline',
    'isTeam1',
    'isTeam2',
    'isOrg'

])
 <div  
    style="opacity: 1; z-index: 999 !important; "
     @class([" popover-middle-content text-center d-none py-0 px-0 " . $position1 . ' ' . $position2, 
        ' warning ' => $deadline['has_started'] && !$deadline['has_ended'] && ($isTeam1 || $isTeam2)
     ]); 
      data-position="{{$position1}}"
    style="width: 35px; height: 28px;"
>
   
    <div class="popover-box row justify-content-start border border-dark border px-2 py-2" 
        style="min-width: 400px !important; background-color: #30405e; opacity: 1 !important; border-radius: 20px;"
    >
        <div class="text-center text-uppercase" >
            <h5 class="text-light mt-2"> 
                <svg height="30px" width="30px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#2e4b59"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <polygon style="fill:#F0C419;" points="370.759,229.517 256,79.448 141.241,229.517 26.483,79.448 88.276,344.276 256,344.276 423.724,344.276 485.517,79.448 "></polygon> <path style="fill:#FFFFFF;" d="M291.31,238.345c0,34.127-15.81,61.793-35.31,61.793s-35.31-27.666-35.31-61.793 s15.81-61.793,35.31-61.793S291.31,204.217,291.31,238.345"></path> <path style="fill:#E64C3C;" d="M256,264.828c-4.873,0-8.828-3.955-8.828-8.828v-35.319c0-4.882,3.955-8.828,8.828-8.828 s8.828,3.946,8.828,8.828V256C264.828,260.873,260.873,264.828,256,264.828"></path> <g> <path style="fill:#BA941C;" d="M450.207,379.586H61.793c-9.754,0-17.655-7.901-17.655-17.655c0-9.755,7.901-17.655,17.655-17.655 h388.414c9.754,0,17.655,7.901,17.655,17.655C467.862,371.686,459.961,379.586,450.207,379.586"></path> <path style="fill:#BA941C;" d="M450.207,485.517H61.793c-9.754,0-17.655-7.901-17.655-17.655s7.901-17.655,17.655-17.655h388.414 c9.754,0,17.655,7.901,17.655,17.655S459.961,485.517,450.207,485.517"></path> </g> <polygon style="fill:#F0C419;" points="70.621,450.207 441.379,450.207 441.379,379.586 70.621,379.586 "></polygon> <g> <path style="fill:#BA941C;" d="M220.69,423.724h-70.621c-4.873,0-8.828-3.955-8.828-8.828s3.955-8.828,8.828-8.828h70.621 c4.873,0,8.828,3.955,8.828,8.828S225.562,423.724,220.69,423.724"></path> <path style="fill:#BA941C;" d="M361.931,423.724H291.31c-4.873,0-8.828-3.955-8.828-8.828s3.955-8.828,8.828-8.828h70.621 c4.873,0,8.828,3.955,8.828,8.828S366.804,423.724,361.931,423.724"></path> <path style="fill:#BA941C;" d="M264.828,414.897c0,4.873-3.955,8.828-8.828,8.828s-8.828-3.955-8.828-8.828 s3.955-8.828,8.828-8.828S264.828,410.024,264.828,414.897"></path> <path style="fill:#BA941C;" d="M406.069,414.897c0,4.873-3.955,8.828-8.828,8.828c-4.873,0-8.828-3.955-8.828-8.828 s3.955-8.828,8.828-8.828C402.114,406.069,406.069,410.024,406.069,414.897"></path> <path style="fill:#BA941C;" d="M105.931,414.897c0-4.873,3.955-8.828,8.828-8.828s8.828,3.955,8.828,8.828 s-3.955,8.828-8.828,8.828S105.931,419.769,105.931,414.897"></path> <path style="fill:#BA941C;" d="M52.966,52.966c0,14.627-11.855,26.483-26.483,26.483S0,67.593,0,52.966 s11.855-26.483,26.483-26.483S52.966,38.338,52.966,52.966"></path> <path style="fill:#BA941C;" d="M282.483,52.966c0,14.627-11.855,26.483-26.483,26.483c-14.627,0-26.483-11.855-26.483-26.483 S241.373,26.483,256,26.483C270.627,26.483,282.483,38.338,282.483,52.966"></path> <path style="fill:#BA941C;" d="M512,52.966c0,14.627-11.855,26.483-26.483,26.483s-26.483-11.855-26.483-26.483 s11.855-26.483,26.483-26.483S512,38.338,512,52.966"></path> </g> </g> </g></svg>
            </h5>
            <p class="text-primary status-box">UPCOMING</p>
        </div>
        <div class="col-12 col-lg-4">
            <div>
                <img src="{{ bladeImageNullq($teamBanner1) }}" alt="Team Banner" width="100"
                    height="100" onerror="this.src='{{ asset('assets/images/404q.png') }}';"
                    data-position="{{$position1.'middle'}}"
                    class="border border-4 popover-img popover-content-img rounded-circle object-fit-cover"
                >
            </div>
             <p class="mt-2 mb-0 py-0 text-center popover-title text-light" data-position="{{$position1. 'middle'}}">{{$teamName1}}</p>
            <div class="{{ 'mt-2 mb-4 py-0 d-flex justify-content-center dotted-score-container ' . $position1 . ' ' . $position2  }}">
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="d-flex justify-content-center align-items-center h-100">
                <h1 class="pe-4 dotted-score-box text-light">0</h1>
                <h1 class="text-light">-</h3>
                <h1 class="ps-4 dotted-score-box text-light">0</h1>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div >
                <img src="{{ bladeImageNullq($teamBanner2) }}" alt="Team Banner" width="100"
                    height="100" onerror="this.src='{{ asset('assets/images/404q.png') }}';"
                    data-position="{{$position2. 'middle'}}" class="border border-4 popover-img popover-content-img rounded-circle object-fit-cover"
                >
            </div>
            <p class="mt-2 mb-2 py-0 text-center popover-title text-light" data-position="{{$position2. 'middle'}}">{{$teamName2}}</p>
            <div class="{{ 'mt-2 mb-4 py-0 d-flex justify-content-center dotted-score-container ' . $position1 . ' ' . $position2 }}">
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score"></div>
                <div class="d-inline-block rounded-circle me-3 bg-secondary dotted-score d-none"></div>
            </div>
        </div>
        <div class="col-12 text-light"> 
            @if($isTeam1 || $isTeam2 || $isOrg)
                @if (!$deadline['has_started'])
                    <div class="text-center">Reporting available in: </div>
                    <div class="text-center diffDate1" data-diff-date="{{$deadline['diff_date']}}"></div>
                @elseif ($deadline['has_started'] && !$deadline['has_ended'])
                    <div class="text-center">Time left to report: </div>
                    <div class="text-center diffDate1" data-diff-date="{{$deadline['diff_date']}}"></div>
                @endif
            @endif
        </div>
    </div>
    
</div>