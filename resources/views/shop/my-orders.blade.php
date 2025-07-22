<!DOCTYPE html>
<html lang="en">

<head>
    @include('googletagmanager::head')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases</title>
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
    

   
       
        
                <h2 class="text-primary my-3 fw-bold"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bag-check me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/></svg>My Purchases</h2>

                @foreach ($orders as $order)
                <div class="row mb-4">
                    <div class="col-lg-12 bg-white border border-light rounded rounded-2 p-4">
                        <div class="order-info mb-4">
                            
                            <div class="row text-muted">
                                <div class="col-md-4">
                                    <strong>Purchase Date:</strong><br>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-check me-1" viewBox="0 0 16 16"><path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                                    {{ $order->created_at->format('M d, Y') }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Subtotal:</strong><br>
                                    RM {{ number_format($order->billing_subtotal, 2) }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Purchase Total:</strong><br>
                                    <span class="text-dark fw-bold fs-5">
                                        RM {{ number_format($order->billing_total, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
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
                                @foreach ($order->orderProducts as $orderProduct)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('storage/' . $orderProduct->product->image) }}" 
                                                 alt="{{ $orderProduct->product->name }}" class="border border-secondary object-fit-cover me-3"
                                                 width="50" height="50"
                                                 onerror="this.onerror=null;this.src='/assets/images/404q.png';">
                                            <div>
                                                <a href="{{ route('shop.show', $orderProduct->product->slug) }}" class="text-dark fw-semibold text-decoration-none">{{ $orderProduct->product->name }}</a>
                                                @if($orderProduct->orderProductVariants && $orderProduct->orderProductVariants->count() > 0)
                                                <div class="text-muted small">
                                                    @php
                                                        $variantsByName = $orderProduct->orderProductVariants->groupBy('name');
                                                    @endphp
                                                    @foreach($variantsByName as $variantName => $variants)
                                                        {{ ucfirst($variantName) }}: {{ ucwords($variants->pluck('value')->join(', ')) }}
                                                        @if(!$loop->last) | @endif
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="">RM {{ number_format($orderProduct->product->price, 2) }}</td>
                                    <td class="fw-semibold">{{ $orderProduct->quantity }}</td>
                                    <td class="text-muted fw-bold">RM {{ number_format($orderProduct->product->price * $orderProduct->quantity, 2) }}</td>
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

   
</body>

</html>
