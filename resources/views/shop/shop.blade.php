<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Checkout</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('includes.Navbar')

    <main class="px-2 pt-4">
        @if (session()->has('success_message'))
            <div class="alert alert-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mx-2">
            <div class="col-12 col-xl-2 mb-6 mt-3">
                <h5>Shop By Category</h5>
                <div class="list-group  mt-4">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.index', ['category' => $category->slug]) }}" 
                           class="list-group-item list-group-item-action {{ setActiveCategory($category->slug) }} {{ setActiveCategory($category->slug, 'text-white') }}">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-12 col-xl-10 mb-6 mt-3">
                <div class="d-flex justify-content-between">
                    <div class=" mb-6">
                        <h4 style="font-weight: bold">{{ $categoryName }}</h4>
                    </div>
                    <div class=" mb-6">

                        <!-- Example single danger button -->

                        <div class="d-flex" >
                            <div class="dropdown me-1">
                                <button type="button" class="btn border-dark rounded-2 text-dark dropdown-toggle me-2" id="dropdownMenuOffset"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    PRODUCT TYPE
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                                    @foreach ($categories as $category)
                                        <a class="dropdown-item"
                                            href="{{ route('shop.index', ['category' => $category->slug]) }}"
                                            style="color: #000;">
                                            {{ $category->name }}</a>
                                    @endforeach
                                </div>
                            </div>

                            <div class="btn-group " >
                                <button type="button" class="btn btn-light border-dark rounded-2 text-dark me-2 dropdown-toggle" id="dropdownMenuOffset3"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    PRICE
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset3">
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'price_less_than_50']) }}">
                                        less than RM 50</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'price_50_to_100']) }}">
                                        RM 50 - RM 100</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'price_100_to_150']) }}">
                                        RM 100 - RM 150</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'price_150_or_more']) }}">
                                        RM 150 or more</a>

                                </div>
                            </div>

                            <div class="btn-group">
                                <button type="button" class="btn border-dark rounded-2 text-dark  me-2 dropdown-toggle" id="dropdownMenuOffset2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    SORT BY
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset2">
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'Newest']) }}">
                                        Newest</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'low_high']) }}"
                                        style="color: #000;">
                                        Price :low - high</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'high_low']) }}">
                                        Price :high - low</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'Top_Sellers']) }}">
                                        Top Sellers</a>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mt-3">


                    @forelse ($products as $product)
                        <div class="col-12 col-lg-4 col-xl-3 ">
                            <div class="shop">
                                <a href="{{ route('shop.show', $product->slug) }}"><img
                                    src="{{ asset('storage/' . $product->image) }}" class="object-fit-cover border border-secondary rounded-3"
                                    width="270" height="270"    
                                    style="max-width: 95%;"    
                                    onerror="this.onerror=null;this.src='/assets/images/404q.png';"
                                ></a>
                                <br><br>

                                <a href="{{ route('shop.show', $product->slug) }}">{{ $product->name }}</a>
                                <br>

                                RM {{ $product->price }}
                                <br><br>

                            </div> 
                        </div>


                    @empty
                        @include('shop.notfound');
                    @endforelse
                </div> <!--  row end-->

            </div>
        </div>
    </main>

</body>

</html>
