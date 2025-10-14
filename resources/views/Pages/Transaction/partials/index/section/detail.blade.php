@php
    use Illuminate\Support\Carbon;
@endphp
<div class="row mb-3">
    <div class="col-md-12">
        <h3>{{ $room->number_room }}</h3>
    </div>
</div>
<hr>
<div class="table-responsive">
    <table class="table table-vcenter table-mobile-md card-table">
        <thead>
            <tr>
                <th>Nama Penyewa</th>
                <th>Tanggal Sewa</th>
                <th>Durasi</th>
                <th>Lama Sewa (Malam)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transactions as $value)
                <tr>
                    <td data-label="Nama Penyewa">
                        <div class="d-flex py-1 align-items-center">
                            <div class="flex-fill">
                                <div class="font-weight-medium">
                                    <button class="btn btn-link" title="Detail Penghuni"
                                        onclick="fnTransactionRoom.detailGuest('{{ $value->member->id }}')">
                                        @if ($value->member->user)
                                            {{ $value->member->user->name }}
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td data-label="Tanggal Sewa">
                        <div>{{ Carbon::parse($value->start_date)->isoFormat('LL') }} -
                            {{ Carbon::parse($value->end_date)->isoFormat('LL') }}</div>
                        <div class="text-secondary">
                            @if (Carbon::parse($value->start_date)->lessThanOrEqualTo(Carbon::now('Asia/Jakarta')))
                                Sudah Checkin
                            @else
                                Belum Checkin
                            @endif
                        </div>
                    </td>
                    <td class="text-secondary" data-label="Durasi">
                        @switch($value->duration)
                            @case('daily')
                                Harian
                            @break

                            @case('weekly')
                                Mingguan
                            @break

                            @case('monthly')
                                Bulanan
                            @break

                            @case('yearly')
                                Tahunan
                            @break
                        @endswitch
                    </td>
                    <td class="text-dark" data-label="Lama Sewa (Malam)">
                        {{ Carbon::parse($value->start_date)->diffInDays($value->end_date) }} Malam
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="text-danger h2 text-center">
                                Tidak ada data
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
