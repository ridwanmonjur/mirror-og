@php
    use Carbon\Carbon;
@endphp
<div>   
    <br>
    @if (isset($totalItems[0]))
        @foreach($totalItems as $totalItem)
            <div wire:key="{{$totalItem['id']}}" class="tab-size mb-3 mx-auto">
                <span class="me-1"> {!! $totalItem['log'] !!} </span>
                <span style="color: #565656;">{{ Carbon::parse($totalItem['created_at'])->diffForHumans() }}</span>
            </div>
        @endforeach
    @else
        <div class="tab-size"> No {{$this->duration}} activities</div>
    @endif
    @if($hasMore)
        <div class="text-center">
        
            <button wire:click="loadActivityLogs()" class="btn btn-link btn-sm text-primary">
                Load More
            </button>
        </div>
    @endif
    <br>

</div>

