<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-6">
                <div class="row mb-3">
                    <div class="col">
                        <label for="nobukti" class="form-label">No. Transaksi</label>
                        <input type="text" name="nobukti" id="nobukti" class="form-control bg-gray-500" readonly
                            value={{ $transaction ? $transaction->nobukti : '' }}>
                        <input type="hidden" name="id-detail" id="id-detail" class="form-control bg-gray-500" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="kodebrg" class="form-label">Barcode</label>
                        <input type="text" name="kodebrg" id="kodebrg" class="form-control" autofocus>
                        <input type="hidden" name="kategori" id="kategori" class="form-control" autofocus>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="namabrg" class="form-label">Nama Barang</label>
                        <input type="text" name="namabrg" id="namabrg" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="harga-beli" class="form-label">Harga Beli</label>
                        <input type="text" name="harga-beli" id="harga-beli" class="form-control">
                    </div>
                    <div class="col-6">
                        <label for="sub-total" class="form-label">Sub Total</label>
                        <input type="number" name="sub-total" id="sub-total" class="form-control bg-gray-500" readonly>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="row mb-3">
                    <div class="col-6">
                        <label for="tgl" class="form-label">Tanggal Transaksi</label>
                        <div class="datepicker-inline" id="tgl"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row justify-content-end ms-auto me-0">
            <div class="col-12 float-end">
                <div class="d-flex gap-2 me-0 ms-auto">
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
                    <button class="btn btn-success" id="btn-posting" data-csrf="{{ csrf_token() }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-checks">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 12l5 5l10 -10" />
                            <path d="M2 12l5 5m5 -5l5 -5" />
                        </svg>
                        Posting
                    </button>
                    <button class="btn btn-danger" id="btn-delete-bulk" data-csrf="{{ csrf_token() }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7h16" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            <path d="M10 12l4 4m0 -4l-4 4" />
                        </svg>
                        Hapus Semua
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
