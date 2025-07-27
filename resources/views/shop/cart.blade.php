<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Cart</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
</head>

<body>
    @php
    if (!function_exists('truncateText')) {
        function truncateText($text, $maxLength = 34) {
            return strlen($text) > $maxLength ? substr($text, 0, $maxLength) . '...' : $text;
        }
    }
    @endphp
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class="px-3 product">
        <br>

        {{-- ----------------------------- ROW CART START ----------------------------- --}}
        <div class="row">
            <div class="col-12  col-xl-8  border border-light rounded rounded-2">
                <div class="bg-white py-3 px-3">
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

                    @foreach ($cart->getContent() as $item)
                        @if ($item->product)
                            <div class="">
                                <div class="row">

                                    <div class="col-lg-6  my-2 flex-nowrap d-flex justify-content-start align-items-start">
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
                                                <a href="{{ route('shop.show', $item->product->slug) }}" class="cart_a text-dark d-block fw-semibold text-truncate fs-6 text-decoration-none">
                                                    {{ truncateText($item->product->name, 30) }}</a>
                                            @else
                                                <span class="cart_a d-block fw-semibold text-truncate ">{{ truncateText($item->product->name ?? 'Product unavailable', 30) }}</span>
                                            @endif
                                            <p class="my-2 text-muted fw-normal">
                                            
                                                @if($item->cartProductVariants)
                                                    @foreach($item->cartProductVariants as $key => $variant)
                                                        <div class="mb-1 small">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-tag me-1" viewBox="0 0 16 16"><path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/><path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z"/></svg>
                                                            <span class="fw-semibold">{{ ucfirst($variant['name']) }}:</span> {{ $variant['value'] }}
                                                            <b class="ms-2 {{ $variant->stock > 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-{{ $variant->stock > 0 ? 'check-circle' : 'x-circle' }} me-1" viewBox="0 0 16 16">
                                                                    {{-- @if($variant->stock > 0)
                                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.061L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                                                    @else
                                                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                                    @endif --}}
                                                                </svg>
                                                                {{-- {{ $variant->stock > 0 ? ($variant->stock . ' in stock') : 'Out of stock' }} --}}
                                                            </b>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No variant selected</span>
                                                @endif 
                                                
                                                @if($item->product->isPhysical)
                                                    <div class="mb-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-box me-1" viewBox="0 0 16 16">
                                                            <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.629 13.09A1 1 0 0 1 0 12.162V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                                                        </svg>
                                                        <span class="text-muted">Physical Product - Requires Shipping</span>
                                                    </div>
                                                @else
                                                    <div class="mb-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-display me-1" viewBox="0 0 16 16">
                                                            <path d="M0 4s0-2 2-2h12s2 0 2 2v6s0 2-2 2h-4c0 .667.083 1.167.25 1.5H11a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1h.75c.167-.333.25-.833.25-1.5H2s-2 0-2-2zm1.398-.855a.758.758 0 0 0-.254.302A1.46 1.46 0 0 0 1 4.01V10c0 .325.078.502.145.602.07.105.17.188.302.254a1.464 1.464 0 0 0 .538.143L2.01 11H14c.325 0 .502-.078.602-.145a.758.758 0 0 0 .254-.302 1.464 1.464 0 0 0 .143-.538L15 9.99V4c0-.325-.078-.502-.145-.602a.757.757 0 0 0-.302-.254A1.46 1.46 0 0 0 13.99 3H2c-.325 0-.502.078-.602.145Z"/>
                                                        </svg>
                                                        <span class="text-muted">Digital Product - Instant Access</span>
                                                    </div>
                                                @endif
                                            </p>
                                        </div>

                                    </div> 

                                    <div class="col-lg-2 my-2">
                                        @if ($item->product)
                                            @php
                                                $variants = $item->cartProductVariants;
                                                $maxQuantity = 20;
                                                if ($variants->count() > 0) {
                                                    $maxQuantity = min(20, $variants->min('stock'));
                                                }
                                            @endphp
                                            <select class="quantity" data-id="{{ $item->id }}"
                                                data-productQuantity="{{ $maxQuantity }}"
                                                data-variant-ids="{{ $variants->pluck('id')->join(',') }}"
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

                                    <div class="col-lg-1 my-2">
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
                                    <div class="col-lg-3 my-2 fw-bold fs-7 text-center">
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
                            <br><br>
                            <a href="{{ route('orders.index') }}" class="text-decoration-none fw-medium me-3"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-check me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>My Purchases</a>
                            <br><br>
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary text-white fw-semibold"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-credit-car me-2" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/><path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/></svg>Checkout</a>
                        </div>
                        <div class="col-md-4">
                            <p class="leading my-1 fw-bold text-end fs-7">Total: RM {{ number_format($cart->getTotal(), 2) }}</p>
                        </div>
                    </div>
                @else
                    <h3 class="text-warning">No items in Cart!</h3>

                    <a href="{{ route('shop.index') }}" class="btn btn-link px-0">Continue Shopping</a>
                    <br>
                    <a href="{{ route('orders.index') }}" class="btn btn-link px-0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag-check me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/></svg>My Purchases</a>

                @endif
                </div>
            </div>
            <div class="col-12  col-xl-4">
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
                                <td>Subtotal</td>
                                <td class="text-end">RM {{ number_format($cart->getSubTotal(), 2) }}</td>
                            </tr>
                            <tr>
                                <td>Discount</td>
                                <td class="text-end">RM 0.00</td>
                            </tr>
                            
                           
                            <tr style="font-weight: bold; border-top: 1px solid #dee2e6;">
                                <td>Total</td>
                                <td class="text-end">RM {{ number_format($cart->getTotal(), 2) }}</td>
                            </tr>
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
                            window.location.href = '{{ route('cart.index') }}'
                        })
                        .catch(function(error) {
                            window.location.href = '{{ route('cart.index') }}'
                        });
                })
            })
        })();
    </script>

    <script src="{{ asset('js/algolia.js') }}"></script>
</body>

</html>