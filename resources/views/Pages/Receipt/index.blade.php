@extends('Layout.main')

@section('content')
    @push('mystyles')
        <link rel="stylesheet"
            href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
    @endpush
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
                        <button class="btn btn-primary btn-3" id="btn-buat-laporan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            </svg>
                            Buat Laporan
                        </button>
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
    @include('Pages.Receipt.partials.modals.modalGenerateReport')
    @push('myscript')
        <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/litepicker.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/momentJS/moment.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/Pages/Receipt/create.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
