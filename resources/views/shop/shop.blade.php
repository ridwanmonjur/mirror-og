<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
</head>

<body>
    @include('includes.Navbar')

    <main class="px-2 pt-4">
        @if (session()->has('success_message'))
            <div class="text-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if (count($errors) > 0)
            <div class=" text-red">
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
                <form method="GET" action="{{ route('shop.index') }}" class="mt-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" value="all" id="all-categories" 
                               {{ request()->category === 'all' || request()->category === null ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label ms-2" for="all-categories">
                            All Categories
                        </label>
                    </div>
                    @foreach ($categories as $category)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="{{ $category->slug }}" 
                                   id="category-{{ $category->slug }}" {{ request()->category === $category->slug ? 'checked' : '' }} 
                                   onchange="this.form.submit()">
                            <label class="form-check-label ms-2" for="category-{{ $category->slug }}">
                                {{ $category->name }}
                            </label>
                        </div>
                    @endforeach
                </form>
            </div>

            <div class="col-12 col-xl-10 mb-6 mt-3">
                <div class="d-flex align-items-center flex-wrap justify-content-between">
                    <div class=" mb-6">
                        <h4 style="font-weight: bold">{{ $categoryName }}</h4>
                    </div>
                    <div class=" mb-6">

                        <!-- Example single danger button -->

                        <div class="d-flex flex-wrap" >
                            <div class="dropdown me-1">
                                <button type="button" class="btn px-0 rounded-2 text-dark dropdown-toggle me-4" id="dropdownMenuOffset"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Product Type
                                    <svg width="16" height="16" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
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

                            <div class="btn-group mb-2" >
                                <button type="button" class="btn  px-0 rounded-2 text-dark me-4 dropdown-toggle" id="dropdownMenuOffset3"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Price
                                    <svg width="16" height="16" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
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

                            <div class="btn-group mb-2">
                                <button type="button" class="btn px-0 rounded-2 text-dark  me-4 dropdown-toggle" id="dropdownMenuOffset2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort By
                                    <svg width="16" height="16" fill="currentColor" class="ms-1" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset2">
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'Newest']) }}">
                                        Newest</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'low_high']) }}"
                                        style="color: #000;">
                                        Price low - high</a>
                                    <a class="dropdown-item"
                                        href="{{ route('shop.index', ['category' => request()->category, 'sort' => 'high_low']) }}">
                                        Price high - low</a>
                                   

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row px-3 mt-3">


                    @forelse ($products as $product)
                        <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-4">
                        <a href="{{ route('shop.show', $product->slug) }}" class="text-decoration-none ">
                            <div style="border-radius: 30px; background-color: rgba(255, 255, 255, 0.7);" class="product-card border border-3 border-primary h-100 d-flex flex-column">
                                
                                    <div class="product-card__image-wrapper  mb-3">
                                        
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                            class="product-card__image w-100 object-fit-cover   border-primary border-bottom"
                                            style="height: 200px;border-radius: 30px 30px 0 0; "   
                                            onerror="this.onerror=null;this.src='/assets/images/404q.png';"
                                            alt="{{ $product->name }}"
                                        >
                                       
                                    </div>
                                

                                    <div class="text-start px-3 d-flex flex-column justify-content-between" >
                                        <div>
                                                <h5  class="product-card__name text-truncate mt-2 mb-3 px-0 text-primary d-block mb-1">
                                                    {{ $product->name }}
                                                </h5>
                                            
                                            @if($product->description)
                                            <div class="product-card__description mb-2 fs-7 ">
                                                {!! strip_tags($product->description) !!}
                                            </div>
                                            @endif
                                        </div>

                                        <div>
                                            @php
                                                $price = number_format($product->price, 2);
                                                $parts = explode('.', $price);
                                                $whole = $parts[0];
                                                $decimal = $parts[1];
                                            @endphp
                                            
                                            <div class="product-card__price text-dark">
                                                <span class="product-card__currency ">RM</span><span class="product-card__price-main">{{ $whole }}</span><span class="product-card__price-decimal">.{{ $decimal }}</span>
                                            </div>
                                            
                                            <div class="mb-0">
                                                <small class="text-muted me-2">Category: </small>
                                                <div class="d-inline-flex flex-wrap justify-content-center align-items-center">
                                                    @foreach($product->categories->take(2) as $category)
                                                        <a href="{{ '/shop?category=' .  $category->slug }}" class="badge mb-2 bg-primary me-1 text-white text-truncate d-inline-block text-decoration-none" style="width: 12ch; ">
                                                            {{ $category->name }}
                                                        </a>
                                                        
                                                        
                                                    @endforeach
                                                    @if($product->isPhysical)
                                                    <span class="badge bg-primary mb-2" style="width: 8ch; ">
                                                    
                                                        Physical
                                                    </span>
                                                @else
                                                    <span class="badge bg-success mb-2" style="width: 8ch; " >
                                                        
                                                        Digital
                                                    </span>
                                                @endif
                                                </div>
                                                @if (isset ($product->categories[2])) 
                                                    ...
                                                @endif
                                                
                                            </div>

                                            <!-- View Product Button -->
                                            <div class="d-flex justify-content-center align-items-center mb-3">
                                                <a href="{{ route('shop.show', $product->slug) }}" class=" border-bottom border-2 text-muted border-secondary small  px-2 mt-2 ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi border-secondary bi-eye me-1" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                    </svg>
                                                    View Product
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                            </div> 
                                </a>

                        </div>


                    @empty
                        @include('shop.notfound');
                    @endforelse
                </div> <!--  row end-->

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </main>

</body>

</html>
