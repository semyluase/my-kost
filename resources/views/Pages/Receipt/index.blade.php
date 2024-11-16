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
                        <a href="{{ url('') }}/inventories/receipts/create" class="btn btn-primary" id="btn-out">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 5l0 14" />
                                <path d="M5 12l14 0" />
                            </svg>
                            Tambah Barang Masuk
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
                @if ($stocks)
                    @foreach ($stocks as $s)
                        <div class="col-3">
                            <div class="card">
                                <div class="row row-0">
                                    <div class="col-3">
                                        <!-- Photo -->
                                        <img src="data:image/{{ Str::afterLast($s->foodSnack->picture->file_name, '.') }};base64,{{ $s->foodSnack->picture->blob }}"
                                            class="w-100 h-100 object-cover card-img-start" alt="{{ $s->foodSnack->name }}">
                                    </div>
                                    <div class="col">
                                        <div class="card-body">
                                            <h3 class="card-title">{{ $s->foodSnack->name }}
                                                ({{ $s->foodSnack->code_item }})
                                            </h3>
                                            <h1 class="text-secondary fs-1">{{ $s->qty }}
                                            </h1>
                                            <div class="row">
                                                <div class="col-12">
                                                    <span class="badge bg-indigo text-indigo-fg">
                                                        Rp. {{ number_format($s->harga_beli, 2, ',', '.') }}
                                                    </span>
                                                    <span class="badge bg-blue text-blue-fg">
                                                        Rp. {{ number_format($s->harga_jual, 2, ',', '.') }}
                                                    </span>
                                                    <span class="badge bg-cyan text-cyan-fg">
                                                        {{ $s->foodSnack->category == 'F' ? 'Food' : ($s->foodSnack->category == 'D' ? 'Drink' : 'Snack') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="card-footer">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <a href="javascripts:;" id="link-detail"
                                                        onclick="fnTransaction.showDetail('{{ $s->code_item }}')">
                                                        Details
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-right">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M5 12l14 0" />
                                                            <path d="M15 16l4 -4" />
                                                            <path d="M15 8l4 4" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
                <div class="col-12">
                    {{-- {{ $stocks->links() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection
