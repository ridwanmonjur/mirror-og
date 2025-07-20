<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results Algolia</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.js@2.6.0/dist/instantsearch.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/instantsearch.js@2.6.0/dist/instantsearch-theme-algolia.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="container">
        @if (session()->has('success_message'))
            <div class="text-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if(count($errors) > 0)
            <div class=" text-red">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="search-results-container-algolia">
            <div>
                <h2>Search</h2>
                <div id="search-box">
                    <!-- SearchBox widget will appear here -->
                </div>

                <div id="stats-container"></div>

                <div class="spacer"></div>
                <h2>Categories</h2>
                <div id="refinement-list">
                    <!-- RefinementList widget will appear here -->
                </div>
            </div>

            <div>
                <div id="hits">
                    <!-- Hits widget will appear here -->
                </div>

                <div id="pagination">
                    <!-- Pagination widget will appear here -->
                </div>
            </div>
        </div>
    </main>

    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/instantsearch.js@2.6.0"></script>
    <script src="{{ asset('js/algolia-instantsearch.js') }}"></script>
</body>

</html>