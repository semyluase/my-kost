@extends('Layout.main')

@section('content')
    @push('mystyles')
        <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
        <link rel="stylesheet" href="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone.css') }}?{{ rand() }}">
        <link rel="stylesheet"
            href="{{ asset('assets/vendor/tabler/libs/litepicker/dist/css/litepicker.css') }}?{{ rand() }}">
    @endpush
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Number;
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
                            @if (!Carbon::parse($room->rent->end_date)->equalTo(Carbon::now('Asia/Jakarta')))
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert-danger" role="alert">
                                            <div class="d-flex">
                                                <div>
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon alert-icon icon-2">
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                                        <path d="M12 9h.01"></path>
                                                        <path d="M11 12h1v4h1"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    Kamar ini melakukan Checkout sebelum waktunya. Mohon diinfokan kepada
                                                    Penghuni bahwa <b>Uang Sewa</b> tidak akan kembali, yang akan
                                                    dikembalikan
                                                    hanya <b>Deposit</b> dan akan <b>dipotong jika kamar yang dihuni
                                                        mengalami
                                                        kerusakan</b>.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row mb-3">
                                <div class="col-lg-6 col-sm-12">
                                    <h3 class="fw-semibold">Data Kamar</h3>
                                    @include('Pages.Transaction.partials.checkout.room', [
                                        'room' => $room,
                                    ])
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <h3 class="fw-semibold">Data Sewa</h3>
                                    @include('Pages.Transaction.partials.checkout.dataCheckout')
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex">
                                <a href="{{ url('transactions/rent-rooms') }}" class="btn btn-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 6l-12 12" />
                                        <path d="M6 6l12 12" />
                                    </svg>
                                    Batal
                                </a>
                                <a href="javascript:;" class="btn btn-primary ms-auto" id="btn-save"
                                    data-csrf="{{ csrf_token() }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                        <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M14 4l0 4l-6 0l0 -4" />
                                    </svg>
                                    Simpan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('myscript')
        <script src="{{ asset('assets/vendor/momentJS/moment.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/tabler/libs/litepicker/dist/litepicker.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone-min.js') }}?{{ rand() }}"></script>
        <script src="{{ asset('assets/js/Pages/Transaction/checkout/app.js') }}?{{ rand() }}"></script>
        <script>
            nomorKamar = "{{ $room->number_room }}"
        </script>
    @endpush
@endsection
