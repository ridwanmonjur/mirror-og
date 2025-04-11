<div class="px-4 py-3">
    @if($getState())
        <div class="w-full h-8 rounded" style="background: {{ $getState() }}"></div>
        <div class="text-xs text-gray-500 mt-1 truncate">{{ $getState() }}</div>
    @else
        <div class="text-gray-400 text-xs">No gradient set</div>
    @endif
</div>