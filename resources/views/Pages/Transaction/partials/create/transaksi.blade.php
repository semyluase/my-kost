@php
    $room->category->load('prices');
@endphp
<div class="row mb-3">
    <div class="col-md-12 mb-3">
        <label for="" class="form-label">Durasi Sewa</label>
        @if (collect($room->category->prices)->count() > 0)
            @foreach ($room->category->prices as $price)
                @switch($price->type)
                    @case('daily')
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="durasi" value="harian" checked="">
                            <span class="form-check-label">Harian</span>
                        </label>
                    @break

                    @case('weekly')
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="durasi" value="mingguan">
                            <span class="form-check-label">Mingguan</span>
                        </label>
                    @break

                    @case('yearly')
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="durasi" value="tahunan">
                            <span class="form-check-label">Tahunan</span>
                        </label>
                    @break

                    @default
                        <label class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="durasi" value="bulanan">
                            <span class="form-check-label">Bulanan</span>
                        </label>
                @endswitch
            @endforeach
        @else
            <div class="text-center">Master Harga Belum Ada</div>
        @endif
    </div>
    <div class="col-md-3 mb-3">
        <label for="start-rent" class="form-label">Tanggal Masuk</label>
        <div class="input-icon mb-2">
            <input class="form-control" placeholder="Select a date" id="start-rent">
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <label for="end-rent" class="form-label">Tanggal Keluar</label>
        <div class="input-icon mb-2">
            <input class="form-control" placeholder="Select a date" id="end-rent">
        </div>
    </div>
</div>
