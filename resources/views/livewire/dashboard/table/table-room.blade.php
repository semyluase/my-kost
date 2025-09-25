<div>
    @php
        use Illuminate\Support\Carbon;
        use Illuminate\Support\Str;
    @endphp
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Kamar</h3>
            </div>
            <div class="card-body border-bottom py-3">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select wire:model.live="category" class="form-select">
                            <option value="">Pilih Kategori</option>
                            @if ($categories)
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="text-secondary">
                        <div class="mx-2 d-inline-block">
                            <input type="text" class="form-control form-control-sm" wire:model.live="length"
                                size="3">
                        </div>
                        Data
                    </div>
                    <div class="ms-auto text-secondary">
                        No Kamar:
                        <div class="ms-2 d-inline-block">
                            <input type="text" class="form-control form-control-sm" wire:model.live="roomNumber">
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-selectable card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="w-1">No. Kamar</th>
                            <th>Kos</th>
                            <th>Kategori</th>
                            <th>Nama Tamu</th>
                            <th>Tanggal Sewa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rooms as $room)
                            <tr>
                                <td>{{ $room->number_room }}</td>
                                <td>{{ $room->home->name }}</td>
                                <td>{{ $room->category->name }}</td>
                                <td>{{ $room->rent ? $room->rent->member->user->name : '-' }}</td>
                                <td>
                                    @if ($room->rent)
                                        {{ Carbon::parse($room->rent->start_date)->isoFormat('LL') }} -
                                        {{ Carbon::parse($room->rent->end_date)->isoFormat('LL') }} <span
                                            class="text-secondary">({{ Carbon::parse($room->rent->start_date)->diffInDays(Carbon::parse($room->rent->end_date)) + 1 }}
                                            Hari)</span>
                                </td>
                            @else
                                -
                        @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center fw-bold">Tidak ada data</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row g-2 justify-content-center justify-content-sm-between">
                    <div class="col-auto">
                        {{ $rooms->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
