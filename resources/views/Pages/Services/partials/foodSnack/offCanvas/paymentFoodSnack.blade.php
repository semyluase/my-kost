@desktop
    <div class="offcanvas offcanvas-end w-50" tabindex="-1" id="offcanvasPayment" aria-labelledby="offcanvasPaymentLabel">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title" id="offcanvasPaymentLabel">Pembayaran</h2>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="payment-section"></div>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="form-label">Tipe Pembayaran</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipe-pembayaran" id="tipe-pembayaran" value="transfer"
                                class="form-selectgroup-input" checked="">
                            <span class="form-selectgroup-label">Transfer</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipe-pembayaran" id="tipe-pembayaran" value="cash"
                                class="form-selectgroup-input">
                            <span class="form-selectgroup-label">Cash</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipe-pembayaran" id="tipe-pembayaran" value="saldo"
                                class="form-selectgroup-input">
                            <span class="form-selectgroup-label">Saldo</span>
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <label for="jumlah-pembayaran" class="form-label">Total Bayar</label>
                    <input type="number" class="form-control" name="jumlah-pembayaran" id="jumlah-pembayaran">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-6">
                    <label for="kembalian" class="form-label">Kembalian</label>
                    <input type="text" name="kembalian" class="form-control bg-gray-500" id="kembalian" readonly>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="row align-items-center">
                <div class="col-auto ms-auto">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="offcanvas">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="btn-save-payment" data-csrf="{{ csrf_token() }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                            <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
                        </svg>
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
@elsedesktop
    <div class="offcanvas offcanvas-end w-100" tabindex="-1" id="offcanvasPayment" aria-labelledby="offcanvasPaymentLabel">
        <div class="offcanvas-header">
            <h2 class="offcanvas-title" id="offcanvasPaymentLabel">Pembayaran</h2>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="payment-section"></div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Pembayaran</label>
                    <div class="form-selectgroup">
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipe-pembayaran" id="tipe-pembayaran" value="transfer"
                                class="form-selectgroup-input" checked="">
                            <span class="form-selectgroup-label">Transfer</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipe-pembayaran" id="tipe-pembayaran" value="cash"
                                class="form-selectgroup-input">
                            <span class="form-selectgroup-label">Cash</span>
                        </label>
                        <label class="form-selectgroup-item">
                            <input type="radio" name="tipe-pembayaran" id="tipe-pembayaran" value="saldo"
                                class="form-selectgroup-input">
                            <span class="form-selectgroup-label">Saldo</span>
                        </label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="jumlah-pembayaran" class="form-label">Total Bayar</label>
                    <input type="number" class="form-control" name="jumlah-pembayaran" id="jumlah-pembayaran">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label for="kembalian" class="form-label">Kembalian</label>
                    <input type="text" name="kembalian" class="form-control bg-gray-500" id="kembalian" readonly>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="row align-items-center">
                <div class="col-auto ms-auto">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="offcanvas">
                        Batal
                    </button>
                    <button class="btn btn-primary" id="btn-save-payment" data-csrf="{{ csrf_token() }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                            <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
                        </svg>
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
@enddesktop
