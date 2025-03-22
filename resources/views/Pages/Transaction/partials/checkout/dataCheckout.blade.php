@php
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Number;
@endphp
<div class="row mb-3">
    <div class="col-6 mb-3">
        <div class="text-start">
            <h3>{{ $room->rent->member->user->name }}</h3>
            <p>{{ $room->rent->member->identity }}</p>
        </div>
    </div>
    <div class="col-6 mb-3">
        <div class="mb-3">
            <label class="form-label">Simple selectgroup</label>
            <div class="form-selectgroup">
                <label class="form-selectgroup-item">
                    <input type="radio" name="jenis-pengembalian" id="jenis-pengembalian" value="cash"
                        class="form-selectgroup-input" checked="">
                    <span class="form-selectgroup-label">Cash</span>
                </label>
                <label class="form-selectgroup-item">
                    <input type="radio" name="jenis-pengembalian" id="jenis-pengembalian" value="transfer"
                        class="form-selectgroup-input">
                    <span class="form-selectgroup-label">Transfer</span>
                </label>
            </div>
        </div>
        <div class="text-start">
            <p>
                <label for="bank" class="form-label">Bank</label>
                <select name="bank" id="bank" class="form-select choices"></select>
            </p>
            <p>
                <label for="no-rek" class="form-label">No Rekening</label>
                <input type="text" class="form-control" id="no-rek">
            </p>
        </div>
    </div>
    <div class="col-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Jenis Pengembalian</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <th>Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Deposit</td>
                        <td>{{ Number::currency($room->rent->member->user->deposite->jumlah, 'Rp.', 'id') }}</td>
                        <td>{{ Carbon::now('Asia/Jakarta')->addDay()->isoFormat('DD MMMM YYYY') }}</td>
                        <td><input type="text" class="form-control" id="pengembalian"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
