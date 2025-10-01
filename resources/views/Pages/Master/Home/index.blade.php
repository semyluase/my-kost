@push('mystyles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone.css') }}?{{ rand() }}">
@endpush
<div class="card">
    <div class="card-header">
        <div class="card-title">Identitas Kos</div>
        <div class="card-actions">
            <button class="btn btn-primary" id="btn-add-home">
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
                    <table class="table table-striped" id="tb-home" style="width:100%;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Kota - Alamat</th>
                                <th>Aturan Kos</th>
                                <th>Gambar Kos</th>
                                <th>#</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-blur fade" id="modal-home" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Identitas Kos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="name-home" class="form-label">Nama</label>
                        <input type="text" name="name-home" id="name-home" class="form-control">
                        <input type="hidden" name="slug-home" id="slug-home" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="city-home" class="form-label">Kota</label>
                        <input type="text" name="city-home" id="city-home" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="phone-home" class="form-label">No. Telp</label>
                        <input type="text" name="phone-home" id="phone-home" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="email-home" class="form-label">Email</label>
                        <input type="email" name="email-home" id="email-home" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="address-home" class="form-label">Alamat</label>
                        <textarea name="address-home" id="address-home" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="" class="form-label">Fasilitas Bersama</label>
                        <div class="row">
                            <div class="col-6">
                                @foreach ($sharedFacilities as $sharedFacility)
                                    @if ($loop->odd)
                                        <label class="form-check">
                                            <input class="form-check-input" name="shared-facility"
                                                id="{{ $sharedFacility->id }}" value="{{ $sharedFacility->id }}"
                                                type="checkbox">
                                            <span class="form-check-label">{{ $sharedFacility->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-6">
                                @foreach ($sharedFacilities as $sharedFacility)
                                    @if ($loop->even)
                                        <label class="form-check">
                                            <input class="form-check-input" name="shared-facility"
                                                id="{{ $sharedFacility->id }}" value="{{ $sharedFacility->id }}"
                                                type="checkbox">
                                            <span class="form-check-label">{{ $sharedFacility->name }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="" class="form-label">Aturan</label>
                        <div class="row">
                            <div class="col-12">
                                @foreach ($rules as $rule)
                                    <label class="form-check">
                                        <input class="form-check-input" name="rule" id="{{ $rule->id }}"
                                            value="{{ $rule->id }}" type="checkbox">
                                        <span class="form-check-label">{{ $rule->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
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
                <button type="button" class="btn btn-primary" id="btn-save-home" data-csrf="{{ csrf_token() }}"
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
<div class="modal modal-blur fade" id="modal-upload-picture-home" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Gambar Rumah Kost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dropzone-home-picture" class="dropzone" action="{{ url('masters/homes/upload-picture') }}"
                    autocomplete="off" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="slugUploadHome" id="slug-upload-home" />
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
                <button type="button" class="btn btn-primary" id="btn-save-upload-home"
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
<div class="modal modal-blur fade" id="modal-view-picture-home" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gambar Rumah Kost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="view-gambar-home"></div>
            </div>
        </div>
    </div>
</div>
@push('myscript')
    <script src="{{ asset('assets/vendor/tabler/libs/dropzone/dist/dropzone-min.js') }}?{{ rand() }}"></script>
    <script src="{{ asset('assets/js/Pages/Master/Home/app.js') }}?{{ rand() }}"></script>
@endpush
