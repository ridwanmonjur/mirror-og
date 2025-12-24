<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View | {{ $product->name }}</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common/breadcrumb.css') }}">
</head>

<body>
    @include('googletagmanager::body')
    <div class="scroll-indicator"></div>
    @include('includes.Navbar')
    <input type="hidden" id="user_role" value="{{ auth()->check() ? auth()->user()->role : null }}">

    @php
        $breadcrumbItems = [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Shop', 'url' => '/shop']
        ];

        if (isset($product->categories) && isset($product->categories[0])) {
            $breadcrumbItems[] = [
                'label' => $product->categories[0]->name,
                'url' => '/shop/?category=' . $product->categories[0]->slug
            ];
        }

        $breadcrumbItems[] = ['label' => $product->name];
    @endphp

    @include('includes.Breadcrumb', ['items' => $breadcrumbItems])

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
        <div class="px-4 py-2 bg-white rounded-3 shadow-sm">
            <div class="row  px-4 g-4">
                <!-- Product Images -->
                <div class="col-lg-3 col-12">
                    <div class="product-image-slider">
                        <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                            @if ($product->images)
                                <div class="carousel-inner">
                                    @foreach ((is_array($product->images) ? $product->images : json_decode($product->images, true)) as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $image) }}"
                                                class="d-block w-100 rounded-3 shadow-sm object-fit-cover"
                                                alt="{{ $product->name }}" style="height: 250px;"
                                                onerror="this.onerror=null;this.src='/assets/images/404q.png';">
                                        </div>
                                    @endforeach
                                </div>
                                <button
                                    class="carousel-control-prev bg-dark rounded-circle border border-white border-2 position-absolute top-50 translate-middle-y"
                                    type="button" data-bs-target="#productCarousel" data-bs-slide="prev"
                                    style="width:35px; height: 35px;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button
                                    class="carousel-control-next bg-dark rounded-circle border border-white border-2 position-absolute top-50 translate-middle-y"
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
                    <div class="product-info rounded px-4">
                        <div class="pb-2">
                            <h4 class="product-detail-name text-dark mb-0 fw-bold text-truncate">{{ $product->name }}</h4>
                        </div>

                        <!-- Product Description -->
                        @if ($product->description)
                            <div class="mt-2 mb-3">
                                <div class="bg-light py-3 border-secondary text-start border px-3 rounded">
                                    <div class="text-dark shop__description mb-2">{{ trim($product->description) }}</div>
                                    @if($product->details)
                                        <div class="text-muted shop__details">{{ trim($product->details) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        

                        <div class="my-3 pb-3 border-bottom border-light">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($product->categories as $category)
                                    <a href="{{ '/shop?category=' . $category->slug }}"
                                        class="badge rounded-pill text-primary bg-white border border-primary text-decoration-none px-3 py-2"
                                        style="font-size: 0.85rem; font-weight: 500;">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                                @php
                                    $totalStock = $product->productVariants->sum('stock');
                                    $stockThreshold = 10;
                                @endphp
                                @if ($totalStock <= 0)
                                   
                                    <span class="badge rounded-pill text-danger bg-white border border-danger px-3 py-2" style="font-size: 0.85rem; font-weight: 500;">
                                        <svg width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                        Not Available
                                    </span>
                                @endif

                               
                            </div>
                        </div>


                        <div class="price-section py-3 mb-2">
                            @php
                                $price = number_format($product->price, 2);
                                $parts = explode('.', $price);
                                $whole = $parts[0];
                                $decimal = $parts[1];
                            @endphp
                            <h3 class="mb-0 text-primary product-detail__price">
                                <span class="product-card__currency" style="font-size: 1.2rem;">RM</span><span class="product-card__price-main" style="font-size: 2.5rem;">{{ $whole }}</span><span class="product-card__price-decimal" style="font-size: 1.5rem;">.{{ $decimal }}</span>
                            </h3>
                        </div>

                        <!-- Product Variants -->
                        @if($product->productVariants->count() > 0)
                            <div class="mb-4">
                                @php
                                    $variantsByName = $product->productVariants->groupBy('name');
                                @endphp
                                
                                @foreach($variantsByName as $variantName => $variants)
                                    <div class="row g-3 mb-3">
                                        <!-- Labels Column -->
                                        <div class="col-md-4 col-xxl-2">
                                            <label for="variant_{{ Str::slug($variantName) }}" class="form-label py-2 fw-semibold">
                                                <svg width="14" height="14" fill="currentColor"
                                                    class="text-muted me-2" viewBox="0 0 16 16">
                                                    <path
                                                        d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z" />
                                                </svg>
                                                {{ ucfirst($variantName) }}
                                            </label>
                                        </div>
                                        <!-- Inputs Column -->
                                        <div class="col-md-4 col-lg-5">
                                            <select class="form-select py-2" id="variant_{{ Str::slug($variantName) }}" name="variant_{{ Str::slug($variantName) }}" data-variant-name="{{ $variantName }}">
                                                <option value="">Choose {{ $variantName }}</option>
                                                @foreach($variants as $variant)
                                                    <option value="{{ $variant->id }}" data-stock="{{ $variant->stock }}"
                                                    >
                                                        {{ $variant->value }} 
                                                        @if($variant->stock > 0)
                                                            ({{ $variant->stock }} in stock)
                                                        @else
                                                            (Out of stock)
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <!-- Quantity Selection -->
                                <div class="row g-3 ">
                                    <!-- Labels Column -->
                                    <div class="col-md-4 col-xxl-2">
                                        <label for="quantity" class="form-label py-2 fw-semibold">
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
                                            <input type="number" class="form-control  py-2 text-center "
                                                name="quantity" id="quantity" value="1" min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif


                        

                        <div >
                            <div class="btn-addcart-product-detail my-3">
                                @if ($totalStock > 0)
                                    <form action="{{ route('cart.store', $product) }}" method="POST"
                                        class="text-start mx-auto mt-2" id="addToCartForm">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="variant_ids" id="selected_variant_ids">
                                        <input type="hidden" name="quantity" id="selected_quantity" value="1">
                                        <button type="submit" id="addToCartBtn"
                                            class="btn btn-primary px-5 text-white fw-bold rounded-pill" data-disabled="true">
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




    <script type="text/javascript">
       

       
        // Product Variants Handler
        $(document).ready(function() {
            const variantSelects = $('select[id^="variant_"]');
            const quantityInput = $('#quantity');
            const addToCartBtn = $('#addToCartBtn');
            const selectedVariantIdsInput = $('#selected_variant_ids');
            const selectedQuantityInput = $('#selected_quantity');

            // Function to reset all selects using vanilla JavaScript
            function resetAllSelects() {
                const selects = document.querySelectorAll('select');
                selects.forEach(function(select) {
                    select.selectedIndex = 0;
                    select.value = '';
                    const options = select.querySelectorAll('option');
                    options.forEach(function(option) {
                        option.selected = false;
                    });
                    const emptyOption = select.querySelector('option[value=""]');
                    if (emptyOption) {
                        emptyOption.selected = true;
                    }
                });
            }

            // Set all select elements to empty value and deselect all options on page load
            resetAllSelects();

            // Use setTimeout to ensure reset happens after page is fully loaded
            setTimeout(function() {
                resetAllSelects();
            }, 100);

            // Also reset when navigating back to page (browser back button)
            window.addEventListener('pageshow', function(event) {
                resetAllSelects();
                setTimeout(function() {
                    resetAllSelects();
                }, 50);
            });

            // Reset on DOMContentLoaded as well
            document.addEventListener('DOMContentLoaded', function() {
                resetAllSelects();
            });

            $('#addToCartForm').on('submit', function(e) {
                const userRole = $('#user_role').val();

                console.log(userRole);
                if (userRole !== 'PARTICIPANT') {
                    window.toastError('Only participants can add to cart');
                    e.preventDefault();
                    return false;
                }

                if (addToCartBtn.data('disabled') === true) {
                    window.toastError('Please select all product options before adding to cart');
                    e.preventDefault();
                    return false;
                }
            });


            function updateCartOptions() {
                let selectedVariants = [];
                let minStock = 0;
                
                variantSelects.each(function() {
                    const selectedOption = $(this).find('option:selected');
                    if (selectedOption.val()) {
                        selectedVariants.push({
                            id: selectedOption.val(),
                            stock: parseInt(selectedOption.data('stock'))
                        });
                        if (minStock === 0) {
                            minStock = parseInt(selectedOption.data('stock'));
                        } else {
                            minStock = Math.min(minStock, parseInt(selectedOption.data('stock')));
                        }
                    }
                });

                if (selectedVariants.length === variantSelects.length && minStock > 0) {
                    addToCartBtn.data('disabled', false).removeClass('btn-disabled');
                    
                    const variantIds = selectedVariants.map(v => v.id);
                    selectedVariantIdsInput.val(JSON.stringify(variantIds));
                } 
            }

            // Update quantity value in hidden input
            quantityInput.on('input', function() {
                selectedQuantityInput.val($(this).val());
            });

            // Bind change event to variant selects
            variantSelects.on('change', updateCartOptions);
            
            // Initial call
            updateCartOptions();
        });
  
        

       
    </script>

    <!--===============================================================================================-->


    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>

</body>

</html>
