<div class="modal modal-blur fade" id="modal-category" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="name-category" class="form-label">Nama</label>
                        <input type="text" name="name-category" id="name-category" class="form-control">
                        <input type="hidden" name="slug-category" id="slug-category" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="" class="form-label">Fasilitas Kamar</label>
                        <div class="row">
                            <div class="col-6">
                                @foreach ($categoryFacilities as $categoryFacility)
                                    @if ($loop->odd)
                                        <label class="form-check">
                                            <input class="form-check-input" name="category-facility"
                                                id="{{ $categoryFacility->id }}" value="{{ $categoryFacility->id }}"
                                                type="checkbox">
                                            <span class="form-check-label">{{ $categoryFacility->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-6">
                                @foreach ($categoryFacilities as $categoryFacility)
                                    @if ($loop->even)
                                        <label class="form-check">
                                            <input class="form-check-input" name="category-facility"
                                                id="{{ $categoryFacility->id }}" value="{{ $categoryFacility->id }}"
                                                type="checkbox">
                                            <span class="form-check-label">{{ $categoryFacility->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 mb-3">
                        <label for="category-price-daily" class="form-label">Harga Harian</label>
                        <input type="text" name="category-price-daily" id="category-price-daily"
                            class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="category-price-weekly" class="form-label">Harga Mingguan</label>
                        <input type="text" name="category-price-weekly" id="category-price-weekly"
                            class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="category-price-monthly" class="form-label">Harga Bulanan</label>
                        <input type="text" name="category-price-monthly" id="category-price-monthly"
                            class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="category-price-yearly" class="form-label">Harga Tahunan</label>
                        <input type="text" name="category-price-yearly" id="category-price-yearly"
                            class="form-control">
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
                <button type="button" class="btn btn-primary" id="btn-save-category" data-csrf="{{ csrf_token() }}"
                    data-bs-dismiss="modal">
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
