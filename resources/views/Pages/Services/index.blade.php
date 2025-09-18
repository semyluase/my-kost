@extends('Layout.main')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    <!-- Page header -->
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <h2 class="page-title">
                        {{ $pageTitle }}
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a class="btn btn-primary" href="{{ url('') }}/transactions/orders/top-up">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                                <path d="M12 19h-4a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v2.5" />
                                <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M16 19h6" />
                                <path d="M19 16v6" />
                            </svg>
                            Top Up
                        </a>
                        <a class="btn btn-primary" href="{{ url('') }}/transactions/orders/laundry/create">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-wash-machine">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                                <path d="M12 14m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path d="M8 6h.01" />
                                <path d="M11 6h.01" />
                                <path d="M14 6h2" />
                                <path d="M8 14c1.333 -.667 2.667 -.667 4 0c1.333 .667 2.667 .667 4 0" />
                            </svg>
                            Laundry
                        </a>
                        <a class="btn btn-primary" href="{{ url('') }}/transactions/orders/cleaning/create">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-hotel-service">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M8.5 10a1.5 1.5 0 0 1 -1.5 -1.5a5.5 5.5 0 0 1 11 0v10.5a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2c0 -1.38 .71 -2.444 1.88 -3.175l4.424 -2.765c1.055 -.66 1.696 -1.316 1.696 -2.56a2.5 2.5 0 1 0 -5 0a1.5 1.5 0 0 1 -1.5 1.5z" />
                            </svg>
                            Cleaning
                        </a>
                        <a class="btn btn-primary" href="{{ url('') }}/transactions/orders/food-snack/create">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-meat">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M13.62 8.382l1.966 -1.967a2 2 0 1 1 3.414 -1.415a2 2 0 1 1 -1.413 3.414l-1.82 1.821" />
                                <path
                                    d="M5.904 18.596c2.733 2.734 5.9 4 7.07 2.829c1.172 -1.172 -.094 -4.338 -2.828 -7.071c-2.733 -2.734 -5.9 -4 -7.07 -2.829c-1.172 1.172 .094 4.338 2.828 7.071z" />
                                <path d="M7.5 16l1 1" />
                                <path
                                    d="M12.975 21.425c3.905 -3.906 4.855 -9.288 2.121 -12.021c-2.733 -2.734 -8.115 -1.784 -12.02 2.121" />
                            </svg>
                            Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card col-card">
                    <div class="card-body">
                        @livewire('Service.AllService')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
