@php
    use App\Models\Pembayaran;

    $payments = Pembayaran::where('is_active', true)->where('slug', '<>', 'saldo')->get();
@endphp
<div class="modal fade" id="modal-laundry-payment" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pembayaran Laundry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="category" class="form-label">Nama Paket</label>
                        <input type="text" name="category" id="category" class="form-control bg-gray-500" readonly>
                        <input type="hidden" name="nobukti" id="nobukti" class="form-control bg-gray-500" readonly>
                    </div>
                    <div class="col-6">
                        <label for="qty" class="form-label">Berat Paket</label>
                        <input type="text" name="qty" id="qty" class="form-control bg-gray-500" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                            @foreach ($payments as $pay)
                                <label class="form-selectgroup-item flex-fill">
                                    <input type="radio" name="payment" id="payment" value="{{ $pay->slug }}"
                                        class="form-selectgroup-input">
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
                                <label for="price" class="form-label">Harga</label>
                                <div class="row mb-3">
                                    <div class="col-2">
                                        Rp.
                                    </div>
                                    <div class="col-10">
                                        <input type="number" name="price" id="price"
                                            class="form-control bg-gray-500" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" data-bs-dismiss="modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M18 6l-12 12" />
                        <path d="M6 6l12 12" />
                    </svg>
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btn-payment" data-csrf="{{ csrf_token() }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 15h-3a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v3" />
                        <path d="M7 9m0 1a1 1 0 0 1 1 -1h12a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1h-12a1 1 0 0 1 -1 -1z" />
                        <path d="M12 14a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    </svg>
                    Bayar
                </button>
            </div>
        </div>
    </div>
</div>
