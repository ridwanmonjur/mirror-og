@props(['items' => []])

<nav class="breadcrumb-nav" aria-label="breadcrumb">
    <ol class="breadcrumb-nav__list">
        @foreach($items as $index => $item)
            @if($loop->last)
                <li class="breadcrumb-nav__item breadcrumb-nav__item--active" aria-current="page">
                    <span class="breadcrumb-nav__text">{{ $item['label'] }}</span>
                </li>
            @else
                <li class="breadcrumb-nav__item">
                    <a href="{{ $item['url'] }}" class="breadcrumb-nav__link">
                        {{ $item['label'] }}
                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="breadcrumb-nav__separator" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                    </svg>
                </li>
            @endif
        @endforeach
    </ol>
</nav>
