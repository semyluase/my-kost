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
                <th>#</th>
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
                    <td>
                        @if (!$value->is_approve)
                            <a href="{{ url('') }}/transactions/rent-rooms/detail-rents/{{ $value->room->slug }}?transaksi={{ $value->id }}"
                                class="btn btn-primary">Detail
                                Pembayaran</a>
                        @endif
                        @if (Carbon::parse($value->end_date)->startOfDay()->addHours(12)->greaterThan(Carbon::now('Asia/Jakarta')->startOfDay()->addHours(12)))
                            <button class="btn btn-outline btn-danger"
                                onclick="fnTransactionRoom.cancelRoom('{{ $value->id }}','{{ csrf_token() }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-forbid">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M9 9l6 6" />
                                </svg>
                                Batalkan Transaksi
                            </button>
                        @endif
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
