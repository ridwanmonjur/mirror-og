<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }}</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="product py-3">
        @if (session()->has('success_message'))
            <div class="text-success alert-dismissible fade show" role="alert">
                {{ session()->get('success_message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (count($errors) > 0)
            <div class=" text-red alert-dismissible fade show" role="alert">
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
       

        <!-- Product Detail -->
        <div class="px-4 py-2 bg-white  ">

             <div class="px-4 my-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/shop" class="text-decoration-none">Shop</a></li>
                    @if (isset($product->categories) && isset($product->categories[0]))
                        <li class="breadcrumb-item"><a href="{{ '/shop/?category=' . $product->categories[0]->slug }}"
                                class="text-decoration-none">{{ $product->categories[0]->name }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>

            <div class="row  px-4 g-4">
                <!-- Product Images -->
                <div class="col-lg-3 col-12">
                    <div class="product-image-slider">
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            @if ($product->images)
                                <div class="carousel-inner">
                                    @foreach (json_decode($product->images, true) as $index => $image)
                                        <div
                                            class="carousel-item  border border-light {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $image) }}"
                                                class="d-block w-100 rounded-3 shadow-sm object-fit-cover border border-light"
                                                alt="{{ $product->name }}" style="height: 250px; "
                                                onerror="this.onerror=null;this.src='/assets/images/404q.png';">
                                        </div>
                                    @endforeach
                                </div>
                                <button
                                    class="carousel-control-prev bg-secondary bg-opacity-75 rounded-circle border border-white border-2 position-absolute top-50 translate-middle-y"
                                    type="button" data-bs-target="#productCarousel" data-bs-slide="prev"
                                    style="width:35px; height: 35px;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button
                                    class="carousel-control-next bg-secondary bg-opacity-75 rounded-circle border border-white border-2  position-absolute top-50 translate-middle-y"
                                    type="button" data-bs-target="#productCarousel" data-bs-slide="next"
                                    style="width:35px; height: 35px;">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-9 col-12">
                    <div class="product-info  rounded px-4 ">
                        <div class=" pb-2">
                            <h4 class="product-detail-name text-dark my-0 pt-0  text-truncate ">{{ $product->name }}
                            </h4>

                        </div>

                        <!-- Product Description -->
                        @if ($product->description)
                            <div class=" my-2">

                                <div class="bg-light border-secondary border p-3 rounded">


                                    <div class="text-muted">
                                        <svg width="16" height="16" fill="currentColor" class="text-warning me-2"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                        </svg>
                                        {!! $product->description !!}
                                    </div>
                                     <div class="product-details__content">
                                        <div class="px-0 pt-2">
                                            {!! $product->details !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        

                        <div class="my-2 pb-3 border-1 border-bottom border-warning ">
                            <div class="d-inline-flex flex-wrap gap-2">
                                @foreach ($product->categories as $category)
                                    <a href="{{ '/shop?category=' . $category->slug }}"
                                        class="badge bg-secondary text-white text-decoration-none">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                                @php
                                    $stockThreshold = 0;
                                @endphp
                                @if ($product->quantity > $stockThreshold)
                                    <span class="badge bg-success">
                                        <svg width="12" height="12" fill="currentColor" class="me-1"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                                        </svg>
                                        In Stock
                                    </span>
                                @elseif($product->quantity > 0)
                                    <span class="badge bg-warning">
                                        <svg width="12" height="12" fill="currentColor" class="me-1"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                        </svg>
                                        Low Stock
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <svg width="12" height="12" fill="currentColor" class="me-1"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                        Not available
                                    </span>
                                @endif
                            </div>

                        </div>


                        <div class="price-section py-3 mb-2">

                            <h3 class="py-0 my-0  text-warning fw-bold"
                                style="transform: scaleY(1.1); display: inline-block;">RM
                                {{ number_format($product->price, 2) }}</h3>
                        </div>

                        <!-- Product Options -->
                        <div class="mb-4">
                            <div class="row g-3">
                                <!-- Labels Column -->
                                <div class="col-md-4 col-xxl-2">
                                    <label for="size" class="form-label fw-semibold">
                                        <svg width="14" height="14" fill="currentColor"
                                            class="text-muted me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z" />
                                        </svg>
                                        Size
                                    </label>
                                </div>
                                <!-- Inputs Column -->
                                <div class="col-md-4 col-lg-5">
                                    <select class="form-select" id="size" name="size">
                                        <option>Choose an option</option>
                                        <option>Size S</option>
                                        <option>Size M</option>
                                        <option>Size L</option>
                                        <option>Size XL</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Next line -->
                            <div class="row g-3 mt-2">
                                <!-- Labels Column -->
                                <div class="col-md-4 col-xxl-2">
                                    <label for="color" class="form-label fw-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-palette me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M8 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m4 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3M5.5 7a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m.5 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3" />
                                            <path
                                                d="M16 8c0 3.15-1.866 2.585-3.567 2.07C11.42 9.763 10.465 9.473 10 10c-.603.683-.475 1.819-.351 2.92C9.826 14.495 9.996 16 8 16a8 8 0 1 1 8-8m-8 7c.611 0 .654-.171.655-.176.078-.146.124-.464.07-1.119-.014-.168-.037-.37-.061-.591-.052-.464-.112-1.005-.118-1.462-.01-.707.083-1.61.704-2.314.369-.417.845-.578 1.272-.618.404-.038.812.026 1.16.104.343.077.702.186 1.025.284l.028.008c.346.105.658.199.953.266.653.148.904.083.991.024C14.717 9.38 15 9.161 15 8a7 7 0 1 0-7 7" />
                                        </svg>
                                        Color
                                    </label>
                                </div>
                                <!-- Inputs Column -->
                                <div class="col-md-4 col-lg-5">
                                    <select class="form-select" id="color" name="color">
                                        <option>Choose an option</option>
                                        <option>Gray</option>
                                        <option>Red</option>
                                        <option>Black</option>
                                        <option>Blue</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Next line -->
                            <div class="row g-3 mt-2">
                                <!-- Labels Column -->
                                <div class="col-md-4 col-xxl-2">
                                    <label for="quantity" class="form-label fw-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-plus-circle me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path
                                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                        </svg>
                                        Quantity
                                    </label>
                                </div>
                                <!-- Inputs Column -->
                                <div class="col-md-3 col-lg-3">
                                    <div class="input-group">
                                        <input type="number" class="form-control text-center num-product"
                                            name="num-product" value="1" min="1">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div >
                            <div class="btn-addcart-product-detail my-3">
                                @if ($product->quantity > 0)
                                    <form action="{{ route('cart.store', $product) }}" method="POST"
                                        class="text-start mx-auto mt-2 ">
                                        {{ csrf_field() }}
                                        <button type="submit"
                                            class="btn btn-warning   px-5  text-dark fw-bold rounded-pill">
                                            <svg width="16" height="16" fill="currentColor" class="me-2"
                                                viewBox="0 0 16 16">
                                                <path
                                                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
                                            </svg>
                                            Add to Cart
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <br> <br>
    </main>

    <script type="text/javascript" src="{{ asset('jquery/jquery-3.2.1.min.js') }}"></script>



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
        $('.block2-btn-addcart').each(function() {
            var nameProduct = $(this).parent().parent().parent().find('.block2-name').html();
            $(this).on('click', function() {
                swal(nameProduct, "is added to cart !", "success");
            });
        });

        $('.block2-btn-addwishlist').each(function() {
            var nameProduct = $(this).parent().parent().parent().find('.block2-name').html();
            $(this).on('click', function() {
                swal(nameProduct, "is added to wishlist !", "success");
            });
        });

        $('.btn-addcart-product-detail').each(function() {
            var nameProduct = $('.product-detail-name').html();
            $(this).on('click', function() {
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
