@extends('Layout.main')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    @push('mystyles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
        <style>
            .choices__list--dropdown {
                z-index: 3000 !important;
            }

            .choices__list--multiple {
                z-index: 2000 !important;
            }
        </style>
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
                <div class="col-6">
                    @include('Pages.Services.partials.cleaning.form')
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
        <script>
            let nobukti = "{{ $cleaning ? $cleaning->nobukti : '' }}"
            let noKamar = "{{ $cleaning ? $cleaning->no_room : '' }}"
        </script>
        <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/Pages/Service/Cleaning/form.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
