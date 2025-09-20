<div class="row mb-3">
    <div class="col-12">
        <div class="row mb-3">
            <div class="col-lg-6 col-sm-12 mb-3">
                <label for="nomor-handphone" class="form-label">Nomor HP</label>
                <div class="input-group mb-2">
                    <input type="text" name="nomor-handphone" id="nomor-handphone" class="form-control">
                    <button class="btn btn-primary" type="button" id="btn-view-member" title="Lihat Member">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                            <path
                                d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                        </svg>
                    </button>
                </div>
                <small class="form-hint">Tekan enter untuk mencari data member</small>
            </div>
            <div class="col-lg-6 col-sm-12 mb-3">
                <label for="name" class="form-label">Nama Penyewa</label>
                <input type="text" name="name" id="name" class="form-control">
            </div>
            <div class="col-lg-6 col-sm-12 mb-3">
                <label for="tanggal-lahir" class="form-label">Tanggal Lahir</label>
                <input class="form-control " placeholder="Select a date" id="tanggal-lahir">
            </div>
            <div class="col-lg-6 col-sm-12 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="col-lg-6 col-sm-12 mb-3">
                <label for="jenis-identitas" class="form-label">Jenis Identitas</label>
                <select name="jenis-identitas" id="jenis-identitas" class="form-select choices">
                    <option value="">Pilih Jenis Identitas</option>
                    <option value="ktm">Kartu Mahasiswa</option>
                    <option value="ktp">Kartu Tanda Penduduk</option>
                    <option value="paspor">Paspor</option>
                </select>
            </div>
            <div class="col-lg-6 col-sm-12 mb-3">
                <label for="nomor-identitas" class="form-label">Nomor Identitas</label>
                <input type="text" name="nomor-identitas" id="nomor-identitas" class="form-control">
            </div>
            <div class="col-lg-8 col-sm-12 mb-3" id="upload-identity">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Foto Identitas</h3>
                        <form class="dropzone dz-clickable" id="dropzone-foto"
                            action="{{ url('/transactions/rent-rooms/upload-identity') }}" autocomplete="off"
                            novalidate="">
                            @csrf
                            <div id="fileFoto"></div>

                            <div class="dz-default dz-message"><button class="dz-button" type="button">Drop files
                                    here
                                    to upload</button></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12 mb-3" id="upload-foto-orang">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Foto Penyewa</h3>
                        <form class="dropzone dz-clickable" id="dropzone-foto-orang"
                            action="{{ url('/transactions/rent-rooms/upload-foto-orang') }}" autocomplete="off"
                            novalidate="">
                            @csrf
                            <div id="fileFoto"></div>

                            <div class="dz-default dz-message"><button class="dz-button" type="button">Drop files
                                    here
                                    to upload</button></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12 mb-3 d-none" id="show-identity">
                <div class="card card-sm">
                    <div class="d-block">
                        <img src="" alt="" class="card-img-top"
                            style="max-height: 15rem; max-width:27rem;" id="image-identity">
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12 mb-3 d-none" id="show-foto-orang">
                <div class="card card-sm">
                    <div class="d-block">
                        <img src="" alt="" class="card-img-top"
                            style="max-height: 27rem; max-width:15rem;" id="image-orang">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
