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
                            <div class="container cart_item">
                                <div class="row">

                                    <div class="col-lg-2">
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

                                    <div class="col-lg-5">
                                        @if ($item->product && $item->product->slug)
                                            <a href="{{ route('shop.show', $item->product->slug) }}" class="cart_a text-dark fw-semibold fs-6 text-decoration-none">
                                                {{ $item->product->name }}</a>
                                        @else
                                            <span class="cart_a">{{ $item->product->name ?? 'Product unavailable' }}</span>
                                        @endif
                                        <p class="cart_p text-muted fw-normal"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-palette me-1" viewBox="0 0 16 16"><path d="M8 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm4 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5.5 7a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm.5 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/><path d="M16 8c0 3.15-1.866 2.585-3.567 2.07C11.42 9.763 10.465 9.473 10 10c-.603.683-.475 1.819-.351 2.92C9.826 14.495 9.996 16 8 16a8 8 0 1 1 8-8z"/></svg>Color: Black <br>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-rulers me-1" viewBox="0 0 16 16"><path d="M1 0a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V7h1v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V7h1v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1H1zm2 4.5a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1zm3 0a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H6zm3 0a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H9zm3 0a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1z"/></svg>Size: 9.5 <b class="ms-2 text-success fw-semibold"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-check-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg>In Stock</b></p>
                                        </p>


                                        


                                    </div> {{-- col-lg-5 end --}}

                                    <div class="col-lg-2">
                                        @if ($item->product)
                                            <select class="quantity" data-id="{{ $item->product_id }}"
                                                data-productQuantity="{{ $item->product->quantity ?? 0 }}"
                                                style="width: 50px; font-size:12px; font-weight: 700; border-radius: 2px; height: 30px;">
                                                @for ($i = 1; $i <= 20; $i++)
                                                    <option value="{{ $i }}" {{ $item->quantity == $i ? 'selected' : '' }}>
                                                        {{ $i }}</option>
                                                @endfor
                                            </select>
                                        @else
                                            <span>N/A</span>
                                        @endif
                                    </div>

                                    <div class="col-lg-1">
                                        <form action="{{ route('cart.destroy', $item->product_id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-link text-danger" title="Remove item">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    
                                    </div>
                                    <div class="col-lg-2 text-success fw-bold fs-6"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-currency-dollar me-1" viewBox="0 0 16 16"><path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z"/></svg>RM {{ number_format($item->subtotal, 2) }}</div>
                                </div>
                            </div>
                            <hr>
                        @endif
                    @endforeach

                    <div class="row">
                        <div class="col-md-10">
                            <a href="{{ route('shop.index') }}" class="text-decoration-none fw-medium me-3" style="margin-right: 8px"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>Continue Shopping</a>
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary text-white fw-semibold"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-card me-2" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/><path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/></svg>Checkout</a>
                        </div>
                        <div class="col-md-2">
                            <p class="text-muted fw-medium">Subtotal : <span class="text-dark">RM {{ number_format($cart->getSubTotal(), 2) }}</span></p>
                            <p class="text-success fw-bold fs-5">Total: RM {{ number_format($cart->getTotal(), 2) }}</p>
                        </div>
                    </div>
                @else
                    <h3 class="text-warning">No items in Cart!</h3>

                    <a href="{{ route('shop.index') }}" class="btn btn-link">Continue Shopping</a>

                @endif
            </div>
            <div class="col-12 col-lg-3  border border-light rounded rounded-2">
                <div class="cart_sidebar bg-white px-2 py-3">
                    
                    <h4 class="text-secondary" style="font-weight: 600; font-size: 22px; margin-left: 9px;">ORDER SUMMARY:</h4>
                    <div class="cart-calculator">
                        <table class="table">
                            <tr>
                                <td>{{ $cart->getContent()->count() }} PRODUCTS</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Product total</td>
                                <td>RM {{ number_format($cart->getSubTotal(), 2) }}</td>

                            </tr>
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>
                                        COUPON : {{ session()->get('coupon')['name'] }}
                                    </td>

                                    <td>- RM {{ number_format(session()->get('coupon')['discount'], 2) }}
                                        <form method="post" action="{{ route('coupon.destroy') }}" style="display:inline">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button type="submit" class="btn btn-link"
                                                style="color: #000; font-size: 12px;"><span
                                                    class="fa fa-trash"></span></button>
                                        </form>
                                    </td>

                                </tr>
                            @endif
                            <tr style="font-weight: bold">
                                <td>Total</td>
                                <td>RM {{ number_format($cart->getTotal(), 2) }}</td>
                            </tr>

                            @if (session()->has('coupon'))
                                <tr>
                                    <td>Discount<br>
                                        <b>Net Total</b>
                                    </td>
                                    <td>- RM {{ number_format($discount, 2) }}<br>
                                        <b>RM {{ number_format($newTotal, 2) }} </b>

                                    </td>
                                </tr>
                            @endif

                        </table>
                    </div>
                </div>
                <br>

                {{--    Coupon start --}}
                <div class="coupon_fr">

                    <div class="coupon_in">
                        <table class="table">
                            <tr>
                                <td>
                                    <b>PROMO CODE</b>
                                </td>
                            </tr>
                        </table>
                        <div class="row">
                            <div class="col">
                                <div class="card card-body">
                                    <form method="post" action="{{ route('coupon.store') }}">
                                        {{ csrf_field() }}
                                        <input type="text" name="coupon_code" class="form-control"
                                            placeholder="CODES ARE CASE-SENSITIVE">
                                        <p style="font-size: 12px; color: grey;">Casing & hyphens need to be exact</p>

                                        <button type="submit" class="btn btn-success text-dark  btn-block rounded-pill">Apply 
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right ms-2" viewBox="0 0 16 16">
                                                <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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