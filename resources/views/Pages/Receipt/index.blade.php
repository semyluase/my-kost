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
                        {{-- <a href="{{ url('') }}/inventories/receipts/create" class="btn btn-primary" id="btn-out">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Tambah Barang Masuk
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-deck row-cards">
                <div class="col-6 mb-3">
                    @livewire('Receipt.AddGoods')
                </div>
                <div class="col-6 mb-3">
                    @livewire('Receipt.MasterGoods')
                </div>
                <div class="col-12 mb-3">
                    @livewire('Receipt.ListGoods')
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
    @endpush
@endsection
