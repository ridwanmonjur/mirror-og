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
    style="width: 35px; height: 28px;"
>
    <div class="popover-content d-none" style="opacity: 1; z-index: 999 !important;">
        <div class="popover-box row justify-content-start px-2 py-4" style="min-width: 400px; background: white !important;">
            <div class="col-12 col-lg-5 text-center border-dark" style="border-right: 2px solid black;">
                <div >
                    <img src="{{ asset('storage/'.$teamBanner) }}" alt="Team Banner" width="100"
                        data-position="{{$position . 'hi'}}"
                        height="100" onerror="this.src='{{ asset('assets/images/404.svg') }}';"
                        class="popover-team-img popover-img object-fit-cover rounded-circle border  border-2"
                    >
                    
                </div>
                <p class="mt-3 mb-4 py-0 fs-7 popover-title" data-position="{{$position . 'hi'}}"> {{$teamName}} </p>
            </div>
            <div class="roster-container col-12 col-lg-7">
                @if (isset($roster[0]))
                    <ul class="d-block ms-0 ps-0">
                        @foreach ($roster as $rosterItem)
                            <li class="d-inline">
                                <img width="30" height="30" onerror="this.src='{{ asset('assets/images/404.svg') }}';"
                                    src="{{ asset( 'storage/'.$rosterItem?->user?->userBanner) }}" alt="User Banner"
                                    class="mb-2 rounded-circle border border-2 object-fit-cover me-3"
                                >
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
        <img src="{{ asset('storage/'.$teamBanner) }}" width="100%" height="100%"
            onerror="this.src='{{ asset('assets/images/404.svg') }}';"
            class="{{ 'popover-button position-absolute z-99 d-none-when-hover object-fit-cover me-2 ' 
                    . 'data-position-'. $position }}" 
            alt="Team View"
            data-position="{{$position}}"
            onclick="previousMatchReportShow(event);" 
        >
    @else 
        <span data-position="{{$position}}" class="replace_me_with_image  popover-button" style="z-index: 99;"></span>
     @endif
    <span></span>
</div>