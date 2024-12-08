<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <label for="no-kamar" class="form-label">No. Kamar</label>
                <select name="no-kamar" id="no-kamar" class="form-select choices"></select>
                <input type="hidden" name="nobukti" id="nobukti" class="form-control"
                    value="{{ $foodSnack ? $foodSnack->nobukti : '' }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="kode-barang" class="form-label">Kode Barang</label>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="kode-barang" name="kode-barang" autofocus>
                    <button class="btn btn-primary" type="button" id="btn-search-barang">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="col-6">
                <label for="nama-barang" class="form-label">Nama Barang</label>
                <input type="text" name="nama-barang" id="nama-barang" class="form-control bg-gray-500" readonly>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="harga-barang" class="form-label">Harga Satuan</label>
                <input type="text" name="harga-barang" id="harga-barang" class="form-control bg-gray-500" readonly>
            </div>
            <div class="col-3">
                <label for="jumlah-barang" class="form-label">Jumlah</label>
                <div class="input-group mb-2">
                    <button class="btn btn-primary" type="button" id="btn-kurang-jumlah">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-minus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </button>
                    <input type="text" class="form-control" id="jumlah-barang" name="jumlah-barang">
                    <button class="btn btn-primary" type="button" id="btn-tambah-jumlah">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 5l0 14" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div class="row mb-3 align-items-center">
            <div class="col-6 d-none" id="sub-total-col">
                <div class="h6">Sub Total</div>
                <div class="text-dark" id="sub-total" style="font-size: 5rem !important;"></div>
            </div>
            <div class="col-6 ms-auto d-none" id="total-col">
                <div class="h6">Total</div>
                <div class="text-dark" id="total" style="font-size: 5rem !important;"></div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col-auto ms-auto">
                <a href="{{ url('') }}/transactions/orders/food-snack" class="btn btn-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cancel">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                        <path d="M18.364 5.636l-12.728 12.728" />
                    </svg>
                    Batal
                </a>
                <button class="btn btn-success" id="btn-pembayaran">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cash">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                        <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
                    </svg>
                    Pembayaran
                </button>
                <button class="btn btn-primary" id="btn-save" data-type="save-data"
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
