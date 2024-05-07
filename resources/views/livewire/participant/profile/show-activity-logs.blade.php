@php
    use Carbon\Carbon;
@endphp
<div>   
    <br>
    @if (isset($totalItems[0]))
        @foreach($totalItems as $totalItem)
            <div wire:key="$totalItem->id" class="tab-size mx-auto">
                <span class="me-1"> {!! $totalItem->log !!} </span>
                <span class="notification-gray">{{ Carbon::parse($totalItem->created_at)->diffForHumans() }}</span>
            </div>
        @endforeach
    @else
        <div class="tab-size"> No {{$this->duration}} activities</div>
    @endif
    <br>
    @if($hasMore)
       
    @endif
</div>

