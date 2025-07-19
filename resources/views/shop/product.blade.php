<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="">
            @if (session()->has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session()->get('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <!-- Breadcrumb -->
        <div class="px-4 my-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/shop" class="text-decoration-none">Shop</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>

        <!-- Product Detail -->
        <div class="px-4 bg-white ">
            <div class="row px-4 g-4">
                <!-- Product Images -->
                <div class="col-lg-3 col-12">
                    <div class="product-image-slider">
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            @if ($product->images)
                                <div class="carousel-inner">
                                    @foreach (json_decode($product->images, true) as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ $image ? asset('storage/' . $image) : asset('img/not-found.jpg') }}" 
                                                class="d-block w-100 rounded shadow-sm" 
                                                alt="{{ $product->name }}"
                                                style="height: 300px; object-fit: cover;"
                                                onerror="this.onerror=null;this.src='{{ asset('img/not-found.jpg') }}';">
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-9 col-12">
                    <div class="product-info">
                        <h4 class="product-detail-name h2 mb-3">{{ $product->name }}</h4>
                        
                        <div class="price-section mb-3">
                            <span class="zms_price h3 text-success fw-bold">RM{{ $product->price }}</span>
                        </div>

                        <p class="mb-3">{{ $product->details }}</p>

                        <div class="mb-4">
                            <span class="badge bg-info">{!! $stockLevel !!}</span>
                        </div>

                        <!-- Product Options -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="size" class="form-label fw-semibold">Size</label>
                                    <select class="form-select" id="size" name="size">
                                        <option>Choose an option</option>
                                        <option>Size S</option>
                                        <option>Size M</option>
                                        <option>Size L</option>
                                        <option>Size XL</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="color" class="form-label fw-semibold">Color</label>
                                    <select class="form-select" id="color" name="color">
                                        <option>Choose an option</option>
                                        <option>Gray</option>
                                        <option>Red</option>
                                        <option>Black</option>
                                        <option>Blue</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity and Add to Cart -->
                        <div class="mb-4">
                            <div class=" g-3 align-items-end">
                                <div class="">
                                    <label for="quantity" class="form-label fw-semibold">Quantity</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control text-center num-product" name="num-product" value="1" min="1">
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="">
                                <div class="btn-addcart-product-detail">
                                    @if ($product->quantity > 0)
                                        <form action="{{ route('cart.store', $product) }}" method="POST" class="d-grid">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-primary px-4 text-white rounded-pill">
                                                ADD TO Cart 
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                        <!-- Product Meta -->
                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="text-muted">Categories: </span>
                                    <span class="fw-semibold">{{ $product->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Product Details Accordion -->
                        <div class="accordion" id="productAccordion">
                            <div class="accordion-item wrap-dropdown-content active-dropdown-content">
                                <h2 class="accordion-header">
                                    <button class="accordion-button js-toggle-dropdown-content" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDescription" aria-expanded="true" aria-controls="collapseDescription">
                                        Description
                                        <i class="down-mark fs-12 color1 fa fa-minus dis-none" aria-hidden="true"></i>
                                        <i class="up-mark fs-12 color1 fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </h2>
                                <div id="collapseDescription" class="accordion-collapse collapse show dropdown-content" data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-0">{{ $product->details }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wrap-dropdown-content">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed js-toggle-dropdown-content" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSpecification" aria-expanded="false" aria-controls="collapseSpecification">
                                        Specification
                                        <i class="down-mark fs-12 color1 fa fa-minus dis-none" aria-hidden="true"></i>
                                        <i class="up-mark fs-12 color1 fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </h2>
                                <div id="collapseSpecification" class="accordion-collapse collapse dropdown-content" data-bs-parent="#productAccordion">
                                    <div class="accordion-body">
                                        <div class="mb-0">{!! $product->description !!}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <script type="text/javascript" src="{{ asset('jquery/jquery-3.2.1.min.js') }}"></script>



    <script type="text/javascript" src="{{ asset('select2/select2.min.js') }}"></script>
    <script type="text/javascript">
        $(".selection-1").select2({
            minimumResultsForSearch: 20,
            dropdownParent: $('#dropDownSelect1')
        });

        $(".selection-2").select2({
            minimumResultsForSearch: 20,
            dropdownParent: $('#dropDownSelect2')
        });
    </script>
    <script type="text/javascript">
        $('.block2-btn-addcart').each(function(){
            var nameProduct = $(this).parent().parent().parent().find('.block2-name').html();
            $(this).on('click', function(){
                swal(nameProduct, "is added to cart !", "success");
            });
        });

        $('.block2-btn-addwishlist').each(function(){
            var nameProduct = $(this).parent().parent().parent().find('.block2-name').html();
            $(this).on('click', function(){
                swal(nameProduct, "is added to wishlist !", "success");
            });
        });

        $('.btn-addcart-product-detail').each(function(){
            var nameProduct = $('.product-detail-name').html();
            $(this).on('click', function(){
                swal(nameProduct, "is added to wishlist !", "success");
            });
        });
    </script>

<!--===============================================================================================-->
    <script src="{{ asset('js/main.js') }}"></script>


    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>

</body>

</html>
