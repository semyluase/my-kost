@php
    use Illuminate\Support\Carbon;
@endphp
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <label for="nobukti" class="form-label">No Transaksi</label>
                <input type="text" name="nobukti" id="nobukti" class="form-control bg-gray-500" readonly
                    value="{{ $cleaning ? $cleaning->nobukti : '' }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="no-kamar" class="form-label">No Kamar</label>
                <select name="no-kamar" id="no-kamar" class="form-select choices"></select>
            </div>
            <div class="col-6">
                <label for="waktu-cleaning" class="form-label">Jam (<span class="text-gray-500">format
                        hh:mm</span>)</label>
                <input type="text" name="waktu-cleaning" id="waktu-cleaning" class="form-control"
                    value="{{ $cleaning ? Carbon::parse($cleaning->tgl_request_cleaning)->isoFormat('HH:mm') : '' }}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-6">
                <label for="mulai-cleaning" class="form-label">Jam (<span class="text-gray-500">format
                        hh:mm</span>)</label>
                <input type="text" name="mulai-cleaning" id="mulai-cleaning" class="form-control"
                    value="{{ $cleaning ? ($cleaning->tgl_mulai_cleaning ? Carbon::parse($cleaning->tgl_mulai_cleaning)->isoFormat('HH:mm') : '') : '' }}">
            </div>
            <div class="col-6">
                <label for="selesai-cleaning" class="form-label">Jam (<span class="text-gray-500">format
                        hh:mm</span>)</label>
                <input type="text" name="selesai-cleaning" id="selesai-cleaning" class="form-control"
                    value="{{ $cleaning ? ($cleaning->tgl_selesai_cleaning ? Carbon::parse($cleaning->tgl_selesai_cleaning)->isoFormat('HH:mm') : '') : '' }}">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="row align-items-center">
            <div class="col-auto ms-auto">
                <div class="btn-list">
                    <a href="{{ url('') }}/transactions/orders/cleaning" class="btn btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M18 6l-12 12" />
                            <path d="M6 6l12 12" />
                        </svg>
                        Batal
                    </a>
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
                </div>
            </div>
        </div>
    </div>
</div>
