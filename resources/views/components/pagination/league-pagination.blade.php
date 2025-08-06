{{-- @php
    dd($pagination);
@endphp --}}
@if($pagination['total_pages'] > 1)
<div class=" mt-3 ">
    <div class="pagination-info">
        <p class="mb-0 text-muted small">
            Showing rounds 
            <span class="fw-semibold text-dark">{{ $pagination['showing_rounds']['from'] }}</span>
            to
            <span class="fw-semibold text-dark">{{ $pagination['showing_rounds']['to'] }}</span>
            of
            <span class="fw-semibold text-dark">{{ $pagination['total_rounds'] }}</span>
            results
        </p>
    </div>

    <nav class="mt-3" aria-label="Pagination">
        
           

            <div class="d-flex align-items-center gap-1 ">
                 {{-- Previous Page Link --}}
                @if($pagination['has_prev_page'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] - 1]) }}" 
                    class="btn btn-outline-primary btn-sm py-2 px-3 text-decoration-none rounded" 
                    rel="prev" 
                    aria-label="Previous">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                        </svg>
                        <span class="d-none d-sm-inline ms-1">Previous</span>
                    </a>
                @else
                    <span class="btn btn-outline-secondary btn-sm py-2 px-3 rounded disabled text-dark">
                        <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                        </svg>
                        <span class="d-none d-sm-inline ms-1">Previous</span>
                    </span>
                @endif
                {{-- First Page --}}
                @if($pagination['current_page'] > 3)
                    <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" 
                       class="btn btn-outline-primary btn-sm px-3 py-2 text-decoration-none border-0 fw-semibold">1</a>
                    @if($pagination['current_page'] > 4)
                        <span class="px-2 text-muted">...</span>
                    @endif
                @endif

                {{-- Page Numbers --}}
                @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++)
                    @if($i == $pagination['current_page'])
                        <span class="btn btn-primary btn-sm px-3 py-2 fw-semibold border-0 text-white">{{ $i }}</span>
                    @else
                        <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" 
                           class="btn btn-outline-primary btn-sm px-3 py-2 text-decoration-none border-0 fw-semibold">{{ $i }}</a>
                    @endif
                @endfor

                {{-- Last Page --}}
                @if($pagination['current_page'] < $pagination['total_pages'] - 2)
                    @if($pagination['current_page'] < $pagination['total_pages'] - 3)
                        <span class="px-2 text-muted">...</span>
                    @endif
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['total_pages']]) }}" 
                       class="btn btn-outline-primary btn-sm px-3 py-2 text-decoration-none border-0 fw-semibold">{{ $pagination['total_pages'] }}</a>
                @endif
                {{-- Next Page Link --}}
                @if($pagination['has_next_page'])
                    <a href="{{ request()->fullUrlWithQuery(['page' => $pagination['current_page'] + 1]) }}" 
                    class="btn btn-outline-primary btn-sm py-2 px-3 text-decoration-none rounded" 
                    rel="next" 
                    aria-label="Next">
                        <span class="d-none d-sm-inline me-1">Next</span>
                        <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </a>
                @else
                    <span class="btn btn-outline-secondary text-dark btn-sm py-2 px-3 rounded disabled opacity-50">
                        <span class="d-none d-sm-inline me-1">Next</span>
                        <svg width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </span>
                @endif
            </div>

            
    </nav>
</div>
@endif