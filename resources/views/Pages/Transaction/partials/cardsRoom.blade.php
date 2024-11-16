<div class="card">
    <div class="row g-0">
        <div class="col">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div style="font-size: 5em;" class="text-primary">{{ $room->number_room }}</div>
                    </div>
                    <div class="col-md-auto">
                        <div class="mt-3 badges">
                            <div
                                class="badge badge-outline {{ collect($room->transaction)->count() > 0 ? 'text-danger' : 'text-success' }} fw-normal badge-pill">
                                @if (collect($room->transaction)->count() > 0)
                                    @foreach ($room->transaction as $transaction)
                                        @if ($transaction->is_approve)
                                            Tidak tersedia
                                        @else
                                            Perlu Persetujuan
                                        @endif
                                    @endforeach
                                @else
                                    Tersedia
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-auto">
                        <div class="mt-3 badges">
                            @if (!$room->rent)
                                <a href="{{ url('/transactions/rent-rooms/create') }}?room={{ $room->slug }}"
                                    class="badge badge-outline text-primary fw-semibold badge-pill">Sewa Kamar</a>
                            @else
                                @if (!$room->rent->is_approve)
                                    <a href="{{ url('/transactions/rent-rooms/approved') }}?room={{ $room->slug }}"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Approve</a>
                                @else
                                    <a href="{{ url('/transactions/rent-rooms/orders') }}?room={{ $room->slug }}"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Pesanan</a>
                                    <a href="{{ url('/transactions/rent-rooms/topups') }}?room={{ $room->slug }}"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Top Up</a>
                                    <a href="{{ url('/transactions/rent-rooms/change-room') }}?room={{ $room->slug }}"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Pindah
                                        Kamar</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
