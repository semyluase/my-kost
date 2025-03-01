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
                            @if ($room->rent)
                                <div
                                    class="badge badge-outline {{ $room->rent->is_approved ? 'text-danger' : 'text-success' }} fw-normal badge-pill">
                                    @if ($room->rent->is_approve)
                                        Tidak tersedia
                                    @elseif ($room->rent->is_approved == false)
                                        Perlu Persetujuan
                                    @else
                                        Tersedia
                                    @endif
                                </div>
                            @else
                                <div class="badge badge-outline text-success fw-normal badge-pill">
                                    Tersedia
                                </div>
                            @endif
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
                                    <a href="{{ url('/transactions/rent-rooms/detail-rents/' . $room->slug) }}"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Detail
                                        Pembayaran</a>
                                @else
                                    <a href="{{ url('/transactions/rent-rooms/checkout') }}?room={{ $room->slug }}"
                                        class="badge badge-outline text-primary fw-semibold badge-pill">Checkout</a>
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
