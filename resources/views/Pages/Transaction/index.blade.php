@extends('Layout.main')

@section('content')
    @php
        use App\Models\Room;
    @endphp
    @push('mystyles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-3">
                                    <select name="category-room" id="category-room" class="form-select choices"></select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="tb-room" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No. Kamar</th>
                                            <th>Kos</th>
                                            <th>Kategori</th>
                                            <th>Penyewa</th>
                                            <th>Tanggal Sewa</th>
                                            <th>Status</th>
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
    @push('myscript')
        <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/Pages/Transaction/app.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
