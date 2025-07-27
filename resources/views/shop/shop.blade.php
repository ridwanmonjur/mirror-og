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
                        <a href="{{ route('shop.show', $product->slug) }}">
                            
                            <div style="border-radius: 30px; background-color: rgba(255, 255, 255, 0.7);" class="product-card border border-3 border-primary pb-4 h-100 d-flex flex-column">
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
                                        <h5  class="product-card__name text-truncate mt-2 mb-3 px-0 text-decoration-none text-primary d-block mb-1">
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
                                                    <a href="{{ '/shop?category=' .  $category->slug }}" class="badge mb-2 bg-primary me-1 text-white text-decoration-none" style="font-size: 0.7rem;">
                                                        {{ $category->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                            @if (isset ($product->categories[2])) 
                                                ...
                                            @endif
                                            
                                        </div>
                                        
                                        <div class="mt-2">
                                            <small class="text-muted me-2">Physical: </small>
                                            @if($product->isPhysical)
                                                <span class="badge bg-primary" style="font-size: 0.65rem;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-box me-1" viewBox="0 0 16 16">
                                                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.629 13.09A1 1 0 0 1 0 12.162V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                                                    </svg>
                                                    Physical
                                                </span>
                                            @else
                                                <span class="badge bg-success" style="font-size: 0.65rem;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-display me-1" viewBox="0 0 16 16">
                                                        <path d="M0 4s0-2 2-2h12s2 0 2 2v6s0 2-2 2h-4c0 .667.083 1.167.25 1.5H11a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1h.75c.167-.333.25-.833.25-1.5H2s-2 0-2-2zm1.398-.855a.758.758 0 0 0-.254.302A1.46 1.46 0 0 0 1 4.01V10c0 .325.078.502.145.602.07.105.17.188.302.254a1.464 1.464 0 0 0 .538.143L2.01 11H14c.325 0 .502-.078.602-.145a.758.758 0 0 0 .254-.302 1.464 1.464 0 0 0 .143-.538L15 9.99V4c0-.325-.078-.502-.145-.602a.757.757 0 0 0-.302-.254A1.46 1.46 0 0 0 13.99 3H2c-.325 0-.502.078-.602.145Z"/>
                                                    </svg>
                                                    Digital
                                                </span>
                                            @endif
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
