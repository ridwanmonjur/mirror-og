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

    <main class="product px-3">
        <div class="container-fluid">
            @if (session()->has('success_message'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session()->get('success_message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center my-4">
                    <h2 class="text-primary fw-bold mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bag-check me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10.854 8.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1zm3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4h-3.5zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5z"/></svg>My Purchases</h2>
                    <a href="{{ route('cart.index') }}" class="text-decoration-none fw-medium"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart me-2" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>View Cart</a>
                </div>

                @foreach ($orders as $order)
                <div class="row mb-4">
                    <div class="col-lg-12 bg-white border border-light rounded rounded-2 p-4">
                        <div class="order-info mb-4">
                            
                            <div class="row text-muted">
                                <div class="col-lg-6">
                                    <strong>Purchase Date:</strong><br>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-check me-1" viewBox="0 0 16 16"><path d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                                    {{ $order->created_at->format('M d, Y') }}
                                </div>
                                
                                <div class="col-lg-6 text-end">
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
                                                @if($orderProduct->product->isPhysical)
                                                    <div class="text-muted small">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-box me-1" viewBox="0 0 16 16">
                                                            <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.629 13.09A1 1 0 0 1 0 12.162V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/>
                                                        </svg>
                                                        Physical Product - Requires Shipping
                                                    </div>
                                                @else
                                                    <div class="text-muted small">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-display me-1" viewBox="0 0 16 16">
                                                            <path d="M0 4s0-2 2-2h12s2 0 2 2v6s0 2-2 2h-4c0 .667.083 1.167.25 1.5H11a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1h.75c.167-.333.25-.833.25-1.5H2s-2 0-2-2zm1.398-.855a.758.758 0 0 0-.254.302A1.46 1.46 0 0 0 1 4.01V10c0 .325.078.502.145.602.07.105.17.188.302.254a1.464 1.464 0 0 0 .538.143L2.01 11H14c.325 0 .502-.078.602-.145a.758.758 0 0 0 .254-.302 1.464 1.464 0 0 0 .143-.538L15 9.99V4c0-.325-.078-.502-.145-.602a.757.757 0 0 0-.302-.254A1.46 1.46 0 0 0 13.99 3H2c-.325 0-.502.078-.602.145Z"/>
                                                        </svg>
                                                        Digital Product - Instant Access
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">RM {{ number_format($orderProduct->product->price, 2) }}</td>
                                    <td class="fw-semibold align-middle">{{ $orderProduct->quantity }}</td>
                                    <td class="text-muted fw-bold align-middle">RM {{ number_format($orderProduct->product->price * $orderProduct->quantity, 2) }}</td>
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
    </main>

   
</body>

</html>
