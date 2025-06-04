@extends('layout.app')

<head>
    <link rel="stylesheet" href="{{ asset('/assets/css/common/settings.css') }}">
    @vite(['resources/sass/app.scss', 'resources/js/app.js',  'resources/js/alpine/settings.js'])
</head>
@section('body-class', 'wallet')

@section('content')
    @include('includes.Navbar')
    <input type="hidden" id="transactions-data" value="{{json_encode($transactions)}}">

    <main  v-scope="TransactionComponent()" @vue:mounted="init">
        <div class="row my-2 px-5 py-2"> 
        <h3 class="col-12 col-md-6 my-2 py-0 text-start">My Transactions</h3>
        <a 
            href="{{route('wallet.dashboard')}}" 
            style="z-index: 99;"
            id="main-nav"  class="cursor-pointer my-2 d py-0 position-relative col-12 col-md-6 text-start text-md-end">
            <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0" />
                </svg>
            </span>
            <span>Go back</span>
        </a>
    </div>
        <div class=" mx-auto px-0 container-main ">
            <div class="card px-0 py-0 border border-2 mx-auto border-secondary mt-2 w-95-lg-75 rounded-30px">
                <div class="card-body px-4 py-3">
                    <div class="my-3">
                        <div class="table-responsive mb-4 " v-cloak v-if="transactions && transactions[0]">
                            <table class="transaction-history__table table ">
                                <thead class="transaction-table__header">
                                    <tr>
                                        <th scope="col"
                                            class="transaction-table__header-cell bg-secondary text-white py-3">Date
                                        </th>
                                        <th scope="col"
                                            class="transaction-table__header-cell bg-secondary text-white py-3">
                                            Transaction</th>
                                        <th scope="col"
                                            class="transaction-table__header-cell bg-secondary text-white py-3">Type
                                        </th>
                                        
                                            <th scope="col"
                                            class="transaction-table__header-cell bg-secondary text-white py-3">
                                            Change</th>
                                        <th scope="col"
                                            class="transaction-table__header-cell bg-secondary text-white py-3">Total
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="transaction in transactions" :key="transaction.id" class="transaction-row">
                                        <x-wallet.transaction-item :fullPage="true" />
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="my-3">
                            <br>
                            <x-wallet.no-list :text="'No transactions available!'" />
                        </div>

                        <div v-if="hasMore" class="text-center  my-4" v-cloak>
                            <button v-on:click="loadMore" :disabled="loading"
                                class="btn text-light rounded-pill btn-primary">
                                <span v-if="loading" v-cloak class="spinner-border spinner-border-sm me-2"></span>
                                <span v-if="loading" v-cloak>Loading...</span>
                                <span v-else>Load More</span>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <br>
        </div>
                <input type="hidden" id="wallet" value="{{ json_encode($wallet) }}">

    </main>
@endsection
