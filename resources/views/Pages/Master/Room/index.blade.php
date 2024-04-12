@push('mystyles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/choicesjs/styles/choices.min.css') }}?{{ rand() }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone.css') }}?{{ rand() }}">
@endpush
<div class="card">
    <div class="card-header">
        <div class="card-title">Kamar</div>
        <div class="card-actions">
            <button class="btn btn-primary" id="btn-add-room">
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
                    <table class="table table-vcard-center card-table table-striped" id="tb-room" style="width:100%">
                        <thead>
                            <tr class="text-center">
                                <th rowspan="2">#</th>
                                <th rowspan="2">Name</th>
                                <th rowspan="2">Kos</th>
                                <th rowspan="2">Kategori</th>
                                <th colspan="2">Fasilitas</th>
                                <th rowspan="2">Daftar Harga</th>
                                <th rowspan="2">#</th>
                            </tr>
                            <tr class="text-center">
                                <th>Kamar</th>
                                <th>Bersama</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modal-room" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kamar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="name-room" class="form-label">Nomor Kamar</label>
                        <input type="text" name="name-room" id="name-room" class="form-control">
                        <input type="hidden" name="slug-room" id="slug-room" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="" class="form-label">Rumah Kos</label>
                        <select type="text" name="home-room" id="home-room" class="form-select choices"></select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="" class="form-label">Kategori</label>
                        <select type="text" name="category-room" id="category-room"
                            class="form-select choices"></select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="" class="form-label">Fasilitas Kamar</label>
                        <div class="row">
                            <div class="col-6">
                                @foreach ($roomFacilities as $roomFacility)
                                    @if ($loop->odd)
                                        <label class="form-check">
                                            <input class="form-check-input" name="room-facility"
                                                id="{{ $roomFacility->id }}" value="{{ $roomFacility->id }}"
                                                type="checkbox">
                                            <span class="form-check-label">{{ $roomFacility->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-6">
                                @foreach ($roomFacilities as $roomFacility)
                                    @if ($loop->even)
                                        <label class="form-check">
                                            <input class="form-check-input" name="room-facility"
                                                id="{{ $roomFacility->id }}" value="{{ $roomFacility->id }}"
                                                type="checkbox">
                                            <span class="form-check-label">{{ $roomFacility->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6 mb-3">
                        <label for="room-price-daily" class="form-label">Harga Harian</label>
                        <input type="text" name="room-price-daily" id="room-price-daily" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="room-price-weekly" class="form-label">Harga Mingguan</label>
                        <input type="text" name="room-price-weekly" id="room-price-weekly" class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="room-price-monthly" class="form-label">Harga Bulanan</label>
                        <input type="text" name="room-price-monthly" id="room-price-monthly"
                            class="form-control">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="room-price-yearly" class="form-label">Harga Tahunan</label>
                        <input type="text" name="room-price-yearly" id="room-price-yearly" class="form-control">
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
                <button type="button" class="btn btn-primary" id="btn-save-room" data-csrf="{{ csrf_token() }}"
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
<div class="modal modal-blur fade" id="modal-upload-picture-room" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Gambar Kamar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dropzone-room-picture" class="dropzone" action="{{ url('masters/rooms/upload-picture') }}"
                    autocomplete="off" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="slugUploadRoom" id="slug-upload-room" />
                    <div class="fallback">
                        <input name="uploadPicture[]" type="file" />
                    </div>
                    <div class="dz-message">
                        <h3 class="dropzone-msg-title">Drag & drop File</h3>
                    </div>
                </form>
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
                <button type="button" class="btn btn-primary" id="btn-save-upload-room"
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
<div class="modal modal-blur fade" id="modal-view-picture-room" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gambar Kamar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="view-gambar-kamar"></div>
            </div>
        </div>
    </div>
</div>
@push('myscript')
    <script src="{{ asset('assets/vendor/choicesjs/scripts/choices.min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone-min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/js/Pages/Master/Room/app.js') }}?{{ rand() }}"></script>
@endpush