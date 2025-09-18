@extends('Layout.main')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp
    @push('mystyles')
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
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="member" class="form-label">Member (No. HP/Username/Email)</label>
                                    <input type="text" name="member" id="member" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Jumlah Top Up</label>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="form-selectgroup">
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-topup" id="select-topup"
                                                        value="10000" class="form-selectgroup-input" checked="">
                                                    <span class="form-selectgroup-label">Rp.
                                                        {{ number_format(10000, 2) }}</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-topup" id="select-topup"
                                                        value="20000" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Rp.
                                                        {{ number_format(20000, 2) }}</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-topup" id="select-topup"
                                                        value="50000" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Rp.
                                                        {{ number_format(50000, 2) }}</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-topup" id="select-topup"
                                                        value="100000" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Rp.
                                                        {{ number_format(100000, 2) }}</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-topup" id="select-topup"
                                                        value="200000" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Rp.
                                                        {{ number_format(200000, 2) }}</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-topup" id="select-topup"
                                                        value="500000" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Rp.
                                                        {{ number_format(500000, 2) }}</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <input type="number" name="topup" id="topup" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label">Cara Pembayaran</label>
                                            <div class="form-selectgroup">
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-payment" id="select-payment"
                                                        value="transfer" class="form-selectgroup-input" checked="">
                                                    <span class="form-selectgroup-label">Transfer</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="select-payment" id="select-payment"
                                                        value="cash" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Cash</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label for="sub-total" class="form-label">Sub Total</label>
                                            <input type="text" name="sub-total" id="sub-total"
                                                class="form-control bg-gray-500" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="payment" class="form-label">Total Pembayaran</label>
                                    <input type="number" name="payment" id="payment" class="form-control">
                                </div>
                                <div class="col-6">
                                    <label for="kembalian" class="form-label">Total Kembalian</label>
                                    <input type="text" name="kembalian" id="kembalian"
                                        class="form-control bg-gray-500" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row alignt-item-center">
                                <div class="col-auto ms-auto">
                                    <a href="{{ url('') }}/transactions/orders" class="btn btn-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M5 12l14 0" />
                                            <path d="M5 12l6 6" />
                                            <path d="M5 12l6 -6" />
                                        </svg>
                                        Kembali
                                    </a>
                                    <button class="btn btn-primary" id="btn-save" data-csrf="{{ csrf_token() }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                            <path d="M14 4l0 4l-6 0l0 -4" />
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <div class="card-body overflow-scroll">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div id="detail-transaction"></div>
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
        <script src="{{ asset('assets/js/Pages/Service/TopUp/app.js') }}?{{ rand() }}"></script>
    @endpush
@endsection
