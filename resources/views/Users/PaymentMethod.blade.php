@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite([ 'resources/js/alpine/teamSelect.js'])    
</head>

@section('content')
        @include('includes.Navbar.NavbarGoToSearchPage')

    <main class="wallet2" >
        <div class=" mx-auto px-0 container-main d min-h-85vh">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-95-lg-50 rounded-30px">
                <div class="card-body px-2 py-2">
                    <div class="px-2 py-2">
                        <div class="card-body">
                            <div class="row mb-3">
                                <h3 class="transaction-history__title text-secondary text-start col-12 col-lg-8">Add Bank Account for Withdrawals</h3>
                                <a href="{{route('wallet.dashboard')}}" class="col-12 col-lg-4 text-start text-lg-end">
                                    <span class="cursor-pointer text-secondary">
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
                                            </svg>
                                        </span>
                                        <span>Go back</span>
                                    </span>
                                </a>
                            </div>

                            @include('includes.Flash')
                            <p class="my-3">Add your bank account details to receive your prize money.</p>

                            <form id="bank-form" action="{{ route('wallet.save-payment-method') }}" method="POST">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <select  id="bank-select" name="bank_name" required>
                                        <option value="">Select your bank</option>
                                        @php
                                           $malaysianBanks = [
                                            // Top Commercial Banks (Domestic)
                                            ['code' => 'maybank', 'name' => 'Maybank', 'logo' => 'may-bank-logo.png'],
                                            ['code' => 'cimb', 'name' => 'CIMB Bank', 'logo' => 'cimb-bank-logo.png'],
                                            ['code' => 'public_bank', 'name' => 'Public Bank', 'logo' => 'public-bank-logo.png'],
                                            ['code' => 'rhb', 'name' => 'RHB Bank', 'logo' => 'rhb-bank-logo.png'],
                                            ['code' => 'hong_leong', 'name' => 'Hong Leong Bank', 'logo' => 'hong-leong-bank-logo.png'],
                                            ['code' => 'ambank', 'name' => 'AmBank', 'logo' => 'am-bank-logo.jpg'],
                                            ['code' => 'affin', 'name' => 'Affin Bank', 'logo' => 'affin-bank-logo.jpeg'],
                                            ['code' => 'alliance', 'name' => 'Alliance Bank', 'logo' => 'alliance-bank-logo.png'],

                                            // Foreign Commercial Banks
                                            ['code' => 'uob', 'name' => 'UOB Malaysia', 'logo' => 'uob-bank-logo.png'],
                                            ['code' => 'ocbc', 'name' => 'OCBC Bank Malaysia', 'logo' => 'ocbc-bank-logo.png'],
                                            ['code' => 'hsbc', 'name' => 'HSBC Bank Malaysia', 'logo' => 'hsbc-bank-logo.png'],
                                            ['code' => 'standard_chartered', 'name' => 'Standard Chartered Malaysia', 'logo' => 'standard-chartered-bank-logo.jpg'],
                                            ['code' => 'citibank', 'name' => 'Citibank Malaysia', 'logo' => 'citi-bank-logo.avif'],

                                            // Islamic Banks
                                            ['code' => 'bank_islam', 'name' => 'Bank Islam Malaysia', 'logo' => 'islam-bank-logo.png'],
                                            ['code' => 'bank_muamalat', 'name' => 'Bank Muamalat Malaysia', 'logo' => 'muamalat-logo.png'],
                                            ['code' => 'al_rajhi_bank', 'name' => 'Al Rajhi Bank Malaysia', 'logo' => 'rajhi-logo.png'],
                                            ['code' => 'kuwait_finance_house', 'name' => 'Kuwait Finance House (Malaysia)', 'logo' => 'kuwait-logo.png'],

                                            // Development Financial Institutions
                                            ['code' => 'bank_rakyat', 'name' => 'Bank Rakyat', 'logo' => 'rakyat-bank-logo.png'],
                                            ['code' => 'bsn', 'name' => 'Bank Simpanan Nasional (BSN)', 'logo' => 'bsn-bank-logo.png'],
                                            ['code' => 'agro_bank', 'name' => 'Agrobank', 'logo' => 'agro-bank-logo.png'],
                                            ['code' => 'mbsb_bank', 'name' => 'MBSB Bank', 'logo' => 'mbsb.jpg'],

                                            // Cooperative Bank
                                            ['code' => 'coop_bank', 'name' => 'Co-operative Bank Pertama', 'logo' => 'coop.png'],
                                            // Additional Foreign Banks (Licensed Banking Institutions)
                                            ['code' => 'american_express', 'name' => 'American Express Bank Malaysia', 'logo' => null],
                                            ['code' => 'bnp_paribas', 'name' => 'BNP Paribas Malaysia', 'logo' => 'bnp-paribas-logo.png'],
                                            ['code' => 'bangkok_bank', 'name' => 'Bangkok Bank Malaysia', 'logo' => 'bangkok-bank.jpg'],
                                            ['code' => 'bank_of_america', 'name' => 'Bank of America Malaysia', 'logo' => 'bank_of_america.png'],
                                            ['code' => 'bank_of_china', 'name' => 'Bank of China Malaysia', 'logo' => 'bank_of_china.png'],
                                            ['code' => 'mufg', 'name' => 'MUFG Bank Malaysia', 'logo' => 'mufg.png'],
                                            ['code' => 'ccb', 'name' => 'China Construction Bank Malaysia', 'logo' => null],
                                            ['code' => 'deutsche_bank', 'name' => 'Deutsche Bank Malaysia', 'logo' => 'deutsche.png'],
                                            ['code' => 'india_international', 'name' => 'India International Bank Malaysia', 'logo' => 'iibm.jpg'],
                                            ['code' => 'icbc', 'name' => 'ICBC Malaysia', 'logo' => 'ICBC.png'],
                                            ['code' => 'jpmorgan', 'name' => 'J.P. Morgan Chase Bank Malaysia', 'logo' => 'jp-morgan.png'],
                                            ['code' => 'mizuho', 'name' => 'Mizuho Bank Malaysia', 'logo' => 'mizuho.jpg'],
                                            ['code' => 'sumitomo', 'name' => 'Sumitomo Mitsui Banking Corporation Malaysia', 'logo' => 'smbc.webp'],
                                            ['code' => 'scotiabank', 'name' => 'Bank of Nova Scotia Malaysia', 'logo' => 'scotia-bank.jpg']
                                        ];                                        
                                        @endphp
                                    </select>
                                    <input value="{{json_encode($malaysianBanks)}}" type="hidden" id="malay-banks">
                                    <div id="bank-logo-container" class="mt-2" style="display: none;">
                                        <img id="bank-logo" src="" alt="Bank Logo" style="height: 40px; object-fit: contain;">
                                    </div>
                                    @error('bank_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" id="account_number" name="account_number" 
                                           placeholder="Enter your account number" required maxlength="20"
                                           pattern="[0-9\-]+" title="Only numbers and hyphens allowed">
                                    @error('account_number')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" 
                                           placeholder="Enter account holder name as per bank records" required maxlength="100"
                                           value="{{ old('account_holder_name', auth()->user()->name ?? '') }}">
                                    <small class="form-text text-muted">Name must match your bank account exactly</small>
                                    @error('account_holder_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="error-message" class="alert alert-danger" style="display: none;"></div>

                                <button id="submit-button" type="submit" class="btn rounded-pill text-light btn-primary">
                                    Save Bank Account Details
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bank-form');
            const submitButton = document.getElementById('submit-button');
            const errorMessage = document.getElementById('error-message');
            const accountNumberInput = document.getElementById('account_number');
            // Format account number input (remove non-numeric characters except hyphens)
            accountNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9\-]/g, '');
                e.target.value = value;
            });

            // Form validation
            form.addEventListener('submit', function(event) {
                let isValid = true;
                errorMessage.style.display = 'none';

                // Validate account number
                const accountNumber = accountNumberInput.value.trim();
                if (accountNumber.length < 8 || accountNumber.length > 20) {
                    showError('Account number must be between 8 and 20 characters');
                    isValid = false;
                }

                if (!isValid) {
                    event.preventDefault();
                    return false;
                }

                // Disable submit button to prevent double submission
                submitButton.disabled = true;
                submitButton.textContent = 'Saving...';
            });

            function showError(message) {
                errorMessage.textContent = message;
                errorMessage.style.display = 'block';
                errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
@endpush