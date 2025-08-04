{{-- @php
    dd($pagination);
@endphp --}}
@if($pagination['total_pages'] > 1)
<div class="pagination-wrapper mt-3">
    <div class="pagination-info">
        <p class="text-sm text-gray-700">
            Showing rounds 
            <span class="font-medium">{{ $pagination['showing_rounds']['from'] }}</span>
            to
            <span class="font-medium">{{ $pagination['showing_rounds']['to'] }}</span>
            of
            <span class="font-medium">{{ $pagination['total_rounds'] }}</span>
            results
        </p>
    </div>

    <nav class="pagination-nav" aria-label="Pagination">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if($pagination['has_prev_page'])
                <li class="page-item">
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                    class="page-link" 
                    rel="prev" 
                    aria-label="Previous">
                        &laquo; Previous
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&laquo; Previous</span>
                </li>
            @endif

            {{-- First Page --}}
            @if($pagination['current_page'] > 3)
                <li class="page-item">
                    <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="page-link">1</a>
                </li>
                @if($pagination['current_page'] > 4)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++)
                @if($i == $pagination['current_page'])
                    <li class="page-item active">
                        <span class="page-link">{{ $i }}</span>
                    </li>
                @else
                    <li class="page-item">
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" class="page-link">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            {{-- Last Page --}}
            @if($pagination['current_page'] < $pagination['total_pages'] - 2)
                @if($pagination['current_page'] < $pagination['total_pages'] - 3)
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @endif
                <li class="page-item">
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['total_pages']]) }}" class="page-link">{{ $pagination['total_pages'] }}</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if($pagination['has_next_page'])
                <li class="page-item">
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                    class="page-link" 
                    rel="next" 
                    aria-label="Next">
                        Next &raquo;
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
</div>
@endif