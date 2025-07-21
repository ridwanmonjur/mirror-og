<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shopping Cart</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="px-3 product">
        <br>

        {{-- ----------------------------- ROW CART START ----------------------------- --}}
        <div class="row">
            <div class="col-12 col-lg-9 bg-white py-3 px-3 border border-light rounded rounded-2">

                @if ($cart->getContent()->count() > 0)
                    <h2 class="text-dark my-0 py-0 leading">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart3 text-primary mx-2" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                        <span class="title_cartpage text-primary fw-semibold"> Your Cart <span>
                    </h2>

                    {{-- success error msg start --}}
                    @if (session()->has('success_message'))
                        <div class="text-success">
                            {{ session()->get('success_message') }}
                        </div>
                    @endif

                    @if (count($errors) > 0)
                        <div class=" text-red py-2 my-0">
                            <ul class="my-0 py-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    {{-- success error msg end --}}
                    <hr>

                    <br>
                    @foreach ($cart->getContent() as $item)
                        @if ($item->product)
                            <div class="">
                                <div class="row">

                                    <div class="col-lg-6 flex-wrap d-flex justify-content-start align-items-start">
                                        <div class="me-3">
                                        @if ($item->product && $item->product->slug)
                                            <a href="{{ route('shop.show', $item->product->slug) }}">
                                                <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                     class="img_cartpage rounded-3 object-fit-cover border border-secondary"
                                                     width="50" height="50"
                                                     onerror="this.onerror=null;this.src='/assets/images/404q.png';"></a>
                                        @else
                                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                                 class="img_cartpage border rounded-3 border-secondary object-fit-cover"
                                                 width="50" height="50"
                                                 onerror="this.onerror=null;this.src='/assets/images/404q.png';">
                                        @endif
                                        </div>
                                        <div>
                                    
                                        @if ($item->product && $item->product->slug)
                                            <a href="{{ route('shop.show', $item->product->slug) }}" class="cart_a text-dark fw-semibold fs-6 text-decoration-none">
                                                {{ $item->product->name }}</a>
                                        @else
                                            <span class="cart_a">{{ $item->product->name ?? 'Product unavailable' }}</span>
                                        @endif
                                        <p class="cart_p text-muted fw-normal">
                                            @php
                                                dd($item->variant);
                                            @endphp
                                            {{-- @if($item->variant)
                                                @foreach($selectedVariants as $variantName => $variant)
                                                    <div class="mb-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-tag me-1" viewBox="0 0 16 16"><path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/><path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z"/></svg>
                                                        <span class="fw-semibold">{{ ucfirst($variantName) }}:</span> {{ $variant->value }}
                                                        <b class="ms-2 {{ $variant->stock > 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-{{ $variant->stock > 0 ? 'check-circle' : 'x-circle' }} me-1" viewBox="0 0 16 16">
                                                                @if($variant->stock > 0)
                                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                                                @else
                                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                                @endif
                                                            </svg>
                                                            {{ $variant->stock > 0 ? ($variant->stock . ' in stock') : 'Out of stock' }}
                                                        </b>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No variant selected</span>
                                            @endif --}}
                                        </p>
                                        </div>

                                    </div> {{-- col-lg-5 end --}}

                                    <div class="col-lg-2">
                                        @if ($item->product)
                                            @php
                                                $maxQuantity = $item->variant ? min(20, $item->variant->stock) : 20;
                                            @endphp
                                            <select class="quantity" data-id="{{ $item->id }}"
                                                data-productQuantity="{{ $maxQuantity }}"
                                                data-variant-id="{{ $item->variant_id }}"
                                                style="width: 50px; font-size:12px; font-weight: 700; border-radius: 2px; height: 30px;">
                                                @for ($i = 1; $i <= $maxQuantity; $i++)
                                                    <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </div>

                                    <div class="col-lg-1">
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-link py-0  text-danger" title="Remove item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    
                                    </div>
                                    <div class="col-lg-3 text-success fw-bold fs-7">
                                        RM {{ number_format($item->subtotal, 2) }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                    @endforeach

                    <div class="row">
                        <div class="col-md-8">
                            <a href="{{ route('shop.index') }}" class="text-decoration-none fw-medium me-3" style="margin-right: 8px"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>Continue Shopping</a>
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary text-white fw-semibold"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card me-2" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/><path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/></svg>Checkout</a>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted fw-medium">Subtotal : <span class="text-dark">RM {{ number_format($cart->getSubTotal(), 2) }}</span></p>
                            <p class="text-success fw-bold fs-5">Total: RM {{ number_format($cart->getTotal(), 2) }}</p>
                        </div>
                    </div>
                @else
                    <h3 class="text-warning">No items in Cart!</h3>

                    <a href="{{ route('shop.index') }}" class="btn btn-link">Continue Shopping</a>

                @endif
            </div>
            <div class="col-12 col-lg-3">
                <div class="cart_sidebar p-3 bg-white rounded rounded-3 border border-light">

                    <h4 class="p-0 m-0 text-primary">Order Summary</h4>
                    <hr>
                    <div class="cart-calculator">
                        <table class="table table-borderless">
                            <tr>
                                <td>Products</td>
                                <td class="text-end">{{ $cart->getCount() }}</td>
                            </tr>
                            <tr>
                                <td>Product total</td>
                                <td class="text-end">RM {{ number_format($cart->getSubTotal(), 2) }}</td>
                            </tr>
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>
                                        Coupon: {{ session()->get('coupon')['name'] }}
                                        <form method="post" action="{{ route('coupon.destroy') }}"
                                            style="display:inline">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button type="submit" class="btn btn-link p-0 ms-1"
                                                style="color: #000; font-size: 12px;"><span
                                                    class="fa fa-trash"></span></button>
                                        </form>
                                    </td>
                                    <td class="text-end">- RM {{ number_format(session()->get('coupon')['discount'], 2) }}</td>
                                </tr>
                            @endif
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>Discount</td>
                                    <td class="text-end">- RM {{ number_format($discount, 2) }}</td>
                                </tr>
                                <tr style="font-weight: bold; border-top: 1px solid #dee2e6;">
                                    <td>Net Total</td>
                                    <td class="text-end">RM {{ number_format($newTotal, 2) }}</td>
                                </tr>
                            @else
                                <tr style="font-weight: bold; border-top: 1px solid #dee2e6;">
                                    <td>Total</td>
                                    <td class="text-end">RM {{ number_format($cart->getTotal(), 2) }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div> {{-- cart_sidebar end --}}
                <br>

                <a href="{{ route('checkout.index') }}" class="btn btn-primary text-white btn-block fw-semibold"
                    style=" margin-right: :4px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card me-2" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/><path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/></svg>Checkout <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-3" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg></a>

            </div>

        </div>

        {{-- ----------------------------- ROW CART END ----------------------------- --}}
    </main>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function() {
            const classname = document.querySelectorAll('.quantity')

            Array.from(classname).forEach(function(element) {
                element.addEventListener('change', function() {
                    const id = element.getAttribute('data-id')
                    const productQuantity = element.getAttribute('data-productQuantity')

                    fetch(`/cart/${id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                quantity: this.value,
                                productQuantity: productQuantity
                            })
                        })
                        .then(function(response) {
                            // console.log(response);
                            window.location.href = '{{ route('cart.index') }}'
                        })
                        .catch(function(error) {
                            // console.log(error);
                            window.location.href = '{{ route('cart.index') }}'
                        });
                })
            })
        })();
    </script>

    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
</body>

</html>