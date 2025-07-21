<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    @include('includes.HeadIcon')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/algolia.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common/shop.css') }}">
   
</head>

<body>
    @include('googletagmanager::body')
    @include('includes.Navbar')

    <main class=" product">
    <div class="">

        @if (session()->has('success_message'))
            <div class="text-success">
                {{ session()->get('success_message') }}
            </div>
        @endif

        @if(count($errors) > 0)
            <div class=" text-red">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    

   
       
        
                <h2 class="text-primary my-3 fw-bold"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bag-check me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/></svg>My Orders</h2>

                @foreach ($orders as $order)
                <div class="row mb-4">
                    <div class="col-lg-12 bg-white border border-light rounded rounded-2 p-4">
                        <div class="order-info mb-4">
                            <h3 class="text-primary fw-bold mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-hash me-1" viewBox="0 0 16 16"><path d="M8.39 12.648a1.32 1.32 0 0 0-.015.18c0 .305.21.508.5.508.266 0 .492-.172.555-.477l.554-2.703h1.204c.421 0 .617-.234.617-.547 0-.312-.188-.53-.617-.53h-.985l.516-2.524h1.265c.43 0 .618-.227.618-.547 0-.313-.188-.524-.618-.524h-1.046l.476-2.304a1.06 1.06 0 0 0 .016-.164.51.51 0 0 0-.516-.516.54.54 0 0 0-.539.43l-.523 2.554H7.617l.477-2.304c.008-.04.015-.118.015-.164a.512.512 0 0 0-.523-.516.539.539 0 0 0-.531.43L6.53 5.484H5.414c-.43 0-.617.22-.617.532 0 .312.187.539.617.539h.906l-.515 2.523H4.609c-.421 0-.609.219-.609.531 0 .313.188.547.61.547h.976l-.516 2.492c-.008.04-.015.125-.015.18 0 .305.21.508.5.508.265 0 .492-.172.554-.477l.555-2.703h2.242l-.515 2.492zm-1.39-4.58h2.242l.515-2.523H7.515l-.515 2.523z"/></svg>
                                Order #{{ $order->id }}
                            </h3>
                            <div class="row text-muted">
                                <div class="col-md-4">
                                    <strong>Order Date:</strong><br>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-check me-1" viewBox="0 0 16 16"><path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                                    {{ $order->created_at->format('M d, Y') }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Subtotal:</strong><br>
                                    RM {{ number_format($order->billing_subtotal, 2) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Order Total:</strong><br>
                                    <span class="text-dark fw-bold fs-5">
                                        RM {{ number_format($order->billing_total, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <h4 class="text-secondary fw-semibold fs-5 mb-3">Order Items</h4>
                        <table class="table table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-secondary fw-semibold">Product</th>
                                    <th scope="col" class="text-secondary fw-semibold">Price</th>
                                    <th scope="col" class="text-secondary fw-semibold">Quantity</th>
                                    <th scope="col" class="text-secondary fw-semibold">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $product->image) }}" 
                                                 alt="{{ $product->name }}" class="border border-secondary object-fit-cover me-3"
                                                 width="50" height="50"
                                                 onerror="this.onerror=null;this.src='/assets/images/404q.png';">
                                            <div>
                                                <a href="{{ route('shop.show', $product->slug) }}" class="text-dark fw-semibold text-decoration-none">{{ $product->name }}</a>
                                                <div class="text-muted small">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-palette me-1" viewBox="0 0 16 16"><path d="M8 5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm4 3a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM5.5 7a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm.5 6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/><path d="M16 8c0 3.15-1.866 2.585-3.567 2.07C11.42 9.763 10.465 9.473 10 10c-.603.683-.475 1.819-.351 2.92C9.826 14.495 9.996 16 8 16a8 8 0 1 1 8-8z"/></svg>COLOR: Black
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-rulers ms-2 me-1" viewBox="0 0 16 16"><path d="M1 0a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V7h1v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V7h1v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1H1zm2 4.5a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1zm3 0a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H6zm3 0a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H9zm3 0a.5.5 0 0 1-.5-.5V3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1z"/></svg>SIZE: 9.5
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-primary fw-bold">RM {{ number_format($product->price, 2) }}</td>
                                    <td class="fw-semibold">{{ $product->pivot->quantity }}</td>
                                    <td class="text-muted fw-bold">RM {{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
        </div>
    </div>
    </main>

    <!-- Include AlgoliaSearch JS Client and autocomplete.js library -->
    <script src="https://cdn.jsdelivr.net/algoliasearch/3/algoliasearch.min.js"></script>
    <script src="https://cdn.jsdelivr.net/autocomplete.js/0/autocomplete.min.js"></script>
    <script src="{{ asset('js/algolia.js') }}"></script>
</body>

</html>
