@push('mystyles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
@endpush
<div class="card">
    <div class="card-header">
        <div class="card-title">Price Laundry</div>
        <div class="card-actions">
            <button class="btn btn-primary" id="btn-add-price-laundry">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
                Tambah Data
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-vcard-center card-table table-striped" id="tb-price-laundry"
                        style="width:100%">
                        <thead>
                            <tr class="text-center">
                                <th>#</th>
                                <th>Jenis</th>
                                <th>Berat</th>
                                <th>Harga</th>
                                <th>#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modal-price-laundry" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Price Laundry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="type-laundry" class="form-label">Jenis</label>
                        <input name="type-laundry" id="type-laundry" class="form-control" />
                        <input type="hidden" name="kode-item-price-laundry" id="kode-item-price-laundry"
                            class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="berat-price-laundry" class="form-label">Berat</label>
                        <input type="number" name="berat-price-laundry" id="berat-price-laundry"
                            class="form-control" />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="price-laundry" class="form-label">Harga</label>
                        <input type="text" name="price-laundry" id="price-laundry" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" data-bs-dismiss="modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-off">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20.042 16.045a9 9 0 0 0 -12.087 -12.087m-2.318 1.677a9 9 0 1 0 12.725 12.73" />
                        <path d="M3 3l18 18" />
                    </svg>
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btn-save-price-laundry"
                    data-csrf="{{ csrf_token() }}" data-bs-dismiss="modal">
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
@push('myscript')
    <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/js/Pages/Master/priceLaundry/app.js') }}?{{ rand() }}"></script>
@endpush
