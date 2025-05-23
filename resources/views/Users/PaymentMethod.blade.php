@extends('layout.app')
<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/fullpage.css') }}">
</head>

@section('content')
    @include('includes.Navbar.NavbarGoToSearchPage')
    <div class="row  " >
        
        <div class="mx-auto" style="max-width: 600px;">
            <div class="card">
                
                <div class="card-header">Add Payment Method for Withdrawals</div>

                <div class="card-body">
                    @include('includes.Flash')
                    <p>Add your bank account or card details to receive your prize money.</p>

                    
                        <form id="payment-form" action="{{ route('wallet.save-payment-method') }}" method="POST">
                            @csrf
                            <div id="payment-element" class="mb-3">
                            </div>

                            <div id="error-message" class="alert alert-danger" style="display: none;"></div>

                            <button id="submit-button" class="btn rounded-pill text-light btn-primary text-light ">
                                Add Payment Method
                            </button>
                        </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stripe = Stripe('{{ config('services.stripe.key') }}');
                const elements = stripe.elements({
                    clientSecret: '{{ $clientSecret }}'
                });

                const paymentElement = elements.create('payment');
                paymentElement.mount('#payment-element');

                const form = document.getElementById('payment-form');
                const submitButton = document.getElementById('submit-button');
                const errorMessage = document.getElementById('error-message');

                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    submitButton.disabled = true;

                    const {
                        error,
                        setupIntent
                    } = await stripe.confirmSetup({
                        elements,
                        confirmParams: {
                            return_url: '{{ route('wallet.save-payment-method') }}',
                        },
                        redirect: 'if_required'
                    });

                    if (error) {
                        errorMessage.textContent = error.message;
                        errorMessage.style.display = 'block';
                        submitButton.disabled = false;
                    } else {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'payment_method_id');
                        hiddenInput.setAttribute('value', setupIntent.payment_method);
                        form.appendChild(hiddenInput);

                        form.submit();
                    }
                });
            });
        </script>
@endpush
