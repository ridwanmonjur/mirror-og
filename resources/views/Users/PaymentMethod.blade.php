@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/js/alpine/settings.js'])
</head>

@section('content')
    <main class="wallet2"  @vue:mounted="init">
        @include('includes.Navbar.NavbarGoToSearchPage')
        <div class="row  mx-auto px-0 container-main">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-75 rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class=" px-2 py-2">


                        <div class="card-body">
                            <div class="d-flex mb-3 justify-content-between cursor-pointer" >
                                <h3 class="transaction-history__title text-secondary  ">Add Payment Method for Withdrawals</h3>
                                <a href="{{route('wallet.dashboard')}}">
                                    <span  class="cursor-pointer text-secondary ">
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                                            </svg>
                                        </span>
                                        <span>Go back</span>
                                    </span>
                                </a>
                            </div>

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
        </div>
    </main>
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
