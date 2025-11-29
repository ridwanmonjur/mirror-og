@php
    use App\Models\EventCategory;
    use Illuminate\Support\Facades\Cache;

    // Cache game icons for 1 day (86400 seconds)
    $gameCategories = Cache::remember('game_sidebar_icons', 86400, function () {
        try {
            return EventCategory::select('gameTitle', 'gameIcon')
                ->whereNotNull('gameIcon')
                ->orderBy('gameTitle', 'asc')
                ->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error fetching game categories for sidebar: ' . $e->getMessage());
            return collect([]);
        }
    });
@endphp

<aside class="game-sidebar">
    <div class="game-sidebar__container">
        @foreach($gameCategories as $category)
            @php
                $iconPath = $category->gameIcon ? asset('storage/' . $category->gameIcon) : asset('assets/images/404.png');
            @endphp
            <div class="game-sidebar__item mb-2" title="{{ $category->gameTitle }}">
                <img
                    src="{{ $iconPath }}"
                    alt="{{ $category->gameTitle }}"
                    onerror="this.src='{{ asset('assets/images/404.png') }}';"
                    loading="lazy"
                    class="game-sidebar__icon"
                >
            </div>
        @endforeach
    </div>
</aside>
