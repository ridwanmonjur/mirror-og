<div>   
   
    @foreach($totalItems as $totalItem)
        <div wire:key="$totalItem->id">
            {{ $totalItem->id }} 
        </div>
    @endforeach

    @if($hasMore)
        <div wire:scroll="loadMore">
            Loading more...
        </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
            Livewire.emit('loadMore');
        }
    });
</script>
@endpush