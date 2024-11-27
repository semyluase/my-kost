@extends('Layout.main')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    @push('mystyles')
        <link rel="stylesheet"
            href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
    @endpush
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
                        <a href="{{ url('') }}/transactions/orders/cleaning/create" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Tambah Transaksi
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
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="input-icon mb-2">
                                    <input class="form-control " placeholder="Select a date" id="datepicker-icon">
                                </div>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-primary" id="btn-search">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                        <path d="M21 21l-6 -6" />
                                    </svg>
                                    Search
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tb-cleaning" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>No. Transaksi</th>
                                                <th>No. Kamar</th>
                                                <th>Tgl Cleaning</th>
                                                <th>Tgl Mulai</th>
                                                <th>Tgl Selesai</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
        <script src="{{ asset('assets/vendor/momentJS/moment.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/js/main.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/Pages/Service/Cleaning/app.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
