@extends('layout')

@section('title', 'Shopping Cart')

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
@endsection

@section('content')

    <style type="text/css">
        form {
            float: left;
            margin-top: 0em;
        }

        .btn-link {
            color: black;
            text-decoration: underline;

        }

        .btn-link:hover {
            color: black;
            text-decoration: underline;

        }

        /*text-transform: uppercase;*/
    </style>
    <br>

    <div class="container">

        {{-- ----------------------------- ROW CART START ----------------------------- --}}
        <div class="row">
            <div class="col-12 col-lg-9 ">




                @if ($cart->getContent()->count() > 0)
                    <h2>YOUR BAG <span class="title_cartpage">{{ $cart->getContent()->count() }} ITEMS </span></h2>


                    {{-- success error msg start --}}
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
                                                <img src="{{ productImage($item->product->image) }}" class="img_cartpage"></a>
                                        @else
                                            <img src="{{ productImage($item->product->image ?? '') }}" class="img_cartpage">
                                        @endif
                                    </div>

                                    <div class="col-lg-5">
                                        @if ($item->product && $item->product->slug)
                                            <a href="{{ route('shop.show', $item->product->slug) }}" class="cart_a">
                                                {{ $item->product->name }}</a>
                                        @else
                                            <span class="cart_a">{{ $item->product->name ?? 'Product unavailable' }}</span>
                                        @endif
                                        <p class="cart_p">Color: Black <br>
                                            Size: 9.5 <b style="margin-left: 7px;">In Stock</b> <i
                                                class="far fa fa-check"></i></p>
                                        <p>

                                        <form action="" method="POST">
                                            <button type="submit" class="btn btn-link">Edit</button>
                                        </form>

                                        <form action="{{ route('cart.destroy', $item->product_id) }}" method="POST">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-link">Delete</button>
                                        </form>

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

                                    <div class="col-lg-1"></div>
                                    <div class="col-lg-2">$ {{ $item->subtotal }}</div>
                                </div>
                            </div>
                            <hr>
                        @endif
                    @endforeach

                    <div class="row">
                        <div class="col-md-10">
                            <a href="{{ route('shop.index') }}" style="margin-right: 8px">Continue Shopping</a>
                            <a href="{{ route('checkout.index') }}" class="btn btn-dark">Checkout <i
                                    class="fa fa-arrow-right" style="width: 60px;"></i></a>
                        </div>
                        <div class="col-md-2">
                            <p>Subtotal : ${{ $cart->getSubTotal() }}</p>
                            <p><b>Total: ${{ $cart->getTotal() }} </b></p>
                        </div>
                    </div>
                @else
                    <h3>No items in Cart!</h3>

                    <a href="{{ route('shop.index') }}" class="btn btn-link">Continue Shopping</a>

                @endif
            </div>
            <div class="col-12 col-lg-3 ">
                <div class="cart_sidebar">
                    <br>
                    <a href="{{ route('checkout.index') }}" class="btn btn-dark btn-lg btn-block"
                        style=" margin-right: :4px;">Checkout <i class="fa fa-arrow-right"
                            style="margin-left: 35px;"></i></a>
                    <h4 style="font-weight: 600; font-size: 22px; margin-left: 9px;">ORDER SUMMARY:</h4>
                    <div class="cart-calculator">
                        <table class="table">
                            <tr>
                                <td>{{ $cart->getContent()->count() }} PRODUCTS</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Product total</td>
                                <td>${{ $cart->getSubTotal() }}</td>

                            </tr>
                            @if (session()->has('coupon'))
                                <tr>
                                    <td>
                                        COUPON : {{ session()->get('coupon')['name'] }}
                                    </td>

                                    <td>- ${{ session()->get('coupon')['discount'] }}
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
                                <td>${{ $cart->getTotal() }}</td>
                            </tr>




                            @if (session()->has('coupon'))
                                <tr>
                                    <td>Discount<br>
                                        <b>Net Total</b>
                                    </td>
                                    <td>- ${{ $discount }}<br>
                                        <b>${{ $newTotal }} </b>

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
                                    <a class="btn btn-link" data-toggle="collapse" href="#multiCollapseExample1"
                                        role="button" aria-expanded="false" aria-controls="multiCollapseExample1"
                                        style="color: #000;">
                                        <b>PROMO CODE</b>
                                    </a>
                                </td>
                                <td>
                                    <a class="btn btn-link" data-toggle="collapse" href="#multiCollapseExample1"
                                        role="button" aria-expanded="false" aria-controls="multiCollapseExample1"
                                        style="color: #000;">
                                        <i class="fa fa-chevron-down"></i> </a>
                                </td>
                            </tr>
                        </table>
                        <div class="row">
                            <div class="col">
                                <div class="collapse multi-collapse" id="multiCollapseExample1">
                                    <div class="card card-body">
                                        <form method="post" action="{{ route('coupon.store') }}">
                                            {{ csrf_field() }}
                                            <input type="text" name="coupon_code" class="form-control"
                                                placeholder="CODES ARE CASE-SENSITIVE">
                                            <p style="font-size: 12px; color: grey;">Casing & hyphens need to be exact</p>

                                            <button type="submit" class="btn btn-dark btn-lg btn-block">Apply <i
                                                    class="fa fa-arrow-right" style="margin-left: 35px;"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                


            </div>

        </div>







        {{--    -------------- Coupon code start ----------------------- --}}
        {{--   <div>
                        Subtotal <br>
                        @if (session()->has('coupon'))
                            Code ({{ session()->get('coupon')['name'] }})
                            <form action="{{ route('coupon.destroy') }}" method="POST" style="display:block">
                                {{ csrf_field() }}
                                {{ method_field('delete') }}
                                <button type="submit" style="font-size:14px;">Remove</button>
                            </form>
                            <hr>
                            New Subtotal <br>
                        @endif
                        Tax ({{config('cart.tax')}}%)<br>
                        <span class="cart-totals-total">Total</span>
                    </div>
                    <div class="cart-totals-subtotal">
                        {{ $cart->getSubTotal() }} <br>
                        @if (session()->has('coupon'))
                            -{{ $discount }} <br>&nbsp;<br>
                            <hr>
                            {{ $newSubtotal }} <br>
                        @endif
                        {{ $newTax }} <br>
                        <span class="cart-totals-total">{{ $newTotal }}</span>
                    </div> --}}
        {{--    -------------- Coupon code end ----------------------- --}}


    </div> {{-- col-lg-9 col-sm-6 col-xs-12 end --}}

    </div>{{--  row end --}}

    {{-- ----------------------------- ROW CART END ----------------------------- --}}


@endsection

@section('extra-js')
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        (function() {
            const classname = document.querySelectorAll('.quantity')

            Array.from(classname).forEach(function(element) {
                element.addEventListener('change', function() {
                    const id = element.getAttribute('data-id')
                    const productQuantity = element.getAttribute('data-productQuantity')

                    axios.patch(`/cart/${id}`, {
                            quantity: this.value,
                            productQuantity: productQuantity
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
@endsection
