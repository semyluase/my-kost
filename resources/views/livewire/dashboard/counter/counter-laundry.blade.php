<div class="col-sm-6 col-lg-3 mb-3">
    <div class="card">
        <a href="{{ url('') }}/transactions/orders" class="text-decoration-none text-dark">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="subheader">Laundry</div>
                    <div class="ms-auto lh-1">
                        <div class="text-secondary">Hari ini</div>
                    </div>
                </div>
                <div class="h1 mb-3">{{ $counterUncomplete }}/{{ $counterComplete }}</div>
                <div class="progress progress-sm">
                    @php
                        $total = $counterComplete != 0 ? ($counterUncomplete / $counterComplete) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $total }}" role="progressbar"
                        aria-valuenow="{{ $counterUncomplete }}" aria-valuemin="0"
                        aria-valuemax="{{ $counterComplete }}" aria-label="{{ $total }} Selesai">
                        <span class="visually-hidden">{{ $total }} Selesai</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
