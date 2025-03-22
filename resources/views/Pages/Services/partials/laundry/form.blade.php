@php
    use Illuminate\Support\Str;
@endphp
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <label for="nobukti" class="form-label">No Transaksi</label>
                <input type="text" name="nobukti" id="nobukti" class="form-control bg-gray-500" readonly
                    value="{{ $laundry ? $laundry->nobukti : '' }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="no-kamar" class="form-label">No Kamar</label>
                <select name="no-kamar" id="no-kamar" class="form-select choices"></select>
            </div>
            <div class="col-6">
                <label for="quantity" class="form-label">Berat</label>
                <input type="number" name="quantity" id="quantity" class="form-control" step="any"
                    value="{{ $laundry ? $laundry->qty_laundry : '' }}" {{ $laundry ? 'disabled' : '' }}>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label for="" class="mb-3">Kategori</label>
                <div class="row">
                    <div class="col-6">
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            @foreach ($laundryPrice as $price)
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="category-laundry" value="{{ $price->kode_item }}"
                                        class="form-selectgroup-input" {{ $laundry ? 'disabled' : '' }}
                                        data-id-laundry="{{ $price->id }}" data-weight="{{ $price->weight }}"
                                        data-price={{ $price->price }}
                                        {{ $laundry ? $laundry->laundry_id == $price->id : ($loop->first ? 'checked' : '') }}>
                                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                                        <div class="me-3">
                                            <span class="form-selectgroup-check"></span>
                                        </div>
                                        <div>
                                            {{ $price->name }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12 justify-content-end">
                <label for="sub-total" class="form-label">Sub Total</label>
                <div class="row mb-3">
                    <div class="col-3">Rp.</div>
                    <div class="col-9">
                        <input type="text" name="sub-total" id="sub-total" class="form-control bg-gray-500" readonly
                            value="{{ $laundry ? $laundry->harga_laundry * $laundry->qty_laundry : 0 }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="">Pembayaran</label>
                <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                    @foreach ($payments as $pay)
                        <label class="form-selectgroup-item flex-fill">
                            <input type="radio" name="payment" value="{{ $pay->slug }}"
                                class="form-selectgroup-input" {{ $loop->first ? 'checked' : '' }}>
                            <div class="form-selectgroup-label d-flex align-items-center p-3">
                                <div class="me-3">
                                    <span class="form-selectgroup-check"></span>
                                </div>
                                <div>
                                    {{ $pay->tipe }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="col-6">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="pembayaran" class="form-label">Total Bayar</label>
                        <div class="row mb-3">
                            <div class="col-2">
                                Rp.
                            </div>
                            <div class="col-10">
                                <input type="number" name="total-bayar" id="total-bayar" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="kembalian" class="form-label">Kembalian</label>
                        <div class="row mb-3">
                            <div class="col-2">
                                Rp.
                            </div>
                            <div class="col-10">
                                <input type="number" name="total-kembali" id="total-kembali"
                                    class="form-control bg-gray-500" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ url('') }}/transactions/orders/laundry" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
                    <button class="btn btn-primary {{ $laundry ? '' : 'd-none' }}" id="btn-take-laundry"
                        data-csrf="{{ csrf_token() }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-paper-bag">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M8 3h8a2 2 0 0 1 2 2v1.82a5 5 0 0 0 .528 2.236l.944 1.888a5 5 0 0 1 .528 2.236v5.82a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-5.82a5 5 0 0 1 .528 -2.236l1.472 -2.944v-3a2 2 0 0 1 2 -2z" />
                            <path d="M14 15m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M6 21a2 2 0 0 0 2 -2v-5.82a5 5 0 0 0 -.528 -2.236l-1.472 -2.944" />
                            <path d="M11 7h2" />
                        </svg>
                        Ambil Laundry
                    </button>
                    <button class="btn btn-primary {{ $laundry ? 'd-none' : '' }}" id="btn-save"
                        data-csrf="{{ csrf_token() }}">
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
