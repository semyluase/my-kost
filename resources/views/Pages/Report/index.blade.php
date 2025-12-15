@extends('Layout.main')

@section('content')
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

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a href="#tab-transaction" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                                    role="tab">Transaksi</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a href="#tab-checkout" class="nav-link" data-bs-toggle="tab" aria-selected="false"
                                    tabindex="-1" role="tab">Checkout</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tab-transaction" role="tabpanel">
                                <h4>Laporan Transaksi</h4>
                                <div>
                                    @livewire('Reports.report')
                                </div>
                            </div>
                            <div class="tab-pane" id="tab-checkout" role="tabpanel">
                                <h4>Checkout</h4>
                                <div>
                                    @livewire('Reports.Checkout.ListCheckout')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
