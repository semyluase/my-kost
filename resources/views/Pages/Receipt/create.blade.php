@extends('Layout.main')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    @push('mystyles')
        <link rel="stylesheet"
            href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/toastify/toastify.css') }}?{{ rand() }}">
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
                    @include('Pages.Receipt.partials.form')
                </div>
                <div class="col-6 mb-3">
                    @include('Pages.Receipt.partials.goods')
                </div>
                <div class="col-12">
                    @include('Pages.Receipt.partials.table')
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
        <script src="{{ asset('assets/vendor/momentJS/moment.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/bundle.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/toastify/toastify.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/pages/Receipt/create.js') }}?{{ rand() }}"></script>
    @endpush
@endsection