@php
    use Illuminate\Support\Carbon;
@endphp
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <label for="nobukti" class="form-label">No Transaksi</label>
                <input type="text" name="nobukti" id="nobukti" class="form-control bg-gray-500" readonly
                    value="{{ $cleaning ? $cleaning->nobukti : '' }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label for="no-kamar" class="form-label">No Kamar</label>
                <select name="no-kamar" id="no-kamar" class="form-select choices"></select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="kategori" class="form-label">Kategori Pembersihan</label>
                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                    @foreach ($categoryCleaning as $value)
                        <label class="form-selectgroup-item flex-fill">
                            <input type="radio" name="kategori-cleaning" id="kategori-cleaning"
                                value="{{ $value->kode_item }}" class="form-selectgroup-input"
                                {{ $cleaning ? ($value->kode_item == $cleaning->code_item ? 'checked' : '') : ($loop->first ? 'checked' : '') }}
                                data-harga="{{ $value->price }}">
                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                <div class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </div>
                                <div>
                                    {{ $value->description }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="tanggal-cleaning" class="form-label">Tanggal</label>
                <div class="input-icon mb-2">
                    <input class="form-control" placeholder="Select a date" id="tanggal">
                    <span class="input-icon-addon">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/calendar -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-1">
                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                            </path>
                            <path d="M16 3v4"></path>
                            <path d="M8 3v4"></path>
                            <path d="M4 11h16"></path>
                            <path d="M11 15h1"></path>
                            <path d="M12 15v3"></path>
                        </svg>
                    </span>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="waktu-cleaning" class="form-label">Jam (<span class="text-gray-500">format
                        hh:mm</span>)</label>
                <input type="text" name="waktu-cleaning" id="waktu-cleaning" class="form-control"
                    value="{{ $cleaning ? Carbon::parse($cleaning->tgl_request_cleaning)->isoFormat('HH:mm') : '' }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Cara Pembayaran</label>
                        <div class="form-selectgroup">
                            <label class="form-selectgroup-item">
                                <input type="radio" name="select-payment" id="select-payment" value="saldo"
                                    class="form-selectgroup-input" checked="">
                                <span class="form-selectgroup-label">Saldo</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="select-payment" id="select-payment" value="transfer"
                                    class="form-selectgroup-input">
                                <span class="form-selectgroup-label">Transfer</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="select-payment" id="select-payment" value="cash"
                                    class="form-selectgroup-input">
                                <span class="form-selectgroup-label">Cash</span>
                            </label>
                            <label class="form-selectgroup-item">
                                <input type="radio" name="select-payment" id="select-payment" value="qris"
                                    class="form-selectgroup-input">
                                <span class="form-selectgroup-label">Qris</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="sub-total" class="form-label">Sub Total</label>
                        <input type="text" name="sub-total" id="sub-total" class="form-control bg-gray-500"
                            readonly>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="payment" class="form-label">Total Pembayaran</label>
                        <input type="text" name="payment" id="payment" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="kembalian" class="form-label">Total Kembalian</label>
                        <input type="text" name="kembalian" id="kembalian" class="form-control bg-gray-500"
                            readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ url('') }}/transactions/orders" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
                    <button class="btn btn-primary" id="btn-save" data-csrf="{{ csrf_token() }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-device-floppy">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
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
